package http

type TestCaseResult struct {
	TestCaseID int    `json:"test_case_id"`
	Passed     bool   `json:"passed"`
	Input      string `json:"input"`
	Expected   string `json:"expected"`
	Actual     string `json:"actual"`
}

type ProblemResult struct {
	ProblemID int              `json:"problem_id"`
	Score     float64          `json:"score"`
	Tests     []TestCaseResult `json:"tests"`
}

type FinalResult struct {
	Submissions []ProblemResult `json:"submissions"`
	TotalScore  float64         `json:"total_score"`
}