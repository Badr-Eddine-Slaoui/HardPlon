import { useAuthStore } from '../stores/auth';


export default defineNuxtRouteMiddleware((to, from) => {
    const auth = useAuthStore()

    if (!user.isLoggedIn) {
        return navigateTo('/login')
    }
})
