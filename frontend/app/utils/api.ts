import { useAuthStore } from '~~/stores/auth';

export const api = $fetch.create({
    baseURL: "http://localhost:3535/api",

    onRequest({ options }) {
        const auth = useAuthStore()

        options.headers = new Headers(options.headers)

        options.headers.set('Accept', 'application/json')
        options.headers.set('Content-Type', 'application/json')
        options.headers.set('Access-Control-Allow-Origin', '*')

        if (auth.token) {
            options.headers.set(
                'Authorization',
                `Bearer ${auth.token}`
            )
        }
    },
})
