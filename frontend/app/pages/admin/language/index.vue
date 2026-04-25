<script setup lang="ts">
    import { useLanguage } from '~~/stores/language';

    useHead({
        title: `Admin Dashboard - Sea Tongues`
    })

    const store = useLanguage();

    const paginate = usePagination(store.fetchLanguages.bind(store));

    onMounted(async() => {
        await paginate.fetchData();
    })

    function openArchiveModal(id: number): void {
        const modal = document.getElementById('archive-modal') as HTMLElement;
        modal.classList.remove('hidden');

        const form = modal.querySelector('form') as HTMLFormElement;
        form.onsubmit = async (e) => {
            e.preventDefault();
            await store.archiveLanguage(id);
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
            await store.restoreLanguage(id);
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
        <main v-if="store.languages?.length as number > 0" class="flex h-[calc(100vh-65px)] overflow-hidden w-full">
            <section class="flex-1 overflow-y-auto p-10 custom-scrollbar">
                <div class="max-w-7xl mx-auto">
                    <div class="flex justify-between items-end mb-8">
                        <div>
                            <h2 class="text-3xl font-bold text-white font-display mb-1">Sea Tongues</h2>
                            <p class="text-[#90c1cb]">Manage the arcane dialects used to command the mechanical beasts.</p>
                        </div>
                        <NuxtLink to="/admin/language/create"
                            class="flex items-center gap-2 px-6 py-3 bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark font-bold rounded-lg transition-all transform hover:scale-105 shadow-[0_4px_14px_0_rgba(255,215,0,0.39)]">
                            <span class="material-symbols-outlined font-bold">translate</span>
                            <span>Forge New Tongue</span>
                        </NuxtLink>
                    </div>
                    <div class="rounded-xl border border-[#315f68] bg-[#182f34] overflow-hidden shadow-2xl">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-[#224249]/50 border-b border-[#315f68]">
                                    <th class="px-6 py-4 text-white text-xs font-bold uppercase tracking-wider">Dialect
                                    </th>
                                    <th class="px-6 py-4 text-white text-xs font-bold uppercase tracking-wider">Vessel (Image)
                                    </th>
                                    <th class="px-6 py-4 text-white text-xs font-bold uppercase tracking-wider">Commands
                                    </th>
                                    <th class="px-6 py-4 text-white text-xs font-bold uppercase tracking-wider text-right">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#224249]">
                                <template v-for="language in store.languages">
                                    <tr v-if="!language.deleted_at" class="hover:bg-white/5 transition-colors">
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="size-10 bg-primary/10 rounded-lg flex items-center justify-center border border-primary/20">
                                                    <span class="material-symbols-outlined text-primary">code</span>
                                                </div>
                                                <span
                                                    class="text-white font-bold block">{{ language.name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <span class="text-[#90c1cb] text-xs font-mono bg-background-dark/50 px-2 py-1 rounded border border-[#224249]">{{ language.docker_image }}</span>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="space-y-1">
                                                <p v-if="language.compile_command" class="text-[10px] text-slate-500 font-mono italic">Compile: {{ language.compile_command }}</p>
                                                <p class="text-[10px] text-primary font-mono font-bold">Run: {{ language.run_command }}</p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 text-right">
                                            <div class="flex justify-end gap-2">
                                                <NuxtLink :to="`/admin/language/${language.id}`"
                                                    class="p-2 text-[#90c1cb] hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                                                    title="View Details">
                                                    <span class="material-symbols-outlined">visibility</span>
                                                </NuxtLink>
                                                <NuxtLink :to="`/admin/language/edit/${language.id}`"
                                                    class="p-2 text-[#90c1cb] hover:text-pirate-gold hover:bg-pirate-gold/10 rounded-lg transition-all"
                                                    title="Update Tongue">
                                                    <span class="material-symbols-outlined">edit</span>
                                                </NuxtLink>
                                                <button @click="openArchiveModal(language.id)"
                                                    class="p-2 text-[#90c1cb] hover:text-red-400 hover:bg-red-400/10 rounded-lg transition-all"
                                                    title="Archive Tongue">
                                                    <span class="material-symbols-outlined">archive</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-else class="hover:bg-white/5 transition-colors opacity-70">
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="size-10 bg-primary/10 rounded-lg flex items-center justify-center border border-primary/20">
                                                    <span class="material-symbols-outlined text-primary">code</span>
                                                </div>
                                                <span
                                                    class="text-white font-bold block">{{ language.name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <span class="text-[#90c1cb] text-xs font-mono bg-background-dark/50 px-2 py-1 rounded border border-[#224249]">{{ language.docker_image }}</span>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="space-y-1">
                                                <p v-if="language.compile_command" class="text-[10px] text-slate-500 font-mono italic">Compile: {{ language.compile_command }}</p>
                                                <p class="text-[10px] text-primary font-mono font-bold">Run: {{ language.run_command }}</p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 text-right">
                                            <div class="flex justify-end gap-2">
                                                <button @click="openResurrectModal(language.id)"
                                                    class="p-2 text-red-400 hover:text-[#90c1cb] hover:bg-[#90c1cb]/10 rounded-lg transition-all"
                                                    title="Restore Tongue">
                                                    <span class="material-symbols-outlined">settings_backup_restore</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Controls -->
                    <Pagination :meta="store.meta" :per_page="paginate.perPage.value" @change="paginate.fetchData"
                        @perPage="paginate.changePerPage" />
                </div>
            </section>
        </main>
        <div v-else class="w-full">
            <main class="flex h-[calc(100vh-65px)] overflow-hidden">
                <section class="flex-1 overflow-y-auto p-10 custom-scrollbar flex items-center justify-center">
                    <div class="max-w-xl w-full text-center">
                        <div class="relative mb-8 flex justify-center">
                            <div
                                class="w-80 h-48 relative overflow-hidden rounded-2xl sea-horizon border border-[#224249] bg-[#14262b] flex items-center justify-center">
                                <div
                                    class="absolute -bottom-4 w-24 h-24 bg-gradient-to-t from-pirate-gold/40 to-transparent rounded-full blur-xl">
                                </div>
                                <div
                                    class="absolute bottom-0 w-16 h-8 bg-pirate-gold/20 rounded-t-full border-t border-pirate-gold/30">
                                </div>
                                <div class="absolute bottom-4 left-1/4 w-1/2 h-px bg-[#224249]"></div>
                                <div class="absolute bottom-8 left-1/3 w-1/3 h-px bg-[#224249]/60"></div>
                                <div class="relative flex flex-col gap-3 items-center opacity-30">
                                    <div class="w-16 h-1 bg-[#90c1cb] rounded-full"></div>
                                    <div class="w-20 h-1 bg-[#90c1cb] rounded-full"></div>
                                    <div class="w-24 h-1 bg-[#90c1cb] rounded-full"></div>
                                </div>
                                <span
                                    class="material-symbols-outlined text-6xl text-[#224249] absolute top-1/3 opacity-40">translate</span>
                            </div>
                        </div>
                        <h2 class="text-3xl font-bold text-white font-display mb-3">Silent Oceans</h2>
                        <p class="text-[#90c1cb] text-lg mb-8">No sea tongues forged yet... <br />define the dialects of the mechanical fleet!</p>
                        <NuxtLink to="/admin/language/create"
                            class="inline-flex items-center gap-2 px-8 py-4 bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark font-bold rounded-lg transition-all transform hover:scale-105 shadow-[0_4px_20px_0_rgba(255,215,0,0.3)]">
                            <span class="material-symbols-outlined font-bold">translate</span>
                            <span>Forge New Tongue</span>
                        </NuxtLink>
                    </div>
                </section>
            </main>
        </div>

        <!-- Archive Modal -->
        <div id="archive-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-background-dark/80 backdrop-blur-sm px-4 hidden">
            <div
                class="relative w-full max-w-md overflow-hidden rounded-xl border border-marine-crimson/50 bg-[#182f34] shadow-[0_0_50px_rgba(139,0,0,0.2)]">
                <div class="absolute inset-0 wave-pattern opacity-20 pointer-events-none"></div>
                <div class="relative p-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div
                            class="size-12 flex items-center justify-center rounded-full bg-marine-crimson/20 border border-marine-crimson/40 text-marine-crimson">
                            <span class="material-symbols-outlined text-3xl">skull</span>
                        </div>
                        <h3 class="text-2xl font-bold text-white font-display">Silence this Tongue?</h3>
                    </div>
                    <div class="mb-8">
                        <p class="text-[#90c1cb] leading-relaxed">
                            By confirming this action, the selected dialect will be silenced and cast into the <span
                                class="text-primary font-semibold">Impel Down Archives</span>.
                        </p>
                        <div class="mt-4 p-3 rounded-lg bg-marine-crimson/10 border border-marine-crimson/20">
                            <p class="text-red-300 text-sm flex items-start gap-2">
                                <span class="material-symbols-outlined text-sm mt-0.5">warning</span>
                                <span>This tongue will no longer be available for problem configuration or tactical execution until reactivated.</span>
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-col gap-3">
                        <form>
                            <button
                                class="w-full py-3 bg-marine-crimson hover:bg-marine-crimson-dark text-white font-bold rounded-lg transition-all shadow-lg shadow-black/20 flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-lg">archive</span>
                                <span>Archive Tongue</span>
                            </button>
                        </form>
                        <button @click="closeArchiveModal()"
                            class="w-full py-3 bg-[#224249] hover:bg-[#315f68] text-white font-medium rounded-lg transition-all border border-[#315f68]">
                            Keep Active
                        </button>
                    </div>
                </div>
                <div class="h-1.5 w-full flex">
                    <div class="flex-1 bg-marine-crimson"></div>
                    <div class="flex-1 bg-white"></div>
                    <div class="flex-1 bg-marine-crimson"></div>
                </div>
            </div>
        </div>

        <!-- Restore Modal -->
        <div id="resurrect-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-background-dark/80 backdrop-blur-sm px-4 hidden">
            <div
                class="relative w-full max-w-lg overflow-hidden rounded-xl border border-[#315f68] bg-[#182f34] shadow-2xl">
                <div class="p-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div
                            class="flex size-14 items-center justify-center rounded-full bg-primary/10 border border-primary/30">
                            <span class="material-symbols-outlined text-primary text-3xl">settings_backup_restore</span>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-white font-display">Restore this Tongue?</h3>
                            <p class="text-primary text-sm font-medium uppercase tracking-wider">Tongue Restoration Protocol
                            </p>
                        </div>
                    </div>
                    <div class="mb-8">
                        <p class="text-[#90c1cb] leading-relaxed text-lg">
                            Are you sure you want to bring this dialect back to the <span
                                class="text-white font-semibold">active mastery tongues</span>? This will make the dialect
                            visible and usable for all marines in the fleet.
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button @click="closeResurrectModal()"
                            class="flex-1 px-6 py-3 border border-[#315f68] text-[#90c1cb] hover:text-white hover:bg-white/5 font-bold rounded-lg transition-all">
                            Stay Silenced
                        </button>
                        <form>
                            <button
                                class="flex-1 px-6 py-3 bg-primary hover:brightness-110 text-background-dark font-bold rounded-lg transition-all shadow-[0_4px_14px_0_rgba(13,204,242,0.3)] flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-xl">verified</span>
                                Reactivate Tongue
                            </button>
                        </form>
                    </div>
                </div>
                <div class="h-1 w-full bg-gradient-to-r from-transparent via-primary/50 to-transparent opacity-30"></div>
            </div>
        </div>
    </NuxtLayout>
</template>
