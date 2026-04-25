<script setup lang="ts">
    import { useRunner } from '../../../../stores/runner';

    useHead({ title: `Admin Dashboard - Runners` })

    const store = useRunner();
    const paginate = usePagination(store.fetchRunners.bind(store));

    onMounted(async() => { await paginate.fetchData(); })

    function openDeleteModal(id: number): void {
        const modal = document.getElementById('delete-modal') as HTMLElement;
        modal.classList.remove('hidden');
        const form = modal.querySelector('form') as HTMLFormElement;
        form.onsubmit = async (e) => {
            e.preventDefault();
            await store.deleteRunner(id);
            closeDeleteModal();
        }
    }

    function closeDeleteModal(): void {
        const modal = document.getElementById('delete-modal') as HTMLElement;
        modal.classList.add('hidden');
    }

    definePageMeta({ middleware: ['auth', 'admin'] })
</script>

<template>
    <NuxtLayout name="admin">
        <main v-if="store.runners?.length as number > 0" class="flex h-[calc(100vh-65px)] overflow-hidden w-full">
            <section class="flex-1 overflow-y-auto p-10 custom-scrollbar">
                <div class="max-w-7xl mx-auto">
                    <div class="flex justify-between items-end mb-8">
                        <div>
                            <h2 class="text-3xl font-bold text-white font-display mb-1">Runners</h2>
                            <p class="text-[#90c1cb]">Manage the execution runners for automated evaluations.</p>
                        </div>
                        <NuxtLink to="/admin/runner/create" class="flex items-center gap-2 px-6 py-3 bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark font-bold rounded-lg transition-all transform hover:scale-105 shadow-[0_4px_14px_0_rgba(255,215,0,0.39)]">
                            <span class="material-symbols-outlined font-bold">add_circle</span>
                            <span>Add New Runner</span>
                        </NuxtLink>
                    </div>
                    <div class="rounded-xl border border-[#315f68] bg-[#182f34] overflow-hidden shadow-2xl">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-[#224249]/50 border-b border-[#315f68]">
                                    <th class="px-6 py-4 text-white text-xs font-bold uppercase tracking-wider">Runner Name</th>
                                    <th class="px-6 py-4 text-white text-xs font-bold uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-4 text-white text-xs font-bold uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-4 text-white text-xs font-bold uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-white text-xs font-bold uppercase tracking-wider text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#224249]">
                                <tr v-for="runner in store.runners" :key="runner.id" class="hover:bg-white/5 transition-colors">
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-3">
                                            <div class="size-10 bg-primary/10 rounded-lg flex items-center justify-center border border-primary/20">
                                                <span class="material-symbols-outlined text-primary">terminal</span>
                                            </div>
                                            <span class="text-white font-bold block">{{ runner.name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <p class="text-[#90c1cb] text-sm max-w-md">{{ runner.description }}</p>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-primary/10 text-primary border border-primary/20">{{ runner.type }}</span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span :class="runner.status === 'active' ? 'bg-green-500/10 text-green-400 border-green-500/20' : 'bg-red-500/10 text-red-400 border-red-500/20'" class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border">{{ runner.status }}</span>
                                    </td>
                                    <td class="px-6 py-5 text-right">
                                        <div class="flex justify-end gap-2">
                                            <NuxtLink :to="`/admin/runner/${runner.id}`" class="p-2 text-[#90c1cb] hover:text-ocean-turquoise hover:bg-ocean-turquoise/10 rounded-lg transition-all" title="View Details">
                                                <span class="material-symbols-outlined">visibility</span>
                                            </NuxtLink>
                                            <NuxtLink :to="`/admin/runner/edit/${runner.id}`" class="p-2 text-[#90c1cb] hover:text-pirate-gold hover:bg-pirate-gold/10 rounded-lg transition-all" title="Update Runner">
                                                <span class="material-symbols-outlined">edit</span>
                                            </NuxtLink>
                                            <button @click="openDeleteModal(runner.id)" class="p-2 text-[#90c1cb] hover:text-red-400 hover:bg-red-400/10 rounded-lg transition-all" title="Delete Runner">
                                                <span class="material-symbols-outlined">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <Pagination :meta="store.meta" :per_page="paginate.perPage.value" @change="paginate.fetchData" @perPage="paginate.changePerPage" />
                </div>
            </section>
        </main>
        <div v-else class="w-full">
            <main class="flex h-[calc(100vh-65px)] overflow-hidden">
                <section class="flex-1 overflow-y-auto p-10 custom-scrollbar flex items-center justify-center">
                    <div class="max-w-xl w-full text-center">
                        <div class="relative mb-8 flex justify-center">
                            <div class="w-80 h-48 relative overflow-hidden rounded-2xl sea-horizon border border-[#224249] bg-[#14262b] flex items-center justify-center">
                                <div class="absolute -bottom-4 w-24 h-24 bg-gradient-to-t from-pirate-gold/40 to-transparent rounded-full blur-xl"></div>
                                <div class="absolute bottom-0 w-16 h-8 bg-pirate-gold/20 rounded-t-full border-t border-pirate-gold/30"></div>
                                <div class="absolute bottom-4 left-1/4 w-1/2 h-px bg-[#224249]"></div>
                                <div class="absolute bottom-8 left-1/3 w-1/3 h-px bg-[#224249]/60"></div>
                                <div class="relative flex flex-col gap-3 items-center opacity-30">
                                    <div class="w-16 h-1 bg-[#90c1cb] rounded-full"></div>
                                    <div class="w-20 h-1 bg-[#90c1cb] rounded-full"></div>
                                    <div class="w-24 h-1 bg-[#90c1cb] rounded-full"></div>
                                </div>
                                <span class="material-symbols-outlined text-6xl text-[#224249] absolute top-1/3 opacity-40">terminal</span>
                            </div>
                        </div>
                        <h2 class="text-3xl font-bold text-white font-display mb-3">No Runners Yet</h2>
                        <p class="text-[#90c1cb] text-lg mb-8">No execution runners configured...<br />set up your first runner!</p>
                        <NuxtLink to="/admin/runner/create" class="inline-flex items-center gap-2 px-8 py-4 bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark font-bold rounded-lg transition-all transform hover:scale-105 shadow-[0_4px_20px_0_rgba(255,215,0,0.3)]">
                            <span class="material-symbols-outlined font-bold">add_circle</span>
                            <span>Add New Runner</span>
                        </NuxtLink>
                        <div class="mt-12 flex justify-center gap-8">
                            <div class="flex items-center gap-2 text-[#5a8b95]">
                                <span class="material-symbols-outlined text-sm">info</span>
                                <span class="text-xs uppercase tracking-widest font-bold">Runner Config</span>
                            </div>
                            <div class="flex items-center gap-2 text-[#5a8b95]">
                                <span class="material-symbols-outlined text-sm">history_edu</span>
                                <span class="text-xs uppercase tracking-widest font-bold">Execution Logs</span>
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </div>
        <div id="delete-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-background-dark/80 backdrop-blur-sm px-4 hidden">
            <div class="relative w-full max-w-md overflow-hidden rounded-xl border border-marine-crimson/50 bg-[#182f34] shadow-[0_0_50px_rgba(139,0,0,0.2)]">
                <div class="absolute inset-0 wave-pattern opacity-20 pointer-events-none"></div>
                <div class="absolute inset-0 flex items-center justify-center opacity-[0.03] pointer-events-none">
                    <div class="size-64 jolly-roger-bg bg-white"></div>
                </div>
                <div class="relative p-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="size-12 flex items-center justify-center rounded-full bg-marine-crimson/20 border border-marine-crimson/40 text-marine-crimson">
                            <span class="material-symbols-outlined text-3xl">skull</span>
                        </div>
                        <h3 class="text-2xl font-bold text-white font-display">Delete this Runner?</h3>
                    </div>
                    <div class="mb-8">
                        <p class="text-[#90c1cb] leading-relaxed">By confirming, the selected runner will be <span class="text-primary font-semibold">permanently removed</span>.</p>
                        <div class="mt-4 p-3 rounded-lg bg-marine-crimson/10 border border-marine-crimson/20">
                            <p class="text-red-300 text-sm flex items-start gap-2">
                                <span class="material-symbols-outlined text-sm mt-0.5">warning</span>
                                <span>This runner and its versions will be permanently deleted. This action cannot be undone.</span>
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-col gap-3">
                        <form>
                            <button class="w-full py-3 bg-marine-crimson hover:bg-marine-crimson-dark text-white font-bold rounded-lg transition-all shadow-lg shadow-black/20 flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-lg">delete_forever</span>
                                <span>Delete Runner</span>
                            </button>
                        </form>
                        <button @click="closeDeleteModal()" class="w-full py-3 bg-[#224249] hover:bg-[#315f68] text-white font-medium rounded-lg transition-all border border-[#315f68]">Keep Runner</button>
                    </div>
                </div>
                <div class="h-1.5 w-full flex">
                    <div class="flex-1 bg-marine-crimson"></div>
                    <div class="flex-1 bg-white"></div>
                    <div class="flex-1 bg-marine-crimson"></div>
                </div>
            </div>
        </div>
    </NuxtLayout>
</template>
