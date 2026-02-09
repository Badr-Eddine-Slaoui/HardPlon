<script setup lang="ts">

    import { useGradeLevel } from '../../../../stores/grade_level';

    useHead({
        title: `Admin Dashboard - Grade Levels`
    })

    const grades = useGradeLevel();

    onMounted(async() => {
        await callOnce('fetchGradeLevels', () => grades.fetchGradeLevels());
    })

    function openArchiveModal(id: number): void {
        const modal = document.getElementById('archive-modal') as HTMLElement;
        modal.classList.remove('hidden');

        const form = modal.querySelector('form') as HTMLFormElement;
        form.onsubmit = async (e) => {
            e.preventDefault();
            await grades.archiveGradeLevel(id);
            closeArchiveModal();
        }
    }

    function closeArchiveModal(): void {
        const modal = document.getElementById('archive-modal') as HTMLElement;
        modal.classList.add('hidden');
    }

    function openResurrectModal(id: number): void {
        const modal = document.getElementById('resurrect-modal') as HTMLElement;
        modal.classList.remove('hidden');

        const form = modal.querySelector('form') as HTMLFormElement;
        form.onsubmit = async (e) => {
            e.preventDefault();
            await grades.restoreGradeLevel(id);
            closeResurrectModal();
        }
    }

    function closeResurrectModal(): void {
        const modal = document.getElementById('resurrect-modal') as HTMLElement;
        modal.classList.add('hidden');
    }
</script>

