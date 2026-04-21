package jobs

import (
	"encoding/json"
	"fmt"
	"log"
	"time"
	"strings"
	"reflect"

	"problems_worker/internal/http"
	"problems_worker/internal/runner"
)

type JobMessage struct {
	JobID int `json:"job_id"`
}

func normalize(v interface{}) interface{} {
	switch val := v.(type) {

	case float64:
		if val == float64(int(val)) {
			return int(val)
		}
		return val

	case string:
		return strings.TrimSpace(val)

	default:
		return val
	}
}

func compareOutput(out string, expectedArr []interface{}) bool {
	out = strings.TrimSpace(out)

	var outArr []interface{}
	if err := json.Unmarshal([]byte(out), &outArr); err == nil {

		if len(outArr) != len(expectedArr) {
			return false
		}

		for i := range outArr {
			if !reflect.DeepEqual(normalize(outArr[i]), normalize(expectedArr[i])) {
				return false
			}
		}
		return true
	}

	var outVal interface{}
	if err := json.Unmarshal([]byte(out), &outVal); err == nil {

		if len(expectedArr) != 1 {
			return false
		}

		return reflect.DeepEqual(
			normalize(outVal),
			normalize(expectedArr[0]),
		)
	}

	if len(expectedArr) != 1 {
		return false
	}

	return strings.TrimSpace(out) == fmt.Sprint(expectedArr[0])
}

func HandleJob(payload string) {
	start := time.Now()

	var msg JobMessage
	if err := json.Unmarshal([]byte(payload), &msg); err != nil {
		log.Println("❌ Invalid payload:", err)
		return
	}

	log.Println("📥 Job:", msg.JobID)

	job, err := http.GetJob(msg.JobID)
	if err != nil {
		log.Println("❌ API error:", err)
		return
	}

	_ = http.UpdateJob(msg.JobID, map[string]interface{}{
		"status": "running",
	})

	final := http.FinalResult{}
	var total float64

	for _, sub := range job.Submissions {

		log.Println("🚀 Problem:", sub.Problem.Title)

		prob := http.ProblemResult{
			ProblemID: sub.Problem.ID,
		}

		pass := 0

		for _, tc := range sub.TestCases {

			out, err := runner.ExecuteTest(sub, tc, msg.JobID)

			expectedArr := tc.ExpectedOutput
			ok := err == nil && compareOutput(out, expectedArr)

			if ok {
				pass++
				log.Println("✅ Passed:", tc.ID)
			} else {
				log.Println("❌ Failed:", tc.ID, "| got:", out, "| expected:", expectedArr)
			}

			prob.Tests = append(prob.Tests, http.TestCaseResult{
				TestCaseID: tc.ID,
				Passed:     ok,
				Input:      fmt.Sprint(tc.Input),
				Expected:   fmt.Sprint(expectedArr),
				Actual:     out,
			})
		}

		if len(sub.TestCases) > 0 {
			prob.Score = float64(pass) / float64(len(sub.TestCases)) * 10
		}

		total += prob.Score
		final.Submissions = append(final.Submissions, prob)
	}

	final.TotalScore = total

	log.Println("📊 Final score:", total)

	_ = http.UpdateJob(msg.JobID, map[string]interface{}{
		"status": "completed",
		"result": final,
	})

	log.Println("🎉 Done in", time.Since(start))
}