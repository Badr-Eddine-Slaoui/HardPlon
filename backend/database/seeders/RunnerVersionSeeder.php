<?php

namespace Database\Seeders;

use App\Models\RunnerVersion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RunnerVersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RunnerVersion::create([
            "runner_id" => 1,
            "version" => "v1.0",
            "docker_image" => "badreddineslaoui/universal-runner:1.0",
            "status" => "active",
            "default_config" => [
                "execution" => [
                    "mode" => "http",
                    "port" => 8000,
                    "start_command" => null,
                    "working_dir" => "/workspace",
                    "healthcheck" => [
                        "type" => "http",
                        "path" => "/",
                        "timeout_seconds" => 10,
                        "interval_ms" => 500
                    ]
                ],
                "resources" => [
                    "cpu" => 1,
                    "memory_mb" => 200,
                    "timeout_seconds" => 15,
                    "pids_limit" => 100
                ],
                "filesystem" => [
                    "read_only" => false,
                    "workspace_path" => "/workspace",
                    "output_path" => "/output",
                    "config_path" => "/config"
                ],
                "network" => [
                    "enabled" => true
                ],
                "features" => [
                    "php" => false,
                    "node" => false,
                    "sqlite" => false
                ],
                "fallback" => [
                    "on_failure" => "retry_with_universal"
                ],
            ],
            "is_default" => false,
            "created_at" => now(),
            "updated_at" => now()
        ]);
    }
}
