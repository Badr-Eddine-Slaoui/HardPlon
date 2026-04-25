<script setup lang="ts">
    import { useBrief } from '~~/stores/brief';
    import { useBriefVersion } from '~~/stores/brief_version';
    import type { BriefVersion } from '~~/types/brief_version';
    import BriefVersionList from '~~/app/components/teacher/BriefVersionList.vue';
    import BriefVersionCreateModal from '~~/app/components/teacher/BriefVersionCreateModal.vue';
    import BriefVersionDetailsModal from '~~/app/components/teacher/BriefVersionDetailsModal.vue';

    useHead({
        title: 'Teacher Dashboard - Brief Intelligence'
    })

    const store = useBrief();
    const versionStore = useBriefVersion();
    const route = useRoute();
    const id = parseInt(route.params.id as string);

    onMounted(async () => {
        await Promise.all([
            store.fetchBrief(id),
            versionStore.fetchBriefVersionsForBrief(id)
        ]);
    });

    // Modal States
    const isCreateModalOpen = ref(false);
    const isDetailsModalOpen = ref(false);
    const selectedVersion = ref<BriefVersion | null>(null);
    const versionToArchive = ref<number | null>(null);
    const versionToRestore = ref<number | null>(null);

    const openDetails = (version: BriefVersion) => {
        selectedVersion.value = version;
        isDetailsModalOpen.value = true;
    };

    const handleCreateSuccess = async () => {
        isCreateModalOpen.value = false;
        await versionStore.fetchBriefVersionsForBrief(id);
    };

    const confirmArchive = (vid: number) => {
        versionToArchive.value = vid;
        const modal = document.getElementById('archive-version-modal');
        modal?.classList.remove('hidden');
    };

    const closeArchive = () => {
        versionToArchive.value = null;
        const modal = document.getElementById('archive-version-modal');
        modal?.classList.add('hidden');
    };

    const executeArchive = async () => {
        if (versionToArchive.value) {
            await versionStore.archiveBriefVersion(versionToArchive.value);
            await versionStore.fetchBriefVersionsForBrief(id);
            closeArchive();
        }
    };

    const confirmRestore = (vid: number) => {
        versionToRestore.value = vid;
        const modal = document.getElementById('restore-version-modal');
        modal?.classList.remove('hidden');
    };

    const closeRestore = () => {
        versionToRestore.value = null;
        const modal = document.getElementById('restore-version-modal');
        modal?.classList.add('hidden');
    };

    const executeRestore = async () => {
        if (versionToRestore.value) {
            await versionStore.restoreBriefVersion(versionToRestore.value);
            await versionStore.fetchBriefVersionsForBrief(id);
            closeRestore();
        }
    };

    definePageMeta({
        middleware: ['auth', 'teacher']
    });
</script>

