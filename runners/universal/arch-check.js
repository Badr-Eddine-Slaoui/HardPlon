const fs = require("fs");
const path = require("path");

const config = require(process.argv[2]);
const workspace = config.config.filesystem.workspace_path;

const rules = config.architecture_rules;

let result = {
    score: 0,
    missing: [],
    forbidden: [],
    structure_errors: [],
    pattern_errors: [],
    optional_found: []
};

for (let file of rules.required || []) {
    if (!fs.existsSync(path.join(workspace, file))) {
        result.missing.push(file);
    }
}

for (let file of rules.forbidden || []) {
    if (fs.existsSync(path.join(workspace, file))) {
        result.forbidden.push(file);
    }
}

for (let file in (rules.structure || {})) {
    const expectedType = rules.structure[file];
    const fullPath = path.join(workspace, file);

    if (!fs.existsSync(fullPath)) {
        result.structure_errors.push(`${file} missing`);
        continue;
    }

    const stat = fs.statSync(fullPath);
    if (expectedType === "file" && !stat.isFile()) {
        result.structure_errors.push(`${file} should be file`);
    }
    if (expectedType === "dir" && !stat.isDirectory()) {
        result.structure_errors.push(`${file} should be dir`);
    }
}

for (let pattern of (rules.patterns || [])) {
    const filePath = path.join(workspace, pattern.path);
    if (!fs.existsSync(filePath)) {
        result.pattern_errors.push(`${pattern.path} not found`);
        continue;
    }
    const content = fs.readFileSync(filePath, "utf-8");
    if (pattern.type === "file_contains" && !content.includes(pattern.value)) {
        result.pattern_errors.push(`${pattern.path} missing "${pattern.value}"`);
    }
    if (pattern.type === "file_not_contains" && content.includes(pattern.value)) {
        result.pattern_errors.push(`${pattern.path} should not contain "${pattern.value}"`);
    }
}

for (let file of rules.optional || []) {
    if (fs.existsSync(path.join(workspace, file))) {
        result.optional_found.push(file);
    }
}

const totalMandatoryChecks =
    (rules.required?.length || 0) +
    (rules.forbidden?.length || 0) +
    (Object.keys(rules.structure || {}).length) +
    (rules.patterns?.length || 0);

const totalFailures =
    result.missing.length +
    result.forbidden.length +
    result.structure_errors.length +
    result.pattern_errors.length;

let baseScore = totalMandatoryChecks === 0 ? 10 : ((totalMandatoryChecks - totalFailures) / totalMandatoryChecks) * 10;

const bonusPerOptional = 0.2;
const totalBonus = result.optional_found.length * bonusPerOptional;

result.score = Math.min(10, Math.max(0, baseScore + totalBonus)).toFixed(2);
result.score = parseFloat(result.score); 

console.log(JSON.stringify(result, null, 2));