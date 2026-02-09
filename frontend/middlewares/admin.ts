import { useAuthStore } from '../stores/auth';


export default defineNuxtRouteMiddleware(async (to, from) => {
    const auth = useAuthStore()

    await auth.fetchUser()

    if (!auth.user.role || auth.user.role !== 'ADMIN') {
        
        if (auth.user.role === 'STUDENT') {
            return navigateTo('/student')
        }

        if (auth.user.role === 'TEACHER') {
            return navigateTo('/teacher')
        }

        return navigateTo('/login')
    }
})