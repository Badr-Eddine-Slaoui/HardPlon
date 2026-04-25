<script setup lang="ts">
    import { marked } from "marked";
    import DOMPurify from "dompurify";
    import { useSubmission } from '~~/stores/submission';
    import { useCorrection } from '~~/stores/correction';
    import type { Submission } from '~~/types/submission';
    
    useHead({
        title: 'Student Dashboard - Mission Rewards'
    })

    const query = useRoute().query

    const store = useSubmission();
    const correction = useCorrection();
    const message = ref('')
    
    const submission_type: Ref<string> = ref('solo')
    const selected_submission: Ref<number | null> = ref(null)

    // Filtered submissions that have corrections
    const correctedSubmissions = computed(() => {
        return store.submissions?.filter(s => {
            const hasCorrection = correction.corrections?.some(c => c.submission_id === s.id)
            const typeMatch = (s.student_id != null && submission_type.value === 'solo') || 
                            (s.squad_id != null && submission_type.value === 'squad')
            return hasCorrection && typeMatch
        }) || []
    })

    const toggleSubmissionType = (type: string)=>{
        submission_type.value = type
    }

    const getSubmission = async (id: number) => {
        selected_submission.value = id
        await store.fetchSubmission(id)
        if (store.submission?.brief_id) {
            await correction.fetchCorrection(store.submission.brief_id)
            message.value = DOMPurify.sanitize(marked.parse(correction.correction?.message ?? "") as string)
        }
    }

    const selectSubmission = (id: number)=>{
        selected_submission.value = id
        getSubmission(id)
    }

    onMounted(async() => {
        await store.fetchStudentSubmissions();
        await correction.fetchStudentCorrections();
        message.value = DOMPurify.sanitize(marked.parse(correction.correction?.message ?? "") as string)
        if(query?.brief_id){
            const sub = store.submissions?.find(s => s.brief_id == parseInt(query?.brief_id as string)) as Submission
            if (sub) {
                selectSubmission(sub.id)
                toggleSubmissionType(sub.student_id != null ? "solo" : "squad")
            }
        } else if (correctedSubmissions.value.length > 0) {
            selectSubmission(correctedSubmissions.value[0].id)
        }
    })

    definePageMeta({
        middleware: ['auth', 'student']
    })

</script>

