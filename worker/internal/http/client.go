package httpclient

import (
	"bytes"
	"encoding/json"
	"fmt"
	"net/http"
	"os"
	"strconv"
)

var BaseURL = os.Getenv("BASE_URL")

type JobPayload struct {
	SubmissionID   int    `json:"submission_id"`
	BriefID        int    `json:"brief_id"`
	RepositoryURL  string `json:"repository_url"`
}

type Runner struct {
	Name    string `json:"name"`
	Version string `json:"version"`
	Image   string `json:"image"`
}

type HealthCheck struct {
	Type           string `json:"type"`
	Path           string `json:"path"`
	TimeoutSeconds int    `json:"timeout_seconds"`
	IntervalMs     int    `json:"interval_ms"`
}

type ExecutionConfig struct {
	Mode         string      `json:"mode"`
	Port         int         `json:"port"`
	StartCommand string      `json:"start_command"`
	WorkingDir   string      `json:"working_dir"`
	HealthCheck  HealthCheck `json:"healthcheck"`
}

type ResourcesConfig struct {
	CPU            int `json:"cpu"`
	MemoryMB       int `json:"memory_mb"`
	TimeoutSeconds int `json:"timeout_seconds"`
	PIDsLimit      int `json:"pids_limit"`
}

type FilesystemConfig struct {
	ReadOnly      bool   `json:"read_only"`
	WorkspacePath string `json:"workspace_path"`
	OutputPath    string `json:"output_path"`
	ConfigPath    string `json:"config_path"`
}

type NetworkConfig struct {
	Enabled bool `json:"enabled"`
}

type FeaturesConfig struct {
	PHP    bool `json:"php"`
	Node   bool `json:"node"`
	SQLite bool `json:"sqlite"`
}

type FallbackConfig struct {
	OnFailure string `json:"on_failure"`
}

type JobConfig struct {
	Execution   ExecutionConfig   `json:"execution"`
	Resources   ResourcesConfig   `json:"resources"`
	Filesystem  FilesystemConfig  `json:"filesystem"`
	Network     NetworkConfig     `json:"network"`
	Features    FeaturesConfig    `json:"features"`
	Fallback    FallbackConfig    `json:"fallback"`
}

type Job struct {
	ID int `json:"job_id"`

	Payload JobPayload `json:"payload"`

	Runner Runner `json:"runner"`

	Config JobConfig `json:"config"`

	ArchitectureRules map[string]interface{} `json:"architecture_rules"`

	TestConfig map[string]interface{} `json:"test_config"`
}

type JobResponse struct {
	Success bool `json:"success"`
	Data struct {
		Job Job `json:"job_payload"`
	} `json:"data"`
	Message string `json:"message"`
}

func GetJob(jobID int) (*Job, error) {
	resp, err := http.Get(BaseURL + "/evaluation-jobs/" + strconv.Itoa(jobID))
	if err != nil {
		return nil, err
	}
	defer resp.Body.Close()

	var jobResp JobResponse

	err = json.NewDecoder(resp.Body).Decode(&jobResp)
	if err != nil {
		return nil, err
	}

	return &jobResp.Data.Job, nil
}

func UpdateJob(jobID int, data map[string]interface{}) error {
	body, _ := json.Marshal(data)

	req, err := http.NewRequest(
		"PUT",
		BaseURL+"/evaluation-jobs/"+strconv.Itoa(jobID),
		bytes.NewBuffer(body),
	)
	if err != nil {
		return err
	}

	req.Header.Set("Content-Type", "application/json")

	client := &http.Client{}
	resp, err := client.Do(req)
	if err != nil {
		return err
	}
	defer resp.Body.Close()

	if resp.StatusCode >= 300 {
		return fmt.Errorf("failed to update job, status: %d, body: %s", resp.StatusCode, resp.Body)
	}

	return nil
}