<script setup lang="ts">
import { useScholarYear } from '~~/stores/scholar_year';

    const years = useScholarYear();

    const id: number = parseInt(useRoute().params?.id as string)

    onMounted(async() => {
        await years.fetchScholarYear(id);
        useHead({
            title: `Admin Dashboard - ${years.scholar_year?.start_year} - ${years.scholar_year?.end_year} Scholar Years`
        })
    })

    const getPercentage = (students: number, capacity: number): number => {
        if (!capacity || capacity === 0) return 0
        return Math.min(parseFloat(((students / capacity) * 100).toFixed(2)), 100)
    }
</script>

<template>
    <NuxtLayout name="admin">
        <main class="flex-1 flex flex-col min-w-0 bg-background-light dark:bg-background-dark overflow-y-auto ocean-waves">
            <header
                class="h-20 flex items-center justify-between px-10 border-b border-slate-200 dark:border-[#224249] bg-white/5 backdrop-blur-md sticky top-0 z-10">
                <div class="flex items-center gap-4">
                    <NuxtLink class="flex items-center gap-2 text-slate-500 dark:text-[#90c1cb] hover:text-primary transition-colors"
                        to="/admin/scholar-year">
                        <span class="material-symbols-outlined text-xl">arrow_back</span>
                        <span class="text-sm font-medium">Back to Years</span>
                    </NuxtLink>
                    <div class="h-4 w-px bg-slate-200 dark:bg-[#224249]"></div>
                    <h2 class="text-lg font-bold">Scholar Year {{ years.scholar_year?.start_year }}-{{ years.scholar_year?.end_year }}</h2>
                </div>
                <div class="flex items-center gap-4">
                    <NuxtLink to="/admin/grade-level/create"
                        class="flex items-center gap-2 px-4 py-2 bg-primary rounded-lg text-white font-bold text-sm hover:brightness-110 transition-all shadow-lg shadow-primary/20">
                        <span class="material-symbols-outlined text-sm">add</span>
                        <span>Deploy New Grade</span>
                    </NuxtLink>
                </div>
            </header>
            <div class="p-10 space-y-8 max-w-[1400px] mx-auto w-full">
                <section class="flex flex-col md:flex-row justify-between items-end gap-6">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 text-primary">
                            <span class="material-symbols-outlined">calendar_today</span>
                            <span class="text-xs font-bold uppercase tracking-widest">Active Academic Term</span>
                        </div>
                        <h1 class="text-4xl font-black tracking-tight">Year {{ years.scholar_year?.start_year }}-{{ years.scholar_year?.end_year }}</h1>
                        <p class="text-slate-500 dark:text-[#90c1cb]">Detailed fleet inspection and enrollment capacity for
                            the current scholar cycle.</p>
                    </div>
                    <div class="flex gap-4">
                        <div
                            class="px-6 py-3 rounded-xl bg-white dark:bg-[#1a2e33] border border-slate-200 dark:border-[#224249] flex flex-col">
                            <span class="text-[10px] uppercase font-bold text-slate-400">Launch Date</span>
                            <span class="font-bold">{{ (new Date(years.scholar_year?.created_at as string)).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) }}</span>
                        </div>
                        <div
                            class="px-6 py-3 rounded-xl bg-white dark:bg-[#1a2e33] border border-slate-200 dark:border-[#224249] flex flex-col">
                            <span class="text-[10px] uppercase font-bold text-slate-400">Voyage Status</span>
                            <span v-if="years.scholar_year?.is_current" class="font-bold text-emerald-500 flex items-center gap-1">
                                <span class="size-2 bg-emerald-500 rounded-full"></span>
                                On Course
                            </span>
                            <span v-else-if="years.scholar_year?.is_upcoming" class="font-bold text-blue-500 flex items-center gap-1">
                                <span class="size-2 bg-blue-500 rounded-full"></span>
                                Upcoming
                            </span>
                            <span v-else-if="years.scholar_year?.is_past" class="font-bold text-rose-500 flex items-center gap-1">
                                <span class="size-2 bg-rose-500 rounded-full"></span>
                                Past
                            </span>
                            <span v-else class="font-bold text-slate-400 flex items-center gap-1">
                                <span class="size-2 bg-slate-400 rounded-full"></span>
                                Archived
                            </span>
                        </div>
                    </div>
                </section>
                <section class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div
                        class="lg:col-span-2 ship-hull-card p-8 rounded-2xl shadow-2xl relative overflow-hidden flex items-center gap-10">
                        <span
                            class="material-symbols-outlined absolute -right-4 -bottom-4 text-white/5 text-[180px] select-none">explore</span>
                        <div class="relative size-48 flex-shrink-0">
                            <svg class="size-full transform -rotate-90" viewBox="0 0 36 36">
                                <circle class="stroke-slate-100 dark:stroke-white/5" cx="18" cy="18"
                                    fill="none" r="16" stroke-width="3.5"></circle>
                                <circle class="stroke-primary" cx="18" cy="18" fill="none" r="16"
                                    :stroke-dasharray="`${getPercentage(years.scholar_year?.students_count as number, years.scholar_year?.capacity as number)}, 100`" stroke-linecap="round" stroke-width="3.5"></circle>
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-3xl font-black">{{ getPercentage(years.scholar_year?.students_count as number, years.scholar_year?.capacity as number )}}%</span>
                                <span class="text-[10px] uppercase font-bold text-[#90c1cb]">Capacity</span>
                            </div>
                        </div>
                        <div class="space-y-6 relative z-10 flex-1">
                            <h3 class="text-xl font-bold">Crew Manifest</h3>
                            <div class="grid grid-cols-2 gap-8">
                                <div>
                                    <p class="text-slate-400 text-sm font-medium">Total Enrolled</p>
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-3xl font-bold">{{ years.scholar_year?.students_count }}</span>
                                        <span class="text-primary text-xs font-bold flex items-center">Students</span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-slate-400 text-sm font-medium">Fleet Capacity</p>
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-3xl font-bold text-slate-500">{{ years.scholar_year?.capacity }}</span>
                                        <span class="text-slate-500 text-xs font-bold">Max Berths</span>
                                    </div>
                                </div>
                            </div>
                            <div class="pt-4 border-t border-white/5">
                                <p class="text-xs text-[#90c1cb] italic flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm">info</span>
                                    Remaining: {{ (years.scholar_year?.capacity as number) - (years.scholar_year?.students_count as number) }} available spots for late-voyage recruits.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div
                            class="bg-white dark:bg-[#1a2e33] border border-slate-200 dark:border-[#224249] p-6 rounded-2xl flex flex-col justify-between h-full">
                            <div class="space-y-4">
                                <div
                                    class="size-12 rounded-xl bg-pirate-gold/10 flex items-center justify-center text-pirate-gold">
                                    <span class="material-symbols-outlined">add_task</span>
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg">Quick Deployment</h3>
                                    <p class="text-sm text-slate-500 dark:text-[#90c1cb]">Assign a new Grade Level to this
                                        scholar year to expand your fleet.</p>
                                </div>
                            </div>
                            <NuxtLink to="/admin/grade-level/create"
                                class="mt-6 w-full py-3 px-4 rounded-xl border-2 border-dashed border-slate-300 dark:border-[#224249] text-slate-500 dark:text-[#90c1cb] font-bold text-sm hover:border-primary hover:text-primary transition-all flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined">layers</span>
                                New Grade Level
                            </NuxtLink>
                        </div>
                    </div>
                </section>
                <section class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <h2 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">Active Grade
                                Levels</h2>
                            <span class="px-2 py-0.5 bg-primary/10 text-primary text-[10px] font-bold rounded-md">{{ years.scholar_year?.grade_levels_count }}
                                TOTAL</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button class="p-2 rounded-lg bg-slate-100 dark:bg-[#224249] text-slate-500">
                                <span class="material-symbols-outlined">filter_list</span>
                            </button>
                            <button class="p-2 rounded-lg bg-slate-100 dark:bg-[#224249] text-slate-500">
                                <span class="material-symbols-outlined">grid_view</span>
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <template v-if="years.scholar_year?.grade_levels_count as number > 0" >
                            <div v-for="(grade_level, index) in years.scholar_year?.grade_levels" class="bg-white dark:bg-[#1a2e33] border border-slate-200 dark:border-[#224249] rounded-2xl p-6 hover:shadow-xl transition-all border-b-4 border-b-primary group">
                                <div class="flex justify-between items-start mb-6">
                                    <div
                                        class="size-10 rounded-lg bg-slate-100 dark:bg-[#224249] flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors">
                                        <span class="text-lg font-bold">G{{ index as number + 1 }}</span>
                                    </div>
                                    <NuxtLink :to="`/admin/grade-level/${grade_level.id}`" class="text-slate-400 hover:text-white"><span
                                            class="material-symbols-outlined">visibility</span></NuxtLink>
                                </div>
                                <h4 class="font-bold text-lg mb-1">{{ grade_level.name }}</h4>
                                <p class="text-sm text-slate-500 dark:text-[#90c1cb] mb-4">{{ grade_level.description ?? 'No description provided.' }}</p>
                                <div
                                    class="flex items-center justify-between text-xs font-bold uppercase tracking-wider text-[#90c1cb]">
                                    <span class="flex items-center gap-1"><span
                                            class="material-symbols-outlined text-sm">groups</span> {{ grade_level.students_count }} Students</span>
                                    <span class="text-primary">{{ grade_level.formations_count }} Formations</span>
                                </div>
                                <div class="mt-4 h-1 w-full bg-slate-100 dark:bg-[#224249] rounded-full overflow-hidden">
                                    <div :class="`h-full bg-primary w-[${getPercentage(grade_level?.students_count as number, grade_level?.capacity as number)}%]`"></div>
                                </div>
                            </div>
                        </template>
                        <div v-else class="col-start-2 bg-white dark:bg-[#1a2e33] border border-slate-200 dark:border-[#224249] p-6 rounded-2xl h-[30vh]">
                            <div class="space-y-4 flex flex-col justify-center items-center gap-y-5 h-full">
                                <div
                                    class="size-12 mx-auto rounded-xl bg-red-500/10 flex items-center justify-center text-red-500">
                                    <span class="material-symbols-outlined">info</span>
                                </div>
                                <div class="text-center">
                                    <h3 class="font-bold text-lg">No Grade Levels Found</h3>
                                    <p class="text-sm text-slate-500 dark:text-[#90c1cb]">Assign a new Grade Level to
                                        this scholar year to expand your fleet.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section
                    class="bg-white/5 border border-slate-200 dark:border-[#224249] rounded-2xl p-10 flex flex-col md:flex-row items-center gap-10">
                    <div class="size-20 rounded-full border-4 border-primary/20 flex items-center justify-center p-2">
                        <span class="material-symbols-outlined text-primary text-4xl">sailing</span>
                    </div>
                    <div class="flex-1 space-y-2">
                        <h3 class="text-xl font-bold italic text-primary">"The journey of a thousand leagues begins with a
                            single grade."</h3>
                        <p class="text-slate-500 dark:text-[#90c1cb] max-w-2xl">This academic year is currently entering
                            the mid-voyage assessment phase. Ensure all grade levels have submitted their crew performance
                            logs by next week.</p>
                    </div>
                    <div class="flex gap-2">
                        <div class="size-2 rounded-full bg-primary"></div>
                        <div class="size-2 rounded-full bg-slate-700"></div>
                        <div class="size-2 rounded-full bg-slate-700"></div>
                    </div>
                </section>
            </div>
            <footer
                class="p-10 border-t border-slate-200 dark:border-[#224249] text-center text-slate-400 dark:text-[#90c1cb] text-sm mt-auto">
                © 1524 Grand Line Academy Management System • Year Fleet Control Center • Ver. 4.2.0
            </footer>
        </main>
    </NuxtLayout>
</template>