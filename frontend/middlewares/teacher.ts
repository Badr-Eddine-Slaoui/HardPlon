import { useAuthStore } from '../stores/auth';


export default defineNuxtRouteMiddleware(async (to, from) => {
    const auth = useAuthStore()

    await auth.fetchUser()

    if (!auth.user.role || auth.user.role !== 'TEACHER') {
        
        if (auth.user.role === 'STUDENT') {
            return navigateTo('/student')
        }

        if (auth.user.role === 'ADMIN') {
            return navigateTo('/admin')
        }

        return navigateTo('/login')
    }
})