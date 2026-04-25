<script setup lang="ts">
    import { useRunnerVersion } from '~~/stores/runner_version';
    import { useRunner } from '~~/stores/runner';
    import type { ReturnData } from '~~/types/api';

    useHead({ title: 'Admin Dashboard - Create Runner Version' })

    const store = useRunnerVersion()
    const runnerStore = useRunner()

    onMounted(async () => {
        await runnerStore.fetchAllRunners();
    })

    const form = reactive({
        runner_id: 0,
        version: '',
        docker_image: '',
        mode: 'http',
        port: 8000,
        healthcheck_path: '/',
        cpu: 1,
        memory_mb: 512,
        timeout_seconds: 15,
        php: false,
        node: false,
        sqlite: false,
        network_enabled: false,
        status: 'active',
        is_default: false
    })

    const errs = ref<Record<string, string>>({})

    const submit = async () => {
        const res: ReturnData<any> = await store.createRunnerVersion(form);
        if (res.success) {
            navigateTo('/admin/runner-version')
        }
        if (res.errors) {
            errs.value = res.errors
        }
    }

    definePageMeta({ middleware: ['auth', 'admin'] })
</script>

<template>
    <NuxtLayout name="admin">
        <main class="w-full flex h-[calc(100vh-65px)] overflow-hidden">
            <section class="flex-1 overflow-y-auto p-10 custom-scrollbar bg-[url('https://www.transparenttextures.com/patterns/dark-matter.png')]">
                <div class="max-w-3xl mx-auto">
                    <div class="mb-10 text-center">
                        <span class="text-primary text-xs font-bold tracking-[0.3em] uppercase mb-2 block">Version Definition</span>
                        <h2 class="text-5xl font-bold text-white font-adventure tracking-wider mb-2">Deploy New Version</h2>
                        <div class="w-24 h-1 bg-pirate-gold mx-auto"></div>
                    </div>
                    <div class="nautical-border bg-[#182f34] p-8 rounded-lg shadow-2xl relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 pointer-events-none">
                            <span class="material-symbols-outlined text-9xl text-white">package_2</span>
                        </div>
                        <form @submit.prevent="submit" class="space-y-8 relative z-10">
                            <!-- Runner & Version -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-pirate-gold text-xs font-bold uppercase tracking-widest mb-2" for="runner-id">Runner</label>
                                    <select v-model="form.runner_id" class="w-full bg-[#101f22] border border-[#224249] text-white rounded-lg px-4 py-3 focus:ring-pirate-gold focus:border-pirate-gold transition-all" id="runner-id">
                                        <option value="0" disabled>Select a runner...</option>
                                        <option v-for="runner in runnerStore.all_runners" :key="runner.id" :value="runner.id">{{ runner.name }}</option>
                                    </select>
                                    <p v-if="errs.runner_id" class="text-red-500 text-sm mt-2">{{ errs.runner_id }}</p>
                                </div>
                                <div>
                                    <label class="block text-pirate-gold text-xs font-bold uppercase tracking-widest mb-2" for="version">Version</label>
                                    <input v-model="form.version" class="w-full bg-[#101f22] border border-[#224249] text-white rounded-lg px-4 py-3 focus:ring-pirate-gold focus:border-pirate-gold placeholder:text-[#4a6d74] transition-all" id="version" placeholder="e.g. 1.0.0" type="text" />
                                    <p v-if="errs.version" class="text-red-500 text-sm mt-2">{{ errs.version }}</p>
                                </div>
                            </div>

                            <!-- Docker Image -->
                            <div>
                                <label class="block text-pirate-gold text-xs font-bold uppercase tracking-widest mb-2" for="docker-image">Docker Image</label>
                                <input v-model="form.docker_image" class="w-full bg-[#101f22] border border-[#224249] text-white rounded-lg px-4 py-3 focus:ring-pirate-gold focus:border-pirate-gold placeholder:text-[#4a6d74] transition-all font-mono" id="docker-image" placeholder="e.g. node:18-alpine" type="text" />
                                <p v-if="errs.docker_image" class="text-red-500 text-sm mt-2">{{ errs.docker_image }}</p>
                            </div>

                            <!-- Execution Config -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-pirate-gold text-xs font-bold uppercase tracking-widest mb-2" for="mode">Execution Mode</label>
                                    <select v-model="form.mode" class="w-full bg-[#101f22] border border-[#224249] text-white rounded-lg px-4 py-3 focus:ring-pirate-gold focus:border-pirate-gold transition-all" id="mode">
                                        <option value="http">HTTP</option>
                                        <option value="browser">Browser</option>
                                        <option value="function">Function</option>
                                    </select>
                                    <p v-if="errs.mode" class="text-red-500 text-sm mt-2">{{ errs.mode }}</p>
                                </div>
                                <div>
                                    <label class="block text-pirate-gold text-xs font-bold uppercase tracking-widest mb-2" for="port">Port</label>
                                    <input v-model="form.port" class="w-full bg-[#101f22] border border-[#224249] text-white rounded-lg px-4 py-3 focus:ring-pirate-gold focus:border-pirate-gold placeholder:text-[#4a6d74] transition-all" id="port" type="number" min="1" max="65535" />
                                    <p v-if="errs.port" class="text-red-500 text-sm mt-2">{{ errs.port }}</p>
                                </div>
                                <div>
                                    <label class="block text-pirate-gold text-xs font-bold uppercase tracking-widest mb-2" for="healthcheck-path">Healthcheck Path</label>
                                    <input v-model="form.healthcheck_path" class="w-full bg-[#101f22] border border-[#224249] text-white rounded-lg px-4 py-3 focus:ring-pirate-gold focus:border-pirate-gold placeholder:text-[#4a6d74] transition-all font-mono" id="healthcheck-path" placeholder="/" type="text" />
                                    <p v-if="errs.healthcheck_path" class="text-red-500 text-sm mt-2">{{ errs.healthcheck_path }}</p>
                                </div>
                            </div>

                            <!-- Resources -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-pirate-gold text-xs font-bold uppercase tracking-widest mb-2" for="cpu">CPU Cores</label>
                                    <input v-model="form.cpu" class="w-full bg-[#101f22] border border-[#224249] text-white rounded-lg px-4 py-3 focus:ring-pirate-gold focus:border-pirate-gold transition-all" id="cpu" type="number" min="0.1" step="0.1" />
                                    <p v-if="errs.cpu" class="text-red-500 text-sm mt-2">{{ errs.cpu }}</p>
                                </div>
                                <div>
                                    <label class="block text-pirate-gold text-xs font-bold uppercase tracking-widest mb-2" for="memory">Memory (MB)</label>
                                    <input v-model="form.memory_mb" class="w-full bg-[#101f22] border border-[#224249] text-white rounded-lg px-4 py-3 focus:ring-pirate-gold focus:border-pirate-gold transition-all" id="memory" type="number" min="16" />
                                    <p v-if="errs.memory_mb" class="text-red-500 text-sm mt-2">{{ errs.memory_mb }}</p>
                                </div>
                                <div>
                                    <label class="block text-pirate-gold text-xs font-bold uppercase tracking-widest mb-2" for="timeout">Timeout (s)</label>
                                    <input v-model="form.timeout_seconds" class="w-full bg-[#101f22] border border-[#224249] text-white rounded-lg px-4 py-3 focus:ring-pirate-gold focus:border-pirate-gold transition-all" id="timeout" type="number" min="1" />
                                    <p v-if="errs.timeout_seconds" class="text-red-500 text-sm mt-2">{{ errs.timeout_seconds }}</p>
                                </div>
                            </div>

                            <!-- Features & Status -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-pirate-gold text-xs font-bold uppercase tracking-widest mb-4">Features</label>
                                    <div class="space-y-3">
                                        <label class="flex items-center gap-3 cursor-pointer">
                                            <input v-model="form.php" type="checkbox" class="w-5 h-5 bg-[#101f22] border-[#224249] rounded text-pirate-gold focus:ring-pirate-gold" />
                                            <span class="text-white text-sm font-medium">PHP Support</span>
                                        </label>
                                        <label class="flex items-center gap-3 cursor-pointer">
                                            <input v-model="form.node" type="checkbox" class="w-5 h-5 bg-[#101f22] border-[#224249] rounded text-pirate-gold focus:ring-pirate-gold" />
                                            <span class="text-white text-sm font-medium">Node.js Support</span>
                                        </label>
                                        <label class="flex items-center gap-3 cursor-pointer">
                                            <input v-model="form.sqlite" type="checkbox" class="w-5 h-5 bg-[#101f22] border-[#224249] rounded text-pirate-gold focus:ring-pirate-gold" />
                                            <span class="text-white text-sm font-medium">SQLite Support</span>
                                        </label>
                                        <label class="flex items-center gap-3 cursor-pointer">
                                            <input v-model="form.network_enabled" type="checkbox" class="w-5 h-5 bg-[#101f22] border-[#224249] rounded text-pirate-gold focus:ring-pirate-gold" />
                                            <span class="text-white text-sm font-medium">Network Enabled</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="space-y-6">
                                    <div>
                                        <label class="block text-pirate-gold text-xs font-bold uppercase tracking-widest mb-2" for="status">Status</label>
                                        <select v-model="form.status" class="w-full bg-[#101f22] border border-[#224249] text-white rounded-lg px-4 py-3 focus:ring-pirate-gold focus:border-pirate-gold transition-all" id="status">
                                            <option value="active">Active</option>
                                            <option value="deprecated">Deprecated</option>
                                            <option value="disabled">Disabled</option>
                                        </select>
                                        <p v-if="errs.status" class="text-red-500 text-sm mt-2">{{ errs.status }}</p>
                                    </div>
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input v-model="form.is_default" type="checkbox" class="w-5 h-5 bg-[#101f22] border-[#224249] rounded text-pirate-gold focus:ring-pirate-gold" />
                                        <span class="text-pirate-gold text-xs font-bold uppercase tracking-widest">Set as Default Version</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-between pt-6 border-t border-[#224249]">
                                <NuxtLink to="/admin/runner-version" class="flex items-center gap-2 px-6 py-3 bg-dark-red hover:bg-red-800 text-white font-bold rounded-lg transition-all transform hover:scale-105 shadow-lg" type="button">
                                    <span class="material-symbols-outlined font-bold">close</span>
                                    <span>Abandon</span>
                                </NuxtLink>
                                <div class="flex gap-4">
                                    <button class="flex items-center gap-2 px-10 py-3 bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark font-black rounded-lg transition-all transform hover:scale-105 shadow-[0_4px_20px_0_rgba(255,215,0,0.4)] uppercase tracking-tighter" type="submit">
                                        <span class="material-symbols-outlined font-bold">verified</span>
                                        <span>Deploy Version</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="mt-12 grid grid-cols-3 gap-6">
                        <div class="text-center p-4 border border-[#224249] rounded-lg bg-[#182f34]/30">
                            <span class="material-symbols-outlined text-primary mb-2">docker</span>
                            <h4 class="text-white text-xs font-bold uppercase">Container Config</h4>
                            <p class="text-[#90c1cb] text-[10px] mt-1">Define Docker runtime settings</p>
                        </div>
                        <div class="text-center p-4 border border-[#224249] rounded-lg bg-[#182f34]/30">
                            <span class="material-symbols-outlined text-primary mb-2">memory</span>
                            <h4 class="text-white text-xs font-bold uppercase">Resource Limits</h4>
                            <p class="text-[#90c1cb] text-[10px] mt-1">Set CPU, memory, and timeout boundaries</p>
                        </div>
                        <div class="text-center p-4 border border-[#224249] rounded-lg bg-[#182f34]/30">
                            <span class="material-symbols-outlined text-primary mb-2">rocket_launch</span>
                            <h4 class="text-white text-xs font-bold uppercase">Deploy</h4>
                            <p class="text-[#90c1cb] text-[10px] mt-1">Version will be available for stacks</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </NuxtLayout>
</template>
