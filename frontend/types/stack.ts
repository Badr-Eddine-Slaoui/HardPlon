import type { RunnerVersion } from "./runner_version";

export interface Stack {
    id: number;
    name: string;
    description: string | null;
    stack_runners?: StackRunner[];
    created_at?: string;
    updated_at?: string;
    deleted_at?: string | null;
}

export interface StackRunner {
    id: number;
    stack_id: number;
    runner_version_id: number;
    priority: number;
    runner_version?: RunnerVersion;
    created_at?: string;
    updated_at?: string;
    deleted_at?: string | null;
}
