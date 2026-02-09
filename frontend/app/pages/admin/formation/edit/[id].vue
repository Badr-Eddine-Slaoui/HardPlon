<script setup lang="ts">
    import { useFormation } from '~~/stores/formation';
    import { useGradeLevel } from '~~/stores/grade_level';
    import type { ReturnData } from '~~/types/api';

    useHead({
        title: 'Admin Dashboard - Edit Formation'
    })

    const id: number = parseInt(useRoute().params?.id as string)

    const grades = useGradeLevel()
    const store = useFormation()

    const form = reactive({
        title: '',
        description: '',
        grade_level_id: 0,
        capacity: 0,
        duration: 0
    })

    onMounted(async() => {
        await grades.fetchGradeLevels();
        await store.fetchFormation(id);
        form.title = store.formation?.title as string
        form.description = store.formation?.description as string
        form.grade_level_id = store.formation?.grade_level_id as number
        form.capacity = store.formation?.capacity as number
        form.duration = store.formation?.duration as number
    })

    const errs = ref({
        title: '',
        description: '',
        grade_level_id: '',
        capacity: '',
        duration: '',
        message: ''
    })

    const submit = async () => {
        const res : ReturnData<any> = await store.createFormation(form);
        if(res.success) {
            navigateTo('/admin/formation')
        }
        
        if(res.errors) {
            errs.value = res.errors
        }
    }
</script>

