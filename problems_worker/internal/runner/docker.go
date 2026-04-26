package runner

import (
	"bytes"
	"context"
	"fmt"
	"strings"
	"time"

	"github.com/docker/docker/api/types"
	"github.com/docker/docker/api/types/container"
	"github.com/docker/docker/client"
	"github.com/docker/docker/pkg/stdcopy"
)

func RunContainer(image, workDir, command string, timeoutSec int) (string, error) {
	ctx := context.Background()

	cli, err := client.NewClientWithOpts(client.FromEnv, client.WithAPIVersionNegotiation(),)
	if err != nil {
		return "", err
	}
	defer cli.Close()

	fmt.Println("🐳 Creating container with image:", image)

	resp, err := cli.ContainerCreate(
		ctx,
		&container.Config{
			Image: image,
			Cmd: []string{
				"sh",
				"-c",
				command,
			},
			WorkingDir: "/workspace",
		},
		&container.HostConfig{
			Binds: []string{
				workDir + ":/workspace",
			},
			NetworkMode: "none",
		},
		nil, nil, "",
	)
	if err != nil {
		return "", err
	}

	fmt.Println("▶️ Starting container:", resp.ID)

	if err := cli.ContainerStart(ctx, resp.ID, types.ContainerStartOptions{}); err != nil {
		return "", err
	}

	timeout := time.After(time.Duration(timeoutSec) * time.Second)
	statusCh, errCh := cli.ContainerWait(ctx, resp.ID, container.WaitConditionNotRunning)

	select {
	case <-timeout:
		_ = cli.ContainerKill(ctx, resp.ID, "SIGKILL")
		return "", fmt.Errorf("timeout")
	case err := <-errCh:
		return "", err
	case <-statusCh:
		fmt.Println("✅ Container finished")
	}

	out, err := cli.ContainerLogs(ctx, resp.ID, types.ContainerLogsOptions{
		ShowStdout: true,
		ShowStderr: true,
	})
	if err != nil {
		return "", err
	}
	defer out.Close()

	var stdout, stderr bytes.Buffer
	_, err = stdcopy.StdCopy(&stdout, &stderr, out)
	if err != nil {
		return "", err
	}

	_ = cli.ContainerRemove(ctx, resp.ID, types.ContainerRemoveOptions{Force: true})

	return strings.TrimSpace(stdout.String()), nil
}