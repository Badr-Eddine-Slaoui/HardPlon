import type { Skill } from './skill';
import type { Formation } from './formation';

export interface SprintSkill {
    sprint_id: number;
    skill_id: number;
    created_at: string | null;
    updated_at: string | null;
    deleted_at: string | null;
    skill: Skill;
}

export interface Sprint {
    id: number;
    formation_id: number;
    name: string;
    description: string;
    start_date: string;
    end_date: string;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
    is_active: boolean;
    formation?: Formation;
    sprint_skills?: SprintSkill[];
}

export interface SprintData {
    sprints: Sprint[]
    archived_sprints: Sprint[]
}