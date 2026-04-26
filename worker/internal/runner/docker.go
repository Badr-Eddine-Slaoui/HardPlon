package runner

import (
	"context"
	"encoding/json"
	"fmt"
	"io"
	"log"
	"os"
	"path/filepath"
	"time"

	"github.com/docker/docker/api/types"
	"github.com/docker/docker/api/types/container"
	"github.com/docker/docker/client"
)

type RunOptions struct {
	Image    string
	RepoPath string
	Config   map[string]interface{}
	JobID    int
	Network  bool
	Resources struct {
		CPU            int
		MemoryMB       int
		TimeoutSeconds int
		PIDsLimit      int
	}
}

func RunContainer(opt RunOptions) (string, error) {
	ctx := context.Background()

	cli, err := client.NewClientWithOpts(client.FromEnv)
	if err != nil {
		return "", err
	}
	defer cli.Close()

	log.Printf("Pulling image: %s", opt.Image)
    reader, err := cli.ImagePull(ctx, opt.Image, types.ImagePullOptions{})
    if err != nil {
        return "", fmt.Errorf("failed to pull image: %w", err)
    }
    defer reader.Close()

	_, _ = io.Copy(io.Discard, reader) 
    log.Println("Image pulled successfully")

	configDir := fmt.Sprintf("/tmp/job_%d_config", opt.JobID)
	outputDir := fmt.Sprintf("/tmp/job_%d_output", opt.JobID)

	_ = os.MkdirAll(configDir, 0755)
	_ = os.MkdirAll(outputDir, 0755)

	configPath := filepath.Join(configDir, "config.json")

	data, err := json.Marshal(opt.Config)
	if err != nil {
		return "", err
	}

	if err := os.WriteFile(configPath, data, 0644); err != nil {
		return "", err
	}

	networkMode := container.NetworkMode("none")
	if opt.Network {
		networkMode = container.NetworkMode("bridge")
	}

	var pidsLimit *int64
	if opt.Resources.PIDsLimit > 0 {
		v := int64(opt.Resources.PIDsLimit)
		pidsLimit = &v
	}

	resp, err := cli.ContainerCreate(ctx,
		&container.Config{
			Image: opt.Image,
			Cmd:   []string{"sh", "runner.sh"},
			Env: []string{
				"JOB_ID=" + fmt.Sprint(opt.JobID),
			},
			Tty: false,
		},
		&container.HostConfig{
			Binds: []string{
				opt.RepoPath + ":/workspace",
				configPath + ":/config/config.json",
				outputDir + ":/output",
			},
			NetworkMode: networkMode,
			Resources: container.Resources{
				Memory:   int64(opt.Resources.MemoryMB) * 1024 * 1024,
				NanoCPUs: int64(opt.Resources.CPU) * 1e9,
				PidsLimit: pidsLimit,
			},
		},
		nil, nil, "",
	)
	if err != nil {
		return "", err
	}

	if err := cli.ContainerStart(ctx, resp.ID, types.ContainerStartOptions{}); err != nil {
		return "", err
	}

	timeout := time.After(time.Duration(opt.Resources.TimeoutSeconds) * time.Second)

	statusCh, errCh := cli.ContainerWait(ctx, resp.ID, container.WaitConditionNotRunning)

	select {
	case <-timeout:
		_ = cli.ContainerKill(ctx, resp.ID, "SIGKILL")
		return "", fmt.Errorf("container timeout")

	case err := <-errCh:
		return "", err

	case <-statusCh:
	}

	if err := cli.ContainerRemove(ctx, resp.ID, types.ContainerRemoveOptions{
		Force: true,
	}); err != nil {
		return "", err
	}

	return filepath.Join(outputDir, "result.json"), nil
}