<script setup lang="ts">
    import { useGradeLevel } from '~~/stores/grade_level';
    import { useScholarYear } from '~~/stores/scholar_year';
    import type { ReturnData } from '~~/types/api';

    useHead({
        title: 'Admin Dashboard - Create Crew Ranks'
    })

    const grades = useGradeLevel()
    const years = useScholarYear()

    onMounted(async() => {
        await years.fetchScholarYears();
    })

    const form = reactive({
        name: '',
        description: '',
        scholar_year_id: 0,
        capacity: 0
    })

    const errs = ref({
        name: '',
        description: '',
        scholar_year_id: '',
        capacity: '',
        message: ''
    })

    const submit = async () => {
        const res : ReturnData<any> = await grades.createGradeLevel(form);
        if(res.success) {
            navigateTo('/admin/grade-level')
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
        <main class="flex-1 flex flex-col min-w-0 bg-background-light dark:bg-background-dark overflow-y-auto ocean-waves">
            <header
                class="h-20 flex items-center justify-between px-10 border-b border-slate-200 dark:border-[#224249] bg-white/5 backdrop-blur-md sticky top-0 z-10">
                <div class="flex items-center gap-4">
                    <NuxtLink class="flex items-center gap-2 text-slate-500 dark:text-[#90c1cb] hover:text-primary transition-colors"
                        to="/admin/grade-level">
                        <span class="material-symbols-outlined text-xl">arrow_back</span>
                        <span class="text-sm font-medium uppercase tracking-widest">Back to Fleet</span>
                    </NuxtLink>
                    <div class="h-4 w-px bg-slate-200 dark:bg-[#224249]"></div>
                    <h2 class="text-lg font-bold font-pirate text-pirate-gold">Rank Commission <span
                            class="text-primary font-normal">/ New Grade</span></h2>
                </div>
            </header>
            <div class="p-10 flex-1 flex flex-col items-center">
                <div class="max-w-2xl w-full">
                    <div class="mb-10 text-center">
                        <div
                            class="inline-flex items-center justify-center p-4 rounded-full bg-pirate-gold/10 text-pirate-gold mb-4 border-2 border-pirate-gold/20">
                            <span class="material-symbols-outlined text-4xl">military_tech</span>
                        </div>
                        <h1 class="text-4xl font-bold font-pirate tracking-wider text-white mb-2">Establish a New Rank</h1>
                        <p class="text-slate-500 dark:text-[#90c1cb]">Prepare the manifest for a new grade level to join the
                            Grand Line Academy fleet.</p>
                    </div>
                    <div class="parchment-card rounded-2xl p-8 relative overflow-hidden">
                        <span
                            class="material-symbols-outlined absolute -right-10 -top-10 text-white/5 text-[240px] select-none rotate-12">history_edu</span>
                        <form @submit.prevent="submit" class="space-y-8 relative z-10">
                            <div class="grid grid-cols-1 gap-8">
                                <div class="space-y-3">
                                    <label class="nautical-label flex items-center gap-2" for="start-year">
                                        <span class="material-symbols-outlined text-sm">calendar_month</span>
                                        Scholar Year
                                    </label>
                                    <div class="relative">
                                        <select v-model="form.scholar_year_id" class="nautical-input text-white" id="start-year" >
                                            <option value="" selected disabled>Select Scholar Year</option>
                                            <template v-for="year in years.scholar_years" :key="year.id">
                                                <option v-if="year.is_active" :value="year.id" >{{ year.start_year }} - {{ year.end_year }}</option>
                                            </template>
                                        </select>
                                    </div>
                                    <span v-if="errs.scholar_year_id" class="text-sm text-rose-600">{{ errs.scholar_year_id }}</span>
                                </div>
                                <div class="space-y-3">
                                    <label class="adventurous-label block" for="grade-name">
                                        Grade Name
                                    </label>
                                    <div class="relative">
                                        <span
                                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500">label</span>
                                        <input v-model="form.name" class="nautical-input w-full pl-[3rem_!important] py-3" id="grade-name"
                                            placeholder="e.g. East Blue Rookies" type="text" />
                                    </div>
                                    <p v-if="errs.name" class="text-red-500 text-sm mt-2">{{ errs.name }}</p>
                                </div>
                                <div class="space-y-3">
                                    <label class="adventurous-label block" for="capacity">
                                        Target Capacity
                                    </label>
                                    <div class="relative">
                                        <span
                                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500">group</span>
                                        <input v-model="form.capacity" class="nautical-input w-full pl-[3rem_!important] py-3" id="capacity" min="0"
                                            placeholder="Number of recruits" type="number"/>
                                    </div>
                                    <p v-if="errs.capacity" class="text-red-500 text-sm mt-2">{{ errs.capacity }}</p>
                                </div>
                                <div class="space-y-3 pt-4">
                                    <label class="adventurous-label block" for="mission-notes">
                                        Mission Notes
                                    </label>
                                    <textarea v-model="form.description" class="nautical-input w-full py-3 px-4 min-h-[100px]" id="mission-notes"
                                        placeholder="Describe the rank's primary objective..."></textarea>
                                    <p v-if="errs.description" class="text-red-500 text-sm mt-2">{{ errs.description }}</p>
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row items-center gap-4 pt-6">
                                <button
                                    class="w-full sm:flex-1 bg-pirate-gold hover:brightness-110 text-slate-900 font-bold py-4 px-6 rounded-xl flex items-center justify-center gap-2 transition-all shadow-lg shadow-pirate-gold/10 font-pirate text-xl"
                                    type="submit">
                                    <span class="material-symbols-outlined">verified</span>
                                    Confirm Rank
                                </button>
                                <NuxtLink to="/admin/grade-level"
                                    class="w-full sm:w-auto bg-abandon-red/20 hover:bg-abandon-red text-abandon-red hover:text-white font-bold py-4 px-10 rounded-xl transition-all border border-abandon-red/30 font-pirate text-xl"
                                    type="button">
                                    Abandon
                                </NuxtLink>
                            </div>
                        </form>
                    </div>
                    <div class="mt-10 flex items-center gap-6 p-6 rounded-xl bg-white/5 border border-[#224249]">
                        <div
                            class="size-12 rounded-full border-2 border-primary/20 flex items-center justify-center text-primary flex-shrink-0">
                            <span class="material-symbols-outlined">help_center</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm text-primary uppercase tracking-widest">Navigator's Tip</h4>
                            <p class="text-sm text-slate-400">Newly commissioned ranks will be immediately visible in the
                                Fleet Inspection dashboard once confirmed.</p>
                        </div>
                    </div>
                </div>
            </div>
            <footer
                class="p-10 border-t border-slate-200 dark:border-[#224249] text-center text-slate-400 dark:text-[#90c1cb] text-sm mt-auto">
                © 1524 Grand Line Academy Management System • Rank Commission Center • Ver. 4.2.0
            </footer>
        </main>
    </NuxtLayout>
</template>