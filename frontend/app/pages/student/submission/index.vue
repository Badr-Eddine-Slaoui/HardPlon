<script setup lang="ts">
    import { useSubmission } from '~~/stores/submission';
    import { useCorrection } from '~~/stores/correction';
    import type { Submission } from '~~/types/submission';
    useHead({
        title: 'Student Dashboard - Bounty Board'
    })

    const query = useRoute().query

    const store = useSubmission();
    const correction = useCorrection();
    const submission_type: Ref<string> = ref('solo')
    const selected_submission: Ref<number | null> = ref(null)
    const submissions_count: Ref<number> = ref(0)

    const toggleSubmissionType = (type: string)=>{
        submission_type.value = type
        submissions_count.value = store.submissions?.filter(s => (s.student_id != null && type == "solo") || (s.squad_id != null && type == "squad")).length as number
    }

    const getSubmission = async (id: number) => {
        selected_submission.value = id
        await store.fetchSubmission(id)
        await correction.fetchCorrection(store.submission?.brief_id as number)
    }

    const selectSubmission = (id: number)=>{
        selected_submission.value = id
        getSubmission(id)
    }

    onMounted(async() => {
        await store.fetchStudentSubmissions();
        await correction.fetchStudentCorrections();

        if(query?.brief_id){
            const submission = store.submissions?.find(s => s.brief_id == parseInt(query?.brief_id as string)) as Submission
            selectSubmission(submission?.id as number)
            toggleSubmissionType(submission?.student_id != null ? "solo" : "squad")
        }else{
            toggleSubmissionType('solo')
            selectSubmission(store.submissions?.find(s => s.student_id != null)?.id as number)
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
                        <template v-if="submissions_count as number > 0">
                            <template v-for="submission in store.submissions" :key="submission.id">
                                <template v-if="(submission.squad_id != null && submission_type === 'squad') || (!submission.student_id != null && submission_type === 'solo')">
                                    <div v-if="selected_submission === submission.id" class="p-4 bg-primary/5 border-l-4 border-primary cursor-pointer">
                                        <div class="flex justify-between items-start mb-1">
                                            <h3 class="text-sm font-bold adventure-title text-primary truncate">{{ submission.brief.title }}</h3>
                                            <span class="text-[10px] text-slate-400">{{ submission.created_at }}</span>
                                        </div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-1 mb-2">{{ submission.brief.description }}</p>
                                        <span v-if="correction?.corrections?.find(c => c.brief_id === submission.brief_id) === undefined"
                                                class="px-2 py-0.5 rounded bg-yellow-500/20 text-yellow-500 text-[10px] font-black uppercase tracking-wider">Not Corrected</span>
                                        <span v-else
                                            class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-500 text-[10px] font-black uppercase tracking-wider">Corrected</span>
                                    </div>
                                    <div v-else @click="selectSubmission(submission.id)"
                                        class="p-4 hover:bg-slate-100 dark:hover:bg-[#182f34] cursor-pointer transition-colors group">
                                        <div class="flex justify-between items-start mb-1">
                                            <h3 class="text-sm font-bold adventure-title group-hover:text-primary truncate">{{ submission.brief.title }}</h3>
                                            <span class="text-[10px] text-slate-400">{{ submission.created_at }}</span>
                                        </div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-1 mb-2">{{ submission.brief.description }}</p>
                                        <span v-if="correction?.corrections?.find(c => c.brief_id === submission.brief_id) === undefined"
                                                class="px-2 py-0.5 rounded bg-yellow-500/20 text-yellow-500 text-[10px] font-black uppercase tracking-wider">Not Corrected</span>
                                        <span v-else
                                            class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-500 text-[10px] font-black uppercase tracking-wider">Corrected</span>
                                    </div>
                                </template>
                            </template>
                        </template>
                        <template v-else>
                            <div
                                class="col-start-2 bg-white dark:bg-[#1a2e33] border border-slate-200 dark:border-[#224249] p-6 rounded-2xl h-[30vh]">
                                <div class="space-y-4 flex flex-col justify-center items-center gap-y-5 h-full">
                                    <div
                                        class="size-12 mx-auto rounded-xl bg-red-500/10 flex items-center justify-center text-red-500">
                                        <span class="material-symbols-outlined">info</span>
                                    </div>
                                    <div class="text-center">
                                        <h3 class="font-bold text-lg">No Missions Found</h3>
                                        <p class="text-sm text-slate-500 dark:text-[#90c1cb]">You don't have any missions currently, please wait until your commander assigns you a mission</p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                <div class="flex-1 flex flex-col overflow-y-auto">
                    <header
                        class="h-16 border-b border-slate-200 dark:border-[#224249] bg-white/80 dark:bg-background-dark/80 backdrop-blur-md flex items-center justify-between px-8 sticky top-0 z-10 shrink-0">
                        <div class="flex items-center gap-4">
                            <h2 class="text-2xl font-bold tracking-tight adventure-title text-primary">Mission Review</h2>
                        </div>
                    </header>
                    <div class="p-8 space-y-8 max-w-5xl mx-auto w-full">
                        <section class="space-y-4">
                            <div class="flex items-center gap-4">
                                <div
                                    class="size-12 rounded-xl bg-slate-100 dark:bg-[#182f34] border border-slate-200 dark:border-[#224249] flex items-center justify-center text-primary">
                                    <span class="material-symbols-outlined text-2xl">verified</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-xl font-bold adventure-title">{{ store.submission?.brief?.title }}</h3>
                                        <span v-if="correction?.corrections?.find(c => c.brief_id === store.submission?.brief_id) !== undefined"
                                            class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-excellent-green/20 text-excellent-green border border-excellent-green/30">Corrected</span>
                                        <span v-else
                                            class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-pirate-gold/20 text-pirate-gold border border-pirate-gold/30">Not Corrected</span>
                                    </div>
                                    <p class="text-sm text-slate-500">Submitted by <span
                                            class="text-primary font-bold">{{ store.submission?.student?.first_name }} {{ store.submission?.student?.last_name }}</span> • Graded by <span
                                            class="text-pirate-gold font-bold">Capt. {{ store.submission?.brief?.teacher?.first_name }} {{ store.submission?.brief?.teacher?.last_name }}</span></p>
                                </div>
                            </div>
                            <div
                                class="bg-white dark:bg-[#182f34] border border-slate-200 dark:border-[#224249] rounded-xl p-6 parchment-effect shadow-sm">
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3">Student
                                    Submission</p>
                                <div class="prose prose-sm dark:prose-invert max-w-none">
                                    <p class="text-slate-600 dark:text-slate-300 mb-4 italic leading-relaxed">
                                        "{{ store.submission?.message }}"
                                    </p>
                                    <div class="flex flex-wrap gap-4 pt-4 border-t border-slate-100 dark:border-[#224249]">
                                        <NuxtLink target="_blank" v-for="link in store.submission?.links" class="flex items-center gap-2 text-xs font-bold text-slate-400 bg-slate-400/5 px-3 py-2 rounded-lg border border-slate-400/20 cursor-pointer"
                                            :to="Object.values(link)[0]">
                                            <span class="material-symbols-outlined text-sm">link</span>
                                            {{ Object.keys(link)[0] }}
                                        </NuxtLink>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <section v-if="correction?.correction" class="space-y-6 pb-12">
                            <div class="flex items-center gap-2 text-pirate-gold">
                                <span class="material-symbols-outlined">auto_stories</span>
                                <h3 class="text-lg font-bold adventure-title uppercase tracking-widest">Captain's Log
                                    Summary</h3>
                            </div>
                            <div class="space-y-8">
                                <div class="space-y-3">
                                    <label class="text-xs font-black uppercase tracking-widest text-slate-400">Captain's
                                        Message</label>
                                    <div
                                        class="w-full dark:bg-[#1a2b2e] rounded-xl p-6 text-sm parchment-static border border-pirate-gold/20 shadow-inner">
                                        <p class="text-slate-300 leading-relaxed font-medium">
                                            "{{ correction?.correction?.message }}"
                                        </p>
                                    </div>
                                </div>
                                <div class="space-y-4">
                                    <label class="text-xs font-black uppercase tracking-widest text-slate-400">Skill
                                        Proficiency Evaluation</label>
                                    <div class="grid grid-cols-1 gap-3">
                                        <div v-for="detail in correction?.correction?.correction_details"
                                            class="bg-white dark:bg-[#182f34] border border-slate-200 dark:border-[#224249] rounded-xl p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                            <div>
                                                <h4 class="font-bold text-base tracking-wide">{{ detail?.brief_skill_level?.skill?.title }}</h4>
                                                <p class="text-[10px] text-slate-500 uppercase font-bold">Target: Level {{ detail?.brief_skill_level?.level.order }}
                                                    ({{ detail?.brief_skill_level?.level.name }})</p>
                                            </div>
                                            <div class="flex items-center">
                                                <div v-if="detail?.grade === 'EXCELLENT'"
                                                    class="bg-excellent-green text-background-dark px-6 py-2 rounded-lg text-[11px] font-black uppercase tracking-widest shadow-[0_0_15px_rgba(107,193,103,0.3)]">
                                                    EXCELLENT
                                                </div>
                                                <div v-else-if="detail?.grade === 'AVERAGE'"
                                                    class="bg-average-yellow text-background-dark px-6 py-2 rounded-lg text-[11px] font-black uppercase tracking-widest shadow-[0_0_15px_rgba(255,217,61,0.3)]">
                                                    AVERAGE
                                                </div>
                                                <div v-else-if="detail?.grade === 'POOR'"
                                                    class="bg-poor-red text-background-dark px-6 py-2 rounded-lg text-[11px] font-black uppercase tracking-widest shadow-[0_0_15px_rgba(255,98,98,0.3)]">
                                                    POOR
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <section v-else class="space-y-8">
                            <div class="space-y-3">
                                <label class="text-xs font-black uppercase tracking-widest text-slate-400">Submission Not Corrected</label>
                                <div
                                    class="w-full dark:bg-[#1a2b2e] rounded-xl p-6 text-sm parchment-static border border-pirate-gold/20 shadow-inner">
                                    <p class="text-slate-300 leading-relaxed font-medium">
                                        "Your submission has not been corrected yet. Please check back later."
                                    </p>
                                </div>
                            </div>
                        </section>
                    </div>
                    <footer
                        class="mt-auto border-t border-slate-200 dark:border-[#224249] bg-white dark:bg-[#102023] px-8 py-3 flex items-center justify-between text-[11px] font-medium text-slate-500 uppercase tracking-widest shrink-0">
                        <div class="flex gap-6">
                            <span class="flex items-center gap-1.5"><span
                                    class="size-2 bg-emerald-500 rounded-full shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                                Signal: Logged On</span>
                            <span class="flex items-center gap-1.5"><span
                                    class="material-symbols-outlined text-[14px]">anchor</span> Fleet: Marine HQ
                                Server</span>
                        </div>
                        <div class="flex gap-4">
                            <span class="text-primary/70">V 2.4.0-ARABASTA</span>
                            <a class="hover:text-primary transition-colors flex items-center gap-1" href="#">
                                <span class="material-symbols-outlined text-[14px]">help</span> Help Center
                            </a>
                        </div>
                    </footer>
                </div>
            </div>
        </main>
    </NuxtLayout>
</template>