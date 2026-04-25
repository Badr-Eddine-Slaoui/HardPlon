<script setup lang="ts">
    import { useRunnerVersion } from '~~/stores/runner_version';

    useHead({ title: 'Admin Dashboard - Runner Version Details' })

    const id: number = parseInt(useRoute().params?.id as string)
    const store = useRunnerVersion()

    onMounted(async () => {
        await store.fetchRunnerVersion(id);
    })

    definePageMeta({ middleware: ['auth', 'admin'] })
</script>

<template>
    <NuxtLayout name="admin">
        <main class="w-full flex h-[calc(100vh-65px)] overflow-hidden">
            <section class="flex-1 overflow-y-auto p-10 custom-scrollbar">
                <div class="max-w-4xl mx-auto" v-if="store.runner_version">
                    <!-- Header -->
                    <div class="flex items-center gap-4 mb-8">
                        <NuxtLink to="/admin/runner-version" class="p-2 text-[#90c1cb] hover:text-white hover:bg-white/10 rounded-lg transition-all">
                            <span class="material-symbols-outlined">arrow_back</span>
                        </NuxtLink>
                        <div>
                            <h2 class="text-3xl font-bold text-white font-display">Version {{ store.runner_version.version }}</h2>
                            <p class="text-[#90c1cb]">Runner version configuration details</p>
                        </div>
                        <div class="ml-auto flex gap-2">
                            <span :class="store.runner_version.status === 'active' ? 'bg-green-500/10 text-green-400 border-green-500/20' : store.runner_version.status === 'deprecated' ? 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20' : 'bg-red-500/10 text-red-400 border-red-500/20'" class="px-4 py-2 rounded-full text-xs font-bold uppercase tracking-wider border">{{ store.runner_version.status }}</span>
                            <span v-if="store.runner_version.is_default" class="px-4 py-2 rounded-full text-xs font-bold uppercase tracking-wider bg-pirate-gold/10 text-pirate-gold border border-pirate-gold/20">Default</span>
                        </div>
                    </div>

                    <!-- Info Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="rounded-xl border border-[#315f68] bg-[#182f34] p-6 shadow-xl">
                            <h3 class="text-pirate-gold text-xs font-bold uppercase tracking-widest mb-4">General Info</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-[#90c1cb] text-sm">Docker Image</span>
                                    <span class="text-white font-mono text-sm bg-[#101f22] px-3 py-1 rounded-lg">{{ store.runner_version.docker_image }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-[#90c1cb] text-sm">Version</span>
                                    <span class="text-white font-bold">{{ store.runner_version.version }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-[#90c1cb] text-sm">Created</span>
                                    <span class="text-white text-sm">{{ new Date(store.runner_version.created_at).toLocaleDateString() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="rounded-xl border border-[#315f68] bg-[#182f34] p-6 shadow-xl">
                            <h3 class="text-pirate-gold text-xs font-bold uppercase tracking-widest mb-4">Resources</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-[#90c1cb] text-sm">CPU</span>
                                    <span class="text-white font-bold">{{ store.runner_version.default_config?.resources?.cpu || '—' }} cores</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-[#90c1cb] text-sm">Memory</span>
                                    <span class="text-white font-bold">{{ store.runner_version.default_config?.resources?.memory_mb || '—' }} MB</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-[#90c1cb] text-sm">Timeout</span>
                                    <span class="text-white font-bold">{{ store.runner_version.default_config?.resources?.timeout_seconds || '—' }}s</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Execution Config -->
                    <div class="rounded-xl border border-[#315f68] bg-[#182f34] p-6 shadow-xl mb-8">
                        <h3 class="text-pirate-gold text-xs font-bold uppercase tracking-widest mb-4">Execution Configuration</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="flex items-center gap-3">
                                <div class="size-10 bg-primary/10 rounded-lg flex items-center justify-center border border-primary/20">
                                    <span class="material-symbols-outlined text-primary">settings</span>
                                </div>
                                <div>
                                    <p class="text-[#90c1cb] text-xs uppercase tracking-wider">Mode</p>
                                    <p class="text-white font-bold">{{ store.runner_version.default_config?.execution?.mode || '—' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="size-10 bg-primary/10 rounded-lg flex items-center justify-center border border-primary/20">
                                    <span class="material-symbols-outlined text-primary">lan</span>
                                </div>
                                <div>
                                    <p class="text-[#90c1cb] text-xs uppercase tracking-wider">Port</p>
                                    <p class="text-white font-bold">{{ store.runner_version.default_config?.execution?.port || '—' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="size-10 bg-primary/10 rounded-lg flex items-center justify-center border border-primary/20">
                                    <span class="material-symbols-outlined text-primary">monitor_heart</span>
                                </div>
                                <div>
                                    <p class="text-[#90c1cb] text-xs uppercase tracking-wider">Healthcheck</p>
                                    <p class="text-white font-bold font-mono">{{ store.runner_version.default_config?.execution?.healthcheck?.path || '—' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Features -->
                    <div class="rounded-xl border border-[#315f68] bg-[#182f34] p-6 shadow-xl mb-8">
                        <h3 class="text-pirate-gold text-xs font-bold uppercase tracking-widest mb-4">Features</h3>
                        <div class="flex flex-wrap gap-3">
                            <span :class="store.runner_version.default_config?.features?.php ? 'bg-green-500/10 text-green-400 border-green-500/20' : 'bg-[#224249] text-[#5a8b95] border-[#315f68]'" class="px-4 py-2 rounded-lg text-sm font-bold border flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">{{ store.runner_version.default_config?.features?.php ? 'check_circle' : 'cancel' }}</span>PHP
                            </span>
                            <span :class="store.runner_version.default_config?.features?.node ? 'bg-green-500/10 text-green-400 border-green-500/20' : 'bg-[#224249] text-[#5a8b95] border-[#315f68]'" class="px-4 py-2 rounded-lg text-sm font-bold border flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">{{ store.runner_version.default_config?.features?.node ? 'check_circle' : 'cancel' }}</span>Node.js
                            </span>
                            <span :class="store.runner_version.default_config?.features?.sqlite ? 'bg-green-500/10 text-green-400 border-green-500/20' : 'bg-[#224249] text-[#5a8b95] border-[#315f68]'" class="px-4 py-2 rounded-lg text-sm font-bold border flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">{{ store.runner_version.default_config?.features?.sqlite ? 'check_circle' : 'cancel' }}</span>SQLite
                            </span>
                            <span :class="store.runner_version.default_config?.network?.enabled ? 'bg-green-500/10 text-green-400 border-green-500/20' : 'bg-[#224249] text-[#5a8b95] border-[#315f68]'" class="px-4 py-2 rounded-lg text-sm font-bold border flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">{{ store.runner_version.default_config?.network?.enabled ? 'check_circle' : 'cancel' }}</span>Network
                            </span>
                        </div>
                    </div>

                    <!-- Runner Info -->
                    <div v-if="store.runner_version.runner" class="rounded-xl border border-[#315f68] bg-[#182f34] p-6 shadow-xl">
                        <h3 class="text-pirate-gold text-xs font-bold uppercase tracking-widest mb-4">Parent Runner</h3>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="size-12 bg-primary/10 rounded-lg flex items-center justify-center border border-primary/20">
                                    <span class="material-symbols-outlined text-primary text-2xl">terminal</span>
                                </div>
                                <div>
                                    <p class="text-white font-bold text-lg">{{ store.runner_version.runner.name }}</p>
                                    <p class="text-[#90c1cb] text-sm">{{ store.runner_version.runner.description }}</p>
                                </div>
                            </div>
                            <NuxtLink :to="`/admin/runner/${store.runner_version.runner.id}`" class="flex items-center gap-2 px-4 py-2 bg-primary/10 text-primary hover:bg-primary hover:text-background-dark rounded-lg transition-all border border-primary/20 font-bold text-sm">
                                <span class="material-symbols-outlined text-sm">visibility</span>
                                View Runner
                            </NuxtLink>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </NuxtLayout>
</template>
