import { useAuthStore } from '~~/stores/auth';


export default defineNuxtRouteMiddleware((to, from) => {
    const auth = useAuthStore()

    if (!auth.role || auth.role !== 'TEACHER') {
        
        if (auth.role === 'STUDENT') {
            return navigateTo('/student', { replace: true })
        }

        if (auth.role === 'ADMIN') {
            return navigateTo('/admin', { replace: true })
        }

        return navigateTo('/login', { replace: true })
    }
})