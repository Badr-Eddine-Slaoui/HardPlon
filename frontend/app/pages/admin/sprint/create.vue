<script setup lang="ts">
    import { useFormation } from '~~/stores/formation';
    import { useSkill } from '~~/stores/skill';
    import { useSprint } from '~~/stores/sprint';
    import type { ReturnData } from '~~/types/api';
    import type { Skill } from '~~/types/skill';

    useHead({
        title: 'Admin Dashboard - Create Sprint'
    })

    const store = useSprint()
    const fomations = useFormation()
    const skills = useSkill()
    const skills_by_formation = ref<Skill[] | null>(null)

    onMounted(async() => {
        await fomations.fetchFormations();
        skills_by_formation.value = await skills.fetchByFormation(fomations.formations?.[0]?.id as number);
    })

    const changeFormation = async (id: number) => {
        skills_by_formation.value = await skills.fetchByFormation(id);
    }

    const form = reactive({
        formation_id: 0,
        name: '',
        description: '',
        start_date: '',
        end_date: '',
        skills: [] as number[]
    })

    const errs = ref({
        formation_id: '',
        name: '',
        description: '',
        start_date: '',
        end_date: '',
        skills: '',
        message: ''
    })

    const submit = async () => {
        console.log(form);
        
        const res : ReturnData<any> = await store.createSprint(form);
        if(res.success) {
            navigateTo('/admin/sprint')
        }
        
        if(res.errors) {
            errs.value = res.errors
        }
    }
</script>

