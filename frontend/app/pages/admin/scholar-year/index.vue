<script setup lang="ts">
    import { useScholarYear } from '../../../../stores/scholar_year';

    useHead({
        title: `Admin Dashboard - Scholar Years`
    })

    const years = useScholarYear();

    onMounted(async() => {
        await years.fetchScholarYears();
    })

    const getPercentage = (students: number, capacity: number): number => {
        if (!capacity || capacity === 0) return 0
        return Math.min(parseFloat(((students / capacity) * 100).toFixed(2)), 100)
    }


    function openArchiveModal(e: MouseEvent, id: number): void {
        const modal = document.getElementById('archive-modal') as HTMLElement;
        modal.classList.remove('hidden');

        const form = modal.querySelector('form') as HTMLFormElement;
        form.onsubmit = async (e) => {
            e.preventDefault();
            await years.archiveScholarYear(id);
            closeArchiveModal();
        }

        const year = modal.querySelector('.year') as HTMLElement;
        const target = e.currentTarget as HTMLElement;
        year.textContent = target?.dataset?.year as string;
    }

    function closeArchiveModal(): void {
        const modal = document.getElementById('archive-modal') as HTMLElement;
        modal.classList.add('hidden');
    }

    function openResurrectModal(e: MouseEvent, id: number): void {
        const modal = document.getElementById('resurrect-modal') as HTMLElement;
        modal.classList.remove('hidden');

        const form = modal.querySelector('form') as HTMLFormElement;
        form.onsubmit = async (e) => {
            e.preventDefault();
            await years.restoreScholarYear(id);
            closeResurrectModal();
        }

        const year = modal.querySelector('.year') as HTMLElement;
        const target = e.currentTarget as HTMLElement;
        year.textContent = target?.dataset?.year as string;
    }

    function closeResurrectModal(): void {
        const modal = document.getElementById('resurrect-modal') as HTMLElement;
        modal.classList.add('hidden');
    }

    definePageMeta({
        middleware: ['auth', 'admin']
    })
</script>

