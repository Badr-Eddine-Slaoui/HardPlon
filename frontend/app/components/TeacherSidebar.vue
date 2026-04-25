<script setup lang="ts">
    import { useRoute } from '#imports'
    import { useAuthStore } from '~~/stores/auth';

    const auth = useAuthStore()

    const route = useRoute()

    const isActive = (r: string, deep = false) => {
        const active = deep
            ? route.path.startsWith(r)
            : route.path === r

        return active
            ? 'flex items-center gap-3 px-4 py-3 rounded-lg bg-primary/10 text-primary border border-primary/20'
            : 'flex items-center gap-3 px-4 py-3 rounded-lg text-slate-600 dark:text-[#90c1cb] hover:bg-slate-100 dark:hover:bg-[#224249] transition-colors'
    }

    const submit = async () => {
        await auth.logout();
        navigateTo('/login');
    }
</script>

<template>
    <aside
        class="w-72 bg-background-light dark:bg-background-dark border-r border-slate-200 dark:border-[#224249] flex flex-col h-screen sticky top-0">
        <div class="p-6 flex flex-col h-full">
            <div class="flex items-center gap-3 mb-10">
                <div class="bg-primary rounded-lg p-2 flex items-center justify-center">
                    <span class="material-symbols-outlined text-white text-3xl">sailing</span>
                </div>
                <div class="flex flex-col">
                    <h1 class="text-lg font-bold leading-tight tracking-tight">Grand Line</h1>
                    <p class="text-primary text-xs font-semibold uppercase tracking-widest">Admin Terminal</p>
                </div>
            </div>
            <nav class="flex-1 space-y-2">
                <NuxtLink :class="isActive('/teacher')"
                    to="/teacher">
                    <span class="material-symbols-outlined">explore</span>
                    <span class="font-medium text-sm">Dashboard</span>
                </NuxtLink>
                <NuxtLink :class="isActive('/teacher/brief', true)"
                    to="/teacher/brief">
                    <span class="material-symbols-outlined">anchor</span>
                    <span class="font-medium text-sm">Briefs</span>
                </NuxtLink>
                <NuxtLink :class="isActive('/teacher/problems', true)"
                    to="/teacher/problems">
                    <span class="material-symbols-outlined">psychology</span>
                    <span class="font-medium text-sm">Marine Challenges</span>
                </NuxtLink>
                <NuxtLink :class="isActive('/teacher/submission', true)"
                    to="/teacher/submission">
                    <span class="material-symbols-outlined">inventory_2</span>
                    <span class="font-medium text-sm">Crew Submissions</span>
                </NuxtLink>
                <form class="w-full" @submit.prevent="submit">
                    <button type="submit"
                        class="flex w-full items-center gap-3 px-4 py-3 rounded-lg text-red-500 bg-red-500/10 dark:text-red-400 hover:bg-slate-100 dark:hover:bg-[#224249] transition-colors">
                        <span class="material-symbols-outlined">logout</span>
                        <span class="font-medium text-sm">Logout</span>
                    </button>
                </form>
            </nav>
        </div>
    </aside>
</template>