import type { User } from './user';
import type { Brief } from './brief';
import type { ProblemSubmission } from './problem_submission';
import type { Correction } from './correction';

export interface EvaluationJob {
    id: number;
    submission_id: number;
    status: 'pending' | 'processing' | 'completed' | 'failed';
    result?: {
        success: boolean;
        architecture: {
            score: number;
            missing: string[];
            forbidden: string[];
            structure_errors: string[];
            pattern_errors: string[];
            optional_found: string[];
        };
        tests: {
            score: number;
            tests: Array<{
                name: string;
                passed: boolean;
                status: number;
            }>;
        };
        timestamp: string;
    };
    error?: string;
    created_at: string;
    updated_at: string;
}

export interface ProblemSubmissionJob {
    id: number;
    submission_id: number;
    status: 'pending' | 'running' | 'completed' | 'failed';
    result?: {
        submissions: Array<{
            problem_id: number;
            score: number;
            tests: Array<{
                test_case_id: number;
                passed: boolean;
                input: string;
                expected: string;
                actual: string;
            }>;
        }>;
        total_score: number;
    };
    created_at: string;
    updated_at: string;
}

export interface Submission {
    id: number;
    brief_id: number;
    student_id: number;
    squad_id: number | null;
    message: string;
    link: string;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
    student: User;
    squad: any;
    brief: Brief;
    evaluation_job?: EvaluationJob;
    problem_submissions?: ProblemSubmission[];
    problem_submission_job?: ProblemSubmissionJob;
    correction?: Correction;
}