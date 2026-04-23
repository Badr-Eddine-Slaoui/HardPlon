<script setup lang="ts">
    import { useSkill } from '../../../../stores/skill';

    useHead({
        title: `Admin Dashboard - Skills`
    })

    const store = useSkill();
    const paginate = usePagination(store.fetchSkills.bind(store));

    onMounted(async() => {
        await paginate.fetchData();
    })

    function openArchiveModal(id: number): void {
        const modal = document.getElementById('archive-modal') as HTMLElement;
        modal.classList.remove('hidden');

        const form = modal.querySelector('form') as HTMLFormElement;
        form.onsubmit = async (e) => {
            e.preventDefault();
            await store.archiveSkill(id);
            closeArchiveModal();
        }
    }

    function closeArchiveModal(): void {
        const modal = document.getElementById('archive-modal') as HTMLElement;
        modal.classList.add('hidden');
    }

    function openResurrectModal(id: number): void {
        const modal = document.getElementById('resurrect-modal') as HTMLElement;
        modal.classList.remove('hidden');

        const form = modal.querySelector('form') as HTMLFormElement;
        form.onsubmit = async (e) => {
            e.preventDefault();
            await store.restoreSkill(id);
            closeResurrectModal();
        }
    }

    function closeResurrectModal(): void {
        const modal = document.getElementById('resurrect-modal') as HTMLElement;
        modal.classList.add('hidden');
    }

    definePageMeta({
        middleware: ['auth', 'admin']
    })
</script>

