#!/bin/sh

set -e

if [ "$(id -u)" = "0" ]; then
    echo "🔧 Fixing workspace permissions..."
    chown -R runner:runner /workspace
    chown -R runner:runner /output
    exec su-exec runner /app/runner.sh "$@"
fi

echo "🚀 Runner started"

git config --global --add safe.directory /workspace

CONFIG="/config/config.json"

echo "🧠 Running architecture check..."
node /app/arch-check.js "$CONFIG" > /tmp/arch.json


get_json() {
    node -e "
        const data = require('$1');
        const value = data$2;
        if (value === undefined || value === null) {
            process.exit(0);
        }
        console.log(typeof value === 'object' ? JSON.stringify(value) : value);
    "
}

MODE=$(get_json "$CONFIG" ".config.execution.mode")
PORT=$(get_json "$CONFIG" ".config.execution.port")
WORKDIR=$(get_json "$CONFIG" ".config.execution.working_dir")
START_CMD=$(get_json "$CONFIG" ".config.execution.start_command || ''")
TIMEOUT=$(get_json "$CONFIG" ".config.resources.timeout_seconds")
HEALTH_PATH=$(get_json "$CONFIG" ".config.execution.healthcheck.path || '/'")
HEALTH_TIMEOUT=$(get_json "$CONFIG" ".config.execution.healthcheck.timeout_seconds")

[ -z "$WORKDIR" ] && WORKDIR="/app"
[ -z "$PORT" ] && PORT=8000

echo "📦 Mode: $MODE"
echo "📁 Working dir: $WORKDIR"

cd "$WORKDIR"

ARTISAN_PATH=$(find . -maxdepth 3 -name artisan 2>/dev/null | head -n 1)
PACKAGE_PATH=$(find . -maxdepth 3 -name package.json 2>/dev/null | head -n 1)

if [ "$MODE" = "http" ]; then
    echo "🌐 Starting HTTP application..."

    if [ -n "$START_CMD" ]; then
        echo "▶ Using custom start command"
        sh -c "$START_CMD" &
    else

        if [ -n "$ARTISAN_PATH" ]; then
            echo "🧩 Laravel project detected"

            DIR=$(dirname "$ARTISAN_PATH")
            cd "$DIR"

            if [ ! -d "vendor" ]; then
                echo "📦 Installing Composer dependencies..."
                composer install --no-interaction --no-dev --prefer-dist --ignore-platform-reqs
            fi

            export APP_DEBUG=true

            php artisan serve --host=0.0.0.0 --port=$PORT &
        fi

        if [ -n "$PACKAGE_PATH" ] && [ ! -n "$ARTISAN_PATH" ]; then
            echo "🧩 Node project detected"

            DIR=$(dirname "$PACKAGE_PATH")
            cd "$DIR"

            npm install
            npm run dev -- --port=$PORT &
        fi

        if [ -z "$ARTISAN_PATH" ] && [ -z "$PACKAGE_PATH" ]; then
            echo "📄 Static project detected"
            npx serve . -l $PORT &
        fi
    fi

    echo "⏳ Waiting for healthcheck..."

    START_TIME=$(date +%s)

    while true; do
        if curl -s "http://localhost:$PORT$HEALTH_PATH" > /dev/null; then
            echo "✅ Application is ready"
            break
        fi

        NOW=$(date +%s)
        ELAPSED=$((NOW - START_TIME))

        if [ "$ELAPSED" -ge "$HEALTH_TIMEOUT" ]; then
            echo "❌ Healthcheck failed"
            exit 1
        fi

        sleep 0.5
    done
fi

echo "🧪 Running tests..."

if [ "$MODE" = "http" ]; then
    timeout "$TIMEOUT" node /app/http-runner.js "$CONFIG" > /tmp/tests.json
elif [ "$MODE" = "browser" ]; then
    timeout "$TIMEOUT" node /app/browser-runner.js "$CONFIG" > /tmp/tests.json
elif [ "$MODE" = "function" ]; then
    timeout "$TIMEOUT" node /app/function-runner.js "$CONFIG" > /tmp/tests.json
fi

node -e "
const fs = require('fs');

const arch = JSON.parse(fs.readFileSync('/tmp/arch.json'));
const tests = JSON.parse(fs.readFileSync('/tmp/tests.json'));

fs.writeFileSync('/output/result.json', JSON.stringify({
    success: true,
    architecture: arch,
    tests: tests,
    timestamp: new Date().toISOString()
}, null, 2));
"

echo "🎉 Evaluation completed successfully"