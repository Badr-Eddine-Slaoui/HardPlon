package http

import (
	"bytes"
	"encoding/json"
	"fmt"
	"net/http"
	"strconv"
)

var BaseURL = "http://caddy/api/worker"


type JobPayload struct {
	SubmissionID   int    `json:"submission_id"`
}

type Language struct {
	ID          int    `json:"id"`
	Name        string `json:"name"`
	DockerImage string `json:"docker_image"`

	CompileCommand *string `json:"compile_command"`
	RunCommand string `json:"run_command"`
}

type Problem struct {
	ID                   int         `json:"id"`
	BriefID              int         `json:"brief_id"`
	BriefSkillLevelID    int         `json:"brief_skill_level_id"`
	LanguageID           int         `json:"language_id"`
	Title                string      `json:"title"`
	Description          string      `json:"description"`
	HiddenHeader        string      `json:"hidden_header"`
	HiddenFooter        string      `json:"hidden_footer"`
	SkeletonCode        string      `json:"skeleton_code"`
	OrderIndex          int         `json:"order_index"`
	Language			Language    `json:"language"`
}

type TestCase struct {
	ID             int           `json:"id"`
	ProblemID      int           `json:"problem_id"`
	Input          []interface{} `json:"input"`
	ExpectedOutput []interface{} `json:"expected_output"`
	IsHidden       bool          `json:"is_hidden"`
}

type Submission struct {
	Problem        Problem    `json:"problem"`
	TestCases      []TestCase `json:"test_cases"`
	SubmissionCode string     `json:"submission_code"`
}

type Job struct {
	ID int `json:"job_id"`

	Payload JobPayload `json:"payload"`

	Submissions []Submission `json:"submissions"`
}

type JobResponse struct {
	Success bool `json:"success"`
	Data struct {
		Job Job `json:"job_payload"`
	} `json:"data"`
	Message string `json:"message"`
}

func GetJob(jobID int) (*Job, error) {
	resp, err := http.Get(BaseURL + "/problem-submission-jobs/" + strconv.Itoa(jobID))
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
		BaseURL+"/problem-submission-jobs/"+strconv.Itoa(jobID),
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