import type { Formation } from './formation';
import type { Level } from './level';

export interface SkillLevel {
    skill_id: number;
    level_id: number;
    criteria: string;
    created_at: string | null;
    updated_at: string | null;
    deleted_at: string | null;
    level: Level;
}

export interface Skill {
    id: number;
    formation_id: number;
    code: string;
    title: string;
    description: string;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
    is_active: boolean;
    formation?: Formation;
    skill_levels?: SkillLevel[];
}