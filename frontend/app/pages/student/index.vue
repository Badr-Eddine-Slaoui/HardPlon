<script setup lang="ts">
import { useStudent } from '~~/stores/student';
import { useUser } from '~~/stores/user';
import { onMounted, computed } from 'vue';

const studentStore = useStudent();
const userStore = useUser();

const stats = computed(() => studentStore.dashboardData?.stats);
const skillsProgress = computed(() => studentStore.dashboardData?.skills_progress || []);
const recentActivity = computed(() => studentStore.dashboardData?.recent_activity || []);

useHead({
    title: 'Command Center - Student Dashboard'
});

onMounted(async () => {
    await studentStore.fetchDashboardData();
});

definePageMeta({
    middleware: ['auth', 'student']
});

const getGradeColor = (grade: string) => {
    switch (grade) {
        case 'EXCELLENT': return 'text-emerald-500 bg-emerald-500/10 border-emerald-500/20';
        case 'AVERAGE': return 'text-amber-500 bg-amber-500/10 border-amber-500/20';
        case 'POOR': return 'text-rose-500 bg-rose-500/10 border-rose-500/20';
        default: return 'text-slate-500 bg-slate-500/10 border-slate-500/20';
    }
};

const getRelativeTime = (dateString: string) => {
    if (!dateString) return 'Unknown';
    const date = new Date(dateString);
    const now = new Date();
    const diff = now.getTime() - date.getTime();
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    if (days === 0) return 'Today';
    if (days === 1) return 'Yesterday';
    return `${days} days ago`;
};
</script>

