<script setup lang="ts">
    import { useClassGroup } from '~~/stores/class_group';


    const store = useClassGroup();

    const id: number = parseInt(useRoute().params?.id as string)

    onMounted(async() => {
        await store.fetchClassGroup(id);
        useHead({
            title: `Admin Dashboard - ${store.class_group?.name} Class`
        })
    })

    const getPercentage = (students: number, capacity: number): number => {
        if (!capacity || capacity === 0) return 0
        return Math.min(parseFloat(((students / capacity) * 100).toFixed(2)), 100)
    }

    definePageMeta({
        middleware: ['auth', 'admin']
    })
</script>

<template>
    <NuxtLayout name="admin">
        <div class="w-full min-h-screen">
            <section class="relative h-[350px] overflow-hidden">
                <img alt="Class Cover" class="w-full h-full object-cover"
                    :src="store.class_group?.image_url" />
                <div class="absolute inset-0 bg-gradient-to-t from-ocean-blue via-ocean-blue/60 to-transparent"></div>
                <div class="absolute inset-0 flex flex-col items-center justify-end pb-12 text-center px-4">
                    <div class="flex items-center gap-4 mb-4">
                        <span class="h-[1px] w-12 bg-primary/50"></span>
                        <span class="text-primary font-bold tracking-[0.3em] uppercase text-xs">Active Formation</span>
                        <span class="h-[1px] w-12 bg-primary/50"></span>
                    </div>
                    <h1
                        class="text-6xl md:text-7xl font-pirate font-black text-primary drop-shadow-[0_0_25px_rgba(255,215,0,0.4)] uppercase tracking-tighter">
                        {{ store.class_group?.name }}</h1>
                    <p class="text-turquoise font-display text-xl md:text-2xl mt-2 font-light tracking-widest uppercase">
                        Formation: {{ store.class_group?.formation.title }}</p>
                </div>
            </section>
            <main class="max-w-[1440px] mx-auto px-8 -mt-10 relative z-10 pb-20">
                <div class="grid grid-cols-12 gap-8">
                    <div class="col-span-12 lg:col-span-3 space-y-8">
                        <div
                            class="bg-deck-blue/80 backdrop-blur rounded-2xl border border-primary/30 p-6 gold-glow overflow-hidden relative group">
                            <div class="absolute top-0 right-0 p-3 opacity-20 group-hover:opacity-100 transition-opacity">
                                <span class="material-symbols-outlined text-primary text-4xl">military_tech</span>
                            </div>
                            <h3 class="text-[10px] font-black text-primary uppercase tracking-[0.2em] mb-4">Main Commander</h3>
                            <div class="flex flex-col items-center text-center">
                                <div class="size-24 rounded-2xl overflow-hidden border-2 border-primary p-1 mb-4">
                                    <img alt="Teacher Avatar" class="w-full h-full object-cover rounded-xl"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuDVvX7ziTfSGSW4O_4wIAG9ifmK0ffhAYhWDjGS32aeAWJin5O_1Ez2OIP8dkANXMZigfhSJVa_ZX6p_PYFsTnDWUViPcyFHc6RXr6wvgTjtr8lfucWPflUmf1Shb-E31_17d0WyM-ZB8sLYLm1qO94fkD1CC7ltaa0GBlBNavyrCNOdSDcdyI8Kda-82t7W-EKyiFkTk5lNycIAJWzRrnbr-3pArIEMMuZhj5RuwysCc4UM5PZxXOBc0qLxCmC6Ho2qnoNBXsEUBQ" />
                                </div>
                                <h4 class="text-xl font-pirate font-bold text-white mb-1">{{ store.class_group?.main_teacher.teacher.first_name }} {{ store.class_group?.main_teacher.teacher.last_name }}</h4>
                                <p class="text-turquoise text-xs font-bold uppercase tracking-widest mb-3">{{ store.class_group?.main_teacher.teacher.phone }}</p>
                                <div class="flex flex-wrap justify-center gap-2">
                                    <span
                                        class="px-2 py-0.5 bg-primary/10 text-primary text-[9px] font-black rounded border border-primary/20">MAIN</span>
                                    <span
                                        class="px-2 py-0.5 bg-primary/10 text-primary text-[9px] font-black rounded border border-primary/20">{{ store.class_group?.main_teacher.teacher.age }} YEARS</span>
                                </div>
                            </div>
                        </div>
                        <div
                            class="bg-deck-blue/80 backdrop-blur rounded-2xl border border-border-blue p-6 hover:border-turquoise/30 transition-all group">
                            <h3 class="text-[10px] font-black text-turquoise uppercase tracking-[0.2em] mb-4">First Mate</h3>
                            <div class="flex items-center gap-4">
                                <div class="size-16 rounded-xl overflow-hidden border border-turquoise/40 p-0.5">
                                    <img alt="Sub Teacher" class="w-full h-full object-cover rounded-lg"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuBayLU-sUlmcw2raPNWMhrLnCM5nKgy9AgXA-aqo-UNJufMZj_ElqksPC_Prgdyv-nvm7tCikAGyArou9gBCxRllVmoRgQ4g9ah4ccENdUvfZFMgxY6v346rWyaIteVracIAYoLr3OchIqcPuoYSIO8Nvcr2uljXAaANB9BEyse6wBbqiviZizrV2OT2AdyYGYIA8sxs_16yktOLNjYkPa4bGxK_RqWVIiiT7BvBYFonRbzw_2Jg-8B9svr1bijKvnh6H-tWBrweeU" />
                                </div>
                                <div>
                                    <h4 class="text-base font-pirate font-bold text-white">{{ store.class_group?.sub_teacher.teacher.first_name }} {{ store.class_group?.sub_teacher.teacher.last_name }}</h4>
                                    <p class="text-text-muted text-[10px] uppercase tracking-wider font-bold">SUB</p>
                                    <div class="mt-1 flex items-center gap-1 text-[9px] text-turquoise">
                                        <span class="material-symbols-outlined text-xs">history_edu</span>
                                        <span>{{ store.class_group?.sub_teacher.teacher.email }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-deck-blue/80 backdrop-blur rounded-2xl border border-border-blue p-6">
                            <h3 class="text-[10px] font-black text-text-silver uppercase tracking-[0.2em] mb-6">Fleet Strength
                            </h3>
                            <div class="flex flex-col items-center">
                                <div class="relative size-32 circular-progress rounded-full flex items-center justify-center"
                                    style="--progress: 80">
                                    <div class="size-28 bg-deck-blue rounded-full flex flex-col items-center justify-center">
                                        <span class="text-3xl font-black text-white">{{ store.class_group?.students_count }}</span>
                                        <span class="text-[9px] text-text-muted font-bold uppercase">of {{ store.class_group?.capacity }} Berths</span>
                                    </div>
                                </div>
                                <div class="mt-6 w-full space-y-3">
                                    <div class="flex justify-between text-xs font-bold">
                                        <span class="text-text-muted">Capacity</span>
                                        <span class="text-white">{{ getPercentage(store.class_group?.students_count as number, store.class_group?.capacity as number) }}% Full</span>
                                    </div>
                                    <div class="h-1 w-full bg-border-blue rounded-full overflow-hidden">
                                        <div class="h-full bg-primary" :style="`width: ${ getPercentage(store.class_group?.students_count as number, store.class_group?.capacity as number) }%`"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-12 lg:col-span-9 space-y-8">
                        <div
                            class="bg-deck-blue/60 backdrop-blur rounded-2xl border border-border-blue p-8 relative overflow-hidden">
                            <div class="absolute top-0 right-0 p-8 opacity-5">
                                <span class="material-symbols-outlined text-9xl">anchor</span>
                            </div>
                            <div class="relative z-10">
                                <h2
                                    class="text-xs font-black text-turquoise uppercase tracking-[0.3em] mb-4 flex items-center gap-3">
                                    <span class="h-4 w-1 bg-turquoise"></span>
                                    Mission Objectives
                                </h2>
                                <p class="text-xl text-text-silver font-light leading-relaxed max-w-3xl">
                                    {{ store.class_group?.description }}
                                </p>
                                <div class="mt-8 flex gap-6">
                                    <div class="flex items-center gap-3">
                                        <span class="material-symbols-outlined text-primary">timer</span>
                                        <span class="text-[10px] font-black uppercase tracking-widest text-text-muted">Duration:
                                            {{ store.class_group?.formation.duration }} Months</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="material-symbols-outlined text-turquoise">badge</span>
                                        <span class="text-[10px] font-black uppercase tracking-widest text-text-muted">Grade:
                                            {{ store.class_group?.formation.grade_name }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-deck-blue/80 backdrop-blur rounded-2xl border border-border-blue overflow-hidden">
                            <div
                                class="px-8 py-6 border-b border-border-blue flex justify-between items-center bg-ocean-blue/40">
                                <div>
                                    <h3 class="text-xl font-pirate font-bold text-white tracking-wider flex items-center gap-3">
                                        CREW MANIFEST
                                    </h3>
                                    <p class="text-[10px] text-text-muted font-bold uppercase tracking-widest mt-1">Verified
                                        Personnel Registry</p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <NuxtLink :to="`/admin/class/edit/${ store.class_group?.id }/`"
                                        class="bg-primary text-ocean-blue px-4 py-2 rounded-lg text-xs font-black uppercase tracking-widest hover:brightness-110 transition-all flex items-center gap-2">
                                        <span class="material-symbols-outlined text-sm">person_add</span> Add New
                                    </NuxtLink>
                                </div>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead>
                                        <tr class="bg-ocean-blue/20">
                                            <th
                                                class="px-8 py-4 text-[10px] font-black text-text-muted uppercase tracking-[0.2em]">
                                                Member Profile</th>
                                            <th
                                                class="px-8 py-4 text-[10px] font-black text-text-muted uppercase tracking-[0.2em]">
                                                Age</th>
                                            <th
                                                class="px-8 py-4 text-[10px] font-black text-text-muted uppercase tracking-[0.2em]">
                                                CIN</th>
                                            <th
                                                class="px-8 py-4 text-[10px] font-black text-text-muted uppercase tracking-[0.2em] text-right">
                                                Email</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-border-blue/30">
                                        <template v-if="store.class_group?.students.length as number > 0">
                                            <tr v-for="student in store.class_group?.students" class="hover:bg-white/5 transition-colors group">
                                                <td class="px-8 py-5">
                                                    <div class="flex items-center gap-4">
                                                        <div
                                                            class="size-12 rounded-xl bg-border-blue overflow-hidden border border-primary/20 group-hover:border-primary transition-colors">
                                                            <img alt="Luffy" class="w-full h-full object-cover"
                                                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuCHGmNKmbupLxigQtyugPPxCgUCLC8iHifS9R0WijCOUGAiqRTkccVjR9i5oY3j6RbyD8vSqgNfHyAN9FA1X-x1tuMEZ27Wl7tLEVtgwgBW_W0xGkHk899R0N6WMVPj9kdYbGyIODdYCzfnlXhsFoMmMKbvzs_MruH_2NULKmjQtNy2aQPKLShETIaqq-mfE0meqTrgvTngsVUouV3oMUF4Ap0n7W_BBP8hdUZfADkJPAzTKGnDm-h9X81H5tPxnXOhc6jKTIg74JI" />
                                                        </div>
                                                        <div>
                                                            <p class="text-white font-bold text-sm tracking-wide">{{ student.first_name }} {{ student.last_name }}
                                                            </p>
                                                            <p
                                                                class="text-[9px] text-primary/70 font-black tracking-widest uppercase">
                                                                ID: M-{{ student.id }}-SUN</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-8 py-5">
                                                    <span
                                                        class="text-xs font-bold text-turquoise/80 uppercase tracking-widest">{{ student.age }} YEARS</span>
                                                </td>
                                                <td class="px-8 py-5">
                                                    <span
                                                        class="text-xs font-bold text-turquoise/80 uppercase tracking-widest">{{ student.cin }}</span>
                                                </td>
                                                <td class="px-8 py-5 text-right">
                                                    <span
                                                        class="text-xs font-bold text-turquoise/80 uppercase tracking-widest">{{ student.email }}</span>
                                                </td>
                                            </tr>
                                        </template>
                                        <tr v-else>
                                            <td class="px-8 py-5 text-center" colspan="4">
                                                <p class="text-sm font-bold text-text-muted">No members found.</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </NuxtLayout>
</template>