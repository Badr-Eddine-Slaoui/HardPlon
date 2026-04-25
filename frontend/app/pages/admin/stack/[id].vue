<script setup lang="ts">
    import { useStack } from '~~/stores/stack';

    useHead({ title: 'Admin Dashboard - Stack Details' })

    const id: number = parseInt(useRoute().params?.id as string)
    const store = useStack()

    onMounted(async () => {
        await store.fetchStack(id);
    })

    definePageMeta({ middleware: ['auth', 'admin'] })
</script>

<template>
    <NuxtLayout name="admin">
        <main class="w-full flex h-[calc(100vh-65px)] overflow-hidden">
            <section class="flex-1 overflow-y-auto p-10 custom-scrollbar bg-[url('https://www.transparenttextures.com/patterns/dark-matter.png')]">
                <div class="max-w-4xl mx-auto" v-if="store.stack">
                    <!-- Header -->
                    <div class="flex items-center gap-4 mb-8">
                        <NuxtLink to="/admin/stack" class="p-2 text-[#90c1cb] hover:text-white hover:bg-white/10 rounded-lg transition-all">
                            <span class="material-symbols-outlined">arrow_back</span>
                        </NuxtLink>
                        <div>
                            <h2 class="text-4xl font-bold text-white font-adventure tracking-wider">{{ store.stack.name }}</h2>
                            <p class="text-[#90c1cb]">Stack configuration and assigned runners</p>
                        </div>
                    </div>

                    <!-- Stack Info -->
                    <div class="nautical-border bg-[#182f34] p-8 rounded-lg shadow-2xl mb-8 relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-5 pointer-events-none">
                            <span class="material-symbols-outlined text-9xl text-white">layers</span>
                        </div>
                        <div class="relative z-10">
                            <h3 class="text-pirate-gold text-xs font-bold uppercase tracking-[0.3em] mb-6 flex items-center gap-2">
                                <span class="w-2 h-2 bg-pirate-gold rounded-full"></span>
                                Stack Registry Information
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-6">
                                    <div class="flex flex-col">
                                        <span class="text-[#4a6d74] text-[10px] font-bold uppercase tracking-widest mb-1">Stack Name</span>
                                        <span class="text-white text-lg font-bold">{{ store.stack.name }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[#4a6d74] text-[10px] font-bold uppercase tracking-widest mb-1">Identifier</span>
                                        <span class="text-primary font-mono text-sm">#{{ store.stack.id }}</span>
                                    </div>
                                </div>
                                <div class="space-y-6">
                                    <div class="flex flex-col">
                                        <span class="text-[#4a6d74] text-[10px] font-bold uppercase tracking-widest mb-1">Created At</span>
                                        <span class="text-white font-medium">{{ new Date(store.stack.created_at).toLocaleDateString() }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[#4a6d74] text-[10px] font-bold uppercase tracking-widest mb-1">Last Update</span>
                                        <span class="text-white font-medium">{{ new Date(store.stack.updated_at).toLocaleDateString() }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-8 pt-8 border-t border-[#224249]">
                                <span class="text-[#4a6d74] text-[10px] font-bold uppercase tracking-widest mb-2 block">Description</span>
                                <p class="text-[#90c1cb] leading-relaxed italic">{{ store.stack.description || 'No description provided for this stack.' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Runners Table -->
                    <div class="nautical-border bg-[#182f34] overflow-hidden rounded-lg shadow-2xl relative">
                        <div class="px-8 py-5 bg-[#224249]/50 border-b border-[#315f68] flex items-center justify-between">
                            <h3 class="text-pirate-gold text-xs font-bold uppercase tracking-[0.3em] flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm font-bold text-primary">terminal</span>
                                Assigned Runners ({{ store.stack.stack_runners?.length || 0 }})
                            </h3>
                            <NuxtLink :to="`/admin/stack/edit/${store.stack.id}`" class="text-xs font-bold text-primary hover:text-white transition-colors uppercase tracking-widest flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">edit</span>
                                Adjust configuration
                            </NuxtLink>
                        </div>
                        <div class="overflow-x-auto">
                            <table v-if="store.stack.stack_runners?.length" class="w-full text-left">
                                <thead>
                                    <tr class="bg-[#224249]/30 border-b border-[#224249]">
                                        <th class="px-8 py-4 text-[#4a6d74] text-[10px] font-bold uppercase tracking-widest">Runner</th>
                                        <th class="px-8 py-4 text-[#4a6d74] text-[10px] font-bold uppercase tracking-widest">Version Info</th>
                                        <th class="px-8 py-4 text-[#4a6d74] text-[10px] font-bold uppercase tracking-widest text-center">Priority</th>
                                        <th class="px-8 py-4 text-[#4a6d74] text-[10px] font-bold uppercase tracking-widest text-right">Registry</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#224249]">
                                    <tr v-for="sr in store.stack.stack_runners" :key="sr.id" class="hover:bg-white/5 transition-colors group">
                                        <td class="px-8 py-5">
                                            <div class="flex items-center gap-3">
                                                <div class="size-10 bg-primary/10 rounded-lg flex items-center justify-center border border-primary/20 group-hover:border-primary/40 transition-all">
                                                    <span class="material-symbols-outlined text-primary">terminal</span>
                                                </div>
                                                <div>
                                                    <span class="text-white font-bold block">{{ sr.runner_version?.runner?.name || 'Unknown' }}</span>
                                                    <span class="text-[10px] text-primary uppercase font-bold tracking-widest">{{ sr.runner_version?.runner?.type }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5">
                                            <div class="flex flex-col">
                                                <span class="text-white text-sm font-bold">v{{ sr.runner_version?.version }}</span>
                                                <span class="text-[#4a6d74] text-[10px] font-mono">{{ sr.runner_version?.docker_image }}</span>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 text-center">
                                            <span class="inline-flex items-center justify-center size-8 rounded-full bg-[#101f22] border border-[#224249] text-pirate-gold font-bold text-sm">
                                                {{ sr.priority }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-5 text-right">
                                            <NuxtLink :to="`/admin/runner/${sr.runner_version?.runner_id}`" class="text-xs font-bold text-[#90c1cb] hover:text-primary transition-colors flex items-center justify-end gap-1">
                                                <span>View Runner</span>
                                                <span class="material-symbols-outlined text-sm">open_in_new</span>
                                            </NuxtLink>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div v-else class="p-16 text-center">
                                <span class="material-symbols-outlined text-6xl text-[#224249] mb-4 block">construction</span>
                                <p class="text-[#90c1cb] italic">No technology runners are currently forged into this stack.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </NuxtLayout>
</template>
