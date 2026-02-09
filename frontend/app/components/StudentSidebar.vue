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
        class="w-64 border-r border-slate-200 dark:border-[#224249] bg-white dark:bg-[#102023] flex flex-col shrink-0">
        <div class="p-6 flex items-center gap-3">
            <div
                class="size-10 bg-primary rounded-lg flex items-center justify-center text-background-dark shadow-[0_0_15px_rgba(13,204,242,0.4)]">
                <span class="material-symbols-outlined font-bold">explore</span>
            </div>
            <div>
                <h1 class="text-lg font-bold leading-none uppercase tracking-tighter adventure-title">
                    Logbook
                </h1>
                <p class="text-xs text-primary font-medium">Grand Line Student</p>
            </div>
        </div>
        <nav class="flex-1 px-4 space-y-2 mt-4">
            <NuxtLink :class="isActive('/student')" to="/student">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="font-medium">Command Center</span>
            </NuxtLink>
            <NuxtLink :class="isActive('/student')" to="/student">
                <span class="material-symbols-outlined fill-1">assignment</span>
                <span class="font-medium">Bounty Board</span>
            </NuxtLink>
            <NuxtLink :class="isActive('/student/submitting', true)" to="/student/submitting">
                <span class="material-symbols-outlined fill-1">inventory_2</span>
                <span class="font-medium">My Treasure</span>
            </NuxtLink>
            <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#224249] transition-colors"
                href="#">
                <span class="material-symbols-outlined">group</span>
                <span class="font-medium">My Crew</span>
            </a>
            <form class="w-full" @submit.prevent="submit">
                <button type="submit"
                    class="flex w-full items-center gap-3 px-4 py-3 rounded-lg text-red-500 bg-red-500/10 dark:text-red-400 hover:bg-slate-100 dark:hover:bg-[#224249] transition-colors">
                    <span class="material-symbols-outlined">logout</span>
                    <span class="font-medium text-sm">Logout</span>
                </button>
            </form>
        </nav>
    </aside>
</template>