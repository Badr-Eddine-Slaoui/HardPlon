<script setup lang="ts">
    import { useRunner } from '~~/stores/runner';
    import type { ReturnData } from '~~/types/api';

    useHead({ title: 'Admin Dashboard - Edit Runner' })

    const id: number = parseInt(useRoute().params?.id as string)

    const store = useRunner()

    const form = reactive({
        name: '',
        description: '',
        type: '',
        status: 'active'
    })

    onMounted(async() => {
        await store.fetchRunner(id);
        form.name = store.runner?.name as string;
        form.description = store.runner?.description as string;
        form.type = store.runner?.type as string;
        form.status = store.runner?.status as string;
    })

    const errs = ref({
        name: '',
        description: '',
        type: '',
        status: '',
        message: ''
    })

    const submit = async () => {
        const res : ReturnData<any> = await store.updateRunner(id, form);
        if(res.success) {
            navigateTo('/admin/runner')
        }
        if(res.errors) {
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
                        <span class="text-primary text-xs font-bold tracking-[0.3em] uppercase mb-2 block">Runner Edition</span>
                        <h2 class="text-5xl font-bold text-white font-adventure tracking-wider mb-2">Update Runner</h2>
                        <div class="w-24 h-1 bg-pirate-gold mx-auto"></div>
                    </div>
                    <div class="nautical-border bg-[#182f34] p-8 rounded-lg shadow-2xl relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 pointer-events-none">
                            <span class="material-symbols-outlined text-9xl text-white">terminal</span>
                        </div>
                        <form @submit.prevent="submit" class="space-y-8 relative z-10">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-pirate-gold text-xs font-bold uppercase tracking-widest mb-2" for="runner-name">Runner Name</label>
                                    <input v-model="form.name" class="w-full bg-[#101f22] border border-[#224249] text-white rounded-lg px-4 py-3 focus:ring-pirate-gold focus:border-pirate-gold placeholder:text-[#4a6d74] transition-all" id="runner-name" placeholder="e.g. Node.js Runner" type="text" />
                                    <p v-if="errs.name" class="text-red-500 text-sm mt-2">{{ errs.name }}</p>
                                </div>
                                <div>
                                    <label class="block text-pirate-gold text-xs font-bold uppercase tracking-widest mb-2" for="runner-type">Type</label>
                                    <select v-model="form.type" class="w-full bg-[#101f22] border border-[#224249] text-white rounded-lg px-4 py-3 focus:ring-pirate-gold focus:border-pirate-gold transition-all" id="runner-type">
                                        <option value="" disabled>Select a type...</option>
                                        <option value="http">HTTP</option>
                                        <option value="browser">Browser</option>
                                        <option value="function">Function</option>
                                    </select>
                                    <p v-if="errs.type" class="text-red-500 text-sm mt-2">{{ errs.type }}</p>
                                </div>
                            </div>
                            <div>
                                <label class="block text-pirate-gold text-xs font-bold uppercase tracking-widest mb-2" for="runner-status">Status</label>
                                <select v-model="form.status" class="w-full bg-[#101f22] border border-[#224249] text-white rounded-lg px-4 py-3 focus:ring-pirate-gold focus:border-pirate-gold transition-all" id="runner-status">
                                    <option value="active">Active</option>
                                    <option value="deprecated">Deprecated</option>
                                </select>
                                <p v-if="errs.status" class="text-red-500 text-sm mt-2">{{ errs.status }}</p>
                            </div>
                            <div>
                                <label class="block text-pirate-gold text-xs font-bold uppercase tracking-widest mb-2" for="runner-description">Description</label>
                                <textarea v-model="form.description" class="w-full bg-[#101f22] border border-[#224249] text-white rounded-lg px-4 py-3 focus:ring-pirate-gold focus:border-pirate-gold placeholder:text-[#4a6d74] transition-all custom-scrollbar" id="runner-description" placeholder="Describe the runner's purpose and capabilities..." rows="6"></textarea>
                                <p v-if="errs.description" class="text-red-500 text-sm mt-2">{{ errs.description }}</p>
                                <p class="mt-2 text-[#90c1cb] text-xs italic">Detail the runner environment, supported languages, and execution context.</p>
                            </div>
                            <div class="flex items-center justify-between pt-6 border-t border-[#224249]">
                                <NuxtLink to="/admin/runner" class="flex items-center gap-2 px-6 py-3 bg-dark-red hover:bg-red-800 text-white font-bold rounded-lg transition-all transform hover:scale-105 shadow-lg" type="button">
                                    <span class="material-symbols-outlined font-bold">close</span>
                                    <span>Abandon</span>
                                </NuxtLink>
                                <div class="flex gap-4">
                                    <button class="flex items-center gap-2 px-10 py-3 bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark font-black rounded-lg transition-all transform hover:scale-105 shadow-[0_4px_20px_0_rgba(255,215,0,0.4)] uppercase tracking-tighter" type="submit">
                                        <span class="material-symbols-outlined font-bold">verified</span>
                                        <span>Update Runner</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="mt-12 grid grid-cols-3 gap-6">
                        <div class="text-center p-4 border border-[#224249] rounded-lg bg-[#182f34]/30">
                            <span class="material-symbols-outlined text-primary mb-2">build</span>
                            <h4 class="text-white text-xs font-bold uppercase">Configure</h4>
                            <p class="text-[#90c1cb] text-[10px] mt-1">Set up the runner environment</p>
                        </div>
                        <div class="text-center p-4 border border-[#224249] rounded-lg bg-[#182f34]/30">
                            <span class="material-symbols-outlined text-primary mb-2">history_edu</span>
                            <h4 class="text-white text-xs font-bold uppercase">Version Log</h4>
                            <p class="text-[#90c1cb] text-[10px] mt-1">Changes are recorded in the Registry</p>
                        </div>
                        <div class="text-center p-4 border border-[#224249] rounded-lg bg-[#182f34]/30">
                            <span class="material-symbols-outlined text-primary mb-2">groups</span>
                            <h4 class="text-white text-xs font-bold uppercase">Deploy</h4>
                            <p class="text-[#90c1cb] text-[10px] mt-1">Runners will be available for evaluations</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </NuxtLayout>
</template>
