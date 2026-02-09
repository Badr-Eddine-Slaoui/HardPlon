import type { Grade } from './grade_level';
export interface Year {
    id: number
    start_year: number
    end_year: number
    capacity: number
    created_at: string
    updated_at: string
    deleted_at: string | null
    grade_levels_count: number
    students_count: number
    is_active: boolean
    is_current: boolean
    is_past: boolean
    is_upcoming: boolean
    grade_levels: Grade[]
}

export interface YearData {
    scholar_years: Year[]
    archived_years: Year[]
    current_year: Year
}