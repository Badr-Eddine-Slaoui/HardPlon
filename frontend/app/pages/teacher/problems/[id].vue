<script setup lang="ts">
    import { useProblem } from '~~/stores/problem';
    import { useProblemTestCase } from '~~/stores/problem_test_case';

    useHead({
        title: `Teacher Dashboard - Challenge Intelligence`
    })

    const problemStore = useProblem();
    const testCaseStore = useProblemTestCase();
    const route = useRoute();

    const isLoading = ref(true);
    const showTestCaseModal = ref(false);
    const viewTestCaseModal = ref(false);
    const selectedTestCase = ref<any>(null);

    const testCaseForm = reactive({
        problem_id: 0,
        input: [''],
        expected_output: [''],
        is_hidden: false
    })

    const errors = ref<any>({});

    onMounted(async () => {
        const id = Number(route.params.id);
        await problemStore.fetchProblem(id);
        if (problemStore.problem) {
            testCaseForm.problem_id = problemStore.problem.id;
        }
        isLoading.value = false;
    });

    function addInput() {
        testCaseForm.input.push('');
    }

    function removeInput(index: number) {
        testCaseForm.input.splice(index, 1);
    }

    function addOutput() {
        testCaseForm.expected_output.push('');
    }

    function removeOutput(index: number) {
        testCaseForm.expected_output.splice(index, 1);
    }

    async function submitTestCase() {
        const res = await testCaseStore.createTestCase(testCaseForm);
        if (res.success) {
            await problemStore.fetchProblem(Number(route.params.id));
            closeModal();
        } else {
            errors.value = res.errors || {};
        }
    }

    function closeModal() {
        showTestCaseModal.value = false;
        viewTestCaseModal.value = false;
        testCaseForm.input = [''];
        testCaseForm.expected_output = [''];
        testCaseForm.is_hidden = false;
        errors.value = {};
    }

    function viewTestCase(tc: any) {
        selectedTestCase.value = tc;
        viewTestCaseModal.value = true;
    }

    async function archiveTestCase(id: number) {
        if (confirm('Are you sure you want to archive this test case?')) {
            await testCaseStore.archiveTestCase(id);
            await problemStore.fetchProblem(Number(route.params.id));
        }
    }

    definePageMeta({
        middleware: ['auth', 'teacher']
    })
</script>

