<script setup lang="ts">
    import { useBriefVersion } from '~~/stores/brief_version';
    import { useLanguage } from '~~/stores/language';

    const props = defineProps<{
        briefId: number;
        isOpen: boolean;
    }>();

    const emit = defineEmits(['close', 'success']);

    const versionStore = useBriefVersion();
    const langStore = useLanguage();

    onMounted(async () => {
        await langStore.fetchAllLanguages();
        if (langStore.all_languages?.length) {
            language.value = langStore?.all_languages[0]?.name as string;
        }
    });

    const currentStep = ref(1);
    const totalSteps = 3;

    // Form State
    const version = ref('');
    const required_files = ref<string[]>([]);
    const optional_files = ref<string[]>([]);
    const forbidden_files = ref<string[]>([]);
    const patterns = ref<{ type: string; path: string; value: string }[]>([]);
    const type = ref<'http' | 'browser' | 'function'>('http');
    const timeout_seconds = ref(30);
    const retries = ref(3);
    const base_path = ref('');
    const language = ref('');
    const tests = ref<any[]>([]);

    // Helper inputs
    const newRequiredFile = ref('');
    const newOptionalFile = ref('');
    const newForbiddenFile = ref('');
    const newPatternType = ref('file_contains');
    const newPatternPath = ref('');
    const newPatternValue = ref('');
    const errors = ref<any>({});

    const addRequiredFile = () => {
        if (!newRequiredFile.value.trim()) return;
        required_files.value.push(newRequiredFile.value.trim());
        newRequiredFile.value = '';
    };
    const addOptionalFile = () => {
        if (!newOptionalFile.value.trim()) return;
        optional_files.value.push(newOptionalFile.value.trim());
        newOptionalFile.value = '';
    };
    const addForbiddenFile = () => {
        if (!newForbiddenFile.value.trim()) return;
        forbidden_files.value.push(newForbiddenFile.value.trim());
        newForbiddenFile.value = '';
    };

    const removeFile = (list: 'required' | 'optional' | 'forbidden', index: number) => {
        if (list === 'required') required_files.value.splice(index, 1);
        if (list === 'optional') optional_files.value.splice(index, 1);
        if (list === 'forbidden') forbidden_files.value.splice(index, 1);
    };

    const addPattern = () => {
        if (!newPatternPath.value.trim() || !newPatternValue.value.trim()) return;
        patterns.value.push({
            type: newPatternType.value,
            path: newPatternPath.value.trim(),
            value: newPatternValue.value.trim()
        });
        newPatternPath.value = '';
        newPatternValue.value = '';
    };
    const removePattern = (index: number) => {
        patterns.value.splice(index, 1);
    };

    // Test Management
    const newJsonValues = ref<string[]>([]);

    const addTest = () => {
        if (type.value === 'http') {
            tests.value.push({
                name: '',
                method: 'GET',
                url: '',
                headers: [] as { name: string; value: string }[],
                body: '',
                expected: { status: 200, json_contains: [] as string[] }
            });
            newJsonValues.value.push('');
        } else if (type.value === 'browser') {
            tests.value.push({
                name: '',
                steps: [] as { action: 'goto' | 'click'; url: string; selector: string }[],
                expected: { title: '', text: '' }
            });
        } else if (type.value === 'function') {
            tests.value.push({
                name: '',
                input: [] as any[],
                expected_output: [] as any[]
            });
        }
    };

    const removeTest = (index: number) => {
        tests.value.splice(index, 1);
        if (type.value === 'http') newJsonValues.value.splice(index, 1);
    };

    watch(type, () => {
        tests.value = [];
        newJsonValues.value = [];
    });

    // HTTP Helpers
    const addHeader = (testIdx: number) => {
        tests.value[testIdx].headers.push({ name: '', value: '' });
    };
    const removeHeader = (testIdx: number, hIdx: number) => {
        tests.value[testIdx].headers.splice(hIdx, 1);
    };
    const addJsonContains = (testIdx: number) => {
        const val = newJsonValues.value[testIdx]?.trim();
        if (!val) return;
        if (!tests.value[testIdx].expected.json_contains) tests.value[testIdx].expected.json_contains = [];
        tests.value[testIdx].expected.json_contains.push(val);
        newJsonValues.value[testIdx] = '';
    };
    const removeJsonContains = (testIdx: number, jcIdx: number) => {
        tests.value[testIdx].expected.json_contains.splice(jcIdx, 1);
    };

    // Browser Helpers
    const addStep = (testIdx: number) => {
        tests.value[testIdx].steps.push({ action: 'goto', url: '', selector: '' });
    };
    const removeStep = (testIdx: number, sIdx: number) => {
        tests.value[testIdx].steps.splice(sIdx, 1);
    };

    // Function Helpers
    const addInput = (testIdx: number) => {
        tests.value[testIdx].input.push('');
    };
    const removeInput = (testIdx: number, iIdx: number) => {
        tests.value[testIdx].input.splice(iIdx, 1);
    };
    const addOutput = (testIdx: number) => {
        tests.value[testIdx].expected_output.push('');
    };
    const removeOutput = (testIdx: number, oIdx: number) => {
        tests.value[testIdx].expected_output.splice(oIdx, 1);
    };

    const submit = async () => {
        errors.value = {};
        const payload = {
            brief_id: props.briefId,
            version: version.value,
            required_files: required_files.value,
            optional_files: optional_files.value,
            forbidden_files: forbidden_files.value,
            patterns: patterns.value,
            type: type.value,
            timeout_seconds: timeout_seconds.value,
            retries: retries.value,
            base_path: base_path.value,
            language: language.value,
            tests: tests.value
        };
        
        const res = await versionStore.createBriefVersion(payload);
        if (res.success) {
            emit('success');
            reset();
        } else {
            errors.value = res.errors || {};
        }
    };

    const reset = () => {
        currentStep.value = 1;
        version.value = '';
        required_files.value = [];
        optional_files.value = [];
        forbidden_files.value = [];
        patterns.value = [];
        tests.value = [];
        errors.value = {};
    };

    const close = () => {
        emit('close');
    };
