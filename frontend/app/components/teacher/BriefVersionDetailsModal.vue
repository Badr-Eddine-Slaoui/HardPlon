<script setup lang="ts">
    import type { BriefVersion, BrowserTest, FunctionTest, HttpTest } from '~~/types/brief_version';

    const props = defineProps<{
        version: BriefVersion | null;
        isOpen: boolean;
    }>();

    const emit = defineEmits(['close']);

    const close = () => {
        emit('close');
    };
</script>

<template>
    <div v-if="isOpen && version" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/80 backdrop-blur-md overflow-y-auto">
        <div class="bg-[#182f34] border border-pirate-gold/30 rounded-2xl w-full max-w-4xl shadow-2xl flex flex-col max-h-[90vh]">
            <div class="p-6 border-b border-pirate-gold/20 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-lg bg-pirate-gold/10 flex items-center justify-center text-pirate-gold">
                        <span class="material-symbols-outlined">visibility</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold adventure-title text-pirate-gold">{{ version.version }}</h3>
                        <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Deployed {{ new Date(version.created_at).toLocaleDateString() }}</p>
                    </div>
                </div>
                <button @click="close" class="text-slate-400 hover:text-white transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-8 space-y-10">
                <!-- Architecture Rules -->
                <section>
                    <h4 class="text-xs font-black text-primary uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">architecture</span> Architecture Rules
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div v-for="type in ['required', 'optional', 'forbidden']" :key="type" class="space-y-3">
                            <h5 class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ type }} Files</h5>
                            <div class="flex flex-wrap gap-2">
                                <span v-for="file in version.architecture_rules?.[type as 'required' | 'optional' | 'forbidden']" :key="file"
                                    class="px-2 py-1 bg-slate-800/50 border border-slate-700 rounded text-[10px] text-slate-300">
                                    {{ file }}
                                </span>
                                <p v-if="!version.architecture_rules?.[type as 'required' | 'optional' | 'forbidden']?.length" class="text-[10px] text-slate-600 italic">None</p>
                            </div>
                        </div>
                    </div>

                    <div v-if="version.architecture_rules?.patterns?.length" class="mt-8 pt-6 border-t border-[#224249]">
                        <h5 class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-4">Content Patterns</h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div v-for="(p, i) in version.architecture_rules.patterns" :key="i"
                                class="p-3 bg-background-dark/30 border border-[#224249] rounded-xl flex items-center gap-4 text-xs">
                                <span class="font-bold text-primary px-2 py-0.5 bg-primary/10 rounded">{{ p.type }}</span>
                                <div class="flex-1 overflow-hidden">
                                    <p class="text-slate-400 truncate font-mono">{{ p.path }}</p>
                                    <p class="text-pirate-gold italic truncate">"{{ p.value }}"</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Test Configuration -->
                <section>
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="text-xs font-black text-primary uppercase tracking-[0.2em] flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">science</span> Test Suite
                        </h4>
                        <div class="flex items-center gap-4">
                            <span class="text-[10px] font-bold px-2 py-1 rounded bg-slate-800 text-slate-400">TYPE: {{ version.test_config?.type }}</span>
                            <span class="text-[10px] font-bold px-2 py-1 rounded bg-slate-800 text-slate-400">TIMEOUT: {{ version.test_config?.timeout_seconds }}s</span>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div v-for="(test, i) in version.test_config?.tests" :key="i" 
                            class="p-6 bg-background-dark/20 border border-[#224249] rounded-2xl">
                            <div class="flex items-center justify-between mb-4">
                                <h5 class="font-bold text-white flex items-center gap-2">
                                    <span class="size-6 rounded bg-primary/10 text-primary flex items-center justify-center text-[10px]">{{ i + 1 }}</span>
                                    {{ test.name }}
                                </h5>
                                <span v-if="(test as HttpTest).method" class="text-[10px] font-black text-pirate-gold px-2 py-0.5 border border-pirate-gold/30 rounded">{{ (test as HttpTest).method }}</span>
                            </div>

                            <!-- HTTP Details -->
                            <div v-if="version.test_config?.type === 'http'" class="space-y-4">
                                <p class="text-xs text-slate-400 font-mono bg-background-dark p-2 rounded">{{ (test as HttpTest).url }}</p>
                                <div class="grid grid-cols-2 gap-6">
                                    <div v-if="(test as HttpTest).headers?.length">
                                        <p class="text-[9px] font-black text-slate-500 uppercase mb-2">Headers</p>
                                        <div class="space-y-1">
                                            <div v-for="h in (test as HttpTest).headers" :key="h.name" class="text-[10px] flex gap-2">
                                                <span class="text-primary">{{ h.name }}:</span>
                                                <span class="text-slate-300">{{ h.value }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-[9px] font-black text-slate-500 uppercase mb-2">Expectation</p>
                                        <div class="text-[10px] space-y-2">
                                            <p><span class="text-slate-500">Status:</span> <span class="text-emerald-500 font-bold">{{ (test as HttpTest).expected?.status }}</span></p>
                                            <div v-if="(test as HttpTest).expected?.json_contains?.length">
                                                <p class="text-slate-500 mb-1">JSON Contains:</p>
                                                <div class="flex flex-wrap gap-1">
                                                    <span v-for="jc in (test as HttpTest).expected.json_contains" :key="jc" class="px-1.5 py-0.5 bg-slate-800 rounded border border-slate-700 text-slate-300 text-[9px]">{{ jc }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="['POST', 'PUT', 'PATCH'].includes((test as HttpTest).method) && (test as HttpTest).body" class="mt-4 pt-4 border-t border-[#224249]">
                                    <p class="text-[9px] font-black text-slate-500 uppercase mb-2">Request Body</p>
                                    <pre class="text-[10px] text-pirate-gold font-mono bg-background-dark/50 p-3 rounded-lg overflow-x-auto whitespace-pre-wrap">{{ (test as HttpTest).body }}</pre>
                                </div>
                            </div>

                            <!-- Browser Details -->
                            <div v-if="version.test_config?.type === 'browser'" class="space-y-6">
                                <div>
                                    <p class="text-[9px] font-black text-slate-500 uppercase mb-3">Workflow Steps</p>
                                    <div class="space-y-2">
                                        <div v-for="(step, si) in (test as BrowserTest).steps" :key="si" class="flex items-center gap-3 text-[10px] p-2 bg-background-dark/50 rounded-lg border border-[#224249]">
                                            <span class="text-primary font-bold uppercase w-12">{{ step.action }}</span>
                                            <span class="text-slate-300 font-mono truncate">{{ step.url || step.selector }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4 border-t border-[#224249] pt-4">
                                    <div>
                                        <p class="text-[9px] font-black text-slate-500 uppercase mb-1">Expected Title</p>
                                        <p class="text-[10px] text-emerald-500 font-medium">"{{ (test as BrowserTest).expected?.title }}"</p>
                                    </div>
                                    <div>
                                        <p class="text-[9px] font-black text-slate-500 uppercase mb-1">Expected Text</p>
                                        <p class="text-[10px] text-emerald-500 font-medium">"{{ (test as BrowserTest).expected?.text }}"</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Function Details -->
                            <div v-if="version.test_config?.type === 'function'" class="space-y-6">
                                <div class="grid grid-cols-2 gap-6">
                                    <div>
                                        <p class="text-[9px] font-black text-slate-500 uppercase mb-2">Input Parameters</p>
                                        <div class="flex flex-wrap gap-2">
                                            <span v-for="inp in (test as FunctionTest).input" :key="inp" class="px-2 py-1 bg-slate-800 rounded font-mono text-[10px] text-slate-300">{{ inp }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-[9px] font-black text-slate-500 uppercase mb-2">Expected Returns</p>
                                        <div class="flex flex-wrap gap-2">
                                            <span v-for="out in (test as FunctionTest).expected_output" :key="out" class="px-2 py-1 bg-emerald-500/10 border border-emerald-500/20 rounded font-mono text-[10px] text-emerald-500">{{ out }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="p-6 border-t border-pirate-gold/20 bg-background-dark/30 flex justify-end shrink-0">
                <button @click="close" class="px-8 py-3 rounded-xl bg-slate-800 text-slate-300 font-bold hover:bg-slate-700 transition-all">
                    Close Intelligence Report
                </button>
            </div>
        </div>
    </div>
</template>
