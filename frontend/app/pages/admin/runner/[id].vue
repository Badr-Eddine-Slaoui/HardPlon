<script setup lang="ts">
    import { useRunner } from '~~/stores/runner';

    useHead({ title: 'Admin Dashboard - Runner Details' })

    const id: number = parseInt(useRoute().params?.id as string)
    const store = useRunner()

    onMounted(async () => {
        await store.fetchRunner(id);
    })

    definePageMeta({ middleware: ['auth', 'admin'] })
</script>

<template>
    <NuxtLayout name="admin">
        <main class="w-full flex h-[calc(100vh-65px)] overflow-hidden">
            <section class="flex-1 overflow-y-auto p-10 custom-scrollbar">
                <div class="max-w-4xl mx-auto" v-if="store.runner">
                    <!-- Header -->
                    <div class="flex items-center gap-4 mb-8">
                        <NuxtLink to="/admin/runner" class="p-2 text-[#90c1cb] hover:text-white hover:bg-white/10 rounded-lg transition-all">
                            <span class="material-symbols-outlined">arrow_back</span>
                        </NuxtLink>
                        <div>
                            <h2 class="text-3xl font-bold text-white font-display">{{ store.runner.name }}</h2>
                            <p class="text-[#90c1cb]">Runner configuration and version history</p>
                        </div>
                        <div class="ml-auto flex gap-2">
                            <span :class="store.runner.status === 'active' ? 'bg-green-500/10 text-green-400 border-green-500/20' : 'bg-red-500/10 text-red-400 border-red-500/20'" class="px-4 py-2 rounded-full text-xs font-bold uppercase tracking-wider border">{{ store.runner.status }}</span>
                            <span class="px-4 py-2 rounded-full text-xs font-bold uppercase tracking-wider bg-primary/10 text-primary border border-primary/20">{{ store.runner.type }}</span>
                        </div>
                    </div>

                    <!-- Runner Info -->
                    <div class="rounded-xl border border-[#315f68] bg-[#182f34] p-6 shadow-xl mb-8">
                        <h3 class="text-pirate-gold text-xs font-bold uppercase tracking-widest mb-4">Runner Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-[#90c1cb] text-sm">Name</span>
                                    <span class="text-white font-bold">{{ store.runner.name }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-[#90c1cb] text-sm">Type</span>
                                    <span class="text-white font-bold">{{ store.runner.type }}</span>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-[#90c1cb] text-sm">Status</span>
                                    <span :class="store.runner.status === 'active' ? 'text-green-400' : 'text-red-400'" class="font-bold capitalize">{{ store.runner.status }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-[#90c1cb] text-sm">Created</span>
                                    <span class="text-white text-sm">{{ new Date(store.runner.created_at).toLocaleDateString() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-[#224249]">
                            <span class="text-[#90c1cb] text-sm">Description</span>
                            <p class="text-white mt-1">{{ store.runner.description }}</p>
                        </div>
                    </div>

                    <!-- Versions Table -->
                    <div class="rounded-xl border border-[#315f68] bg-[#182f34] overflow-hidden shadow-xl">
                        <div class="px-6 py-4 bg-[#224249]/50 border-b border-[#315f68] flex items-center justify-between">
                            <h3 class="text-pirate-gold text-xs font-bold uppercase tracking-widest">Versions ({{ store.runner.versions?.length || 0 }})</h3>
                            <NuxtLink to="/admin/runner-version/create" class="flex items-center gap-1 text-xs text-primary hover:text-pirate-gold transition-colors font-bold">
                                <span class="material-symbols-outlined text-sm">add_circle</span>
                                Add Version
                            </NuxtLink>
                        </div>
                        <table v-if="store.runner.versions?.length" class="w-full text-left">
                            <thead>
                                <tr class="bg-[#224249]/30 border-b border-[#224249]">
                                    <th class="px-6 py-3 text-white text-xs font-bold uppercase tracking-wider">Version</th>
                                    <th class="px-6 py-3 text-white text-xs font-bold uppercase tracking-wider">Docker Image</th>
                                    <th class="px-6 py-3 text-white text-xs font-bold uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-white text-xs font-bold uppercase tracking-wider">Default</th>
                                    <th class="px-6 py-3 text-white text-xs font-bold uppercase tracking-wider text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#224249]">
                                <tr v-for="ver in store.runner.versions" :key="ver.id" class="hover:bg-white/5 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="size-8 bg-primary/10 rounded-lg flex items-center justify-center border border-primary/20">
                                                <span class="material-symbols-outlined text-primary text-sm">package_2</span>
                                            </div>
                                            <span class="text-white font-bold">{{ ver.version }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-[#90c1cb] text-sm font-mono">{{ ver.docker_image }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span :class="ver.status === 'active' ? 'bg-green-500/10 text-green-400 border-green-500/20' : ver.status === 'deprecated' ? 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20' : 'bg-red-500/10 text-red-400 border-red-500/20'" class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border">{{ ver.status }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span v-if="ver.is_default" class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-pirate-gold/10 text-pirate-gold border border-pirate-gold/20">Default</span>
                                        <span v-else class="text-[#90c1cb] text-sm">—</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <NuxtLink :to="`/admin/runner-version/${ver.id}`" class="p-2 text-[#90c1cb] hover:text-ocean-turquoise hover:bg-ocean-turquoise/10 rounded-lg transition-all inline-block" title="View Version Details">
                                            <span class="material-symbols-outlined">visibility</span>
                                        </NuxtLink>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-else class="p-8 text-center">
                            <span class="material-symbols-outlined text-4xl text-[#224249] mb-2">package_2</span>
                            <p class="text-[#90c1cb]">No versions configured for this runner yet.</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </NuxtLayout>
</template>