<template>
    <NuxtLayout name="admin">
        <main v-if="grades.grade_levels?.length as number > 0" class="flex-1 flex flex-col min-w-0 bg-background-light dark:bg-background-dark overflow-y-auto ocean-waves">
            <header
                class="h-20 flex items-center justify-between px-10 border-b border-slate-200 dark:border-[#224249] bg-white/5 backdrop-blur-md sticky top-0 z-10">
                <div class="flex items-center gap-4">
                    <h2 class="text-lg font-bold">Crew Ranks <span class="text-pirate-gold font-normal">/ Grade Levels
                            Management</span></h2>
                </div>
                <div class="flex items-center gap-4">
                    <NuxtLink to="/admin/grade-level/create"
                        class="flex items-center gap-2 px-6 py-2.5 bg-pirate-gold rounded-lg text-background-dark font-black text-sm hover:brightness-110 transition-all shadow-lg shadow-pirate-gold/20 uppercase tracking-tighter">
                        <span class="material-symbols-outlined font-bold">add_circle</span>
                        <span>Recruit New Grade</span>
                    </NuxtLink>
                </div>
            </header>
            <div class="p-10 space-y-8 max-w-[1400px] mx-auto w-full">
                <section class="flex flex-col md:flex-row justify-between items-end gap-6">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 text-pirate-gold">
                            <span class="material-symbols-outlined">workspace_premium</span>
                            <span class="text-xs font-bold uppercase tracking-[0.2em]">Fleet Hierarchy</span>
                        </div>
                        <h1 class="text-4xl font-black tracking-tight text-white">Fleet Grade Levels</h1>
                        <p class="text-slate-500 dark:text-[#90c1cb]">Manage the progression and enrollment of your crew
                            members across the Grand Line Academy.</p>
                    </div>
                    <div class="flex gap-4">
                        <div class="relative">
                            <span
                                class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400">search</span>
                            <input
                                class="pl-10 pr-4 py-2.5 bg-white dark:bg-[#1a2e33] border border-slate-200 dark:border-[#224249] rounded-xl text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all w-64"
                                placeholder="Search grades..." type="text" />
                        </div>
                    </div>
                </section>
                <section class="data-table-container rounded-2xl overflow-hidden shadow-2xl gold-border-glow">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-deep-blue/40 border-b border-[#224249]">
                                <th class="px-8 py-5 text-xs font-bold uppercase tracking-widest text-pirate-gold">Grade
                                    Level Name</th>
                                <th class="px-8 py-5 text-xs font-bold uppercase tracking-widest text-pirate-gold">Assigned
                                    Scholar Year</th>
                                <th
                                    class="px-8 py-5 text-xs font-bold uppercase tracking-widest text-pirate-gold text-center">
                                    Current Enrollment</th>
                                <th
                                    class="px-8 py-5 text-xs font-bold uppercase tracking-widest text-pirate-gold text-right">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#224249]">
                            <template v-for="grade_level in grades.grade_levels">
                                <tr v-if="grade_level.is_active" class="hover:bg-table-row-hover group transition-colors">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="size-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary font-bold border border-primary/20">
                                                E1</div>
                                            <div>
                                                <p
                                                    class="font-bold text-white group-hover:text-primary transition-colors">
                                                    {{ grade_level.name }}</p>
                                                <p class="text-xs text-[#90c1cb]">
                                                    {{ grade_level.description ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="material-symbols-outlined text-sm text-[#90c1cb]">calendar_today</span>
                                            <span class="text-sm font-medium">Year
                                                {{ grade_level.scholar_year }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <div v-if="grade_level.is_full"
                                            class="inline-flex items-center gap-2 px-3 py-1 bg-red-500/10 text-red-500 rounded-full text-sm font-bold">
                                            <span class="material-symbols-outlined text-sm">groups</span>
                                            {{ grade_level.students_count }} / {{ grade_level.capacity }}
                                        </div>
                                        <div v-else-if="grade_level.is_almost_full"
                                            class="inline-flex items-center gap-2 px-3 py-1 bg-amber-500/10 text-amber-500 rounded-full text-sm font-bold">
                                            <span class="material-symbols-outlined text-sm">groups</span>
                                            {{ grade_level.students_count }} / {{ grade_level.capacity }}
                                        </div>
                                        <div v-else-if="grade_level.is_nearly_empty"
                                            class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-500/10 text-emerald-500 rounded-full text-sm font-bold">
                                            <span class="material-symbols-outlined text-sm">groups</span>
                                            {{ grade_level.students_count }} / {{ grade_level.capacity }}
                                        </div>
                                        <div v-else-if="grade_level.is_empty"
                                            class="inline-flex items-center gap-2 px-3 py-1 bg-slate-500/10 text-slate-400 rounded-full text-sm font-bold">
                                            <span class="material-symbols-outlined text-sm">groups</span>
                                            {{ grade_level.students_count }} / {{ grade_level.capacity }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center justify-end gap-3">
                                            <NuxtLink :to="`/admin/grade-level/${grade_level.id}`"
                                                class="p-2 rounded-lg text-slate-400 hover:text-primary hover:bg-primary/10 transition-all"
                                                title="View Details">
                                                <span class="material-symbols-outlined">visibility</span>
                                            </NuxtLink>
                                            <NuxtLink :to="`/admin/grade-level/edit/${grade_level.id}`"
                                                class="p-2 rounded-lg text-slate-400 hover:text-pirate-gold hover:bg-pirate-gold/10 transition-all"
                                                title="Edit Rank">
                                                <span class="material-symbols-outlined">edit</span>
                                            </NuxtLink>
                                            <button @click="openArchiveModal(grade_level.id)"
                                                class="p-2 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-400/10 transition-all"
                                                title="Archive Grade">
                                                <span class="material-symbols-outlined">archive</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-else class="hover:bg-table-row-hover group transition-colors opacity-70">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="size-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary font-bold border border-primary/20">
                                                E1</div>
                                            <div>
                                                <p
                                                    class="font-bold text-white group-hover:text-primary transition-colors">
                                                    {{ grade_level.name }}</p>
                                                <p class="text-xs text-[#90c1cb]">
                                                    {{ grade_level.description ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="material-symbols-outlined text-sm text-[#90c1cb]">calendar_today</span>
                                            <span class="text-sm font-medium">Year
                                                {{ grade_level.scholar_year }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <div v-if="grade_level.is_full"
                                            class="inline-flex items-center gap-2 px-3 py-1 bg-red-500/10 text-red-500 rounded-full text-sm font-bold">
                                            <span class="material-symbols-outlined text-sm">groups</span>
                                            {{ grade_level.students_count }} / {{ grade_level.capacity }}
                                        </div>
                                        <div v-else-if="grade_level.is_almost_full"
                                            class="inline-flex items-center gap-2 px-3 py-1 bg-amber-500/10 text-amber-500 rounded-full text-sm font-bold">
                                            <span class="material-symbols-outlined text-sm">groups</span>
                                            {{ grade_level.students_count }} / {{ grade_level.capacity }}
                                        </div>
                                        <div v-else-if="grade_level.is_nearly_empty"
                                            class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-500/10 text-emerald-500 rounded-full text-sm font-bold">
                                            <span class="material-symbols-outlined text-sm">groups</span>
                                            {{ grade_level.students_count }} / {{ grade_level.capacity }}
                                        </div>
                                        <div v-else-if="grade_level.is_empty"
                                            class="inline-flex items-center gap-2 px-3 py-1 bg-slate-500/10 text-slate-400 rounded-full text-sm font-bold">
                                            <span class="material-symbols-outlined text-sm">groups</span>
                                            {{ grade_level.students_count }} / {{ grade_level.capacity }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center justify-end gap-3">
                                            <button @click="openResurrectModal(grade_level.id)" class="p-2 rounded-lg text-rose-400 bg-rose-400/10 transition-all"
                                                title="Restore Grade">
                                                <span class="material-symbols-outlined">unarchive</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </section>
                <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div
                        class="p-6 rounded-2xl bg-white/5 border border-slate-200 dark:border-[#224249] flex items-center gap-4">
                        <div class="size-12 rounded-xl bg-pirate-gold/10 flex items-center justify-center text-pirate-gold">
                            <span class="material-symbols-outlined">military_tech</span>
                        </div>
                        <div>
                            <h4 class="font-bold">Total Active Grades</h4>
                            <p class="text-2xl font-black text-white">{{ grades.grade_levels?.length ?? "N/A" }}</p>
                        </div>
                    </div>
                    <div
                        class="p-6 rounded-2xl bg-white/5 border border-slate-200 dark:border-[#224249] flex items-center gap-4">
                        <div class="size-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined">group</span>
                        </div>
                        <div>
                            <h4 class="font-bold">Total Enrollment</h4>
                            <p class="text-2xl font-black text-white">{{ grades.grade_levels?.reduce((a, b) => a + b.students_count, 0) ?? "N/A" }} Apprentices</p>
                        </div>
                    </div>
                    <div
                        class="p-6 rounded-2xl bg-white/5 border border-slate-200 dark:border-[#224249] flex items-center gap-4">
                        <div class="size-12 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                            <span class="material-symbols-outlined">archive</span>
                        </div>
                        <div>
                            <h4 class="font-bold">Archived Grades</h4>
                            <p class="text-2xl font-black text-white">{{ grades.archive_grade_levels?.length ?? "N/A" }} Past Grades</p>
                        </div>
                    </div>
                </section>
            </div>
            <footer
                class="p-10 border-t border-slate-200 dark:border-[#224249] text-center text-slate-400 dark:text-[#90c1cb] text-sm mt-auto bg-background-dark/50 backdrop-blur-sm">
                © 1524 Grand Line Academy Management System • Crew Rank Control Center • Ver. 4.2.0
            </footer>
        </main>
        <main v-else class="flex-1 flex flex-col min-w-0 bg-background-light dark:bg-background-dark overflow-y-auto ocean-waves">
            <header
                class="h-20 flex items-center justify-between px-10 border-b border-slate-200 dark:border-[#224249] bg-white/5 backdrop-blur-md sticky top-0 z-10">
                <div class="flex items-center gap-4">
                    <NuxtLink class="flex items-center gap-2 text-slate-500 dark:text-[#90c1cb] hover:text-primary transition-colors"
                        to="/admin/scholar-year">
                        <span class="material-symbols-outlined text-xl">arrow_back</span>
                        <span class="text-sm font-medium">Back to Years</span>
                    </NuxtLink>
                    <div class="h-4 w-px bg-slate-200 dark:bg-[#224249]"></div>
                    <h2 class="text-lg font-bold">Grade Levels</h2>
                </div>
            </header>
            <div class="flex-1 flex items-center justify-center p-10">
                <div class="max-w-2xl w-full text-center space-y-8">
                    <div class="relative inline-block">
                        <div
                            class="w-80 h-56 mx-auto map-texture rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-700 shadow-inner flex items-center justify-center relative overflow-hidden">
                            <div class="absolute inset-0 opacity-10 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[180px]">explore</span>
                            </div>
                            <div
                                class="z-10 flex flex-col items-center gap-4 opacity-40 group hover:opacity-60 transition-opacity">
                                <span
                                    class="material-symbols-outlined text-6xl text-slate-400 dark:text-slate-500">map</span>
                                <div class="flex gap-2">
                                    <div class="w-8 h-1 bg-slate-300 dark:bg-slate-700 rounded-full"></div>
                                    <div class="w-12 h-1 bg-slate-300 dark:bg-slate-700 rounded-full"></div>
                                    <div class="w-6 h-1 bg-slate-300 dark:bg-slate-700 rounded-full"></div>
                                </div>
                            </div>
                            <div
                                class="absolute -bottom-4 -right-4 w-24 h-24 bg-white dark:bg-[#1a2e33] border-4 border-slate-200 dark:border-[#224249] rounded-full flex items-center justify-center shadow-lg">
                                <span class="material-symbols-outlined text-4xl text-primary">explore</span>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <h3 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">No ranks established
                            yet...</h3>
                        <p class="text-lg text-slate-500 dark:text-[#90c1cb]">Expand your fleet by assigning grade levels
                            to the Grand Line Academy curriculum.</p>
                    </div>
                    <div class="flex flex-col items-center gap-4">
                        <NuxtLink to="/admin/grade-level/create"
                            class="flex items-center gap-3 px-8 py-4 bg-pirate-gold text-slate-900 font-black text-lg rounded-xl hover:scale-105 transition-all shadow-xl shadow-pirate-gold/20">
                            <span class="material-symbols-outlined">add_moderator</span>
                            <span>Recruit New Grade</span>
                        </NuxtLink>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Current Status: Fog of War
                        </p>
                    </div>
                </div>
            </div>
            <footer
                class="p-10 border-t border-slate-200 dark:border-[#224249] text-center text-slate-400 dark:text-[#90c1cb] text-sm">
                © 1524 Grand Line Academy Management System • Uncharted Waters Protocol • Ver. 4.2.0
            </footer>
        </main>
        <div id="archive-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 hidden">
            <div
                class="bg-white dark:bg-[#1a2e33] w-full max-w-md rounded-2xl border border-slate-200 dark:border-[#224249] shadow-2xl relative overflow-hidden">

                <div class="relative z-10 p-8 text-center">
                    <div
                        class="size-20 bg-pirate-red/10 text-pirate-red rounded-full flex items-center justify-center mx-auto mb-6 border-2 border-pirate-red/20">
                        <span class="material-symbols-outlined text-4xl">inventory_2</span>
                    </div>
                    <h3 class="text-2xl font-black tracking-tight mb-2">Retire this Rank?</h3>
                    <p class="text-slate-500 dark:text-[#90c1cb] mb-8 leading-relaxed">
                        Warning, Commander! Archiving this Grade Level will remove it from the active fleet. It will be sent
                        to the reserves until re-commissioned.
                    </p>
                    <div class="flex flex-col gap-3">
                        <form>
                            <button
                                class="w-full py-4 bg-pirate-red text-white font-bold rounded-xl hover:brightness-110 transition-all shadow-lg shadow-pirate-red/20 uppercase tracking-widest text-sm">
                                Send to Reserves
                            </button>
                        </form>
                        <button @click="closeArchiveModal()"
                            class="w-full py-4 bg-slate-100 dark:bg-[#224249] text-slate-600 dark:text-[#90c1cb] font-bold rounded-xl hover:bg-slate-200 dark:hover:bg-[#2c535c] transition-all uppercase tracking-widest text-sm">
                            Keep Active
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div id="resurrect-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 hidden">
            <div class="bg-[#1a2e33] border border-[#224249] w-full max-w-md rounded-2xl shadow-2xl overflow-hidden relative">
                <div class="absolute -top-10 -right-10 opacity-5 pointer-events-none">
                    <span class="material-symbols-outlined text-[160px]">explore</span>
                </div>
                <div class="p-8 relative z-10">
                    <div class="flex items-center gap-3 mb-6">
                        <div
                            class="size-12 rounded-full bg-ocean-turquoise/10 flex items-center justify-center text-ocean-turquoise">
                            <span class="material-symbols-outlined text-3xl">anchor</span>
                        </div>
                        <h3 class="text-2xl font-black tracking-tight">Reactivate this Rank?</h3>
                    </div>
                    <div class="space-y-4 mb-8">
                        <p class="text-[#90c1cb] leading-relaxed">
                            This grade level has been docked in the archives. Are you ready to hoist the sails and bring
                            this grade back into the <span class="text-white font-bold">active fleet</span>?
                        </p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <button @click="closeResurrectModal()"
                            class="px-6 py-3 rounded-xl border border-[#224249] text-slate-400 font-bold text-sm hover:bg-white/5 transition-all">
                            Stay Retired
                        </button>
                        <form>
                            <button
                                class="px-6 py-3 rounded-xl bg-ocean-turquoise text-white font-bold text-sm hover:brightness-110 transition-all shadow-lg shadow-ocean-turquoise/20 flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-lg">sailing</span>
                                Return to Sea
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </NuxtLayout>
</template>