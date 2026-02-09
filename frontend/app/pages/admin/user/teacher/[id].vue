<script setup lang="ts">
    import { useUser } from '~~/stores/user';

    useHead({
        title: `Admin Dashboard - Teacher Member`
    })

    const store = useUser();

    const id: number = parseInt(useRoute().params?.id as string)

    onMounted(async() => {
        await store.fetchUser('TEACHER', id);
    })
</script>

<template>
    <NuxtLayout name="admin">
        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto">
            <div class="p-8">
                <nav class="flex items-center gap-2 text-sm font-medium text-primary/50 mb-6">
                    <a class="hover:text-primary transition-colors" href="#">Crew Registry</a>
                    <span class="material-symbols-outlined text-xs">chevron_right</span>
                    <span class="text-primary font-bold">Commander Dossier</span>
                </nav>
                <div class="bg-primary/5 border border-primary/20 rounded-2xl p-8 mb-8 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8 opacity-10">
                        <span class="material-symbols-outlined text-9xl">shield_person</span>
                    </div>
                    <div class="flex flex-col md:flex-row gap-8 items-start relative z-10">
                        <div class="relative">
                            <div class="size-40 rounded-2xl bg-cover bg-center border-4 border-primary/30 shadow-2xl"
                                style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDV3IlBFJLGm-BLV8Nwc5IrfMHtfR0eZluHofv9Pab2PnHzP0qZWEWYV0Sp4moH7EJd268006LwHoLDJjyFq183UXEduaZ_K9QWR53h2t-kMQOyHH8M6gKsgTJ0aSxgyC2NztyhIvN8llv5Rdk9_Qgb5MLtN6kOniO7d_uFHgNOvqs0OdgjPMTSRAj-65yIMpVg1b56iu7nCBH54Chplwy535KA0fD09EPzeLnxxuOEEU0_-TYCFnD1WGzzX4GZy6nJUaLZRgpoU8Y');">
                            </div>
                            <div
                                class="absolute -bottom-3 -right-3 bg-teacher-turquoise text-background-dark p-2 rounded-lg font-bold text-xs shadow-lg flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">verified_user</span>
                                ACTIVE
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                                <div>
                                    <h2 class="text-4xl font-black tracking-tight text-white mb-1">{{ store.user?.first_name }} {{ store.user?.last_name }}</h2>
                                    <p class="text-teacher-turquoise font-bold flex items-center gap-2">
                                        <span class="material-symbols-outlined text-sm">history_edu</span>
                                        Teacher
                                    </p>
                                </div>
                                <div class="flex gap-3">
                                    <NuxtLink :to="`/admin/user/teacher/${id}/edit`"
                                        class="bg-primary/10 hover:bg-primary/20 text-primary border border-primary/30 px-4 py-2 rounded-lg font-bold text-sm transition-all flex items-center gap-2">
                                        <span class="material-symbols-outlined text-sm">edit</span> Edit Dossier
                                    </NuxtLink>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                                <div class="space-y-1">
                                    <p class="text-[10px] text-primary/40 uppercase font-black tracking-widest">Age</p>
                                    <p class="text-white font-medium">{{ store.user?.age }} Years Old</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] text-primary/40 uppercase font-black tracking-widest">
                                        Identification (CIN)</p>
                                    <p class="text-white font-medium">{{ store.user?.cin }}</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] text-primary/40 uppercase font-black tracking-widest">Den Den
                                        Mushi (Phone)</p>
                                    <p class="text-white font-medium">{{ store.user?.phone }}</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] text-primary/40 uppercase font-black tracking-widest">Email
                                        Address</p>
                                    <p class="text-white font-medium">{{ store.user?.email }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary text-3xl">groups_3</span>
                            <h3 class="text-2xl font-bold text-white">Commanded Fleets</h3>
                        </div>
                        <span
                            class="px-3 py-1 bg-primary/10 text-primary text-xs font-bold rounded-full border border-primary/20">{{ store.user?.class_teachers?.length }}
                            ACTIVE CLASSES</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <template v-if="store.user?.class_teachers?.length as number > 0">
                            <div v-for="class_teacher in store.user?.class_teachers"
                                class="group bg-primary/5 border border-primary/10 rounded-xl overflow-hidden hover:border-primary/40 transition-all hover:shadow-2xl hover:shadow-primary/5">
                                <div class="h-40 bg-cover bg-center relative"
                                    :style="`background-image: url('${class_teacher.class_group?.image_url}');`">
                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-background-dark via-transparent to-transparent">
                                    </div>
                                    <div class="absolute bottom-4 left-4">
                                        <span v-if="class_teacher.class_group?.main_teacher.teacher.id == store.user?.id"
                                            class="bg-primary text-background-dark text-[10px] font-black px-2 py-0.5 rounded uppercase tracking-tighter">Main</span>
                                        <span v-if="class_teacher.class_group?.sub_teacher.teacher.id == store.user?.id"
                                            class="bg-primary text-background-dark text-[10px] font-black px-2 py-0.5 rounded uppercase tracking-tighter">Sub</span>
                                    </div>
                                </div>
                                <div class="p-5 space-y-4">
                                    <div>
                                        <h4 class="text-lg font-bold text-white group-hover:text-primary transition-colors">
                                            {{ class_teacher.class_group?.name }}</h4>
                                        <p class="text-primary/50 text-xs font-medium">Formation: {{ class_teacher.class_group?.formation?.title }} ({{ class_teacher.class_group?.formation?.grade_name }})</p>
                                    </div>
                                    <div class="flex items-center justify-between border-t border-primary/10 pt-4">
                                        <div class="flex items-center gap-2 text-primary/60">
                                            <span class="material-symbols-outlined text-sm">calendar_month</span>
                                            <span class="text-xs font-bold">SY {{ class_teacher.class_group?.formation?.grade_level?.scholar_year }}</span>
                                        </div>
                                        <div class="flex items-center gap-1 text-primary/60">
                                            <span class="material-symbols-outlined text-sm">group</span>
                                            <span class="text-xs font-bold">{{ class_teacher.class_group?.students_count }} Scholars</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div v-else class="bg-primary/10 border border-primary/20 rounded-xl p-6 flex items-center gap-4">
                            <div class="size-12 rounded-full bg-primary/20 flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined">groups_3</span>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-primary/60 uppercase">Commanded Fleets</p>
                                <p class="text-2xl font-black text-white">None</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-primary/10 border border-primary/20 rounded-xl p-6 flex items-center gap-4">
                        <div class="size-12 rounded-full bg-primary/20 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined">auto_stories</span>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-primary/60 uppercase">Total Lessons</p>
                            <p class="text-2xl font-black text-white">458</p>
                        </div>
                    </div>
                    <div class="bg-primary/10 border border-primary/20 rounded-xl p-6 flex items-center gap-4">
                        <div class="size-12 rounded-full bg-primary/20 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined">anchor</span>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-primary/60 uppercase">Years of Service</p>
                            <p class="text-2xl font-black text-white">{{ store.user?.created_at }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </NuxtLayout>
</template>