package main

import (
	"fmt"
	"log"
	"os"

	"worker/internal/jobs"
	myredis "worker/internal/redis"

	"github.com/joho/godotenv"
	goredis "github.com/redis/go-redis/v9"
)

func main() {

	err := godotenv.Load()
	if err != nil {
		log.Fatal("Error loading .env file")
	}

	redisHost := os.Getenv("REDIS_HOST")
	redisPort := os.Getenv("REDIS_PORT")

	rdb := goredis.NewClient(&goredis.Options{
		Addr: fmt.Sprintf("%s:%s", redisHost, redisPort),
	})

	log.Println("Worker started...")

	myredis.Subscribe(rdb, "evaluation.jobs.created", jobs.HandleJob)
}