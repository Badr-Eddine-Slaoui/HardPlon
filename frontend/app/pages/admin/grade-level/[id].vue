<script setup lang="ts">
    import { useGradeLevel } from '~~/stores/grade_level';

    const grades = useGradeLevel();

    const id: number = parseInt(useRoute().params?.id as string)

    onMounted(async() => {
        await grades.fetchGradeLevel(id);
        useHead({
            title: `Admin Dashboard - ${grades.grade_level?.name} Rank`
        })
    })

    const getPercentage = (students: number, capacity: number): number => {
        if (!capacity || capacity === 0) return 0
        return Math.min(parseFloat(((students / capacity) * 100).toFixed(2)), 100)
    }

    definePageMeta({
        middleware: ['auth', 'admin']
    })
</script>

<template>
    <NuxtLayout name="admin">
        <main class="flex-1 flex flex-col min-w-0 bg-background-light dark:bg-background-dark overflow-y-auto ocean-waves">
            <header
                class="h-20 flex items-center justify-between px-10 border-b border-slate-200 dark:border-[#224249] bg-white/5 backdrop-blur-md sticky top-0 z-10">
                <div class="flex items-center gap-4">
                    <NuxtLink class="flex items-center gap-2 text-slate-500 dark:text-[#90c1cb] hover:text-pirate-gold transition-colors"
                        to="/admin/grade-level">
                        <span class="material-symbols-outlined text-xl text-pirate-gold">arrow_back</span>
                        <span class="text-sm font-medium">Back to Grade Levels</span>
                    </NuxtLink>
                    <div class="h-4 w-px bg-slate-200 dark:bg-[#224249]"></div>
                    <nav class="flex items-center gap-2 text-sm">
                        <span class="text-slate-400">Grade Levels</span>
                        <span class="material-symbols-outlined text-xs text-slate-500">chevron_right</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ grades.grade_level?.name }}</span>
                        <span class="material-symbols-outlined text-xs text-slate-500">chevron_right</span>
                        <span class="text-pirate-gold font-bold">Rank Inspection</span>
                    </nav>
                </div>
                <div class="flex items-center gap-4">
                    <NuxtLink to="/admin/formation/create"
                        class="flex items-center gap-2 px-4 py-2 bg-pirate-gold rounded-lg text-background-dark font-black text-sm hover:brightness-110 transition-all shadow-lg shadow-pirate-gold/20">
                        <span class="material-symbols-outlined text-sm">military_tech</span>
                        <span>Create Formation</span>
                    </NuxtLink>
                </div>
            </header>
            <div class="p-10 space-y-8 max-w-[1400px] mx-auto w-full">
                <section class="flex flex-col md:flex-row justify-between items-end gap-6">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 text-pirate-gold">
                            <span class="material-symbols-outlined">skull</span>
                            <span class="text-xs font-bold uppercase tracking-widest">Active Rank Status: Level 1</span>
                        </div>
                        <h1 class="text-4xl font-black tracking-tight flex items-center gap-4">
                            <span class="text-slate-900 dark:text-white">{{ grades.grade_level?.name }} Rank</span>
                            <span
                                class="px-3 py-1 bg-pirate-gold/10 border border-pirate-gold text-pirate-gold text-xs font-bold rounded">TIER
                                I</span>
                        </h1>
                        <p class="text-slate-500 dark:text-[#90c1cb]">{{ grades.grade_level?.description }}</p>
                    </div>
                    <div class="flex gap-4">
                        <div
                            class="px-6 py-3 rounded-xl bg-white dark:bg-[#1a2e33] border border-slate-200 dark:border-[#224249] flex flex-col">
                            <span class="text-[10px] uppercase font-bold text-slate-400">Active Units</span>
                            <span class="font-bold text-xl">{{ grades.grade_level?.formations_count }} <span class="text-xs text-slate-500">Units</span></span>
                        </div>
                        <div
                            class="px-6 py-3 rounded-xl bg-white dark:bg-[#1a2e33] border border-slate-200 dark:border-[#224249] flex flex-col">
                            <span class="text-[10px] uppercase font-bold text-slate-400">Scholar Year</span>
                            <span class="font-bold text-xl text-primary">{{ grades.grade_level?.scholar_year }}</span>
                        </div>
                    </div>
                </section>
                <section class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div
                        class="lg:col-span-2 ship-hull-card p-8 rounded-2xl shadow-2xl relative overflow-hidden flex items-center gap-10">
                        <span
                            class="material-symbols-outlined absolute -right-4 -bottom-4 text-pirate-gold/5 text-[180px] select-none">anchor</span>
                        <div class="relative size-48 flex-shrink-0">
                            <svg class="size-full transform -rotate-90" viewBox="0 0 36 36">
                                <circle class="stroke-slate-100 dark:stroke-white/5" cx="18" cy="18"
                                    fill="none" r="16" stroke-width="3"></circle>
                                <circle class="stroke-pirate-gold" cx="18" cy="18" fill="none" r="16"
                                    :stroke-dasharray="`${getPercentage(grades.grade_level?.students_count as number, grades.grade_level?.capacity as number)}, 100`" stroke-linecap="round" stroke-width="3"></circle>
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-3xl font-black">{{ getPercentage(grades.grade_level?.students_count as number, grades.grade_level?.capacity as number ) }}%</span>
                                <span class="text-[10px] uppercase font-bold text-pirate-gold">Manifest</span>
                            </div>
                        </div>
                        <div class="space-y-6 relative z-10 flex-1">
                            <h3 class="text-xl font-bold flex items-center gap-2">
                                <span class="material-symbols-outlined text-pirate-gold">groups</span>
                                Enrollment Metrics
                            </h3>
                            <div class="grid grid-cols-2 gap-8">
                                <div>
                                    <p class="text-slate-400 text-sm font-medium">Recruits Enrolled</p>
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-3xl font-bold text-white">{{ grades.grade_level?.students_count }}</span>
                                        <span class="text-pirate-gold text-xs font-bold uppercase">Students</span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-slate-400 text-sm font-medium">Total Berths</p>
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-3xl font-bold text-slate-500">{{ grades.grade_level?.capacity }}</span>
                                        <span class="text-slate-500 text-xs font-bold uppercase">Capacity</span>
                                    </div>
                                </div>
                            </div>
                            <div class="pt-4 border-t border-white/5">
                                <p class="text-xs text-[#90c1cb] italic flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm">verified_user</span>
                                    {{ (grades.grade_level?.capacity as number) - (grades.grade_level?.students_count as number) }} slots remaining for this grade's fleet deployment.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div
                        class="bg-white dark:bg-[#1a2e33] border border-slate-200 dark:border-[#224249] p-6 rounded-2xl flex flex-col justify-between h-full relative overflow-hidden group hover:border-pirate-gold/50 transition-all">
                        <div
                            class="absolute -right-4 -top-4 text-pirate-gold/5 opacity-20 group-hover:opacity-100 transition-opacity">
                            <span class="material-symbols-outlined text-7xl">add_box</span>
                        </div>
                        <div class="space-y-4">
                            <div
                                class="size-12 rounded-xl bg-pirate-gold/10 flex items-center justify-center text-pirate-gold">
                                <span class="material-symbols-outlined">sailing</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg">Quick Deployment</h3>
                                <p class="text-sm text-slate-500 dark:text-[#90c1cb]">Add a new Formation to the {{ grades.grade_level?.name }} Fleet.</p>
                            </div>
                        </div>
                        <NuxtLink to="/admin/formation/create"
                            class="mt-6 w-full py-4 px-4 rounded-xl border-2 border-dashed border-slate-300 dark:border-[#224249] text-slate-500 dark:text-[#90c1cb] font-bold text-sm hover:border-pirate-gold hover:text-pirate-gold transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined">add</span>
                            Assemble New Formation
                        </NuxtLink>
                    </div>
                </section>
                <section class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <h2 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">Active Formations</h2>
                            <span
                                class="px-2 py-0.5 bg-pirate-gold/10 text-pirate-gold text-[10px] font-bold rounded-md border border-pirate-gold/20">INSPECTION
                                READY</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="relative group">
                                <input
                                    class="bg-white dark:bg-[#1a2e33] border border-slate-200 dark:border-[#224249] rounded-lg py-1.5 pl-8 pr-4 text-sm focus:ring-pirate-gold focus:border-pirate-gold transition-all w-64"
                                    placeholder="Search units..." type="text" />
                                <span
                                    class="material-symbols-outlined absolute left-2 top-1/2 -translate-y-1/2 text-slate-400 text-sm">search</span>
                            </div>
                            <button
                                class="p-2 rounded-lg bg-slate-100 dark:bg-[#224249] text-slate-500 hover:text-white transition-colors">
                                <span class="material-symbols-outlined">tune</span>
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <template v-if="grades.grade_level?.formations.length as number > 0">
                            <div v-for="(formation, index) in grades.grade_level?.formations"
                                class="bg-white dark:bg-[#1a2e33] border border-slate-200 dark:border-[#224249] rounded-2xl p-6 hover:shadow-xl transition-all border-b-4 border-b-primary group">
                                <div class="flex justify-between items-start mb-6">
                                    <div
                                        class="size-10 rounded-lg bg-slate-100 dark:bg-[#224249] flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors">
                                        <span class="text-lg font-bold">F{{ index as number + 1 }}</span>
                                    </div>
                                    <NuxtLink :to="`/admin/formation/${formation.id}`" class="text-slate-400 hover:text-white"><span
                                            class="material-symbols-outlined">visibility</span></NuxtLink>
                                </div>
                                <h4 class="font-bold text-lg mb-1">{{ formation.title }}</h4>
                                <p class="text-sm text-slate-500 dark:text-[#90c1cb] mb-4">{{ formation.description ?? 'No description provided.' }}</p>
                                <div
                                    class="flex items-center justify-between text-xs font-bold uppercase tracking-wider text-[#90c1cb]">
                                    <span class="flex items-center gap-1"><span
                                            class="material-symbols-outlined text-sm">groups</span> {{ formation.students_count }} Students</span>
                                    <span class="text-primary">{{ formation.class_groups_count }} Classes</span>
                                </div>
                                <div class="mt-4 h-1 w-full bg-slate-100 dark:bg-[#224249] rounded-full overflow-hidden">
                                    <div :class="`  h-full bg-primary w-[${ getPercentage(formation?.students_count as number, formation?.capacity as number)}%]`"></div>
                                </div>
                            </div>
                        </template>
                        <div v-else
                            class="col-start-2 bg-white dark:bg-[#1a2e33] border border-slate-200 dark:border-[#224249] p-6 rounded-2xl h-[30vh]">
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
                    class="bg-marine-blue border border-pirate-gold/20 rounded-2xl p-10 flex flex-col md:flex-row items-center gap-10">
                    <div
                        class="size-20 rounded-full border-4 border-pirate-gold/30 flex items-center justify-center p-2 bg-pirate-gold/5">
                        <span class="material-symbols-outlined text-pirate-gold text-4xl">sailing</span>
                    </div>
                    <div class="flex-1 space-y-2">
                        <h3 class="text-xl font-bold italic text-pirate-gold">"Inherited Will, The Destiny of the Age, and
                            The Dreams of the People."</h3>
                        <p class="text-slate-500 dark:text-[#90c1cb] max-w-2xl text-sm leading-relaxed">
                            The East Blue Rookies represent the foundation of our academy's strength. Ensure all class
                            rosters are finalized before the next Log Pose alignment.
                            Unauthorized Devil Fruit usage in the training halls remains strictly prohibited.
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <div class="size-2 rounded-full bg-pirate-gold"></div>
                        <div class="size-2 rounded-full bg-slate-700"></div>
                        <div class="size-2 rounded-full bg-slate-700"></div>
                    </div>
                </section>
            </div>
            <footer
                class="p-10 border-t border-slate-200 dark:border-[#224249] text-center text-slate-400 dark:text-[#90c1cb] text-sm mt-auto">
                © 1524 Grand Line Academy Management System • Rank Inspection Protocol • Sector: East Blue • Ver. 4.2.0
            </footer>
        </main>
    </NuxtLayout>
</template>