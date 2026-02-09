import type { Formation } from './formation';

export interface Grade {
    id: number
    scholar_year_id: number
    scholar_year: string
    name: string
    capacity: number
    description: string
    created_at: string
    updated_at: string
    deleted_at: null
    formations_count: number
    students_count: number
    is_active: boolean
    is_full: boolean
    is_empty: boolean
    is_almost_full: boolean
    is_nearly_empty: boolean
    formations: Formation[]
}

export interface GradeData {
    grade_levels: Grade[]
    archived_grade_levels: Grade[]
}