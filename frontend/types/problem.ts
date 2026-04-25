import type { Brief } from "./brief";
import type { Language } from "./language";
import type { ProblemTestCase } from "./problem_test_case";

export interface Problem {
    id: number;
    brief_id: number;
    brief_skill_level_id: number;
    language_id: number;
    title: string;
    description: string;
    hidden_header: string | null;
    hidden_footer: string | null;
    skeleton_code: string | null;
    order_index: number;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;

    // Relationships
    brief?: Brief;
    language?: Language;
    test_cases?: ProblemTestCase[];
    brief_skill_level?: {
        id: number;
        skill_id: number;
        level_id: number;
        skill: {
            id: number;
            name: string;
        };
        level: {
            id: number;
            name: string;
        };
    };
}