<template>
    <NuxtLayout name="admin">
        <main v-if="years.scholar_years?.length as number > 0" class="flex-1 flex flex-col min-w-0 bg-background-light dark:bg-background-dark overflow-y-auto">
            <header
                class="h-20 flex items-center justify-between px-10 border-b border-slate-200 dark:border-[#224249] bg-white/5 backdrop-blur-md sticky top-0 z-10">
                <div class="flex items-center gap-4">
                    <h2 class="text-xl font-bold tracking-tight">Voyage Log: Scholar Years</h2>
                </div>
                <div class="flex items-center gap-6">
                    <NuxtLink to="/admin/scholar-year/create"
                        class="bg-pirate-gold hover:bg-yellow-500 text-black font-bold px-6 py-2.5 rounded-lg flex items-center gap-2 transition-all shadow-[0_0_15px_rgba(255,215,0,0.3)]">
                        <span class="material-symbols-outlined text-xl">rocket_launch</span>
                        Launch New Year
                    </NuxtLink>
                </div>
            </header>
            <div class="p-10 space-y-8 max-w-[1400px] mx-auto w-full">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-[#90c1cb]">
                        <span class="material-symbols-outlined text-sm">home</span>
                        <NuxtLink to="/admin">Dashboard</NuxtLink>
                        <span class="material-symbols-outlined text-sm">chevron_right</span>
                        <span class="text-primary font-medium">Scholar Years</span>
                    </div>
                    <div class="relative w-full max-w-xs">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#90c1cb]">search</span>
                        <input
                            class="w-full bg-white dark:bg-[#1a2e33] border border-slate-200 dark:border-[#224249] rounded-lg pl-11 pr-4 py-2 text-sm focus:ring-2 focus:ring-primary transition-all placeholder:text-slate-400"
                            placeholder="Filter log entries..." type="text" />
                    </div>
                </div>
                <section class="space-y-4">
                    <div
                        class="rounded-xl overflow-hidden bg-white dark:bg-[#1a2e33] border border-slate-200 dark:border-[#224249] shadow-xl">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-slate-50 dark:bg-[#224249]/50">
                                <tr>
                                    <th
                                        class="px-6 py-5 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-[#90c1cb]">
                                        Academic Year</th>
                                    <th
                                        class="px-6 py-5 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-[#90c1cb]">
                                        Status</th>
                                    <th
                                        class="px-6 py-5 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-[#90c1cb]">
                                        Capacity</th>
                                    <th
                                        class="px-6 py-5 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-[#90c1cb]">
                                        Grade Levels</th>
                                    <th
                                        class="px-6 py-5 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-[#90c1cb] text-right">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-[#224249]">
                                <template v-for="scholar_year in years.scholar_years" :key="scholar_year.id">
                                    <tr v-if="scholar_year.is_active" class="hover:bg-slate-50 dark:hover:bg-[#224249]/30 transition-colors">
                                        <td class="px-6 py-6">
                                            <div class="flex items-center gap-3">
                                                <span
                                                    class="material-symbols-outlined text-primary">calendar_today</span>
                                                <span class="font-bold text-lg">{{ scholar_year.start_year }} -
                                                    {{ scholar_year.end_year }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-6">
                                            <span v-if="scholar_year.is_current"
                                                class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                                Active
                                            </span>
                                            <span v-else-if="scholar_year.is_upcoming"
                                                class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                                Upcoming
                                            </span>
                                            <span v-else-if="scholar_year.is_past"
                                                class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-500/10 text-slate-500 border border-slate-500/20">
                                                Past
                                            </span>
                                        </td>
                                        <td class="px-6 py-6 w-64">
                                            <div class="flex flex-col gap-1.5">
                                                <div class="flex justify-between text-xs font-medium">
                                                    <span
                                                        class="text-slate-500 dark:text-[#90c1cb]">{{ scholar_year.students_count }}/{{ scholar_year.capacity }}
                                                        Crew</span>
                                                    <span class="text-primary">{{ getPercentage(scholar_year.students_count, scholar_year.capacity) }}%</span>
                                                </div>
                                                <div
                                                    class="w-full h-2 bg-slate-100 dark:bg-[#224249] rounded-full overflow-hidden">
                                                    <div class="h-full bg-primary rounded-full shadow-[0_0_8px_#0dccf2]"
                                                        :style="`width: ${getPercentage(scholar_year.students_count, scholar_year.capacity)}%`"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-6">
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="px-2 py-0.5 rounded bg-deep-blue/20 text-primary border border-primary/30 font-bold">{{ scholar_year.grade_levels_count }}</span>
                                                <span class="text-sm text-slate-500 dark:text-[#90c1cb]">Levels</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-6 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <NuxtLink :to="`/admin/scholar-year/${scholar_year.id}`"
                                                    class="p-2 rounded-lg bg-ocean-turquoise/10 text-ocean-turquoise hover:bg-ocean-turquoise hover:text-white transition-all border border-ocean-turquoise/20"
                                                    title="View Details">
                                                    <span class="material-symbols-outlined text-lg">visibility</span>
                                                </NuxtLink>
                                                <NuxtLink :to="`/admin/scholar-year/edit/${scholar_year.id}`"
                                                    class="p-2 rounded-lg bg-deep-blue/40 text-primary hover:bg-primary hover:text-white transition-all border border-primary/20"
                                                    title="Edit Year">
                                                    <span class="material-symbols-outlined text-lg">edit</span>
                                                </NuxtLink>
                                                <button :data-year="`${scholar_year.start_year}-${scholar_year.end_year}`" @click="openArchiveModal($event, scholar_year.id)"
                                                    class="p-2 rounded-lg bg-slate-100 dark:bg-[#224249] text-slate-600 dark:text-[#90c1cb] hover:bg-red-500 hover:text-white transition-all border border-slate-200 dark:border-[#224249]"
                                                    title="Archive Year">
                                                    <span class="material-symbols-outlined text-lg">archive</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-else class="hover:bg-slate-50 dark:hover:bg-[#224249]/30 transition-colors opacity-70">
                                        <td class="px-6 py-6">
                                            <div class="flex items-center gap-3 text-slate-500">
                                                <span class="material-symbols-outlined">inventory_2</span>
                                                <span class="font-bold text-lg">{{ scholar_year.start_year }} -
                                                    {{ scholar_year.end_year }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-6">
                                            <span
                                                class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-500/10 text-slate-500 border border-slate-500/20">
                                                Archived
                                            </span>
                                        </td>
                                        <td class="px-6 py-6 w-64">
                                            <div class="flex flex-col gap-1.5">
                                                <div class="flex justify-between text-xs font-medium">
                                                    <span
                                                        class="text-slate-500 dark:text-[#90c1cb]">{{ scholar_year.students_count }}/{{ scholar_year.capacity }}
                                                        Crew</span>
                                                    <span class="text-slate-500">{{ getPercentage(scholar_year.students_count, scholar_year.capacity) }}%</span>
                                                </div>
                                                <div
                                                    class="w-full h-2 bg-slate-100 dark:bg-[#224249] rounded-full overflow-hidden">
                                                    <div class="h-full bg-slate-500 rounded-full"
                                                        :style="`width: ${getPercentage(scholar_year.students_count, scholar_year.capacity)}%`"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-6">
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="px-2 py-0.5 rounded bg-slate-100 dark:bg-[#224249] text-slate-500 border border-slate-300 dark:border-[#2c535c] font-bold">{{ scholar_year.grade_levels_count }}</span>
                                                <span class="text-sm text-slate-500 dark:text-[#90c1cb]">Levels</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-6 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <button :data-year="`${scholar_year.start_year}-${scholar_year.end_year}`" @click="openResurrectModal($event, scholar_year.id)"
                                                    class="p-2 rounded-lg bg-pirate-gold/10 text-pirate-gold hover:bg-pirate-gold hover:text-black transition-all border border-pirate-gold/20"
                                                    title="Unarchive Year">
                                                    <span class="material-symbols-outlined text-lg">unarchive</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </section>
                <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="p-6 rounded-xl bg-[#1a2e33] border border-[#224249] flex items-center gap-4">
                        <div class="size-12 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-2xl">sailing</span>
                        </div>
                        <div>
                            <p class="text-xs text-[#90c1cb] uppercase font-bold tracking-widest">Active Voyage</p>
                                <p v-if="years.current_year" class="font-bold text-lg">{{ years.current_year.start_year }} - {{ years.current_year.end_year }} Session</p>
                                <p v-else class="font-bold text-lg">No Active Voyage</p>
                        </div>
                    </div>
                    <div class="p-6 rounded-xl bg-[#1a2e33] border border-[#224249] flex items-center gap-4">
                        <div
                            class="size-12 rounded-full bg-pirate-gold/10 flex items-center justify-center text-pirate-gold">
                            <span class="material-symbols-outlined text-2xl">group</span>
                        </div>
                        <div>
                            <p class="text-xs text-[#90c1cb] uppercase font-bold tracking-widest">Total Enrollment</p>
                            <p class="font-bold text-lg">{{ years.scholar_years?.reduce((total, year) => total + year.students_count, 0) }} Apprentices</p>
                        </div>
                    </div>
                    <div class="p-6 rounded-xl bg-[#1a2e33] border border-[#224249] flex items-center gap-4">
                        <div
                            class="size-12 rounded-full bg-ocean-turquoise/10 flex items-center justify-center text-ocean-turquoise">
                            <span class="material-symbols-outlined text-2xl">route</span>
                        </div>
                        <div>
                            <p class="text-xs text-[#90c1cb] uppercase font-bold tracking-widest">Archived Voyages</p>
                            <p class="font-bold text-lg">{{ years.archived_years?.length }} Past Years</p>
                        </div>
                    </div>
                </section>
            </div>
            <footer
                class="p-10 border-t border-slate-200 dark:border-[#224249] text-center text-slate-400 dark:text-[#90c1cb] text-sm mt-auto">
                © 1524 Grand Line Academy Management System • Version 4.2.0 "New World"
            </footer>
        </main>
        <main v-else class="flex-1 flex flex-col min-w-0 bg-background-light dark:bg-background-dark overflow-y-auto">
            <header
                class="h-20 flex items-center justify-between px-10 border-b border-slate-200 dark:border-[#224249] bg-white/5 backdrop-blur-md sticky top-0 z-10">
                <div class="flex items-center gap-4">
                    <h2 class="text-xl font-bold tracking-tight">Voyage Log: Scholar Years</h2>
                </div>
            </header>
            <div class="flex-1 flex items-center justify-center p-10">
                <div class="max-w-2xl w-full text-center space-y-8">
                    <div class="relative flex justify-center">
                        <div class="relative group">
                            <div class="absolute inset-0 bg-pirate-gold/10 blur-3xl rounded-full scale-150 -z-10"></div>
                            <div
                                class="w-64 h-64 bg-slate-100/50 dark:bg-[#1a2e33] rounded-3xl border-2 border-dashed border-[#224249] flex flex-col items-center justify-center p-8 treasure-glow">
                                <div class="relative mb-4">
                                    <span
                                        class="material-symbols-outlined text-8xl text-[#224249] dark:text-[#90c1cb]/20">inventory_2</span>
                                    <span
                                        class="material-symbols-outlined absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-4xl text-slate-400 dark:text-[#90c1cb]/40">skull</span>
                                </div>
                                <div class="flex gap-2">
                                    <span class="material-symbols-outlined text-pirate-gold text-2xl">star</span>
                                    <span class="material-symbols-outlined text-pirate-gold text-2xl">star</span>
                                    <span class="material-symbols-outlined text-pirate-gold text-2xl">star</span>
                                </div>
                            </div>
                            <div
                                class="absolute -top-4 -right-4 bg-[#101f22] border-2 border-[#224249] p-3 rounded-xl shadow-2xl rotate-12">
                                <span class="material-symbols-outlined text-white text-3xl">flag</span>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <h3 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">
                            No treasure here yet...
                        </h3>
                        <p class="text-lg text-slate-500 dark:text-[#90c1cb] max-w-md mx-auto leading-relaxed">
                            The charts are empty and the compass is still. Start your journey by launching the first scholar
                            year of this epoch.
                        </p>
                    </div>
                    <div class="flex justify-center pt-4">
                        <NuxtLink to="/admin/scholar-year/create"
                            class="bg-pirate-gold hover:bg-yellow-500 text-black font-extrabold px-10 py-4 rounded-xl flex items-center gap-3 transition-all shadow-[0_10px_30px_rgba(255,215,0,0.3)] hover:-translate-y-1 uppercase tracking-wider text-sm">
                            <span class="material-symbols-outlined text-2xl">rocket_launch</span>
                            Launch New Year
                        </NuxtLink>
                    </div>
                    <div class="flex items-center justify-center gap-4 pt-12">
                        <div class="h-px w-12 bg-slate-200 dark:bg-[#224249]"></div>
                        <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-[#90c1cb]">
                            <span class="material-symbols-outlined text-sm">explore</span>
                            Set your course
                        </div>
                        <div class="h-px w-12 bg-slate-200 dark:bg-[#224249]"></div>
                    </div>
                </div>
            </div>
            <footer
                class="p-10 border-t border-slate-200 dark:border-[#224249] text-center text-slate-400 dark:text-[#90c1cb] text-sm mt-auto">
                © 1524 Grand Line Academy Management System • Version 4.2.0 "New World"
            </footer>
        </main>
        <div id="archive-modal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 hidden">
            <div
                class="relative w-full max-w-lg bg-academy-panel border-2 border-[#224249] rounded-2xl shadow-2xl overflow-hidden wave-pattern">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-primary/30 to-transparent">
                </div>
                <div class="p-8 space-y-6">
                    <div class="text-center space-y-3">
                        <div
                            class="inline-flex items-center justify-center size-16 rounded-full bg-locker-red/10 text-locker-red mb-2 border border-locker-red/20 shadow-[0_0_20px_rgba(139,0,0,0.2)]">
                            <span class="material-symbols-outlined text-4xl">anchor</span>
                        </div>
                        <h3 class="text-2xl font-bold text-pirate-gold tracking-wide uppercase italic">Archive this Voyage?
                        </h3>
                        <div class="h-px w-24 bg-gradient-to-r from-transparent via-pirate-gold/50 to-transparent mx-auto">
                        </div>
                    </div>
                    <div class="text-center space-y-4">
                        <p class="text-[#90c1cb] text-lg leading-relaxed">
                            Beware, Admiral! Sending the <span class="text-white font-bold"><span class="year"></span> Voyage</span> to
                            <span class="text-locker-red font-bold">Davy Jones' Locker</span> will move all logs to the
                            historical archives.
                        </p>
                        <p class="text-xs text-slate-500 uppercase tracking-widest font-semibold">
                            Current progress will be locked for the crew.
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 pt-4">
                        <button @click="closeArchiveModal()"
                            class="flex-1 px-6 py-3.5 rounded-xl font-bold text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-white/10 hover:bg-slate-200 dark:hover:bg-white/20 transition-all border border-slate-300 dark:border-white/10">
                            Keep Sailing
                        </button>
                        <form>
                            <button
                                class="flex-1 px-6 py-3.5 rounded-xl font-bold text-white bg-locker-red hover:bg-red-700 shadow-[0_4px_15px_rgba(139,0,0,0.4)] transition-all flex items-center justify-center gap-2 group">
                                <span
                                    class="material-symbols-outlined text-xl group-hover:rotate-12 transition-transform">inventory_2</span>
                                Move to Locker
                            </button>
                        </form>
                    </div>
                </div>
                <div class="absolute bottom-4 right-4 opacity-5 pointer-events-none">
                    <span class="material-symbols-outlined text-8xl">skull</span>
                </div>
            </div>
        </div>

        <div id="resurrect-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 hidden">
            <div
                class="bg-[#1a2e33] w-full max-w-md rounded-2xl border border-[#224249] shadow-[0_20px_50px_rgba(0,0,0,0.5)] overflow-hidden relative">
                <div class="absolute -top-4 -right-4 opacity-5 pointer-events-none">
                    <span class="material-symbols-outlined text-[120px] text-primary">explore</span>
                </div>
                <div class="p-8 space-y-6 relative">
                    <div class="flex flex-col items-center text-center space-y-4">
                        <div
                            class="size-16 rounded-full bg-primary/10 flex items-center justify-center text-primary ring-4 ring-primary/5">
                            <span class="material-symbols-outlined text-4xl">anchor</span>
                        </div>
                        <div class="space-y-2">
                            <h3 class="text-2xl font-bold tracking-tight text-white">Resurrect this Voyage?</h3>
                            <p class="text-[#90c1cb] leading-relaxed">
                                Are you sure you want to bring the <span class="text-primary font-bold"><span class="year"></span> Scholar
                                    Year</span> back to active status? This will make it visible to all crew members in the
                                Grand Line.
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-col gap-3">
                        <form>
                            <button type="submit"
                                class="w-full bg-ocean-turquoise hover:bg-[#1a9d96] text-white font-bold py-3.5 rounded-xl transition-all shadow-lg shadow-ocean-turquoise/20 flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined">sailing</span>
                                Set Sail Again
                            </button>
                        </form>
                        <button @click="closeResurrectModal()"
                            class="w-full bg-transparent hover:bg-slate-800 text-[#90c1cb] font-semibold py-3 rounded-xl transition-all border border-[#224249]">
                            Stay Hidden
                        </button>
                    </div>
                </div>
                <div class="h-1 w-full bg-[#224249]">
                    <div class="h-full bg-ocean-turquoise w-1/3 shadow-[0_0_10px_#20B2AA]"></div>
                </div>
            </div>
        </div>
    </NuxtLayout>
</template>