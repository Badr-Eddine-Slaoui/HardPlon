package git

import (
	"fmt"
	"os"
	"os/exec"
)

func CloneRepo(repoURL string, jobID int) (string, error) {
	path := fmt.Sprintf("/tmp/job_%d", jobID)

	os.RemoveAll(path)

	cmd := exec.Command("git", "clone", repoURL, path)

	output, err := cmd.CombinedOutput()
	if err != nil {
		return "", fmt.Errorf("git clone failed: %s", string(output))
	}

	return path, nil
}