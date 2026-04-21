package main

import (
	"log"

	goredis "github.com/redis/go-redis/v9"
	"problems_worker/internal/jobs"
	myredis "problems_worker/internal/redis"       
)

func main() {
	rdb := goredis.NewClient(&goredis.Options{
		Addr: "redis:6379",
	})

	log.Println("Worker started...")

	myredis.Subscribe(rdb, "problem.submissions.jobs.created", jobs.HandleJob)
}