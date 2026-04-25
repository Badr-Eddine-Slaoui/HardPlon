<script setup lang="ts">
    import { useRunnerVersion } from '../../../../stores/runner_version';
    import { useRunner } from '../../../../stores/runner';

    useHead({ title: `Admin Dashboard - Runner Versions` })

    const store = useRunnerVersion();
    const runnerStore = useRunner();
    const paginate = usePagination(store.fetchRunnerVersions.bind(store));

    onMounted(async() => {
        await paginate.fetchData();
        await runnerStore.fetchRunners();
    })

    function openDeleteModal(id: number): void {
        const modal = document.getElementById('delete-modal') as HTMLElement;
        modal.classList.remove('hidden');
        const form = modal.querySelector('form') as HTMLFormElement;
        form.onsubmit = async (e) => {
            e.preventDefault();
            await store.deleteRunnerVersion(id);
            closeDeleteModal();
        }
    }

    function closeDeleteModal(): void {
        const modal = document.getElementById('delete-modal') as HTMLElement;
        modal.classList.add('hidden');
    }

    function getRunnerName(runnerId: number): string {
        const runner = runnerStore.runners?.find(r => r.id === runnerId);
        return runner?.name || 'Unknown';
    }

    definePageMeta({ middleware: ['auth', 'admin'] })
</script>

<template>
    <NuxtLayout name="admin">
        <main v-if="store.runner_versions?.length as number > 0" class="flex h-[calc(100vh-65px)] overflow-hidden w-full">
            <section class="flex-1 overflow-y-auto p-10 custom-scrollbar">
                <div class="max-w-7xl mx-auto">
                    <div class="flex justify-between items-end mb-8">
                        <div>
                            <h2 class="text-3xl font-bold text-white font-display mb-1">Runner Versions</h2>
                            <p class="text-[#90c1cb]">Manage versioned configurations for execution runners.</p>
                        </div>
                        <NuxtLink to="/admin/runner-version/create" class="flex items-center gap-2 px-6 py-3 bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark font-bold rounded-lg transition-all transform hover:scale-105 shadow-[0_4px_14px_0_rgba(255,215,0,0.39)]">
                            <span class="material-symbols-outlined font-bold">add_circle</span>
                            <span>Add New Version</span>
                        </NuxtLink>
                    </div>
                    <div class="rounded-xl border border-[#315f68] bg-[#182f34] overflow-hidden shadow-2xl">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-[#224249]/50 border-b border-[#315f68]">
                                    <th class="px-6 py-4 text-white text-xs font-bold uppercase tracking-wider">Version</th>
                                    <th class="px-6 py-4 text-white text-xs font-bold uppercase tracking-wider">Runner</th>
                                    <th class="px-6 py-4 text-white text-xs font-bold uppercase tracking-wider">Docker Image</th>
                                    <th class="px-6 py-4 text-white text-xs font-bold uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-white text-xs font-bold uppercase tracking-wider">Default</th>
                                    <th class="px-6 py-4 text-white text-xs font-bold uppercase tracking-wider text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#224249]">
                                <tr v-for="rv in store.runner_versions" :key="rv.id" class="hover:bg-white/5 transition-colors">
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-3">
                                            <div class="size-10 bg-primary/10 rounded-lg flex items-center justify-center border border-primary/20">
                                                <span class="material-symbols-outlined text-primary">package_2</span>
                                            </div>
                                            <span class="text-white font-bold block">{{ rv.version }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-primary/10 text-primary border border-primary/20">{{ getRunnerName(rv.runner_id) }}</span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <p class="text-[#90c1cb] text-sm max-w-md font-mono">{{ rv.docker_image }}</p>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span :class="rv.status === 'active' ? 'bg-green-500/10 text-green-400 border-green-500/20' : rv.status === 'deprecated' ? 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20' : 'bg-red-500/10 text-red-400 border-red-500/20'" class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border">{{ rv.status }}</span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span v-if="rv.is_default" class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-pirate-gold/10 text-pirate-gold border border-pirate-gold/20">Default</span>
                                        <span v-else class="text-[#90c1cb] text-sm">—</span>
                                    </td>
                                    <td class="px-6 py-5 text-right">
                                        <div class="flex justify-end gap-2">
                                            <NuxtLink :to="`/admin/runner-version/${rv.id}`" class="p-2 text-[#90c1cb] hover:text-ocean-turquoise hover:bg-ocean-turquoise/10 rounded-lg transition-all" title="View Details">
                                                <span class="material-symbols-outlined">visibility</span>
                                            </NuxtLink>
                                            <button @click="openDeleteModal(rv.id)" class="p-2 text-[#90c1cb] hover:text-red-400 hover:bg-red-400/10 rounded-lg transition-all" title="Delete Version">
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
                                <span class="material-symbols-outlined text-6xl text-[#224249] absolute top-1/3 opacity-40">package_2</span>
                            </div>
                        </div>
                        <h2 class="text-3xl font-bold text-white font-display mb-3">No Runner Versions Yet</h2>
                        <p class="text-[#90c1cb] text-lg mb-8">No runner versions configured...<br />set up your first version!</p>
                        <NuxtLink to="/admin/runner-version/create" class="inline-flex items-center gap-2 px-8 py-4 bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark font-bold rounded-lg transition-all transform hover:scale-105 shadow-[0_4px_20px_0_rgba(255,215,0,0.3)]">
                            <span class="material-symbols-outlined font-bold">add_circle</span>
                            <span>Add New Version</span>
                        </NuxtLink>
                    </div>
                </section>
            </main>
        </div>
        <div id="delete-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-background-dark/80 backdrop-blur-sm px-4 hidden">
            <div class="relative w-full max-w-md overflow-hidden rounded-xl border border-marine-crimson/50 bg-[#182f34] shadow-[0_0_50px_rgba(139,0,0,0.2)]">
                <div class="absolute inset-0 wave-pattern opacity-20 pointer-events-none"></div>
                <div class="relative p-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="size-12 flex items-center justify-center rounded-full bg-marine-crimson/20 border border-marine-crimson/40 text-marine-crimson">
                            <span class="material-symbols-outlined text-3xl">skull</span>
                        </div>
                        <h3 class="text-2xl font-bold text-white font-display">Delete this Version?</h3>
                    </div>
                    <div class="mb-8">
                        <p class="text-[#90c1cb] leading-relaxed">By confirming, the selected runner version will be <span class="text-primary font-semibold">permanently removed</span>.</p>
                        <div class="mt-4 p-3 rounded-lg bg-marine-crimson/10 border border-marine-crimson/20">
                            <p class="text-red-300 text-sm flex items-start gap-2">
                                <span class="material-symbols-outlined text-sm mt-0.5">warning</span>
                                <span>This runner version and its stack assignments will be permanently deleted.</span>
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-col gap-3">
                        <form>
                            <button class="w-full py-3 bg-marine-crimson hover:bg-marine-crimson-dark text-white font-bold rounded-lg transition-all shadow-lg shadow-black/20 flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-lg">delete_forever</span>
                                <span>Delete Version</span>
                            </button>
                        </form>
                        <button @click="closeDeleteModal()" class="w-full py-3 bg-[#224249] hover:bg-[#315f68] text-white font-medium rounded-lg transition-all border border-[#315f68]">Keep Version</button>
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
