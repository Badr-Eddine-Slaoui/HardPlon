import { defineStore } from 'pinia';
import { reactive, ref } from 'vue';
import type { meta, ReturnData } from '../types/api';
import type { User, UserData } from '../types/user';
import type { PaginatedData } from '../types/pagination';
import { useToastStore } from './toast';

export const useUser = defineStore(
    'user',
    () => {
        const api = useApi()
        const users = ref<User[] | null>(null)
        const user = ref<User | null>(null)
        const archived_users = ref<User[] | null>(null)
        const meta: meta = reactive({
            current_page: 0,
            last_page: 0,
            next_page_url: null,
            prev_page_url: null,
            total: 0,
            per_page: 0,
            from: 0,
            to: 0
        })
        const toast = useToastStore()

        async function fetchUsers(page: number = 1, per_page: number = 5): Promise<void> {
            try {
                const res = await api<ReturnData<{ users: PaginatedData<User[]>, archived_users: PaginatedData<User[]>}>>(`/admin/users?page=${page}&per_page=${per_page}`)
                users.value = res.data?.users?.data as User[]
                archived_users.value = res.data?.archived_users?.data as User[]
                
                Object.assign(meta, res.data?.users)
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function fetchUser(role: string, id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ user: User }>>(`/admin/users/${role}/${id}`)
                user.value = res.data?.user as User
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function fetchTeachersByRole(role: string, class_group?: number): Promise<User[] | null> {
            try {
                const res = await api<ReturnData<UserData>>(`/admin/users/${role}_teachers/${class_group ?? ''}`)
                return res.data?.users ?? null
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
                return null
            }
        }

        async function fetchStudents(class_group?: number): Promise<User[] | null> {
            try {
                const res = await api<ReturnData<UserData>>(`/admin/users/students/${class_group ?? ''}`)
                return res.data?.users ?? null
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
                return null
            }
        }

        async function fetchTeacherStudents(class_group: number): Promise<User[] | null> {
            try {
                const res = await api<ReturnData<UserData>>(`/teacher/class-groups/${class_group}/students`)
                return res.data?.users ?? null
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
                return null
            }
        }

        async function createUser(data: { first_name: string, last_name: string, age: number, email: string, cin: string, phone: string, role: string, password: string }): Promise<ReturnData> {
            try {
                const res = await api<ReturnData<{ user: User, message: string }>>('/admin/users', {
                    method: 'POST',
                    body: data
                })

                if (!users.value?.length) {
                    await fetchUsers()
                }

                users.value?.unshift(res.data?.user as User)

                toast.push({
                    message: res?.message || 'User created successfully.',
                    type: 'success',
                })

                return {
                    success: true,
                    message: res?.message || 'User created successfully.',
                    data: null
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    if (err.data.errors.first_name || err.data.errors.last_name || err.data.errors.age || err.data.errors.email || err.data.errors.cin || err.data.errors.phone || err.data.errors.role || err.data.errors.password) {
                        let { first_name, last_name, age, email, cin, phone, role, password } = err.data.errors;
                        first_name = first_name ? first_name[0] : "";
                        last_name = last_name ? last_name[0] : "";
                        age = age ? age[0] : "";
                        email = email ? email[0] : "";
                        cin = cin ? cin[0] : "";
                        phone = phone ? phone[0] : "";
                        role = role ? role[0] : "";
                        password = password ? password[0] : "";

                        toast.push({
                            message: "Validation error. Please check your input.",
                            type: 'error',
                        })

                        return {
                            success: false,
                            message: "Validation error. Please check your input.",
                            data: null,
                            errors: {
                                first_name,
                                last_name,
                                age,
                                email,
                                cin,
                                phone,
                                role,
                                password
                            },
                        }
                    }
                }

                toast.push({
                    message: err.message || 'Something went wrong. Please try again.',
                    type: 'error',
                })

                return {
                    success: false,
                    message: err.message || 'Something went wrong. Please try again.',
                    data: null,
                    errors: {
                        first_name: '',
                        last_name: '',
                        age: '',
                        email: '',
                        cin: '',
                        phone: '',
                        role: '',
                        password: '',
                        message: err.message || 'Something went wrong. Please try again.'
                    },
                }
            }
        }

        async function updateUser(id: number, data: { first_name: string, last_name: string, age: number, email: string, cin: string, phone: string, role: string }): Promise<ReturnData> {
            try {
                const res = await api<ReturnData<{ user: User, message: string }>>(`/admin/users/${id}`, {
                    method: 'PUT',
                    body: data
                })

                if (!users.value?.length) {
                    await fetchUsers()
                }

                const restoredUser = res.data?.user as User
                const user = users.value?.find(y => y.id === id) as User

                Object.assign(user, restoredUser)

                toast.push({
                    message: res?.message || 'User updated successfully.',
                    type: 'success',
                })
                
                return {
                    success: true,
                    message: res?.message || 'User updated successfully.',
                    data: null
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    if (err.data.errors.first_name || err.data.errors.last_name || err.data.errors.age || err.data.errors.email || err.data.errors.cin || err.data.errors.phone || err.data.errors.role) {
                        let { first_name, last_name, age, email, cin, phone, role } = err.data.errors;
                        first_name = first_name ? first_name[0] : "";
                        last_name = last_name ? last_name[0] : "";
                        age = age ? age[0] : "";
                        email = email ? email[0] : "";
                        cin = cin ? cin[0] : "";
                        phone = phone ? phone[0] : "";
                        role = role ? role[0] : "";

                        toast.push({
                            message: "Validation error. Please check your input.",
                            type: 'error',
                        })

                        return {
                            success: false,
                            message: "Validation error. Please check your input.",
                            data: null,
                            errors: {
                                first_name,
                                last_name,
                                age,
                                email,
                                cin,
                                phone,
                                role
                            },
                        }
                    }
                }

                toast.push({
                    message: err.message || 'Something went wrong. Please try again.',
                    type: 'error',
                })

                return {
                    success: false,
                    message: err.message || 'Something went wrong. Please try again.',
                    data: null,
                    errors: {
                        first_name: '',
                        last_name: '',
                        age: '',
                        email: '',
                        cin: '',
                        phone: '',
                        role: '',
                        message: err.message || 'Something went wrong. Please try again.'
                    },
                }
            }
        }

        async function archiveUser(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ user: User }>>(`/admin/users/${id}`, {
                    method: 'DELETE'
                })

                if (!users.value?.length) {
                    await fetchUsers()
                }

                const restoredUser = res.data?.user as User
                const user = users.value?.find(y => y.id === id) as User

                Object.assign(user, restoredUser)

                toast.push({
                    message: res?.message || 'User archived successfully.',
                    type: 'success',
                })
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function restoreUser(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ user: User }>>(`/admin/users/${id}/restore`, {
                    method: 'POST'
                })

                if (!users.value?.length) {
                    await fetchUsers()
                }

                const restoredUser = res.data?.user as User
                const user = users.value?.find(y => y.id === id) as User

                Object.assign(user, restoredUser)

                toast.push({
                    message: res?.message || 'User restored successfully.',
                    type: 'success',
                })
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        return {
            users,
            user,
            archived_users,
            meta,
            fetchUsers,
            fetchTeachersByRole,
            fetchStudents,
            fetchTeacherStudents,
            fetchUser,
            createUser,
            updateUser,
            archiveUser,
            restoreUser
        }
    }
)
