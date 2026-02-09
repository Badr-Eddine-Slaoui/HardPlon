<script setup lang="ts">
    import { useBrief } from '~~/stores/brief';
    import type { ReturnData } from '~~/types/api';
    import { useClassGroup } from '~~/stores/class_group';
    import type { ClassGroup } from '~~/types/class_group';
    import type { Sprint, SprintSkill } from '~~/types/sprint';
    import { useSprint } from '~~/stores/sprint';

    useHead({
        title: 'Teacher Dashboard - Create Brief'
    })

    const store = useBrief()
    const classes = useClassGroup()
    const sprint = useSprint()

    const class_groups: Ref<ClassGroup[] | null> =  ref([])
    const sprints: Ref<Sprint[] | null> =  ref([])
    const skills: Ref<SprintSkill[] | null> =  ref([])
    const skill_levels: Ref<{ level_id: number|null, skill_id: number }[]> =  ref([])
    const is_all_skills_set: Ref<boolean> =  ref(false)

    onMounted(async() => {
        class_groups.value = await classes.fetchTeacherClassGroups();
    })

    const getSprints = async () => {
        if(!form.class_group_id) return
        if(!classes.class_groups) await classes.fetchClassGroups()
        const formation_id = classes.class_groups?.find(x => x.id == form.class_group_id)?.formation_id as number
        sprints.value = await sprint.fetchSprintsByFormationId(formation_id);
        form.sprint_id = 0
    }

    const getSkills = async () => {
        skills.value = await sprint.fetchSprintSkills(form.sprint_id);
        form.skill_levels = []
        skill_levels.value = skills.value?.map((x: SprintSkill) => ({ level_id: null, skill_id: x.skill.id }))
    }

    const setSkillLevel = (skill_id: number, level_id: number) => {
        let item = skill_levels.value?.find(x => x.skill_id == skill_id) as { level_id: number, skill_id: number }
        item.level_id = level_id
        is_all_skills_set.value = skill_levels.value?.every(x => x.level_id != null)
    }

    const form = reactive({
        sprint_id: 0,
        class_group_id: 0,
        title: '',
        description: '',
        is_collective: false,
        content: '',
        start_date: '',
        end_date: '',
        skill_levels: [] as { level_id: number, skill_id: number }[]
    })

    const errs = ref({
        sprint_id: '',
        class_group_id: '',
        title: '',
        description: '',
        is_collective: '',
        content: '',
        start_date: '',
        end_date: '',
        skill_levels: '',
        message: ''
    })

    const submit = async () => {
        if(!is_all_skills_set.value) {
            errs.value.message = 'All skills must be set'
            return
        }

        form.skill_levels = skill_levels.value?.filter(x => x.level_id != null) as { level_id: number, skill_id: number }[]
        
        const res : ReturnData<any> = await store.createBrief(form);
        if(res.success) {
            navigateTo('/teacher/brief')
        }
        
        if(res.errors) {
            errs.value = res.errors
        }
    }

    let current_step = 0;
    let max_step = 2;

    function nextStep() {
        if (current_step < max_step) {
            const steps = document.querySelectorAll('.step') as NodeListOf<HTMLElement>;
            steps[current_step++]?.classList.add('hidden');
            steps[current_step]?.classList.remove('hidden');
            updateStepLineColor();
            updateStepCircleColor();
        }
    }

    function prevStep() {
        if (current_step > 0) {
            const steps = document.querySelectorAll('.step') as NodeListOf<HTMLElement>;
            steps[current_step--]?.classList.add('hidden');
            steps[current_step]?.classList.remove('hidden');
            updateStepLineColor();
            updateStepCircleColor();
        }
    }

    function updateStepLineColor() {
        const step_lines = document.querySelectorAll('.step-line') as NodeListOf<HTMLElement>;
        step_lines.forEach((line, index) => {
            if (index < current_step) {
                line.classList.add('bg-primary');
                line.classList.remove('bg-[#224249]');
            } else {
                line.classList.remove('bg-primary');
                line.classList.add('bg-[#224249]');
            }
        });
    }

    function updateStepCircleColor() {
        const step_circles = document.querySelectorAll('.step-circle') as NodeListOf<HTMLElement>;
        step_circles.forEach((circle, index) => {
            let number = circle.querySelector('.number') as HTMLElement;
            let title = circle.querySelector('.title') as HTMLElement;
            if (index <= current_step) {
                number.classList.add('bg-primary', 'text-background-dark', 'ring-primary');
                number.classList.remove('bg-slate-800', 'text-slate-400', 'ring-slate-800');
                title.classList.add('text-primary');
                title.classList.remove('text-slate-500');
            } else {
                number.classList.remove('bg-primary', 'text-background-dark', 'ring-primary');
                number.classList.add('bg-slate-800', 'text-slate-400', 'ring-slate-800');
                title.classList.remove('text-primary');
                title.classList.add('text-slate-500');
            }
        });
    }
