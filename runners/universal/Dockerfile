FROM node:20-alpine

RUN apk add --no-cache \
    bash git curl chromium nss freetype harfbuzz ttf-freefont coreutils su-exec \
    php83 php83-common php83-cli php83-mbstring php83-xml php83-dom \
    php83-iconv php83-fileinfo php83-session php83-pdo php83-tokenizer \
    php83-openssl php83-phar php83-curl

RUN ln -sf /usr/bin/php83 /usr/bin/php

ENV PLAYWRIGHT_BROWSERS_PATH=/ms-playwright
ENV PLAYWRIGHT_SKIP_BROWSER_DOWNLOAD=1

WORKDIR /app

RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin \
    --filename=composer

COPY package.json package-lock.json* ./
RUN npm install --omit=dev

RUN npm install -g serve

COPY . .

RUN adduser -D runner
RUN mkdir -p /app/vendor /output /config && chown -R runner:runner /app
RUN chmod +x /app/runner.sh

EXPOSE 8000

CMD ["./runner.sh"]