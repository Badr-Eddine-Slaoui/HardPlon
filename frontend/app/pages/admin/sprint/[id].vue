<script setup lang="ts">
    import { useSprint } from '~~/stores/sprint';

    const store = useSprint();

    const id: number = parseInt(useRoute().params?.id as string)

    onMounted(async() => {
        await store.fetchSprint(id);
        useHead({
            title: `Admin Dashboard - Sprint: ${store.sprint?.name}`
        })
    })
</script>

<template>
    <NuxtLayout name="admin">
        <main class="max-w-[1400px] mx-auto p-10 space-y-10 nautical-grid">
            <section class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        <span
                            class="px-3 py-1 bg-green-500/20 text-green-400 border border-green-500/40 rounded text-[10px] font-black uppercase tracking-[0.2em]">En
                            Route</span>
                        <span class="text-primary/60 text-xs font-bold uppercase tracking-widest">Voyage ID: EB-1502</span>
                    </div>
                    <h1 class="text-6xl font-pirate text-white tracking-wide">{{ store?.sprint?.name }}</h1>
                    <p class="text-slate-400 font-medium max-w-2xl">A strategic push into the eastern seas to identify
                        emerging talents and map new educational territories.</p>
                </div>
                <div class="flex gap-4">
                    <NuxtLink :to="`/admin/sprint/edit/${store.sprint?.id}`"
                        class="px-6 py-3 bg-accent-blue border border-pirate-gold/30 text-white font-bold rounded-lg hover:bg-accent-blue/80 transition-all flex items-center gap-2 group">
                        <span
                            class="material-symbols-outlined text-pirate-gold group-hover:rotate-12 transition-transform">edit</span>
                        Edit Voyage
                    </NuxtLink>
                </div>
            </section>
            <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="gold-border-card p-6 flex items-center gap-5">
                    <div
                        class="size-12 rounded-lg bg-pirate-gold/10 border border-pirate-gold/20 flex items-center justify-center">
                        <span class="material-symbols-outlined text-pirate-gold">calendar_today</span>
                    </div>
                    <div>
                        <p class="text-primary/60 text-[10px] font-black uppercase tracking-widest">Departure Date</p>
                        <p class="text-white text-xl font-bold font-display">{{ (new Date(store?.sprint?.start_date as string)).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' } ) }}</p>
                    </div>
                </div>
                <div class="gold-border-card p-6 flex items-center gap-5">
                    <div
                        class="size-12 rounded-lg bg-pirate-gold/10 border border-pirate-gold/20 flex items-center justify-center">
                        <span class="material-symbols-outlined text-pirate-gold">flag</span>
                    </div>
                    <div>
                        <p class="text-primary/60 text-[10px] font-black uppercase tracking-widest">Arrival Target</p>
                        <p class="text-white text-xl font-bold font-display">{{ (new Date(store?.sprint?.end_date as string)).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' } ) }}</p>
                    </div>
                </div>
                <div class="gold-border-card p-6 flex items-center gap-5">
                    <div
                        class="size-12 rounded-lg bg-pirate-gold/10 border border-pirate-gold/20 flex items-center justify-center">
                        <span class="material-symbols-outlined text-pirate-gold">groups</span>
                    </div>
                    <div>
                        <p class="text-primary/60 text-[10px] font-black uppercase tracking-widest">Formation Title</p>
                        <p class="text-white text-xl font-bold font-display">{{ store?.sprint?.formation?.title }}</p>
                    </div>
                </div>
            </section>
            <div class="grid grid-cols-12 gap-10 items-start">
                <section class="col-span-12 lg:col-span-7 bg-deep-blue/40 border border-white/5 rounded-3xl p-10 relative">
                    <div class="absolute top-6 right-8 opacity-10">
                        <span class="material-symbols-outlined text-8xl text-white">explore</span>
                    </div>
                    <div class="flex items-center gap-3 mb-8">
                        <span class="material-symbols-outlined text-pirate-gold">description</span>
                        <h3 class="text-2xl font-pirate text-pirate-gold tracking-widest uppercase">Mission Briefing</h3>
                    </div>
                    <div class="prose prose-invert max-w-none min-h-[50vh]">
                        <p>{{ store?.sprint?.description }}</p>
                    </div>
                </section>
                <section class="col-span-12 lg:col-span-5 space-y-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-pirate-gold">military_tech</span>
                            <h3 class="text-2xl font-pirate text-pirate-gold tracking-widest uppercase">Target Abilities
                            </h3>
                        </div>
                        <span class="text-primary/50 text-[10px] font-bold uppercase tracking-widest">{{ store?.sprint?.sprint_skills?.length }} Skills
                            Assigned</span>
                    </div>
                    <div class="space-y-4">
                        <div v-for="sprint_skill in store?.sprint?.sprint_skills"
                            class="bg-deep-blue border border-white/5 rounded-2xl p-6 hover:border-pirate-gold/30 transition-all group">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="size-14 rounded-xl flex items-center justify-center bg-pirate-gold/20 transition-colors">
                                        <span
                                            class="text-pirate-gold">{{ sprint_skill.skill.code }}</span>
                                    </div>
                                    <div>
                                        <h4 class="text-white font-bold tracking-wide">{{ sprint_skill.skill.title }}</h4>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="space-y-2">
                                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Description</span>
                                <p class="text-white">{{ sprint_skill.skill.description }}</p>
                            </div>
                        </div>
                    </div>
                    <NuxtLink :to="`/admin/sprint/edit/${store?.sprint?.id}`"
                        class="w-full py-4 bg-transparent border-2 border-dashed border-white/10 text-slate-500 rounded-2xl hover:border-pirate-gold/40 hover:text-pirate-gold transition-all flex items-center justify-center gap-2 font-bold text-sm uppercase tracking-widest group">
                        <span
                            class="material-symbols-outlined text-xl group-hover:scale-110 transition-transform">add_circle</span>
                        Assign New Ability
                    </NuxtLink>
                </section>
            </div>
            <footer class="mt-20 pt-10 border-t border-white/5 flex justify-between items-center opacity-40">
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-xl">anchor</span>
                        <span class="text-xs font-bold uppercase tracking-widest font-display">Locked to Log Pose</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-xl">map</span>
                        <span class="text-xs font-bold uppercase tracking-widest font-display">Coordinate: 12.04 N, 45.21
                            E</span>
                    </div>
                </div>
                <p class="text-[10px] font-bold uppercase tracking-[0.3em]">World Government Educational Division</p>
            </footer>
        </main>
    </NuxtLayout>
</template>