</script>

<template>
    <NuxtLayout name="teacher">
        <main class="flex-1 flex flex-col overflow-y-auto">
            <div class="p-8">
                <div class="max-w-4xl mx-auto">
                    <div class="mb-10 flex justify-between items-center relative z-0">
                        <div class="step-line absolute top-1/2 left-0 w-1/2 h-0.5 bg-[#224249] -z-10 -translate-y-1/2">
                        </div>
                        <div class="step-line absolute top-1/2 left-1/2 w-1/2 h-0.5 bg-[#224249] -z-10 -translate-y-1/2">
                        </div>
                        <div class="step-circle flex flex-col items-center gap-2 bg-background-dark px-4">
                            <div
                                class="number size-10 rounded-full bg-primary text-background-dark flex items-center justify-center font-bold border-4 border-background-dark ring-2 ring-primary">
                                1</div>
                            <span class="title text-[10px] font-black uppercase tracking-widest text-primary">Sprint
                                Selection</span>
                        </div>
                        <div class="step-circle flex flex-col items-center gap-2 bg-background-dark px-4">
                            <div
                                class="number size-10 rounded-full bg-slate-800 text-slate-400 flex items-center justify-center font-bold border-4 border-background-dark ring-2 ring-slate-800">
                                2</div>
                            <span class="title text-[10px] font-black uppercase tracking-widest text-slate-500">Skill
                                Mapping</span>
                        </div>
                        <div class="step-circle flex flex-col items-center gap-2 bg-background-dark px-4">
                            <div
                                class="number size-10 rounded-full bg-slate-800 text-slate-400 flex items-center justify-center font-bold border-4 border-background-dark ring-2 ring-slate-800">
                                3</div>
                            <span class="title text-[10px] font-black uppercase tracking-widest text-slate-500">Brief
                                Details</span>
                        </div>
                    </div>
                    <form @submit.prevent="submit" id="create-brief-form"
                        class="bg-white dark:bg-[#182f34] border border-slate-200 dark:border-[#224249] rounded-xl overflow-hidden shadow-2xl">
                        <div class="step">
                            <section class="bg-[#182f34] border border-[#224249] rounded-2xl p-8 relative overflow-hidden">
                                <div class="absolute -top-10 -right-10 opacity-5 pointer-events-none">
                                    <span class="material-symbols-outlined text-[160px]">sailing</span>
                                </div>
                                <div class="relative z-10 flex flex-col md:flex-row md:items-end gap-8">
                                    <div class="flex-1 space-y-4">
                                        <div>
                                            <h3 class="text-xl font-bold text-white flex items-center gap-2">
                                                <span class="material-symbols-outlined text-primary">explore</span>
                                                Step 1: Select Target Crew
                                            </h3>
                                            <p class="text-slate-400 text-sm mt-1">Select the target crew for this brief</p>
                                        </div>
                                        <div class="max-w-md relative">
                                            <label
                                                class="block text-[10px] font-black text-primary uppercase mb-2 tracking-widest"
                                                for="sprint-select">Target Crew</label>
                                            <div class="relative">
                                                <select v-model="form.class_group_id" @change="getSprints()"
                                                    class="w-full bg-background-dark border-[#224249] text-white rounded-xl py-3.5 px-4 appearance-none focus:ring-primary focus:border-primary transition-all cursor-pointer text-sm font-bold"
                                                    id="sprint-select">
                                                    <option selected disabled>Select a class group</option>
                                                    <option v-for="class_group in class_groups" :value="class_group.id">{{ class_group.name }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            <div v-if="sprints?.length as number > 0" class="sprint-container">
                                <div
                                    class="p-6 border-b border-slate-200 dark:border-[#224249] bg-slate-50/50 dark:bg-background-dark/30">
                                    <h3 class="text-lg font-bold flex items-center gap-2">
                                        <span class="material-symbols-outlined text-primary">sailing</span>
                                        Step 2: Assign to a Voyage (Sprint)
                                    </h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Select which arc this mission belongs
                                        to.</p>
                                </div>
                                <div class="p-8">
                                    <div class="sprints grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <label v-for="(sprint, index) in sprints" class="relative cursor-pointer group">
                                            <input v-model="form.sprint_id" @change="getSkills()" class="peer sr-only" type="radio" name="sprint" :value="sprint.id" />
                                            <div
                                                class="p-4 rounded-xl border-2 border-[#224249] bg-background-dark/50 group-hover:border-primary/50 peer-checked:border-primary peer-checked:bg-primary/5 transition-all">
                                                <div class="flex items-center justify-between mb-3">
                                                    <span class="text-primary text-3xl">S{{ index + 1 }}</span>
                                                    <span
                                                        class="material-symbols-outlined text-primary opacity-0 peer-checked:opacity-100">check_circle</span>
                                                </div>
                                                <p class="font-bold text-sm">{{ sprint.name }}</p>
                                                <p
                                                    class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-tighter">{{ sprint.description }}</p>
                                            </div>
                                        </label>
                                    </div>
                                    <div v-if="form.sprint_id"
                                        class="mt-8 pt-6 border-t border-slate-200 dark:border-[#224249] flex justify-end items-center">
                                        <button type="button" @click="nextStep()"
                                            class="bg-primary hover:bg-primary/80 text-background-dark px-8 py-2 rounded-lg font-black text-sm transition-all shadow-lg flex items-center gap-2">
                                            Next: Skill Mapping
                                            <span class="material-symbols-outlined text-lg">arrow_forward</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="step p-5 hidden">
                            <section id="skill-container" class="space-y-6 mt-5">
                                <div class="flex items-center justify-between border-b border-[#224249] pb-4">
                                    <div>
                                        <h3
                                            class="text-xl font-bold text-white flex items-center gap-2 uppercase tracking-tight">
                                            <span class="material-symbols-outlined text-gold">stars</span>
                                            Step 2: Ability Charting
                                        </h3>
                                        <p class="text-xs text-slate-400 mt-1">Define the required mastery levels for skills in
                                            <span class="text-gold font-bold italic">Alabasta Data Structures</span>
                                        </p>
                                    </div>
                                    <div class="flex gap-2">
                                        <span
                                            class="px-3 py-1 bg-primary/10 border border-primary/20 text-primary text-[10px] font-bold rounded flex items-center gap-1">
                                            <span class="material-symbols-outlined text-xs">info</span> <span class="skills-count">{{ skills?.length ?? 0 }}</span> Skills Detected
                                        </span>
                                    </div>
                                </div>
                                <div v-if="skills?.length as number > 0" class="skills grid gap-4">
                                    <div v-for="skill in skills"
                                        class="bg-[#182f34] border border-[#224249] hover:border-gold/40 rounded-xl p-5 transition-all group">
                                        <div class="flex flex-col lg:flex-row lg:items-center gap-6">
                                            <div class="flex-1 flex items-start gap-4">
                                                <div
                                                    class="size-14 bg-background-dark border border-[#224249] rounded-xl flex items-center justify-center shrink-0 group-hover:border-gold/30 transition-colors">
                                                    <span class="text-gold text-md">{{ skill.skill.code }}</span>
                                                </div>
                                                <div>
                                                    <h4
                                                        class="font-bold text-lg text-slate-100 group-hover:text-gold transition-colors">{{ skill.skill.title }}</h4>
                                                    <p class="text-xs text-slate-400 leading-relaxed max-w-lg">{{ skill.skill.description }}</p>
                                                </div>
                                            </div>
                                            <div class="lg:w-80 shrink-0">
                                                <div class="bg-background-dark/50 rounded-lg p-4 border border-[#224249]">
                                                    <p
                                                        class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3">
                                                        Required Mastery Level</p>
                                                    <div class="flex justify-between gap-4">
                                                        <label v-for="(skill_level, index) in skill.skill.skill_levels" class="flex-1 cursor-pointer group/radio">
                                                            <input class="hidden peer" :name="`level_id_${skill.skill.id}`" type="radio"
                                                                :value="skill_level.level.id" @change="setSkillLevel(skill.skill.id, skill_level.level.id)" />
                                                            <div
                                                                class="w-full text-center py-2 px-1 rounded border border-[#224249] peer-checked:bg-gold/10 peer-checked:border-gold transition-all">
                                                                <div
                                                                    class="size-4 mx-auto rounded-full border-2 border-[#224249] peer-checked:border-gold flex items-center justify-center mb-1">
                                                                </div>
                                                                <span
                                                                    class="text-[9px] font-bold text-slate-400 peer-checked:text-gold uppercase">{{ skill_level.level.name }}</span>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            <div v-if="skills?.length as number > 0" class="flex items-center justify-between pt-10 border-t border-[#224249]">
                                <button type="button" @click="prevStep()"
                                    class="bg-primary hover:bg-primary/80 text-background-dark px-8 py-2 rounded-lg font-black text-sm transition-all shadow-lg flex items-center gap-2">
                                    <span class="material-symbols-outlined text-lg">arrow_back</span>
                                    Prev: Sprint Selection
                                </button>
                                <button v-if="is_all_skills_set" type="button" @click="nextStep()"
                                    class="bg-primary hover:bg-primary/80 text-background-dark px-8 py-2 rounded-lg font-black text-sm transition-all shadow-lg flex items-center gap-2">
                                    Next: Brief Details
                                    <span class="material-symbols-outlined text-lg">arrow_forward</span>
                                </button>
                            </div>
                        </div>
                        <div class="step p-5 hidden">
                            <div class="flex-1 space-y-6">
                                <section class="bg-[#142d32] border border-[#1a3a41] rounded-2xl p-6 shadow-xl">
                                    <h3
                                        class="text-sm font-bold uppercase tracking-widest text-primary mb-6 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-lg">description</span>
                                        Mission Briefing Details
                                    </h3>
                                    <div class="space-y-6">
                                        <div>
                                            <label
                                                class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Brief
                                                Title</label>
                                            <input v-model="form.title"
                                                class="w-full bg-ocean-deep border-[#1a3a41] rounded-xl focus:ring-primary focus:border-primary text-white placeholder-slate-600"
                                                placeholder="e.g. Navigation Through the Calm Belt" type="text" />
                                            <p v-if="errs.title" class="text-red-500 text-xs mt-1">{{ errs.title }}</p>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Short
                                                Description</label>
                                            <textarea v-model="form.description"
                                                class="w-full bg-ocean-deep border-[#1a3a41] rounded-xl focus:ring-primary focus:border-primary text-white placeholder-slate-600"
                                                placeholder="Brief overview for the bounty board..." rows="2"></textarea>
                                            <p v-if="errs.description" class="text-red-500 text-xs mt-1">{{ errs.description }}</p>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Mission
                                                Content (Full Details)</label>
                                            <QuillEditor
                                                v-model="form.content"
                                            />
                                            <p v-if="errs.content" class="text-red-500 text-xs mt-1">{{ errs.content }}</p>
                                        </div>
                                    </div>
                                </section>
                                <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="bg-[#142d32] border border-[#1a3a41] rounded-2xl p-6">
                                        <h3
                                            class="text-sm font-bold uppercase tracking-widest text-primary mb-4 flex items-center gap-2">
                                            <span class="material-symbols-outlined text-lg">groups</span>
                                            Crew Structure
                                        </h3>
                                        <div
                                            class="flex items-center justify-between p-4 bg-ocean-deep rounded-xl border border-[#1a3a41]">
                                            <span class="text-sm font-medium">Solo or Squad?</span>
                                            <div class="flex bg-[#1a3a41] p-1 rounded-lg">
                                                <label class="relative cursor-pointer group">
                                                    <input v-model="form.is_collective" type="radio" checked name="type" :value="false"
                                                        class="peer sr-only">
                                                    <span
                                                        class="px-3 py-1 text-xs font-bold rounded-md text-slate-400 peer-checked:text-ocean-deep peer-checked:bg-primary transition-all">SOLO</span>
                                                </label>
                                                <label class="relative cursor-pointer group">
                                                    <input v-model="form.is_collective" type="radio" name="type" :value="true"
                                                        class="peer sr-only">
                                                    <span
                                                        class="px-3 py-1 text-xs font-bold rounded-md text-slate-400 peer-checked:text-ocean-deep peer-checked:bg-primary transition-all">SQUAD</span>
                                                </label>
                                                <p v-if="errs.is_collective" class="text-red-500 text-xs mt-1">{{ errs.is_collective }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-[#142d32] border border-[#1a3a41] rounded-2xl p-6">
                                        <h3
                                            class="text-sm font-bold uppercase tracking-widest text-primary mb-4 flex items-center gap-2">
                                            <span class="material-symbols-outlined text-lg">event</span>
                                            Voyage Timeline
                                        </h3>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Set
                                                    Sail</label>
                                                <input v-model="form.start_date"
                                                    class="w-full bg-ocean-deep border-[#1a3a41] rounded-lg text-xs py-2 text-white focus:ring-primary"
                                                    type="date" />
                                                <p v-if="errs.start_date" class="text-red-500 text-xs mt-1">{{ errs.start_date }}</p>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Drop
                                                    Anchor</label>
                                                <input v-model="form.end_date"
                                                    class="w-full bg-ocean-deep border-[#1a3a41] rounded-lg text-xs py-2 text-white focus:ring-primary"
                                                    type="date" />
                                                <p v-if="errs.end_date" class="text-red-500 text-xs mt-1">{{ errs.end_date }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                            <div class="flex items-center justify-between pt-10 border-t border-[#224249]">
                                <button type="button" @click="prevStep()"
                                    class="bg-primary hover:bg-primary/80 text-background-dark px-8 py-2 rounded-lg font-black text-sm transition-all shadow-lg flex items-center gap-2">
                                    <span class="material-symbols-outlined text-lg">arrow_back</span>
                                    Prev: Brief Details
                                </button>
                                <button type="submit"
                                    class="bg-pirate-gold text-ocean-deep hover:bg-pirate-gold/80 px-8 py-2 rounded-lg font-black text-sm transition-all shadow-lg flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm">rocket_launch</span>
                                    Create Brief
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <footer
                class="mt-auto border-t border-slate-200 dark:border-[#224249] bg-white dark:bg-[#102023] px-8 py-3 flex items-center justify-between text-[11px] font-medium text-slate-500">
                <div class="flex gap-6">
                    <span class="flex items-center gap-1.5"><span class="size-2 bg-emerald-500 rounded-full"></span>
                        System: All Systems Go</span>
                    <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[14px]">anchor</span>
                        Anchored to: Marine HQ
                        Server</span>
                </div>
                <div class="flex gap-4">
                    <span>V 2.4.0-ARABASTA</span>
                    <a class="hover:text-primary transition-colors" href="#">Help Center</a>
                </div>
            </footer>
        </main>
    </NuxtLayout>
</template>