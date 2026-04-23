import type { User } from "./user";

export interface AuthResponse {
    access_token: string;
    token_type: string;
    user: User;
}

export interface ReturnData<T = null>{
    success: boolean;
    data: T | null;
    message: string;
    errors?: any;
}

export interface meta {
    current_page: number;
    last_page: number;
    next_page_url: string | null;
    prev_page_url: string | null;
    total: number;
    per_page: number;
    from: number;
    to: number;
}