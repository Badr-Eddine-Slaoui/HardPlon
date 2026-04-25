<script setup lang="ts">
    import { marked } from "marked";
    import DOMPurify from "dompurify";
    import { useSubmission } from '~~/stores/submission';
    import { useCorrection } from '~~/stores/correction';
    import { useBrief } from '~~/stores/brief';
    import { useClassGroup } from '~~/stores/class_group';
    import { useUser } from '~~/stores/user';
    import type { Submission } from '~~/types/submission';
    import type { Brief } from '~~/types/brief';
    import type { ClassGroup } from '~~/types/class_group';
    import type { User } from '~~/types/user';
    import type { Correction } from '~~/types/correction';
import type { ArchitectureRule } from "~~/types/brief_version";

    useHead({
        title: 'Mission Evaluation - Bounty Board'
    })

    const briefStore = useBrief();
    const submissionStore = useSubmission();
    const correctionStore = useCorrection();
    const classGroupStore = useClassGroup();
    const userStore = useUser();

    const classGroups = ref<ClassGroup[]>([]);
    const students = ref<User[]>([]);
    const briefs = ref<Brief[]>([]);

    const selectedClassGroupId = ref<number | null>(null);
    const selectedStudentId = ref<number | null>(null);
    const selectedBriefId = ref<number | null>(null);

    const activeTab = ref<'brief' | 'submission' | 'correction'>('brief');
    const loading = ref(false);

    const currentBrief = computed(() => briefs.value.find(b => b.id === selectedBriefId.value));
    const currentSubmission = ref<Submission | null>(null);
    const currentCorrection = ref<Correction | null>(null);
    const currentCorrectionMessage = ref<string | null>(null);
    const visibleCodeProblemId = ref<number | null>(null);

    const fetchInitialData = async () => {
        loading.value = true;
        classGroups.value = await classGroupStore.fetchTeacherClassGroups();
        if (classGroups.value.length > 0) {
            selectedClassGroupId.value = classGroups?.value[0]?.id as number;
            await onClassGroupChange();
        }
        loading.value = false;
    };

    const onClassGroupChange = async () => {
        if (!selectedClassGroupId.value) return;
        loading.value = true;
        students.value = await userStore.fetchTeacherStudents(selectedClassGroupId.value) as User[];
        briefs.value = await briefStore.fetchClassGroupBriefs(selectedClassGroupId.value) as Brief[];
        
        if (students.value.length > 0) {
            selectedStudentId.value = students?.value[0]?.id as number;
        } else {
            selectedStudentId.value = null;
        }

        if (briefs.value.length > 0) {
            selectedBriefId.value = briefs?.value[0]?.id as number;
            await onBriefOrStudentChange();
        } else {
            selectedBriefId.value = null;
            currentSubmission.value = null;
            currentCorrection.value = null;
            currentCorrectionMessage.value = null;
        }
        loading.value = false;
    };

    const onBriefOrStudentChange = async () => {
        if (!selectedBriefId.value || !selectedStudentId.value) return;
        loading.value = true;
        
        currentSubmission.value = await submissionStore.fetchStudentBriefSubmission(selectedBriefId.value, selectedStudentId.value);
        
        await correctionStore.fetchStudentCorrection(selectedBriefId.value, selectedStudentId.value);
        currentCorrection.value = correctionStore.correction;
        currentCorrectionMessage.value = DOMPurify.sanitize(marked.parse(currentCorrection.value?.message ?? "") as string);

        loading.value = false;
    };

    const selectBrief = async (id: number) => {
        selectedBriefId.value = id;
        await onBriefOrStudentChange();
    };

    const selectStudent = async (id: number) => {
        selectedStudentId.value = id;
        await onBriefOrStudentChange();
    };

    onMounted(fetchInitialData);

    definePageMeta({
        middleware: ['auth', 'teacher']
    });

    const getStatusColor = (status: string) => {
        switch (status) {
            case 'completed': return 'text-emerald-500 bg-emerald-500/10 border-emerald-500/20';
            case 'processing': return 'text-amber-500 bg-amber-500/10 border-amber-500/20';
            case 'failed': return 'text-rose-500 bg-rose-500/10 border-rose-500/20';
            default: return 'text-slate-500 bg-slate-500/10 border-slate-500/20';
        }
    };

    const getGradeColor = (grade: string) => {
        switch (grade) {
            case 'EXCELLENT': return 'bg-emerald-500/20 text-emerald-500 border-emerald-500/30';
            case 'AVERAGE': return 'bg-amber-500/20 text-amber-500 border-amber-500/30';
            case 'POOR': return 'bg-rose-500/20 text-rose-500 border-rose-500/30';
            default: return 'bg-slate-500/20 text-slate-500 border-slate-500/30';
        }
    };

</script>

