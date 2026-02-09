<script setup lang="ts">
    import TomSelect from 'tom-select'
    import 'tom-select/dist/css/tom-select.css'
    import { useClassGroup } from '~~/stores/class_group';
    import { useFormation } from '~~/stores/formation';
    import type { ReturnData } from '~~/types/api';
    import type { User } from '../../../../types/user';
    import { useUser } from '~~/stores/user';

    useHead({
        title: 'Admin Dashboard - Create Class Group'
    })

    const classes = useClassGroup()
    const store = useFormation()
    const userStore = useUser()

    const main_teachers: Ref<User[] | null> = ref([])
    const sub_teachers : Ref<User[] | null> = ref([])
    const students: Ref<User[] | null> = ref([])

    onMounted(async() => {
        await store.fetchFormations();
        main_teachers.value = await userStore.fetchTeachersByRole('main')
        sub_teachers.value = await userStore.fetchTeachersByRole('sub')
        students.value = await userStore.fetchStudents()
        await nextTick()

        new TomSelect('#students', {
            plugins: ['remove_button'],
            placeholder: 'Add Students...',
            create: true,
        })
    })

    const form = reactive({
        name: '',
        image_url: '',
        description: '',
        formation_id: 0,
        main_teacher_id: 0,
        sub_teacher_id: 0,
        capacity: 0,
        students_ids: <number[]>[]
    })

    const errs = ref({
        name: '',
        image_url: '',
        description: '',
        formation_id: '',
        main_teacher_id: '',
        sub_teacher_id: '',
        capacity: '',
        students_ids: '',
        message: ''
    })

    const submit = async () => {
        const res : ReturnData<any> = await classes.createClassGroup(form);
        if(res.success) {
            navigateTo('/admin/class')
        }
        
        if(res.errors) {
            errs.value = res.errors
        }
    }

    definePageMeta({
        middleware: ['auth', 'admin']
    })
</script>

