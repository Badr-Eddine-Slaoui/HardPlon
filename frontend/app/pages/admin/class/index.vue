<script setup lang="ts">
    import { useClassGroup } from '../../../../stores/class_group';

    useHead({
        title: `Admin Dashboard - Classes`
    })

    const classes = useClassGroup();

    onMounted(async() => {
        await classes.fetchClassGroups();
    })

    const getPercentage = (students: number, capacity: number): number => {
        if (!capacity || capacity === 0) return 0
        return Math.min(parseFloat(((students / capacity) * 100).toFixed(2)), 100)
    }

    function openArchiveModal(e: MouseEvent, id: number): void {
        const modal = document.getElementById('archive-modal') as HTMLElement;
        modal.classList.remove('hidden');

        const form = modal.querySelector('form') as HTMLFormElement;
        form.onsubmit = async (e) => {
            e.preventDefault();
            await classes.archiveClassGroup(id);
            closeArchiveModal();
        }

        const title = modal.querySelector('.title') as HTMLElement;
        const target = e.currentTarget as HTMLElement;
        title.textContent = target?.dataset?.title as string;
    }

    function closeArchiveModal(): void {
        const modal = document.getElementById('archive-modal') as HTMLElement;
        modal.classList.add('hidden');
    }

    function openResurrectModal(e: MouseEvent, id: number): void {
        const modal = document.getElementById('resurrect-modal') as HTMLElement;
        modal.classList.remove('hidden');

        const form = modal.querySelector('form') as HTMLFormElement;
        form.onsubmit = async (e) => {
            e.preventDefault();
            await classes.restoreClassGroup(id);
            closeResurrectModal();
        }

        const title = modal.querySelector('.title') as HTMLElement;
        const target = e.currentTarget as HTMLElement;
        title.textContent = target?.dataset?.title as string;
    }

    function closeResurrectModal(): void {
        const modal = document.getElementById('resurrect-modal') as HTMLElement;
        modal.classList.add('hidden');
    }
</script>

