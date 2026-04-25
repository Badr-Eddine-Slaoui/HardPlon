<script setup lang="ts">
    import { useLanguage } from '~~/stores/language';

    useHead({
        title: `Admin Dashboard - Refine Sea Tongue`
    })

    const store = useLanguage();
    const router = useRouter();
    const route = useRoute();

    const form = reactive({
        name: '',
        docker_image: '',
        compile_command: '',
        run_command: ''
    })

    const errs = ref<any>({})

    onMounted(async () => {
        const id = parseInt(route.params.id as string);
        await store.fetchLanguage(id);
        if (store.language) {
            form.name = store.language.name;
            form.docker_image = store.language.docker_image;
            form.compile_command = store.language.compile_command || '';
            form.run_command = store.language.run_command;
        }
    })

    async function submit() {
        const id = parseInt(route.params.id as string);
        const res = await store.updateLanguage(id, form);

        if (res.success) {
            router.push('/admin/language');
        } else {
            errs.value = res.errors;
        }
    }

    definePageMeta({
        middleware: ['auth', 'admin']
    })
</script>

<template>
    <NuxtLayout name="admin">
        <main class="flex h-[calc(100vh-65px)] overflow-hidden w-full">
            <section class="flex-1 overflow-y-auto p-10 custom-scrollbar">
                <div class="max-w-3xl mx-auto">
                    <div class="mb-10">
                        <NuxtLink to="/admin/language" class="text-primary hover:text-primary/80 flex items-center gap-2 mb-4 transition-colors">
                            <span class="material-symbols-outlined">arrow_back</span>
                            <span class="text-xs font-bold uppercase tracking-widest">Back to Tongues</span>
                        </NuxtLink>
                        <h2 class="text-3xl font-bold text-white font-display mb-1">Refine Tongue</h2>
                        <p class="text-[#90c1cb]">Adjust the arcane specifications for the dialect: <span class="text-primary font-bold">{{ store.language?.name }}</span></p>
                    </div>

                    <div class="rounded-2xl border border-[#315f68] bg-[#182f34] p-8 shadow-2xl relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-8 opacity-5 pointer-events-none">
                            <span class="material-symbols-outlined text-[120px] text-primary">edit_square</span>
                        </div>

                        <form @submit.prevent="submit" class="space-y-8 relative">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <!-- Dialect Name -->
                                <div class="space-y-2">
                                    <label for="name" class="block text-[10px] font-black text-primary uppercase tracking-widest ml-1">Dialect Name</label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="material-symbols-outlined text-primary/40 group-focus-within:text-primary transition-colors">code</span>
                                        </div>
                                        <input 
                                            v-model="form.name"
                                            type="text" 
                                            id="name"
                                            placeholder="e.g., Python 3.12"
                                            class="w-full bg-[#14262b] border border-[#224249] rounded-lg py-3 pl-10 pr-4 text-white placeholder:text-slate-600 focus:outline-none focus:border-primary/50 focus:ring-1 focus:ring-primary/20 transition-all"
                                        />
                                    </div>
                                    <p v-if="errs.name" class="text-red-400 text-[10px] font-bold mt-1 ml-1 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">error</span>
                                        {{ errs.name }}
                                    </p>
                                </div>

                                <!-- Vessel (Docker Image) -->
                                <div class="space-y-2">
                                    <label for="docker_image" class="block text-[10px] font-black text-primary uppercase tracking-widest ml-1">Vessel (Docker Image)</label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="material-symbols-outlined text-primary/40 group-focus-within:text-primary transition-colors">token</span>
                                        </div>
                                        <input 
                                            v-model="form.docker_image"
                                            type="text" 
                                            id="docker_image"
                                            placeholder="e.g., python:3.12-slim"
                                            class="w-full bg-[#14262b] border border-[#224249] rounded-lg py-3 pl-10 pr-4 text-white placeholder:text-slate-600 focus:outline-none focus:border-primary/50 focus:ring-1 focus:ring-primary/20 transition-all font-mono"
                                        />
                                    </div>
                                    <p v-if="errs.docker_image" class="text-red-400 text-[10px] font-bold mt-1 ml-1 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">error</span>
                                        {{ errs.docker_image }}
                                    </p>
                                </div>
                            </div>

                            <!-- Commands Section -->
                            <div class="pt-6 border-t border-[#224249]">
                                <h3 class="text-xs font-bold text-white uppercase tracking-wider mb-6 flex items-center gap-2">
                                    <span class="size-2 bg-primary rounded-full animate-pulse"></span>
                                    Operational Commands
                                </h3>

                                <div class="grid grid-cols-1 gap-8">
                                    <!-- Compile Command -->
                                    <div class="space-y-2">
                                        <label for="compile_command" class="block text-[10px] font-black text-primary uppercase tracking-widest ml-1">Compile Command (Optional)</label>
                                        <div class="relative group">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="material-symbols-outlined text-primary/40 group-focus-within:text-primary transition-colors">terminal</span>
                                            </div>
                                            <input 
                                                v-model="form.compile_command"
                                                type="text" 
                                                id="compile_command"
                                                placeholder="e.g., gcc main.c -o main"
                                                class="w-full bg-[#14262b] border border-[#224249] rounded-lg py-3 pl-10 pr-4 text-white placeholder:text-slate-600 focus:outline-none focus:border-primary/50 focus:ring-1 focus:ring-primary/20 transition-all font-mono"
                                            />
                                        </div>
                                        <p v-if="errs.compile_command" class="text-red-400 text-[10px] font-bold mt-1 ml-1 flex items-center gap-1">
                                            <span class="material-symbols-outlined text-xs">error</span>
                                            {{ errs.compile_command }}
                                        </p>
                                    </div>

                                    <!-- Run Command -->
                                    <div class="space-y-2">
                                        <label for="run_command" class="block text-[10px] font-black text-primary uppercase tracking-widest ml-1">Run Command</label>
                                        <div class="relative group">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="material-symbols-outlined text-primary/40 group-focus-within:text-primary transition-colors">play_arrow</span>
                                            </div>
                                            <input 
                                                v-model="form.run_command"
                                                type="text" 
                                                id="run_command"
                                                placeholder="e.g., python3 main.py"
                                                class="w-full bg-[#14262b] border border-[#224249] rounded-lg py-3 pl-10 pr-4 text-white placeholder:text-slate-600 focus:outline-none focus:border-primary/50 focus:ring-1 focus:ring-primary/20 transition-all font-mono"
                                            />
                                        </div>
                                        <p v-if="errs.run_command" class="text-red-400 text-[10px] font-bold mt-1 ml-1 flex items-center gap-1">
                                            <span class="material-symbols-outlined text-xs">error</span>
                                            {{ errs.run_command }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-6 border-t border-[#224249]">
                                <div class="flex items-center gap-2 text-[#5a8b95]">
                                    <span class="material-symbols-outlined text-sm animate-spin-slow">settings</span>
                                    <span class="text-[10px] uppercase tracking-widest font-bold">Refining Vessel Parameters</span>
                                </div>
                                <button 
                                    type="submit"
                                    class="px-10 py-4 bg-primary hover:brightness-110 text-background-dark font-black rounded-lg transition-all transform hover:scale-105 shadow-[0_4px_20px_0_rgba(13,204,242,0.3)] flex items-center gap-2"
                                >
                                    <span class="material-symbols-outlined">auto_fix_high</span>
                                    <span>Update Tongue</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </main>
    </NuxtLayout>
</template>

<style scoped>
@keyframes spin-slow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
.animate-spin-slow {
    animation: spin-slow 8s linear infinite;
}
</style>
