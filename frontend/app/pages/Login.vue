<script setup lang="ts">
    import type { ReturnData } from '~~/types/api';
    import { useAuthStore } from '../../stores/auth';
    const auth = useAuthStore()

    const form = reactive({
        email: '',
        password: '',
    })

    const errs = ref({
        email: '',
        password: '',
        login: '',
    })

    const submit = async () => {
        const res : ReturnData<any> = await auth.login(form);
        if(res.success) {
            if(auth.user?.role === 'ADMIN') {
                navigateTo('/admin')
            }
            else if(auth.user?.role === 'TEACHER') {
                navigateTo('/teacher')
            }
            else if(auth.user?.role === 'STUDENT') {
                navigateTo('/student')
            }
        }
        
        if(res.errors) {
            errs.value = res.errors
        }
    }

    definePageMeta({
        middleware: [
            'guest',
        ],
    })

</script>

<template>
    <NuxtLayout name="login">
        <header
            class="flex items-center justify-between whitespace-nowrap border-b border-solid border-slate-200 dark:border-slate-800 px-10 py-3 bg-white/5 backdrop-blur-md">
            <div class="flex items-center gap-4 text-white">
                <div class="size-8 text-pirate-gold">
                    <span class="material-symbols-outlined text-4xl">sailing</span>
                </div>
                <h2 class="text-white text-xl font-bold leading-tight tracking-[-0.015em]">Set Sail</h2>
            </div>
            <div class="flex flex-1 justify-end gap-8">
                <div class="hidden md:flex items-center gap-9">
                    <a class="text-slate-400 hover:text-primary text-sm font-medium leading-normal transition-colors"
                        href="#">Rules of the Sea</a>
                    <a class="text-slate-400 hover:text-primary text-sm font-medium leading-normal transition-colors"
                        href="#">Privacy Policy</a>
                </div>
                <button
                    class="flex min-w-[84px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-primary text-background-dark text-sm font-bold leading-normal tracking-[0.015em] hover:bg-white transition-all">
                    <span class="truncate">Support</span>
                </button>
            </div>
        </header>
        <main class="flex-1 flex flex-col items-center justify-center p-6 nautical-bg relative">
            <!-- Floating Compass Icon Decor -->
            <div class="absolute top-10 right-10 opacity-10 pointer-events-none">
                <span class="material-symbols-outlined text-[200px] text-primary">explore</span>
            </div>
            <div class="w-full max-w-[480px] z-10">
                <!-- Header Image Area -->
                <div class="mb-2">
                    <div class="bg-cover bg-center flex flex-col justify-end overflow-hidden bg-slate-900 rounded-t-xl min-h-[160px] border-x border-t border-slate-800"
                        data-alt="Illustration of a sunset over the Grand Line ocean"
                        style='background-image: linear-gradient(0deg, rgba(10, 15, 20, 1) 0%, rgba(10, 15, 20, 0) 60%), url("https://lh3.googleusercontent.com/aida-public/AB6AXuA5yiwFb00DuchK21wszZxLv8dAELZdxhNzoH6-LdfTnaSqKc75ymm_Uu2Ocrw87vIZ5kS8LnGHK_VwBXjn1qSFGgglvrVy6sRwinkOiIT36hRmC9Pxr-JxpzAQifPWcH2ptvml26QBo1DDhOtNSlyWoUOKNmPAtxV7NX7CrL6tAk7McjA2AYSpMWxYnBdiKxISJSzK4hPgTj-aaALO7DCJRdKHrMKjhkGdVV9eBhKfa4UZqbPQ2-ou0C02buwRvpoLrgGMPrvLilg");'>
                        <div class="p-6">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="material-symbols-outlined text-pirate-gold">stars</span>
                                <span class="text-pirate-gold text-xs font-bold uppercase tracking-widest">Official
                                    Portal</span>
                            </div>
                            <p class="text-white tracking-light text-3xl font-bold leading-tight">Grand Line Academy</p>
                        </div>
                    </div>
                </div>
                <!-- Main Login Card -->
                <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-b-xl shadow-2xl p-8">
                    <!-- Headline/Meta Text -->
                    <div class="text-center mb-8">
                        <h1 class="text-white text-2xl font-bold mb-2">Welcome to the Academy</h1>
                        <p class="text-slate-400 text-sm">Chart your course to knowledge across the four blues.</p>
                    </div>
                    <!-- Form -->
                    <form @submit.prevent="submit" class="space-y-5">
                        <div>
                            <label class="block text-slate-400 text-xs font-bold uppercase tracking-wider mb-2"
                                for="username">Navigator ID</label>
                            <div class="relative">
                                <span
                                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-xl">account_circle</span>
                                <input v-model="form.email"
                                    class="w-full bg-slate-950/50 border border-slate-700 rounded-lg py-3 pl-11 pr-4 text-white focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all placeholder:text-slate-600"
                                    id="username" placeholder="Enter your Email" type="text"/>
                            </div>
                            <span v-if="errs.email" class="text-sm text-rose-600">{{ errs.email }}</span>
                        </div>
                        <div>
                            <div class="flex justify-between mb-2">
                                <label class="block text-slate-400 text-xs font-bold uppercase tracking-wider"
                                    for="password">Secret Code</label>
                            </div>
                            <div class="relative">
                                <span
                                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-xl">key</span>
                                <input v-model="form.password"
                                    class="w-full bg-slate-950/50 border border-slate-700 rounded-lg py-3 pl-11 pr-4 text-white focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all placeholder:text-slate-600"
                                    id="password" placeholder="••••••••" type="password" />
                            </div>
                            <span v-if="errs.password" class="text-sm text-rose-600">{{ errs.password }}</span>
                            <span v-if="errs.login" class="text-sm text-rose-600">{{ errs.login }}</span>
                        </div>
                        <div class="flex items-center gap-2 py-2">
                            <input
                                class="rounded border-slate-700 bg-slate-950 text-primary focus:ring-primary focus:ring-offset-slate-900"
                                id="remember" type="checkbox" />
                            <label class="text-sm text-slate-400" for="remember">Stay aboard (30 days)</label>
                        </div>
                        <button
                            class="w-full bg-primary hover:bg-white hover:scale-[1.02] active:scale-[0.98] text-background-dark font-bold py-4 rounded-lg shadow-[0_0_20px_rgba(13,204,242,0.3)] transition-all flex items-center justify-center gap-2 group"
                            type="submit">
                            <span>Board the Ship</span>
                            <span
                                class="material-symbols-outlined text-xl group-hover:translate-x-1 transition-transform">east</span>
                        </button>
                    </form>
                </div>
                <!-- Footer Small -->
                <footer class="mt-8 text-center text-slate-600 text-xs flex flex-col gap-2">
                    <p>© 1522-1524 Grand Line Academy. All rights reserved.</p>
                    <div class="flex justify-center gap-4">
                        <a class="hover:text-primary" href="#">Ship Guidelines</a>
                        <a class="hover:text-primary" href="#">Terms of Voyage</a>
                    </div>
                </footer>
            </div>
        </main>
    </NuxtLayout>
</template>