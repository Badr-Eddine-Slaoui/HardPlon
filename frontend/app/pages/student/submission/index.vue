<script setup lang="ts">
    import { marked } from "marked";
    import DOMPurify from "dompurify";
    import { useSubmission } from '~~/stores/submission';
    import { useCorrection } from '~~/stores/correction';
    import { useProblemSubmission } from '~~/stores/problem_submission';
    import type { Submission } from '~~/types/submission';
    
    useHead({
        title: 'Student Dashboard - Bounty Board'
    })

    const query = useRoute().query

    const store = useSubmission();
    const correction = useCorrection();
    const problemStore = useProblemSubmission();
    const message = ref('')
    
    const submission_type: Ref<string> = ref('solo')
    const selected_submission: Ref<number | null> = ref(null)
    const submissions_count: Ref<number> = ref(0)

    // Problem Solving State
    const isSolving = ref(false)
    const currentProblemIdx = ref(0)
    const problemAnswers = ref<Record<number, string>>({})

    const toggleSubmissionType = (type: string)=>{
        submission_type.value = type
        submissions_count.value = store.submissions?.filter(s => (s.student_id != null && type == "solo") || (s.squad_id != null && type == "squad")).length as number
    }

    const getSubmission = async (id: number) => {
        selected_submission.value = id
        await store.fetchSubmission(id)
        if (store.submission?.brief_id) {
            await correction.fetchCorrection(store.submission.brief_id)
        }
        
        message.value = DOMPurify.sanitize(marked.parse(correction.correction?.message ?? "") as string)

        // Initialize problem answers if needed
        if (store.submission?.brief?.problems) {
            store.submission.brief.problems.forEach(p => {
                if (!problemAnswers.value[p.id]) {
                    problemAnswers.value[p.id] = p.skeleton_code || ''
                }
            })
        }
    }

    const selectSubmission = (id: number)=>{
        isSolving.value = false
        selected_submission.value = id
        getSubmission(id)
    }

    const startProblems = () => {
        isSolving.value = true
        currentProblemIdx.value = 0
    }

    const nextProblem = () => {
        if (store?.submission?.brief?.problems && currentProblemIdx.value < store.submission.brief.problems.length - 1) {
            currentProblemIdx.value++
        }
    }

    const prevProblem = () => {
        if (currentProblemIdx.value > 0) {
            currentProblemIdx.value--
        }
    }

    const submitAllProblems = async () => {
        if (!store.submission) return

        const submissions = Object.entries(problemAnswers.value).map(([id, code]) => ({
            problem_id: parseInt(id),
            code
        }))

        const res = await problemStore.storeProblemSubmissions(store.submission.id, submissions)
        if (res.success) {
            isSolving.value = false
            await getSubmission(store.submission.id)
        }
    }

    onMounted(async() => {
        await store.fetchStudentSubmissions();
        await correction.fetchStudentCorrections();

        if(query?.brief_id){
            const sub = store.submissions?.find(s => s.brief_id == parseInt(query?.brief_id as string)) as Submission
            if (sub) {
                selectSubmission(sub.id)
                toggleSubmissionType(sub.student_id != null ? "solo" : "squad")
            }
        }else{
            toggleSubmissionType('solo')
            const firstSub = store.submissions?.find(s => s.student_id != null)
            if (firstSub) selectSubmission(firstSub.id)
        }
        
        if (correction.correction) {
            message.value = DOMPurify.sanitize(marked.parse(correction.correction.message) as string)
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

                    <!-- Case 1: Processing -->
                    <div v-if="store.submission && (store.submission.evaluation_job?.status === 'pending' || store.submission.evaluation_job?.status === 'processing')" 
                        class="flex-1 flex flex-col items-center justify-center p-8 text-center space-y-6">
                        <div class="relative">
                            <div class="size-32 rounded-full border-4 border-primary/20 border-t-primary animate-spin"></div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="material-symbols-outlined text-4xl text-primary animate-pulse">radar</span>
                            </div>
                        </div>
                        <div class="max-w-md">
                            <h3 class="text-2xl font-bold adventure-title text-primary mb-2">Analyzing Treasure...</h3>
                            <p class="text-slate-500 dark:text-slate-400">
                                The Fleet Captain is currently inspecting your repository. 
                                This process takes a few moments to verify your architecture and test cases.
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <span class="px-3 py-1 rounded-full bg-primary/10 text-primary text-[10px] font-bold uppercase animate-bounce">Evaluating Code</span>
                            <span class="px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 text-[10px] font-bold uppercase">Awaiting Problems</span>
                        </div>
                    </div>

                    <!-- Case 2: Solving Problems -->
                    <div v-else-if="store.submission && isSolving" class="flex-1 flex flex-col">
                        <div class="p-8 max-w-5xl mx-auto w-full flex-1 flex flex-col">
                            <div class="mb-8 flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="size-10 rounded-lg bg-pirate-gold/10 border border-pirate-gold/20 flex items-center justify-center text-pirate-gold">
                                        <span class="material-symbols-outlined">quiz</span>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold adventure-title uppercase tracking-wide">Problem Challenge</h3>
                                        <p class="text-xs text-slate-500 uppercase font-black">Question {{ currentProblemIdx + 1 }} of {{ store.submission.brief?.problems?.length }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button @click="prevProblem" :disabled="currentProblemIdx === 0"
                                        class="p-2 rounded-lg bg-slate-100 dark:bg-[#182f34] text-slate-500 hover:text-primary disabled:opacity-30 disabled:cursor-not-allowed transition-all">
                                        <span class="material-symbols-outlined">arrow_back</span>
                                    </button>
                                    <button @click="nextProblem" :disabled="currentProblemIdx === (store.submission.brief?.problems?.length ?? 0) - 1"
                                        class="p-2 rounded-lg bg-slate-100 dark:bg-[#182f34] text-slate-500 hover:text-primary disabled:opacity-30 disabled:cursor-not-allowed transition-all">
                                        <span class="material-symbols-outlined">arrow_forward</span>
                                    </button>
                                </div>
                            </div>

                            <div v-if="store.submission.brief?.problems?.[currentProblemIdx]" class="flex-1 grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                                <!-- Problem Description -->
                                <div class="space-y-6 overflow-y-auto pr-4 bounty-scroll max-h-[60vh]">
                                    <div class="bg-white dark:bg-[#182f34] border border-slate-200 dark:border-[#224249] rounded-xl p-6 shadow-sm parchment-effect">
                                        <h4 class="text-lg font-bold text-primary mb-4 flex items-center gap-2">
                                            <span class="material-symbols-outlined text-sm">terminal</span>
                                            {{ store?.submission?.brief?.problems[currentProblemIdx]?.title }}
                                        </h4>
                                        <div class="prose prose-sm dark:prose-invert max-w-none text-slate-300 mb-6">
                                            <p>{{ store?.submission?.brief?.problems[currentProblemIdx]?.description }}</p>
                                        </div>
                                        
                                        <div v-if="store?.submission?.brief?.problems[currentProblemIdx]?.test_cases?.length" class="space-y-3">
                                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-500">Test Cases</p>
                                            <div v-for="tc in store?.submission?.brief?.problems[currentProblemIdx]?.test_cases" :key="tc.id"
                                                class="p-3 bg-black/20 rounded-lg border border-white/5 text-xs font-mono">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="text-primary opacity-50">Input:</span>
                                                    <span>{{ tc.input }}</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-emerald-500 opacity-50">Expect:</span>
                                                    <span>{{ tc.expected_output }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Code Editor (Simple Textarea) -->
                                <div class="flex flex-col h-full min-h-[400px]">
                                    <div class="flex-1 bg-[#0a1416] border border-slate-800 rounded-xl overflow-hidden flex flex-col">
                                        <div class="bg-slate-900 px-4 py-2 border-b border-slate-800 flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <span class="size-2 rounded-full bg-red-500"></span>
                                                <span class="size-2 rounded-full bg-yellow-500"></span>
                                                <span class="size-2 rounded-full bg-green-500"></span>
                                                <span class="ml-2 text-[10px] font-mono text-slate-500 uppercase">{{ store?.submission?.brief?.problems[currentProblemIdx]?.language?.name }}</span>
                                            </div>
                                            <span class="text-[10px] font-mono text-slate-500 italic">solution</span>
                                        </div>
                                        <textarea v-model="problemAnswers[store?.submission?.brief?.problems[currentProblemIdx]?.id as number]"
                                            class="flex-1 w-full bg-transparent p-6 font-mono text-sm text-emerald-500 focus:ring-0 border-none resize-none placeholder:text-slate-800"
                                            placeholder="// Write your solution here..."></textarea>
                                    </div>
                                    <div class="mt-4 flex items-center justify-between gap-4">
                                        <p class="text-[10px] text-slate-500 italic flex items-center gap-1">
                                            <span class="material-symbols-outlined text-sm">info</span>
                                            Changes are saved locally as you type.
                                        </p>
                                        <button v-if="currentProblemIdx === (store?.submission?.brief?.problems?.length ?? 0) - 1"
                                            @click="submitAllProblems"
                                            class="bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark px-6 py-3 rounded-xl font-black text-sm uppercase tracking-widest transition-all shadow-lg flex items-center gap-2">
                                            <span class="material-symbols-outlined">send</span>
                                            Submit All Answers
                                        </button>
                                        <button v-else @click="nextProblem"
                                            class="bg-primary hover:bg-primary/80 text-background-dark px-6 py-3 rounded-xl font-black text-sm uppercase tracking-widest transition-all shadow-lg flex items-center gap-2">
                                            Next Problem
                                            <span class="material-symbols-outlined">arrow_forward</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Case 3: Ready for Problems -->
                    <div v-else-if="store.submission && store.submission.evaluation_job?.status === 'completed' && store.submission.problem_submissions?.length === 0"
                        class="flex-1 flex flex-col items-center justify-center p-8 text-center space-y-8">
                        <div class="relative">
                            <div class="size-32 rounded-3xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center animate-bounce">
                                <span class="material-symbols-outlined text-6xl text-emerald-500">task_alt</span>
                            </div>
                            <div class="absolute -top-2 -right-2 px-3 py-1 bg-pirate-gold text-background-dark text-[10px] font-black rounded-full shadow-lg">STAGE 1 CLEAR</div>
                        </div>
                        <div class="max-w-md">
                            <h3 class="text-3xl font-bold adventure-title text-emerald-500 mb-3">Submission Done!</h3>
                            <p class="text-slate-500 dark:text-slate-400 mb-8 leading-relaxed">
                                Your mission repository has been successfully delivered and verified. 
                                However, to complete the evaluation and receive your bounty, you must now solve the logic problems.
                            </p>
                            <button @click="startProblems"
                                class="bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark px-10 py-5 rounded-2xl font-black text-xl uppercase tracking-widest transition-all shadow-[0_10px_30px_rgba(212,175,55,0.3)] hover:-translate-y-1 hover:shadow-[0_15px_40px_rgba(212,175,55,0.4)] flex items-center gap-3 border border-pirate-gold-dark/30">
                                <span class="material-symbols-outlined text-3xl">swords</span>
                                Start Challenges
                            </button>
                        </div>
                    </div>

                    <!-- Case 4: Default (Results/Correction) -->
                    <div v-else-if="store.submission" class="p-8 space-y-8 max-w-5xl mx-auto w-full pb-20">
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
                                        <NuxtLink target="_blank" :to="store.submission?.link" class="flex items-center gap-2 text-xs font-bold text-slate-400 bg-slate-400/5 px-3 py-2 rounded-lg border border-slate-400/20 cursor-pointer">
                                            <span class="material-symbols-outlined text-sm">link</span>
                                            Repository Link
                                        </NuxtLink>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Problem Submissions Results -->
                        <section v-if="store.submission?.problem_submissions?.length" class="space-y-4">
                            <div class="flex items-center gap-2 text-primary">
                                <span class="material-symbols-outlined">terminal</span>
                                <h3 class="text-lg font-bold adventure-title uppercase tracking-widest">Logic Results</h3>
                            </div>
                            <div class="grid grid-cols-1 gap-4">
                                <div v-for="(ps, index) in store.submission.problem_submissions" :key="ps.id"
                                    class="bg-white dark:bg-[#182f34] border border-slate-200 dark:border-[#224249] rounded-xl p-6 flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <div :class="['size-10 rounded-lg flex items-center justify-center border transition-all', 
                                            store?.submission?.problem_submission_job?.status === 'completed' ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-500' : 
                                            store?.submission?.problem_submission_job?.status === 'failed' ? 'bg-red-500/10 border-red-500/20 text-red-500' : 
                                            'bg-slate-100 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-500 animate-pulse']">
                                            <span class="material-symbols-outlined text-xl">{{ store?.submission?.problem_submission_job?.status === 'completed' ? 'check_circle' : store?.submission?.problem_submission_job?.status === 'failed' ? 'error' : 'schedule' }}</span>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-sm">{{ ps.problem?.title }}</h4>
                                            <p class="text-[10px] text-slate-500 uppercase">{{ ps.problem?.language?.name }} • {{ store?.submission?.problem_submission_job?.status }}</p>
                                        </div>
                                    </div>
                                    <div v-if="store?.submission?.problem_submission_job?.result?.submissions[index]?.score" class="text-right">
                                        <p class="text-[10px] text-slate-500 uppercase font-black">Score</p>
                                        <p class="text-xl font-bold" :class="store?.submission?.problem_submission_job?.result?.submissions[index]?.score as number >= 7 ? 'text-emerald-500' : store?.submission?.problem_submission_job?.result?.submissions[index]?.score as number >= 4 ? 'text-yellow-500' : 'text-red-500'">{{ store?.submission?.problem_submission_job?.result?.submissions[index]?.score }}/10</p>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section v-else class="space-y-8">
                            <div class="space-y-3">
                                <label class="text-xs font-black uppercase tracking-widest text-slate-400">Submission Status</label>
                                <div
                                    class="w-full dark:bg-[#1a2b2e] rounded-xl p-6 text-sm parchment-static border border-pirate-gold/20 shadow-inner">
                                    <p class="text-slate-300 leading-relaxed font-medium">
                                        "Your submission has been received and logic challenges are completed. A Fleet Captain will review your performance shortly. Keep your compass steady!"
                                    </p>
                                </div>
                            </div>
                        </section>
                    </div>

                    <!-- Case 5: Empty State -->
                    <div v-else class="flex-1 flex flex-col items-center justify-center p-8 text-center h-full">
                        <div class="size-24 rounded-full bg-slate-800 border-2 border-slate-700 flex items-center justify-center text-slate-500 mb-6">
                            <span class="material-symbols-outlined text-5xl">inventory_2</span>
                        </div>
                        <h3 class="text-2xl font-bold adventure-title text-slate-300 mb-2">No Delivery Selected</h3>
                        <p class="text-slate-500 max-w-md">Select a delivery from the left panel to review its status, logic challenges, and results.</p>
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