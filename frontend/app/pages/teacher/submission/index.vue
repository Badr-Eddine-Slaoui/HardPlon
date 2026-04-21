<script setup lang="ts">
    import { useSubmission } from '~~/stores/submission';
    import { useCorrection } from '~~/stores/correction';
    import type { Submission } from '~~/types/submission';
    import { useClassGroup } from '~~/stores/class_group';
    import { useUser } from '~~/stores/user';
    import type { ClassGroup } from '~~/types/class_group';
    import type { User } from '~~/types/user';
    import type { ReturnData } from '~~/types/api';

    useHead({
        title: 'Student Dashboard - Bounty Board'
    })

    const classes = useClassGroup();
    const users = useUser();
    const store = useSubmission();
    const correction = useCorrection();

    const class_groups: Ref<ClassGroup[] | null> =  ref([])
    const students: Ref<User[] | null> = ref(null)

    const selected_class_group: Ref<number | null> = ref(null)
    const selected_student: Ref<number | null> = ref(null)
    const selected_submission: Ref<number | null> = ref(null)
    const is_corrected: Ref<boolean> = ref(false)

    const details: Ref<{ brief_skill_level_id: number, grade: string }[]> = ref([] as { brief_skill_level_id: number, grade: string }[])

    const is_all_grades_setted: Ref<boolean> = ref(false)

    const edit_mode: Ref<boolean> = ref(false)
    
    const set_edit_mode = () => {
        edit_mode.value = true
        form.brief_id = correction.correction?.brief_id as number
        form.student_id = correction.correction?.student_id as number
        form.message = correction.correction?.message as string
        details.value = correction.correction?.correction_details?.map(detail => ({ brief_skill_level_id: detail.brief_skill_level_id, grade: detail.grade })) as { brief_skill_level_id: number, grade: string }[]
        form.details = details.value
        is_all_grades_setted.value = details.value?.every(d => d.grade != '')
    }

    const form = reactive({
        brief_id: store.submission?.brief_id as number,
        student_id: store.submission?.student_id as number,
        message: '',
        details: [] as { brief_skill_level_id: number, grade: string }[]
    })

    const errs = ref({
        brief_id: '',
        student_id: '',
        message: '',
        details: '',
    })

    const getClassGroupStudents = async (id: number) => {
        selected_class_group.value = id
        students.value = await users.fetchTeacherStudents(id)
    }

    const getStudentSubmissions = async (id: number) => {
        selected_student.value = id
        await store.fetchSubmissionsByStudentId(id)
        await correction.fetchStudentCorrectionsById(id)
    }

    const getSubmission = async (id: number) => {
        is_all_grades_setted.value = false
        edit_mode.value = false
        details.value = []
        form.message = ''
        form.details = []
        await store.fetchSubmission(id)
        await correction.fetchStudentCorrection(store.submission?.brief_id as number, store.submission?.student_id as number)
        form.brief_id = store.submission?.brief_id as number
        form.student_id = store.submission?.student_id as number
        is_corrected.value = correction.correction ? true : false
        if(!is_corrected.value){
            details.value = store.submission?.brief?.brief_skill_levels?.map(b => ({ brief_skill_level_id: b.id, grade: '' })) as { brief_skill_level_id: number, grade: string }[]
        }
    }

    const selectSubmission = (id: number)=>{
        selected_submission.value = id
        getSubmission(id)
    }

    const mountPage = async() => {
        class_groups.value = await classes.fetchTeacherClassGroups();
        if(!class_groups.value) return
        await getClassGroupStudents(class_groups.value?.[0]?.id as number)
        if(!students.value) return
        await getStudentSubmissions(students.value?.[0]?.id as number)
        if(!store.submissions) return
        selectSubmission(store.submissions?.[0]?.id as number)
    }

    const addDetailGrade = (id: number, grade: string) => {
        const detail = details.value?.find(d => d.brief_skill_level_id == id) as { brief_skill_level_id: number, grade: string }
        detail.grade = grade
        is_all_grades_setted.value = details.value?.every(d => d.grade != '')
    }

    onMounted(mountPage)

    const submit = async () => {
        form.details = details.value

        let res : ReturnData<any>;

        if(!edit_mode.value) {
            res = await correction.createCorrection(form);
        }else{
            res = await correction.updateCorrection(correction.correction?.id as number, form);
        }

        if (res.success) {
            edit_mode.value = false
            is_corrected.value = false
            details.value = []
            form.message = ''
            form.details = []
            await mountPage()
        }
        
        if(res.errors) {
            errs.value = res.errors
        }
    }

    definePageMeta({
        middleware: ['auth', 'teacher']
    })