<template>
    <NuxtLayout name="admin">
        <main class="flex w-full min-h-screen overflow-hidden">
            <section class="flex-1 overflow-y-auto p-10 custom-scrollbar">
                <div class="max-w-5xl mx-auto">
                    <div class="mb-10 text-center">
                        <h2 class="text-5xl font-black text-white font-nautical tracking-widest mb-2">CHART A NEW SPRINT
                        </h2>
                        <div class="h-1 w-32 bg-pirate-gold mx-auto mb-4"></div>
                        <p class="text-[#90c1cb] italic">"Prepare the crew for the next leg of our journey across the Grand
                            Line."</p>
                    </div>
                    <form @submit.prevent="submit" class="space-y-8">
                        <div class="nautical-border p-8 bg-marine-dark/50 shadow-2xl space-y-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="flex flex-col gap-2">
                                    <label
                                        class="text-pirate-gold text-xs font-bold uppercase tracking-widest flex items-center gap-2">
                                        <span class="material-symbols-outlined text-sm">label</span>
                                        Sprint Formation
                                    </label>
                                    <select v-model="form.formation_id" @change="changeFormation(form.formation_id)"
                                        class="bg-background-dark border-marine-border text-white rounded-lg focus:ring-primary focus:border-primary px-4 py-3 text-lg placeholder:text-marine-border/50"
                                        type="text">
                                        <option value="" disabled>Select a formation</option>
                                        <option v-for="formation in fomations.formations" :selected="formation.id === fomations.formations?.[0]?.id" :key="formation.id" :value="formation.id">{{ formation.title }}</option>
                                    </select>
                                    <span v-if="errs.formation_id" class="text-red-500 text-sm mt-1">{{ errs.formation_id }}</span>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label
                                        class="text-pirate-gold text-xs font-bold uppercase tracking-widest flex items-center gap-2">
                                        <span class="material-symbols-outlined text-sm">label</span>
                                        Sprint Name
                                    </label>
                                    <input v-model="form.name"
                                        class="bg-background-dark border-marine-border text-white rounded-lg focus:ring-primary focus:border-primary px-4 py-3 text-lg placeholder:text-marine-border/50"
                                        placeholder="e.g., Alabasta Reclamation" type="text"/>
                                    <span v-if="errs.name" class="text-red-500 text-sm mt-1">{{ errs.name }}</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="flex flex-col gap-2">
                                    <label
                                        class="text-pirate-gold text-xs font-bold uppercase tracking-widest flex items-center gap-2">
                                        <span class="material-symbols-outlined text-sm">calendar_month</span>
                                        Start Date
                                    </label>
                                    <div class="relative">
                                        <input v-model="form.start_date"
                                            class="w-full bg-background-dark border-marine-border text-white rounded-lg focus:ring-primary focus:border-primary px-10 py-3 text-lg placeholder:text-marine-border/50"
                                            placeholder="Select voyage dates..." type="date"/>
                                        <span
                                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-primary">date_range</span>
                                    </div>
                                    <span v-if="errs.start_date" class="text-red-500 text-sm mt-1">{{ errs.start_date }}</span>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label
                                        class="text-pirate-gold text-xs font-bold uppercase tracking-widest flex items-center gap-2">
                                        <span class="material-symbols-outlined text-sm">calendar_month</span>
                                        End Date
                                    </label>
                                    <div class="relative">
                                        <input v-model="form.end_date"
                                            class="w-full bg-background-dark border-marine-border text-white rounded-lg focus:ring-primary focus:border-primary px-10 py-3 text-lg placeholder:text-marine-border/50"
                                            placeholder="Select voyage dates..." type="date" />
                                        <span
                                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-primary">date_range</span>
                                    </div>
                                    <span v-if="errs.end_date" class="text-red-500 text-sm mt-1">{{ errs.end_date }}</span>
                                </div>
                            </div>
                            <div class="flex flex-col gap-4">
                                <label
                                    class="text-pirate-gold text-xs font-bold uppercase tracking-widest flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm">bolt</span>
                                    Assign Skills from Arsenal
                                </label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                    <div v-for="skill in skills_by_formation" :key="skill.id"
                                        class="flex items-center gap-3 p-3 bg-background-dark border border-marine-border rounded-lg cursor-pointer hover:border-primary group transition-all">
                                        <div
                                            class="size-12 bg-primary/10 rounded flex items-center justify-center border border-primary/20">
                                            <span class="text-primary text-sm">{{ skill.code }}</span>
                                        </div>
                                        <span class="text-white text-sm font-medium">{{ skill.title }}</span>
                                        <input v-model="form.skills"
                                            class="ml-auto rounded border-marine-border bg-marine-dark text-primary focus:ring-0 focus:ring-offset-0"
                                            type="checkbox" :value="skill.id" />
                                    </div>
                                </div>
                                <span v-if="errs.skills" class="text-red-500 text-sm mt-1">{{ errs.skills }}</span>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-pirate-gold text-xs font-bold uppercase tracking-widest flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm">description</span>
                                    Mission Briefing
                                </label>
                                <textarea v-model="form.description"
                                    class="bg-background-dark border-marine-border text-white rounded-lg focus:ring-primary focus:border-primary px-4 py-3 text-base placeholder:text-marine-border/50"
                                    placeholder="Describe the objectives of this voyage..." rows="3"></textarea>
                                <span v-if="errs.description" class="text-red-500 text-sm mt-1">{{ errs.description }}</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-center gap-6 mt-12">
                            <NuxtLink :to="`/admin/sprint`"
                                class="px-10 py-4 bg-abort-red hover:bg-red-800 text-white font-black uppercase tracking-widest rounded-lg transition-all shadow-lg hover:shadow-red-900/40 font-nautical"
                                type="button">
                                ABORT
                            </NuxtLink>
                            <button
                                class="px-12 py-4 bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark font-black uppercase tracking-widest rounded-lg transition-all transform hover:scale-105 shadow-[0_10px_20px_0_rgba(255,215,0,0.2)] font-nautical"
                                type="submit">
                                LOCK COURSE
                            </button>
                        </div>
                    </form>
                    <div class="mt-12 pt-8 border-t border-marine-border/30 text-center">
                        <p class="text-marine-border text-xs uppercase tracking-[0.3em] font-bold">Logistics Command Center
                            • Marine HQ</p>
                    </div>
                </div>
            </section>
        </main>
    </NuxtLayout>
</template>