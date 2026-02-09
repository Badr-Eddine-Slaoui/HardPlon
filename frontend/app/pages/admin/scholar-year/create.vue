<script setup lang="ts">
    import { useScholarYear } from '~~/stores/scholar_year';
    import type { ReturnData } from '~~/types/api';

    useHead({
        title: 'Admin Dashboard - Create Scholar Years'
    })

    const currentYear = new Date().getFullYear()

    const start_years = Array.from(
        { length: 11 },
        (_, i) => currentYear + i
    )

    const end_years = Array.from(
        { length: 11 },
        (_, i) => currentYear + i + 1
    )

    const years = useScholarYear()

    const form = reactive({
        start_year: 0,
        end_year: 0,
        capacity: 0
    })

    const errs = ref({
        start_year: '',
        end_year: '',
        capacity: '',
        message: ''
    })

    const submit = async () => {
        const res : ReturnData<any> = await years.createScholarYear(form);
        if(res.success) {
            navigateTo('/admin/scholar-year')
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
        <main class="flex-1 flex flex-col min-w-0 bg-background-light dark:bg-background-dark overflow-y-auto">
            <header
                class="h-20 flex items-center justify-between px-10 border-b border-slate-200 dark:border-[#224249] bg-white/5 backdrop-blur-md sticky top-0 z-10">
                <div class="flex items-center gap-6 flex-1">
                    <nav class="flex items-center text-sm text-slate-500 dark:text-[#90c1cb]">
                        <NuxtLink class="hover:text-primary transition-colors" to="/admin/scholar-year">Academic Years</NuxtLink>
                        <span class="material-symbols-outlined mx-2 text-xs">chevron_right</span>
                        <span class="text-slate-900 dark:text-white font-bold">New Voyage</span>
                    </nav>
                </div>
            </header>
            <div class="p-10 max-w-[900px] mx-auto w-full">
                <section class="mb-10 text-center relative">
                    <div class="absolute inset-0 flex items-center justify-center opacity-10 pointer-events-none">
                        <span class="material-symbols-outlined text-[200px]">explore</span>
                    </div>
                    <h1 class="text-5xl font-black tracking-tight text-slate-900 dark:text-white mb-4">Map a New Voyage</h1>
                    <p class="text-slate-500 dark:text-[#90c1cb] text-lg max-w-xl mx-auto italic font-medium">"Plot the
                        coordinates for the next academic expedition across the Grand Line."</p>
                </section>
                <div
                    class="bg-white dark:bg-[#1a2e33] border border-slate-200 dark:border-[#224249] rounded-2xl shadow-2xl overflow-hidden relative">
                    <div class="h-1.5 w-full bg-gradient-to-r from-pirate-red via-pirate-gold to-pirate-red"></div>
                    <form @submit.prevent="submit" class="p-8 md:p-12 space-y-10 compass-bg">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label class="nautical-label flex items-center gap-2" for="start-year">
                                    <span class="material-symbols-outlined text-sm">calendar_today</span>
                                    Start Year (Departure)
                                </label>
                                <div class="relative">
                                    <select v-model="form.start_year" class="nautical-input" id="start-year" >
                                        <option value="" selected disabled>Select Start Year</option>
                                        <option v-for="year in start_years" :value="year" >{{ year }}</option>
                                    </select>
                                </div>
                                <span v-if="errs.start_year" class="text-sm text-rose-600">{{ errs.start_year }}</span>
                            </div>
                            <div class="space-y-2">
                                <label class="nautical-label flex items-center gap-2" for="end-year">
                                    <span class="material-symbols-outlined text-sm">anchor</span>
                                    End Year (Arrival)
                                </label>
                                <div class="relative">
                                    <select v-model="form.end_year" class="nautical-input" id="end-year" name="end_year">
                                        <option value="" selected disabled>Select End Year</option>
                                        <option v-for="year in end_years" :value="year">{{ year }}</option>
                                    </select>
                                </div>
                                <span v-if="errs.end_year" class="text-sm text-rose-600">{{ errs.end_year }}</span>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <label class="nautical-label flex items-center gap-2" for="capacity">
                                <span class="material-symbols-outlined text-sm">groups</span>
                                Total Fleet Capacity (Students)
                            </label>
                            <div class="relative">
                                <span
                                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">person_search</span>
                                <input v-model="form.capacity" class="nautical-input pl-[3rem_!important]" id="capacity" min="1" placeholder="e.g. 500" type="number" />
                            </div>
                            <span v-if="errs.capacity" class="text-sm text-rose-600">{{ errs.capacity }}</span>
                            <p class="text-xs text-slate-500 dark:text-[#90c1cb]">The total number of seats available
                                across all classes for this scholar year.</p>
                        </div>
                        <div
                            class="flex flex-col sm:flex-row items-center justify-end gap-4 pt-6 border-t border-slate-200 dark:border-[#224249]">
                            <NuxtLink to="/admin/scholar-year"
                                class="w-full sm:w-auto px-8 py-3 rounded-lg font-bold text-slate-600 dark:text-[#90c1cb] hover:bg-slate-100 dark:hover:bg-[#224249] transition-all"
                                type="button">
                                Cancel
                            </NuxtLink>
                            <button
                                class="w-full sm:w-auto px-10 py-3 rounded-lg bg-pirate-gold text-[#101f22] font-black uppercase tracking-widest flex items-center justify-center gap-2 hover:bg-yellow-400 active:scale-95 transition-all shadow-[0_0_20px_rgba(255,215,0,0.3)]"
                                type="submit">
                                <span class="material-symbols-outlined">sailing</span>
                                Set Sail
                            </button>
                        </div>
                    </form>
                </div>
                <section class="mt-12 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-5 rounded-xl border border-pirate-red/30 bg-pirate-red/5 flex gap-4">
                        <span class="material-symbols-outlined text-pirate-red">warning</span>
                        <p class="text-sm text-slate-600 dark:text-[#90c1cb]">Ensure dates do not overlap with existing
                            voyages to avoid temporal anomalies in the Log Pose.</p>
                    </div>
                    <div class="p-5 rounded-xl border border-primary/30 bg-primary/5 flex gap-4">
                        <span class="material-symbols-outlined text-primary">info</span>
                        <p class="text-sm text-slate-600 dark:text-[#90c1cb]">New years are usually planned 3 months before
                            departure from the current island.</p>
                    </div>
                </section>
            </div>
            <footer
                class="mt-auto p-10 border-t border-slate-200 dark:border-[#224249] text-center text-slate-400 dark:text-[#90c1cb] text-sm">
                © 1524 Grand Line Academy Management System • Charting New Territories
            </footer>
        </main>
    </NuxtLayout>
</template>