<template>
    <NuxtLayout name="admin">
        <main v-if="store.skills?.length as number > 0" class="w-full flex h-[calc(100vh-65px)] overflow-hidden">
            <section class="flex-1 overflow-y-auto p-10 custom-scrollbar">
                <div class="max-w-7xl mx-auto">
                    <div class="flex justify-between items-end mb-8">
                        <div>
                            <h2 class="text-3xl font-bold text-white font-display mb-1">Arsenal Overview</h2>
                            <p class="text-[#90c1cb]">Manage all combat abilities and technical skills available to the
                                fleet.</p>
                        </div>
                        <NuxtLink to="/admin/skill/create"
                            class="flex items-center gap-2 px-6 py-3 bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark font-bold rounded-lg transition-all transform hover:scale-105 shadow-[0_4px_14px_0_rgba(255,215,0,0.39)]">
                            <span class="material-symbols-outlined font-bold">hardware</span>
                            <span>Forge New Ability</span>
                        </NuxtLink>
                    </div>
                    <div class="rounded-xl border border-[#315f68] bg-[#182f34] overflow-hidden shadow-2xl">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-[#224249]/50 border-b border-[#315f68]">
                                    <th class="px-6 py-4 text-white text-xs font-bold uppercase tracking-wider">Ability Name
                                    </th>
                                    <th class="px-6 py-4 text-white text-xs font-bold uppercase tracking-wider">Associated
                                        Formations</th>
                                    <th class="px-6 py-4 text-white text-xs font-bold uppercase tracking-wider">Description
                                    </th>
                                    <th class="px-6 py-4 text-white text-xs font-bold uppercase tracking-wider">Assigned
                                        Levels</th>
                                    <th class="px-6 py-4 text-white text-xs font-bold uppercase tracking-wider text-right">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#224249]">
                                <template v-for="skill in store.skills">
                                    <tr v-if="skill.is_active" class="hover:bg-white/5 transition-colors">
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="size-16 bg-primary/10 rounded-lg flex items-center justify-center border border-primary/20">
                                                    <span class="text-primary">{{ skill.code }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-white font-bold block w-[100px] truncate">{{ skill.title }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="flex flex-wrap gap-1">
                                                <span
                                                    class="px-2 py-0.5 bg-[#224249] text-[#90c1cb] text-xs rounded border border-[#315f68] w-[100px] truncate">{{ skill.formation?.title }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="flex flex-wrap gap-1">
                                                <p class="w-[200px] truncate">{{ skill.description }}</p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="flex gap-2">
                                                <span v-for="skill_level in skill.skill_levels"
                                                    class="px-2 py-1 bg-blue-500/10 text-blue-400 text-[10px] font-bold uppercase rounded border border-blue-500/20">{{ skill_level.level.name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 text-right">
                                            <div class="flex justify-end gap-2">
                                                <NuxtLink :to="`/admin/skill/${skill.id}`"
                                                    class="p-2 text-[#90c1cb] hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                                                    title="View Details">
                                                    <span class="material-symbols-outlined">visibility</span>
                                                </NuxtLink>
                                                <NuxtLink :to="`/admin/skill/edit/${skill.id}`"
                                                    class="p-2 text-[#90c1cb] hover:text-pirate-gold hover:bg-pirate-gold/10 rounded-lg transition-all"
                                                    title="Edit Ability">
                                                    <span class="material-symbols-outlined">edit</span>
                                                </NuxtLink>
                                                <button @click="openArchiveModal(skill.id)"
                                                    class="p-2 text-[#90c1cb] hover:text-red-400 hover:bg-red-400/10 rounded-lg transition-all"
                                                    title="Archive Ability">
                                                    <span class="material-symbols-outlined">archive</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-else class="hover:bg-white/5 transition-colors opacity-55">
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="size-16 bg-primary/10 rounded-lg flex items-center justify-center border border-primary/20">
                                                    <span class="text-primary">{{ skill.code }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-white font-bold block w-[100px] truncate">{{ skill.title }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="flex flex-wrap gap-1">
                                                <span
                                                    class="px-2 py-0.5 bg-[#224249] text-[#90c1cb] text-xs rounded border border-[#315f68] w-[100px] truncate">{{ skill.formation?.title }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="flex flex-wrap gap-1">
                                                <p class="w-[200px] truncate">{{ skill.description }}</p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="flex gap-2">
                                                <span v-for="skill_level in skill.skill_levels"
                                                    class="px-2 py-1 bg-blue-500/10 text-blue-400 text-[10px] font-bold uppercase rounded border border-blue-500/20">{{ skill_level.level.name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 text-right">
                                            <div class="flex justify-end gap-2">
                                                <button @click="openResurrectModal(skill.id)"
                                                    class="p-2 text-red-400 hover:text-[#90c1cb] hover:bg-[#90c1cb]/10 rounded-lg transition-all"
                                                    title="Archive Ability">
                                                    <span class="material-symbols-outlined">unarchive</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Controls -->
                    <Pagination :meta="store.meta" :per_page="paginate.perPage.value" @change="paginate.fetchData"
                        @perPage="paginate.changePerPage" />
                </div>
            </section>
        </main>
        <main v-else class="w-full flex h-[calc(100vh-65px)] overflow-hidden">
            <section
                class="flex-1 overflow-y-auto p-10 custom-scrollbar flex items-center justify-center bg-background-dark/50">
                <div class="max-w-2xl w-full text-center">
                    <div class="relative inline-block mb-10">
                        <div class="relative w-80 h-64 mx-auto flex items-end justify-center">
                            <div class="absolute inset-x-0 bottom-0 h-4 bg-[#224249] rounded-full opacity-50"></div>
                            <div
                                class="absolute left-8 bottom-4 w-4 h-56 bg-gradient-to-t from-[#315f68] to-[#182f34] border-x border-[#315f68]/30 rounded-t-lg">
                            </div>
                            <div
                                class="absolute right-8 bottom-4 w-4 h-56 bg-gradient-to-t from-[#315f68] to-[#182f34] border-x border-[#315f68]/30 rounded-t-lg">
                            </div>
                            <div
                                class="absolute left-4 right-4 bottom-16 h-3 bg-[#224249] border-y border-[#315f68]/30 rounded-full">
                            </div>
                            <div
                                class="absolute left-4 right-4 bottom-32 h-3 bg-[#224249] border-y border-[#315f68]/30 rounded-full">
                            </div>
                            <div
                                class="absolute left-4 right-4 bottom-48 h-3 bg-[#224249] border-y border-[#315f68]/30 rounded-full">
                            </div>
                            <div class="absolute top-4 left-4 text-[#90c1cb]/30">
                                <span class="material-symbols-outlined text-6xl rotate-12">filter_vintage</span>
                            </div>
                            <div class="absolute top-16 right-6 text-[#90c1cb]/20">
                                <span class="material-symbols-outlined text-4xl -rotate-45">filter_vintage</span>
                            </div>
                            <div class="mb-12">
                                <span class="material-symbols-outlined text-8xl text-[#224249]">inventory_2</span>
                            </div>
                        </div>
                        <div
                            class="absolute top-0 left-1/2 -translate-x-1/2 w-px h-32 bg-gradient-to-b from-transparent via-[#90c1cb]/20 to-transparent">
                        </div>
                    </div>
                    <h2 class="text-3xl font-bold text-white font-display mb-4">The armory is empty...</h2>
                    <p class="text-[#90c1cb] text-lg max-w-md mx-auto mb-10">
                        No abilities have been forged for the fleet. Every great pirate starts with a single technique.
                    </p>
                    <div class="flex flex-col items-center gap-4">
                        <NuxtLink to="/admin/skill/create"
                            class="group flex items-center gap-3 px-10 py-4 bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark font-black rounded-lg transition-all transform hover:scale-105 shadow-[0_8px_30px_0_rgba(255,215,0,0.3)] text-xl tracking-tight uppercase">
                            <span class="material-symbols-outlined font-black">humerus</span>
                            <span>Forge New Ability</span>
                        </NuxtLink>
                    </div>
                </div>
            </section>
        </main>
        <div id="archive-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm px-4 hidden">
            <div
                class="relative w-full max-w-lg bg-[#182f34] border-2 border-[#315f68] rounded-xl shadow-[0_0_50px_rgba(0,0,0,0.5)] overflow-hidden">
                <div class="absolute top-4 right-4 opacity-5 pointer-events-none transform rotate-12 scale-150">
                    <span class="material-symbols-outlined text-[160px] text-white">skull</span>
                </div>
                <div class="p-8 relative z-10">
                    <div class="flex items-center gap-4 mb-6">
                        <div
                            class="size-14 rounded-full bg-marine-crimson/20 border border-marine-crimson/50 flex items-center justify-center">
                            <span class="material-symbols-outlined text-marine-crimson text-3xl">priority_high</span>
                        </div>
                        <h3 class="text-3xl font-bold text-white font-display tracking-tight">Seal this Ability?</h3>
                    </div>
                    <div class="space-y-4 mb-10">
                        <p class="text-[#90c1cb] text-lg leading-relaxed">
                            Admiral, you are about to archive this skill rank. This action will move the ability to the
                            <span class="text-pirate-gold font-semibold">Forbidden Archives</span>.
                        </p>
                        <div class="bg-black/20 border-l-4 border-marine-crimson p-4 rounded-r-lg">
                            <p class="text-white text-sm">
                                <span class="font-bold text-marine-crimson uppercase tracking-wider block mb-1">Warning</span>
                                This rank will be immediately removed from all active naval briefs and training registries.
                                It can only be restored through the Fleet Command console.
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <form>
                            <button
                                class="flex-1 px-6 py-4 bg-marine-crimson hover:bg-marine-crimson-dark text-white font-bold rounded-lg transition-all transform hover:scale-[1.02] shadow-lg flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-xl">lock</span>
                                Seal Away
                            </button>
                        </form>
                        <button @click="closeArchiveModal()"
                            class="flex-1 px-6 py-4 bg-[#224249] hover:bg-[#315f68] text-white font-bold rounded-lg transition-all transform hover:scale-[1.02] border border-[#315f68] flex items-center justify-center gap-2">
                            Keep in Arsenal
                        </button>
                    </div>
                </div>
                <div class="absolute top-0 left-0 w-2 h-2 border-t-2 border-l-2 border-pirate-gold/30"></div>
                <div class="absolute top-0 right-0 w-2 h-2 border-t-2 border-r-2 border-pirate-gold/30"></div>
                <div class="absolute bottom-0 left-0 w-2 h-2 border-b-2 border-l-2 border-pirate-gold/30"></div>
                <div class="absolute bottom-0 right-0 w-2 h-2 border-b-2 border-r-2 border-pirate-gold/30"></div>
            </div>
        </div>
        <div id="resurrect-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-background-dark/80 backdrop-blur-sm hidden">
            <div
                class="relative w-full max-w-lg bg-[#182f34] border border-[#315f68] rounded-xl shadow-[0_0_50px_rgba(13,204,242,0.15)] overflow-hidden">
                <div class="absolute -top-12 -right-12 opacity-5 pointer-events-none">
                    <span class="material-symbols-outlined text-[12rem] text-primary select-none">explore</span>
                </div>
                <div class="p-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div
                            class="size-12 bg-primary/10 rounded-full flex items-center justify-center border border-primary/30">
                            <span class="material-symbols-outlined text-primary text-3xl">history_edu</span>
                        </div>
                        <h3 class="text-2xl font-bold text-white font-display">Unseal this Ability?</h3>
                    </div>
                    <div class="space-y-4 mb-8">
                        <p class="text-[#90c1cb] text-lg leading-relaxed">
                            This skill has been locked away in the forbidden archives. Do you wish to bring this ability
                            back into the <span class="text-primary font-bold">active arsenal</span>?
                        </p>
                        <div class="p-4 bg-background-dark/50 border border-[#224249] rounded-lg flex items-center gap-3">
                            <span class="material-symbols-outlined text-pirate-gold">info</span>
                            <span class="text-sm text-[#90c1cb]">Restoring will make this skill visible to all active
                                marines for training.</span>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <form>
                            <button
                                class="flex-1 px-6 py-3 bg-ocean-turquoise hover:bg-ocean-turquoise/90 text-background-dark font-bold rounded-lg transition-all transform hover:scale-[1.02] active:scale-95 shadow-[0_4px_14px_0_rgba(13,204,242,0.3)]">
                                Release Ability
                            </button>
                        </form>
                        <button @click="closeResurrectModal()"
                            class="flex-1 px-6 py-3 bg-[#224249] hover:bg-[#315f68] text-white font-bold rounded-lg transition-all">
                            Keep Sealed
                        </button>
                    </div>
                </div>
                <div class="h-1 w-full bg-gradient-to-r from-transparent via-primary/50 to-transparent"></div>
            </div>
        </div>
    </NuxtLayout>
</template>