<template>
    <NuxtLayout name="student">
        <main class="flex-1 flex flex-col h-screen">
            <header
                class="h-16 border-b border-slate-200 dark:border-[#224249] bg-white/80 dark:bg-background-dark/80 backdrop-blur-md flex items-center justify-between px-8 shrink-0 z-10">
                <div class="flex items-center gap-4">
                    <h2 class="text-2xl font-bold tracking-tight adventure-title text-primary">
                        Bounty Board
                    </h2>
                    <div class="hidden md:flex items-center gap-2">
                        <span
                            class="px-2 py-0.5 rounded bg-primary/10 text-primary text-[10px] font-bold border border-primary/20">CLASS
                            MISSIONS</span>
                        <span class="h-4 w-[1px] bg-slate-200 dark:bg-[#224249]"></span>
                        <p class="text-xs text-slate-500 dark:text-slate-400 italic">
                            "Wealth, fame, power... the world is waiting."
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div
                        class="flex items-center gap-2 px-3 py-1 bg-slate-100 dark:bg-[#182f34] border border-slate-200 dark:border-[#224249] rounded-lg">
                        <span class="material-symbols-outlined text-pirate-gold text-lg">monetization_on</span>
                        <span class="text-sm font-bold">12,450 ฿</span>
                    </div>
                </div>
            </header>
            <div class="flex flex-1 overflow-hidden">
                <div
                    class="w-96 border-r border-slate-200 dark:border-[#224249] flex flex-col bg-white dark:bg-[#102023]">
                    <div class="p-4 flex gap-2 shrink-0">
                        <button @click="toggleSubmissionType('solo')"
                            :class="`flex-1 py-2 px-4 rounded-lg font-bold text-sm  ${submission_type === 'solo' ? 'bg-primary text-background-dark shadow-lg shadow-primary/20' : 'bg-slate-100 dark:bg-[#224249] text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-[#2d565f]'} transition-all flex items-center justify-center gap-2`">
                            <span class="material-symbols-outlined text-lg">person</span>
                            Solo
                        </button>
                        <button @click="toggleSubmissionType('squad')"
                            :class="`flex-1 py-2 px-4 rounded-lg font-bold text-sm  ${submission_type === 'squad' ? 'bg-primary text-background-dark shadow-lg shadow-primary/20' : 'bg-slate-100 dark:bg-[#224249] text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-[#2d565f]'} transition-all flex items-center justify-center gap-2`">
                            <span class="material-symbols-outlined text-lg">groups</span>
                            Squad
                        </button>
                    </div>
                    <div class="flex-1 overflow-y-auto bounty-scroll p-4 space-y-4">
                        <template v-if="correctedSubmissions.length > 0">
                            <div v-for="submission in correctedSubmissions" :key="submission.id"
                                @click="selectSubmission(submission.id)"
                                :class="['p-4 rounded-xl border transition-all cursor-pointer', 
                                    selected_submission === submission.id ? 'bg-primary/10 border-primary shadow-lg' : 'bg-white dark:bg-[#182f34] border-slate-200 dark:border-[#224249] hover:border-primary/50']">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-500 text-[10px] font-black uppercase tracking-wider">Validated</span>
                                    <span class="text-xs font-bold text-pirate-gold">1,000 ฿</span>
                                </div>
                                <h3 class="adventure-title text-sm font-bold truncate mb-1">{{ submission.brief.title }}</h3>
                                <p class="text-[10px] text-slate-500">{{ (new Date(submission.created_at)).toLocaleDateString() }}</p>
                            </div>
                        </template>
                        <template v-else>
                            <div class="p-8 text-center space-y-4">
                                <div class="size-16 mx-auto rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                                    <span class="material-symbols-outlined text-3xl">sentiment_dissatisfied</span>
                                </div>
                                <p class="text-xs text-slate-500 uppercase font-black">No Rewards Yet</p>
                            </div>
                        </template>
                    </div>
                </div>
                <div class="flex-1 flex flex-col overflow-y-auto">
                    <header
                        class="h-16 border-b border-slate-200 dark:border-[#224249] bg-white/80 dark:bg-background-dark/80 backdrop-blur-md flex items-center justify-between px-8 sticky top-0 z-10 shrink-0">
                        <div class="flex items-center gap-4">
                            <h2 class="text-2xl font-bold tracking-tight adventure-title text-primary">Mission Rewards</h2>
                        </div>
                    </header>
                    
                    <div v-if="store.submission && correction.correction" class="p-8 space-y-10 max-w-5xl mx-auto w-full pb-20">
                        <!-- Header Info -->
                        <div class="flex items-center justify-between gap-6 bg-emerald-500/5 p-6 rounded-2xl border border-emerald-500/10">
                            <div class="flex items-center gap-4">
                                <div class="size-16 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-500">
                                    <span class="material-symbols-outlined text-4xl">workspace_premium</span>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold adventure-title text-emerald-500">{{ store.submission.brief.title }}</h3>
                                    <p class="text-sm text-slate-500">Validated on {{ (new Date(correction.correction.created_at)).toLocaleDateString() }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-slate-500 font-black uppercase">Mission Status</p>
                                <span class="text-lg font-bold text-emerald-500">SUCCESSFUL</span>
                            </div>
                        </div>

                        <!-- Captain's Feedback -->
                        <section class="space-y-4">
                            <div class="flex items-center gap-2 text-pirate-gold">
                                <span class="material-symbols-outlined">auto_stories</span>
                                <h3 class="text-lg font-bold adventure-title uppercase tracking-widest">Captain's Evaluation</h3>
                            </div>
                            <div class="bg-white dark:bg-[#182f34] border border-slate-200 dark:border-[#224249] rounded-xl p-8 shadow-sm parchment-effect">
                                <p class="text-slate-600 dark:text-slate-300 leading-relaxed italic whitespace-pre-line" v-html="message"></p>
                                <div class="mt-6 pt-6 border-t border-slate-100 dark:border-white/5 flex items-center gap-3">
                                    <div class="size-8 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-sm">person</span>
                                    </div>
                                    <p class="text-xs font-bold text-slate-500">Captain {{ store.submission.brief.teacher.first_name }} {{ store.submission.brief.teacher.last_name }}</p>
                                </div>
                            </div>
                        </section>

                        <!-- Skill Mastery -->
                        <section class="space-y-6">
                            <div class="flex items-center gap-2 text-primary">
                                <span class="material-symbols-outlined">military_tech</span>
                                <h3 class="text-lg font-bold adventure-title uppercase tracking-widest">Skill Mastery</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div v-for="detail in correction.correction.correction_details" :key="detail.id"
                                    class="bg-white dark:bg-[#182f34] border border-slate-200 dark:border-[#224249] rounded-xl p-6 transition-all hover:translate-y-[-2px]">
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <h4 class="font-bold text-base">{{ detail.brief_skill_level.skill.title }}</h4>
                                            <p class="text-[10px] text-slate-500 uppercase font-black">{{ detail.brief_skill_level.skill.code }}</p>
                                        </div>
                                        <div :class="['px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest', 
                                            detail.grade === 'EXCELLENT' ? 'bg-excellent-green text-background-dark' : 
                                            detail.grade === 'AVERAGE' ? 'bg-average-yellow text-background-dark' : 'bg-poor-red text-background-dark']">
                                            {{ detail.grade }}
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs">
                                        <span class="text-slate-500">Target Proficiency:</span>
                                        <span class="font-bold text-primary">Level {{ detail.brief_skill_level.level.order }} ({{ detail.brief_skill_level.level.name }})</span>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Artifact Link -->
                        <section class="space-y-4">
                            <div class="flex items-center gap-2 text-slate-500">
                                <span class="material-symbols-outlined">folder_open</span>
                                <h3 class="text-lg font-bold adventure-title uppercase tracking-widest">Mission Artifacts</h3>
                            </div>
                            <div class="bg-black/20 rounded-xl p-4 border border-white/5">
                                <NuxtLink target="_blank" :to="store.submission.link" class="flex items-center justify-between p-3 hover:bg-white/5 rounded-lg transition-all group">
                                    <div class="flex items-center gap-3">
                                        <span class="material-symbols-outlined text-primary">link</span>
                                        <span class="text-sm font-mono text-slate-300">repository_link</span>
                                    </div>
                                    <span class="material-symbols-outlined text-sm text-slate-500 group-hover:text-primary transition-all">open_in_new</span>
                                </NuxtLink>
                            </div>
                        </section>
                    </div>
                    
                    <div v-else class="flex-1 flex flex-col items-center justify-center p-8 text-center space-y-4">
                        <div class="size-24 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-300">
                            <span class="material-symbols-outlined text-5xl">inventory_2</span>
                        </div>
                        <div class="max-w-xs">
                            <h3 class="text-lg font-bold adventure-title uppercase">Select a Reward</h3>
                            <p class="text-sm text-slate-500">Choose a validated mission from the bounty log to view your mastery and feedback.</p>
                        </div>
                    </div>

                    <footer
                        class="mt-auto border-t border-slate-200 dark:border-[#224249] bg-white dark:bg-[#102023] px-8 py-3 flex items-center justify-between text-[11px] font-medium text-slate-500 uppercase tracking-widest shrink-0">
                        <div class="flex gap-6">
                            <span class="flex items-center gap-1.5"><span
                                    class="size-2 bg-emerald-500 rounded-full shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                                Log: Secure</span>
                            <span class="flex items-center gap-1.5"><span
                                    class="material-symbols-outlined text-[14px]">anchor</span> Marine HQ Archive</span>
                        </div>
                        <div class="flex gap-4">
                            <span class="text-primary/70">V 2.4.0-ARABASTA</span>
                            <a class="hover:text-primary transition-colors flex items-center gap-1" href="#">
                                <span class="material-symbols-outlined text-[14px]">help</span> Archive Help
                            </a>
                        </div>
                    </footer>
                </div>
            </div>
        </main>
    </NuxtLayout>
</template>