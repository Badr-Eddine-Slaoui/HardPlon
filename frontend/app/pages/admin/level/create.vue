<script setup lang="ts">
    import { useLevel } from '~~/stores/level';
    import type { ReturnData } from '~~/types/api';

    useHead({
        title: 'Admin Dashboard - Create Level'
    })

    const store = useLevel()

    const form = reactive({
        name: '',
        description: '',
        order: 0
    })

    const errs = ref({
        name: '',
        description: '',
        order: '',
        message: ''
    })

    const submit = async () => {
        const res : ReturnData<any> = await store.createLevel(form);
        if(res.success) {
            navigateTo('/admin/level')
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
            <section
                class="flex-1 overflow-y-auto p-10 custom-scrollbar bg-[url('https://www.transparenttextures.com/patterns/dark-matter.png')]">
                <div class="max-w-3xl mx-auto">
                    <div class="mb-10 text-center">
                        <span class="text-primary text-xs font-bold tracking-[0.3em] uppercase mb-2 block">Level
                            Definition</span>
                        <h2 class="text-5xl font-bold text-white font-adventure tracking-wider mb-2">Ascend to a New Rank
                        </h2>
                        <div class="w-24 h-1 bg-pirate-gold mx-auto"></div>
                    </div>
                    <div class="nautical-border bg-[#182f34] p-8 rounded-lg shadow-2xl relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 pointer-events-none">
                            <span class="material-symbols-outlined text-9xl text-white">anchor</span>
                        </div>
                        <form @submit.prevent="submit" class="space-y-8 relative z-10">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="md:col-span-2">
                                    <label class="block text-pirate-gold text-xs font-bold uppercase tracking-widest mb-2"
                                        for="level-name">
                                        Level Name
                                    </label>
                                    <input v-model="form.name"
                                        class="w-full bg-[#101f22] border border-[#224249] text-white rounded-lg px-4 py-3 focus:ring-pirate-gold focus:border-pirate-gold placeholder:text-[#4a6d74] transition-all"
                                        id="level-name" placeholder="e.g. Advanced Future Sight" type="text" />
                                    <p v-if="errs.name" class="text-red-500 text-sm mt-2">{{ errs.name }}</p>
                                </div>
                                <div>
                                    <label class="block text-pirate-gold text-xs font-bold uppercase tracking-widest mb-2"
                                        for="rank-order">
                                        Rank / Order
                                    </label>
                                    <div class="relative">
                                        <input v-model="form.order"
                                            class="w-full bg-[#101f22] border border-[#224249] text-white rounded-lg px-4 py-3 focus:ring-pirate-gold focus:border-pirate-gold placeholder:text-[#4a6d74] transition-all"
                                            id="rank-order" min="1" placeholder="1" type="number" />
                                        <div
                                            class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[#4a6d74]">
                                            <span class="material-symbols-outlined text-sm">unfold_more</span>
                                        </div>
                                    </div>
                                    <p v-if="errs.order" class="text-red-500 text-sm mt-2">{{ errs.order }}</p>
                                </div>
                            </div>
                            <div>
                                <label class="block text-pirate-gold text-xs font-bold uppercase tracking-widest mb-2"
                                    for="mastery-criteria">
                                    Mastery Criteria
                                </label>
                                <textarea v-model="form.description"
                                    class="w-full bg-[#101f22] border border-[#224249] text-white rounded-lg px-4 py-3 focus:ring-pirate-gold focus:border-pirate-gold placeholder:text-[#4a6d74] transition-all custom-scrollbar"
                                    id="mastery-criteria" placeholder="Describe the trials one must overcome to achieve this rank..." rows="6"></textarea>
                                <p v-if="errs.description" class="text-red-500 text-sm mt-2">{{ errs.description }}</p>
                                <p class="mt-2 text-[#90c1cb] text-xs italic">Detail the exact milestones, training hours,
                                    or feats of strength required.</p>
                            </div>
                            <div class="flex items-center justify-between pt-6 border-t border-[#224249]">
                                <NuxtLink to="/admin/level"
                                    class="flex items-center gap-2 px-6 py-3 bg-dark-red hover:bg-red-800 text-white font-bold rounded-lg transition-all transform hover:scale-105 shadow-lg"
                                    type="button">
                                    <span class="material-symbols-outlined font-bold">close</span>
                                    <span>Abandon</span>
                                </NuxtLink>
                                <div class="flex gap-4">
                                    <button
                                        class="flex items-center gap-2 px-10 py-3 bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark font-black rounded-lg transition-all transform hover:scale-105 shadow-[0_4px_20px_0_rgba(255,215,0,0.4)] uppercase tracking-tighter"
                                        type="submit">
                                        <span class="material-symbols-outlined font-bold">verified</span>
                                        <span>Confirm Rank</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="mt-12 grid grid-cols-3 gap-6">
                        <div class="text-center p-4 border border-[#224249] rounded-lg bg-[#182f34]/30">
                            <span class="material-symbols-outlined text-primary mb-2">military_tech</span>
                            <h4 class="text-white text-xs font-bold uppercase">Define Mastery</h4>
                            <p class="text-[#90c1cb] text-[10px] mt-1">Set clear boundaries for progression</p>
                        </div>
                        <div class="text-center p-4 border border-[#224249] rounded-lg bg-[#182f34]/30">
                            <span class="material-symbols-outlined text-primary mb-2">history_edu</span>
                            <h4 class="text-white text-xs font-bold uppercase">Update Log</h4>
                            <p class="text-[#90c1cb] text-[10px] mt-1">Changes are recorded in the Fleet Registry</p>
                        </div>
                        <div class="text-center p-4 border border-[#224249] rounded-lg bg-[#182f34]/30">
                            <span class="material-symbols-outlined text-primary mb-2">groups</span>
                            <h4 class="text-white text-xs font-bold uppercase">Broadcast</h4>
                            <p class="text-[#90c1cb] text-[10px] mt-1">Marines will be notified of new trials</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </NuxtLayout>
</template>