// internal/redis/subscriber.go
package redis

import (
	"context"
	"log"

	"github.com/redis/go-redis/v9"
)

func Subscribe(rdb *redis.Client, channel string, handler func(string)) {
	ctx := context.Background()

	pubsub := rdb.Subscribe(ctx, channel)

	_, err := pubsub.Receive(ctx)
	if err != nil {
		log.Fatal("Failed to subscribe:", err)
	}

	log.Println("Subscribed to:", channel)

	ch := pubsub.Channel()

	log.Println("Listening on:", channel)

	for msg := range ch {
		log.Println("Received raw:", msg.Payload)
		handler(msg.Payload)
	}
}