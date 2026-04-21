<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    public function up(): void
    {
        Schema::create('brief_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brief_id')->constrained('briefs')->cascadeOnDelete();
            $table->string('version');
            // Architecture Rules Example:
            /*
                "architecture_rules": {
                    "required": [
                        "routes/api.php",
                        "app/Http/Controllers"
                    ],
                    "optional": [
                        "README.md"
                    ],
                    "forbidden": [
                        ".env",
                        "node_modules",
                        "vendor"
                    ],
                    "structure": {
                        "routes/api.php": "file",
                        "app/Http/Controllers": "dir"
                    },
                    "patterns": [
                        {
                            "type": "file_contains",
                            "path": "routes/api.php",
                            "value": "Route::"
                        }
                    ]
                },
            */
            $table->json('architecture_rules');
            // Test Config Example:
            /*
                "test_config": {
                    "type": "http",
                    "base_path": "/",
                    "timeout_seconds": 5,
                    "retries": 1,

                    "tests": [
                        {
                            "name": "Get users",
                            "visibility": "visible",
                            "method": "GET",
                            "url": "/api/users",
                            "expected": {
                                "status": 200,
                                "json_contains": ["id", "name"]
                            }
                        },
                        {
                            "name": "Create user",
                            "visibility": "hidden",
                            "method": "POST",
                            "url": "/api/users",
                            "body": {
                                "name": "Test User"
                            },
                            "expected": {
                                "status": 201
                            }
                        }
                    ]
                },
            */
            $table->json('test_config');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brief_versions');
    }
};
