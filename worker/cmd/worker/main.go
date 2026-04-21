package main

import (
	"log"

	goredis "github.com/redis/go-redis/v9"
	"worker/internal/jobs"
	myredis "worker/internal/redis"       
)

func main() {
	rdb := goredis.NewClient(&goredis.Options{
		Addr: "redis:6379",
	})

	log.Println("Worker started...")

	myredis.Subscribe(rdb, "evaluation.jobs.created", jobs.HandleJob)
}