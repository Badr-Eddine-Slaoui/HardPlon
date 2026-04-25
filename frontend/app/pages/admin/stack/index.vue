<script setup lang="ts">
    import { useStack } from '../../../../stores/stack';

    useHead({
        title: `Admin Dashboard - Tech Stacks`
    })

    const store = useStack();

    const paginate = usePagination(store.fetchStacks.bind(store));

    onMounted(async() => {
        await paginate.fetchData();
    })

    function openArchiveModal(id: number): void {
        const modal = document.getElementById('archive-modal') as HTMLElement;
        modal.classList.remove('hidden');

        const form = modal.querySelector('form') as HTMLFormElement;
        form.onsubmit = async (e) => {
            e.preventDefault();
            await store.archiveStack(id);
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
            await store.restoreStack(id);
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
        <main v-if="(store.stacks?.length as number > 0) || (store.archived_stacks?.length as number > 0)" class="flex h-[calc(100vh-65px)] overflow-hidden w-full">
            <section class="flex-1 overflow-y-auto p-10 custom-scrollbar bg-[url('https://www.transparenttextures.com/patterns/dark-matter.png')]">
                <div class="max-w-7xl mx-auto">
                    <div class="flex justify-between items-end mb-8">
                        <div>
                            <span class="text-primary text-xs font-bold tracking-[0.3em] uppercase mb-2 block">System Infrastructure</span>
                            <h2 class="text-4xl font-bold text-white font-adventure tracking-wider mb-1">Tech Stacks</h2>
                            <p class="text-[#90c1cb]">Manage the technology configurations for project environments.</p>
                        </div>
                        <NuxtLink to="/admin/stack/create"
                            class="flex items-center gap-2 px-6 py-3 bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark font-bold rounded-lg transition-all transform hover:scale-105 shadow-[0_4px_14px_0_rgba(255,215,0,0.39)] uppercase tracking-tighter text-sm">
                            <span class="material-symbols-outlined font-bold">layers</span>
                            <span>Forge New Stack</span>
                        </NuxtLink>
                    </div>
                    <div class="nautical-border bg-[#182f34] overflow-hidden rounded-xl shadow-2xl relative">
                        <div class="absolute top-0 right-0 p-4 opacity-5 pointer-events-none">
                            <span class="material-symbols-outlined text-9xl text-white">database</span>
                        </div>
                        <table class="w-full text-left relative z-10">
                            <thead>
                                <tr class="bg-[#224249]/50 border-b border-[#315f68]">
                                    <th class="px-6 py-4 text-pirate-gold text-xs font-bold uppercase tracking-widest">Stack ID</th>
                                    <th class="px-6 py-4 text-pirate-gold text-xs font-bold uppercase tracking-widest">Stack Name</th>
                                    <th class="px-6 py-4 text-pirate-gold text-xs font-bold uppercase tracking-widest">Description</th>
                                    <th class="px-6 py-4 text-pirate-gold text-xs font-bold uppercase tracking-widest text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#224249]">
                                <template v-for="stack in store.stacks">
                                    <tr v-if="stack.is_active" class="hover:bg-white/5 transition-colors group">
                                        <td class="px-6 py-5">
                                            <span class="text-2xl font-black text-primary/30 font-display group-hover:text-primary/60 transition-colors">#{{ stack.id }}</span>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-3">
                                                <div class="size-10 bg-primary/10 rounded-lg flex items-center justify-center border border-primary/20 group-hover:border-primary/50 transition-all">
                                                    <span class="material-symbols-outlined text-primary">layers</span>
                                                </div>
                                                <span class="text-white font-bold block">{{ stack.name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <p class="text-[#90c1cb] text-sm max-w-md line-clamp-1">{{ stack.description }}</p>
                                        </td>
                                        <td class="px-6 py-5 text-right">
                                            <div class="flex justify-end gap-2">
                                                <NuxtLink :to="`/admin/stack/${stack.id}`"
                                                    class="p-2 text-[#90c1cb] hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                                                    title="View Details">
                                                    <span class="material-symbols-outlined">visibility</span>
                                                </NuxtLink>
                                                <NuxtLink :to="`/admin/stack/edit/${stack.id}`"
                                                    class="p-2 text-[#90c1cb] hover:text-pirate-gold hover:bg-pirate-gold/10 rounded-lg transition-all"
                                                    title="Update Stack">
                                                    <span class="material-symbols-outlined">edit</span>
                                                </NuxtLink>
                                                <button @click="openArchiveModal(stack.id)"
                                                    class="p-2 text-[#90c1cb] hover:text-red-400 hover:bg-red-400/10 rounded-lg transition-all"
                                                    title="Archive Stack">
                                                    <span class="material-symbols-outlined">archive</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-else class="hover:bg-white/5 transition-colors opacity-60 grayscale-[0.5]">
                                        <td class="px-6 py-5">
                                            <span class="text-2xl font-black text-white/20 font-display">#{{ stack.id }}</span>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-3">
                                                <div class="size-10 bg-white/5 rounded-lg flex items-center justify-center border border-white/10">
                                                    <span class="material-symbols-outlined text-white/30">layers</span>
                                                </div>
                                                <span class="text-white/50 font-bold block">{{ stack.name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <p class="text-[#90c1cb]/50 text-sm max-w-md">{{ stack.description }}</p>
                                        </td>
                                        <td class="px-6 py-5 text-right">
                                            <div class="flex justify-end gap-2">
                                                <button @click="openResurrectModal(stack.id)"
                                                    class="p-2 text-red-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                                                    title="Restore Stack">
                                                    <span class="material-symbols-outlined">unarchive</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <!-- Also iterate over archived_stacks explicitly just in case -->
                                <template v-for="stack in store.archived_stacks">
                                    <tr class="hover:bg-white/5 transition-colors opacity-60 grayscale-[0.5]">
                                        <td class="px-6 py-5">
                                            <span class="text-2xl font-black text-white/20 font-display">#{{ stack.id }}</span>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-3">
                                                <div class="size-10 bg-white/5 rounded-lg flex items-center justify-center border border-white/10">
                                                    <span class="material-symbols-outlined text-white/30">layers</span>
                                                </div>
                                                <span class="text-white/50 font-bold block">{{ stack.name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <p class="text-[#90c1cb]/50 text-sm max-w-md">{{ stack.description }}</p>
                                        </td>
                                        <td class="px-6 py-5 text-right">
                                            <div class="flex justify-end gap-2">
                                                <button @click="openResurrectModal(stack.id)"
                                                    class="p-2 text-red-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                                                    title="Restore Stack">
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
        <div v-else class="w-full bg-[url('https://www.transparenttextures.com/patterns/dark-matter.png')]">
            <main class="flex h-[calc(100vh-65px)] overflow-hidden">
                <section class="flex-1 overflow-y-auto p-10 custom-scrollbar flex items-center justify-center">
                    <div class="max-w-xl w-full text-center">
                        <div class="relative mb-8 flex justify-center">
                             <div class="w-80 h-48 relative overflow-hidden rounded-2xl sea-horizon border border-[#224249] bg-[#14262b] flex items-center justify-center shadow-2xl">
                                <div class="absolute inset-0 opacity-10">
                                    <span class="material-symbols-outlined text-[200px] text-white">layers</span>
                                </div>
                                <span class="material-symbols-outlined text-6xl text-pirate-gold animate-pulse">layers</span>
                            </div>
                        </div>
                        <h2 class="text-4xl font-bold text-white font-adventure tracking-wider mb-3">Unforged Stacks</h2>
                        <p class="text-[#90c1cb] text-lg mb-8">No technology stacks defined yet... <br />start forging the tools for your crew!</p>
                        <NuxtLink to="/admin/stack/create"
                            class="inline-flex items-center gap-2 px-10 py-4 bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark font-black rounded-lg transition-all transform hover:scale-105 shadow-[0_4px_20px_0_rgba(255,215,0,0.3)] uppercase tracking-tighter">
                            <span class="material-symbols-outlined font-bold">layers</span>
                            <span>Forge New Stack</span>
                        </NuxtLink>
                    </div>
                </section>
            </main>
        </div>

        <!-- Archive Modal -->
        <div id="archive-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-background-dark/80 backdrop-blur-sm px-4 hidden">
            <div class="relative w-full max-w-md overflow-hidden rounded-xl border border-marine-crimson/50 bg-[#182f34] shadow-[0_0_50px_rgba(139,0,0,0.2)]">
                <div class="absolute inset-0 wave-pattern opacity-20 pointer-events-none"></div>
                <div class="absolute inset-0 flex items-center justify-center opacity-[0.03] pointer-events-none">
                    <div class="size-64 jolly-roger-bg bg-white"></div>
                </div>
                <div class="relative p-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="size-12 flex items-center justify-center rounded-full bg-marine-crimson/20 border border-marine-crimson/40 text-marine-crimson">
                            <span class="material-symbols-outlined text-3xl font-bold">skull</span>
                        </div>
                        <h3 class="text-2xl font-bold text-white font-adventure tracking-wide">Archive Stack?</h3>
                    </div>
                    <div class="mb-8">
                        <p class="text-[#90c1cb] leading-relaxed">
                            By confirming this action, the selected stack will be cast into the <span class="text-primary font-bold">Impel Down Archives</span>.
                        </p>
                        <div class="mt-4 p-3 rounded-lg bg-marine-crimson/10 border border-marine-crimson/20">
                            <p class="text-red-300 text-sm flex items-start gap-2">
                                <span class="material-symbols-outlined text-sm mt-0.5">warning</span>
                                <span>This stack will no longer be available for project deployments or tactical mappings until reactivated.</span>
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-col gap-3">
                        <form>
                            <button class="w-full py-4 bg-marine-crimson hover:bg-marine-crimson-dark text-white font-bold rounded-lg transition-all shadow-lg shadow-black/20 flex items-center justify-center gap-2 uppercase tracking-widest text-xs">
                                <span class="material-symbols-outlined text-lg">archive</span>
                                <span>Archive Stack</span>
                            </button>
                        </form>
                        <button @click="closeArchiveModal()" class="w-full py-4 bg-[#224249] hover:bg-[#315f68] text-white font-bold rounded-lg transition-all border border-[#315f68] uppercase tracking-widest text-xs">Keep Active</button>
                    </div>
                </div>
                <div class="h-1.5 w-full flex">
                    <div class="flex-1 bg-marine-crimson"></div>
                    <div class="flex-1 bg-white"></div>
                    <div class="flex-1 bg-marine-crimson"></div>
                </div>
            </div>
        </div>

        <!-- Restore Modal -->
        <div id="resurrect-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-background-dark/80 backdrop-blur-sm px-4 hidden">
            <div class="relative w-full max-w-lg overflow-hidden rounded-xl border border-[#315f68] bg-[#182f34] shadow-2xl">
                <div class="absolute -right-12 -top-12 opacity-5 pointer-events-none">
                    <span class="material-symbols-outlined text-[240px] text-primary">explore</span>
                </div>
                <div class="p-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="flex size-14 items-center justify-center rounded-full bg-primary/10 border border-primary/30">
                            <span class="material-symbols-outlined text-primary text-3xl">settings_backup_restore</span>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-white font-adventure tracking-wide">Restore Stack?</h3>
                            <p class="text-primary text-sm font-medium uppercase tracking-wider">Infrastructure Restoration Protocol</p>
                        </div>
                    </div>
                    <div class="mb-8">
                        <p class="text-[#90c1cb] leading-relaxed text-lg">
                            Are you sure you want to bring this technology stack back to the <span class="text-white font-semibold">active fleet</span>? This will make the stack available for all project configurations and deployments.
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button @click="closeResurrectModal()" class="flex-1 px-6 py-4 border border-[#315f68] text-[#90c1cb] hover:text-white hover:bg-white/5 font-bold rounded-lg transition-all uppercase tracking-widest text-xs">Stay Archived</button>
                        <form>
                            <button class="flex-1 px-8 py-4 bg-primary hover:brightness-110 text-background-dark font-black rounded-lg transition-all shadow-[0_4px_14px_0_rgba(13,204,242,0.3)] flex items-center justify-center gap-2 uppercase tracking-widest text-xs">
                                <span class="material-symbols-outlined text-xl font-bold">verified</span>
                                Restore Stack
                            </button>
                        </form>
                    </div>
                </div>
                <div class="h-1 w-full bg-gradient-to-r from-transparent via-primary/50 to-transparent opacity-30"></div>
            </div>
        </div>
    </NuxtLayout>
</template>
