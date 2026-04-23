import type { Formation } from "./formation"
import type { ClassTeacher, User } from "./user"

export interface ClassGroup {
    id: number
    formation_id: number
    name: string
    image_url: string
    capacity: number
    description: string
    created_at: string
    updated_at: string
    deleted_at: string | null
    students_count: number
    is_active: boolean
    formation: Formation
    students: User[]
    main_teacher: ClassTeacher
    sub_teacher: ClassTeacher
}