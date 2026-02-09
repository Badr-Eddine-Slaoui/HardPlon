<script setup lang="ts">
    import { useSkill } from '~~/stores/skill';

    const store = useSkill();

    const id: number = parseInt(useRoute().params?.id as string)

    onMounted(async() => {
        await store.fetchSkill(id);
        useHead({
            title: `Admin Dashboard - Skill ${store.skill?.code}: ${store.skill?.title}`
        })
    })

    definePageMeta({
        middleware: ['auth', 'admin']
    })
</script>

<template>
    <NuxtLayout name="admin">
        <main class="w-full flex h-[calc(100vh-65px)] overflow-hidden">
            <section class="flex-1 overflow-y-auto p-10 custom-scrollbar bg-[#0a1416]">
                <div class="max-w-6xl mx-auto flex flex-col lg:flex-row gap-8">
                    <div class="lg:w-[65%] flex flex-col gap-8">
                        <div
                            class="bounty-texture burned-edge p-10 text-[#2c1e14] rounded-sm relative border-[14px] border-[#d9c59a] border-double">
                            <div class="absolute top-4 left-6 border-b border-[#8b4513]/40 pb-0.5">
                                <span class="text-[11px] font-bold uppercase font-mono tracking-tight text-[#5d3a1a]">MARINE
                                    ID: 009-SK</span>
                            </div>
                            <div class="text-center">
                                <h3 class="font-gothic text-5xl mb-1 opacity-90 uppercase tracking-[0.2em] leading-none">
                                    WANTED</h3>
                                <div class="w-full h-[1px] bg-[#8b4513]/40 my-6"></div>
                                <h2
                                    class="font-pirate text-6xl font-black uppercase tracking-tight leading-none drop-shadow-md">
                                    {{ store.skill?.code }}: {{ store.skill?.title }}</h2>
                                <p class="font-pirate text-xl italic mt-3 text-[#5d3a1a] opacity-80">"The Invisible Armor of
                                    the New World"</p>
                            </div>
                            <div class="grid grid-cols-2 gap-10 mt-12 pb-6">
                                <div class="space-y-4">
                                    <h4
                                        class="font-bold uppercase text-[10px] tracking-widest border-b border-[#8b4513]/30 pb-1 text-[#5d3a1a]">
                                        Description</h4>
                                    <p class="font-pirate text-lg leading-snug">
                                        {{ store.skill?.description }}
                                    </p>
                                </div>
                                <div class="space-y-4">
                                    <h4
                                        class="font-bold uppercase text-[10px] tracking-widest border-b border-[#8b4513]/30 pb-1 text-[#5d3a1a]">
                                        Strategic Assets</h4>
                                    <ul class="font-pirate text-lg space-y-2.5">
                                        <li class="flex items-center gap-2.5"><span
                                                class="material-symbols-outlined text-sm font-bold">anchor</span> Logia
                                            Neutralization</li>
                                        <li class="flex items-center gap-2.5"><span
                                                class="material-symbols-outlined text-sm font-bold">anchor</span> Weapon
                                            Imbuement</li>
                                        <li class="flex items-center gap-2.5"><span
                                                class="material-symbols-outlined text-sm font-bold">anchor</span> Invisible
                                            Fortification</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="mt-8 pt-8 border-t border-[#8b4513]/20 flex justify-between items-end">
                                <div class="flex flex-col">
                                    <p class="text-[9px] font-bold uppercase opacity-60 text-[#5d3a1a]">Dossier Verified By
                                    </p>
                                    <p class="font-gothic text-2xl leading-none mt-1">Sengoku the Buddha</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] font-bold uppercase text-[#5d3a1a]">Combat Threat Level</p>
                                    <div class="flex gap-1.5 justify-end mt-2">
                                        <span class="size-3.5 bg-[#8b4513] rounded-sm transform rotate-45"></span>
                                        <span class="size-3.5 bg-[#8b4513] rounded-sm transform rotate-45"></span>
                                        <span class="size-3.5 bg-[#8b4513] rounded-sm transform rotate-45"></span>
                                        <span class="size-3.5 bg-[#8b4513] rounded-sm transform rotate-45"></span>
                                        <span
                                            class="size-3.5 border border-[#8b4513]/40 rounded-sm transform rotate-45"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-[#182f34] border border-[#224249] rounded-xl overflow-hidden shadow-2xl">
                            <div
                                class="px-8 py-5 border-b border-[#224249] bg-background-dark/30 flex items-center justify-between">
                                <h3 class="text-lg font-bold font-display flex items-center gap-3">
                                    <span class="material-symbols-outlined text-primary">explore</span>
                                    Deployed in Formation
                                </h3>
                            </div>
                            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div
                                    class="bg-[#224249]/30 p-5 rounded-lg border border-[#315f68] flex items-center gap-5 hover:border-primary hover:bg-[#224249]/50 transition-all cursor-pointer group">
                                    <div
                                        class="size-14 rounded-lg bg-primary/10 flex items-center justify-center border border-primary/20 group-hover:bg-primary/20 transition-all shrink-0">
                                        <span class="material-symbols-outlined text-primary text-3xl">groups</span>
                                    </div>
                                    <div class="overflow-hidden">
                                        <h4 class="text-white font-bold text-lg leading-tight truncate">{{ store.skill?.formation?.title }}
                                        </h4>
                                        <p class="text-[#90c1cb] text-sm mt-1">{{ store.skill?.formation?.description }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="lg:w-[35%]">
                        <div class="bg-[#182f34] border border-[#224249] rounded-xl shadow-2xl sticky top-0">
                            <div class="px-8 py-6 border-b border-[#224249] bg-background-dark/30">
                                <h3 class="text-xl font-bold font-display flex items-center gap-3">
                                    <span class="material-symbols-outlined text-pirate-gold">military_tech</span>
                                    Mastery Timeline
                                </h3>
                            </div>
                            <div class="p-8 pt-10">
                                <div class="relative timeline-line space-y-12">
                                    <template v-for="(skill_level, index) in store.skill?.skill_levels">
                                        <div v-if="index == 0" class="relative flex items-start gap-6 group">
                                            <div
                                                class="flex items-center justify-center w-10 h-10 rounded-full border-2 border-primary bg-[#101f22] group-hover:bg-primary transition-all shrink-0 z-10 shadow-[0_0_10px_rgba(13,204,242,0.3)]">
                                                <span
                                                    class="material-symbols-outlined text-sm group-hover:text-white text-primary">star</span>
                                            </div>
                                            <div class="flex-1 -mt-1">
                                                <div class="flex items-center justify-between mb-1">
                                                    <span
                                                        class="text-xs font-black text-primary uppercase tracking-[0.15em]">Rank
                                                        0{{ skill_level.level?.order }}</span>
                                                </div>
                                                <h4 class="text-white font-bold text-lg leading-tight">{{ skill_level.level?.name }}</h4>
                                                <p class="text-[#90c1cb] text-sm mt-2 leading-relaxed">{{ skill_level.criteria }}</p>
                                            </div>
                                        </div>
                                        <div v-else-if="index == (store.skill?.skill_levels?.length as number) - 1" class="relative flex items-start gap-6 group">
                                            <div
                                                class="flex items-center justify-center w-10 h-10 rounded-full border-2 border-pirate-gold bg-[#101f22] group-hover:bg-pirate-gold transition-all shrink-0 z-10 shadow-[0_0_20px_rgba(255,215,0,0.25)]">
                                                <span
                                                    class="material-symbols-outlined text-sm group-hover:text-background-dark text-pirate-gold font-bold">star</span>
                                            </div>
                                            <div class="flex-1 -mt-1">
                                                <div class="flex items-center justify-between mb-1">
                                                    <span
                                                        class="text-xs font-black text-pirate-gold uppercase tracking-[0.15em]">Rank
                                                        0{{ skill_level.level?.order }}</span>
                                                    <span class="text-[10px] text-pirate-gold-dark font-bold">LEGENDARY</span>
                                                </div>
                                                <h4 class="text-white font-bold text-lg leading-tight">{{ skill_level.level?.name }}</h4>
                                                <p class="text-[#90c1cb] text-sm mt-2 leading-relaxed">{{ skill_level.criteria }}</p>
                                            </div>
                                        </div>
                                        <div v-else class="relative flex items-start gap-6 group">
                                            <div
                                                class="flex items-center justify-center w-10 h-10 rounded-full border border-[#315f68] bg-[#101f22] group-hover:border-2 group-hover:border-primary group-hover:bg-primary transition-all shrink-0 z-10 shadow-xl">
                                                <span
                                                    class="material-symbols-outlined text-sm group-hover:text-white text-[#315f68]">star</span>
                                            </div>
                                            <div class="flex-1 -mt-1">
                                                <div class="flex items-center justify-between mb-1">
                                                    <span
                                                        class="text-xs font-black text-[#90c1cb] uppercase tracking-[0.15em]">Rank
                                                        0{{ skill_level.level?.order }}</span>
                                                </div>
                                                <h4 class="text-white font-bold text-lg leading-tight">{{ skill_level.level?.name }}</h4>
                                                <p class="text-[#90c1cb] text-sm mt-2 leading-relaxed">{{ skill_level.criteria }}</p>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <NuxtLink :to="`/admin/skill/edit/${store.skill?.id}`"
                                    class="w-full mt-14 py-4 border border-dashed border-[#315f68] text-[#90c1cb] hover:text-white hover:border-primary hover:bg-primary/5 transition-all rounded-lg flex items-center justify-center gap-3 text-sm font-bold uppercase tracking-widest">
                                    <span class="material-symbols-outlined text-xl">add_circle</span>
                                    Add Rank Milestone
                                </NuxtLink>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </NuxtLayout>
</template>