<template>
    <NuxtLayout name="admin">
        <main class="flex-1 flex flex-col min-w-0 bg-background-light dark:bg-background-dark overflow-y-auto ocean-waves">
            <header
                class="h-20 flex items-center justify-between px-10 border-b border-slate-200 dark:border-[#224249] bg-white/5 backdrop-blur-md sticky top-0 z-10">
                <div class="flex items-center gap-4">
                    <h2 class="text-lg font-bold">Formations <span class="text-pirate-gold font-normal">/ Strategy
                            Editing</span></h2>
                </div>
            </header>
            <div class="p-10 max-w-4xl mx-auto w-full">
                <section class="mb-8">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="h-px flex-1 bg-gradient-to-r from-transparent via-pirate-gold/30 to-transparent"></div>
                        <h1
                            class="text-4xl font-adventure font-black tracking-widest text-pirate-gold text-center uppercase">
                            Edit Strategy</h1>
                        <div class="h-px flex-1 bg-gradient-to-r from-transparent via-pirate-gold/30 to-transparent"></div>
                    </div>
                    <p class="text-center text-[#90c1cb] max-w-lg mx-auto">Chart a new course for your fleet. Define the
                        tactics, durations, and ranks required for this formation.</p>
                </section>
                <div class="form-panel nautical-border rounded-xl p-8 gold-glow">
                    <form @submit.prevent="submit" class="space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label
                                    class="flex items-center gap-2 text-xs font-bold uppercase tracking-[0.2em] text-pirate-gold">
                                    <span class="material-symbols-outlined text-sm">label</span>
                                    Formation Name
                                </label>
                                <input v-model="form.title"
                                    class="w-full px-4 py-3 rounded-lg bg-[#0a1619] border-[#224249] text-white focus:ring-pirate-gold focus:border-pirate-gold transition-all"
                                    placeholder="e.g. Iron Wall Advance" type="text" />
                                <p v-if="errs.title" class="text-red-500 text-xs mt-1">{{ errs.title }}</p>
                            </div>
                            <div class="space-y-2">
                                <label
                                    class="flex items-center gap-2 text-xs font-bold uppercase tracking-[0.2em] text-pirate-gold">
                                    <span class="material-symbols-outlined text-sm">military_tech</span>
                                    Assigned Rank
                                </label>
                                <select v-model="form.grade_level_id"
                                    class="w-full px-4 py-3 rounded-lg bg-[#0a1619] border-[#224249] text-white focus:ring-pirate-gold focus:border-pirate-gold transition-all">
                                    <option value="" selected disabled>Select Crew Rank...</option>
                                    <option v-for="grade in grades.grade_levels" :value="grade.id" :key="grade.id">
                                        {{ grade.name }}
                                    </option>
                                </select>
                                <p v-if="errs.grade_level_id" class="text-red-500 text-xs mt-1">
                                    {{ errs.grade_level_id }}
                                </p>
                            </div>
                            <div class="space-y-2">
                                <label
                                    class="flex items-center gap-2 text-xs font-bold uppercase tracking-[0.2em] text-pirate-gold">
                                    <span class="material-symbols-outlined text-sm">schedule</span>
                                    Duration (Months)
                                </label>
                                <input v-model="form.duration"
                                    class="w-full px-4 py-3 rounded-lg bg-[#0a1619] border-[#224249] text-white focus:ring-pirate-gold focus:border-pirate-gold transition-all"
                                    max="10" min="1" placeholder="6" type="number" />
                                <p v-if="errs.duration" class="text-red-500 text-xs mt-1">
                                    {{ errs.duration }}
                                </p>
                            </div>
                            <div class="space-y-2">
                                <label
                                    class="flex items-center gap-2 text-xs font-bold uppercase tracking-[0.2em] text-pirate-gold">
                                    <span class="material-symbols-outlined text-sm">group</span>
                                    Capacity
                                </label>
                                <input v-model="form.capacity"
                                    class="w-full px-4 py-3 rounded-lg bg-[#0a1619] border-[#224249] text-white focus:ring-pirate-gold focus:border-pirate-gold transition-all"
                                    min="1" placeholder="6" type="number" />
                                <p v-if="errs.capacity" class="text-red-500 text-xs mt-1">
                                    {{ errs.capacity }}
                                </p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label
                                class="flex items-center gap-2 text-xs font-bold uppercase tracking-[0.2em] text-pirate-gold">
                                <span class="material-symbols-outlined text-sm">history_edu</span>
                                Strategy Description
                            </label>
                            <textarea v-model="form.description"
                                class="w-full px-4 py-3 rounded-lg bg-[#0a1619] border-[#224249] text-white focus:ring-pirate-gold focus:border-pirate-gold transition-all resize-none"
                                placeholder="Detail the tactical maneuvers and crew positioning for this formation..." rows="6"></textarea>
                            <p v-if="errs.description" class="text-red-500 text-xs mt-1">{{ errs.description }}</p>
                        </div>
                        <div class="flex items-center justify-end gap-4 pt-6 border-t border-[#224249]">
                            <NuxtLink to="/admin/formation"
                                class="flex items-center gap-2 px-8 py-3 bg-dark-red text-white rounded-lg font-bold text-sm hover:brightness-110 transition-all uppercase tracking-wider"
                                type="button">
                                <span class="material-symbols-outlined">delete_forever</span>
                                Abandon
                            </NuxtLink>
                            <button
                                class="flex items-center gap-2 px-10 py-3 bg-pirate-gold text-background-dark rounded-lg font-black text-sm hover:brightness-110 transition-all shadow-lg shadow-pirate-gold/20 uppercase tracking-tighter"
                                type="submit">
                                <span class="material-symbols-outlined font-bold">assignment_turned_in</span>
                                Update Strategy
                            </button>
                        </div>
                    </form>
                </div>
                <div class="mt-12 grid grid-cols-2 gap-6 opacity-60">
                    <div class="flex items-center gap-4 p-4 rounded-xl border border-dashed border-[#224249]">
                        <span class="material-symbols-outlined text-4xl text-pirate-gold">compass_calibration</span>
                        <p class="text-xs uppercase tracking-widest leading-relaxed">System verification: <br /><span
                                class="text-white font-bold">Log Pose Synchronized</span></p>
                    </div>
                    <div class="flex items-center gap-4 p-4 rounded-xl border border-dashed border-[#224249]">
                        <span class="material-symbols-outlined text-4xl text-pirate-gold">verified_user</span>
                        <p class="text-xs uppercase tracking-widest leading-relaxed">Strategy integrity: <br /><span
                                class="text-white font-bold">Fleet Authorized</span></p>
                    </div>
                </div>
            </div>
            <footer
                class="p-10 border-t border-slate-200 dark:border-[#224249] text-center text-slate-400 dark:text-[#90c1cb] text-sm mt-auto bg-background-dark/50 backdrop-blur-sm">
                © 1524 Grand Line Academy Management System • Fleet Strategic Command • Ver. 4.2.0
            </footer>
        </main>
    </NuxtLayout>
</template>