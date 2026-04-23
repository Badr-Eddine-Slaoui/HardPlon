export interface Level {
    id: number;
    name: string;
    description: string;
    order: number;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
    is_active: boolean;
}