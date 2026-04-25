import type { Runner } from './runner';

export interface RunnerVersion {
    id: number;
    runner_id: number;
    version: string;
    docker_image: string;
    default_config: Record<string, any>;
    status: string;
    is_default: boolean;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
    runner?: Runner;
}
