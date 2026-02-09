<script setup lang="ts">
    import { useUser } from '~~/stores/user';

    useHead({
        title: `Admin Dashboard - Student Member`
    })

    const store = useUser();

    const id: number = parseInt(useRoute().params?.id as string)

    onMounted(async() => {
        await store.fetchUser('STUDENT', id);
    })

    definePageMeta({
        middleware: ['auth', 'admin']
    })
</script>

<template>
    <NuxtLayout name="admin">
        <main class="flex-1 flex flex-col min-w-0 overflow-hidden bg-deep-blue/10">
            <div class="flex-1 overflow-y-auto p-8">
                <div class="max-w-6xl mx-auto">
                    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
                        <div>
                            <h2 class="text-3xl font-black tracking-tight text-white mb-1 uppercase">Crew Member Dossier
                            </h2>
                            <p class="text-primary/60 flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">verified_user</span>
                                Authenticating Pirate Identity: #STU-1042-WANO
                            </p>
                        </div>
                        <div class="flex gap-3">
                            <NuxtLink :to="`/admin/user/student/${id}/edit`"
                                class="flex items-center gap-2 bg-primary/10 hover:bg-primary/20 text-primary border border-primary/30 px-4 py-2 rounded-lg font-bold transition-all">
                                <span class="material-symbols-outlined">edit</span> Edit Records
                            </NuxtLink>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                        <div class="lg:col-span-5 flex justify-center">
                            <div
                                class="bounty-card w-full max-w-sm rounded-sm p-6 shadow-2xl transform hover:-rotate-1 transition-transform">
                                <div class="text-center mb-4">
                                    <h3 class="text-4xl font-serif font-black tracking-[0.2em] mb-1">WANTED</h3>
                                    <p class="text-xs font-serif font-bold tracking-widest border-y border-[#3d2b1f] py-1">
                                        DEAD OR ALIVE</p>
                                </div>
                                <div
                                    class="aspect-[4/5] bg-[#3d2b1f]/10 mb-4 overflow-hidden border-2 border-[#3d2b1f]/20 relative">
                                    <img alt="Student Portrait"
                                        class="w-full h-full object-cover filter sepia-[0.3] contrast-[1.1]"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuANgXUFkXb5GqMc33vABiN-w8Dt_YdS_JfNtEp6Nr9WS4CzbLhIdHJufNlExJKG313mA-Apz3-KqY34X7-TEz9xYag62a-a2CskTBhggbVzJz7wGW0ipk7HTUjl-jyKoPZPdQqN6hTcvnzHN_30ts1VFlzf4AhyFg8dLusoKRtX7E1C4okjEvT2ySLqAvbX3O9GDf0rbmfrUi337eeJve_xopPR15EntRYZM94MCvh1wExvCBe48pqhnY2mdz0St3SImDEyauSXJSw" />
                                    <div class="absolute inset-0 bg-gradient-to-t from-[#3d2b1f]/20 to-transparent">
                                    </div>
                                </div>
                                <div class="text-center space-y-2">
                                    <h4
                                        class="text-3xl font-serif font-black tracking-tight uppercase border-b-2 border-[#3d2b1f]/10 pb-2">
                                        {{ store.user?.first_name }} {{ store.user?.last_name }}</h4>
                                    <div class="grid grid-cols-2 gap-4 text-left pt-2 font-serif">
                                        <div>
                                            <p class="text-[10px] uppercase opacity-60">Status</p>
                                            <p class="text-sm font-bold">MEDIC STUDENT</p>
                                        </div>
                                        <div>
                                            <p class="text-[10px] uppercase opacity-60">Identity</p>
                                            <p class="text-sm font-bold">{{ store.user?.cin }}</p>
                                        </div>
                                        <div class="col-span-2">
                                            <p class="text-[10px] uppercase opacity-60">Electronic Transponder Snail</p>
                                            <p class="text-sm font-bold">{{ store.user?.email }}</p>
                                        </div>
                                        <div class="col-span-2">
                                            <p class="text-[10px] uppercase opacity-60">Signal Frequency</p>
                                            <p class="text-sm font-bold">{{ store.user?.phone }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-8 text-center">
                                    <p class="text-xs font-serif italic mb-1">Age: {{ store.user?.age }} Years</p>
                                    <div class="flex items-center justify-center gap-1">
                                        <span class="text-2xl font-black font-serif">฿ 100,000,000</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="lg:col-span-7 space-y-6">
                            <div class="bg-primary/5 border border-primary/20 rounded-xl p-6 backdrop-blur-sm">
                                <div class="flex items-center gap-3 mb-6">
                                    <span
                                        class="material-symbols-outlined text-teacher-turquoise bg-teacher-turquoise/10 p-2 rounded-lg">hub</span>
                                    <h3 class="text-lg font-bold text-white uppercase tracking-wider">Crew Assignment
                                    </h3>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-1">
                                        <p class="text-xs text-primary/40 uppercase font-bold tracking-widest">
                                            Ship/Class Name</p>
                                        <p class="text-white font-medium flex items-center gap-2">
                                            <span class="material-symbols-outlined text-student-blue text-sm">sailing</span>
                                            {{ store.user?.class_group?.name }}
                                        </p>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-xs text-primary/40 uppercase font-bold tracking-widest">Fleet
                                            Admiral (Main Teacher)</p>
                                        <p class="text-white font-medium flex items-center gap-2 text-teacher-turquoise">
                                            <span class="material-symbols-outlined text-sm">school</span>
                                            {{ store.user?.class_group?.main_teacher.teacher.first_name }} {{ store.user?.class_group?.main_teacher.teacher.last_name }}
                                        </p>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-xs text-primary/40 uppercase font-bold tracking-widest">
                                            Navigator (Sub Teacher)</p>
                                        <p class="text-white font-medium flex items-center gap-2">
                                            <span class="material-symbols-outlined text-sm">compass_calibration</span>
                                            {{ store.user?.class_group?.sub_teacher.teacher.first_name }} {{ store.user?.class_group?.sub_teacher.teacher.last_name }}
                                        </p>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-xs text-primary/40 uppercase font-bold tracking-widest">
                                            Assignment Status</p>
                                        <span v-if="store.user?.is_active"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-500/10 text-emerald-400 text-xs font-bold border border-emerald-500/20">
                                            <span class="size-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                            Active Duty
                                        </span>
                                        <span v-else
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-red-500/10 text-red-400 text-xs font-bold border border-red-500/20">
                                            <span class="size-1.5 rounded-full bg-red-400 animate-pulse"></span>
                                            Retired
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-primary/5 border border-primary/20 rounded-xl p-6 backdrop-blur-sm">
                                <div class="flex items-center gap-3 mb-6">
                                    <span
                                        class="material-symbols-outlined text-primary bg-primary/10 p-2 rounded-lg">explore</span>
                                    <h3 class="text-lg font-bold text-white uppercase tracking-wider">Voyage Details
                                    </h3>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-1">
                                        <p class="text-xs text-primary/40 uppercase font-bold tracking-widest">
                                            Formation Name</p>
                                        <p class="text-white font-medium">{{ store.user?.class_group?.formation?.title }}</p>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-xs text-primary/40 uppercase font-bold tracking-widest">Grade
                                            Level</p>
                                        <p class="text-white font-medium">{{ store.user?.class_group?.formation?.grade_name }}</p>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-xs text-primary/40 uppercase font-bold tracking-widest">Voyage
                                            Duration</p>
                                        <p class="text-white font-medium">{{ store.user?.class_group?.formation?.duration }} Months</p>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-xs text-primary/40 uppercase font-bold tracking-widest">Scholar
                                            Year</p>
                                        <p class="text-white font-medium italic text-primary">{{ store.user?.class_group?.formation?.grade_level?.scholar_year }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </NuxtLayout>
</template>