<template>
    <NuxtLayout name="teacher">
        <main v-if="!isLoading" class="flex-1 overflow-y-auto p-10 custom-scrollbar">
            <div class="max-w-6xl mx-auto space-y-10">
                <!-- Header -->
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div>
                        <NuxtLink to="/teacher/problems" class="text-primary hover:text-primary/80 flex items-center gap-2 mb-4 transition-colors">
                            <span class="material-symbols-outlined">arrow_back</span>
                            <span class="text-xs font-bold uppercase tracking-widest">Back to Logbook</span>
                        </NuxtLink>
                        <div class="flex items-center gap-4 mb-2">
                            <h2 class="text-4xl font-black text-white adventure-title tracking-wide">{{ problemStore.problem?.title }}</h2>
                            <span class="px-3 py-1 bg-primary/10 text-primary text-[10px] font-black rounded-full border border-primary/20 uppercase tracking-widest">
                                Rank {{ problemStore.problem?.order_index }}
                            </span>
                        </div>
                        <p class="text-[#90c1cb] italic">Mission: {{ problemStore.problem?.brief?.title }} | Language: {{ problemStore.problem?.language?.name }}</p>
                    </div>
                    <div class="flex gap-3">
                        <NuxtLink :to="`/teacher/problems/edit/${problemStore.problem?.id}`" class="px-6 py-3 bg-[#224249] hover:bg-[#2c535d] text-primary font-bold rounded-lg transition-all flex items-center gap-2 border border-[#315f68]">
                            <span class="material-symbols-outlined text-lg">edit</span>
                            Refine Challenge
                        </NuxtLink>
                        <button @click="showTestCaseModal = true" class="px-6 py-3 bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark font-black rounded-lg transition-all shadow-lg shadow-pirate-gold/20 flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">add_task</span>
                            Forge Test Case
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                    <!-- Problem Info -->
                    <div class="lg:col-span-2 space-y-10">
                        <!-- Description -->
                        <div class="rounded-2xl border border-[#315f68] bg-[#182f34] p-8 shadow-xl parchment-effect relative overflow-hidden">
                            <div class="absolute top-0 right-0 p-8 opacity-5 pointer-events-none">
                                <span class="material-symbols-outlined text-[100px] text-primary">description</span>
                            </div>
                            <h3 class="text-[10px] font-black text-primary uppercase tracking-widest mb-4 flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">description</span>
                                Challenge Description
                            </h3>
                            <div class="text-slate-300 leading-relaxed whitespace-pre-wrap">
                                {{ problemStore.problem?.description }}
                            </div>
                        </div>

                        <!-- Code Layout -->
                        <div class="space-y-6">
                            <h3 class="text-[10px] font-black text-primary uppercase tracking-widest flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">terminal</span>
                                Logical Blueprint
                            </h3>
                            
                            <div class="grid grid-cols-1 gap-6">
                                <!-- Skeleton -->
                                <div class="rounded-xl border border-[#224249] bg-[#14262b] overflow-hidden">
                                    <div class="bg-[#182f34] px-4 py-2 border-b border-[#224249] flex items-center justify-between">
                                        <span class="text-[10px] font-bold text-[#5a8b95] uppercase tracking-widest">Skeleton Code (Visible)</span>
                                        <div class="flex gap-1.5">
                                            <div class="size-2 rounded-full bg-red-500/20"></div>
                                            <div class="size-2 rounded-full bg-yellow-500/20"></div>
                                            <div class="size-2 rounded-full bg-green-500/20"></div>
                                        </div>
                                    </div>
                                    <pre class="p-4 text-xs font-mono text-[#90c1cb] overflow-x-auto"><code>{{ problemStore.problem?.skeleton_code || '// No skeleton code provided.' }}</code></pre>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Header -->
                                    <div class="rounded-xl border border-[#224249] bg-[#14262b] overflow-hidden opacity-80">
                                        <div class="bg-[#182f34] px-4 py-2 border-b border-[#224249]">
                                            <span class="text-[10px] font-bold text-[#5a8b95] uppercase tracking-widest">Hidden Header</span>
                                        </div>
                                        <pre class="p-4 text-[10px] font-mono text-slate-500 overflow-x-auto italic"><code>{{ problemStore.problem?.hidden_header || '// Empty' }}</code></pre>
                                    </div>
                                    <!-- Footer -->
                                    <div class="rounded-xl border border-[#224249] bg-[#14262b] overflow-hidden opacity-80">
                                        <div class="bg-[#182f34] px-4 py-2 border-b border-[#224249]">
                                            <span class="text-[10px] font-bold text-[#5a8b95] uppercase tracking-widest">Hidden Footer</span>
                                        </div>
                                        <pre class="p-4 text-[10px] font-mono text-slate-500 overflow-x-auto italic"><code>{{ problemStore.problem?.hidden_footer || '// Empty' }}</code></pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Test Cases List -->
                    <div class="space-y-6">
                        <h3 class="text-[10px] font-black text-primary uppercase tracking-widest flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">verified</span>
                            Trials (Test Cases)
                        </h3>
                        
                        <div v-if="problemStore.problem?.test_cases?.length" class="space-y-4">
                            <div v-for="(tc, index) in problemStore.problem.test_cases" :key="tc.id" class="rounded-xl border border-[#224249] bg-[#182f34] p-4 flex items-center justify-between group hover:border-primary/30 transition-all shadow-lg hover:shadow-primary/5">
                                <div class="flex items-center gap-4">
                                    <div class="size-10 rounded-lg bg-primary/10 border border-primary/20 flex items-center justify-center text-primary font-black text-xs">
                                        #{{ index + 1 }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-bold text-white">Trial {{ index + 1 }}</p>
                                            <span v-if="tc.is_hidden" class="px-1.5 py-0.5 bg-slate-500/10 text-slate-400 text-[8px] font-black rounded border border-slate-500/20 uppercase">Hidden</span>
                                        </div>
                                        <p class="text-[10px] text-slate-500 font-medium">{{ tc.input?.length }} Input{{ tc.input?.length > 1 ? 's' : '' }} | {{ tc.expected_output?.length }} Output{{ tc.expected_output?.length > 1 ? 's' : '' }}</p>
                                    </div>
                                </div>
                                <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                    <button @click="viewTestCase(tc)" class="size-8 rounded-lg bg-[#224249] hover:bg-primary/20 text-slate-400 hover:text-primary flex items-center justify-center transition-all">
                                        <span class="material-symbols-outlined text-lg">visibility</span>
                                    </button>
                                    <button @click="archiveTestCase(tc.id)" class="size-8 rounded-lg bg-[#224249] hover:bg-red-500/20 text-slate-400 hover:text-red-500 flex items-center justify-center transition-all">
                                        <span class="material-symbols-outlined text-lg">archive</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div v-else class="rounded-xl border border-[#224249] border-dashed p-10 text-center space-y-4">
                            <span class="material-symbols-outlined text-4xl text-[#224249]">task_alt</span>
                            <p class="text-xs text-[#5a8b95] italic font-medium">No trials forged for this challenge yet.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Create Test Case Modal -->
            <div v-if="showTestCaseModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
                <div class="bg-[#182f34] border-2 border-pirate-gold/30 rounded-2xl w-full max-w-2xl shadow-[0_20px_50px_rgba(0,0,0,0.5)] overflow-hidden flex flex-col max-h-[90vh]">
                    <div class="bg-background-dark p-6 border-b border-pirate-gold/20 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="size-12 rounded-full bg-pirate-gold/10 border border-pirate-gold/30 flex items-center justify-center text-pirate-gold">
                                <span class="material-symbols-outlined text-3xl">add_task</span>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold adventure-title text-pirate-gold tracking-wide">Forge Trial</h3>
                                <p class="text-[10px] uppercase tracking-[0.2em] text-slate-400 font-bold">New Test Case</p>
                            </div>
                        </div>
                        <button @click="closeModal" class="text-slate-500 hover:text-white transition-colors">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <div class="p-8 overflow-y-auto space-y-8 custom-scrollbar flex-1">
                        <!-- Inputs -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="text-[10px] font-black text-primary uppercase tracking-widest">Inputs (One per line if needed)</label>
                                <button @click="addInput" class="text-xs font-bold text-primary hover:text-white transition-colors flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">add_circle</span> Add Input
                                </button>
                            </div>
                            <div v-for="(input, idx) in testCaseForm.input" :key="idx" class="flex gap-2">
                                <textarea v-model="testCaseForm.input[idx]" rows="1" class="flex-1 bg-[#14262b] border border-[#224249] rounded-lg py-2 px-4 text-white text-sm focus:outline-none focus:border-primary/50 transition-all resize-none font-mono" placeholder="Input value..."></textarea>
                                <button @click="removeInput(idx)" v-if="testCaseForm.input.length > 1" class="text-red-500 hover:text-red-400 px-2 transition-colors">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </div>
                            <p v-if="errors.input" class="text-red-400 text-[10px] font-bold">{{ errors.input[0] }}</p>
                        </div>

                        <!-- Expected Outputs -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">Expected Outputs</label>
                                <button @click="addOutput" class="text-xs font-bold text-emerald-500 hover:text-white transition-colors flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">add_circle</span> Add Output
                                </button>
                            </div>
                            <div v-for="(output, idx) in testCaseForm.expected_output" :key="idx" class="flex gap-2">
                                <textarea v-model="testCaseForm.expected_output[idx]" rows="1" class="flex-1 bg-[#14262b] border border-[#224249] rounded-lg py-2 px-4 text-white text-sm focus:outline-none focus:border-emerald-500/50 transition-all resize-none font-mono" placeholder="Expected output value..."></textarea>
                                <button @click="removeOutput(idx)" v-if="testCaseForm.expected_output.length > 1" class="text-red-500 hover:text-red-400 px-2 transition-colors">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </div>
                            <p v-if="errors.expected_output" class="text-red-400 text-[10px] font-bold">{{ errors.expected_output[0] }}</p>
                        </div>

                        <!-- Visibility -->
                        <div class="flex items-center gap-3 bg-[#14262b] p-4 rounded-xl border border-[#224249]">
                            <div class="relative inline-flex items-center cursor-pointer">
                                <input v-model="testCaseForm.is_hidden" type="checkbox" class="sr-only peer">
                                <div class="w-11 h-6 bg-[#224249] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-400 after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary peer-checked:after:bg-white"></div>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-white uppercase tracking-wider">Hidden Trial</p>
                                <p class="text-[10px] text-slate-500 italic">This trial will not be visible to the crew during testing.</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 bg-background-dark/50 flex gap-3 border-t border-[#224249]">
                        <button @click="closeModal" class="flex-1 px-6 py-3 rounded-xl border border-slate-700 text-slate-300 font-bold hover:bg-slate-800 transition-colors">
                            Abandon
                        </button>
                        <button @click="submitTestCase" class="flex-1 px-6 py-3 rounded-xl bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark font-black transition-all flex items-center justify-center gap-2 shadow-lg shadow-pirate-gold/10">
                            <span class="material-symbols-outlined text-lg">verified</span>
                            Finalize Trial
                        </button>
                    </div>
                </div>
            </div>

            <!-- View Test Case Modal -->
            <div v-if="viewTestCaseModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
                <div class="bg-[#182f34] border-2 border-primary/30 rounded-2xl w-full max-w-xl shadow-[0_20px_50px_rgba(0,0,0,0.5)] overflow-hidden">
                    <div class="bg-background-dark p-6 border-b border-primary/20 flex items-center justify-between">
                         <div class="flex items-center gap-4">
                            <div class="size-12 rounded-full bg-primary/10 border border-primary/30 flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined text-3xl">visibility</span>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold adventure-title text-primary tracking-wide">Trial Insights</h3>
                                <p class="text-[10px] uppercase tracking-[0.2em] text-slate-400 font-bold">Reviewing Test Case</p>
                            </div>
                        </div>
                        <button @click="closeModal" class="text-slate-500 hover:text-white transition-colors">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                    
                    <div class="p-8 space-y-6">
                        <div class="space-y-4">
                            <h4 class="text-[10px] font-black text-[#5a8b95] uppercase tracking-widest">Inputs</h4>
                            <div class="space-y-2">
                                <div v-for="(val, idx) in selectedTestCase?.input" :key="idx" class="bg-[#14262b] p-3 rounded-lg border border-[#224249] font-mono text-xs text-white">
                                    {{ val }}
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h4 class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">Expected Outputs</h4>
                            <div class="space-y-2">
                                <div v-for="(val, idx) in selectedTestCase?.expected_output" :key="idx" class="bg-[#14262b] p-3 rounded-lg border border-[#224249] font-mono text-xs text-emerald-500/80">
                                    {{ val }}
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-[#224249] flex items-center justify-between">
                            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Visibility Status:</span>
                            <span :class="selectedTestCase?.is_hidden ? 'text-slate-400' : 'text-emerald-500'" class="text-[10px] font-black uppercase tracking-widest">
                                {{ selectedTestCase?.is_hidden ? 'Secret (Hidden)' : 'Public' }}
                            </span>
                        </div>
                    </div>

                    <div class="p-6 bg-background-dark/50">
                        <button @click="closeModal" class="w-full px-6 py-3 rounded-xl bg-primary hover:brightness-110 text-background-dark font-black transition-all">
                            Acknowledged
                        </button>
                    </div>
                </div>
            </div>

        </main>
        <div v-else class="flex-1 flex items-center justify-center">
            <div class="size-12 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
        </div>
    </NuxtLayout>
</template>