<template>
    <NuxtLayout name="teacher">
        <main class="flex-1 flex flex-row overflow-hidden bg-[#0a1416]">
            <!-- Sidebar: Navigation -->
            <div class="w-85 border-r border-slate-800 bg-[#0d1b1e] flex flex-col shrink-0">
                <div class="p-6 border-b border-slate-800 bg-[#0f1f23]">
                    <h3 class="adventure-title text-xl font-bold text-pirate-gold">Marine Registry</h3>
                    <p class="text-[10px] text-slate-500 uppercase tracking-widest font-black mt-1">Fleet & Crew Management</p>
                </div>

                <!-- Selectors -->
                <div class="p-4 space-y-4">
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2 block px-1">Select Fleet (Class)</label>
                        <select v-model="selectedClassGroupId" @change="onClassGroupChange"
                            class="w-full bg-[#152a2e] border-slate-700 text-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-pirate-gold focus:border-pirate-gold transition-all">
                            <option v-for="cg in classGroups" :key="cg.id" :value="cg.id">{{ cg.name }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2 block px-1">Select Crew Member (Student)</label>
                        <select v-model="selectedStudentId" @change="onBriefOrStudentChange"
                            class="w-full bg-[#152a2e] border-slate-700 text-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-pirate-gold focus:border-pirate-gold transition-all">
                            <option v-for="student in students" :key="student.id" :value="student.id">{{ student.first_name }} {{ student.last_name }}</option>
                        </select>
                    </div>
                </div>

                <!-- Briefs List -->
                <div class="flex-1 overflow-y-auto custom-scrollbar">
                    <div class="p-4 border-t border-slate-800">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-3 block px-1">Assigned Missions</label>
                        <div class="space-y-2">
                            <template v-if="briefs.length > 0">
                                <button v-for="brief in briefs" :key="brief.id" 
                                    @click="selectBrief(brief.id)"
                                    :class="[
                                        'w-full text-left p-4 rounded-2xl border transition-all group relative overflow-hidden',
                                        selectedBriefId === brief.id 
                                            ? 'bg-pirate-gold/10 border-pirate-gold/30 ring-1 ring-pirate-gold/20' 
                                            : 'bg-[#152a2e]/50 border-slate-800 hover:border-slate-600 hover:bg-[#1a3439]'
                                    ]">
                                    <div class="flex justify-between items-start mb-1">
                                        <h4 :class="['text-sm font-bold adventure-title truncate pr-2', selectedBriefId === brief.id ? 'text-pirate-gold' : 'text-slate-300 group-hover:text-white']">
                                            {{ brief.title }}
                                        </h4>
                                    </div>
                                    <p class="text-[11px] text-slate-500 line-clamp-1 mb-2">{{ brief.description }}</p>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[9px] font-black uppercase tracking-tighter bg-slate-800 text-slate-400 px-2 py-0.5 rounded border border-slate-700">
                                            {{ brief.stack?.name }}
                                        </span>
                                    </div>
                                </button>
                            </template>
                            <div v-else class="p-8 text-center bg-[#152a2e]/20 rounded-2xl border border-dashed border-slate-800">
                                <span class="material-symbols-outlined text-slate-700 text-4xl mb-2">anchor</span>
                                <p class="text-xs text-slate-600 font-bold uppercase tracking-widest">No Missions Found</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col min-w-0 bg-[#0a1416] relative overflow-hidden">
                <div v-if="selectedBriefId && currentBrief" class="h-full flex flex-col">
                    <!-- Loading Overlay -->
                    <div v-if="loading" class="absolute inset-0 bg-[#0a1416]/60 backdrop-blur-sm z-50 flex flex-col items-center justify-center">
                        <div class="relative">
                            <div class="size-16 border-4 border-pirate-gold/20 border-t-pirate-gold rounded-full animate-spin"></div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="material-symbols-outlined text-pirate-gold animate-pulse">anchor</span>
                            </div>
                        </div>
                        <p class="mt-4 text-xs font-black text-pirate-gold uppercase tracking-[0.3em] animate-pulse">Navigating Waters...</p>
                    </div>

                    <!-- Header with Tabs -->
                    <header class="h-20 bg-[#0f1f23]/80 backdrop-blur-md border-b border-slate-800 px-8 flex items-center justify-between shrink-0 z-20">
                        <div class="flex items-center gap-6">
                            <div class="flex flex-col">
                                <h2 class="text-2xl font-bold adventure-title text-pirate-gold leading-none">{{ currentBrief.title }}</h2>
                                <p class="text-[10px] text-slate-500 font-black uppercase tracking-widest mt-1">
                                    Evaluating: <span class="text-slate-300">{{ students.find(s => s.id === selectedStudentId)?.first_name }} {{ students.find(s => s.id === selectedStudentId)?.last_name }}</span>
                                </p>
                            </div>
                        </div>

                        <!-- Tab Buttons -->
                        <div class="flex bg-[#0a1416] p-1 rounded-xl border border-slate-800 shadow-inner">
                            <button @click="activeTab = 'brief'" 
                                :class="['px-6 py-2 rounded-lg text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2', 
                                activeTab === 'brief' ? 'bg-pirate-gold text-background-dark shadow-lg' : 'text-slate-500 hover:text-slate-300']">
                                <span class="material-symbols-outlined text-sm">article</span>
                                Brief
                            </button>
                            <button @click="activeTab = 'submission'" 
                                :class="['px-6 py-2 rounded-lg text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2', 
                                activeTab === 'submission' ? 'bg-pirate-gold text-background-dark shadow-lg' : 'text-slate-500 hover:text-slate-300']">
                                <span class="material-symbols-outlined text-sm">package_2</span>
                                Submission
                            </button>
                            <button @click="activeTab = 'correction'" 
                                :class="['px-6 py-2 rounded-lg text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2', 
                                activeTab === 'correction' ? 'bg-pirate-gold text-background-dark shadow-lg' : 'text-slate-500 hover:text-slate-300']">
                                <span class="material-symbols-outlined text-sm">workspace_premium</span>
                                Correction
                            </button>
                        </div>
                    </header>

                    <!-- Scrollable Content -->
                    <div class="flex-1 overflow-y-auto custom-scrollbar p-8">
                        <div class="max-w-5xl mx-auto space-y-8 pb-12">
                            
                            <!-- TAB 1: BRIEF DETAILS -->
                            <div v-if="activeTab === 'brief'" class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
                                <!-- Top Info Cards -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="bg-[#0f1f23] border border-slate-800 rounded-2xl p-6 parchment-effect relative group hover:border-pirate-gold/30 transition-all">
                                        <div class="size-10 rounded-xl bg-pirate-gold/10 flex items-center justify-center text-pirate-gold mb-4 group-hover:scale-110 transition-transform">
                                            <span class="material-symbols-outlined">terminal</span>
                                        </div>
                                        <h4 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Tech Stack</h4>
                                        <p class="text-lg font-bold text-slate-200">{{ currentBrief.stack?.name || 'N/A' }}</p>
                                    </div>
                                    <div class="bg-[#0f1f23] border border-slate-800 rounded-2xl p-6 parchment-effect relative group hover:border-pirate-gold/30 transition-all">
                                        <div class="size-10 rounded-xl bg-pirate-gold/10 flex items-center justify-center text-pirate-gold mb-4 group-hover:scale-110 transition-transform">
                                            <span class="material-symbols-outlined">trending_up</span>
                                        </div>
                                        <h4 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Skill Targets</h4>
                                        <p class="text-lg font-bold text-slate-200">{{ currentBrief.brief_skill_levels?.length || 0 }} Ranks</p>
                                    </div>
                                    <div class="bg-[#0f1f23] border border-slate-800 rounded-2xl p-6 parchment-effect relative group hover:border-pirate-gold/30 transition-all">
                                        <div class="size-10 rounded-xl bg-pirate-gold/10 flex items-center justify-center text-pirate-gold mb-4 group-hover:scale-110 transition-transform">
                                            <span class="material-symbols-outlined">extension</span>
                                        </div>
                                        <h4 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Challenges</h4>
                                        <p class="text-lg font-bold text-slate-200">{{ currentBrief.problems?.length || 0 }} Problems</p>
                                    </div>
                                </div>

                                <!-- Skills & Levels -->
                                <section>
                                    <h3 class="text-sm font-black text-pirate-gold uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                                        <span class="h-px w-8 bg-pirate-gold/30"></span>
                                        Required Skill Proficiency
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div v-for="bsl in currentBrief.brief_skill_levels" :key="bsl.id" 
                                            class="bg-[#152a2e]/40 border border-slate-800/50 rounded-xl p-4 flex items-center justify-between group hover:bg-[#1a3439]/60 transition-colors">
                                            <div class="flex items-center gap-3">
                                                <div class="size-8 rounded-lg bg-slate-800 flex items-center justify-center text-[10px] font-black text-pirate-gold group-hover:bg-pirate-gold group-hover:text-background-dark transition-all">
                                                    {{ bsl.skill?.code }}
                                                </div>
                                                <div>
                                                    <h5 class="text-sm font-bold text-slate-200">{{ bsl.skill?.title }}</h5>
                                                    <p class="text-[10px] text-slate-500 font-bold uppercase">{{ bsl.level?.name }} (Rank {{ bsl.level?.order }})</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                <!-- Architecture Rules -->
                                <section v-if="currentBrief.brief_versions?.[0]?.architecture_rules">
                                    <h3 class="text-sm font-black text-pirate-gold uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                                        <span class="h-px w-8 bg-pirate-gold/30"></span>
                                        Marine Architecture Standards
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <!-- Required -->
                                        <div v-if="(currentBrief.brief_versions[0].architecture_rules as ArchitectureRule).required?.length" class="bg-[#152a2e]/20 border border-slate-800 rounded-2xl p-6 parchment-effect">
                                            <h5 class="text-[10px] font-black text-emerald-500 uppercase tracking-widest mb-4 flex items-center gap-2">
                                                <span class="material-symbols-outlined text-sm">check_circle</span> Required Files
                                            </h5>
                                            <ul class="space-y-2">
                                                <li v-for="rule in (currentBrief.brief_versions[0].architecture_rules as ArchitectureRule).required" :key="rule"
                                                    class="text-xs text-slate-400 font-medium flex items-center gap-2">
                                                    <span class="size-1 bg-emerald-500/40 rounded-full"></span> {{ rule }}
                                                </li>
                                            </ul>
                                        </div>
                                        <!-- Forbidden -->
                                        <div v-if="(currentBrief.brief_versions[0].architecture_rules as ArchitectureRule).forbidden?.length" class="bg-[#152a2e]/20 border border-slate-800 rounded-2xl p-6 parchment-effect">
                                            <h5 class="text-[10px] font-black text-rose-500 uppercase tracking-widest mb-4 flex items-center gap-2">
                                                <span class="material-symbols-outlined text-sm">block</span> Forbidden Items
                                            </h5>
                                            <ul class="space-y-2">
                                                <li v-for="rule in (currentBrief.brief_versions[0].architecture_rules as ArchitectureRule).forbidden" :key="rule"
                                                    class="text-xs text-slate-400 font-medium flex items-center gap-2">
                                                    <span class="size-1 bg-rose-500/40 rounded-full"></span> {{ rule }}
                                                </li>
                                            </ul>
                                        </div>
                                        <!-- Structure -->
                                        <div v-if="(currentBrief.brief_versions[0].architecture_rules as ArchitectureRule).structure" class="bg-[#152a2e]/20 border border-slate-800 rounded-2xl p-6 parchment-effect">
                                            <h5 class="text-[10px] font-black text-pirate-gold uppercase tracking-widest mb-4 flex items-center gap-2">
                                                <span class="material-symbols-outlined text-sm">account_tree</span> Layout Schema
                                            </h5>
                                            <ul class="space-y-2">
                                                <li v-for="(type, path) in (currentBrief.brief_versions[0].architecture_rules as ArchitectureRule).structure" :key="path"
                                                    class="text-xs text-slate-400 font-medium flex items-center justify-between">
                                                    <span>{{ path }}</span>
                                                    <span class="text-[8px] font-black bg-slate-800 px-1.5 py-0.5 rounded uppercase">{{ type }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </section>

                                <!-- Problems & Test Cases -->
                                <section>
                                    <h3 class="text-sm font-black text-pirate-gold uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                                        <span class="h-px w-8 bg-pirate-gold/30"></span>
                                        Marine Challenges (Problems)
                                    </h3>
                                    <div class="space-y-4">
                                        <div v-for="problem in currentBrief.problems" :key="problem.id"
                                            class="bg-[#0f1f23] border border-slate-800 rounded-2xl overflow-hidden group hover:border-pirate-gold/20 transition-all">
                                            <div class="p-6 border-b border-slate-800/50 flex justify-between items-start">
                                                <div>
                                                    <div class="flex items-center gap-3 mb-1">
                                                        <h4 class="text-lg font-bold text-slate-200 adventure-title">{{ problem.title }}</h4>
                                                        <span class="text-[10px] font-black bg-[#1a3439] text-pirate-gold px-2 py-0.5 rounded border border-pirate-gold/20">
                                                            {{ problem.language?.name }}
                                                        </span>
                                                    </div>
                                                    <p class="text-sm text-slate-500">{{ problem.description }}</p>
                                                </div>
                                            </div>
                                            <!-- Test Cases -->
                                            <div class="p-4 bg-[#0a1416]/50">
                                                <p class="text-[9px] font-black text-slate-600 uppercase tracking-widest mb-3 px-2">Verification Protocols (Test Cases)</p>
                                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                                    <div v-for="tc in problem.test_cases" :key="tc.id"
                                                        class="bg-[#152a2e]/40 border border-slate-800 rounded-xl p-3 flex flex-col gap-1">
                                                        <div class="flex justify-between items-center mb-1">
                                                            <span class="text-[9px] font-black text-slate-500 uppercase">Input Sample</span>
                                                            <span v-if="!tc.is_hidden" class="text-[8px] font-black bg-emerald-500/10 text-emerald-500 px-1.5 py-0.5 rounded border border-emerald-500/20">PUBLIC</span>
                                                            <span v-else class="text-[8px] font-black bg-slate-800 text-slate-500 px-1.5 py-0.5 rounded border border-slate-700">HIDDEN</span>
                                                        </div>
                                                        <code class="text-[11px] text-slate-300 font-mono bg-black/30 p-2 rounded block truncate">{{ tc.input }}</code>
                                                        <span class="text-[9px] font-black text-slate-500 uppercase mt-1">Expected Output</span>
                                                        <code class="text-[11px] text-pirate-gold font-mono bg-black/30 p-2 rounded block truncate">{{ tc.expected_output }}</code>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>

                            <!-- TAB 2: SUBMISSION -->
                            <div v-if="activeTab === 'submission'" class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
                                <div v-if="currentSubmission" class="space-y-8">
                                    <!-- Main Submission Info -->
                                    <div class="bg-[#0f1f23] border border-slate-800 rounded-2xl p-8 parchment-effect relative overflow-hidden shadow-2xl">
                                        <div class="absolute top-0 right-0 p-8 opacity-10">
                                            <span class="material-symbols-outlined text-8xl text-pirate-gold">anchor</span>
                                        </div>
                                        
                                        <div class="relative z-10 space-y-6">
                                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-800/50 pb-6">
                                                <div class="flex items-center gap-4">
                                                    <div class="size-16 rounded-2xl bg-pirate-gold/10 border border-pirate-gold/20 flex items-center justify-center text-pirate-gold shadow-[0_0_20px_rgba(212,175,55,0.1)]">
                                                        <span class="material-symbols-outlined text-4xl">inventory_2</span>
                                                    </div>
                                                    <div>
                                                        <h3 class="text-2xl font-bold adventure-title text-slate-100">Project Payload</h3>
                                                        <p class="text-xs text-slate-500 font-bold uppercase tracking-widest mt-1">Submitted on {{ new Date(currentSubmission.created_at).toLocaleDateString() }}</p>
                                                    </div>
                                                </div>
                                                <NuxtLink :to="currentSubmission.link" target="_blank"
                                                    class="bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest transition-all flex items-center gap-2 group">
                                                    <span class="material-symbols-outlined text-sm group-hover:rotate-45 transition-transform">open_in_new</span>
                                                    Access Repository
                                                </NuxtLink>
                                            </div>

                                            <!-- System Evaluation Summary -->
                                            <div v-if="currentSubmission.evaluation_job?.result" class="space-y-6">
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div class="bg-black/20 rounded-xl p-4 border border-slate-800/50">
                                                        <div class="flex items-center justify-between mb-2">
                                                            <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Architecture Score</span>
                                                            <span :class="['text-xs font-black', currentSubmission.evaluation_job.result.architecture.score === 10 ? 'text-emerald-500' : 'text-amber-500']">
                                                                {{ currentSubmission.evaluation_job.result.architecture.score }}/10
                                                            </span>
                                                        </div>
                                                        <div class="h-1.5 w-full bg-slate-800 rounded-full overflow-hidden">
                                                            <div class="h-full bg-pirate-gold transition-all duration-1000" :style="{ width: `${currentSubmission.evaluation_job.result.architecture.score * 10}%` }"></div>
                                                        </div>
                                                    </div>
                                                    <div class="bg-black/20 rounded-xl p-4 border border-slate-800/50">
                                                        <div class="flex items-center justify-between mb-2">
                                                            <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">HTTP Tests Score</span>
                                                            <span :class="['text-xs font-black', currentSubmission.evaluation_job.result.tests.score === 10 ? 'text-emerald-500' : 'text-amber-500']">
                                                                {{ currentSubmission.evaluation_job.result.tests.score }}/10
                                                            </span>
                                                        </div>
                                                        <div class="h-1.5 w-full bg-slate-800 rounded-full overflow-hidden">
                                                            <div class="h-full bg-pirate-gold transition-all duration-1000" :style="{ width: `${currentSubmission.evaluation_job.result.tests.score * 10}%` }"></div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Detailed Analysis -->
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                    <!-- Architecture Details -->
                                                    <div class="space-y-3">
                                                        <h4 class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Architectural Integrity</h4>
                                                        <div class="space-y-2">
                                                            <div v-if="currentSubmission.evaluation_job.result.architecture.missing.length === 0 && currentSubmission.evaluation_job.result.architecture.forbidden.length === 0" 
                                                                class="flex items-center gap-2 text-xs text-emerald-500 font-medium">
                                                                <span class="material-symbols-outlined text-sm">verified</span>
                                                                Structure Verified
                                                            </div>
                                                            <div v-for="miss in currentSubmission.evaluation_job.result.architecture.missing" :key="miss"
                                                                class="flex items-center gap-2 text-[10px] text-rose-500 bg-rose-500/5 px-2 py-1 rounded border border-rose-500/10">
                                                                <span class="material-symbols-outlined text-xs">close</span>
                                                                Missing: {{ miss }}
                                                            </div>
                                                            <div v-for="forbid in currentSubmission.evaluation_job.result.architecture.forbidden" :key="forbid"
                                                                class="flex items-center gap-2 text-[10px] text-rose-500 bg-rose-500/5 px-2 py-1 rounded border border-rose-500/10">
                                                                <span class="material-symbols-outlined text-xs">block</span>
                                                                Forbidden: {{ forbid }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Test Details -->
                                                    <div class="space-y-3">
                                                        <h4 class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Protocol Validation</h4>
                                                        <div class="space-y-2">
                                                            <div v-for="test in currentSubmission.evaluation_job.result.tests.tests" :key="test.name"
                                                                class="flex items-center justify-between text-[10px] bg-slate-900/50 px-3 py-1.5 rounded-lg border border-slate-800/50">
                                                                <div class="flex items-center gap-2">
                                                                    <span :class="['material-symbols-outlined text-xs', test.passed ? 'text-emerald-500' : 'text-rose-500']">
                                                                        {{ test.passed ? 'check_circle' : 'error' }}
                                                                    </span>
                                                                    <span class="text-slate-300 font-medium">{{ test.name }}</span>
                                                                </div>
                                                                <span class="text-slate-500 font-black">HTTP {{ test.status }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="space-y-3">
                                                <h4 class="text-[10px] font-black text-pirate-gold uppercase tracking-[0.2em]">Crew Message</h4>
                                                <div class="bg-black/20 rounded-xl p-6 border border-slate-800 shadow-inner">
                                                    <p class="text-slate-300 italic leading-relaxed">"{{ currentSubmission.message }}"</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Code Problems Status -->
                                    <section>
                                        <div class="flex items-center justify-between mb-4">
                                            <h3 class="text-sm font-black text-pirate-gold uppercase tracking-[0.2em] flex items-center gap-2">
                                                <span class="h-px w-8 bg-pirate-gold/30"></span>
                                                Logical Fragments (Problem Submissions)
                                            </h3>
                                            <div v-if="currentSubmission.problem_submission_job" 
                                                :class="['px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border', getStatusColor(currentSubmission.problem_submission_job.status)]">
                                                Job: {{ currentSubmission.problem_submission_job.status }}
                                            </div>
                                        </div>

                                        <div v-if="currentSubmission.problem_submissions?.length as number > 0" class="grid grid-cols-1 gap-4">
                                            <div v-for="(ps, index) in currentSubmission.problem_submissions" :key="ps.id"
                                                class="bg-[#0f1f23] border border-slate-800 rounded-2xl overflow-hidden group">
                                                <div class="p-6">
                                                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                                        <div class="flex items-center gap-4">
                                                            <div :class="['size-12 rounded-xl flex items-center justify-center border transition-all', 
                                                                currentSubmission.problem_submission_job?.status === 'completed' ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-500' : 
                                                                currentSubmission.problem_submission_job?.status === 'failed' ? 'bg-rose-500/10 border-rose-500/20 text-rose-500' : 
                                                                'bg-slate-800 border-slate-700 text-slate-500']">
                                                                <span class="material-symbols-outlined">
                                                                    {{ currentSubmission.problem_submission_job?.status === 'completed' ? 'check_circle' : 
                                                                       currentSubmission.problem_submission_job?.status === 'failed' ? 'error' : 
                                                                       currentSubmission.problem_submission_job?.status === 'running' ? 'sync' : 'hourglass_empty' }}
                                                                </span>
                                                            </div>
                                                            <div>
                                                                <div class="flex items-center gap-2">
                                                                    <h4 class="font-bold text-slate-200">{{ ps.problem?.title }}</h4>
                                                                    <span class="text-[9px] font-black bg-slate-800 text-slate-400 px-1.5 py-0.5 rounded border border-slate-700">
                                                                        {{ ps.problem?.language?.name }}
                                                                    </span>
                                                                </div>
                                                                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">Status: {{ currentSubmission.problem_submission_job?.status || 'Pending' }}</p>
                                                            </div>
                                                        </div>

                                                        <div class="flex items-center gap-3">
                                                            <div v-if="currentSubmission?.problem_submission_job?.result" class="text-right">
                                                                <p class="text-[10px] font-black text-slate-500 uppercase tracking-tighter">Test Success Rate</p>
                                                                <p :class="['text-lg font-black', currentSubmission?.problem_submission_job?.result?.submissions[index]?.score === 10 ? 'text-emerald-500' : 'text-amber-500']">
                                                                    {{ currentSubmission?.problem_submission_job?.result?.submissions[index]?.score as number }} / 10
                                                                </p>
                                                            </div>
                                                            <button 
                                                                @click="visibleCodeProblemId = visibleCodeProblemId === ps.id ? null : ps.id"
                                                                :class="['p-2 rounded-lg transition-all border', visibleCodeProblemId === ps.id ? 'bg-pirate-gold text-background-dark border-pirate-gold' : 'bg-slate-800 hover:bg-slate-700 text-slate-300 border-slate-700']">
                                                                <span class="material-symbols-outlined text-sm">{{ visibleCodeProblemId === ps.id ? 'visibility_off' : 'code' }}</span>
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <!-- Student Code Viewer -->
                                                    <transition enter-active-class="transition duration-300 ease-out" enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100" leave-active-class="transition duration-200 ease-in" leave-from-class="transform scale-100 opacity-100" leave-to-class="transform scale-95 opacity-0">
                                                        <div v-if="visibleCodeProblemId === ps.id" class="mt-6 border-t border-slate-800 pt-6">
                                                            <div class="flex items-center justify-between mb-3 px-2">
                                                                <h5 class="text-[9px] font-black text-pirate-gold uppercase tracking-widest">Student Payload Source</h5>
                                                                <span class="text-[8px] font-mono text-slate-600">{{ ps.problem?.language?.name }} Environment</span>
                                                            </div>
                                                            <div class="bg-black/60 rounded-2xl p-6 font-mono text-[11px] overflow-x-auto text-slate-300 border border-slate-800/50 shadow-inner custom-scrollbar relative">
                                                                <div class="absolute top-0 right-0 p-4 opacity-5 pointer-events-none">
                                                                    <span class="material-symbols-outlined text-4xl">code</span>
                                                                </div>
                                                                <pre class="leading-relaxed"><code>{{ ps.code }}</code></pre>
                                                            </div>
                                                        </div>
                                                    </transition>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-else class="h-[40vh] flex flex-col items-center justify-center bg-[#0f1f23]/30 rounded-3xl border border-dashed border-slate-800">
                                            <div class="size-20 rounded-full bg-slate-800/50 flex items-center justify-center text-slate-600 mb-6">
                                                <span class="material-symbols-outlined text-5xl">person_off</span>
                                            </div>
                                            <h3 class="text-xl font-bold adventure-title text-slate-500 mb-2">No Submission Found</h3>
                                            <p class="text-slate-500">The student has not submitted any solutions for this brief yet.</p>
                                        </div>
                                    </section>
                                </div>
                                <div v-else class="h-[40vh] flex flex-col items-center justify-center bg-[#0f1f23]/30 rounded-3xl border border-dashed border-slate-800">
                                    <div class="size-20 rounded-full bg-slate-800/50 flex items-center justify-center text-slate-600 mb-6">
                                        <span class="material-symbols-outlined text-5xl">person_off</span>
                                    </div>
                                    <h3 class="text-xl font-bold adventure-title text-slate-500 mb-2">No Submission Found</h3>
                                    <p class="text-sm text-slate-600 font-medium">The student has not reported completion of this mission yet.</p>
                                </div>
                            </div>

                            <!-- TAB 3: CORRECTION -->
                            <div v-if="activeTab === 'correction'" class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
                                <div v-if="currentCorrection" class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
                                    <!-- Summary Header -->
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <div class="md:col-span-2 bg-[#0f1f23] border border-slate-800 rounded-3xl p-8 parchment-effect relative overflow-hidden shadow-2xl">
                                            <div class="absolute top-0 right-0 p-8 opacity-5">
                                                <span class="material-symbols-outlined text-9xl text-pirate-gold">military_tech</span>
                                            </div>
                                            <div class="relative z-10 flex items-center gap-6">
                                                <div :class="['size-20 rounded-2xl flex items-center justify-center shadow-2xl border-2 transition-all', 
                                                    currentCorrection.result === 'Valid' ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-500 shadow-emerald-500/10' : 'bg-rose-500/10 border-rose-500/30 text-rose-500 shadow-rose-500/10']">
                                                    <span class="material-symbols-outlined text-5xl">{{ currentCorrection.result === 'Valid' ? 'verified' : 'cancel' }}</span>
                                                </div>
                                                <div>
                                                    <h3 class="text-3xl font-black adventure-title tracking-tight text-slate-100">{{ currentCorrection.result === 'Valid' ? 'Mission Success' : 'Mission Failed' }}</h3>
                                                    <p class="text-slate-500 font-bold uppercase text-[10px] tracking-[0.3em] mt-1">Status: {{ currentCorrection.result }} • Recorded by Adm. {{ currentCorrection.teacher?.last_name }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="bg-[#0a1416] border border-slate-800 rounded-3xl p-6 flex flex-col justify-center items-center text-center group hover:border-pirate-gold/20 transition-all">
                                            <div class="size-12 rounded-full bg-slate-800/50 flex items-center justify-center text-slate-500 mb-3 group-hover:bg-pirate-gold/10 group-hover:text-pirate-gold transition-all">
                                                <span class="material-symbols-outlined">event</span>
                                            </div>
                                            <p class="text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1">Appraisal Date</p>
                                            <p class="text-sm font-bold text-slate-300">{{ new Date(currentCorrection.created_at).toLocaleDateString(undefined, { dateStyle: 'long' }) }}</p>
                                        </div>
                                    </div>

                                    <!-- Feedback Message -->
                                    <div class="space-y-4">
                                        <h4 class="text-xs font-black text-pirate-gold uppercase tracking-[0.3em] flex items-center gap-3">
                                            Official Appraisal
                                            <span class="h-px flex-1 bg-pirate-gold/20"></span>
                                        </h4>
                                        <div class="bg-black/30 rounded-2xl p-8 border border-slate-800 shadow-inner relative group backdrop-blur-sm text-slate-200">
                                            <div class="absolute -top-4 -left-4 size-10 rounded-xl bg-pirate-gold flex items-center justify-center text-background-dark shadow-lg rotate-12 group-hover:rotate-0 transition-all duration-500">
                                                <span class="material-symbols-outlined">history_edu</span>
                                            </div>
                                            <!-- Styled Markdown Content -->
                                            <div class="prose prose-invert max-w-none prose-sm prose-p:leading-relaxed prose-headings:text-pirate-gold prose-headings:font-black prose-strong:text-pirate-gold prose-code:text-emerald-400 prose-code:bg-slate-900/50 prose-code:px-1 prose-code:rounded overflow-hidden" 
                                                v-html="currentCorrectionMessage">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Detailed Skills Grades -->
                                    <div class="space-y-4">
                                        <h4 class="text-xs font-black text-pirate-gold uppercase tracking-[0.3em] flex items-center gap-3">
                                            Skill Mastery Breakdown
                                            <span class="h-px flex-1 bg-pirate-gold/20"></span>
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div v-for="detail in currentCorrection.correction_details" :key="detail.id"
                                                class="bg-[#152a2e]/60 border border-slate-800 rounded-2xl p-5 flex items-center justify-between group hover:border-pirate-gold/30 transition-all hover:bg-[#1a3439]/80">
                                                <div class="flex items-center gap-4">
                                                    <div class="size-12 rounded-xl bg-slate-900 flex items-center justify-center text-xs font-black text-pirate-gold group-hover:scale-105 group-hover:bg-pirate-gold group-hover:text-background-dark transition-all shadow-inner">
                                                        {{ detail.brief_skill_level?.skill?.code }}
                                                    </div>
                                                    <div>
                                                        <h5 class="text-sm font-bold text-slate-200 leading-tight">{{ detail.brief_skill_level?.skill?.title }}</h5>
                                                        <p class="text-[10px] text-slate-500 font-bold uppercase mt-0.5 tracking-tighter">{{ detail.brief_skill_level?.level?.name }}</p>
                                                    </div>
                                                </div>
                                                <div :class="['px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest border transition-all shadow-sm', getGradeColor(detail.grade)]">
                                                    {{ detail.grade }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Evaluation Pending / No Correction -->
                                <div v-else class="space-y-8">
                                    <!-- Job Status if running -->
                                    <div v-if="(currentSubmission?.evaluation_job?.status === 'pending' || currentSubmission?.evaluation_job?.status === 'processing') || 
                                              (currentSubmission?.problem_submission_job?.status === 'pending' || currentSubmission?.problem_submission_job?.status === 'running')" 
                                        class="h-[50vh] flex flex-col items-center justify-center bg-[#0f1f23]/30 rounded-3xl border border-dashed border-pirate-gold/30">
                                        <div class="relative mb-10">
                                            <div class="size-24 rounded-full border-2 border-pirate-gold/20 border-t-pirate-gold animate-spin"></div>
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <span class="material-symbols-outlined text-4xl text-pirate-gold animate-pulse">analytics</span>
                                            </div>
                                        </div>
                                        <h3 class="text-2xl font-bold adventure-title text-pirate-gold mb-3">AI Scribes are busy...</h3>
                                        <p class="text-slate-500 text-center max-w-md px-6">
                                            The system is currently evaluating the student's submission. 
                                            The captain's verdict will be available once the analysis is complete.
                                        </p>
                                    </div>

                                    <!-- Evaluate Now if jobs done but no correction -->
                                    <div v-else class="h-[50vh] flex flex-col items-center justify-center bg-[#0f1f23]/30 rounded-3xl border border-dashed border-slate-800">
                                        <div class="size-24 rounded-full bg-slate-800/50 flex items-center justify-center text-slate-600 mb-8">
                                            <span class="material-symbols-outlined text-6xl">gavel</span>
                                        </div>
                                        <h3 class="text-2xl font-bold adventure-title text-slate-400 mb-4">No Verdict Rendered</h3>
                                        <p class="text-slate-500 mb-8 text-center max-w-sm px-6">
                                            The student has submitted their payload, but no official appraisal has been recorded yet.
                                        </p>
                                        <button class="bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark px-10 py-4 rounded-2xl font-black text-sm uppercase tracking-[0.2em] transition-all transform hover:scale-105 active:scale-95 shadow-[0_0_30px_rgba(212,175,55,0.2)]">
                                            Evaluate Now
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Empty State: No Selection -->
                <div v-else class="flex-1 flex flex-col items-center justify-center">
                    <div class="size-32 rounded-full bg-slate-800/20 border border-slate-800/50 flex items-center justify-center text-slate-700 mb-8 relative">
                        <span class="material-symbols-outlined text-7xl animate-pulse">explore</span>
                        <div class="absolute inset-0 rounded-full border border-pirate-gold/10 animate-ping"></div>
                    </div>
                    <h2 class="text-3xl font-bold adventure-title text-slate-500 mb-3 tracking-wider">Uncharted Waters</h2>
                    <p class="text-slate-600 font-medium text-center max-w-sm px-6">
                        Select a <span class="text-pirate-gold">Crew Member</span> and <span class="text-pirate-gold">Mission Brief</span> from the registry to begin the evaluation process.
                    </p>
                </div>

                <!-- Footer Info -->
                <footer class="h-10 border-t border-slate-800 bg-[#0d1b1e] px-6 flex items-center justify-between text-[9px] font-black text-slate-600 uppercase tracking-[0.2em] shrink-0 z-20">
                    <div class="flex gap-6">
                        <span class="flex items-center gap-1.5"><span class="size-1.5 bg-emerald-500 rounded-full shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span> Marine HQ Online</span>
                        <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[12px]">security</span> Encryption: AES-256</span>
                    </div>
                    <div class="flex gap-4">
                        <span class="text-pirate-gold/50 tracking-tighter">PROTO-ID: {{ selectedBriefId ? `BRF-${selectedBriefId}` : 'NONE' }}</span>
                    </div>
                </footer>
            </div>
        </main>
    </NuxtLayout>
</template>

<style scoped>
    .parchment-effect {
        background-image: radial-gradient(circle at 2px 2px, rgba(212,175,55,0.03) 1px, transparent 0);
        background-size: 24px 24px;
    }

    .adventure-title {
        font-family: 'Outfit', sans-serif;
        letter-spacing: -0.02em;
    }

    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #1e3a41;
        border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #d4af37;
    }

    select option {
        background-color: #0d1b1e;
        color: #e2e8f0;
    }
</style>