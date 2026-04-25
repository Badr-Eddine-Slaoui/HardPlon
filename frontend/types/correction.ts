import type { BriefSkillLevel, Brief } from './brief';
import type { User } from './user';

export interface CorrectionDetail {
    id: number;
    correction_id: number;
    brief_skill_level_id: number;
    grade: string;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
    brief_skill_level: BriefSkillLevel
}

export interface Correction {
    id: number;
    brief_id: number;
    submission_id: number;
    student_id: number;
    teacher_id: number;
    message: string;
    result: string;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
    brief: Brief;
    teacher: User;
    student: User;
    correction_details: CorrectionDetail[];
}