<template>
    <NuxtLayout name="student">
        <main class="flex-1 flex flex-col h-screen overflow-hidden bg-[#0d1b1e]">
            <!-- Header -->
            <header class="h-24 border-b border-slate-800 bg-[#0a1416]/80 backdrop-blur-md flex items-center justify-between px-10 shrink-0 z-20">
                <div class="flex items-center gap-6">
                    <div class="size-14 rounded-2xl bg-pirate-gold/10 border border-pirate-gold/20 flex items-center justify-center text-pirate-gold shadow-[0_0_20px_rgba(212,175,55,0.1)]">
                        <span class="material-symbols-outlined text-3xl">anchor</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-black adventure-title text-slate-100 tracking-tight">Ahoy, {{ userStore.user?.first_name }}!</h1>
                        <p class="text-xs text-slate-500 font-bold uppercase tracking-[0.2em] mt-1">
                            Welcome back to the <span class="text-pirate-gold">Command Center</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="flex flex-col items-end mr-4">
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Current Rank</span>
                        <span class="text-lg font-black text-pirate-gold adventure-title">{{ stats?.rank || 'Cabin Boy' }}</span>
                    </div>
                    <div class="h-12 w-px bg-slate-800"></div>
                    <div class="flex items-center gap-4 px-6 py-3 bg-black/40 border border-slate-800 rounded-2xl shadow-inner">
                        <div class="size-10 rounded-full bg-pirate-gold/20 flex items-center justify-center text-pirate-gold">
                            <span class="material-symbols-outlined">monetization_on</span>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-tighter leading-none">Belly Balance</p>
                            <p class="text-xl font-black text-slate-100">{{ stats?.total_belly?.toLocaleString() ?? 0 }} ฿</p>
                        </div>
                    </div>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-10 custom-scrollbar">
                <!-- Loading State -->
                <div v-if="studentStore.loading && !studentStore.dashboardData" class="flex items-center justify-center h-full">
                    <div class="size-12 border-4 border-pirate-gold border-t-transparent rounded-full animate-spin"></div>
                </div>

                <!-- Content -->
                <div v-else-if="studentStore.dashboardData">
                    <!-- Stats Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
                        <div class="bg-[#0f1f23] border border-slate-800 rounded-3xl p-6 relative overflow-hidden group hover:border-pirate-gold/30 transition-all duration-500 shadow-xl">
                            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity">
                                <span class="material-symbols-outlined text-8xl text-pirate-gold">flag</span>
                            </div>
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-4">Total Missions</p>
                            <div class="flex items-end gap-3">
                                <span class="text-4xl font-black text-slate-100 adventure-title">{{ stats?.completed_missions ?? 0 }}</span>
                                <span class="text-slate-500 font-bold mb-1.5">/ {{ stats?.total_missions ?? 0 }}</span>
                            </div>
                            <div class="w-full bg-slate-800 h-1.5 rounded-full mt-6 overflow-hidden">
                                <div class="bg-pirate-gold h-full rounded-full shadow-[0_0_10px_rgba(212,175,55,0.5)] transition-all duration-1000" 
                                    :style="{ width: (stats?.success_rate ?? 0) + '%' }"></div>
                            </div>
                        </div>

                        <div class="bg-[#0f1f23] border border-slate-800 rounded-3xl p-6 relative overflow-hidden group hover:border-emerald-500/30 transition-all duration-500 shadow-xl">
                            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity">
                                <span class="material-symbols-outlined text-8xl text-emerald-500">verified</span>
                            </div>
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-4">Success Rate</p>
                            <div class="flex items-center gap-4">
                                <span class="text-4xl font-black text-emerald-500 adventure-title">{{ stats?.success_rate ?? 0 }}%</span>
                                <div class="px-2 py-1 rounded bg-emerald-500/10 text-emerald-500 text-[10px] font-black uppercase tracking-widest border border-emerald-500/20">
                                    Excellent
                                </div>
                            </div>
                            <p class="text-xs text-slate-500 mt-6 font-medium italic">"Fortune favors the bold."</p>
                        </div>

                        <div class="bg-[#0f1f23] border border-slate-800 rounded-3xl p-6 relative overflow-hidden group hover:border-pirate-gold/30 transition-all duration-500 shadow-xl">
                            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity">
                                <span class="material-symbols-outlined text-8xl text-pirate-gold">inventory_2</span>
                            </div>
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-4">Treasure Earned</p>
                            <div class="flex items-center gap-3">
                                <span class="text-4xl font-black text-slate-100 adventure-title">{{ (stats?.total_belly ?? 0) / 1000 }}</span>
                                <span class="text-slate-500 font-bold mb-1.5 uppercase text-xs">Validated Chests</span>
                            </div>
                            <div class="flex items-center gap-1 mt-6">
                                <span class="size-1.5 rounded-full bg-pirate-gold shadow-[0_0_5px_rgba(212,175,55,1)]"></span>
                                <p class="text-[10px] font-black text-pirate-gold uppercase tracking-widest">Premium Bounty</p>
                            </div>
                        </div>

                        <div class="bg-[#0f1f23] border border-slate-800 rounded-3xl p-6 relative overflow-hidden group hover:border-sky-500/30 transition-all duration-500 shadow-xl">
                            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity">
                                <span class="material-symbols-outlined text-8xl text-sky-500">military_tech</span>
                            </div>
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-4">Current Fleet Status</p>
                            <div class="flex items-center gap-3">
                                <span class="text-3xl font-black text-slate-100 adventure-title">{{ stats?.rank ?? 'Cabin Boy' }}</span>
                            </div>
                            <div class="mt-6 flex items-center gap-2">
                                <div class="flex -space-x-2">
                                    <div class="size-6 rounded-full border border-slate-800 bg-slate-700"></div>
                                    <div class="size-6 rounded-full border border-slate-800 bg-slate-600"></div>
                                    <div class="size-6 rounded-full border border-slate-800 bg-slate-500"></div>
                                </div>
                                <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">+12 Crewmates Online</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                        <!-- Skills Progress -->
                        <div class="lg:col-span-2 space-y-6">
                            <div class="flex items-center justify-between">
                                <h2 class="text-sm font-black text-pirate-gold uppercase tracking-[0.3em] flex items-center gap-4">
                                    Skill Mastery Breakdown
                                    <span class="h-px w-32 bg-pirate-gold/20"></span>
                                </h2>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div v-for="skill in skillsProgress" :key="skill.id" 
                                    class="bg-[#152a2e]/60 border border-slate-800 rounded-2xl p-5 flex items-center justify-between group hover:border-pirate-gold/30 hover:bg-[#1a3439]/80 transition-all shadow-lg">
                                    <div class="flex items-center gap-4">
                                        <div class="size-12 rounded-xl bg-slate-900 flex items-center justify-center text-xs font-black text-pirate-gold group-hover:bg-pirate-gold group-hover:text-background-dark transition-all shadow-inner">
                                            {{ skill.brief_skill_level?.skill?.code }}
                                        </div>
                                        <div>
                                            <h5 class="text-sm font-bold text-slate-200 leading-tight">{{ skill.brief_skill_level?.skill?.title }}</h5>
                                            <p class="text-[10px] text-slate-500 font-bold uppercase mt-0.5 tracking-tighter">Level {{ skill.brief_skill_level?.level?.order }}: {{ skill.brief_skill_level?.level?.name }}</p>
                                        </div>
                                    </div>
                                    <div :class="['px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest border transition-all shadow-sm', getGradeColor(skill.grade)]">
                                        {{ skill.grade }}
                                    </div>
                                </div>

                                <!-- Empty State for Skills -->
                                <div v-if="!skillsProgress.length" class="col-span-2 bg-[#0f1f23]/30 border border-dashed border-slate-800 rounded-3xl p-10 flex flex-col items-center text-center">
                                    <span class="material-symbols-outlined text-4xl text-slate-700 mb-3">school</span>
                                    <p class="text-sm text-slate-500 font-medium">No skill appraisals recorded yet. <br>Complete your first mission to see your progress!</p>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activity -->
                        <div class="space-y-6">
                            <h2 class="text-sm font-black text-pirate-gold uppercase tracking-[0.3em] flex items-center gap-4">
                                Recent Log Entries
                                <span class="h-px flex-1 bg-pirate-gold/20"></span>
                            </h2>

                            <div class="bg-[#0f1f23] border border-slate-800 rounded-3xl p-8 relative overflow-hidden shadow-2xl">
                                <div class="absolute top-0 left-0 w-1 h-full bg-pirate-gold/10"></div>
                                
                                <div class="space-y-8 relative z-10">
                                    <div v-for="activity in recentActivity" :key="activity.id" class="flex gap-6 relative group">
                                        <!-- Timeline line -->
                                        <div class="absolute left-[19px] top-10 bottom-[-32px] w-0.5 bg-slate-800 group-last:hidden"></div>
                                        
                                        <div :class="['size-10 rounded-full flex items-center justify-center border-2 shrink-0 z-10 transition-transform group-hover:scale-110', 
                                            activity.type === 'submission' ? 'bg-sky-500/10 border-sky-500/30 text-sky-500' : 'bg-emerald-500/10 border-emerald-500/30 text-emerald-500']">
                                            <span class="material-symbols-outlined text-xl">{{ activity.icon }}</span>
                                        </div>
                                        
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between mb-1">
                                                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">{{ activity.type }} Recorded</p>
                                                <span class="text-[9px] font-bold text-slate-600 uppercase">{{ getRelativeTime(activity.date) }}</span>
                                            </div>
                                            <h4 class="text-sm font-bold text-slate-200 group-hover:text-pirate-gold transition-colors">{{ activity.title }}</h4>
                                            <div class="mt-2 flex items-center gap-2">
                                                <span :class="['text-[8px] font-black px-1.5 py-0.5 rounded uppercase tracking-tighter', 
                                                    activity.status === 'Valid' ? 'bg-emerald-500/10 text-emerald-500' : 
                                                    activity.status === 'Submitted' ? 'bg-sky-500/10 text-sky-500' : 'bg-rose-500/10 text-rose-500']">
                                                    {{ activity.status }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Empty State for Activity -->
                                    <div v-if="!recentActivity.length" class="flex flex-col items-center py-10 text-center">
                                        <span class="material-symbols-outlined text-4xl text-slate-700 mb-3">history</span>
                                        <p class="text-xs text-slate-500 font-medium italic">No entries in the captain's log yet.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="flex flex-col items-center justify-center h-full text-slate-500">
                    <span class="material-symbols-outlined text-6xl mb-4">sentiment_dissatisfied</span>
                    <p class="text-lg font-bold">No dashboard data available.</p>
                    <button @click="studentStore.fetchDashboardData()" class="mt-4 px-6 py-2 bg-pirate-gold text-background-dark font-black rounded-xl hover:bg-pirate-gold/80 transition-colors">Retry Fetching</button>
                </div>
            </div>

            <!-- Footer -->
            <footer class="h-10 border-t border-slate-800 bg-[#0a1416] px-10 flex items-center justify-between text-[9px] font-black text-slate-600 uppercase tracking-[0.2em] shrink-0 z-20">
                <div class="flex gap-6">
                    <span class="flex items-center gap-1.5"><span class="size-1.5 bg-emerald-500 rounded-full shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span> Connection: Secured</span>
                    <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[12px]">security</span> Fleet Encryption Active</span>
                </div>
                <div class="flex gap-4">
                    <span class="text-pirate-gold/50">Dashboard Proto-ID: {{ userStore.user?.id }}</span>
                </div>
            </footer>
        </main>
    </NuxtLayout>
</template>

<style scoped>
.adventure-title {
    font-family: 'Outfit', sans-serif;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #1a3439;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #d4af37;
}

.parchment-effect {
    background-image: radial-gradient(circle at 2px 2px, rgba(212,175,55,0.03) 1px, transparent 0);
    background-size: 24px 24px;
}
</style>
