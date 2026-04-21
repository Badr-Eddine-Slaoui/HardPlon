const { exec } = require("child_process");
const util = require("util");

const execAsync = util.promisify(exec);

const config = require(process.argv[2]);

const TIMEOUT = config.test_config.timeout_seconds * 1000;
const LANGUAGE = config.test_config.language;
const WORKDIR = config.config.execution.working_dir;

function buildCommand(input) {
    const args = input.join(" ");

    switch (LANGUAGE) {
        case "javascript":
            return `node ${WORKDIR}/solution.js ${args}`;

        case "python":
            return `python3 ${WORKDIR}/solution.py ${args}`;

        case "php":
            return `php ${WORKDIR}/solution.php ${args}`;

        default:
            throw new Error("Unsupported language: " + LANGUAGE);
    }
}

async function runTest(test) {
    try {
        const cmd = buildCommand(test.input);

        const { stdout } = await execAsync(cmd, {
            timeout: TIMEOUT,
            cwd: WORKDIR
        });

        const output = stdout.trim();

        const expected = test.expected.join(" ").trim();

        const passed = output === expected;

        return {
            name: test.name,
            passed,
            output,
            expected
        };

    } catch (err) {
        return {
            name: test.name,
            passed: false,
            error: err.message
        };
    }
}

(async () => {
    let results = [];

    for (let test of config.test_config.tests) {
        const res = await runTest(test);
        results.push(res);
    }

    console.log(JSON.stringify(results));
})();