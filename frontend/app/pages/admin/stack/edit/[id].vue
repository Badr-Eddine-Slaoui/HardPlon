<script setup lang="ts">
    import { useStack } from '~~/stores/stack';
    import { useRunnerVersion } from '~~/stores/runner_version';
    import type { ReturnData } from '~~/types/api';

    useHead({
        title: 'Admin Dashboard - Edit Stack'
    })

    const id: number = parseInt(useRoute().params?.id as string)
    const stackStore = useStack()
    const runnerVersionStore = useRunnerVersion()

    const form = reactive({
        name: '',
        description: '',
        runner_versions: [] as { runner_version_id: number, priority: number }[]
    })

    const errs = ref<Record<string, any>>({
        name: '',
        description: '',
        runner_versions: '',
        message: ''
    })

    onMounted(async () => {
        await runnerVersionStore.fetchAllRunnerVersions()
        await stackStore.fetchStack(id)
        
        if (stackStore.stack) {
            form.name = stackStore.stack.name
            form.description = stackStore.stack.description || ''
            form.runner_versions = stackStore.stack.stack_runners?.map(sr => ({
                runner_version_id: sr.runner_version_id,
                priority: sr.priority
            })) || []
        }
    })

    const addRunner = () => {
        form.runner_versions.push({ runner_version_id: 0, priority: form.runner_versions.length + 1 })
    }

    const removeRunner = (index: number) => {
        form.runner_versions.splice(index, 1)
    }

    const submit = async () => {
        const res : ReturnData<any> = await stackStore.updateStack(id, form);
        if(res.success) {
            navigateTo('/admin/stack')
        }
        
        if(res.errors) {
            errs.value = res.errors
        }
    }

    definePageMeta({
        middleware: ['auth', 'admin']
    })
</script>

