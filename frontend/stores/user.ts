import { defineStore } from 'pinia';
import { ref } from 'vue';
import { api } from '~/utils/api';
import type { ReturnData } from '../types/api';
import type { PaginatedUsersData, User, UserData } from '../types/user';

export const useUser = defineStore(
    'user',
    () => {
        const users = ref<User[] | null>(null)
        const user = ref<User | null>(null)
        const archived_users = ref<User[] | null>(null)
        const meta: {
            current_page: number;
            last_page: number;
            next_page_url: string | null;
            prev_page_url: string | null;
            total: number;
            per_page: number;
            from: number;
            to: number;
        } = reactive({
            current_page: 0,
            last_page: 0,
            next_page_url: null,
            prev_page_url: null,
            total: 0,
            per_page: 0,
            from: 0,
            to: 0
        })

        async function fetchUsers(page: number = 1, per_page: number = 5): Promise<void> {
            const res = await api<ReturnData<UserData<PaginatedUsersData>>>(`/admin/users?page=${page}&per_page=${per_page}`)
            users.value = res.data?.users?.data as User[]
            archived_users.value = res.data?.archived_users?.data as User[]
            
            Object.assign(meta, res.data?.users)
        }

        async function fetchUser(role: string, id: number): Promise<void> {
            const res = await api<ReturnData<{ user: User }>>(`/admin/users/${role}/${id}`)
            user.value = res.data?.user as User
        }

        async function fetchTeachersByRole(role: string, class_group?: number): Promise<User[] | null> {
            const res = await api<ReturnData<UserData>>(`/admin/users/${role}_teachers/${class_group ?? ''}`)
            return res.data?.users ?? null
        }

        async function fetchStudents(class_group?: number): Promise<User[] | null> {
            const res = await api<ReturnData<UserData>>(`/admin/users/students/${class_group ?? ''}`)
            return res.data?.users ?? null
        }

        async function fetchTeacherStudents(class_group: number): Promise<User[] | null> {
            const res = await api<ReturnData<UserData>>(`/teacher/class-groups/${class_group}/students`)
            return res.data?.users ?? null
        }

        async function createUser(data: { first_name: string, last_name: string, age: number, email: string, cin: string, phone: string, role: string, password: string }): Promise<ReturnData<any>> {
            try {
                const res = await api<ReturnData<{ user: User, message: string }>>('/admin/users', {
                    method: 'POST',
                    body: data
                })
                users.value?.push(res.data?.user as User)
                return {
                    success: true,
                    data: res.data?.message
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
                        return {
                            success: false,
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
                return {
                    success: false,
                    errors: {
                        first_name: '',
                        last_name: '',
                        age: '',
                        email: '',
                        cin: '',
                        phone: '',
                        role: '',
                        password: '',
                        message: err.data.message
                    },
                }
            }
        }

        async function updateUser(id: number, data: { first_name: string, last_name: string, age: number, email: string, cin: string, phone: string, role: string }): Promise<ReturnData<any>> {
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
                return {
                    success: true,
                    data: res.data?.message
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
                        return {
                            success: false,
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
                return {
                    success: false,
                    errors: {
                        first_name: '',
                        last_name: '',
                        age: '',
                        email: '',
                        cin: '',
                        phone: '',
                        role: '',
                        message: err.message
                    },
                }
            }
        }

        async function archiveUser(id: number): Promise<void> {
            const res = await api<ReturnData<{ user: User }>>(`/admin/users/${id}`, {
                method: 'DELETE'
            })

            if (!users.value?.length) {
                await fetchUsers()
            }

            const restoredUser = res.data?.user as User
            const user = users.value?.find(y => y.id === id) as User

            Object.assign(user, restoredUser)
        }

        async function restoreUser(id: number): Promise<void> {
            const res = await api<ReturnData<{ user: User }>>(`/admin/users/${id}/restore`, {
                method: 'POST'
            })

            if (!users.value?.length) {
                await fetchUsers()
            }

            const restoredUser = res.data?.user as User
            const user = users.value?.find(y => y.id === id) as User

            Object.assign(user, restoredUser)
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
