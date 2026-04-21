import type { User } from './user';
import type { Brief } from './brief';

export interface Submission {
    id: number;
    brief_id: number;
    student_id: number;
    squad_id: number | null;
    message: string;
    links: { [key: string]: string}[];
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
    student: User;
    squad: any;
    brief: Brief;
}