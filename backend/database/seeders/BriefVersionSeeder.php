<?php

namespace Database\Seeders;

use App\Models\Brief;
use App\Models\BriefVersion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BriefVersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $briefs = Brief::all();

        foreach ($briefs as $brief) {
            BriefVersion::create([
                'brief_id' => $brief->id,
                'version' => "1.0",
                'architecture_rules' => [
                    "required" => [
                        "backend/routes/api.php",
                        "backend/app/Http/Controllers"
                    ],
                    "optional" => [
                        "README.md"
                    ],
                    "forbidden" => [
                        ".env",
                        "node_modules",
                        "vendor"
                    ],
                    "structure" => [
                        "backend/routes/api.php" => "file",
                        "backend/app/Http/Controllers" => "dir"
                    ],
                    "patterns" => [
                        [
                            "type" => "file_contains",
                            "path" => "backend/routes/api.php",
                            "value" => "Route::"
                        ]
                    ]
                ],
                'test_config' => [
                    "type" => "http",
                    "timeout_seconds" => 5,
                    "retries" => 1,
                    "tests" => [
                        [
                            "name" => "Get users",
                            "method" => "GET",
                            "url" => "/api/users",
                            "headers" => [
                                [
                                    "name" => "Accept",
                                    "value" => "application/json"
                                ]
                            ],
                            "expected" => [
                                "status" => 200,
                                "json_contains" => [
                                    "id",
                                    "name"
                                ]
                            ]
                        ],
                        [
                            "name" => "Create user",
                            "method" => "POST",
                            "url" => "/api/users",
                            "body" => [
                                "name" => "Test User"
                            ],
                            "expected" => [
                                "status" => 201
                            ]
                        ]
                    ],
                    "base_path" => "/"
                ],
                "created_at" => now(),
                "updated_at" => now()
            ]);
        }
    }
}
