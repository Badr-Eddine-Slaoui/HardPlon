import { defineStore } from 'pinia';
import { ref } from 'vue';
import { api } from '~/utils/api';
import type { ReturnData } from '../types/api';
import type { User, UserData } from '../types/user';

export const useUser = defineStore(
    'user',
    () => {
        const users = ref<User[] | null>(null)
        const user = ref<User | null>(null)
        const archived_users = ref<User[] | null>(null)

        async function fetchUsers(): Promise<void> {
            const res = await api<ReturnData<UserData>>('/admin/users')
            users.value = res.data?.users as User[]
            archived_users.value = res.data?.archived_users as User[]
        }

        async function fetchUser(role: string, id: number): Promise<void> {
            const res = await api<ReturnData<{ user: User}>>(`/admin/users/${role}/${id}`)
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
            try{
                const res = await api<ReturnData<{ user: User, message: string}>>('/admin/users', {
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
            try{
                const res = await api<ReturnData<{ user: User, message: string}>>(`/admin/users/${id}`, {
                    method: 'PUT',
                    body: data
                })

                if(!users.value?.length){
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

            if(!users.value?.length){
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

            if(!users.value?.length){
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
    },
    {
        persist: {
            pick: ['users', 'archived_users'],
        },
    }
)
