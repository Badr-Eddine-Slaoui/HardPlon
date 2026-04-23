import type { Skill } from './skill';
import type { Level } from './level';
import type { Sprint } from './sprint';
import type { ClassGroup } from './class_group';
import type { User } from './user';

export interface BriefSkillLevel {
    id: number;
    brief_id: number;
    skill_id: number;
    level_id: number;
    created_at: string | null;
    updated_at: string | null;
    deleted_at: string | null;
    skill: Skill;
    level: Level;
}

export interface Brief {
    id: number;
    sprint_id: number;
    class_group_id: number;
    teacher_id: number;
    title: string;
    description: string;
    content: string;
    is_collective: boolean;
    start_date: string;
    end_date: string;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
    is_active: boolean;
    sprint: Sprint;
    class_group: ClassGroup;
    teacher: User;
    brief_skill_levels: BriefSkillLevel[];
}