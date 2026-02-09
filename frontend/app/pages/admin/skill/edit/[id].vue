<script setup lang="ts">
    import { useFormation } from '~~/stores/formation';
    import { useLevel } from '~~/stores/level';
    import { useSkill } from '~~/stores/skill';
    import type { ReturnData } from '~~/types/api';
import type { SkillLevel } from '~~/types/skill';

    useHead({
        title: 'Admin Dashboard - Edit Skill'
    })

    const id: number = parseInt(useRoute().params?.id as string)

    const store = useSkill()
    const fomations = useFormation()
    const levels = useLevel()

    const form = reactive({
        formation_id: 0,
        title: '',
        description: '',
        code: '',
        skill_levels: [] as { level_id: number, criteria: string }[]
    })

    onMounted(async() => {
        await fomations.fetchFormations();
        await levels.fetchLevels();
        await store.fetchSkill(id);
        form.formation_id = store.skill?.formation_id as number
        form.title = store.skill?.title as string
        form.description = store.skill?.description as string
        form.code = store.skill?.code as string
        form.skill_levels = store.skill?.skill_levels?.map((l: SkillLevel) => {
            return {
                level_id: l.level_id,
                criteria: l.criteria
            }
        }) as { level_id: number, criteria: string }[]
    })

    const errs = ref({
        formation_id: '',
        title: '',
        description: '',
        code: '',
        skill_levels: '',
        message: ''
    })

    const isChecked = (level_id: number) => {
        return form.skill_levels.some(l => l.level_id === level_id)
    }

    const toggleLevel = (level_id: number) => {
        const index = form.skill_levels.findIndex(l => l.level_id === level_id)

        if (index === -1) {
            form.skill_levels.push({
                level_id: level_id,
                criteria: ''
            })
        } else {
            form.skill_levels.splice(index, 1)
        }
    }

    const getCriteria = (level_id: number) => {
        const item = form.skill_levels.find(l => l.level_id === level_id)
        return item ? item.criteria : ''
    }

    const updateCriteria = (level_id: number, value: string) => {
        const item = form.skill_levels.find(l => l.level_id === level_id)
        if (item) {
            item.criteria = value
        }
    }

    const submit = async () => {
        const res : ReturnData<any> = await store.updateSkill(id, form);
        if(res.success) {
            navigateTo('/admin/skill')
        }
        
        if(res.errors) {
            errs.value = res.errors
        }
    }
</script>

