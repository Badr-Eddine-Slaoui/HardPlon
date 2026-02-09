import { useAuthStore } from '~~/stores/auth';

export default defineNuxtRouteMiddleware(async (to, from) => {
    const auth = useAuthStore()
    
    if (!auth.token || auth.checkTockenExpiration()) {
        return navigateTo('/login', { replace: true })
    }

    return 
})