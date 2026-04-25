<script setup lang="ts">
    import { useBrief } from '~~/stores/brief';
    import { useLanguage } from '~~/stores/language';
    import { useProblem } from '~~/stores/problem';
    import type { BriefSkillLevel } from '~~/types/brief';

    useHead({
        title: `Teacher Dashboard - Refine Marine Challenge`
    })

    const briefStore = useBrief();
    const langStore = useLanguage();
    const problemStore = useProblem();
    const router = useRouter();
    const route = useRoute();

    const briefSkillLevels = ref<BriefSkillLevel[]>([]);

    const form = reactive({
        brief_id: null as number | null,
        brief_skill_level_id: null as number | null,
        language_id: null as number | null,
        title: '',
        description: '',
        hidden_header: '',
        hidden_footer: '',
        skeleton_code: '',
        order_index: 0
    })

    const errors = ref<any>({});
    const isLoading = ref(true);

    onMounted(async () => {
        const id = Number(route.params.id);
        await Promise.all([
            briefStore.fetchBriefs(1, 100),
            langStore.fetchAllLanguages(),
            problemStore.fetchProblem(id)
        ]);

        if (problemStore.problem) {
            Object.assign(form, {
                brief_id: problemStore.problem.brief_id,
                brief_skill_level_id: problemStore.problem.brief_skill_level_id,
                language_id: problemStore.problem.language_id,
                title: problemStore.problem.title,
                description: problemStore.problem.description,
                hidden_header: problemStore.problem.hidden_header || '',
                hidden_footer: problemStore.problem.hidden_footer || '',
                skeleton_code: problemStore.problem.skeleton_code || '',
                order_index: problemStore.problem.order_index
            });

            if (form.brief_id) {
                briefSkillLevels.value = await briefStore.fetchBriefSkillLevels(form.brief_id);
            }
        }
        isLoading.value = false;
    });

    watch(() => form.brief_id, async (newBriefId) => {
        form.brief_skill_level_id = null;
        if (newBriefId) {
            briefSkillLevels.value = await briefStore.fetchBriefSkillLevels(newBriefId);
        } else {
            briefSkillLevels.value = [];
        }
    });

    async function submit() {
        const id = Number(route.params.id);
        const res = await problemStore.updateProblem(id, form);
        if (res.success) {
            router.push('/teacher/problems');
        } else {
            errors.value = res.errors || {};
        }
    }

    definePageMeta({
        middleware: ['auth', 'teacher']
    })
</script>

