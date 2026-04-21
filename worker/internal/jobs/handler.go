package jobs

import (
	"encoding/json"
	"fmt"
	"log"
	"os"
	"time"

	"worker/internal/git"
	httpclient "worker/internal/http"
	"worker/internal/runner"
)

type JobMessage struct {
	JobID int `json:"job_id"`
}

func HandleJob(payload string) {
	
	startTime := time.Now()

	var msg JobMessage
	if err := json.Unmarshal([]byte(payload), &msg); err != nil {
		log.Println("Invalid payload:", err)
		return
	}

	defer func() {
		duration := time.Since(startTime)
		log.Printf("⏱️ Job %d execution time: %.1fs", msg.JobID, duration.Seconds())

		configDir := fmt.Sprintf("/tmp/job_%d_config", msg.JobID)
		outputDir := fmt.Sprintf("/tmp/job_%d_output", msg.JobID)
		repoPath := fmt.Sprintf("/tmp/job_%d", msg.JobID)

		os.RemoveAll(configDir)
		os.RemoveAll(outputDir)
		os.RemoveAll(repoPath)
		log.Printf("🧹 Cleanup completed for Job %d", msg.JobID)
	}()

	log.Println("📥 Job received:", msg.JobID)

	job, err := httpclient.GetJob(msg.JobID)
	if err != nil {
		log.Println("❌ Failed to fetch job:", err)
		return
	}

	log.Println("📦 Job loaded")
	log.Println("🔗 Repo:", job.Payload.RepositoryURL)
	log.Println("🐳 Runner:", job.Runner.Image)

	if job.Payload.RepositoryURL == "" {
		log.Println("❌ Missing repo URL")
		return
	}

	repoPath, err := git.CloneRepo(job.Payload.RepositoryURL, msg.JobID)
	if err != nil {
		log.Println("❌ Clone failed:", err)

		_ = httpclient.UpdateJob(msg.JobID, map[string]interface{}{
			"status": "failed",
			"error":  err.Error(),
		})
		return
	}

	log.Println("📁 Repo cloned:", repoPath)

	_ = httpclient.UpdateJob(msg.JobID, map[string]interface{}{
		"status": "running",
	})

	resultPath, err := runner.RunContainer(runner.RunOptions{
		Image:    job.Runner.Image,
		RepoPath: repoPath,
		Config: map[string]interface{}{
			"config":             job.Config,
			"architecture_rules": job.ArchitectureRules,
			"test_config":        job.TestConfig,
		},
		JobID:   msg.JobID,
		Network: job.Config.Network.Enabled,
		Resources: struct {
			CPU            int
			MemoryMB       int
			TimeoutSeconds int
			PIDsLimit      int
		}{
			CPU:            job.Config.Resources.CPU,
			MemoryMB:       job.Config.Resources.MemoryMB,
			TimeoutSeconds: job.Config.Resources.TimeoutSeconds,
			PIDsLimit:      job.Config.Resources.PIDsLimit,
		},
	})

	if err != nil {
		log.Println("❌ Runner failed:", err)

		_ = httpclient.UpdateJob(msg.JobID, map[string]interface{}{
			"status": "failed",
			"error":  err.Error(),
		})
		return
	}

	log.Println("📄 Result file:", resultPath)

	resultData, err := os.ReadFile(resultPath)
	if err != nil {
		log.Println("❌ Failed to read result:", err)

		_ = httpclient.UpdateJob(msg.JobID, map[string]interface{}{
			"status": "failed",
			"error":  "cannot read result.json",
		})
		return
	}

	_ = httpclient.UpdateJob(msg.JobID, map[string]interface{}{
		"status": "completed",
		"result": json.RawMessage(resultData),
	})

	log.Println("🎉 Job completed successfully")
}