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

    const links = ref([] as Array<{ [key: string]: string }>)

    const link = reactive({
        label: '',
        url: '',
    })

    const link_errs = ref({
        label: '',
        url: '',
    })

    const openLinksModal = () => {
        const modal = document.getElementById('links-modal') as HTMLElement;
        modal.classList.remove('hidden');
    }

    const closeLinksModal = () => {
        const modal = document.getElementById('links-modal') as HTMLElement;
        modal.classList.add('hidden');
    }

    const addLink = () => {
        if (link.label === '' || link.url === '') {
            link_errs.value.label = link.label === '' ? 'Link label is required' : '';
            link_errs.value.url = link.url === '' ? 'Link url is required' : '';
            return;
        }
        links.value.push({[link.label]: link.url});
        closeLinksModal();
        link.label = '';
        link.url = '';
        link_errs.value.label = '';
        link_errs.value.url = '';
        console.log(links.value);
    }

    const removeLink = (index: number) => {
        links.value.splice(index, 1);
    }

    const form = reactive({
        brief_id: id,
        message: '',
        links: [] as Array<{ [key: string]: string }>,
    })

    const errs = ref({
        brief_id: '',
        message: '',
        links: '',
    })

    onMounted(async() => {
        await briefs.fetchBrief(id);
    })

    const submit = async () => {
        form.links = links.value;
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
                            <NuxtLink to="/student/submission"
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
                                    <p class="text-sm font-bold text-accent-red">{{ (new Date(briefs.brief?.end_date as string)).toLocaleString('en-US', { month: 'long', day: 'numeric', year: 'numeric'})  }}</p>
                                </div>
                            </div>
                            <div
                                class="prose prose-sm dark:prose-invert max-w-none text-slate-600 dark:text-slate-300 space-y-4">
                                <p>{{ briefs.brief?.description }}</p>
                                <div class="bg-black/5 dark:bg-black/20 p-4 rounded-lg border-l-4 border-primary">
                                    <h4 class="text-xs font-bold uppercase mb-2">Requirements:</h4>
                                    <ul class="list-disc list-inside text-xs space-y-1">
                                        <li v-for="brief_skill_level in briefs.brief?.brief_skill_levels">{{ brief_skill_level.skill.code }}: {{ brief_skill_level.skill.title }}, Required level: {{ brief_skill_level.level.name }}</li>
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
                            <div class="flex items-center justify-between">
                                <label class="text-sm font-bold adventure-title tracking-wide">Project Links</label>
                                <span class="text-[10px] font-bold text-slate-400 uppercase">Up to 5 allowed</span>
                            </div>
                            <div class="space-y-3">
                                <div v-for="(link, index) in links"
                                    class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-background-dark border border-slate-200 dark:border-[#224249] rounded-lg">
                                    <span class="material-symbols-outlined text-primary text-sm">anchor</span>
                                    <input :title="Object.keys(link)[0] as string"
                                        class="bg-transparent border-none p-0 text-xs w-full focus:ring-0 text-slate-600 dark:text-slate-300"
                                        readonly type="text" :placeholder="link[Object.keys(link)[0] as string]" />
                                    <button @click="removeLink(index)" class="text-slate-400 hover:text-accent-red">
                                        <span class="material-symbols-outlined text-lg">close</span>
                                    </button>
                                </div>
                                <button v-if="links.length < 5" @click="openLinksModal()"
                                    class="w-full py-3 border-2 border-dashed border-slate-200 dark:border-[#224249] rounded-lg text-slate-400 hover:border-primary hover:text-primary transition-all flex items-center justify-center gap-2 text-xs font-bold uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-sm">add</span>
                                    Add Link
                                </button>
                            </div>
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
        <div id="links-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay hidden">
            <div
                class="w-full max-w-md bg-deep-ocean border-2 border-pirate-gold/30 rounded-2xl overflow-hidden shadow-[0_0_50px_rgba(212,175,55,0.15)]">
                <div class="p-6">
                    <h3 class="text-2xl font-bold adventure-title text-pirate-gold mb-2 tracking-wide uppercase">Secure New
                        Link</h3>
                    <p class="text-sm text-slate-400 mb-6">Paste the coordinates to your findings (GitHub, Figma, or Drive
                        links)</p>
                    <div class="space-y-6">
                        <div>
                            <label for="label" class="block text-sm font-bold adventure-title tracking-wide mb-2">Label:</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span
                                        class="material-symbols-outlined text-primary group-focus-within:text-pirate-gold transition-colors">anchor</span>
                                </div>
                                <input v-model="link.label" id="label" autocomplete="off"
                                    class="block w-full pl-11 bg-background-dark/50 border-slate-700 text-white text-sm rounded-xl focus:ring-pirate-gold focus:border-pirate-gold transition-all py-3.5"
                                    placeholder="GitHub" type="url" />
                            </div>
                            <p v-if="link_errs.label" class="text-red-500 text-xs mt-1">{{ link_errs.label }}</p>
                        </div>
                        <div>
                            <label for="url" class="block text-sm font-bold adventure-title tracking-wide mb-2">URL:</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span
                                        class="material-symbols-outlined text-primary group-focus-within:text-pirate-gold transition-colors">anchor</span>
                                </div>
                                <input v-model="link.url" id="url" autocomplete="off"
                                    class="block w-full pl-11 bg-background-dark/50 border-slate-700 text-white text-sm rounded-xl focus:ring-pirate-gold focus:border-pirate-gold transition-all py-3.5"
                                    placeholder="https://github.com/crew/project-one" type="url" />
                            </div>
                            <p v-if="link_errs.url" class="text-red-500 text-xs mt-1">{{ link_errs.url }}</p>
                        </div>
                        <div class="flex flex-col gap-3">
                            <button @click="addLink()"
                                class="w-full bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark py-4 rounded-xl font-black text-sm transition-all shadow-lg adventure-title uppercase tracking-widest text-lg">
                                Anchor Link
                            </button>
                            <button @click="closeLinksModal()"
                                class="text-primary hover:text-primary/80 text-xs font-bold uppercase tracking-widest text-center py-2 transition-colors">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </NuxtLayout>
</template>