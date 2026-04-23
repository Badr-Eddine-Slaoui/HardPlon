<script setup lang="ts">
    import { useFormation } from '../../../../stores/formation';

    useHead({
        title: `Admin Dashboard - Formations`
    })

    const store = useFormation();
    const paginate = usePagination(store.fetchFormations.bind(store));

    onMounted(async() => {
        await paginate.fetchData();
    })

    function openArchiveModal(e: MouseEvent, id: number): void {
        const modal = document.getElementById('archive-modal') as HTMLElement;
        modal.classList.remove('hidden');

        const form = modal.querySelector('form') as HTMLFormElement;
        form.onsubmit = async (e) => {
            e.preventDefault();
            await store.archiveFormation(id);
            closeArchiveModal();
        }

        const title = modal.querySelector('.title') as HTMLElement;
        const target = e.currentTarget as HTMLElement;
        title.textContent = target?.dataset?.title as string;
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
            await store.restoreFormation(id);
            closeResurrectModal();
        }

        const title = modal.querySelector('.title') as HTMLElement;
        const target = e.currentTarget as HTMLElement;
        title.textContent = target?.dataset?.title as string;
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
        <main v-if="store.formations?.length as number > 0" class="flex-1 flex flex-col min-w-0 bg-background-light dark:bg-background-dark overflow-y-auto ocean-waves">
            <header
                class="h-20 flex items-center justify-between px-10 border-b border-slate-200 dark:border-[#224249] bg-white/5 backdrop-blur-md sticky top-0 z-10">
                <div class="flex items-center gap-4">
                    <h2 class="text-lg font-bold">Battle Strategies <span class="text-pirate-gold font-normal">/ Formations
                            Management</span></h2>
                </div>
                <div class="flex items-center gap-4">
                    <NuxtLink to="/admin/formation/create"
                        class="flex items-center gap-2 px-6 py-2.5 bg-pirate-gold rounded-lg text-background-dark font-black text-sm hover:brightness-110 transition-all shadow-lg shadow-pirate-gold/20 uppercase tracking-tighter">
                        <span class="material-symbols-outlined font-bold">add_circle</span>
                        <span>Draft New Formation</span>
                    </NuxtLink>
                </div>
            </header>
            <div class="p-10 space-y-8 max-w-[1400px] mx-auto w-full">
                <section class="flex flex-col md:flex-row justify-between items-end gap-6">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 text-pirate-gold">
                            <span class="material-symbols-outlined">history_edu</span>
                            <span class="text-xs font-bold uppercase tracking-[0.2em]">Combat Curriculum</span>
                        </div>
                        <h1 class="text-4xl font-black tracking-tight text-white">Battle Formations</h1>
                        <p class="text-slate-500 dark:text-[#90c1cb]">Establish and refine educational programs to prepare
                            your crew for the New World.</p>
                    </div>
                    <div class="flex gap-4">
                        <div class="relative">
                            <span
                                class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400">search</span>
                            <input
                                class="pl-10 pr-4 py-2.5 bg-white dark:bg-[#1a2e33] border border-slate-200 dark:border-[#224249] rounded-xl text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all w-64"
                                placeholder="Search formations..." type="text" />
                        </div>
                    </div>
                </section>
                <section class="data-table-container rounded-2xl overflow-hidden shadow-2xl gold-border-glow">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-deep-blue/40 border-b border-[#224249]">
                                <th class="px-8 py-5 text-xs font-bold uppercase tracking-widest text-pirate-gold">Formation
                                    Name</th>
                                <th class="px-8 py-5 text-xs font-bold uppercase tracking-widest text-pirate-gold">Assigned
                                    Rank</th>
                                <th class="px-8 py-5 text-xs font-bold uppercase tracking-widest text-pirate-gold">Duration
                                </th>
                                <th class="px-8 py-5 text-xs font-bold uppercase tracking-widest text-pirate-gold">Capacity
                                </th>
                                <th
                                    class="px-8 py-5 text-xs font-bold uppercase tracking-widest text-pirate-gold text-right">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#224249]">
                            <template v-for="formation in store.formations">
                                <tr v-if="formation.is_active" class="hover:bg-table-row-hover group transition-colors">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="size-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary font-bold border border-primary/20">
                                                <span class="material-symbols-outlined">map</span>
                                            </div>
                                            <div>
                                                <p
                                                    class="font-bold text-white group-hover:text-primary transition-colors">
                                                    {{ formation?.title }}</p>
                                                <p class="text-xs text-[#90c1cb] w-[250px] truncate">{{ formation?.description ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="px-3 py-1 bg-deep-blue/40 border border-pirate-gold/30 rounded-full text-xs font-bold text-pirate-gold">{{ formation?.grade_name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-2 text-sm text-white">
                                            <span class="material-symbols-outlined text-sm text-primary">timer</span>
                                            {{ formation?.duration }} Months
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <div v-if="formation?.is_full"
                                            class="inline-flex items-center gap-2 px-3 py-1 bg-red-500/10 text-red-500 rounded-full text-sm font-bold">
                                            <span class="material-symbols-outlined text-sm">groups</span>
                                            {{ formation?.students_count }} / {{ formation?.capacity }}
                                        </div>
                                        <div v-else-if="formation?.is_almost_full"
                                            class="inline-flex items-center gap-2 px-3 py-1 bg-amber-500/10 text-amber-500 rounded-full text-sm font-bold">
                                            <span class="material-symbols-outlined text-sm">groups</span>
                                            {{ formation?.students_count }} / {{ formation?.capacity }}
                                        </div>
                                        <div v-else-if="formation?.is_nearly_empty"
                                            class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-500/10 text-emerald-500 rounded-full text-sm font-bold">
                                            <span class="material-symbols-outlined text-sm">groups</span>
                                            {{ formation?.students_count }} / {{ formation?.capacity }}
                                        </div>
                                        <div v-else-if="formation?.is_empty"
                                            class="inline-flex items-center gap-2 px-3 py-1 bg-slate-500/10 text-slate-400 rounded-full text-sm font-bold">
                                            <span class="material-symbols-outlined text-sm">groups</span>
                                            {{ formation?.students_count }} / {{ formation?.capacity }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center justify-end gap-3">
                                            <NuxtLink :to="`/admin/formation/${formation?.id}`"
                                                class="p-2 rounded-lg text-slate-400 hover:text-primary hover:bg-primary/10 transition-all"
                                                title="View Formation">
                                                <span class="material-symbols-outlined">visibility</span>
                                            </NuxtLink>
                                            <NuxtLink :to="`/admin/formation/edit/${formation?.id}`"
                                                class="p-2 rounded-lg text-slate-400 hover:text-pirate-gold hover:bg-pirate-gold/10 transition-all"
                                                title="Update Strategy">
                                                <span class="material-symbols-outlined">edit</span>
                                            </NuxtLink>
                                            <button :data-title="formation?.title" @click="openArchiveModal($event, formation?.id as number)"
                                                class="p-2 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-400/10 transition-all"
                                                title="Archive Formation">
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
                                                <span class="material-symbols-outlined">map</span>
                                            </div>
                                            <div>
                                                <p
                                                    class="font-bold text-white group-hover:text-primary transition-colors">
                                                    {{ formation?.title }}</p>
                                                <p class="text-xs text-[#90c1cb] w-[250px] truncate">{{ formation?.description ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="px-3 py-1 bg-deep-blue/40 border border-pirate-gold/30 rounded-full text-xs font-bold text-pirate-gold">{{ formation?.grade_name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-2 text-sm text-white">
                                            <span class="material-symbols-outlined text-sm text-primary">timer</span>
                                            {{ formation?.duration }} Months
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <div v-if="formation?.is_full"
                                            class="inline-flex items-center gap-2 px-3 py-1 bg-red-500/10 text-red-500 rounded-full text-sm font-bold">
                                            <span class="material-symbols-outlined text-sm">groups</span>
                                            {{ formation?.students_count }} / {{ formation?.capacity }}
                                        </div>
                                        <div v-else-if="formation?.is_almost_full"
                                            class="inline-flex items-center gap-2 px-3 py-1 bg-amber-500/10 text-amber-500 rounded-full text-sm font-bold">
                                            <span class="material-symbols-outlined text-sm">groups</span>
                                            {{ formation?.students_count }} / {{ formation?.capacity }}
                                        </div>
                                        <div v-else-if="formation?.is_nearly_empty"
                                            class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-500/10 text-emerald-500 rounded-full text-sm font-bold">
                                            <span class="material-symbols-outlined text-sm">groups</span>
                                            {{ formation?.students_count }} / {{ formation?.capacity }}
                                        </div>
                                        <div v-else-if="formation?.is_empty"
                                            class="inline-flex items-center gap-2 px-3 py-1 bg-slate-500/10 text-slate-400 rounded-full text-sm font-bold">
                                            <span class="material-symbols-outlined text-sm">groups</span>
                                            {{ formation?.students_count }} / {{ formation?.capacity }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center justify-end gap-3">
                                            <button :data-title="formation?.title" @click="openResurrectModal($event, formation?.id as number)" class="p-2 rounded-lg text-rose-400 bg-rose-400/10 transition-all"
                                                title="Restore Formation">
                                                <span class="material-symbols-outlined">unarchive</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </section>
                <!-- Pagination Controls -->
                <Pagination :meta="store.meta" :per_page="paginate.perPage.value" @change="paginate.fetchData"
                    @perPage="paginate.changePerPage" />
                <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div
                        class="p-6 rounded-2xl bg-white/5 border border-slate-200 dark:border-[#224249] flex items-center gap-4">
                        <div class="size-12 rounded-xl bg-pirate-gold/10 flex items-center justify-center text-pirate-gold">
                            <span class="material-symbols-outlined">military_tech</span>
                        </div>
                        <div>
                            <h4 class="font-bold">Total Active Formations</h4>
                            <p class="text-2xl font-black text-white">{{ store.formations?.length ?? "N/A" }}</p>
                        </div>
                    </div>
                    <div
                        class="p-6 rounded-2xl bg-white/5 border border-slate-200 dark:border-[#224249] flex items-center gap-4">
                        <div class="size-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined">group</span>
                        </div>
                        <div>
                            <h4 class="font-bold">Total Enrollment</h4>
                            <p class="text-2xl font-black text-white">{{ store.formations?.reduce((acc, formation) => acc + formation.students_count, 0) ?? "N/A" }} Apprentices</p>
                        </div>
                    </div>
                    <div
                        class="p-6 rounded-2xl bg-white/5 border border-slate-200 dark:border-[#224249] flex items-center gap-4">
                        <div
                            class="size-12 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                            <span class="material-symbols-outlined">archive</span>
                        </div>
                        <div>
                            <h4 class="font-bold">Archived Formations</h4>
                            <p class="text-2xl font-black text-white">{{ store.archived_formations?.length ?? "N/A" }} Past Grades</p>
                        </div>
                    </div>
                </section>
            </div>
            <footer
                class="p-10 border-t border-slate-200 dark:border-[#224249] text-center text-slate-400 dark:text-[#90c1cb] text-sm mt-auto bg-background-dark/50 backdrop-blur-sm">
                © 1524 Grand Line Academy Management System • Formation &amp; Strategy Hub • Ver. 4.2.0
            </footer>
        </main>
        <main v-else class="flex-1 flex flex-col min-w-0 bg-background-light dark:bg-background-dark overflow-y-auto ocean-waves">
            <header
                class="h-20 flex items-center justify-between px-10 border-b border-slate-200 dark:border-[#224249] bg-white/5 backdrop-blur-md sticky top-0 z-10">
                <div class="flex items-center gap-4">
                    <h2 class="text-lg font-bold">Uncharted Strategies <span class="text-pirate-gold font-normal">/
                            Strategic Formations</span></h2>
                </div>
                <div class="flex items-center gap-4">
                    <NuxtLink to="/admin/formation/create"
                        class="flex items-center gap-2 px-6 py-2.5 bg-pirate-gold rounded-lg text-background-dark font-black text-sm hover:brightness-110 transition-all shadow-lg shadow-pirate-gold/20 uppercase tracking-tighter">
                        <span class="material-symbols-outlined font-bold">edit_note</span>
                        <span>Draft New Formation</span>
                    </NuxtLink>
                </div>
            </header>
            <div class="flex-1 flex flex-col items-center justify-center p-10 max-w-[1400px] mx-auto w-full">
                <div
                    class="empty-state-card blueprint-pattern w-full max-w-2xl p-16 rounded-[2rem] flex flex-col items-center text-center space-y-8 relative overflow-hidden">
                    <div class="absolute top-4 left-4 opacity-10">
                        <span class="material-symbols-outlined text-6xl">straighten</span>
                    </div>
                    <div class="absolute bottom-4 right-4 opacity-10">
                        <span class="material-symbols-outlined text-6xl">compass_calibration</span>
                    </div>
                    <div class="relative">
                        <div class="bg-amber-100/10 rounded-2xl p-8 mb-4 border border-pirate-gold/30">
                            <span
                                class="material-symbols-outlined text-8xl text-pirate-gold/80 leading-none">scrollable_header</span>
                        </div>
                        <div class="absolute -top-2 -right-2 bg-primary p-2 rounded-full shadow-lg">
                            <span class="material-symbols-outlined text-white text-xl">question_mark</span>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <h3 class="text-3xl font-black tracking-tight text-white uppercase">No strategies drafted
                            yet...</h3>
                        <p class="text-[#90c1cb] text-lg font-medium italic">Plot your course! The Grand Line requires
                            a master plan before setting sail.</p>
                    </div>
                    <div class="flex flex-col items-center gap-4 pt-4">
                        <NuxtLink to="/admin/formation/create"
                            class="flex items-center gap-3 px-8 py-4 bg-pirate-gold rounded-xl text-background-dark font-black text-lg hover:scale-105 active:scale-95 transition-all shadow-xl shadow-pirate-gold/30 uppercase tracking-tight">
                            <span class="material-symbols-outlined font-black">history_edu</span>
                            <span>Draft New Formation</span>
                        </NuxtLink>
                        <p class="text-slate-500 text-xs uppercase tracking-[0.3em] font-bold">Secure Your Fleet's
                            Future</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-16 w-full opacity-50">
                    <div
                        class="p-6 rounded-2xl bg-white/5 border border-slate-200 dark:border-[#224249] flex items-center gap-4">
                        <div
                            class="size-12 rounded-xl bg-pirate-gold/10 flex items-center justify-center text-pirate-gold">
                            <span class="material-symbols-outlined">format_list_bulleted</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-xs uppercase tracking-widest text-[#90c1cb]">Active Layouts</h4>
                            <p class="text-2xl font-black text-white">0</p>
                        </div>
                    </div>
                    <div
                        class="p-6 rounded-2xl bg-white/5 border border-slate-200 dark:border-[#224249] flex items-center gap-4">
                        <div class="size-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined">history</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-xs uppercase tracking-widest text-[#90c1cb]">Past Manoeuvres</h4>
                            <p class="text-2xl font-black text-white">None</p>
                        </div>
                    </div>
                    <div
                        class="p-6 rounded-2xl bg-white/5 border border-slate-200 dark:border-[#224249] flex items-center gap-4">
                        <div
                            class="size-12 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                            <span class="material-symbols-outlined">flag</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-xs uppercase tracking-widest text-[#90c1cb]">Objective Reach</h4>
                            <p class="text-2xl font-black text-white">--</p>
                        </div>
                    </div>
                </div>
            </div>
            <footer
                class="p-10 border-t border-slate-200 dark:border-[#224249] text-center text-slate-400 dark:text-[#90c1cb] text-sm mt-auto bg-background-dark/50 backdrop-blur-sm">
                © 1524 Grand Line Academy Management System • Strategic Formation Center • Ver. 4.2.0
            </footer>
        </main>
        <div id="archive-modal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 hidden">
            <div
                class="relative w-full max-w-md bg-[#1a2e33] rounded-2xl overflow-hidden border border-pirate-gold/30 shadow-2xl gold-border-glow">
                <div class="absolute inset-0 skull-watermark opacity-10"></div>
                <div class="absolute bottom-0 left-0 right-0 h-32 modal-waves opacity-20"></div>
                <div class="relative p-8 flex flex-col items-center text-center">
                    <div
                        class="mb-6 size-20 rounded-full bg-archive-red/10 border border-archive-red/30 flex items-center justify-center shadow-inner">
                        <span class="material-symbols-outlined text-archive-red text-4xl font-bold">folder_zip</span>
                    </div>
                    <h3 class="text-2xl font-black text-white mb-3 tracking-tight">Shelve this Strategy?</h3>
                    <p class="text-[#90c1cb] mb-8 leading-relaxed">
                        This formation will be moved to the <span
                            class="title text-pirate-gold font-semibold uppercase tracking-wider text-xs px-1.5 py-0.5 bg-pirate-gold/10 rounded"></span>. Active crew members in this rank will need to be reassigned to a new voyage
                        strategy.
                    </p>
                    <div class="flex flex-col w-full gap-3">
                        <form>
                            <button
                                class="w-full py-3.5 bg-archive-red hover:bg-red-800 text-white font-bold rounded-xl transition-all shadow-lg shadow-red-900/40 uppercase tracking-widest text-sm border border-red-500/20">
                                Archive Strategy
                            </button>
                        </form>
                        <button @click="closeArchiveModal()"
                            class="w-full py-3.5 bg-transparent hover:bg-white/5 text-slate-400 hover:text-white font-bold rounded-xl transition-all uppercase tracking-widest text-sm">
                            Keep Active
                        </button>
                    </div>
                    <div class="absolute top-4 right-4 opacity-10 rotate-12">
                        <span class="material-symbols-outlined text-4xl text-white">skull</span>
                    </div>
                </div>
            </div>
        </div>

        <div id="resurrect-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 hidden">
            <div
                class="w-full max-w-md bg-[#1a2e33] border-2 border-primary/30 rounded-2xl shadow-[0_0_50px_rgba(13,204,242,0.15)] overflow-hidden relative">
                <div class="absolute -top-12 -right-12 opacity-5 pointer-events-none">
                    <span class="material-symbols-outlined text-[180px] text-primary select-none">explore</span>
                </div>
                <div class="p-8 relative z-10">
                    <div
                        class="size-16 bg-ocean-turquoise/10 rounded-full flex items-center justify-center mb-6 mx-auto border border-ocean-turquoise/20">
                        <span class="material-symbols-outlined text-ocean-turquoise text-4xl">unarchive</span>
                    </div>
                    <div class="text-center space-y-3">
                        <h3 class="text-2xl font-black text-white tracking-tight uppercase">Redeploy this Strategy?</h3>
                        <p class="text-[#90c1cb] text-sm leading-relaxed">
                            You are about to restore the <span class="title text-primary font-bold"></span>
                            formation. This action will bring the strategy back to active status for current fleet
                            operations.
                        </p>
                    </div>
                    <div class="mt-10 flex flex-col gap-3">
                        <form>
                            <button
                                class="w-full py-3.5 bg-ocean-turquoise text-background-dark font-black rounded-xl hover:brightness-110 transition-all shadow-lg shadow-ocean-turquoise/20 uppercase tracking-tighter text-sm">
                                Return to Service
                            </button>
                        </form>
                        <button @click="closeResurrectModal()"
                            class="w-full py-3.5 bg-transparent border border-[#224249] text-[#90c1cb] font-bold rounded-xl hover:bg-white/5 transition-all text-sm uppercase tracking-tighter">
                            Keep Shelved
                        </button>
                    </div>
                </div>
                <div class="absolute bottom-4 left-4 opacity-20">
                    <span class="material-symbols-outlined text-sm text-primary">explore</span>
                </div>
            </div>
        </div>
    </NuxtLayout>
</template>