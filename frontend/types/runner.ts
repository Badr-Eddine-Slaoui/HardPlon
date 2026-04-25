import type { RunnerVersion } from './runner_version';

export interface Runner {
    id: number;
    name: string;
    description: string;
    type: string;
    status: string;
    created_at: string;
    updated_at: string;
    versions?: RunnerVersion[];
}
