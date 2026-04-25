<script setup lang="ts">
    import { useBrief } from '~~/stores/brief';
    import type { Brief } from '~~/types/brief';
    import { useSubmission } from '../../../stores/submission';

    useHead({
        title: 'Student Dashboard - Bounty Board'
    })

    const store = useBrief();
    const briefs = ref<Brief[] | null>(null)
    const brief_type: Ref<string> = ref('solo')
    const selected_brief: Ref<number | null> = ref(null)
    const submitted_briefs = ref<number[] | null>(null)
    const briefs_count: Ref<number> = ref(0)
    const submission = useSubmission()

    const toggleBriefType = (type: string)=>{
        brief_type.value = type
        briefs_count.value = briefs.value?.filter(b => (Number(b.is_collective) && type === "squad") || (!Number(b.is_collective) && type === "solo")).length || 0
    }

    const architectureRules = computed(() => {
        if (!store.brief?.brief_versions?.length) return null;
        const rules = store.brief.brief_versions[0].architecture_rules;
        if (Array.isArray(rules)) {
            return { isArray: true, arrayRules: rules, objRules: null };
        }
        return { isArray: false, arrayRules: null, objRules: rules as any };
    })

    const getBrief = async (id: number) => {
        selected_brief.value = id
        await store.fetchStudentBrief(id)
    }

    const selectBrief = (id: number)=>{
        selected_brief.value = id
        getBrief(id)
    }

    onMounted(async() => {
        await submission.fetchStudentSubmissions();
        submitted_briefs.value = submission.submissions?.map(b => b.brief_id) as number[]
        briefs.value = await store.fetchStudentBriefs();
        
        const first_solo = briefs.value?.find(b => !Number(b.is_collective));
        const first_squad = briefs.value?.find(b => Number(b.is_collective));

        if (first_solo) {
            toggleBriefType('solo')
            selectBrief(first_solo.id as number)
        } else if (first_squad) {
            toggleBriefType('squad')
            selectBrief(first_squad.id as number)
        } else {
            toggleBriefType('solo')
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
                        <button @click="toggleBriefType('solo')"
                            :class="`flex-1 py-2 px-4 rounded-lg font-bold text-sm  ${brief_type === 'solo' ? 'bg-primary text-background-dark shadow-lg shadow-primary/20' : 'bg-slate-100 dark:bg-[#224249] text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-[#2d565f]'} transition-all flex items-center justify-center gap-2`">
                            <span class="material-symbols-outlined text-lg">person</span>
                            Solo
                        </button>
                        <button @click="toggleBriefType('squad')"
                            :class="`flex-1 py-2 px-4 rounded-lg font-bold text-sm  ${brief_type === 'squad' ? 'bg-primary text-background-dark shadow-lg shadow-primary/20' : 'bg-slate-100 dark:bg-[#224249] text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-[#2d565f]'} transition-all flex items-center justify-center gap-2`">
                            <span class="material-symbols-outlined text-lg">groups</span>
                            Squad
                        </button>
                    </div>
                    <div class="flex-1 overflow-y-auto bounty-scroll p-4 space-y-4">
                        <template v-if="briefs_count > 0">
                            <template v-for="brief in briefs" :key="brief.id">
                                <template v-if="(Number(brief.is_collective) && brief_type === 'squad') || (!Number(brief.is_collective) && brief_type === 'solo')">
                                    <div v-if="selected_brief === brief.id"

                                        class="p-4 rounded-xl bg-primary/10 border-2 border-primary shadow-lg cursor-pointer transition-all">
                                        <div class="flex justify-between items-start mb-2">
                                            <span
                                                class="px-2 py-0.5 rounded bg-primary text-background-dark text-[10px] font-black uppercase tracking-wider">ACTIVE</span>
                                            <span class="text-xs font-bold text-pirate-gold">500 ฿</span>
                                        </div>
                                        <h3 class="adventure-title text-lg font-bold leading-tight mb-1">
                                            {{ brief.title }}
                                        </h3>
                                        <p class="text-xs text-slate-400 line-clamp-2 mb-3">
                                            {{ brief.description }}
                                        </p>
                                        <div class="flex items-center gap-4">
                                            <div class="flex items-center gap-1 text-[10px] font-bold text-slate-400 uppercase">
                                                <span class="material-symbols-outlined text-sm">schedule</span>
                                                {{ brief.end_date }}
                                            </div>
                                            <div class="flex items-center gap-1 text-[10px] font-bold text-slate-400 uppercase">
                                                <span class="material-symbols-outlined text-sm">terminal</span>
                                                {{ brief.sprint.name }}
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else @click="selectBrief(brief.id)"
                                        class="p-4 rounded-xl bg-slate-50 dark:bg-[#182f34] border border-slate-200 dark:border-[#224249] hover:border-primary/50 cursor-pointer transition-all">
                                        <div class="flex justify-between items-start mb-2">
                                            <span v-if="submitted_briefs?.find(b => b === brief.id) === undefined"
                                                class="px-2 py-0.5 rounded bg-yellow-500/20 text-yellow-500 text-[10px] font-black uppercase tracking-wider">Not Submitted</span>
                                            <span v-else
                                                class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-500 text-[10px] font-black uppercase tracking-wider">Submitted</span>
                                            <span class="text-xs font-bold text-pirate-gold">300 ฿</span>
                                        </div>
                                        <h3 class="adventure-title text-lg font-bold leading-tight mb-1">{{ brief.title }}</h3>
                                        <p class="text-xs text-slate-400 line-clamp-2 mb-3">{{ brief.description }}</p>
                                        <div class="flex items-center gap-4">
                                            <div class="flex items-center gap-1 text-[10px] font-bold text-slate-400 uppercase">
                                                <span class="material-symbols-outlined text-sm">schedule</span>
                                                {{ brief.end_date }}
                                            </div>
                                            <div class="flex items-center gap-1 text-[10px] font-bold text-slate-400 uppercase">
                                                <span class="material-symbols-outlined text-sm">terminal</span>
                                                {{ brief.sprint.name }}
                                            </div>
                                        </div>
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
                <div class="flex-1 flex flex-col bg-[#101f22] overflow-y-auto bounty-scroll relative">
                    <template v-if="selected_brief && store.brief">
                        <div class="p-8 max-w-4xl mx-auto w-full flex-1">
                            <div class="mb-8 flex items-center justify-between">
                                <div>
                                    <span class="text-primary font-bold text-sm tracking-widest uppercase">Mission Briefing
                                        #{{ store.brief?.id }}</span>
                                    <h2 class="text-4xl font-bold adventure-title mt-2">
                                        {{ store.brief?.title }}
                                    </h2>
                                </div>
                                <div class="text-right">
                                    <div class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">
                                        Reward Pool
                                    </div>
                                    <div class="text-3xl font-bold text-pirate-gold">
                                        500 ฿
                                        <span class="text-sm text-slate-500 font-sans">Belly</span>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-6 mb-10">
                                <div class="bg-[#182f34] p-4 rounded-xl border border-primary/20">
                                    <p class="text-[10px] font-black text-primary uppercase tracking-widest mb-1">
                                        Sprint Phase
                                    </p>
                                    <p class="text-lg font-bold">{{ store.brief?.sprint.name }}</p>
                                    <p class="text-xs text-slate-400 w-[80%] truncate">{{ store.brief?.sprint.description }}</p>
                                </div>
                                <div class="bg-[#182f34] p-4 rounded-xl border border-primary/20">
                                    <p class="text-[10px] font-black text-primary uppercase tracking-widest mb-1">
                                        Class Group
                                    </p>
                                    <p class="text-lg font-bold">{{ store.brief?.class_group?.name }}</p>
                                    <p class="text-xs text-slate-400 w-[80%] truncate">{{ store.brief?.class_group?.description }}</p>
                                </div>
                                <div class="bg-[#182f34] p-4 rounded-xl border border-primary/20">
                                    <p class="text-[10px] font-black text-primary uppercase tracking-widest mb-1">
                                        Deadline
                                    </p>
                                    <p class="text-lg font-bold">{{ (new Date(store.brief?.end_date as string)).toLocaleString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) }}</p>
                                    <p class="text-xs text-accent-red font-bold">{{ (new Date(store.brief?.end_date as string)).toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true }) }}</p>
                                </div>
                            </div>
                            <div class="space-y-8">
                                <section>
                                    <h4 class="adventure-title text-2xl mb-4 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-primary">flag</span>
                                        The Objective
                                    </h4>
                                    <div class="bg-white/5 p-6 rounded-xl parchment-effect border border-white/5">
                                        <p class="text-slate-300 leading-relaxed">
                                            {{ store.brief?.description }}
                                        </p>
                                    </div>
                                </section>
                                <section>
                                    <h4 class="adventure-title text-2xl mb-4 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-primary">article</span>
                                        Content
                                    </h4>
                                    <div class="bg-white/5 p-6 rounded-xl parchment-effect border border-white/5">
                                        <p v-html="store.brief?.content" class="text-slate-300 leading-relaxed">
                                        </p>
                                    </div>
                                </section>
                                <section>
                                    <h4 class="adventure-title text-2xl mb-4 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-primary">format_list_bulleted</span>
                                        Requirements
                                    </h4>
                                    <ul class="space-y-3">
                                        <li v-for="brief_skill_level in store.brief?.brief_skill_levels" class="flex gap-3 text-sm">
                                            <span
                                                class="material-symbols-outlined text-primary text-sm mt-0.5">verified</span>
                                            <span class="text-slate-300">{{ brief_skill_level.skill.code }}: {{ brief_skill_level.skill.title }}, required level: {{ brief_skill_level.level.name }}</span>
                                        </li>
                                    </ul>
                                </section>
                                
                                <section v-if="store.brief?.stack && architectureRules">
                                    <h4 class="adventure-title text-2xl mb-4 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-primary">architecture</span>
                                        Architecture Rules
                                    </h4>
                                    <div class="bg-white/5 p-6 rounded-xl parchment-effect border border-white/5">
                                        <div class="mb-4">
                                            <span class="px-3 py-1 rounded-lg bg-primary/20 text-primary font-bold text-sm border border-primary/30 uppercase tracking-widest">{{ store.brief?.stack.name }}</span>
                                        </div>
                                        <div v-if="architectureRules.isArray">
                                            <ul class="list-disc list-inside text-sm text-slate-300 font-mono">
                                                <li v-for="rule in architectureRules.arrayRules" :key="rule">{{ rule }}</li>
                                            </ul>
                                        </div>
                                        <div v-else class="space-y-4">
                                            <div v-if="architectureRules.objRules?.required?.length" class="space-y-1">
                                                <p class="text-xs font-black uppercase tracking-widest text-emerald-500">Required Paths</p>
                                                <ul class="list-disc list-inside text-sm text-slate-300 font-mono">
                                                    <li v-for="rule in architectureRules.objRules.required" :key="rule">{{ rule }}</li>
                                                </ul>
                                            </div>
                                            <div v-if="architectureRules.objRules?.forbidden?.length" class="space-y-1">
                                                <p class="text-xs font-black uppercase tracking-widest text-red-500">Forbidden Paths</p>
                                                <ul class="list-disc list-inside text-sm text-slate-300 font-mono">
                                                    <li v-for="rule in architectureRules.objRules.forbidden" :key="rule">{{ rule }}</li>
                                                </ul>
                                            </div>
                                            <div v-if="architectureRules.objRules?.structure" class="space-y-1">
                                                <p class="text-xs font-black uppercase tracking-widest text-blue-400">Structure</p>
                                                <ul class="list-disc list-inside text-sm text-slate-300 font-mono">
                                                    <li v-for="(type, path) in architectureRules.objRules.structure" :key="path">
                                                        {{ path }} <span class="text-slate-500 text-xs">({{ type }})</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                <section v-if="store.brief?.brief_versions?.length && store.brief.brief_versions[0].test_config">
                                    <h4 class="adventure-title text-2xl mb-4 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-primary">science</span>
                                        Test Configuration
                                    </h4>
                                    <div class="bg-white/5 p-6 rounded-xl parchment-effect border border-white/5 space-y-4">
                                        <div class="flex items-center gap-4 text-sm text-slate-300 font-mono flex-wrap">
                                            <div class="px-3 py-1 rounded bg-slate-800/50 border border-slate-700">Type: <span class="text-primary font-bold uppercase">{{ store.brief.brief_versions[0].test_config.type }}</span></div>
                                            <div class="px-3 py-1 rounded bg-slate-800/50 border border-slate-700">Timeout: <span class="text-pirate-gold font-bold">{{ store.brief.brief_versions[0].test_config.timeout_seconds }}s</span></div>
                                            <div class="px-3 py-1 rounded bg-slate-800/50 border border-slate-700">Retries: <span class="text-blue-400 font-bold">{{ store.brief.brief_versions[0].test_config.retries }}</span></div>
                                        </div>

                                        <div v-if="(store.brief.brief_versions[0].test_config.tests as any[])?.length" class="space-y-4 mt-6 border-t border-white/10 pt-6">
                                            <p class="text-xs font-black uppercase tracking-widest text-slate-500">Test Cases</p>
                                            <div class="grid grid-cols-1 gap-4">
                                                <div v-for="(test, idx) in store.brief.brief_versions[0].test_config.tests" :key="idx" class="bg-black/40 p-4 rounded-lg border border-white/5 text-sm font-mono space-y-3 relative">
                                                    <div class="absolute -top-2 -left-2 size-6 rounded bg-slate-700 text-slate-300 flex items-center justify-center text-[10px] font-black">
                                                        #{{ idx + 1 }}
                                                    </div>
                                                    
                                                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 pl-4">
                                                        <div class="flex items-center gap-3">
                                                            <span :class="{'text-emerald-400': (test as any).method === 'GET', 'text-blue-400': (test as any).method === 'POST', 'text-yellow-400': (test as any).method === 'PUT', 'text-red-400': (test as any).method === 'DELETE', 'text-purple-400': (test as any).method === 'PATCH'}" class="font-black">{{ (test as any).method }}</span>
                                                            <span class="text-slate-300">{{ (test as any).url }}</span>
                                                        </div>
                                                        <span class="text-slate-500 text-xs sm:ml-auto block">{{ (test as any).name }}</span>
                                                    </div>

                                                    <div v-if="(test as any).headers?.length" class="pl-4">
                                                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1">Headers</p>
                                                        <div class="flex flex-wrap gap-2">
                                                            <span v-for="header in (test as any).headers" :key="header.name" class="px-2 py-0.5 rounded text-[10px] bg-slate-800 text-slate-300 border border-slate-700">{{ header.name }}: {{ header.value }}</span>
                                                        </div>
                                                    </div>

                                                    <div v-if="(test as any).body" class="pl-4">
                                                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1">Body</p>
                                                        <div class="p-2 rounded bg-slate-900 border border-slate-800 text-xs text-slate-400 overflow-x-auto whitespace-pre-wrap">{{ JSON.stringify((test as any).body, null, 2) }}</div>
                                                    </div>

                                                    <div v-if="(test as any).expected" class="pl-4 border-t border-white/5 pt-3 mt-3">
                                                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1">Expected Results</p>
                                                        <div class="flex flex-col gap-1">
                                                            <div v-if="(test as any).expected.status" class="flex gap-2 items-center">
                                                                <span class="text-slate-500">Status:</span>
                                                                <span :class="(test as any).expected.status >= 200 && (test as any).expected.status < 300 ? 'text-emerald-400' : 'text-red-400'" class="font-bold">{{ (test as any).expected.status }}</span>
                                                            </div>
                                                            <div v-if="(test as any).expected.json_contains?.length" class="flex flex-col gap-1 mt-1">
                                                                <span class="text-slate-500">JSON Must Contain Keys:</span>
                                                                <div class="flex flex-wrap gap-2">
                                                                    <span v-for="key in (test as any).expected.json_contains" :key="key" class="px-2 py-0.5 rounded text-[10px] bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">{{ key }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>

                        </div>
                        <div v-if="submitted_briefs?.find(b => b === selected_brief) === undefined"
                            class="p-6 border-t border-slate-200 dark:border-[#224249] bg-white dark:bg-[#102023] shrink-0 sticky bottom-0 z-20">
                            <div class="max-w-4xl mx-auto flex items-center justify-between">
                                <div
                                    class="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-widest">
                                    <span class="material-symbols-outlined text-sm">info</span>
                                    Attach your Git repository link
                                </div>
                                <NuxtLink :to="`/student/submission/${selected_brief}/create`"
                                    class="bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark px-8 py-4 rounded-xl font-black text-lg uppercase tracking-widest transition-all shadow-[0_8px_20px_rgba(212,175,55,0.3)] hover:shadow-[0_8px_30px_rgba(212,175,55,0.5)] flex items-center gap-3 border border-pirate-gold-dark/30 hover:-translate-y-1">
                                    <span class="material-symbols-outlined text-2xl">diamond</span>
                                    Deliver Treasure
                                </NuxtLink>
                            </div>
                        </div>
                        <div v-else
                            class="p-6 border-t border-slate-200 dark:border-[#224249] bg-white dark:bg-[#102023] shrink-0 sticky bottom-0 z-20">
                            <div class="max-w-4xl mx-auto flex items-center justify-end">
                                <NuxtLink :to="`/student/submission?brief_id=${selected_brief}`"
                                    class="bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark px-8 py-4 rounded-xl font-black text-lg uppercase tracking-widest transition-all shadow-[0_8px_20px_rgba(212,175,55,0.3)] hover:shadow-[0_8px_30px_rgba(212,175,55,0.5)] flex items-center gap-3 border border-pirate-gold-dark/30 hover:-translate-y-1">
                                    <span class="material-symbols-outlined text-2xl">visibility</span>
                                    See Your Delivery
                                </NuxtLink>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div class="flex-1 flex flex-col items-center justify-center p-8 text-center h-full">
                            <div class="size-24 rounded-full bg-slate-800 border-2 border-slate-700 flex items-center justify-center text-slate-500 mb-6">
                                <span class="material-symbols-outlined text-5xl">explore_off</span>
                            </div>
                            <h3 class="text-2xl font-bold adventure-title text-slate-300 mb-2">No Mission Selected</h3>
                            <p class="text-slate-500 max-w-md">Select a mission from the Bounty Board to view its briefing details, architecture rules, and test configuration.</p>
                        </div>
                    </template>
                </div>
            </div>
        </main>
    </NuxtLayout>
</template>