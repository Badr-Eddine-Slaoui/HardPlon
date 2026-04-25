<script setup lang="ts">
    import { useBriefVersion } from '~~/stores/brief_version';
    import type { BriefVersion } from '~~/types/brief_version';

    const props = defineProps<{
        briefId: number;
    }>();

    const emit = defineEmits(['view-details', 'archive', 'restore']);

    const versionStore = useBriefVersion();

    const changePage = async (page: number) => {
        await versionStore.fetchBriefVersionsForBrief(props.briefId, page);
    };
</script>

<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold adventure-title text-primary tracking-wide">Mission Versions (Automated Tests)</h3>
            <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">{{ versionStore.meta.total }} Versions Logged</span>
        </div>
        
        <div v-if="versionStore.brief_versions?.length" class="space-y-4">
            <div v-for="version in versionStore.brief_versions" :key="version.id"
                class="bg-white dark:bg-[#182f34] border border-slate-200 dark:border-[#224249] rounded-xl overflow-hidden shadow-lg transition-all hover:border-primary/30 group">
                <div class="p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-4 cursor-pointer" @click="emit('view-details', version)">
                        <div class="size-12 rounded-lg bg-primary/10 border border-primary/20 flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined">{{ version.test_config?.type === 'http' ? 'api' : version.test_config?.type === 'browser' ? 'language' : 'code' }}</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg text-slate-800 dark:text-white group-hover:text-primary transition-colors">{{ version.version }}</h4>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-slate-100 dark:bg-[#224249] text-slate-500 dark:text-slate-400">
                                    {{ version.test_config?.type }}
                                </span>
                                <span class="text-[10px] text-slate-400 italic">
                                    Created {{ new Date(version.created_at).toLocaleDateString() }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="emit('view-details', version)"
                            class="p-2 rounded-lg bg-slate-100 dark:bg-[#224249] text-slate-500 hover:text-primary hover:bg-primary/10 transition-all"
                            title="View Intelligence Report">
                            <span class="material-symbols-outlined">visibility</span>
                        </button>
                        <button @click="emit('archive', version.id)"
                            class="p-2 rounded-lg bg-slate-100 dark:bg-[#224249] text-slate-500 hover:text-red-500 hover:bg-red-500/10 transition-all"
                            title="Archive Version">
                            <span class="material-symbols-outlined">archive</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div v-else class="bg-slate-50 dark:bg-[#182f34]/50 border-2 border-dashed border-slate-200 dark:border-[#224249] rounded-2xl p-12 text-center">
            <span class="material-symbols-outlined text-5xl text-slate-300 dark:text-slate-700 mb-4 select-none">bug_report</span>
            <p class="text-slate-500 dark:text-slate-400 font-medium">No automated test versions deployed for this mission.</p>
        </div>

        <!-- Pagination -->
        <div v-if="versionStore.meta.last_page > 1" class="flex justify-center gap-2 mt-4">
            <button v-for="p in versionStore.meta.last_page" :key="p"
                @click="changePage(p)"
                :class="['px-3 py-1 rounded text-xs font-bold transition-all', versionStore.meta.current_page === p ? 'bg-primary text-background-dark' : 'bg-slate-100 dark:bg-[#224249] text-slate-500 hover:bg-primary/20 hover:text-primary']">
                {{ p }}
            </button>
        </div>

        <!-- Archived Versions -->
        <div v-if="versionStore.archived_brief_versions?.length" class="mt-12 pt-8 border-t border-dashed border-[#224249]">
            <h4 class="text-xs font-black uppercase tracking-widest text-slate-500 mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">inventory_2</span> Davy Jones' Locker (Archived Versions)
            </h4>
            <div class="space-y-2">
                <div v-for="version in versionStore.archived_brief_versions" :key="version.id"
                    class="bg-slate-100/30 dark:bg-[#182f34]/20 border border-slate-200 dark:border-[#224249] rounded-lg p-3 flex items-center justify-between opacity-60 hover:opacity-100 transition-opacity">
                    <div class="flex items-center gap-3">
                        <span class="text-[10px] font-bold text-slate-400 px-1.5 py-0.5 border border-slate-700 rounded">{{ version.test_config?.type }}</span>
                        <span class="text-sm font-medium text-slate-500">{{ version.version }}</span>
                    </div>
                    <button @click="emit('restore', version.id)" class="text-emerald-500 hover:text-emerald-400 transition-colors" title="Restore Version">
                        <span class="material-symbols-outlined text-lg">unarchive</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
