import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { AuthResponse, ReturnData } from '~~/types/api';
import type { User } from '~~/types/user';
import { useToastStore } from './toast';



export const useAuthStore = defineStore(
    'auth',
    () => {
        const api = useApi()
        const user = ref<any>(null)
        const token = ref<string | null>(null)

        const toast = useToastStore()

        const isLoggedIn = computed(() => !!token.value)

        async function login(data: { email: string; password: string }): Promise<ReturnData<any>> {
            try {
                const res = await api<ReturnData<AuthResponse>>('/login', {
                    method: 'POST',
                    body: data,
                })

                token.value = res.data?.access_token as string
                user.value = res.data?.user as User

                toast.push({
                    message: 'Login successful',
                    type: 'success',
                })

                return {
                    success: true,
                    data: token.value,
                    message: res.message,
                }

            } catch (err: any) {
                if (err?.data?.errors) {
                    if (err.data.errors.email || err.data.errors.password) {
                        let { email, password } = err.data.errors;
                        email = email ? email[0] : "";
                        password = password ? password[0] : "";

                        toast.push({
                            message: 'Validation errors',
                            type: 'error',
                        })

                        return {
                            success: false,
                            data: null,
                            message: 'Validation errors',
                            errors: {
                                email,
                                password,
                                login: ''
                            },
                        }
                    }
                }

                if(err?.data?.message) {

                    toast.push({
                        message: err.data.message,
                        type: 'error',
                    })

                    return {
                        success: false,
                        data: null,
                        message: err.data.message,
                        errors: {
                            email: '',
                            password: '',
                            login: err.data.message ?? '',
                        },
                    }
                }
                
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })

                
                return {
                    success: false,
                    data: null,
                    message: 'Something went wrong. Please try again.',
                    errors: null,
                }
            }
        }

        async function fetchUser() {
            try {
                user.value = (await api<ReturnData<{ user: User }>>('/me')).data?.user
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function logout() {
            try {
                const res = await api<ReturnData>('/logout', { method: 'POST' })
                token.value = null
                user.value = null
                toast.push({
                    message: res.message,
                    type: 'success',
                })
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        const decoded = computed(() =>
            token.value ? decodeJwt(token.value as string) : null
        )

        const role = computed(() => decoded.value?.role ?? null)

        function checkTockenExpiration() {
            if (!decoded.value) return true
            if (!decoded.value.exp) return true
            return decoded.value.exp * 1000 < Date.now()
        }

        return {
            user,
            token,
            decoded,
            role,
            checkTockenExpiration,
            isLoggedIn,
            login,
            fetchUser,
            logout,
        }
    },
    {
        persist: {
            pick: ['token'],
        },
    }
)
