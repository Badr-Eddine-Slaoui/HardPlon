import { useAuthStore } from '~~/stores/auth'

export default defineNuxtRouteMiddleware((to) => {
    const auth = useAuthStore()

    if (!auth.isLoggedIn || auth.checkTockenExpiration() || !auth.token) return

    switch (auth.role) {
        case 'ADMIN':
            return navigateTo('/admin', { replace: true })
        case 'TEACHER':
            return navigateTo('/teacher', { replace: true })
        case 'STUDENT':
            return navigateTo('/student', { replace: true })
    }
})