</script>

<template>
    <div v-if="isOpen" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/80 backdrop-blur-md overflow-y-auto">
        <div class="bg-[#182f34] border border-pirate-gold/30 rounded-2xl w-full max-w-5xl shadow-2xl flex flex-col max-h-[95vh] animate-in fade-in zoom-in-95 duration-300">
            <!-- Header -->
            <div class="p-6 border-b border-pirate-gold/20 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-lg bg-pirate-gold/10 flex items-center justify-center text-pirate-gold">
                        <span class="material-symbols-outlined">terminal</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold adventure-title text-pirate-gold">Deploy New Version</h3>
                        <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Step {{ currentStep }} of {{ totalSteps }}</p>
                    </div>
                </div>
                <button @click="close" class="text-slate-400 hover:text-white transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <!-- Body -->
            <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
                <!-- Progress -->
                <div class="mb-10 flex items-center gap-2">
                    <div v-for="s in totalSteps" :key="s" 
                        :class="['flex-1 h-1.5 rounded-full transition-all duration-500', s <= currentStep ? 'bg-primary shadow-[0_0_10px_rgba(13,204,242,0.3)]' : 'bg-slate-700']"></div>
                </div>

                <!-- Step 1: Core Config -->
                <div v-if="currentStep === 1" class="space-y-8 animate-in fade-in slide-in-from-bottom-4">
                    <div>
                        <label class="block text-xs font-black text-primary uppercase tracking-widest mb-2">Version Name</label>
                        <input v-model="version" type="text" placeholder="e.g. v1.0 - Initial Deployment"
                            class="w-full bg-background-dark border-[#224249] text-white rounded-xl py-3 px-4 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                        <p v-if="errors.version" class="text-red-500 text-[10px] mt-1 font-bold">{{ errors.version[0] }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-primary uppercase tracking-widest mb-3">Test Infrastructure</label>
                        <div class="grid grid-cols-3 gap-4">
                            <button v-for="t in ['http', 'browser', 'function']" :key="t"
                                @click="type = t as any"
                                :class="['p-6 rounded-xl border-2 transition-all flex flex-col items-center gap-3 group', 
                                    type === t ? 'border-primary bg-primary/5 text-primary shadow-lg shadow-primary/10' : 'border-[#224249] bg-background-dark/50 text-slate-500 hover:border-primary/50']">
                                <span class="material-symbols-outlined text-3xl group-hover:scale-110 transition-transform">
                                    {{ t === 'http' ? 'api' : t === 'browser' ? 'language' : 'code' }}
                                </span>
                                <span class="text-xs font-black uppercase tracking-widest">{{ t }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Files & Patterns -->
                <div v-if="currentStep === 2" class="space-y-10 animate-in fade-in slide-in-from-bottom-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Required Files -->
                        <div class="space-y-4">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Required Files</label>
                            <div class="flex gap-2">
                                <input v-model="newRequiredFile" @keyup.enter="addRequiredFile" type="text" placeholder="index.html"
                                    class="flex-1 bg-background-dark border-[#224249] text-white rounded-lg py-2 px-3 text-xs outline-none focus:ring-1 focus:ring-primary">
                                <button @click="addRequiredFile" class="size-9 rounded-lg bg-primary/10 text-primary flex items-center justify-center hover:bg-primary/20 transition-all">
                                    <span class="material-symbols-outlined text-lg">add</span>
                                </button>
                            </div>
                            <div class="flex flex-wrap gap-2 max-h-40 overflow-y-auto pr-2 custom-scrollbar">
                                <span v-for="(f, i) in required_files" :key="i"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-slate-800 text-[10px] text-slate-300 border border-slate-700 animate-in zoom-in-95">
                                    {{ f }}
                                    <button @click="removeFile('required', i)" class="text-slate-500 hover:text-red-500 transition-colors">
                                        <span class="material-symbols-outlined text-sm">close</span>
                                    </button>
                                </span>
                            </div>
                            <p v-if="errors.required_files" class="text-red-500 text-[10px] mt-1">{{ errors.required_files[0] }}</p>
                        </div>

                        <!-- Optional Files -->
                        <div class="space-y-4">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Optional Files</label>
                            <div class="flex gap-2">
                                <input v-model="newOptionalFile" @keyup.enter="addOptionalFile" type="text" placeholder="styles.css"
                                    class="flex-1 bg-background-dark border-[#224249] text-white rounded-lg py-2 px-3 text-xs outline-none focus:ring-1 focus:ring-primary">
                                <button @click="addOptionalFile" class="size-9 rounded-lg bg-primary/10 text-primary flex items-center justify-center hover:bg-primary/20 transition-all">
                                    <span class="material-symbols-outlined text-lg">add</span>
                                </button>
                            </div>
                            <div class="flex flex-wrap gap-2 max-h-40 overflow-y-auto pr-2 custom-scrollbar">
                                <span v-for="(f, i) in optional_files" :key="i"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-slate-800 text-[10px] text-slate-300 border border-slate-700 animate-in zoom-in-95">
                                    {{ f }}
                                    <button @click="removeFile('optional', i)" class="text-slate-500 hover:text-red-500 transition-colors">
                                        <span class="material-symbols-outlined text-sm">close</span>
                                    </button>
                                </span>
                            </div>
                            <p v-if="errors.optional_files" class="text-red-500 text-[10px] mt-1">{{ errors.optional_files[0] }}</p>
                        </div>

                        <!-- Forbidden Files -->
                        <div class="space-y-4">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Forbidden Files</label>
                            <div class="flex gap-2">
                                <input v-model="newForbiddenFile" @keyup.enter="addForbiddenFile" type="text" placeholder="test.js"
                                    class="flex-1 bg-background-dark border-[#224249] text-white rounded-lg py-2 px-3 text-xs outline-none focus:ring-1 focus:ring-primary">
                                <button @click="addForbiddenFile" class="size-9 rounded-lg bg-primary/10 text-primary flex items-center justify-center hover:bg-primary/20 transition-all">
                                    <span class="material-symbols-outlined text-lg">add</span>
                                </button>
                            </div>
                            <div class="flex flex-wrap gap-2 max-h-40 overflow-y-auto pr-2 custom-scrollbar">
                                <span v-for="(f, i) in forbidden_files" :key="i"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-slate-800 text-[10px] text-slate-300 border border-slate-700 animate-in zoom-in-95">
                                    {{ f }}
                                    <button @click="removeFile('forbidden', i)" class="text-slate-500 hover:text-red-500 transition-colors">
                                        <span class="material-symbols-outlined text-sm">close</span>
                                    </button>
                                </span>
                            </div>
                            <p v-if="errors.forbidden_files" class="text-red-500 text-[10px] mt-1">{{ errors.forbidden_files[0] }}</p>
                        </div>
                    </div>

                    <div class="pt-8 border-t border-[#224249]">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">Security Patterns</label>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            <select v-model="newPatternType" class="bg-background-dark border-[#224249] text-white rounded-lg py-2 px-3 text-xs outline-none focus:ring-1 focus:ring-primary">
                                <option value="file_contains">File Contains</option>
                                <option value="dir_contains">Dir Contains</option>
                                <option value="file_not_contains">File Not Contains</option>
                                <option value="dir_not_contains">Dir Not Contains</option>
                            </select>
                            <input v-model="newPatternPath" type="text" placeholder="path/to/script.js"
                                class="bg-background-dark border-[#224249] text-white rounded-lg py-2 px-3 text-xs outline-none focus:ring-1 focus:ring-primary">
                            <input v-model="newPatternValue" type="text" placeholder="String to match"
                                class="bg-background-dark border-[#224249] text-white rounded-lg py-2 px-3 text-xs outline-none focus:ring-1 focus:ring-primary">
                            <button @click="addPattern" class="bg-primary/10 text-primary rounded-lg py-2 font-black text-[10px] uppercase tracking-widest hover:bg-primary/20 transition-all">Add Rule</button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div v-for="(p, i) in patterns" :key="i"
                                class="flex items-center justify-between p-3 bg-background-dark/50 border border-[#224249] rounded-xl animate-in slide-in-from-left-4">
                                <div class="flex items-center gap-3 text-[10px]">
                                    <span class="font-black text-primary px-2 py-0.5 bg-primary/10 rounded">{{ p.type }}</span>
                                    <span class="text-slate-400 font-mono">{{ p.path }}</span>
                                    <span class="text-pirate-gold italic">"{{ p.value }}"</span>
                                </div>
                                <button @click="removePattern(i)" class="text-slate-500 hover:text-red-500 transition-colors">
                                    <span class="material-symbols-outlined text-sm">delete</span>
                                </button>
                            </div>
                        </div>
                        <p v-if="errors.patterns" class="text-red-500 text-[10px] mt-2">{{ errors.patterns[0] }}</p>
                    </div>
                </div>

                <!-- Step 3: Test Engine -->
                <div v-if="currentStep === 3" class="space-y-8 animate-in fade-in slide-in-from-bottom-4 pb-12">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-background-dark/30 p-6 rounded-2xl border border-[#224249]">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Timeout (Seconds)</label>
                            <input v-model.number="timeout_seconds" type="number"
                                class="w-full bg-background-dark border-[#224249] text-white rounded-xl py-2 px-4 text-xs focus:ring-1 focus:ring-primary outline-none">
                            <p v-if="errors.timeout_seconds" class="text-red-500 text-[10px] mt-1">{{ errors.timeout_seconds[0] }}</p>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Max Retries</label>
                            <input v-model.number="retries" type="number"
                                class="w-full bg-background-dark border-[#224249] text-white rounded-xl py-2 px-4 text-xs focus:ring-1 focus:ring-primary outline-none">
                            <p v-if="errors.retries" class="text-red-500 text-[10px] mt-1">{{ errors.retries[0] }}</p>
                        </div>
                        <div v-if="type === 'http'" class="md:col-span-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Base URL</label>
                            <input v-model="base_path" type="text" placeholder="http://localhost:8000"
                                class="w-full bg-background-dark border-[#224249] text-white rounded-xl py-3 px-4 text-xs focus:ring-1 focus:ring-primary outline-none">
                            <p v-if="errors.base_path" class="text-red-500 text-[10px] mt-1">{{ errors.base_path[0] }}</p>
                        </div>
                        <div v-if="type === 'function'" class="md:col-span-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Runtime Language</label>
                            <select v-model="language" class="w-full bg-background-dark border-[#224249] text-white rounded-xl py-3 px-4 text-xs focus:ring-1 focus:ring-primary outline-none transition-all">
                                <option v-for="lang in langStore.all_languages" :key="lang.id" :value="lang.name">{{ lang.name }}</option>
                            </select>
                            <p v-if="errors.language" class="text-red-500 text-[10px] mt-1">{{ errors.language[0] }}</p>
                        </div>
                    </div>

                    <div class="pt-8 border-t border-[#224249]">
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h4 class="text-sm font-black text-white uppercase tracking-widest">Battle Scenarios (Tests)</h4>
                                <p class="text-[10px] text-slate-500 mt-1 uppercase tracking-wider">{{ tests.length }} Scenarios Configured</p>
                            </div>
                            <button @click="addTest" class="flex items-center gap-2 px-5 py-2.5 bg-primary text-background-dark rounded-xl font-black text-[10px] uppercase tracking-widest hover:scale-105 transition-all shadow-lg shadow-primary/20">
                                <span class="material-symbols-outlined text-sm">add_circle</span> Add scenario
                            </button>
                        </div>
                        
                        <div class="space-y-10">
                            <div v-for="(test, i) in tests" :key="i" 
                                class="p-8 bg-background-dark/40 border-2 border-[#224249] rounded-3xl relative animate-in zoom-in-95 duration-300">
                                <button @click="removeTest(i)" class="absolute -top-3 -right-3 size-8 rounded-full bg-red-500 text-white flex items-center justify-center hover:scale-110 transition-all shadow-lg shadow-red-500/20">
                                    <span class="material-symbols-outlined text-sm">close</span>
                                </button>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                                    <div>
                                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Scenario Name</label>
                                        <input v-model="test.name" type="text" placeholder="e.g. Verify Login Success" class="w-full bg-background-dark border-[#224249] text-white rounded-xl py-2 px-3 text-xs outline-none focus:ring-1 focus:ring-primary">
                                        <p v-if="errors[`tests.${i}.name`]" class="text-red-500 text-[9px] mt-1 font-bold">{{ errors[`tests.${i}.name`][0] }}</p>
                                    </div>
                                    
                                    <div v-if="type === 'http'">
                                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Endpoint Configuration</label>
                                        <div class="flex gap-2">
                                            <select v-model="test.method" class="w-28 bg-background-dark border-[#224249] text-white rounded-xl py-2 px-3 text-[10px] outline-none">
                                                <option>GET</option><option>POST</option><option>PUT</option><option>DELETE</option><option>PATCH</option>
                                            </select>
                                            <input v-model="test.url" type="text" placeholder="/api/auth/login" class="flex-1 bg-background-dark border-[#224249] text-white rounded-xl py-2 px-3 text-[10px] outline-none focus:ring-1 focus:ring-primary">
                                        </div>
                                        <p v-if="errors[`tests.${i}.url`]" class="text-red-500 text-[9px] mt-1 font-bold">{{ errors[`tests.${i}.url`][0] }}</p>
                                    </div>
                                </div>

                                <!-- HTTP Config -->
                                <div v-if="type === 'http'" class="space-y-8">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                        <div class="bg-background-dark/30 p-5 rounded-2xl border border-[#224249]">
                                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4 flex justify-between items-center">
                                                Headers
                                                <button @click="addHeader(i)" class="text-[9px] text-primary hover:underline">Add Header</button>
                                            </label>
                                            <div class="space-y-3">
                                                <div v-for="(h, hi) in test.headers" :key="hi" class="flex gap-2 animate-in slide-in-from-left-2">
                                                    <input v-model="h.name" placeholder="Key" class="flex-1 bg-background-dark border-[#224249] text-white rounded-lg py-1.5 px-3 text-[10px] outline-none focus:ring-1 focus:ring-primary">
                                                    <input v-model="h.value" placeholder="Value" class="flex-1 bg-background-dark border-[#224249] text-white rounded-lg py-1.5 px-3 text-[10px] outline-none focus:ring-1 focus:ring-primary">
                                                    <button @click="removeHeader(i, hi as number)" class="text-slate-600 hover:text-red-500 transition-colors">
                                                        <span class="material-symbols-outlined text-sm">close</span>
                                                    </button>
                                                </div>
                                                <p v-if="!test.headers.length" class="text-[9px] text-slate-600 italic">No headers defined.</p>
                                            </div>
                                        </div>
                                        <div v-if="['POST', 'PUT', 'PATCH'].includes(test.method)" class="bg-background-dark/30 p-5 rounded-2xl border border-[#224249]">
                                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4">Payload (JSON Body)</label>
                                            <textarea v-model="test.body" placeholder='{"email": "luffy@pirates.com"}' rows="3" class="w-full bg-background-dark border-[#224249] text-white rounded-xl py-2 px-3 text-[10px] font-mono outline-none focus:ring-1 focus:ring-primary custom-scrollbar"></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="p-6 bg-primary/5 border border-primary/20 rounded-2xl">
                                        <h5 class="text-[10px] font-black text-primary uppercase tracking-widest mb-4">Verification Logic</h5>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                            <div>
                                                <label class="block text-[9px] font-bold text-slate-500 uppercase mb-2">HTTP Status</label>
                                                <input v-model.number="test.expected.status" type="number" class="w-full bg-background-dark border-[#224249] text-white rounded-lg py-2 px-3 text-[10px] outline-none focus:ring-1 focus:ring-primary">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block text-[9px] font-bold text-slate-500 uppercase mb-2">JSON Structure contains (Strings)</label>
                                                <div class="flex gap-2 mb-3">
                                                    <input v-model="newJsonValues[i]" @keyup.enter="addJsonContains(i)" placeholder="e.g. token, user.id" class="flex-1 bg-background-dark border-[#224249] text-white rounded-lg py-2 px-3 text-[10px] outline-none focus:ring-1 focus:ring-primary">
                                                    <button @click="addJsonContains(i)" class="px-4 bg-primary text-background-dark rounded-lg text-[10px] font-black uppercase">Add</button>
                                                </div>
                                                <div class="flex flex-wrap gap-2">
                                                    <span v-for="(jc, jci) in test.expected.json_contains" :key="jci" class="px-2.5 py-1 bg-slate-800 text-[9px] text-slate-300 rounded-md border border-slate-700 flex items-center gap-1.5 animate-in zoom-in-95">
                                                        {{ jc }}
                                                        <button @click="removeJsonContains(i, jci as number)" class="text-slate-500 hover:text-red-500">
                                                            <span class="material-symbols-outlined text-[12px]">close</span>
                                                        </button>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- BROWSER Config -->
                                <div v-if="type === 'browser'" class="space-y-8">
                                    <div>
                                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4 flex justify-between items-center">
                                            Navigation & Interaction Steps
                                            <button @click="addStep(i)" class="text-[9px] text-primary hover:underline">Add Step</button>
                                        </label>
                                        <div class="space-y-3">
                                            <div v-for="(step, si) in test.steps" :key="si" class="bg-background-dark/30 p-4 rounded-2xl border border-[#224249] flex flex-wrap gap-4 items-end animate-in slide-in-from-left-2">
                                                <div class="flex-1 min-w-[140px]">
                                                    <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1.5">Action</label>
                                                    <select v-model="step.action" class="w-full bg-background-dark border-[#224249] text-white rounded-lg py-1.5 px-3 text-[10px] outline-none focus:ring-1 focus:ring-primary">
                                                        <option value="goto">Navigate to URL</option>
                                                        <option value="click">Click CSS Selector</option>
                                                    </select>
                                                </div>
                                                <div v-if="step.action === 'goto'" class="flex-[2] min-w-[200px]">
                                                    <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1.5">Full URL</label>
                                                    <input v-model="step.url" placeholder="http://localhost:8000/dashboard" class="w-full bg-background-dark border-[#224249] text-white rounded-lg py-1.5 px-3 text-[10px] outline-none focus:ring-1 focus:ring-primary">
                                                </div>
                                                <div v-if="step.action === 'click'" class="flex-[2] min-w-[200px]">
                                                    <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1.5">Target Selector</label>
                                                    <input v-model="step.selector" placeholder="button[type='submit']" class="w-full bg-background-dark border-[#224249] text-white rounded-lg py-1.5 px-3 text-[10px] outline-none focus:ring-1 focus:ring-primary">
                                                </div>
                                                <button @click="removeStep(i, si as number)" class="size-8 rounded-lg bg-red-500/10 text-red-500 flex items-center justify-center hover:bg-red-500/20 transition-all">
                                                    <span class="material-symbols-outlined text-sm">close</span>
                                                </button>
                                            </div>
                                            <p v-if="!test.steps.length" class="text-[9px] text-slate-600 italic">No steps configured.</p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-6 p-5 bg-primary/5 rounded-2xl border border-primary/10">
                                        <div>
                                            <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Verify Page Title</label>
                                            <input v-model="test.expected.title" placeholder="Home | Academix" class="w-full bg-background-dark border-[#224249] text-white rounded-lg py-2 px-3 text-[10px] outline-none focus:ring-1 focus:ring-primary">
                                        </div>
                                        <div>
                                            <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Verify Body Text</label>
                                            <input v-model="test.expected.text" placeholder="Welcome back, Captain!" class="w-full bg-background-dark border-[#224249] text-white rounded-lg py-2 px-3 text-[10px] outline-none focus:ring-1 focus:ring-primary">
                                        </div>
                                    </div>
                                </div>

                                <!-- FUNCTION Config -->
                                <div v-if="type === 'function'" class="space-y-8">
                                    <div class="grid grid-cols-2 gap-10">
                                        <div class="bg-background-dark/30 p-5 rounded-2xl border border-[#224249]">
                                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4 flex justify-between items-center">
                                                Arguments (Input)
                                                <button @click="addInput(i)" class="text-[9px] text-primary hover:underline">Add Arg</button>
                                            </label>
                                            <div class="space-y-3">
                                                <div v-for="(inp, ii) in test.input" :key="ii" class="flex gap-2 animate-in slide-in-from-left-2">
                                                    <input v-model="test.input[ii]" placeholder="Value" class="flex-1 bg-background-dark border-[#224249] text-white rounded-lg py-1.5 px-3 text-[10px] outline-none focus:ring-1 focus:ring-primary">
                                                    <button @click="removeInput(i, ii as number)" class="text-slate-600 hover:text-red-500 transition-colors">
                                                        <span class="material-symbols-outlined text-sm">close</span>
                                                    </button>
                                                </div>
                                                <p v-if="!test.input.length" class="text-[9px] text-slate-600 italic">No input parameters.</p>
                                            </div>
                                        </div>
                                        <div class="bg-background-dark/30 p-5 rounded-2xl border border-[#224249]">
                                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4 flex justify-between items-center">
                                                Expected Returns
                                                <button @click="addOutput(i)" class="text-[9px] text-primary hover:underline">Add Return</button>
                                            </label>
                                            <div class="space-y-3">
                                                <div v-for="(out, oi) in test.expected_output" :key="oi" class="flex gap-2 animate-in slide-in-from-left-2">
                                                    <input v-model="test.expected_output[oi]" placeholder="Value" class="flex-1 bg-background-dark border-[#224249] text-white rounded-lg py-1.5 px-3 text-[10px] outline-none focus:ring-1 focus:ring-primary">
                                                    <button @click="removeOutput(i, oi as number)" class="text-slate-600 hover:text-red-500 transition-colors">
                                                        <span class="material-symbols-outlined text-sm">close</span>
                                                    </button>
                                                </div>
                                                <p v-if="!test.expected_output.length" class="text-[9px] text-slate-600 italic">No expected outputs.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <p v-if="errors[`tests.${i}`]" class="text-red-500 text-[10px] mt-4 font-black uppercase text-center">{{ errors[`tests.${i}`][0] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-6 border-t border-pirate-gold/20 bg-background-dark/50 flex items-center justify-between shrink-0">
                <button @click="currentStep > 1 ? currentStep-- : close()" 
                    class="px-6 py-3 rounded-xl border border-slate-700 text-slate-300 font-bold hover:bg-slate-800 transition-all text-xs uppercase tracking-widest">
                    {{ currentStep === 1 ? 'Abandon Quest' : 'Previous Phase' }}
                </button>
                
                <div class="flex gap-4">
                    <button v-if="currentStep < totalSteps" @click="currentStep++"
                        class="px-8 py-3 rounded-xl bg-primary text-background-dark font-black transition-all hover:scale-105 active:scale-95 flex items-center gap-2 text-xs uppercase tracking-widest shadow-lg shadow-primary/20">
                        Proceed Forward
                        <span class="material-symbols-outlined text-lg">arrow_forward</span>
                    </button>
                    <button v-else @click="submit"
                        class="px-8 py-3 rounded-xl bg-pirate-gold text-background-dark font-black transition-all hover:scale-105 active:scale-95 flex items-center gap-2 text-xs uppercase tracking-widest shadow-lg shadow-pirate-gold/30">
                        Deploy Final Version
                        <span class="material-symbols-outlined text-lg">rocket_launch</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #224249;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #13ccf2;
    }
</style>