<template>
    <NuxtLayout name="admin">
        <main v-if="classes.class_groups?.length as number > 0" class="flex w-full min-h-screen overflow-hidden">
            <section class="flex-1 overflow-y-auto p-10 custom-scrollbar">
                <div class="max-w-7xl mx-auto">
                    <div class="flex justify-between items-end mb-8">
                        <div>
                            <h2 class="text-3xl font-bold text-white font-display mb-1">Fleet Command</h2>
                            <p class="text-[#90c1cb]">Manage active class groups and tactical deployments across the
                                fleet.</p>
                        </div>
                        <NuxtLink to="/admin/class/create"
                            class="flex items-center gap-2 px-6 py-3 bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark font-bold rounded-lg transition-all transform hover:scale-105 shadow-[0_4px_14px_0_rgba(255,215,0,0.39)]">
                            <span class="material-symbols-outlined font-bold">add_moderator</span>
                            <span>Commission New Fleet</span>
                        </NuxtLink>
                    </div>
                    <div class="rounded-xl border border-[#315f68] bg-[#182f34] overflow-hidden shadow-2xl">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-[#224249]/50 border-b border-[#315f68]">
                                    <th class="px-6 py-4 text-white text-xs font-bold uppercase tracking-wider">Fleet
                                        Name</th>
                                    <th class="px-6 py-4 text-white text-xs font-bold uppercase tracking-wider">
                                        Commander</th>
                                    <th class="px-6 py-4 text-white text-xs font-bold uppercase tracking-wider">Tactical
                                        Formation</th>
                                    <th class="px-6 py-4 text-white text-xs font-bold uppercase tracking-wider">Crew
                                        Size</th>
                                    <th class="px-6 py-4 text-white text-xs font-bold uppercase tracking-wider text-right">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#224249]">
                                <template v-for="class_group in classes.class_groups" :key="class_group.id">
                                    <tr v-if="class_group.is_active" class="hover:bg-white/5 transition-colors">
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="size-10 bg-primary/10 rounded-lg flex items-center justify-center border border-primary/20">
                                                    <span
                                                        class="material-symbols-outlined text-primary">directions_boat</span>
                                                </div>
                                                <span class="text-white font-bold">{{ class_group.name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-2">
                                                <img alt="Teacher Avatar"
                                                    class="w-8 h-8 rounded-full border border-[#315f68]"
                                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuCfam2DBclD1TUzoWKqqW4Gr0Ahqt4BtObFrglwoKE4Z2yRSJjWjK19DRs6L3OthWJZJ_mkOXNZS99bPYbWvCkGhFdAvR59DSeL-Y5YGZZERsvHo3yGBmdFJ2VyV71yltOhuZW01LdBa25eA2T1YCohCAXQXkRZYPyrsBl2e_LUqdzPgBwJWwTk8kCWBf7Z45EPViMCbkdlTf9c8FonAPcKxZrk8JkJfNPKh6WjDNB6xg-3HtIDbeJEVve5nMuVLNTY-U90aDBphQI" />
                                                <span
                                                    class="text-white">{{ class_group.main_teacher.teacher.first_name }} {{ class_group.main_teacher.teacher.last_name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 text-[#90c1cb]">{{ class_group.formation.title }}</td>
                                        <td class="px-6 py-5">
                                            <div class="flex flex-col gap-1.5 min-w-32">
                                                <div class="flex justify-between text-xs text-white">
                                                    <span>{{ class_group.students_count }} /
                                                        {{ class_group.capacity }}</span>
                                                    <span class="text-primary">{{ getPercentage(class_group.students_count as number, class_group.capacity as number) }}%</span>
                                                </div>
                                                <div class="w-full bg-[#102023] rounded-full h-1.5">
                                                    <div class="bg-primary h-1.5 rounded-full shadow-[0_0_8px_rgba(13,204,242,0.6)]"
                                                        :style="`width: ${getPercentage(class_group.students_count as number, class_group.capacity as number )}%`"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 text-right">
                                            <div class="flex justify-end gap-2">
                                                <NuxtLink :to="`/admin/class/${class_group.id}`"
                                                    class="p-2 text-[#90c1cb] hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                                                    title="View Details">
                                                    <span class="material-symbols-outlined">visibility</span>
                                                </NuxtLink>
                                                <NuxtLink :to="`/admin/class/edit/${class_group.id}`"
                                                    class="p-2 text-[#90c1cb] hover:text-pirate-gold hover:bg-pirate-gold/10 rounded-lg transition-all"
                                                    title="Edit Fleet">
                                                    <span class="material-symbols-outlined">edit</span>
                                                </NuxtLink>
                                                <button :data-title="class_group.name" @click="openArchiveModal($event, class_group.id)"
                                                    class="p-2 text-[#90c1cb] hover:text-red-400 hover:bg-red-400/10 rounded-lg transition-all"
                                                    title="Archive Fleet">
                                                    <span class="material-symbols-outlined">archive</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-else class="hover:bg-white/5 transition-colors opacity-55">
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="size-10 bg-primary/10 rounded-lg flex items-center justify-center border border-primary/20">
                                                    <span
                                                        class="material-symbols-outlined text-primary">directions_boat</span>
                                                </div>
                                                <span class="text-white font-bold">{{ class_group.name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-2">
                                                <img alt="Teacher Avatar"
                                                    class="w-8 h-8 rounded-full border border-[#315f68]"
                                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuCfam2DBclD1TUzoWKqqW4Gr0Ahqt4BtObFrglwoKE4Z2yRSJjWjK19DRs6L3OthWJZJ_mkOXNZS99bPYbWvCkGhFdAvR59DSeL-Y5YGZZERsvHo3yGBmdFJ2VyV71yltOhuZW01LdBa25eA2T1YCohCAXQXkRZYPyrsBl2e_LUqdzPgBwJWwTk8kCWBf7Z45EPViMCbkdlTf9c8FonAPcKxZrk8JkJfNPKh6WjDNB6xg-3HtIDbeJEVve5nMuVLNTY-U90aDBphQI" />
                                                <span
                                                    class="text-white">{{ class_group.main_teacher.teacher.first_name }} {{ class_group.main_teacher.teacher.last_name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 text-[#90c1cb]">{{ class_group.formation.title }}</td>
                                        <td class="px-6 py-5">
                                            <div class="flex flex-col gap-1.5 min-w-32">
                                                <div class="flex justify-between text-xs text-white">
                                                    <span>{{ class_group.students_count }} /
                                                        {{ class_group.capacity }}</span>
                                                    <span class="text-primary">{{ getPercentage(class_group.students_count as number, class_group.capacity as number) }}%</span>
                                                </div>
                                                <div class="w-full bg-[#102023] rounded-full h-1.5">
                                                    <div class="bg-primary h-1.5 rounded-full shadow-[0_0_8px_rgba(13,204,242,0.6)]"
                                                        :style="{ width: getPercentage(class_group.students_count as number, class_group.capacity as number) + '%' }"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 text-right">
                                            <div class="flex justify-end gap-2">
                                                <button :data-title="class_group.name" @click="openResurrectModal($event, class_group.id)"
                                                    class="p-2 text-red-400 hover:text-[#90c1cb] hover:bg-[#90c1cb]/10 rounded-lg transition-all"
                                                    title="Archive Fleet">
                                                    <span class="material-symbols-outlined">unarchive</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
        <main v-else class="flex w-full min-h-screen">
            <section class="flex-1 shipyard-bg flex items-center justify-center p-10 relative overflow-hidden">
                <div class="absolute inset-0 opacity-10 pointer-events-none">
                    <div class="absolute top-10 left-10"><span
                            class="material-symbols-outlined text-8xl text-white">sailing</span></div>
                    <div class="absolute bottom-20 right-20 rotate-12"><span
                            class="material-symbols-outlined text-[150px] text-white">anchor</span></div>
                    <div class="absolute top-1/4 right-1/4 opacity-20"><span
                            class="material-symbols-outlined text-6xl text-white">waves</span></div>
                </div>
                <div class="max-w-xl w-full text-center flex flex-col items-center relative z-10">
                    <div class="mb-8 relative">
                        <div
                            class="w-48 h-48 bg-primary/5 rounded-full flex items-center justify-center border border-primary/10 backdrop-blur-sm">
                            <span class="material-symbols-outlined text-[120px] text-primary/30 select-none">anchor</span>
                        </div>
                        <div
                            class="absolute -bottom-4 -right-4 w-24 h-24 bg-background-dark flex items-center justify-center rounded-full border border-[#224249]">
                            <span
                                class="material-symbols-outlined text-4xl text-[#90c1cb] opacity-50">directions_boat</span>
                        </div>
                    </div>
                    <h1 class="text-white text-4xl font-bold font-display mb-4 tracking-tight">
                        No fleets commissioned yet...
                    </h1>
                    <p class="text-[#90c1cb] text-xl font-normal mb-10 max-w-md mx-auto leading-relaxed">
                        The shipyard stands quiet. It's time to assemble your crew and prepare for your first voyage!
                    </p>
                    <NuxtLink to="/admin/class/create"
                        class="group flex items-center gap-3 bg-pirate-gold hover:bg-[#d4a21e] text-background-dark px-10 py-5 rounded-xl font-bold text-xl shadow-[0_10px_30px_-5px_rgba(230,179,37,0.3)] transition-all hover:scale-[1.05] active:scale-95">
                        <span
                            class="material-symbols-outlined font-bold group-hover:rotate-12 transition-transform">add_moderator</span>
                        <span>Commission New Fleet</span>
                    </NuxtLink>
                    <div class="mt-12 flex items-center gap-2 text-[#90c1cb] text-sm opacity-60">
                        <span class="material-symbols-outlined text-base">info</span>
                        <span>Fleets allow you to organize students into crews and assign navigators.</span>
                    </div>
                </div>
                <div
                    class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-primary/20 to-transparent">
                </div>
            </section>
        </main>
        <div id="archive-modal" class="fixed inset-0 bg-black/85 backdrop-blur-sm flex items-center justify-center z-50 p-6 hidden">
            <div
                class="bg-background-dark border border-danger/40 rounded-xl max-w-lg w-full shadow-[0_0_50px_-12px_rgba(139,0,0,0.4)] relative overflow-hidden">
                <div class="absolute inset-0 flex items-center justify-center opacity-[0.03] pointer-events-none select-none">
                    <span class="material-symbols-outlined text-[320px] text-white rotate-12">skull</span>
                </div>
                <div class="absolute -top-12 -left-12 opacity-5 pointer-events-none">
                    <span class="material-symbols-outlined text-[180px] text-danger">scuba_diving</span>
                </div>
                <div class="pt-10 pb-4 px-8 text-center relative z-10">
                    <div class="flex justify-center mb-6">
                        <div
                            class="h-16 w-16 bg-danger/10 rounded-full flex items-center justify-center border border-danger/30">
                            <span class="material-symbols-outlined text-danger text-4xl">warning</span>
                        </div>
                    </div>
                    <h2 class="text-white tracking-tight text-3xl font-bold leading-tight font-display">
                        Decommission this Fleet?
                    </h2>
                </div>
                <div class="px-8 pb-8 text-center relative z-10">
                    <p class="text-[#90c1cb] text-lg font-normal leading-relaxed">
                        Lowering the flag? This action will archive the <span class="title text-white font-bold">Heart
                            Pirates Wing</span> and all its current assignments. The crew will be sent to the
                        reserve list until further notice.
                    </p>
                </div>
                <div class="bg-[#182f34] p-8 flex flex-col gap-3 relative z-10">
                    <form>
                        <button
                            class="flex w-full cursor-pointer items-center justify-center rounded-lg h-14 bg-danger text-white gap-3 text-lg font-bold transition-all hover:bg-[#a00000] hover:scale-[1.02] shadow-lg shadow-danger/20">
                            <span class="material-symbols-outlined">inventory_2</span>
                            <span>Decommission</span>
                        </button>
                    </form>
                    <button @click="closeArchiveModal()"
                        class="flex w-full cursor-pointer items-center justify-center rounded-lg h-12 bg-transparent border border-[#315f68] text-[#90c1cb] text-base font-medium hover:bg-[#224249] hover:text-white transition-all">
                        Keep Sailing
                    </button>
                </div>
                <div class="h-1 w-full bg-gradient-to-r from-transparent via-danger to-transparent opacity-50">
                </div>
            </div>
        </div>
        <div id="resurrect-modal" class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center z-50 p-6 hidden">
            <div
                class="bg-background-dark border border-primary/40 rounded-xl max-w-lg w-full shadow-[0_0_50px_-12px_rgba(13,204,242,0.3)] relative overflow-hidden">
                <div class="absolute -top-10 -right-10 opacity-10 pointer-events-none">
                    <span class="material-symbols-outlined text-[200px] text-primary">explore</span>
                </div>
                <div class="pt-10 pb-4 px-8 text-center relative z-10">
                    <div class="flex justify-center mb-6">
                        <div
                            class="h-16 w-16 bg-primary/10 rounded-full flex items-center justify-center border border-primary/20">
                            <span class="material-symbols-outlined text-primary text-4xl">sailing</span>
                        </div>
                    </div>
                    <h2 class="text-white tracking-tight text-3xl font-bold leading-tight font-display">
                        Return Fleet to Service?
                    </h2>
                </div>
                <div class="px-8 pb-8 text-center relative z-10">
                    <p class="text-[#90c1cb] text-lg font-normal leading-relaxed">
                        Are you ready to reactivate this archived class? Bringing this vessel back into the fleet
                        will allow students and instructors to resume their voyage and access all historical logs.
                    </p>
                </div>
                <div class="bg-[#182f34] p-8 flex flex-col gap-3 relative z-10">
                    <form>
                        <button
                            class="flex w-full cursor-pointer items-center justify-center rounded-lg h-14 bg-primary text-background-dark gap-3 text-lg font-bold transition-all hover:bg-primary/90 hover:scale-[1.02] shadow-lg shadow-primary/20">
                            <span class="material-symbols-outlined">directions_boat</span>
                            <span>Set Sail Again</span>
                        </button>
                    </form>
                    <button @click="closeResurrectModal()"
                        class="flex w-full cursor-pointer items-center justify-center rounded-lg h-12 bg-transparent border border-[#315f68] text-[#90c1cb] text-base font-medium hover:bg-[#224249] hover:text-white transition-all">
                        Keep in Port
                    </button>
                </div>
                <div class="h-1 w-full bg-gradient-to-r from-transparent via-primary to-transparent opacity-50">
                </div>
            </div>
        </div>
    </NuxtLayout>
</template>