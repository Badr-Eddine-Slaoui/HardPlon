import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { AuthResponse, ReturnData } from '~~/types/api';



export const useAuthStore = defineStore(
    'auth',
    () => {
        const user = ref<any>(null)
        const token = ref<string | null>(null)

        const isLoggedIn = computed(() => !!token.value)

        async function login(data: { email: string; password: string }): Promise<ReturnData<any>> {
            try {
                const res = await api<AuthResponse>('/login', {
                    method: 'POST',
                    body: data,
                })
                token.value = res.access_token
                user.value = res.user
                return {
                    success: true,
                    data: token.value,
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    if (err.data.errors.email || err.data.errors.password) {
                        let { email, password } = err.data.errors;
                        email = email ? email[0] : "";
                        password = password ? password[0] : "";
                        return {
                            success: false,
                            errors: {
                                email,
                                password,
                                login: ''
                            },
                        }
                    }
                }

                if(err?.data?.message) {
                    return {
                        success: false,
                        errors: {
                            email: '',
                            password: '',
                            login: err.data.message ?? '',
                        },
                    }
                }

                return {
                    success: false,
                    errors: null,
                }
            }
        }

        async function fetchUser() {
            user.value = await api<any>('/me')
        }

        async function logout() {
            await api<any>('/logout', { method: 'POST' })
            token.value = null
            user.value = null
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
