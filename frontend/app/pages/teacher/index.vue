<script setup lang="ts">
    import { useTeacherStore } from '~~/stores/teacher';
    import { useAuthStore } from '~~/stores/auth';

    useHead({
        title: 'Teacher Dashboard'
    })

    definePageMeta({
        middleware: ['auth', 'teacher']
    })

    const store = useTeacherStore()
    const auth = useAuthStore()

    onMounted(async () => {
        await store.fetchStatistics()
    })
</script>

<template>
    <NuxtLayout name="teacher">
        <main class="flex-1 flex flex-col overflow-y-auto">
            <header
                class="h-16 border-b border-border-teal bg-ocean-deep/80 backdrop-blur-md flex items-center justify-between px-8 sticky top-0 z-10">
                <div class="flex flex-col">
                    <h2 class="text-lg font-bold tracking-tight text-white">Welcome Back, Captain {{ auth.user?.name ?? 'Teacher' }}</h2>
                    <p class="text-[10px] text-gold-accent font-bold uppercase tracking-[0.2em]">Voyage Day 142 • New
                        World Sector</p>
                </div>
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-4 text-xs font-bold text-slate-400">
                        <div class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sm text-gold-accent">air</span>
                            <span>WIND: 14 KTS</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sm text-gold-accent">water_drop</span>
                            <span>TIDE: RISING</span>
                        </div>
                    </div>
                    <div class="h-6 w-[1px] bg-border-teal"></div>
                    <button class="relative group">
                        <span
                            class="material-symbols-outlined text-slate-400 group-hover:text-gold-accent transition-colors">notifications</span>
                        <span class="absolute -top-1 -right-1 size-2 bg-red-500 rounded-full border-2 border-ocean-deep"></span>
                    </button>
                </div>
            </header>
            <div class="p-8 space-y-8 max-w-7xl mx-auto w-full">
                <!-- Statistics Section -->
                <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div
                        class="bg-card-dark border border-gold-accent/20 rounded-2xl p-6 relative overflow-hidden group gold-glow">
                        <div
                            class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity text-gold-accent">
                            <span class="material-symbols-outlined text-9xl">groups</span>
                        </div>
                        <div class="flex items-center gap-4 mb-4">
                            <div
                                class="size-10 rounded-lg bg-gold-accent/10 flex items-center justify-center text-gold-accent border border-gold-accent/20">
                                <span class="material-symbols-outlined">groups</span>
                            </div>
                            <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400">Crew Under Command
                            </h3>
                        </div>
                        <div class="flex items-end gap-2">
                            <span class="text-4xl font-black text-white">{{ store.statistics?.students_count ?? 0 }}</span>
                            <span class="text-xs text-emerald-500 font-bold mb-1.5 flex items-center gap-0.5">
                                <span class="material-symbols-outlined text-sm">trending_up</span> Active
                            </span>
                        </div>
                        <div class="mt-4 h-1 w-full bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full bg-gold-accent" :style="{ width: '100%' }"></div>
                        </div>
                    </div>
                    <div
                        class="bg-card-dark border border-gold-accent/20 rounded-2xl p-6 relative overflow-hidden group gold-glow">
                        <div
                            class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity text-gold-accent">
                            <span class="material-symbols-outlined text-9xl">bolt</span>
                        </div>
                        <div class="flex items-center gap-4 mb-4">
                            <div
                                class="size-10 rounded-lg bg-gold-accent/10 flex items-center justify-center text-gold-accent border border-gold-accent/20">
                                <span class="material-symbols-outlined">bolt</span>
                            </div>
                            <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400">Average Fleet Haki
                            </h3>
                        </div>
                        <div class="flex items-end gap-2">
                            <span class="text-4xl font-black text-white">{{ store.statistics?.average_haki ?? 0 }}%</span>
                            <span class="text-xs text-gold-accent font-bold mb-1.5 flex items-center gap-0.5">
                                <span class="material-symbols-outlined text-sm">stars</span> {{ (store.statistics?.average_haki ?? 0) > 70 ? 'Elite' : 'Rookie' }}
                            </span>
                        </div>
                        <div class="mt-4 h-1 w-full bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full bg-gold-accent" :style="{ width: (store.statistics?.average_haki ?? 0) + '%' }"></div>
                        </div>
                    </div>
                </section>

                <!-- Metrics Chart Section -->
                <section class="bg-card-dark border border-border-teal rounded-2xl overflow-hidden shadow-2xl">
                    <div class="p-6 border-b border-border-teal flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-gold-accent">explore</span>
                            <h2 class="text-lg font-bold text-white">Voyage Progress: Success Metrics</h2>
                        </div>
                    </div>
                    <div class="p-8 h-[360px] relative bg-ocean-dark/50">
                        <div class="absolute inset-x-8 inset-y-12 flex flex-col justify-between pointer-events-none">
                            <div class="border-t border-border-teal/30 w-full h-0"></div>
                            <div class="border-t border-border-teal/30 w-full h-0"></div>
                            <div class="border-t border-border-teal/30 w-full h-0"></div>
                            <div class="border-t border-border-teal/30 w-full h-0"></div>
                        </div>
                        <div class="absolute inset-0 p-8 flex items-end justify-between gap-4">
                            <div class="w-full flex items-end justify-around h-full pt-4">
                                <div v-for="metric in store.statistics?.weekly_metrics" :key="metric.label" class="w-16 flex flex-col items-center gap-3">
                                    <div
                                        class="w-12 bg-gold-accent/5 border-t-2 border-gold-accent/40 rounded-t relative group transition-all hover:bg-gold-accent/20"
                                        :class="{ 'bg-gold-accent/20 border-t-4 border-gold-accent': metric.label === 'Live' }"
                                        :style="{ height: metric.value + '%' }">
                                        <div
                                            class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gold-accent text-ocean-deep px-2 py-0.5 rounded text-[10px] font-bold opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                            {{ metric.value }}%</div>
                                    </div>
                                    <span class="text-[10px] font-bold uppercase" :class="metric.label === 'Live' ? 'text-gold-accent' : 'text-slate-500'">{{ metric.label }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="bg-ocean-dark p-4 flex justify-center gap-8 text-[10px] font-bold text-slate-500 border-t border-border-teal">
                        <div class="flex items-center gap-2">
                            <span class="size-2 rounded-full bg-gold-accent"></span>
                            <span class="uppercase">Active Fleet Participation</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="size-2 rounded-full bg-border-teal"></span>
                            <span class="uppercase">Standard Baseline</span>
                        </div>
                    </div>
                </section>

                <!-- Logs and Upcoming Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 pb-8">
                    <div class="bg-card-dark border border-border-teal rounded-2xl p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="font-bold flex items-center gap-2 text-white">
                                <span class="material-symbols-outlined text-gold-accent">history</span>
                                Recent Fleet Logs
                            </h3>
                        </div>
                        <div class="space-y-4">
                            <div v-for="log in store.statistics?.recent_logs" :key="log.id" class="flex items-center gap-4 p-3 rounded-xl bg-ocean-deep/50 border border-border-teal">
                                <div
                                    class="size-8 rounded-lg flex items-center justify-center"
                                    :class="`bg-${log.color}-500/10 text-${log.color}-500`">
                                    <span class="material-symbols-outlined text-lg">{{ log.icon }}</span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-white">{{ log.title }}</p>
                                    <p class="text-[10px] text-slate-500 uppercase">{{ log.description }}</p>
                                </div>
                                <span class="text-[10px] text-slate-500 font-medium whitespace-nowrap">{{ log.time }}</span>
                            </div>
                            <div v-if="!store.statistics?.recent_logs?.length" class="text-center py-8 text-slate-500 text-xs italic">
                                No recent logs recorded in this sector.
                            </div>
                        </div>
                    </div>
                    <div class="bg-card-dark border border-border-teal rounded-2xl p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="font-bold flex items-center gap-2 text-white">
                                <span class="material-symbols-outlined text-gold-accent">anchor</span>
                                Upcoming Landfalls
                            </h3>
                        </div>
                        <div class="space-y-4">
                            <div v-for="event in store.statistics?.upcoming_events" :key="event.id" class="flex items-center justify-between group cursor-pointer">
                                <div class="flex gap-4">
                                    <div
                                        class="flex flex-col items-center justify-center size-10 rounded-lg font-bold border"
                                        :class="event.color === 'red' ? 'bg-red-500/10 text-red-500 border-red-500/20' : 'bg-gold-accent/10 text-gold-accent border-gold-accent/20'">
                                        <span class="text-[10px] leading-none uppercase">{{ event.date.split(' ')[0] }}</span>
                                        <span class="text-sm leading-none">{{ event.date.split(' ')[1] }}</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold group-hover:text-gold-accent transition-colors text-white">
                                            {{ event.title }}</p>
                                        <p class="text-[10px] text-slate-500 uppercase tracking-wider">{{ event.type }} •
                                            {{ event.time }}</p>
                                    </div>
                                </div>
                                <span class="material-symbols-outlined text-slate-600">chevron_right</span>
                            </div>
                            <div v-if="!store.statistics?.upcoming_events?.length" class="text-center py-8 text-slate-500 text-xs italic">
                                No upcoming landfalls sighted.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <footer
                class="mt-auto border-t border-border-teal bg-ocean-dark px-8 py-3 flex items-center justify-between text-[11px] font-medium text-slate-500">
                <div class="flex gap-6">
                    <span class="flex items-center gap-1.5"><span class="size-2 bg-emerald-500 rounded-full"></span>
                        Fleet Status: Synchronized</span>
                    <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[14px]">anchor</span>
                        Anchored: Marine HQ Sector
                        7</span>
                </div>
                <div class="flex gap-4 items-center">
                    <span
                        class="px-2 py-0.5 bg-gold-accent/10 text-gold-accent rounded border border-gold-accent/20 font-bold uppercase">v2.4.0-ARABASTA</span>
                    <a class="hover:text-gold-accent transition-colors" href="#">System Protocols</a>
                    <a class="hover:text-gold-accent transition-colors" href="#">Support Signal</a>
                </div>
            </footer>
        </main>
    </NuxtLayout>
</template>