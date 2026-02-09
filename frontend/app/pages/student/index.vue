<script setup lang="ts">
    import { useBrief } from '~~/stores/brief';
    import type { Brief } from '~~/types/brief';
import { useSubmitting } from '../../../stores/submitting';

    useHead({
        title: 'Student Dashboard - Bounty Board'
    })

    const store = useBrief();
    const briefs = ref<Brief[] | null>(null)
    const brief_type: Ref<string> = ref('solo')
    const selected_brief: Ref<number | null> = ref(null)
    const submitted_briefs = ref<number[] | null>(null)
    const briefs_count: Ref<number> = ref(0)
    const submitting = useSubmitting()

    const toggleBriefType = (type: string)=>{
        brief_type.value = type
        briefs_count.value = briefs.value?.filter(b => (b.is_collective && type == "squad") || (!b.is_collective && type == "solo")).length as number
    }

    const getBrief = async (id: number) => {
        selected_brief.value = id
        await store.fetchBrief(id)
    }

    const selectBrief = (id: number)=>{
        selected_brief.value = id
        getBrief(id)
    }

    onMounted(async() => {
        await submitting.fetchStudentSubmittings();
        submitted_briefs.value = submitting.submittings?.map(b => b.brief_id) as number[]
        briefs.value = await store.fetchStudentBriefs();
        toggleBriefType('solo')
        selectBrief(briefs.value?.find(b => !b.is_collective)?.id as number)
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
                        <template v-if="briefs_count as number > 0">
                            <template v-for="brief in briefs" :key="brief.id">
                                <template v-if="(brief.is_collective && brief_type === 'squad') || (!brief.is_collective && brief_type === 'solo')">
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
                <div class="flex-1 flex flex-col bg-[#101f22] overflow-y-auto bounty-scroll">
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
                                    Formation
                                </p>
                                <p class="text-lg font-bold">{{ store.brief?.class_group.formation.title }}</p>
                                <p class="text-xs text-slate-400 w-[80%] truncate">{{ store.brief?.class_group.formation.description }}</p>
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
                                        <span class="text-slate-300">{{ brief_skill_level.skill.code }}: {{ brief_skill_level.skill.title }}, requred level: {{ brief_skill_level.level.name }}</span>
                                    </li>
                                </ul>
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
                            <NuxtLink :to="`/student/submitting/${selected_brief}/create`"
                                class="bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark px-8 py-4 rounded-xl font-black text-lg uppercase tracking-widest transition-all shadow-[0_8px_20px_rgba(212,175,55,0.3)] hover:shadow-[0_8px_30px_rgba(212,175,55,0.5)] flex items-center gap-3 border border-pirate-gold-dark/30 hover:-translate-y-1">
                                <span class="material-symbols-outlined text-2xl">diamond</span>
                                Deliver Treasure
                            </NuxtLink>
                        </div>
                    </div>
                    <div v-else
                        class="p-6 border-t border-slate-200 dark:border-[#224249] bg-white dark:bg-[#102023] shrink-0 sticky bottom-0 z-20">
                        <div class="max-w-4xl mx-auto flex items-center justify-end">
                            <NuxtLink :to="`/student/submitting?brief_id=${selected_brief}`"
                                class="bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark px-8 py-4 rounded-xl font-black text-lg uppercase tracking-widest transition-all shadow-[0_8px_20px_rgba(212,175,55,0.3)] hover:shadow-[0_8px_30px_rgba(212,175,55,0.5)] flex items-center gap-3 border border-pirate-gold-dark/30 hover:-translate-y-1">
                                <span class="material-symbols-outlined text-2xl">visibility</span>
                                See Your Delivery
                            </NuxtLink>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </NuxtLayout>
</template>