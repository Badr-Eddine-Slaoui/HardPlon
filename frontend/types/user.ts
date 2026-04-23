import type { ClassGroup } from "./class_group";

export interface ClassTeacher {
    id: number
    class_group_id: number
    teacher_id: number
    role: string
    assigned_at: string
    created_at: string
    updated_at: string
    deleted_at: string | null
    class_group: ClassGroup
    teacher: User
}

export interface User {
    id: number
    first_name: string
    last_name: string
    age: number
    email: string
    cin: string
    phone: string
    role: string
    id_class: null
    created_at: string
    updated_at: string
    deleted_at: string | null
    is_active: boolean
    class_teachers?: ClassTeacher[]
    class_group?: ClassGroup
}