<template>
    <NuxtLayout name="admin">
        <main class="w-full flex h-[calc(100vh-65px)] overflow-hidden">
            <section class="flex-1 overflow-y-auto p-10 custom-scrollbar bg-[url('https://www.transparenttextures.com/patterns/dark-matter.png')]">
                <div class="max-w-4xl mx-auto">
                    <div class="mb-10 text-center">
                        <span class="text-primary text-xs font-bold tracking-[0.3em] uppercase mb-2 block">Stack Edition</span>
                        <h2 class="text-5xl font-bold text-white font-adventure tracking-wider mb-2">Reforge Stack</h2>
                        <div class="w-24 h-1 bg-pirate-gold mx-auto"></div>
                    </div>
                    <div class="nautical-border bg-[#182f34] p-8 rounded-lg shadow-2xl relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 pointer-events-none">
                            <span class="material-symbols-outlined text-9xl text-white">layers</span>
                        </div>
                        <form @submit.prevent="submit" class="space-y-8 relative z-10">
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label class="block text-pirate-gold text-xs font-bold uppercase tracking-widest mb-2" for="stack-name">Stack Name</label>
                                    <input v-model="form.name" class="w-full bg-[#101f22] border border-[#224249] text-white rounded-lg px-4 py-3 focus:ring-pirate-gold focus:border-pirate-gold placeholder:text-[#4a6d74] transition-all" id="stack-name" placeholder="e.g. Modern Web Stack" type="text" />
                                    <p v-if="errs.name" class="text-red-500 text-sm mt-2">{{ errs.name }}</p>
                                </div>
                                <div>
                                    <label class="block text-pirate-gold text-xs font-bold uppercase tracking-widest mb-2" for="stack-description">Description</label>
                                    <textarea v-model="form.description" class="w-full bg-[#101f22] border border-[#224249] text-white rounded-lg px-4 py-3 focus:ring-pirate-gold focus:border-pirate-gold placeholder:text-[#4a6d74] transition-all custom-scrollbar" id="stack-description" placeholder="Describe the stack purpose..." rows="4"></textarea>
                                    <p v-if="errs.description" class="text-red-500 text-sm mt-2">{{ errs.description }}</p>
                                </div>
                            </div>

                            <!-- Runner Selection -->
                            <div class="pt-6 border-t border-[#224249]">
                                <div class="flex items-center justify-between mb-6">
                                    <h3 class="text-white font-display text-xl flex items-center gap-2">
                                        <span class="material-symbols-outlined text-primary">terminal</span>
                                        <span>Assign Runners</span>
                                    </h3>
                                    <button @click="addRunner" type="button" class="flex items-center gap-2 px-4 py-2 bg-primary/10 hover:bg-primary/20 text-primary border border-primary/30 rounded-lg transition-all text-xs font-bold uppercase tracking-widest">
                                        <span class="material-symbols-outlined text-sm font-bold">add</span>
                                        <span>Add Runner</span>
                                    </button>
                                </div>
                                
                                <div class="space-y-4">
                                    <div v-for="(rv, index) in form.runner_versions" :key="index" class="flex flex-wrap md:flex-nowrap gap-4 items-end bg-[#101f22]/50 p-5 rounded-lg border border-[#224249] group hover:border-primary/30 transition-all">
                                        <div class="flex-1 min-w-[200px]">
                                            <label class="block text-[#4a6d74] text-[10px] font-bold uppercase tracking-widest mb-2">Runner Version</label>
                                            <select v-model="rv.runner_version_id" class="w-full bg-[#101f22] border border-[#224249] text-white rounded-lg px-3 py-3 text-sm focus:ring-pirate-gold focus:border-pirate-gold transition-all">
                                                <option :value="0" disabled>Select Version...</option>
                                                <option v-for="version in runnerVersionStore.all_runner_versions" :key="version.id" :value="version.id">
                                                    {{ version.runner?.name }} (v{{ version.version }}) - {{ version.docker_image }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="w-full md:w-32">
                                            <label class="block text-[#4a6d74] text-[10px] font-bold uppercase tracking-widest mb-2">Priority</label>
                                            <div class="relative">
                                                <input v-model="rv.priority" type="number" class="w-full bg-[#101f22] border border-[#224249] text-white rounded-lg px-3 py-3 text-sm focus:ring-pirate-gold focus:border-pirate-gold transition-all" />
                                            </div>
                                        </div>
                                        <button @click="removeRunner(index)" type="button" class="p-3 text-red-400 hover:bg-red-400/10 rounded-lg transition-all" title="Remove Runner">
                                            <span class="material-symbols-outlined">delete_forever</span>
                                        </button>
                                    </div>
                                    
                                    <div v-if="form.runner_versions.length === 0" class="text-center py-12 border-2 border-dashed border-[#224249] rounded-xl bg-[#101f22]/20">
                                        <span class="material-symbols-outlined text-4xl text-[#224249] mb-2 block">construction</span>
                                        <p class="text-[#4a6d74] text-sm italic font-medium">No runners assigned to this stack yet.</p>
                                    </div>
                                    <p v-if="errs.runner_versions" class="text-red-500 text-sm mt-2">{{ errs.runner_versions }}</p>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-8 border-t border-[#224249]">
                                <NuxtLink to="/admin/stack" class="flex items-center gap-2 px-6 py-3 bg-dark-red hover:bg-red-800 text-white font-bold rounded-lg transition-all transform hover:scale-105 shadow-lg" type="button">
                                    <span class="material-symbols-outlined font-bold">close</span>
                                    <span>Abandon</span>
                                </NuxtLink>
                                <button class="flex items-center gap-2 px-10 py-3 bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark font-black rounded-lg transition-all transform hover:scale-105 shadow-[0_4px_20px_0_rgba(255,215,0,0.4)] uppercase tracking-tighter" type="submit">
                                    <span class="material-symbols-outlined font-bold">verified</span>
                                    <span>Update Stack</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </main>
    </NuxtLayout>
</template>
