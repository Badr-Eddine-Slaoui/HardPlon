<script setup lang="ts">
    import { useBrief } from '../../../../stores/brief';

    useHead({
        title: `Teacher Dashboard - Briefs`
    })

    const store = useBrief();

    onMounted(async() => {
        await store.fetchBriefs();
    })


    function openArchiveModal(id: number): void {
        const modal = document.getElementById('archive-modal') as HTMLElement;
        modal.classList.remove('hidden');

        const form = modal.querySelector('form') as HTMLFormElement;
        form.onsubmit = async (e) => {
            e.preventDefault();
            await store.archiveBrief(id);
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
            await store.restoreBrief(id);
            closeResurrectModal();
        }
    }

    function closeResurrectModal(): void {
        const modal = document.getElementById('resurrect-modal') as HTMLElement;
        modal.classList.add('hidden');
    }

    const changePage = async (page: number) => {
        await store.fetchBriefs(page);
    };

    definePageMeta({
        middleware: ['auth', 'teacher']
    })
</script>

<template>
    <NuxtLayout name="teacher">
        <main v-if="store.briefs?.length as number > 0" class="flex-1 flex flex-col overflow-y-auto">
            <header
                class="h-16 border-b border-slate-200 dark:border-[#224249] bg-white/80 dark:bg-background-dark/80 backdrop-blur-md flex items-center justify-between px-8 sticky top-0 z-10">
                <div class="flex items-center gap-4">
                    <h2 class="text-2xl font-bold tracking-tight adventure-title text-primary">Mission Logbook</h2>
                    <div class="hidden md:flex items-center gap-2">
                        <span
                            class="px-2 py-0.5 rounded bg-primary/10 text-primary text-[10px] font-bold border border-primary/20">REGISTRY
                            2.4</span>
                        <span class="h-4 w-[1px] bg-slate-200 dark:bg-[#224249]"></span>
                        <p class="text-xs text-slate-500 dark:text-slate-400 italic">"Chart your course, find the One
                            Piece."</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <NuxtLink to="/teacher/brief/create"
                        class="bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark px-5 py-2 rounded-lg font-bold text-sm transition-all shadow-[0_4px_10px_rgba(212,175,55,0.3)] hover:shadow-[0_4px_20px_rgba(212,175,55,0.5)] flex items-center gap-2 border border-pirate-gold-dark/30">
                        <span class="material-symbols-outlined text-lg">edit_note</span>
                        Draft New Mission
                    </NuxtLink>
                </div>
            </header>
            <div class="p-8 max-w-7xl mx-auto w-full">
                <div
                    class="bg-white dark:bg-[#182f34] border border-slate-200 dark:border-[#224249] rounded-xl overflow-hidden shadow-xl parchment-effect">
                    <div
                        class="p-6 border-b border-slate-200 dark:border-[#224249] flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <div class="relative w-full lg:w-96">
                            <span
                                class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                            <input
                                class="w-full bg-slate-50 dark:bg-background-dark border-slate-200 dark:border-[#224249] rounded-lg pl-10 pr-4 py-2 text-sm focus:ring-primary focus:border-primary placeholder:italic"
                                placeholder="Scout for missions..." type="text" />
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-bold uppercase text-slate-400">Saga:</span>
                                <select
                                    class="bg-slate-50 dark:bg-background-dark border-slate-200 dark:border-[#224249] rounded-lg px-3 py-1.5 text-xs focus:ring-primary focus:border-primary">
                                    <option>All Sprints</option>
                                    <option>East Blue Saga</option>
                                    <option>Alabasta Arc</option>
                                    <option>Skypiea Saga</option>
                                    <option>Water 7 Arc</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="bg-slate-50/50 dark:bg-background-dark/50 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-[#224249]">
                                    <th class="px-6 py-4">Mission Title</th>
                                    <th class="px-6 py-4">Associated Sprint</th>
                                    <th class="px-6 py-4">Type</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-[#224249]">
                                <template v-for="brief in store.briefs" :key="brief.id">
                                    <tr v-if="!brief.deleted_at" class="hover:bg-slate-50 dark:hover:bg-[#224249]/30 transition-colors group">
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="size-9 rounded-lg bg-primary/10 border border-primary/20 flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                                                    <span class="material-symbols-outlined text-xl">map</span>
                                                </div>
                                                <div>
                                                    <p
                                                        class="text-sm font-bold group-hover:text-primary transition-colors adventure-title tracking-wide text-lg">
                                                        {{ brief.title }}</p>
                                                    <p
                                                        class="text-[10px] text-slate-500 dark:text-slate-400 flex items-center gap-1">
                                                        <span
                                                            class="material-symbols-outlined text-[12px]">calendar_today</span>
                                                        Logged: {{ (new Date(brief.created_at )).toLocaleString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <span
                                                class="text-sm font-medium text-slate-600 dark:text-slate-300">{{ brief.sprint?.name }}</span>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div v-if="brief.is_collective" class="flex items-center gap-1.5 text-xs font-semibold">
                                                <span
                                                    class="material-symbols-outlined text-sm text-primary">groups</span>
                                                Crew
                                            </div>
                                            <div v-else class="flex items-center gap-1.5 text-xs font-semibold">
                                                <span
                                                    class="material-symbols-outlined text-sm text-primary">person</span>
                                                Solo
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                                <span
                                                    class="size-1.5 bg-emerald-500 rounded-full mr-1.5 animate-pulse"></span>
                                                ACTIVE
                                            </span>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="flex justify-center gap-2">
                                                <NuxtLink :to="`/teacher/brief/${brief.id}`"
                                                    class="size-8 rounded-lg flex items-center justify-center bg-slate-100 dark:bg-[#224249] hover:bg-primary/20 text-slate-400 hover:text-primary transition-all group/btn"
                                                    title="See Details">
                                                    <span class="material-symbols-outlined text-lg">visibility</span>
                                                </NuxtLink>
                                                <NuxtLink :to="`/teacher/brief/edit/${brief.id}`"
                                                    class="size-8 rounded-lg flex items-center justify-center bg-slate-100 dark:bg-[#224249] hover:bg-pirate-gold/20 text-slate-400 hover:text-pirate-gold transition-all"
                                                    title="Update Mission">
                                                    <span class="material-symbols-outlined text-lg">edit</span>
                                                </NuxtLink>
                                                <button @click="openArchiveModal(brief.id)"
                                                    class="size-8 rounded-lg flex items-center justify-center bg-slate-100 dark:bg-[#224249] hover:bg-red-500/20 text-slate-400 hover:text-red-500 transition-all"
                                                    title="Archive Mission">
                                                    <span class="material-symbols-outlined text-lg">archive</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-else
                                        class="hover:bg-slate-50 dark:hover:bg-[#224249]/30 transition-colors group opacity-55 hover:opacity-100 transition-opacity">
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="size-9 rounded-lg bg-primary/10 border border-primary/20 flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                                                    <span class="material-symbols-outlined text-xl">map</span>
                                                </div>
                                                <div>
                                                    <p
                                                        class="text-sm font-bold group-hover:text-primary transition-colors adventure-title tracking-wide text-lg">
                                                        {{ brief.title }}</p>
                                                    <p
                                                        class="text-[10px] text-slate-500 dark:text-slate-400 flex items-center gap-1">
                                                        <span
                                                            class="material-symbols-outlined text-[12px]">calendar_today</span>
                                                        Logged: {{ (new Date(brief.created_at )).toLocaleString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <span
                                                class="text-sm font-medium text-slate-600 dark:text-slate-300">{{ brief.sprint?.name }}</span>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div v-if="brief.is_collective" class="flex items-center gap-1.5 text-xs font-semibold">
                                                <span
                                                    class="material-symbols-outlined text-sm text-primary">groups</span>
                                                Crew
                                            </div>
                                            <div v-else class="flex items-center gap-1.5 text-xs font-semibold">
                                                <span
                                                    class="material-symbols-outlined text-sm text-primary">person</span>
                                                Solo
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-500/10 text-slate-500 border border-slate-500/20 uppercase">
                                                Archived
                                            </span>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="flex justify-center gap-2">
                                                <button @click="openResurrectModal(brief.id)"
                                                    class="size-8 rounded-lg flex items-center justify-center bg-slate-100 dark:bg-[#224249] hover:bg-emerald-500/20 text-slate-400 hover:text-emerald-500 transition-all"
                                                    title="Restore Mission">
                                                    <span class="material-symbols-outlined text-lg">unarchive</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div class="p-6 border-t border-slate-200 dark:border-[#224249] flex items-center justify-between">
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Showing <span class="font-bold text-primary">{{ store.meta.from }}</span> to <span
                                class="font-bold text-primary">{{ store.meta.to }}</span> of <span
                                class="font-bold text-primary">{{ store.meta.total }}</span> missions
                        </p>
                        <div class="flex items-center gap-2">
                            <button @click="changePage(store.meta.current_page - 1)" :disabled="store.meta.current_page === 1"
                                class="size-8 rounded-lg flex items-center justify-center border border-slate-200 dark:border-[#224249] text-slate-500 hover:bg-slate-50 dark:hover:bg-[#224249] disabled:opacity-50 transition-all">
                                <span class="material-symbols-outlined text-lg">chevron_left</span>
                            </button>
                            <template v-for="page in store.meta.last_page" :key="page">
                                <button v-if="page >= store.meta.current_page - 2 && page <= store.meta.current_page + 2"
                                    @click="changePage(page)"
                                    :class="[
                                        'size-8 rounded-lg flex items-center justify-center text-xs font-bold transition-all',
                                        store.meta.current_page === page
                                            ? 'bg-primary text-background-dark shadow-lg shadow-primary/20'
                                            : 'border border-slate-200 dark:border-[#224249] text-slate-500 hover:bg-slate-50 dark:hover:bg-[#224249]'
                                    ]">
                                    {{ page }}
                                </button>
                            </template>
                            <button @click="changePage(store.meta.current_page + 1)"
                                :disabled="store.meta.current_page === store.meta.last_page"
                                class="size-8 rounded-lg flex items-center justify-center border border-slate-200 dark:border-[#224249] text-slate-500 hover:bg-slate-50 dark:hover:bg-[#224249] disabled:opacity-50 transition-all">
                                <span class="material-symbols-outlined text-lg">chevron_right</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <footer
                class="mt-auto border-t border-slate-200 dark:border-[#224249] bg-white dark:bg-[#102023] px-8 py-3 flex items-center justify-between text-[11px] font-medium text-slate-500 uppercase tracking-widest">
                <div class="flex gap-6">
                    <span class="flex items-center gap-1.5"><span
                            class="size-2 bg-emerald-500 rounded-full shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                        Signal: Logged On</span>
                    <span class="flex items-center gap-1.5"><span
                            class="material-symbols-outlined text-[14px]">anchor</span>
                        Fleet: Marine HQ Server</span>
                </div>
                <div class="flex gap-4">
                    <span class="text-primary/70">V 2.4.0-ARABASTA</span>
                    <a class="hover:text-primary transition-colors flex items-center gap-1" href="#">
                        <span class="material-symbols-outlined text-[14px]">help</span> Help Center
                    </a>
                </div>
            </footer>
        </main>
        <main v-else class="flex-1 flex flex-col overflow-y-auto">
            <header
                class="h-16 border-b border-slate-200 dark:border-[#224249] bg-white/80 dark:bg-background-dark/80 backdrop-blur-md flex items-center justify-between px-8 sticky top-0 z-10">
                <div class="flex items-center gap-4">
                    <h2 class="text-2xl font-bold tracking-tight adventure-title text-primary">Mission Logbook</h2>
                    <div class="hidden md:flex items-center gap-2">
                        <span
                            class="px-2 py-0.5 rounded bg-primary/10 text-primary text-[10px] font-bold border border-primary/20">REGISTRY
                            2.4</span>
                        <span class="h-4 w-[1px] bg-slate-200 dark:bg-[#224249]"></span>
                        <p class="text-xs text-slate-500 dark:text-slate-400 italic">"Chart your course, find the One
                            Piece."</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <NuxtLink to="/teacher/brief/create"
                        class="bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark px-5 py-2 rounded-lg font-bold text-sm transition-all shadow-[0_4px_10px_rgba(212,175,55,0.3)] hover:shadow-[0_4px_20px_rgba(212,175,55,0.5)] flex items-center gap-2 border border-pirate-gold-dark/30">
                        <span class="material-symbols-outlined text-lg">edit_note</span>
                        Draft New Mission
                    </NuxtLink>
                </div>
            </header>
            <div class="flex-1 flex flex-col items-center justify-center p-8">
                <div class="max-w-2xl w-full text-center space-y-8">
                    <div class="relative flex justify-center">
                        <div class="absolute inset-0 bg-primary/5 blur-3xl rounded-full"></div>
                        <div class="relative z-10 floating">
                            <div
                                class="size-48 lg:size-64 rounded-full border-2 border-dashed border-primary/20 flex items-center justify-center bg-[#182f34]/40 backdrop-blur-sm">
                                <div class="flex flex-col items-center">
                                    <span
                                        class="material-symbols-outlined text-8xl lg:text-9xl text-pirate-gold/60 select-none">inventory_2</span>
                                    <span
                                        class="material-symbols-outlined text-4xl text-primary/40 -mt-6 select-none">sailing</span>
                                </div>
                            </div>
                            <span
                                class="material-symbols-outlined absolute -top-4 -right-4 text-primary/30 text-3xl animate-pulse">stars</span>
                            <span
                                class="material-symbols-outlined absolute bottom-4 -left-8 text-primary/20 text-4xl">water_lux</span>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <h3 class="adventure-title text-4xl lg:text-5xl text-white tracking-wide">No missions in the
                            logbook yet...</h3>
                        <p class="text-slate-400 text-lg lg:text-xl font-light italic">"The sea is vast and full of
                            secrets. Chart your first course and begin the adventure!"</p>
                    </div>
                    <div class="flex flex-col items-center gap-6 pt-4">
                        <NuxtLink to="/teacher/brief/create"
                            class="bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark px-10 py-4 rounded-xl font-black text-lg transition-all shadow-[0_10px_30px_rgba(212,175,55,0.3)] hover:shadow-[0_15px_40px_rgba(212,175,55,0.5)] flex items-center gap-3 border-2 border-pirate-gold-dark/40 scale-100 hover:scale-105 active:scale-95">
                            <span class="material-symbols-outlined text-2xl">add_location_alt</span>
                            Draft New Mission
                        </NuxtLink>
                        <div class="flex items-center gap-8 text-slate-500">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">history_edu</span>
                                <span class="text-[10px] font-bold uppercase tracking-widest">Tutorial Guide</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">auto_stories</span>
                                <span class="text-[10px] font-bold uppercase tracking-widest">Mission Templates</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <footer
                class="mt-auto border-t border-slate-200 dark:border-[#224249] bg-white dark:bg-[#102023] px-8 py-3 flex items-center justify-between text-[11px] font-medium text-slate-500 uppercase tracking-widest">
                <div class="flex gap-6">
                    <span class="flex items-center gap-1.5"><span
                            class="size-2 bg-emerald-500 rounded-full shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                        Signal: Logged On</span>
                    <span class="flex items-center gap-1.5"><span
                            class="material-symbols-outlined text-[14px]">anchor</span> Fleet: Marine HQ Server</span>
                </div>
                <div class="flex gap-4">
                    <span class="text-primary/70">V 2.4.0-ARABASTA</span>
                    <a class="hover:text-primary transition-colors flex items-center gap-1" href="#">
                        <span class="material-symbols-outlined text-[14px]">help</span> Help Center
                    </a>
                </div>
            </footer>
        </main>
        <div id="archive-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 hidden">
            <div
                class="bg-[#182f34] border-2 border-pirate-gold/30 rounded-2xl w-full max-w-md shadow-[0_20px_50px_rgba(0,0,0,0.5)] overflow-hidden">
                <div class="bg-background-dark p-6 border-b border-pirate-gold/20 flex items-center gap-4">
                    <div
                        class="size-12 rounded-full bg-dark-red/20 border border-dark-red/50 flex items-center justify-center text-accent-red">
                        <span class="material-symbols-outlined text-3xl">inventory_2</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold adventure-title text-pirate-gold tracking-wide">Send to the Locker?
                        </h3>
                        <p class="text-[10px] uppercase tracking-[0.2em] text-slate-400 font-bold">Archive Mission</p>
                    </div>
                </div>
                <div class="p-8">
                    <div class="flex flex-col items-center text-center gap-4">
                        <span class="material-symbols-outlined text-5xl text-slate-500/30">sailing</span>
                        <p class="text-slate-300 font-medium">
                            This mission will be cast into Davy Jones' Locker. It will be hidden from your student crew and
                            preserved in the archives.
                        </p>
                        <div class="w-full h-px bg-gradient-to-r from-transparent via-pirate-gold/20 to-transparent my-2">
                        </div>
                        <p class="text-xs text-slate-500 italic">"The sea never forgets, but the crew won't see this chart
                            anymore."</p>
                    </div>
                </div>
                <div class="p-6 bg-background-dark/50 flex flex-col sm:flex-row gap-3">
                    <button @click="closeArchiveModal()"
                        class="flex-1 px-6 py-3 rounded-xl border border-slate-700 text-slate-300 font-bold hover:bg-slate-800 transition-colors flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-lg">explore</span>
                        Keep Sailing
                    </button>
                    <form>
                        <button type="submit"
                            class="flex-1 px-6 py-3 rounded-xl bg-dark-red hover:bg-accent-red text-white font-bold transition-all shadow-lg shadow-dark-red/20 flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-lg">archive</span>
                            Move to Locker
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div id="resurrect-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 hidden">
            <div
                class="bg-white dark:bg-[#102023] border-2 border-primary/30 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.5)] max-w-md w-full overflow-hidden parchment-effect relative">
                <div class="absolute -right-12 -top-12 opacity-5 text-primary">
                    <span class="material-symbols-outlined text-[160px] rotate-12">explore</span>
                </div>
                <div class="p-8 relative">
                    <div class="flex justify-center mb-6">
                        <div
                            class="size-16 rounded-full bg-primary/10 border-2 border-primary/20 flex items-center justify-center text-primary shadow-[0_0_20px_rgba(13,204,242,0.2)]">
                            <span class="material-symbols-outlined text-3xl">sailing</span>
                        </div>
                    </div>
                    <div class="text-center space-y-3">
                        <h3 class="text-2xl adventure-title text-primary tracking-wide">Resurface this Mission?
                        </h3>
                        <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed px-2">
                            Captain, are you sure you wish to bring this brief back into the active fleet? This brief will be returned to
                            the Registry for all crew members to see.
                        </p>
                    </div>
                    <div class="mt-8 flex flex-col sm:flex-row gap-3">
                        <button @click="closeResurrectModal()"
                            class="flex-1 px-6 py-3 rounded-xl border border-slate-200 dark:border-[#224249] text-slate-600 dark:text-slate-400 font-bold text-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-all text-center">
                            Stay Hidden
                        </button>
                        <form>
                            <button
                                type="submit"
                                class="flex-1 px-6 py-3 rounded-xl bg-ocean-turquoise hover:bg-teal-600 text-white font-black text-sm transition-all shadow-lg shadow-ocean-turquoise/30 flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-lg">anchor</span>
                                Set Sail
                            </button>
                        </form>
                    </div>
                </div>
                <div class="h-1.5 w-full bg-gradient-to-r from-transparent via-primary/30 to-transparent"></div>
            </div>
        </div>
    </NuxtLayout>
</template>