<template>
    <NuxtLayout name="admin">
        <main class="w-full flex h-screen overflow-hidden">
            <section class="flex-1 overflow-y-auto p-10 custom-scrollbar">
                <form @submit.prevent="submit" class="max-w-4xl mx-auto">
                    <div class="flex justify-between items-center mb-10">
                        <div>
                            <h2 class="text-4xl font-bold text-white font-display mb-2">Edit Ability</h2>
                            <p class="text-[#90c1cb]">Craft the parameters of a new combat or tactical skill for the
                                Marines.</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <NuxtLink to="/admin/skill"
                                class="px-6 py-3 border border-[#315f68] text-white hover:bg-[#315f68] font-bold rounded-lg transition-all">
                                Abandon
                            </NuxtLink>
                            <button type="submit"
                                class="flex items-center gap-2 px-8 py-3 bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark font-bold rounded-lg transition-all transform hover:scale-105 shadow-[0_4px_14px_0_rgba(255,215,0,0.39)]">
                                <span class="material-symbols-outlined font-bold">anchor</span>
                                <span>Anchor Skill</span>
                            </button>
                        </div>
                    </div>
                    <div class="space-y-8">
                        <div class="rounded-xl border border-[#315f68] bg-[#182f34] p-8 form-card shadow-2xl">
                            <div class="flex items-center gap-3 mb-6">
                                <span class="material-symbols-outlined text-primary">description</span>
                                <h3 class="text-xl font-bold text-white font-display uppercase tracking-wider">Skill Core
                                </h3>
                            </div>
                            <div class="grid grid-cols-1 gap-6">
                                <div class="flex flex-col gap-2">
                                    <label class="text-[#90c1cb] text-sm font-bold uppercase tracking-wide">Formation</label>
                                    <select v-model="form.formation_id"
                                        class="bg-[#224249] border-[#315f68] text-white rounded-lg focus:ring-primary focus:border-primary px-4 py-3 text-lg placeholder:text-white/20">
                                        <option disabled selected value="">Select a Formation</option>
                                        <option v-for="formation in fomations.formations" :key="formation.id" :value="formation.id">{{ formation.title }} ({{ formation.grade_name }})</option>
                                    </select>
                                    <p v-if="errs.formation_id" class="text-red-500 text-xs mt-1">{{ errs.formation_id }}</p>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label class="text-[#90c1cb] text-sm font-bold uppercase tracking-wide">Ability
                                        Code</label>
                                    <input v-model="form.code"
                                        class="bg-[#224249] border-[#315f68] text-white rounded-lg focus:ring-primary focus:border-primary px-4 py-3 text-lg placeholder:text-white/20"
                                        placeholder="e.g., GEPO" type="text"/>
                                    <p v-if="errs.code" class="text-red-500 text-xs mt-1">{{ errs.code }}</p>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label class="text-[#90c1cb] text-sm font-bold uppercase tracking-wide">Ability
                                        Title</label>
                                    <input v-model="form.title"
                                        class="bg-[#224249] border-[#315f68] text-white rounded-lg focus:ring-primary focus:border-primary px-4 py-3 text-lg placeholder:text-white/20"
                                        placeholder="e.g., Grappler" type="text" />
                                    <p v-if="errs.title" class="text-red-500 text-xs mt-1">{{ errs.title }}</p>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label
                                        class="text-[#90c1cb] text-sm font-bold uppercase tracking-wide">Description</label>
                                    <textarea v-model="form.description"
                                        class="bg-[#224249] border-[#315f68] text-white rounded-lg focus:ring-primary focus:border-primary px-4 py-3 placeholder:text-white/20 resize-none"
                                        placeholder="Describe the origin, mechanics, and visual appearance of this skill..." rows="4"></textarea>
                                    <p v-if="errs.description" class="text-red-500 text-xs mt-1">{{ errs.description }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="rounded-xl border border-[#315f68] bg-[#182f34] p-8 form-card shadow-2xl">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="material-symbols-outlined text-primary">military_tech</span>
                                <h3 class="text-xl font-bold text-white font-display uppercase tracking-wider">Mastery
                                    Levels</h3>
                            </div>
                            <p class="text-[#90c1cb] text-sm mb-8">Toggle the available ranks for this skill and define the
                                unique promotion criteria for each.</p>
                            <p v-if="errs.skill_levels" class="text-red-500 text-sm mt-2">{{ errs.skill_levels }}</p>
                            <div class="space-y-6">
                                <div v-for="(level, index) in levels.levels" :key="level.id"
                                    class="group border border-[#224249] rounded-xl p-6 transition-all hover:bg-[#224249]/30">
                                    <div class="flex items-start gap-4">
                                        <div class="pt-1">
                                            <input :checked="isChecked(level.id)"
                                                    @change="toggleLevel(level.id)"
                                                class="w-6 h-6 rounded border-[#315f68] bg-background-dark text-primary focus:ring-primary"
                                                id="lvl-beginner" type="checkbox" :value="level.id" />
                                        </div>
                                        <div class="flex-1 space-y-4">
                                            <div class="flex items-center justify-between">
                                                <label
                                                    class="text-lg font-bold text-white font-display flex items-center gap-2 cursor-pointer"
                                                    for="lvl-beginner">
                                                    {{ level.name }}
                                                    <span
                                                        class="text-xs px-2 py-0.5 bg-primary/20 text-primary rounded-full font-sans uppercase">Level
                                                        0{{ level.order }}</span>
                                                </label>
                                            </div>
                                            <div class="flex flex-col gap-2">
                                                <label class="text-xs font-bold text-[#90c1cb] uppercase">Specific Criteria
                                                    &amp; Milestones</label>
                                                <textarea :value="getCriteria(level.id)"
                                                        :disabled="!isChecked(level.id)"
                                                        @input="updateCriteria(level.id, ($event?.target as HTMLTextAreaElement)?.value)" name="milestones[]"
                                                    class="bg-[#224249] border-[#315f68] text-white text-sm rounded-lg focus:ring-primary focus:border-primary px-4 py-3 placeholder:text-white/20 resize-none w-full"
                                                    placeholder="What must they achieve to reach this rank?" rows="2"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-12 flex items-center justify-between border-t border-[#224249] pt-8 mb-10">
                        <div class="flex items-center gap-2 text-[#90c1cb] text-sm italic">
                            <span class="material-symbols-outlined text-xs">info</span>
                            All skill drafts are stored in the temporary archive until anchored.
                        </div>
                        <div class="flex gap-4">
                            <a href="{{ route('admin.skill') }}"
                                class="px-8 py-3 bg-[#224249] text-white hover:bg-[#315f68] font-bold rounded-lg transition-all">
                                Abandon
                            </a>
                            <button
                                class="flex items-center gap-2 px-10 py-3 bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark font-bold rounded-lg transition-all transform hover:scale-105 shadow-[0_4px_14px_0_rgba(255,215,0,0.39)]">
                                <span class="material-symbols-outlined font-bold">anchor</span>
                                <span>Anchor Skill</span>
                            </button>
                        </div>
                    </div>
                </form>
            </section>
        </main>
    </NuxtLayout>
</template>