<template>
    <NuxtLayout name="teacher">
        <main class="flex-1 flex flex-col overflow-y-auto">
            <!-- Hero Header -->
            <section class="relative h-[300px] w-full shrink-0 overflow-hidden">
                <img alt="Class Banner" class="w-full h-full object-cover"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuBaV6nhIiE5r43pR-KcySdyKGepDAuFIw4f73q1BtyskDW-kQm7jqUY_5e4YudtirhPQbIVU9IqfJwUEQtkoqZjD_exl8EkBbaOlU9gmi4831Bd2QLewmA937l5jE-Yw79CsfE1CpueC6D61Fvam9FSlLxgrVQzW9HXMOlGyPWqTx61OIdV5a4lqK_wSW6EdNN4C04sfoL4oUodWxzxCbk8z3MYl3Zo3Z1O35rKeXyt3LAcKopZz8pAq3Gk6gXLRijY6eWo8tdQZwk" />
                <div class="absolute inset-0 hero-overlay flex flex-col justify-end p-8 pb-12">
                    <div class="max-w-7xl mx-auto w-full">
                        <nav class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-primary/80 mb-4">
                            <NuxtLink class="hover:text-primary transition-colors" to="/teacher/brief">Mission Logbook</NuxtLink>
                            <span class="material-symbols-outlined text-sm">chevron_right</span>
                            <span>Intelligence Report</span>
                        </nav>
                        <p class="text-pirate-gold text-lg md:text-xl font-bold adventure-title tracking-widest mb-1">
                            THE GRAND LINE ACADEMY - {{ store.brief?.class_group?.name }}</p>
                        <h2 class="text-4xl md:text-6xl font-black adventure-title text-white tracking-tight drop-shadow-2xl">
                            {{ store.brief?.title }}
                        </h2>
                    </div>
                </div>
            </section>

            <!-- Main Content Grid -->
            <div class="p-8 max-w-7xl mx-auto w-full grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Sidebar Info -->
                <div class="lg:col-span-4 space-y-6">
                    <div class="bg-white dark:bg-[#182f34] border border-slate-200 dark:border-[#224249] rounded-xl overflow-hidden shadow-xl p-6 relative animate-in fade-in slide-in-from-left-4">
                        <div class="absolute top-0 right-0 p-3">
                            <span class="material-symbols-outlined text-pirate-gold/20 text-6xl select-none">inventory_2</span>
                        </div>
                        <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400 mb-6 flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg text-primary">fact_check</span> Logistics & Status
                        </h3>
                        <div class="space-y-5 relative z-10">
                            <div class="flex items-center justify-between border-b border-slate-100 dark:border-[#224249] pb-3">
                                <span class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider">Creator</span>
                                <span class="text-sm font-bold adventure-title">Capt. {{ store.brief?.teacher?.first_name }} {{ store.brief?.teacher?.last_name }}</span>
                            </div>
                            <div class="flex items-center justify-between border-b border-slate-100 dark:border-[#224249] pb-3">
                                <span class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider">Start Date</span>
                                <span class="text-sm font-bold">{{ store.brief?.start_date ? new Date(store.brief.start_date).toLocaleDateString() : 'N/A' }}</span>
                            </div>
                            <div class="flex items-center justify-between border-b border-slate-100 dark:border-[#224249] pb-3">
                                <span class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider">Deadline</span>
                                <span class="text-sm font-bold text-accent-red">{{ store.brief?.end_date ? new Date(store.brief.end_date).toLocaleDateString() : 'N/A' }}</span>
                            </div>
                            <div class="flex items-center justify-between border-b border-slate-100 dark:border-[#224249] pb-3">
                                <span class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider">Stack</span>
                                <span class="text-sm font-bold text-primary">{{ store.brief?.stack?.name || 'N/A' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider">Formation</span>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[9px] font-black bg-primary/10 text-primary border border-primary/20">
                                    <span class="material-symbols-outlined text-xs">{{ store.brief?.is_collective ? 'groups' : 'person' }}</span>
                                    {{ store.brief?.is_collective ? 'SQUAD MISSION' : 'SOLO MISSION' }}
                                </span>
                            </div>
                        </div>
                        <div class="mt-8 flex flex-col gap-3">
                            <NuxtLink :to="`/teacher/brief/edit/${store.brief?.id}`"
                                class="w-full bg-slate-100 dark:bg-[#224249] hover:bg-slate-200 dark:hover:bg-[#2a4f58] text-slate-700 dark:text-slate-200 py-3 rounded-lg font-bold text-sm transition-all flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined">edit</span> Update Details
                            </NuxtLink>
                            <button @click="isCreateModalOpen = true"
                                class="w-full bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark py-3 rounded-lg font-bold text-sm transition-all shadow-lg flex items-center justify-center gap-2 border border-pirate-gold-dark/30">
                                <span class="material-symbols-outlined">add_circle</span> Deploy New Version
                            </button>
                        </div>
                    </div>

                    <div class="bg-primary/5 border border-primary/20 rounded-xl p-6 animate-in fade-in slide-in-from-left-4 delay-75">
                        <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-primary mb-4">Current Voyage (Sprint)</h3>
                        <div class="flex items-start gap-4">
                            <div class="size-12 bg-primary rounded-lg flex items-center justify-center text-background-dark shrink-0">
                                <span class="text-xl font-bold">S{{ store.brief?.sprint?.id }}</span>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold adventure-title leading-tight">{{ store.brief?.sprint?.name }}</h4>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 italic">"{{ store.brief?.sprint?.description }}"</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="lg:col-span-8 space-y-12">
                    <!-- Instruction Parchment -->
                    <div class="parchment p-8 md:p-12 rounded shadow-2xl relative overflow-hidden animate-in fade-in slide-in-from-bottom-4">
                        <div class="absolute top-4 right-4 opacity-10">
                            <span class="material-symbols-outlined text-9xl">history_edu</span>
                        </div>
                        <div class="relative z-10">
                            <div class="mb-10">
                                <h3 class="text-xs font-black uppercase tracking-[0.3em] text-[#5d4a2a] mb-4 flex items-center gap-2">
                                    <span class="h-px w-8 bg-[#5d4a2a]/30"></span> Mission Objective
                                </h3>
                                <p class="text-xl md:text-2xl font-bold italic adventure-title leading-relaxed text-[#3d2f1b]">
                                    "{{ store.brief?.description }}"
                                </p>
                            </div>
                            <div class="space-y-6">
                                <h3 class="text-xs font-black uppercase tracking-[0.3em] text-[#5d4a2a] mb-4 flex items-center gap-2">
                                    <span class="h-px w-8 bg-[#5d4a2a]/30"></span> Detailed Instructions
                                </h3>
                                <div v-html="store.brief?.content"
                                    class="prose prose-slate dark:prose-invert max-w-none font-medium leading-relaxed text-[#2c2416]">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Versions List Component -->
                    <BriefVersionList 
                        :brief-id="id"
                        @view-details="openDetails"
                        @archive="confirmArchive"
                        @restore="confirmRestore"
                    />
                </div>
            </div>

            <!-- Modals -->
            <BriefVersionCreateModal 
                :brief-id="id"
                :is-open="isCreateModalOpen"
                @close="isCreateModalOpen = false"
                @success="handleCreateSuccess"
            />

            <BriefVersionDetailsModal 
                :version="selectedVersion"
                :is-open="isDetailsModalOpen"
                @close="isDetailsModalOpen = false"
            />

            <!-- Archive Version Confirmation -->
            <div id="archive-version-modal" class="fixed inset-0 z-[70] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 hidden">
                <div class="bg-[#182f34] border-2 border-pirate-gold/30 rounded-2xl w-full max-w-md overflow-hidden animate-in zoom-in-95">
                    <div class="p-6 bg-background-dark border-b border-pirate-gold/20 flex items-center gap-4">
                        <div class="size-12 rounded-full bg-red-500/10 border border-red-500/30 flex items-center justify-center text-red-500">
                            <span class="material-symbols-outlined text-3xl">archive</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold adventure-title text-pirate-gold">Retire Version?</h3>
                            <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Archive Automated Test</p>
                        </div>
                    </div>
                    <div class="p-8 text-center">
                        <p class="text-slate-300 text-sm">This test configuration will be archived. It will no longer be used for new submissions until restored.</p>
                    </div>
                    <div class="p-6 bg-background-dark/50 flex gap-3">
                        <button @click="closeArchive" class="flex-1 px-4 py-3 rounded-xl border border-slate-700 text-slate-300 font-bold text-xs">Keep Active</button>
                        <button @click="executeArchive" class="flex-1 px-4 py-3 rounded-xl bg-red-500 hover:bg-red-600 text-white font-bold text-xs transition-colors">Move to Archive</button>
                    </div>
                </div>
            </div>

            <!-- Restore Version Confirmation -->
            <div id="restore-version-modal" class="fixed inset-0 z-[70] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 hidden">
                <div class="bg-[#182f34] border-2 border-primary/30 rounded-2xl w-full max-w-md overflow-hidden animate-in zoom-in-95">
                    <div class="p-6 bg-background-dark border-b border-primary/20 flex items-center gap-4">
                        <div class="size-12 rounded-full bg-primary/10 border border-primary/30 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-3xl">unarchive</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold adventure-title text-primary">Resurface Version?</h3>
                            <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Restore Automated Test</p>
                        </div>
                    </div>
                    <div class="p-8 text-center">
                        <p class="text-slate-300 text-sm">Bring this test configuration back to the active fleet for current student missions?</p>
                    </div>
                    <div class="p-6 bg-background-dark/50 flex gap-3">
                        <button @click="closeRestore" class="flex-1 px-4 py-3 rounded-xl border border-slate-700 text-slate-300 font-bold text-xs">Stay Archived</button>
                        <button @click="executeRestore" class="flex-1 px-4 py-3 rounded-xl bg-primary hover:bg-primary/80 text-background-dark font-bold text-xs transition-all">Restore Now</button>
                    </div>
                </div>
            </div>

            <footer class="mt-auto border-t border-slate-200 dark:border-[#224249] bg-white dark:bg-[#102023] px-8 py-3 flex items-center justify-between text-[11px] font-medium text-slate-500 uppercase tracking-widest">
                <div class="flex gap-6">
                    <span class="flex items-center gap-1.5"><span class="size-2 bg-emerald-500 rounded-full shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span> Signal: Logged On</span>
                    <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[14px]">anchor</span> Fleet: Marine HQ Server</span>
                </div>
                <div class="flex gap-4">
                    <span class="text-primary/70">V 2.4.0-ARABASTA</span>
                    <a class="hover:text-primary transition-colors flex items-center gap-1" href="#"><span class="material-symbols-outlined text-[14px]">help</span> Help Center</a>
                </div>
            </footer>
        </main>
    </NuxtLayout>
</template>