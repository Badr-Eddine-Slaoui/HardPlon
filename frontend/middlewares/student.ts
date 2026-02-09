import { useAuthStore } from '../stores/auth';


export default defineNuxtRouteMiddleware(async (to, from) => {
    const auth = useAuthStore()

    await auth.fetchUser()

    if (!auth.user.role || auth.user.role !== 'STUDENT') {
        
        if (auth.user.role === 'TEACHER') {
            return navigateTo('/teacher')
        }

        if (auth.user.role === 'ADMIN') {
            return navigateTo('/admin')
        }

        return navigateTo('/login')
    }
})