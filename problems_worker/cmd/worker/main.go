package main

import (
	"crypto/tls"
	"fmt"
	"log"
	"os"

	"problems_worker/internal/jobs"
	myredis "problems_worker/internal/redis"

	"github.com/joho/godotenv"
	goredis "github.com/redis/go-redis/v9"
)

func main() {
	err := godotenv.Load()
    if err != nil {
        log.Println("Skipping .env file loading: using system environment variables")
    }

    redisHost := os.Getenv("REDIS_HOST")
    redisPort := os.Getenv("REDIS_PORT")
    useTLS := os.Getenv("REDIS_TLS") == "true"

    if redisHost == "" || redisPort == "" {
        log.Fatal("REDIS_HOST or REDIS_PORT is not set in environment")
    }

	opts := &goredis.Options{
		Addr: fmt.Sprintf("%s:%s", redisHost, redisPort),
	}

	if useTLS {
		opts.TLSConfig = &tls.Config{
			MinVersion: tls.VersionTLS12,
		}
	}

	rdb := goredis.NewClient(opts)

	log.Println("Worker started...")

	myredis.Subscribe(rdb, "problem.submissions.jobs.created", jobs.HandleJob)
}