<template>
    <NuxtLayout name="teacher">
        <main v-if="!isLoading" class="flex-1 overflow-y-auto p-10 custom-scrollbar">
            <div class="max-w-4xl mx-auto">
                <div class="mb-10">
                    <NuxtLink to="/teacher/problems" class="text-primary hover:text-primary/80 flex items-center gap-2 mb-4 transition-colors">
                        <span class="material-symbols-outlined">arrow_back</span>
                        <span class="text-xs font-bold uppercase tracking-widest">Back to Logbook</span>
                    </NuxtLink>
                    <h2 class="text-3xl font-bold text-white font-display mb-1 tracking-tight adventure-title">Refine Challenge</h2>
                    <p class="text-[#90c1cb]">Adjust the logic and trials of this challenge.</p>
                </div>

                <div class="rounded-2xl border border-[#315f68] bg-[#182f34] p-8 shadow-2xl relative overflow-hidden parchment-effect">
                    <div class="absolute top-0 right-0 p-8 opacity-5 pointer-events-none">
                        <span class="material-symbols-outlined text-[120px] text-primary">edit</span>
                    </div>

                    <form @submit.prevent="submit" class="space-y-8 relative">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Brief Selection -->
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-primary uppercase tracking-widest ml-1">Target Mission (Brief)</label>
                                <select v-model="form.brief_id" class="w-full bg-[#14262b] border border-[#224249] rounded-lg py-3 px-4 text-white focus:outline-none focus:border-primary/50 focus:ring-1 focus:ring-primary/20 transition-all text-sm">
                                    <option :value="null" disabled>Select a Mission</option>
                                    <option v-for="brief in briefStore.briefs" :key="brief.id" :value="brief.id">
                                        {{ brief.title }}
                                    </option>
                                </select>
                                <p v-if="errors.brief_id" class="text-red-400 text-[10px] font-bold mt-1 ml-1 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-xs">error</span>
                                    {{ errors.brief_id[0] }}
                                </p>
                            </div>

                            <!-- Skill Level Selection -->
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-primary uppercase tracking-widest ml-1">Target Skill Rank</label>
                                <select v-model="form.brief_skill_level_id" :disabled="!form.brief_id" class="w-full bg-[#14262b] border border-[#224249] rounded-lg py-3 px-4 text-white focus:outline-none focus:border-primary/50 focus:ring-1 focus:ring-primary/20 transition-all text-sm disabled:opacity-50">
                                    <option :value="null" disabled>Select Rank</option>
                                    <option v-for="bsl in briefSkillLevels" :key="bsl.id" :value="bsl.id">
                                        {{ bsl.skill?.title }} ({{ bsl.level?.name }})
                                    </option>
                                </select>
                                <p v-if="errors.brief_skill_level_id" class="text-red-400 text-[10px] font-bold mt-1 ml-1 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-xs">error</span>
                                    {{ errors.brief_skill_level_id[0] }}
                                </p>
                            </div>

                            <!-- Language Selection -->
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-primary uppercase tracking-widest ml-1">Mastery Tongue (Language)</label>
                                <select v-model="form.language_id" class="w-full bg-[#14262b] border border-[#224249] rounded-lg py-3 px-4 text-white focus:outline-none focus:border-primary/50 focus:ring-1 focus:ring-primary/20 transition-all text-sm">
                                    <option :value="null" disabled>Select Language</option>
                                    <option v-for="lang in langStore.all_languages" :key="lang.id" :value="lang.id">
                                        {{ lang.name }}
                                    </option>
                                </select>
                                <p v-if="errors.language_id" class="text-red-400 text-[10px] font-bold mt-1 ml-1 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-xs">error</span>
                                    {{ errors.language_id[0] }}
                                </p>
                            </div>

                            <!-- Order Index -->
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-primary uppercase tracking-widest ml-1">Challenge Rank (Order Index)</label>
                                <input v-model="form.order_index" type="number" min="0" class="w-full bg-[#14262b] border border-[#224249] rounded-lg py-3 px-4 text-white focus:outline-none focus:border-primary/50 focus:ring-1 focus:ring-primary/20 transition-all text-sm">
                                <p v-if="errors.order_index" class="text-red-400 text-[10px] font-bold mt-1 ml-1 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-xs">error</span>
                                    {{ errors.order_index[0] }}
                                </p>
                            </div>
                        </div>

                        <!-- Title -->
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-primary uppercase tracking-widest ml-1">Challenge Title</label>
                            <input v-model="form.title" type="text" placeholder="e.g., The Grand Navigation Logic" class="w-full bg-[#14262b] border border-[#224249] rounded-lg py-3 px-4 text-white placeholder:text-slate-600 focus:outline-none focus:border-primary/50 focus:ring-1 focus:ring-primary/20 transition-all text-sm">
                            <p v-if="errors.title" class="text-red-400 text-[10px] font-bold mt-1 ml-1 flex items-center gap-1">
                                <span class="material-symbols-outlined text-xs">error</span>
                                {{ errors.title[0] }}
                            </p>
                        </div>

                        <!-- Description -->
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-primary uppercase tracking-widest ml-1">Challenge Description</label>
                            <textarea v-model="form.description" rows="4" placeholder="Describe the trial..." class="w-full bg-[#14262b] border border-[#224249] rounded-lg py-3 px-4 text-white placeholder:text-slate-600 focus:outline-none focus:border-primary/50 focus:ring-1 focus:ring-primary/20 transition-all text-sm resize-none"></textarea>
                            <p v-if="errors.description" class="text-red-400 text-[10px] font-bold mt-1 ml-1 flex items-center gap-1">
                                <span class="material-symbols-outlined text-xs">error</span>
                                {{ errors.description[0] }}
                            </p>
                        </div>

                        <!-- Code Sections -->
                        <div class="grid grid-cols-1 gap-8">
                             <div class="space-y-2">
                                <label class="block text-[10px] font-black text-primary uppercase tracking-widest ml-1">Skeleton Code (Visible to Crew)</label>
                                <textarea v-model="form.skeleton_code" rows="6" class="w-full bg-[#14262b] border border-[#224249] rounded-lg py-3 px-4 text-white font-mono text-xs focus:outline-none focus:border-primary/50 focus:ring-1 focus:ring-primary/20 transition-all resize-none"></textarea>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Hidden Header (Prepended Code)</label>
                                    <textarea v-model="form.hidden_header" rows="4" class="w-full bg-[#14262b] border border-[#224249] rounded-lg py-3 px-4 text-white font-mono text-xs focus:outline-none focus:border-primary/50 focus:ring-1 focus:ring-primary/20 transition-all resize-none"></textarea>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Hidden Footer (Appended Code)</label>
                                    <textarea v-model="form.hidden_footer" rows="4" class="w-full bg-[#14262b] border border-[#224249] rounded-lg py-3 px-4 text-white font-mono text-xs focus:outline-none focus:border-primary/50 focus:ring-1 focus:ring-primary/20 transition-all resize-none"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-6 border-t border-[#224249]">
                            <div class="flex items-center gap-2 text-[#5a8b95]">
                                <span class="material-symbols-outlined text-sm animate-bounce">anchor</span>
                                <span class="text-[10px] uppercase tracking-widest font-bold">Standard Marine Protocol</span>
                            </div>
                            <button type="submit" class="px-10 py-4 bg-primary hover:brightness-110 text-background-dark font-black rounded-lg transition-all transform hover:scale-105 shadow-[0_4px_20px_0_rgba(13,204,242,0.3)] flex items-center gap-2">
                                <span class="material-symbols-outlined">save</span>
                                <span>Save Changes</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
        <div v-else class="flex-1 flex items-center justify-center">
            <div class="size-12 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
        </div>
    </NuxtLayout>
</template>
