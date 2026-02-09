<script setup lang="ts">
    import { useBrief } from '~~/stores/brief';

    useHead({
        title: 'Teacher Dashboard - Brief'
    })

    const store = useBrief();

    const id: number = parseInt(useRoute().params?.id as string)

    onMounted(async() => {
        await store.fetchBrief(id);
    })
</script>

<template>
    <NuxtLayout name="teacher">
        <main class="flex-1 flex flex-col overflow-y-auto">
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
                            THE GRAND LINE ACADEMY - {{ store.brief?.class_group.name }}</p>
                        <h2 class="text-4xl md:text-6xl font-black adventure-title text-white tracking-tight drop-shadow-2xl">
                            {{ store.brief?.title }}
                        </h2>
                    </div>
                </div>
            </section>
            <div class="p-8 max-w-7xl mx-auto w-full grid grid-cols-1 lg:grid-cols-12 gap-8">
                <div class="lg:col-span-4 space-y-6">
                    <div
                        class="bg-white dark:bg-[#182f34] border border-slate-200 dark:border-[#224249] rounded-xl overflow-hidden shadow-xl p-6 relative">
                        <div class="absolute top-0 right-0 p-3">
                            <span class="material-symbols-outlined text-pirate-gold/20 text-6xl">inventory_2</span>
                        </div>
                        <h3
                            class="text-xs font-black uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400 mb-6 flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">fact_check</span> Logistics &amp; Status
                        </h3>
                        <div class="space-y-5 relative z-10">
                            <div class="flex items-center justify-between border-b border-slate-100 dark:border-[#224249] pb-3">
                                <span
                                    class="text-xs text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider">Teacher
                                    / Creator</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold adventure-title">Capt. {{ store.brief?.teacher.first_name }} {{ store.brief?.teacher.last_name }}</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between border-b border-slate-100 dark:border-[#224249] pb-3">
                                <span
                                    class="text-xs text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider">Start
                                    Date</span>
                                <span class="text-sm font-bold">{{ (new Date(store.brief?.start_date as string)).toLocaleString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) }}</span>
                            </div>
                            <div class="flex items-center justify-between border-b border-slate-100 dark:border-[#224249] pb-3">
                                <span
                                    class="text-xs text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider">Deadline</span>
                                <span class="text-sm font-bold text-accent-red">{{ (new Date(store.brief?.end_date as string)).toLocaleString('en-US', { month: 'long', day: 'numeric', year: 'numeric' } ) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span
                                    class="text-xs text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider">Formation</span>
                                <span v-if="store.brief?.is_collective"
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black bg-primary/10 text-primary border border-primary/20">
                                    <span class="material-symbols-outlined text-sm">groups</span> SQUAD MISSION
                                </span>
                                <span v-else
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black bg-primary/10 text-primary border border-primary/20">
                                    <span class="material-symbols-outlined text-sm">person</span> SOLO MISSION
                                </span>
                            </div>
                        </div>
                        <div class="mt-8">
                            <NuxtLink :to="`/teacher/brief/edit/${store.brief?.id}`"
                                class="w-full bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark py-3 rounded-lg font-bold text-sm transition-all shadow-[0_4px_10px_rgba(212,175,55,0.3)] flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined">edit</span>
                                Update Intelligence Briefing
                            </NuxtLink>
                        </div>
                    </div>
                    <div class="bg-primary/5 border border-primary/20 rounded-xl p-6">
                        <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-primary mb-3">Current Saga /
                            Sprint</h3>
                        <div class="flex items-start gap-4">
                            <div
                                class="size-12 bg-primary rounded-lg flex items-center justify-center text-background-dark shrink-0">
                                <span class="text-xl font-bold">S-{{ store.brief?.sprint.id }}</span>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold adventure-title leading-tight">{{ store.brief?.sprint.name }}</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 italic">"{{ store.brief?.sprint.description }}"</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="lg:col-span-8 space-y-8">
                    <div class="parchment p-8 md:p-12 rounded-sm shadow-2xl relative overflow-hidden">
                        <div class="absolute top-4 right-4 opacity-10">
                            <span class="material-symbols-outlined text-9xl">history_edu</span>
                        </div>
                        <div class="relative z-10">
                            <div class="mb-10">
                                <h3
                                    class="text-xs font-black uppercase tracking-[0.3em] text-[#5d4a2a] mb-4 flex items-center gap-2">
                                    <span class="h-px w-8 bg-[#5d4a2a]/30"></span> Mission Objective
                                </h3>
                                <p class="text-xl md:text-2xl font-bold italic adventure-title leading-relaxed">
                                    "{{ store.brief?.description }}"
                                </p>
                            </div>
                            <div class="space-y-6">
                                <h3
                                    class="text-xs font-black uppercase tracking-[0.3em] text-[#5d4a2a] mb-4 flex items-center gap-2">
                                    <span class="h-px w-8 bg-[#5d4a2a]/30"></span> Detailed Instructions
                                </h3>
                                <div v-html="store.brief?.content"
                                    class="prose prose-slate dark:prose-invert max-w-none space-y-4 font-medium leading-relaxed text-[#2c2416]">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-bold adventure-title text-primary tracking-wide">Ability
                                Requirements</h3>
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">4 Skills
                                Mapped</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div v-for="brief_skill_level in store.brief?.brief_skill_levels"
                                class="bg-white dark:bg-[#182f34] border border-slate-200 dark:border-[#224249] p-5 rounded-xl hover:border-primary/40 transition-all group">
                                <div class="flex justify-between items-start mb-3">
                                    <div
                                        class="bg-primary/10 text-primary px-2 py-1 rounded text-[10px] font-black border border-primary/20 tracking-tighter">
                                        {{ brief_skill_level.skill.code }}</div>
                                    <div
                                        class="text-pirate-gold font-bold text-[10px] uppercase tracking-widest flex items-center gap-1">
                                        <span class="material-symbols-outlined text-sm">stars</span> {{ brief_skill_level.level.name }}
                                    </div>
                                </div>
                                <h4 class="font-bold text-lg mb-2 group-hover:text-primary transition-colors">
                                    {{ brief_skill_level.skill.title }}</h4>
                                <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-2 italic">{{ brief_skill_level.skill.description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <footer
                class="mt-auto border-t border-slate-200 dark:border-[#224249] bg-white dark:bg-[#102023] px-8 py-3 flex items-center justify-between text-[11px] font-medium text-slate-500 uppercase tracking-widest">
                <div class="flex gap-6">
                    <span class="flex items-center gap-1.5"><span
                            class="size-2 bg-emerald-500 rounded-full shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                        Signal: Logged On</span>
                    <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[14px]">anchor</span>
                        Fleet: Marine HQ Server</span>
                </div>
                <div class="flex gap-4">
                    <span class="text-primary/70">V 2.4.0-ARABASTA</span>
                    <a class="hover:text-primary transition-colors flex items-center gap-1" href="#">
                        <span class="material-symbols-outlined text-[14px]">help</span> Help Center
                    </a>
                </div>
            </footer>
        </main>
    </NuxtLayout>
</template>