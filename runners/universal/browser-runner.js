const { chromium } = require("playwright");

const config = require(process.argv[2]);

const PORT = config.config.execution.port;
const BASE_URL = "http://localhost:" + PORT;
const TIMEOUT = config.test_config.timeout_seconds * 1000;
const RETRIES = config.test_config.retries || 0;

async function runTest(test, page) {
    try {
        for (let step of test.steps) {
            switch (step.action) {
                case "goto":
                    await page.goto(BASE_URL + step.url, { timeout: TIMEOUT });
                    break;

                case "click":
                    await page.click(step.selector, { timeout: TIMEOUT });
                    break;

                case "type":
                    await page.fill(step.selector, step.value);
                    break;

                case "wait":
                    await page.waitForTimeout(step.duration || 500);
                    break;

                default:
                    throw new Error("Unknown action: " + step.action);
            }
        }

        // 🔍 EXPECTATIONS
        if (test.expected) {
            if (test.expected.title) {
                const title = await page.title();
                if (title !== test.expected.title) {
                    throw new Error(`Title mismatch: ${title}`);
                }
            }

            if (test.expected.text) {
                const content = await page.textContent("body");
                if (!content.includes(test.expected.text)) {
                    throw new Error(`Text not found: ${test.expected.text}`);
                }
            }

            if (test.expected.url) {
                const current = page.url();
                if (!current.includes(test.expected.url)) {
                    throw new Error(`URL mismatch: ${current}`);
                }
            }
        }

        return { name: test.name, passed: true };

    } catch (err) {
        return {
            name: test.name,
            passed: false,
            error: err.message
        };
    }
}

(async () => {
    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext();
    const page = await context.newPage();

    let results = [];

    for (let test of config.test_config.tests) {
        let lastError = null;

        for (let i = 0; i <= RETRIES; i++) {
            const res = await runTest(test, page);

            if (res.passed) {
                results.push(res);
                break;
            }

            lastError = res;
            if (i === RETRIES) {
                results.push(lastError);
            }
        }
    }

    await browser.close();

    console.log(JSON.stringify(results));
})();