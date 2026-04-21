package runner

import (
	"fmt"
	"os"
	"path/filepath"
	"strings"

	"problems_worker/internal/http"
)

func ExecuteTest(sub http.Submission, tc http.TestCase, jobID int) (string, error) {

	workDir := filepath.Join(os.TempDir(),
		fmt.Sprintf("job_%d_p%d_tc%d", jobID, sub.Problem.ID, tc.ID))

	_ = os.MkdirAll(workDir, 0755)
	defer os.RemoveAll(workDir)

	fileName := "solution"

	fullCode := fmt.Sprintf("%s\n%s\n%s",
		sub.Problem.HiddenHeader,
		sub.SubmissionCode,
		sub.Problem.HiddenFooter,
	)

	codePath := filepath.Join(workDir, fileName)
	_ = os.WriteFile(codePath, []byte(fullCode), 0644)

	compile := ""
	if sub.Problem.Language.CompileCommand != nil {
		compile = *sub.Problem.Language.CompileCommand + " && "
	}

	runCmd := strings.ReplaceAll(
		sub.Problem.Language.RunCommand,
		"{file}",
		fileName,
	)

	var inputParts []string
	for _, v := range tc.Input {
		inputParts = append(inputParts, fmt.Sprint(v))
	}
	inputString := strings.Join(inputParts, " ")

	finalCmd := fmt.Sprintf(`%secho '%s' | %s`,
		compile,
		strings.ReplaceAll(inputString, "'", "'\\''"),
		runCmd,
	)

	fmt.Println("⚙️ Running:", finalCmd)

	output, err := RunContainer(
		sub.Problem.Language.DockerImage,
		workDir,
		finalCmd,
		10,
	)

	return strings.TrimSpace(output), err
}