</script>

<template>
    <NuxtLayout name="teacher">
                <main class="flex-1 flex flex-row overflow-hidden">
            <div
                class="w-80 border-r border-slate-200 dark:border-[#224249] bg-white dark:bg-[#102023]/50 flex flex-col shrink-0">
                <div class="p-4 border-b border-slate-200 dark:border-[#224249]">
                    <h3 class="adventure-title text-lg font-bold">Assigned Briefs</h3>
                    <p class="text-[10px] text-slate-500 uppercase tracking-widest font-bold">Roronoa Zoro's Log</p>
                </div>
                <div class="mb-6 p-3">
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 px-3">Fleet Selection
                    </p>
                    <div class="space-y-1">
                        <select v-if="class_groups" @change="getClassGroupStudents(parseInt(($event.target as HTMLSelectElement).value as string))"
                            class="w-full bg-slate-50 dark:bg-background-dark border-slate-200 dark:border-[#224249] rounded-lg px-3 py-2 text-sm focus:ring-primary focus:border-primary">
                            <option v-for="class_group in class_groups" :key="class_group.id" :value="class_group.id" :selected="class_group.id === selected_class_group" >{{ class_group.name }}</option>
                        </select>
                        <select v-if="selected_class_group" @change="getStudentSubmissions(parseInt(($event.target as HTMLSelectElement).value as string))"
                            class="w-full bg-slate-50 dark:bg-background-dark border-slate-200 dark:border-[#224249] rounded-lg px-3 py-2 text-sm focus:ring-primary focus:border-primary">
                            <option v-for="student in students" :key="student.id" :value="student.id" :selected="student.id === selected_student" >{{ student.first_name }} {{ student.last_name }}</option>
                        </select>
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto">
                    <div class="divide-y divide-slate-100 dark:divide-[#224249]">
                        <template v-if="store.submissions?.length as number > 0">
                            <template v-for="submission in store.submissions" :key="submission.id">
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
                        <template v-else>
                            <div
                                class="col-start-2 bg-white dark:bg-[#1a2e33] border border-slate-200 dark:border-[#224249] p-6 m-3 rounded-2xl h-[30vh]">
                                <div class="space-y-4 flex flex-col justify-center items-center gap-y-5 h-full">
                                    <div
                                        class="size-12 mx-auto rounded-xl bg-red-500/10 flex items-center justify-center text-red-500">
                                        <span class="material-symbols-outlined">info</span>
                                    </div>
                                    <div class="text-center">
                                        <h3 class="font-bold text-lg">No Submissions Found</h3>
                                        <p class="text-sm text-slate-500 dark:text-[#90c1cb]">Select a student to view their submissions</p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            <form v-if="selected_submission" @submit.prevent="submit" class="flex-1 flex flex-col overflow-y-auto">
                <header
                    class="h-16 border-b border-slate-200 dark:border-[#224249] bg-white/80 dark:bg-background-dark/80 backdrop-blur-md flex items-center justify-between px-8 sticky top-0 z-10 shrink-0">
                    <div class="flex items-center gap-4">
                        <h2 class="text-2xl font-bold tracking-tight adventure-title text-primary">Mission Correction
                        </h2>
                    </div>
                    <div class="flex items-center gap-3">
                        <template v-if="!is_corrected">
                            <button v-if="is_all_grades_setted" type="submit"
                                class="bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark px-6 py-2 rounded-lg font-black text-xs uppercase tracking-widest transition-all shadow-[0_4px_10px_rgba(212,175,55,0.3)] hover:shadow-[0_4px_20px_rgba(212,175,55,0.5)] flex items-center gap-2 border border-pirate-gold-dark/30">
                                <span class="material-symbols-outlined text-sm">workspace_premium</span>
                                Publish Bounty
                            </button>
                        </template>
                        <template v-else>
                            <button v-if="!edit_mode"  type="button" @click="set_edit_mode()"
                                class="bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark px-6 py-2 rounded-lg font-black text-xs uppercase tracking-widest transition-all shadow-[0_4px_10px_rgba(212,175,55,0.3)] hover:shadow-[0_4px_20px_rgba(212,175,55,0.5)] flex items-center gap-2 border border-pirate-gold-dark/30">
                                <span class="material-symbols-outlined text-sm">workspace_premium</span>
                                Edit Bounty
                            </button>
                            <template v-else>
                                <button v-if="is_all_grades_setted"  type="submit"
                                    class="bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark px-6 py-2 rounded-lg font-black text-xs uppercase tracking-widest transition-all shadow-[0_4px_10px_rgba(212,175,55,0.3)] hover:shadow-[0_4px_20px_rgba(212,175,55,0.5)] flex items-center gap-2 border border-pirate-gold-dark/30">
                                    <span class="material-symbols-outlined text-sm">workspace_premium</span>
                                    Update Bounty
                                </button>
                            </template>
                        </template>
                    </div>
                </header>
                <div class="p-8 space-y-8 max-w-5xl mx-auto w-full">
                    <section class="space-y-4">
                        <div class="flex items-center gap-4">
                            <div
                                class="size-12 rounded-xl bg-slate-100 dark:bg-[#182f34] border border-slate-200 dark:border-[#224249] flex items-center justify-center">
                                <span class="material-symbols-outlined text-primary text-2xl">description</span>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold adventure-title">{{ store.submission?.brief.title }}</h3>
                                <p class="text-sm text-slate-500">Submitted by <span
                                        class="text-primary font-bold">{{ store.submission?.student.first_name }} {{ store.submission?.student.last_name }}</span> on {{ (new Date(store.submission?.created_at as string)).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' } ) }}</p>
                            </div>
                        </div>
                        <div
                            class="bg-white dark:bg-[#182f34] border border-slate-200 dark:border-[#224249] rounded-xl p-6 parchment-effect shadow-sm">
                            <div class="prose prose-sm dark:prose-invert max-w-none">
                                <p class="text-slate-600 dark:text-slate-300 mb-4 italic leading-relaxed">
                                    "{{ store.submission?.message }}"
                                </p>
                                <div class="flex flex-wrap gap-4 pt-4 border-t border-slate-100 dark:border-[#224249]">
                                    <NuxtLink v-for="link in store.submission?.links" target="_blank" class="flex items-center gap-2 text-xs font-bold text-primary bg-primary/5 px-3 py-2 rounded-lg border border-primary/20 hover:bg-primary/10 transition-colors"
                                        :to="link[Object.keys(link)[0] as string]">
                                        <span class="material-symbols-outlined text-sm">link</span>
                                        {{ Object.keys(link)[0] }}
                                    </NuxtLink>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section v-if="!is_corrected" class="space-y-6 pb-12">
                        <div class="flex items-center gap-2 text-pirate-gold">
                            <span class="material-symbols-outlined">edit_square</span>
                            <h3 class="text-lg font-bold adventure-title uppercase tracking-widest">Captain's Assessment
                            </h3>
                        </div>
                        <div class="space-y-8">
                            <div class="space-y-2">
                                <label class="text-xs font-black uppercase tracking-widest text-slate-400">Captain's
                                    Message</label>
                                <textarea v-model="form.message"
                                    class="w-full bg-white dark:bg-[#182f34] border-slate-200 dark:border-[#224249] rounded-xl p-4 text-sm focus:ring-primary focus:border-primary placeholder:italic parchment-effect"
                                    placeholder="Write your feedback to the crew member..." rows="4"></textarea>
                                <p v-if="errs.message" class="text-red-500 text-xs mt-1">{{ errs.message }}</p>
                            </div>
                            <div class="space-y-4">
                                <label class="text-xs font-black uppercase tracking-widest text-slate-400">Skill
                                    Proficiency Evaluation</label>
                                <div class="space-y-3">
                                    <div v-for="brief_skill_level in store.submission?.brief?.brief_skill_levels"
                                        class="bg-white dark:bg-[#182f34] border border-slate-200 dark:border-[#224249] rounded-xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                        <div>
                                            <h4 class="font-bold text-sm tracking-wide">{{ brief_skill_level?.skill?.code }}: {{ brief_skill_level?.skill?.title }}</h4>
                                            <p class="text-[10px] text-slate-500 uppercase font-bold">Target: Level {{ brief_skill_level?.level?.order }}
                                                ({{ brief_skill_level?.level?.name }})</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="flex bg-slate-100 dark:bg-background-dark p-1 rounded-lg border border-slate-200 dark:border-[#224249]">
                                                <input class="hidden custom-radio" :id="`skill_${brief_skill_level?.skill?.id}_poor`" :name="`skill_${brief_skill_level?.skill?.id}`"
                                                    type="radio" @change="addDetailGrade(brief_skill_level?.id, 'POOR')" />
                                                <label
                                                    class="px-4 py-1.5 rounded-md text-[10px] font-black uppercase cursor-pointer hover:bg-poor-red/10 text-poor-red border border-transparent transition-all"
                                                    :for="`skill_${brief_skill_level?.skill?.id}_poor`">POOR</label>
                                                <input class="hidden custom-radio" :id="`skill_${brief_skill_level?.skill?.id}_avg`" :name="`skill_${brief_skill_level?.skill?.id}`"
                                                    type="radio" @change="addDetailGrade(brief_skill_level?.id, 'AVERAGE')" />
                                                <label
                                                    class="px-4 py-1.5 rounded-md text-[10px] font-black uppercase cursor-pointer hover:bg-average-yellow/10 text-average-yellow border border-transparent transition-all"
                                                    :for="`skill_${brief_skill_level?.skill?.id}_avg`">AVERAGE</label>
                                                <input class="hidden custom-radio" :id="`skill_${brief_skill_level?.skill?.id}_exc`"
                                                    :name="`skill_${brief_skill_level?.skill?.id}`" type="radio" @change="addDetailGrade(brief_skill_level?.id, 'EXCELLENT')" />
                                                <label
                                                    class="px-4 py-1.5 rounded-md text-[10px] font-black uppercase cursor-pointer hover:bg-excellent-green/10 text-excellent-green border border-transparent transition-all"
                                                    :for="`skill_${brief_skill_level?.skill?.id}_exc`">EXCELLENT</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end pt-4">
                            <button v-if="is_all_grades_setted" type="submit"
                                class="bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark px-10 py-3 rounded-xl font-black text-sm uppercase tracking-[0.2em] transition-all shadow-[0_4px_15px_rgba(212,175,55,0.4)] hover:scale-[1.02] flex items-center gap-3">
                                <span class="material-symbols-outlined font-bold">history_edu</span>
                                Publish Bounty
                            </button>
                        </div>
                    </section>
                    <template v-else>
                        <section v-if="!edit_mode" class="space-y-6 pb-12">
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
                            <div class="flex justify-end pt-4">
                                <button type="button" @click="set_edit_mode()"
                                    class="bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark px-10 py-3 rounded-xl font-black text-sm uppercase tracking-[0.2em] transition-all shadow-[0_4px_15px_rgba(212,175,55,0.4)] hover:scale-[1.02] flex items-center gap-3">
                                    <span class="material-symbols-outlined font-bold">history_edu</span>
                                    Edit Bounty
                                </button>
                            </div>
                        </section>
                        <section v-else class="space-y-6 pb-12">
                            <div class="flex items-center gap-2 text-pirate-gold">
                                <span class="material-symbols-outlined">edit_square</span>
                                <h3 class="text-lg font-bold adventure-title uppercase tracking-widest">Captain's Assessment
                                </h3>
                            </div>
                            <div class="space-y-8">
                                <div class="space-y-2">
                                    <label class="text-xs font-black uppercase tracking-widest text-slate-400">Captain's
                                        Message</label>
                                    <textarea v-model="form.message"
                                        class="w-full bg-white dark:bg-[#182f34] border-slate-200 dark:border-[#224249] rounded-xl p-4 text-sm focus:ring-primary focus:border-primary placeholder:italic parchment-effect"
                                        placeholder="Write your feedback to the crew member..." rows="4"></textarea>
                                    <p v-if="errs.message" class="text-red-500 text-xs mt-1">{{ errs.message }}</p>
                                </div>
                                <div class="space-y-4">
                                    <label class="text-xs font-black uppercase tracking-widest text-slate-400">Skill
                                        Proficiency Evaluation</label>
                                    <div class="space-y-3">
                                        <div v-for="detail in correction?.correction?.correction_details"
                                            class="bg-white dark:bg-[#182f34] border border-slate-200 dark:border-[#224249] rounded-xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                            <div>
                                                <h4 class="font-bold text-sm tracking-wide">{{ detail?.brief_skill_level?.skill?.code }}: {{ detail?.brief_skill_level?.skill?.title }}</h4>
                                                <p class="text-[10px] text-slate-500 uppercase font-bold">Target: Level {{ detail?.brief_skill_level?.level?.order }}
                                                    ({{ detail?.brief_skill_level?.level?.name }})</p>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <div
                                                    class="flex bg-slate-100 dark:bg-background-dark p-1 rounded-lg border border-slate-200 dark:border-[#224249]">
                                                    <input class="hidden custom-radio" :id="`skill_${detail?.brief_skill_level?.skill?.id}_poor`" :name="`skill_${detail?.brief_skill_level?.skill?.id}`"
                                                        type="radio" @change="addDetailGrade(detail?.brief_skill_level?.id, 'POOR')" :checked="detail?.grade === 'POOR'" />
                                                    <label
                                                        class="px-4 py-1.5 rounded-md text-[10px] font-black uppercase cursor-pointer hover:bg-poor-red/10 text-poor-red border border-transparent transition-all"
                                                        :for="`skill_${detail?.brief_skill_level?.skill?.id}_poor`">POOR</label>
                                                    <input class="hidden custom-radio" :id="`skill_${detail?.brief_skill_level?.skill?.id}_avg`" :name="`skill_${detail?.brief_skill_level?.skill?.id}`"
                                                        type="radio" @change="addDetailGrade(detail?.brief_skill_level?.id, 'AVERAGE')" :checked="detail?.grade === 'AVERAGE'" />
                                                    <label
                                                        class="px-4 py-1.5 rounded-md text-[10px] font-black uppercase cursor-pointer hover:bg-average-yellow/10 text-average-yellow border border-transparent transition-all"
                                                        :for="`skill_${detail?.brief_skill_level?.skill?.id}_avg`">AVERAGE</label>
                                                    <input class="hidden custom-radio" :id="`skill_${detail?.brief_skill_level?.skill?.id}_exc`"
                                                        :name="`skill_${detail?.brief_skill_level?.skill?.id}`" type="radio" @change="addDetailGrade(detail?.brief_skill_level?.id, 'EXCELLENT')" :checked="detail?.grade === 'EXCELLENT'" />
                                                    <label
                                                        class="px-4 py-1.5 rounded-md text-[10px] font-black uppercase cursor-pointer hover:bg-excellent-green/10 text-excellent-green border border-transparent transition-all"
                                                        :for="`skill_${detail?.brief_skill_level?.skill?.id}_exc`">EXCELLENT</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end pt-4">
                                <button v-if="is_all_grades_setted" type="submit"
                                    class="bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark px-10 py-3 rounded-xl font-black text-sm uppercase tracking-[0.2em] transition-all shadow-[0_4px_15px_rgba(212,175,55,0.4)] hover:scale-[1.02] flex items-center gap-3">
                                    <span class="material-symbols-outlined font-bold">history_edu</span>
                                    Update Bounty
                                </button>
                            </div>
                        </section>
                    </template>
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
            </form>
            <div v-else class="w-full h-[calc(100vh-4.5rem)] flex items-center justify-center">
                <div class="flex flex-col items-center gap-2">
                    <div class="flex items-center gap-2">
                        <div class="size-12 rounded-full bg-slate-100 dark:bg-[#182f34] border border-slate-200 dark:border-[#224249] flex items-center justify-center">
                            <span class="material-symbols-outlined text-2xl text-pirate-gold">warning</span>
                        </div>
                        <div class="flex flex-col">
                            <h1 class="text-2xl font-bold tracking-tight">There's no submission selected</h1>
                            <p class="text-sm font-medium tracking-tight">Please select a submission to correct</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </NuxtLayout>
</template>