<template>
    <NuxtLayout name="admin">
        <main class="flex w-full min-h-screen overflow-hidden">
            <section
                class="flex-1 overflow-y-auto bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-[#1a353b] via-background-dark to-background-dark p-10 flex justify-center items-start">
                <div
                    class="max-w-2xl w-full bg-background-dark/80 backdrop-blur-sm nautical-border p-8 md:p-12 shadow-[0_0_60px_-15px_rgba(13,204,242,0.2)]">
                    <div class="absolute top-4 right-4 text-[#224249]">
                        <span class="material-symbols-outlined text-4xl">explore</span>
                    </div>
                    <div class="mb-10 text-center">
                        <div
                            class="inline-flex items-center justify-center p-3 bg-primary/10 rounded-full mb-4 border border-primary/20">
                            <span class="material-symbols-outlined text-primary text-4xl">assignment_add</span>
                        </div>
                        <h2 class="text-3xl font-bold text-white font-display tracking-tight uppercase">Commission a New
                            Fleet</h2>
                        <div class="h-1 w-32 bg-primary/30 mx-auto mt-4 rounded-full"></div>
                    </div>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-sm font-bold text-[#90c1cb] uppercase tracking-wider flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm">badge</span>
                                    Fleet Name
                                </label>
                                <input v-model="form.name" class="input-field" placeholder="e.g. Whitebeard 2nd Division" type="text" />
                                <span v-if="errs.name" class="text-red-500 text-sm mt-1">{{ errs.name }}</span>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-sm font-bold text-[#90c1cb] uppercase tracking-wider flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm">image</span>
                                    Image URL
                                </label>
                                <input v-model="form.image_url" class="input-field" placeholder="e.g. https://example.com/image.jpg" type="text"/>
                                <span v-if="errs.image_url" class="text-red-500 text-sm mt-1">{{ errs.image_url }}</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-sm font-bold text-[#90c1cb] uppercase tracking-wider flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm">grid_view</span>
                                    Assign Formation
                                </label>
                                <select v-model="form.formation_id" class="input-field appearance-none">
                                    <option disabled selected value="">Select Strategy</option>
                                    <option v-for="formation in store.formations" :key="formation.id" :value="formation.id">{{ formation.title }}</option>
                                </select>
                                <span v-if="errs.formation_id" class="text-red-500 text-sm mt-1">{{ errs.formation_id }}</span>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-sm font-bold text-[#90c1cb] uppercase tracking-wider flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm">numbers</span>
                                    Fleet Size
                                </label>
                                <input v-model="form.capacity" class="input-field" max="1000" min="1" placeholder="Capacity"
                                    type="number"/>
                                <span v-if="errs.capacity" class="text-red-500 text-sm mt-1">{{ errs.capacity }}</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-sm font-bold text-[#90c1cb] uppercase tracking-wider flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm">group</span>
                                    Main Teacher
                                </label>
                                <select v-model="form.main_teacher_id" class="input-field appearance-none">
                                    <option disabled selected value="">Select Main Teacher</option>
                                    <option v-for="teacher in main_teachers" :key="teacher.id" :value="teacher.id">{{ teacher.first_name }} {{ teacher.last_name }}</option>
                                </select>
                                <span v-if="errs.main_teacher_id" class="text-red-500 text-sm mt-1">{{ errs.main_teacher_id }}</span>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label
                                    class="text-sm font-bold text-[#90c1cb] uppercase tracking-wider flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm">group</span>
                                    Sub Teacher
                                </label>
                                <select v-model="form.sub_teacher_id" class="input-field appearance-none">
                                    <option disabled selected value="">Select Sub Teacher</option>
                                    <option v-for="teacher in sub_teachers" :key="teacher.id" :value="teacher.id">{{ teacher.first_name }} {{ teacher.last_name }}</option>
                                </select>
                                <span v-if="errs.sub_teacher_id" class="text-red-500 text-sm mt-1">{{ errs.sub_teacher_id }}</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label
                                class="text-sm font-bold text-[#90c1cb] uppercase tracking-wider flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">person_search</span>
                                Assign Students
                            </label>
                            <div class="relative">
                                <span
                                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#90c1cb]">search</span>
                                <select v-model="form.students_ids" class="input-field appearance-none" id="students" multiple >
                                    <option disabled selected value="">Select Students</option>
                                    <option v-for="student in students" :key="student.id" :value="student.id">{{ student.first_name }} {{ student.last_name }}</option>
                                </select>
                            </div>
                            <span v-if="errs.students_ids" class="text-red-500 text-sm mt-1">{{ errs.students_ids }}</span>
                            <p class="text-[10px] text-[#5a8b96] italic">Only certified commanders from the Grand Line are
                                eligible.</p>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label
                                class="text-sm font-bold text-[#90c1cb] uppercase tracking-wider flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">person_search</span>
                                Description
                            </label>
                            <textarea v-model="form.description" class="input-field appearance-none" rows="6" placeholder="Fleet Description">
                                </textarea>
                            <span v-if="errs.description" class="text-red-500 text-sm mt-1">{{ errs.description }}</span>
                        </div>
                        <div class="pt-8 flex flex-col sm:flex-row gap-4">
                            <button
                                class="flex-1 bg-pirate-gold hover:bg-pirate-gold-dark text-background-dark font-bold text-lg py-4 rounded-lg flex items-center justify-center gap-2 transition-all hover:scale-[1.02] shadow-lg shadow-pirate-gold/20 uppercase tracking-tighter"
                                type="submit">
                                <span class="material-symbols-outlined">anchor</span>
                                Anchor Fleet
                            </button>
                            <NuxtLink to="/admin/class"
                                class="sm:w-1/3 bg-transparent border-2 border-marine-red text-marine-red hover:bg-marine-red hover:text-white font-bold text-lg py-4 rounded-lg flex items-center justify-center gap-2 transition-all uppercase tracking-tighter"
                                >
                                <span class="material-symbols-outlined">close</span>
                                Abort
                            </NuxtLink>
                        </div>
                    </form>
                    <div class="mt-8 flex items-center justify-between opacity-20">
                        <span class="material-symbols-outlined text-4xl">sailing</span>
                        <div class="h-[1px] flex-1 mx-4 bg-[#90c1cb]"></div>
                        <span class="material-symbols-outlined text-4xl">water_drop</span>
                    </div>
                </div>
            </section>
        </main>
    </NuxtLayout>
</template>