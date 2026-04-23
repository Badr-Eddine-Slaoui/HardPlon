import type { Grade } from "./grade_level"

export interface Formation {
    id: number
    grade_level_id: number
    grade_name: string
    title: string
    description: string
    duration: number
    capacity: number
    created_at: string
    updated_at: string
    deleted_at: string | null
    class_groups_count: number
    students_count: number
    is_active: boolean
    is_full: boolean
    is_empty: boolean
    is_almost_full: boolean
    is_nearly_empty: boolean
    class_groups: any
    grade_level?: Grade
}