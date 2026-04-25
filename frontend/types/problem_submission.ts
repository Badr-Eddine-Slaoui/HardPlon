import type { Problem } from './problem';

export interface ProblemSubmission {
    id: number;
    submission_id: number;
    problem_id: number;
    code: string;
    result?: any;
    status: 'pending' | 'processing' | 'completed' | 'failed';
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
    problem: Problem;
}
