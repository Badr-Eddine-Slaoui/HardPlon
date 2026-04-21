const axios = require("axios");

const config = require(process.argv[2]);

const base = "http://localhost:" + config.config.execution.port;
const retries = config.test_config.retries || 1;


async function requestWithRetry(test) {
    for (let i = 0; i <= retries; i++) {
        try {
            return await axios({
                method: test.method,
                url: base + test.url,
                data: test.body || {},
                headers: Object.fromEntries(
                    (test.headers || []).map(h => [h.name, h.value])
                ),
                timeout: config.test_config.timeout_seconds * 1000
            });
        } catch (e) {
            if (e.response) return e.response;
            if (i === retries) throw e;
        }
    }
}


function checkForContains(keys, data) {
    if (Array.isArray(data)) {
        return data.some(item => checkForContains(keys, item));
    }

    if (typeof data === "object" && data !== null) {
        return keys.every(key => key in data);
    }

    return false;
}

(async () => {
    let results = [];
    const tests = config.test_config.tests || [];

    for (let test of tests) {
        try {
            const res = await requestWithRetry(test);

            let passed = res.status === test.expected.status;

            if (passed && test.expected.json_contains) {
                data = res.data;

                passed = checkForContains(test.expected.json_contains, data); 
            }

            results.push({ 
                name: test.name, 
                passed, 
                status: res.status,
                actual_data: !passed ? res.data : undefined 
            });

        } catch (err) {
            results.push({ name: test.name, passed: false, error: err.message });
        }
    }

    const totalTests = results.length;
    const passedTests = results.filter(r => r.passed).length;
    const score = totalTests === 0 ? 10 : (passedTests / totalTests) * 10;

    console.log(JSON.stringify({
        score: parseFloat(score.toFixed(2)),
        tests: results
    }));
})();