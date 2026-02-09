<script setup lang="ts">
    import { useSprint } from '../../../../stores/sprint';

    useHead({
        title: `Admin Dashboard - Sprints`
    })

    const store = useSprint();

    onMounted(async() => {
        await store.fetchSprints();
    })

    function openArchiveModal(id: number): void {
        const modal = document.getElementById('archive-modal') as HTMLElement;
        modal.classList.remove('hidden');

        const form = modal.querySelector('form') as HTMLFormElement;
        form.onsubmit = async (e) => {
            e.preventDefault();
            await store.archiveSprint(id);
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
            await store.restoreSprint(id);
            closeResurrectModal();
        }
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
        <main v-if="store.sprints?.length as number > 0" class="w-full flex min-h-screen overflow-hidden">
            <section class="flex-1 overflow-y-auto p-10 custom-scrollbar">
                <div class="max-w-7xl mx-auto">
                    <div class="flex justify-between items-end mb-8">
                        <div>
                            <h2 class="text-3xl font-bold text-white font-display mb-1">Sprint Registry</h2>
                            <p class="text-[#90c1cb]">Manage the educational expeditions across the Grand Line.</p>
                        </div>
                        <NuxtLink to="/admin/sprint/create"
                            class="flex items-center gap-2 px-6 py-3 bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark font-bold rounded-lg transition-all transform hover:scale-105 shadow-[0_4px_14px_0_rgba(255,215,0,0.39)]">
                            <span class="material-symbols-outlined font-bold">map</span>
                            <span>Chart New Sprint</span>
                        </NuxtLink>
                    </div>
                    <div class="rounded-xl border border-[#315f68] bg-[#182f34] overflow-hidden shadow-2xl">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-[#224249]/50 border-b border-[#315f68]">
                                    <th class="px-6 py-4 text-white text-xs font-bold uppercase tracking-wider">Sprint
                                        Name</th>
                                    <th class="px-6 py-4 text-white text-xs font-bold uppercase tracking-wider">Sprint
                                        Description</th>
                                    <th class="px-6 py-4 text-white text-xs font-bold uppercase tracking-wider">Timeline
                                    </th>
                                    <th class="px-6 py-4 text-white text-xs font-bold uppercase tracking-wider text-center">
                                        Skills Included</th>
                                    <th class="px-6 py-4 text-white text-xs font-bold uppercase tracking-wider text-right">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#224249]">
                                <template v-for="(sprint, index) in store.sprints" :key="sprint.id">
                                    <tr v-if="sprint.is_active" class="hover:bg-white/5 transition-colors">
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="size-10 bg-primary/10 rounded-lg flex items-center justify-center border border-primary/20">
                                                    <span class="text-primary">S-{{ index + 1 }}</span>
                                                </div>
                                                <div>
                                                    <span
                                                        class="text-white font-bold block">{{ sprint.name }}</span>
                                                    <span
                                                        class="text-xs text-[#90c1cb] block w-[150px] truncate">{{ sprint.formation?.title }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <span class="text-white text-sm block w-[150px] truncate ">{{ sprint.description }}</span>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="flex flex-col">
                                                <span
                                                    class="text-white text-sm">{{ (new Date(sprint.start_date)).toLocaleString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) }}</span>
                                                <span class="text-[#90c1cb] text-xs">to
                                                    {{ (new Date(sprint.end_date)).toLocaleString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 text-center">
                                            <span
                                                class="px-3 py-1 bg-[#224249] text-white text-sm font-bold rounded-full border border-[#315f68]">{{ sprint.sprint_skills?.length }}</span>
                                        </td>
                                        <td class="px-6 py-5 text-right">
                                            <div class="flex justify-end gap-2">
                                                <NuxtLink :to="`/admin/sprint/${sprint.id}`"
                                                    class="p-2 text-[#90c1cb] hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                                                    title="View Details">
                                                    <span class="material-symbols-outlined">visibility</span>
                                                </NuxtLink>
                                                <NuxtLink :to="`/admin/sprint/edit/${sprint.id}`"
                                                    class="p-2 text-[#90c1cb] hover:text-pirate-gold hover:bg-pirate-gold/10 rounded-lg transition-all"
                                                    title="Edit Sprint">
                                                    <span class="material-symbols-outlined">edit</span>
                                                </NuxtLink>
                                                <button @click="openArchiveModal(sprint.id)"
                                                    class="p-2 text-[#90c1cb] hover:text-red-400 hover:bg-red-400/10 rounded-lg transition-all"
                                                    title="Archive Sprint">
                                                    <span class="material-symbols-outlined">archive</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-else class="hover:bg-white/5 transition-colors opacity-55">
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="size-10 bg-primary/10 rounded-lg flex items-center justify-center border border-primary/20">
                                                    <span class="text-primary">S-{{ index + 1 }}</span>
                                                </div>
                                                <div>
                                                    <span
                                                        class="text-white font-bold block">{{ sprint.name }}</span>
                                                    <span
                                                        class="text-xs text-[#90c1cb] block w-[150px] truncate">{{ sprint.formation?.title }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <span class="text-white text-sm block w-[150px] truncate ">{{ sprint.description }}</span>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="flex flex-col">
                                                <span
                                                    class="text-white text-sm">{{ (new Date(sprint.start_date)).toLocaleString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) }}</span>
                                                <span class="text-[#90c1cb] text-xs">to
                                                    {{ (new Date(sprint.end_date)).toLocaleString('en-US', { month: 'long', day: 'numeric', year: 'numeric'} ) }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 text-center">
                                            <span
                                                class="px-3 py-1 bg-[#224249] text-white text-sm font-bold rounded-full border border-[#315f68]">{{ sprint.sprint_skills?.length }}</span>
                                        </td>
                                        <td class="px-6 py-5 text-right">
                                            <div class="flex justify-end gap-2">
                                                <button @click="openResurrectModal(sprint.id)"
                                                    class="p-2 text-red-400 hover:text-[#90c1cb] hover:bg-[#90c1cb]/10 rounded-lg transition-all"
                                                    title="Archive Sprint">
                                                    <span class="material-symbols-outlined">unarchive</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
        <main v-else class="w-full flex min-h-screen overflow-hidden">
            <section class="flex-1 overflow-hidden flex flex-col items-center justify-center p-10 bg-[#0c1a1d]">
                <div class="max-w-2xl w-full flex flex-col items-center text-center">
                    <div class="relative mb-12">
                        <div class="absolute inset-0 bg-primary/5 blur-3xl rounded-full"></div>
                        <div class="relative flex flex-col items-center">
                            <div class="relative w-80 h-48 flex items-center justify-center">
                                <div class="relative">
                                    <span
                                        class="material-symbols-outlined text-[160px] text-[#2a4d55] opacity-40">sailing</span>
                                    <div class="absolute -bottom-2 left-0 w-full h-1 bg-[#1c363d] rounded-full"></div>
                                    <div class="absolute -bottom-6 left-1/2 -translate-x-1/2 flex gap-4">
                                        <div class="size-2 rounded-full bg-[#1c363d] animate-pulse"></div>
                                        <div class="size-2 rounded-full bg-[#1c363d] animate-pulse delay-75"></div>
                                        <div class="size-2 rounded-full bg-[#1c363d] animate-pulse delay-150"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-4 mb-10">
                        <h3 class="text-3xl font-bold text-white font-display tracking-tight">The sea is still...</h3>
                        <p class="text-[#90c1cb] text-lg max-w-md mx-auto leading-relaxed">
                            No sprints have been charted for the journey. We're currently drifting in the Calm Belt.
                        </p>
                    </div>
                    <NuxtLink to="/admin/sprint/create"
                        class="flex items-center gap-3 px-8 py-4 bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark font-bold text-lg rounded-xl transition-all transform hover:scale-105 shadow-[0_4px_20px_0_rgba(255,215,0,0.3)] group">
                        <span
                            class="material-symbols-outlined font-bold group-hover:rotate-12 transition-transform">edit_calendar</span>
                        <span>Chart New Sprint</span>
                    </NuxtLink>
                    <div class="mt-16 grid grid-cols-3 gap-8 w-full border-t border-[#224249] pt-12 opacity-60">
                        <div class="flex flex-col items-center gap-2">
                            <span class="material-symbols-outlined text-primary">anchor</span>
                            <span class="text-xs text-[#90c1cb] uppercase font-bold tracking-widest">Set Objectives</span>
                        </div>
                        <div class="flex flex-col items-center gap-2">
                            <span class="material-symbols-outlined text-primary">groups</span>
                            <span class="text-xs text-[#90c1cb] uppercase font-bold tracking-widest">Assign Crew</span>
                        </div>
                        <div class="flex flex-col items-center gap-2">
                            <span class="material-symbols-outlined text-primary">flag</span>
                            <span class="text-xs text-[#90c1cb] uppercase font-bold tracking-widest">Begin Voyage</span>
                        </div>
                    </div>
                </div>
            </section>
        </main>
        <div id="archive-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm px-4 hidden">
            <div
                class="relative w-full max-w-md bg-[#182f34] border-2 border-[#315f68] rounded-xl shadow-2xl overflow-hidden p-8">
                <span class="material-symbols-outlined text-white skull-watermark">skull</span>
                <div class="relative z-10 flex flex-col items-center text-center">
                    <div
                        class="size-16 bg-marine-red/20 rounded-full flex items-center justify-center border border-marine-red/40 mb-6">
                        <span class="material-symbols-outlined text-marine-red text-4xl">anchor</span>
                    </div>
                    <h3 class="text-2xl font-bold text-white font-display mb-3 tracking-wide uppercase">Abandon this
                        Voyage?</h3>
                    <p class="text-[#90c1cb] text-base leading-relaxed mb-8">
                        By archiving this sprint, you are pulling it from active service. It will no longer appear in
                        active formations and will be moved to the archives.
                    </p>
                    <div class="flex flex-col w-full gap-3">
                        <form>
                            <button
                                class="w-full py-3 bg-marine-red hover:bg-marine-red-dark text-white font-bold rounded-lg transition-all transform hover:scale-[1.02] active:scale-95 shadow-lg border border-marine-red-dark">
                                Abandon Voyage
                            </button>
                        </form>
                        <button @click="closeArchiveModal()"
                            class="w-full py-3 bg-transparent hover:bg-[#224249] text-white font-medium rounded-lg transition-all border border-[#315f68]">
                            Stay on Course
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div id="resurrect-modal" class="fixed inset-0 z-50 flex items-center justify-center modal-overlay p-4 hidden">
            <div
                class="relative w-full max-w-md bg-[#182f34] border border-[#315f68] rounded-xl shadow-[0_20px_50px_rgba(0,0,0,0.5)] overflow-hidden">
                <div class="absolute -top-10 -right-10 opacity-5 pointer-events-none">
                    <span class="material-symbols-outlined text-[160px] text-white rotate-12">explore</span>
                </div>
                <div class="p-8 text-center relative z-10">
                    <div
                        class="inline-flex items-center justify-center size-16 bg-ocean-turquoise/10 rounded-full mb-6 border border-ocean-turquoise/20">
                        <span class="material-symbols-outlined text-4xl text-ocean-turquoise">restore</span>
                    </div>
                    <h3 class="text-2xl font-bold text-white font-display mb-3">Resume this Voyage?</h3>
                    <p class="text-[#90c1cb] text-base leading-relaxed mb-8">
                        By restoring this sprint, you will bring the voyage back to active status for the entire fleet.
                        All progress and logs will be synchronized immediately.
                    </p>
                    <div class="flex flex-col gap-3">
                        <form>
                            <button
                                class="w-full py-3.5 px-6 bg-ocean-turquoise hover:bg-ocean-turquoise-dark text-white font-bold rounded-lg transition-all shadow-lg hover:shadow-ocean-turquoise/20">
                                Resume Voyage
                            </button>
                        </form>
                        <button @click="closeResurrectModal()"
                            class="w-full py-3.5 px-6 bg-transparent hover:bg-[#224249] text-[#90c1cb] hover:text-white font-semibold rounded-lg transition-all border border-[#224249]">
                            Keep Anchored
                        </button>
                    </div>
                </div>
                <div
                    class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-ocean-turquoise to-transparent opacity-30">
                </div>
            </div>
        </div>
    </NuxtLayout>
</template>