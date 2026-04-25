<script setup lang="ts">
    import { useSubmission } from '~~/stores/submission';
    import { useBrief } from '~~/stores/brief';
    import type { ReturnData } from '~~/types/api';

    useHead({
        title: 'Student Dashboard - Bounty Board'
    })

    const id: number = parseInt(useRoute().params?.id as string)

    const store = useSubmission();
    const briefs = useBrief();

    const form = reactive({
        brief_id: id,
        message: '',
        link: '',
    })

    const errs = ref({
        brief_id: '',
        message: '',
        link: '',
    })

    onMounted(async() => {
        await briefs.fetchStudentBrief(id)
    })

    const submit = async () => {
        const res : ReturnData<any> = await store.createSubmission(form);
        if(res.success) {
            navigateTo('/student/submission')
        }
        
        if(res.errors) {
            errs.value = res.errors
        }
    }

    definePageMeta({
        middleware: ['auth', 'student']
    })

</script>

<template>
    <NuxtLayout name="student">
        <main class="flex-1 flex flex-col overflow-hidden">
            <header
                class="h-16 border-b border-slate-200 dark:border-[#224249] bg-white/80 dark:bg-background-dark/80 backdrop-blur-md flex items-center justify-between px-8 sticky top-0 z-10">
                <div class="flex items-center gap-4">
                    <h2 class="text-2xl font-bold tracking-tight adventure-title text-primary">Bounty Board</h2>
                    <div class="hidden md:flex items-center gap-2">
                        <span class="h-4 w-[1px] bg-slate-200 dark:bg-[#224249]"></span>
                        <p class="text-xs text-slate-500 dark:text-slate-400 italic">"Delivering Treasure to the Fleet
                            Captain"</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-bold text-pirate-gold">500,000,000 ฿</span>
                    <span class="material-symbols-outlined text-pirate-gold">monetization_on</span>
                </div>
            </header>
            <div class="flex flex-1 overflow-hidden">
                <div class="flex-1 p-8 overflow-y-auto">
                    <div class="max-w-2xl mx-auto space-y-6">
                        <div class="flex items-center gap-2 mb-2">
                            <NuxtLink to="/student/briefs"
                                class="text-primary hover:text-primary/80 flex items-center gap-1 text-sm font-bold">
                                <span class="material-symbols-outlined text-sm">arrow_back</span>
                                Back to Board
                            </NuxtLink>
                        </div>
                        <div
                            class="bg-white dark:bg-[#182f34] border border-slate-200 dark:border-[#224249] rounded-xl p-6 shadow-xl parchment-effect">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <span
                                        class="px-2 py-1 rounded bg-accent-red/10 text-accent-red text-[10px] font-bold border border-accent-red/20 mb-2 inline-block">S-RANK
                                        MISSION</span>
                                    <h3 class="text-3xl font-bold adventure-title tracking-wide text-primary">{{ briefs.brief?.title }}</h3>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] text-slate-500 uppercase font-black">Deadline</p>
                                    <p class="text-sm font-bold text-accent-red">{{ briefs.brief?.end_date ? (new Date(briefs.brief?.end_date as string)).toLocaleString('en-US', { month: 'long', day: 'numeric', year: 'numeric'}) : 'N/A'  }}</p>
                                </div>
                            </div>
                            <div
                                class="prose prose-sm dark:prose-invert max-w-none text-slate-600 dark:text-slate-300 space-y-4">
                                <p>{{ briefs.brief?.description }}</p>
                                <div class="bg-black/5 dark:bg-black/20 p-4 rounded-lg border-l-4 border-primary">
                                    <h4 class="text-xs font-bold uppercase mb-2">Requirements:</h4>
                                    <ul class="list-disc list-inside text-xs space-y-1">
                                        <li v-for="brief_skill_level in briefs.brief?.brief_skill_levels" :key="brief_skill_level.id">{{ brief_skill_level.skill.code }}: {{ brief_skill_level.skill.title }}, Required level: {{ brief_skill_level.level.name }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <form @submit.prevent="submit"
                    class="w-[450px] border-l border-slate-200 dark:border-[#224249] bg-white dark:bg-[#102023] flex flex-col">
                    <div class="p-6 border-b border-slate-200 dark:border-[#224249]">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="material-symbols-outlined text-pirate-gold">workspace_premium</span>
                            <h3 class="text-lg font-bold adventure-title uppercase">Deliver Treasure</h3>
                        </div>
                        <p class="text-xs text-slate-500">Seal your findings and send them to the Fleet Captain.</p>
                    </div>
                    <div class="flex-1 overflow-y-auto p-6 space-y-8">
                        <section class="space-y-3">
                            <label class="block text-sm font-bold adventure-title tracking-wide" for="message">Message
                                to the Captain</label>
                            <textarea v-model="form.message"
                                class="w-full bg-slate-50 dark:bg-background-dark border-slate-200 dark:border-[#224249] rounded-xl p-4 text-sm focus:ring-primary focus:border-primary placeholder:italic resize-none"
                                id="message"
                                placeholder="Captain, I found something interesting near the Reverse Mountain..."
                                rows="4"></textarea>
                            <p v-if="errs.message" class="text-red-500 text-xs mt-1">{{ errs.message }}</p>
                        </section>
                        <section class="space-y-4">
                            <label class="block text-sm font-bold adventure-title tracking-wide" for="link">Repository Link</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="material-symbols-outlined text-primary group-focus-within:text-pirate-gold transition-colors">anchor</span>
                                </div>
                                <input v-model="form.link" id="link" autocomplete="off"
                                    class="block w-full pl-11 bg-slate-50 dark:bg-background-dark border-slate-200 dark:border-[#224249] text-sm rounded-xl focus:ring-primary focus:border-primary transition-all py-3.5"
                                    placeholder="https://github.com/crew/project-one" type="url" />
                            </div>
                            <p v-if="errs.link" class="text-red-500 text-xs mt-1">{{ errs.link }}</p>
                        </section>
                        <div class="p-4 bg-pirate-gold/5 border border-pirate-gold/20 rounded-xl">
                            <div class="flex gap-3">
                                <span class="material-symbols-outlined text-pirate-gold text-lg shrink-0">info</span>
                                <p class="text-[11px] leading-relaxed text-slate-500 dark:text-slate-400">Once
                                    submitted, the treasure will be locked for review by the Marine high command. Make
                                    sure all your charts are accurate!</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 border-t border-slate-200 dark:border-[#224249]">
                        <button
                            class="w-full bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark py-4 rounded-xl font-black text-sm transition-all shadow-[0_4px_15px_rgba(212,175,55,0.4)] hover:shadow-[0_4px_25px_rgba(212,175,55,0.6)] flex items-center justify-center gap-3 border border-pirate-gold-dark/30 adventure-title uppercase tracking-widest text-lg">
                            <span class="material-symbols-outlined text-2xl">send</span>
                            Submit to Fleet
                        </button>
                    </div>
                </form>
            </div>
            <footer
                class="mt-auto border-t border-slate-200 dark:border-[#224249] bg-white dark:bg-[#102023] px-8 py-3 flex items-center justify-between text-[11px] font-medium text-slate-500 uppercase tracking-widest">
                <div class="flex gap-6">
                    <span class="flex items-center gap-1.5"><span
                            class="size-2 bg-emerald-500 rounded-full shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                        Vessel: Going Merry</span>
                    <span class="flex items-center gap-1.5"><span
                            class="material-symbols-outlined text-[14px]">anchor</span> Port: Alabasta</span>
                </div>
                <div class="flex gap-4">
                    <span class="text-primary/70">V 2.4.0-ARABASTA</span>
                    <a class="hover:text-primary transition-colors flex items-center gap-1" href="#">
                        <span class="material-symbols-outlined text-[14px]">help</span> Tavern Support
                    </a>
                </div>
            </footer>
        </main>
    </NuxtLayout>
</template>
e>