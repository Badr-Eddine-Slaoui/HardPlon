import type { Problem } from "./problem";

export interface ProblemTestCase {
    id: number;
    problem_id: number;
    input: any[];
    expected_output: any[];
    is_hidden: boolean;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;

    // Relationships
    problem?: Problem;
}
