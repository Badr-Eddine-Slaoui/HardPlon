<script setup lang="ts">
    import { useLanguage } from '~~/stores/language';

    useHead({
        title: `Admin Dashboard - Sea Tongue Details`
    })

    const store = useLanguage();
    const route = useRoute();

    onMounted(async () => {
        const id = parseInt(route.params.id as string);
        await store.fetchLanguage(id);
    })

    definePageMeta({
        middleware: ['auth', 'admin']
    })
</script>

<template>
    <NuxtLayout name="admin">
        <main class="flex h-[calc(100vh-65px)] overflow-hidden w-full">
            <section class="flex-1 overflow-y-auto p-10 custom-scrollbar">
                <div v-if="store.language" class="max-w-4xl mx-auto">
                    <div class="mb-10 flex items-end justify-between">
                        <div>
                            <NuxtLink to="/admin/language" class="text-primary hover:text-primary/80 flex items-center gap-2 mb-4 transition-colors">
                                <span class="material-symbols-outlined">arrow_back</span>
                                <span class="text-xs font-bold uppercase tracking-widest">Back to Tongues</span>
                            </NuxtLink>
                            <h2 class="text-4xl font-bold text-white font-display mb-1">{{ store.language.name }}</h2>
                            <div class="flex items-center gap-3 mt-2">
                                <span v-if="!store.language.deleted_at" class="px-2 py-0.5 bg-emerald-500/10 border border-emerald-500/30 text-emerald-500 text-[10px] font-black uppercase tracking-widest rounded">Active Dialect</span>
                                <span v-else class="px-2 py-0.5 bg-red-500/10 border border-red-500/30 text-red-500 text-[10px] font-black uppercase tracking-widest rounded">Silenced Tongue</span>
                                <span class="text-slate-500 text-xs font-mono tracking-tighter">ID: {{ store.language.id }}</span>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <NuxtLink :to="`/admin/language/edit/${store.language.id}`"
                                class="flex items-center gap-2 px-6 py-3 bg-[#224249] hover:bg-[#315f68] text-white font-bold rounded-lg transition-all border border-[#315f68]">
                                <span class="material-symbols-outlined">edit</span>
                                <span>Modify Dialect</span>
                            </NuxtLink>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Main Specs -->
                        <div class="lg:col-span-2 space-y-8">
                            <div class="rounded-2xl border border-[#315f68] bg-[#182f34] p-8 shadow-2xl relative overflow-hidden">
                                <div class="absolute top-0 right-0 p-8 opacity-5 pointer-events-none">
                                    <span class="material-symbols-outlined text-[120px] text-primary">terminal</span>
                                </div>
                                <h3 class="text-xs font-bold text-white uppercase tracking-widest mb-8 flex items-center gap-2">
                                    <span class="size-2 bg-primary rounded-full animate-pulse"></span>
                                    Core Specifications
                                </h3>

                                <div class="space-y-6">
                                    <div>
                                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Docker Vessel (Image)</label>
                                        <div class="bg-background-dark/50 border border-[#224249] rounded-xl p-4 font-mono text-primary flex items-center justify-between">
                                            <span>{{ store.language.docker_image }}</span>
                                            <span class="material-symbols-outlined text-sm opacity-30">token</span>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div v-if="store.language.compile_command">
                                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Compile Sequence</label>
                                            <div class="bg-background-dark/50 border border-[#224249] rounded-xl p-4 font-mono text-[#90c1cb] text-sm">
                                                {{ store.language.compile_command }}
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Execution Protocol</label>
                                            <div class="bg-background-dark/50 border border-[#224249] rounded-xl p-4 font-mono text-emerald-400 text-sm">
                                                {{ store.language.run_command }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Problems Using This Tongue -->
                            <div class="rounded-2xl border border-[#315f68] bg-[#182f34] p-8 shadow-2xl">
                                <h3 class="text-xs font-bold text-white uppercase tracking-widest mb-6 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm text-[#90c1cb]">assignment</span>
                                    Bound Challenges
                                </h3>
                                <div class="bg-background-dark/30 rounded-xl border border-[#224249] p-10 text-center">
                                    <span class="material-symbols-outlined text-4xl text-slate-700 mb-3">inventory_2</span>
                                    <p class="text-slate-500 text-sm">No problems are currently bound to this dialect.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Side Meta -->
                        <div class="space-y-8">
                            <div class="rounded-2xl border border-[#315f68] bg-[#182f34] p-6 shadow-2xl">
                                <h3 class="text-xs font-bold text-white uppercase tracking-widest mb-6">Dialect Lifecycle</h3>
                                <div class="space-y-4">
                                    <div class="flex items-start gap-3">
                                        <div class="size-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary mt-0.5">
                                            <span class="material-symbols-outlined text-sm">history</span>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Forged At</p>
                                            <p class="text-white text-xs mt-0.5">{{ new Date(store.language.created_at).toLocaleDateString() }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <div class="size-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary mt-0.5">
                                            <span class="material-symbols-outlined text-sm">update</span>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Last Refined</p>
                                            <p class="text-white text-xs mt-0.5">{{ new Date(store.language.updated_at).toLocaleDateString() }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-marine-crimson/30 bg-marine-crimson/5 p-6 shadow-2xl overflow-hidden relative group">
                                <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:opacity-[0.07] transition-opacity duration-700">
                                    <span class="material-symbols-outlined text-9xl">skull</span>
                                </div>
                                <h3 class="text-xs font-bold text-red-400 uppercase tracking-widest mb-6">Danger Zone</h3>
                                <p class="text-xs text-red-300/60 leading-relaxed mb-6">Archive this tongue to prevent any further use in problems or execution cycles.</p>
                                <button v-if="!store.language.deleted_at" class="w-full py-3 bg-red-500/10 hover:bg-red-500/20 text-red-500 border border-red-500/30 rounded-lg text-xs font-black uppercase tracking-widest transition-all">
                                    Silence Tongue
                                </button>
                                <button v-else class="w-full py-3 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-500 border border-emerald-500/30 rounded-lg text-xs font-black uppercase tracking-widest transition-all">
                                    Resurrect Tongue
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </NuxtLayout>
</template>
