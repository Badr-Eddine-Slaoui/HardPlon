<script setup lang="ts">
    import { useUser } from '~~/stores/user';
    import type { ReturnData } from '~~/types/api';

    useHead({
        title: 'Admin Dashboard - Edit User'
    })

    const id: number = parseInt(useRoute().params?.id as string)
    const role: string = useRoute().params?.role as string

    const store = useUser()

    const form = reactive({
        first_name: '',
        last_name: '',
        age: 0,
        email: '',
        cin: '',
        phone: '+212 ',
        role: ''
    })

    onMounted(async() => {
        await store.fetchUser(role.toUpperCase(), id);
        form.first_name = store.user?.first_name as string
        form.last_name = store.user?.last_name as string
        form.age = store.user?.age as number
        form.email = store.user?.email as string
        form.cin = store.user?.cin as string
        form.phone = store.user?.phone as string
        form.role = store.user?.role as string
    })

    const errs = ref({
        first_name: '',
        last_name: '',
        age: '',
        email: '',
        cin: '',
        phone: '',
        role: '',
        message: ''
    })

    const submit = async () => {
        const res : ReturnData<any> = await store.updateUser(id, form);
        if(res.success) {
            navigateTo('/admin/user')
        }
        
        if(res.errors) {
            errs.value = res.errors
        }
    }
</script>

<template>
    <NuxtLayout name="admin">
        <main class="px-6 lg:px-40 flex flex-col justify-center py-10 max-w-[1200px] mx-auto">
            <!-- Breadcrumbs -->
            <div class="flex flex-wrap gap-2 mb-4">
                <NuxtLink class="text-[#90c1cb] text-sm font-medium leading-normal hover:text-primary flex items-center gap-1"
                    to="/admin/user">
                    <span class="material-symbols-outlined text-sm">grid_view</span> Fleet Management
                </NuxtLink>
                <span class="text-[#90c1cb] text-sm font-medium leading-normal">/</span>
                <span class="text-slate-900 dark:text-white text-sm font-medium leading-normal">Edit Member</span>
            </div>
            <!-- Page Heading -->
            <div class="flex flex-wrap justify-between items-end gap-3 mb-8">
                <div class="flex min-w-72 flex-col gap-2">
                    <h1
                        class="text-slate-900 dark:text-white text-4xl font-black leading-tight tracking-[-0.033em] font-display">
                        Edit Member</h1>
                    <p class="text-[#90c1cb] text-base font-normal leading-normal">Expanding the fleet's horizons with new
                        talent and devil fruit users.</p>
                </div>
                <NuxtLink to="/admin/user"
                    class="flex min-w-[84px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-[#224249] text-white text-sm font-bold leading-normal tracking-[0.015em] hover:bg-[#315f68] transition-all">
                    <span class="truncate">View All Members</span>
                </NuxtLink>
            </div>
            <!-- Form Container -->
            <form @submit.prevent="submit" class="bg-[#182f34]/30 rounded-xl p-8 nautical-border shadow-2xl backdrop-blur-sm">
                <div class="max-w-2xl">
                    <!-- User Identity Section -->
                    <div class="flex items-center gap-2 mb-6">
                        <span class="material-symbols-outlined text-primary">person_search</span>
                        <h2 class="text-white tracking-light text-2xl font-bold leading-tight font-display">User Identity
                        </h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <!-- First Name -->
                        <div class="flex flex-col gap-2">
                            <label class="text-white text-sm font-medium leading-normal px-1">First Name</label>
                            <div class="relative group">
                                <span
                                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#90c1cb] group-focus-within:text-primary transition-colors">badge</span>
                                <input v-model="form.first_name"
                                    class="form-input flex w-full rounded-lg text-white focus:outline-0 focus:ring-2 focus:ring-primary/50 border border-[#315f68] bg-[#102023] h-14 placeholder:text-[#90c1cb]/50 pl-12 pr-4 text-base font-normal"
                                    placeholder="e.g. Monkey D. Luffy" />
                            </div>
                            <p v-if="errs.first_name" class="text-red-500 text-sm mt-2">
                                {{ errs.first_name }}
                            </p>
                        </div>
                        <!-- Last Name -->
                        <div class="flex flex-col gap-2">
                            <label class="text-white text-sm font-medium leading-normal px-1">Last Name</label>
                            <div class="relative group">
                                <span
                                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#90c1cb] group-focus-within:text-primary transition-colors">badge</span>
                                <input v-model="form.last_name"
                                    class="form-input flex w-full rounded-lg text-white focus:outline-0 focus:ring-2 focus:ring-primary/50 border border-[#315f68] bg-[#102023] h-14 placeholder:text-[#90c1cb]/50 pl-12 pr-4 text-base font-normal"
                                    placeholder="e.g. Monkey D. Luffy" />
                            </div>
                            <p v-if="errs.last_name" class="text-red-500 text-sm mt-2">
                                {{ errs.last_name }}
                            </p>
                        </div>
                        <!-- Age -->
                        <div class="flex flex-col gap-2">
                            <label class="text-white text-sm font-medium leading-normal px-1">Age</label>
                            <div class="relative group">
                                <span
                                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#90c1cb] group-focus-within:text-primary transition-colors">badge</span>
                                <input v-model="form.age"
                                    class="form-input flex w-full rounded-lg text-white focus:outline-0 focus:ring-2 focus:ring-primary/50 border border-[#315f68] bg-[#102023] h-14 placeholder:text-[#90c1cb]/50 pl-12 pr-4 text-base font-normal"
                                    placeholder="18" min="18" max="33" type="number" />
                            </div>
                            <p v-if="errs.age" class="text-red-500 text-sm mt-2">
                                {{ errs.age }}
                            </p>
                        </div>
                        <!-- Email -->
                        <div class="flex flex-col gap-2">
                            <label class="text-white text-sm font-medium leading-normal px-1">Email Address</label>
                            <div class="relative group">
                                <span
                                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#90c1cb] group-focus-within:text-primary transition-colors">mail</span>
                                <input v-model="form.email"
                                    class="form-input flex w-full rounded-lg text-white focus:outline-0 focus:ring-2 focus:ring-primary/50 border border-[#315f68] bg-[#102023] h-14 placeholder:text-[#90c1cb]/50 pl-12 pr-4 text-base font-normal"
                                    placeholder="captain@thousand-sunny.com" type="email" />
                            </div>
                            <p v-if="errs.email" class="text-red-500 text-sm mt-2">
                                {{ errs.email }}
                            </p>
                        </div>
                        <!-- Phone -->
                        <div class="flex flex-col gap-2">
                            <label class="text-white text-sm font-medium leading-normal px-1">Phone Number</label>
                            <div class="relative group">
                                <span
                                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#90c1cb] group-focus-within:text-primary transition-colors">mail</span>
                                <input v-model="form.phone"
                                    class="form-input flex w-full rounded-lg text-white focus:outline-0 focus:ring-2 focus:ring-primary/50 border border-[#315f68] bg-[#102023] h-14 placeholder:text-[#90c1cb]/50 pl-12 pr-4 text-base font-normal"
                                    placeholder="+212 612345678" type="tel" />
                            </div>
                            <p v-if="errs.phone" class="text-red-500 text-sm mt-2">
                                {{ errs.phone }}
                            </p>
                        </div>
                        <!-- CIN -->
                        <div class="flex flex-col gap-2">
                            <label class="text-white text-sm font-medium leading-normal px-1">CIN</label>
                            <div class="relative group">
                                <span
                                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#90c1cb] group-focus-within:text-primary transition-colors">mail</span>
                                <input v-model="form.cin"
                                    class="form-input flex w-full rounded-lg text-white focus:outline-0 focus:ring-2 focus:ring-primary/50 border border-[#315f68] bg-[#102023] h-14 placeholder:text-[#90c1cb]/50 pl-12 pr-4 text-base font-normal"
                                    placeholder="HH123456" type="text"/>
                            </div>
                            <p v-if="errs.cin" class="text-red-500 text-sm mt-2">
                                {{ errs.cin }}
                            </p>
                        </div>
                        <!-- Role Selection -->
                        <div class="flex flex-col gap-2">
                            <label class="text-white text-sm font-medium leading-normal px-1">Select Position (Role)</label>
                            <div class="relative group">
                                <span
                                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#90c1cb] group-focus-within:text-primary transition-colors">military_tech</span>
                                <select v-model="form.role"
                                    class="form-select flex w-full rounded-lg text-white focus:outline-0 focus:ring-2 focus:ring-primary/50 border border-[#315f68] bg-[#102023] h-14 pl-12 pr-4 text-base font-normal appearance-none">
                                    <option selected value="STUDENT">Student / Apprentice</option>
                                    <option value="TEACHER">Teacher / Navigator</option>
                                    <option value="ADMIN">Admin / Admiral</option>
                                </select>
                            </div>
                            <p v-if="errs.role" class="text-red-500 text-sm mt-2">
                                {{ errs.role }}
                            </p>
                        </div>
                    </div>
                    <!-- Conditional Fields: Teacher (Optional Display/Hidden logic) -->
                    <div class="mb-8 p-6 bg-[#224249]/20 rounded-lg border-l-4 border-primary/40 hidden"
                        id="teacher-fields">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="material-symbols-outlined text-primary">menu_book</span>
                            <h3 class="text-white text-lg font-bold font-display">Instructor Credentials</h3>
                        </div>
                        <div class="flex flex-col gap-2 max-w-sm">
                            <label class="text-white text-sm font-medium leading-normal px-1">Primary Subject / Fruit
                                Ability</label>
                            <input
                                class="form-input flex w-full rounded-lg text-white focus:outline-0 focus:ring-2 focus:ring-primary/50 border border-[#315f68] bg-[#102023] h-12 px-4 text-base font-normal"
                                placeholder="e.g. Advanced Haki Studies" value="" />
                        </div>
                    </div>
                    <!-- Footer Actions -->
                    <div class="flex flex-wrap items-center justify-end gap-4 mt-12 pt-8 border-t border-[#315f68]">
                        <NuxtLink to="/admin/user"
                            class="flex min-w-[120px] items-center justify-center rounded-lg h-14 px-8 bg-transparent text-[#e53e3e] border border-dark-red/50 text-base font-bold hover:bg-dark-red/10 transition-all uppercase tracking-wider">
                            Cancel
                        </NuxtLink>
                        <button type="submit"
                            class="flex min-w-[200px] items-center justify-center rounded-lg h-14 px-10 bg-gold text-[#102023] text-base font-black hover:scale-[1.02] active:scale-95 transition-all shadow-[0_0_20px_rgba(255,215,0,0.3)] uppercase tracking-wider gap-2">
                            <span class="material-symbols-outlined font-bold">anchor</span>
                            Add to Fleet
                        </button>
                    </div>
                </div>
            </form>
            <!-- Decorative Map Element -->
            <div class="mt-12 opacity-30 flex flex-col items-center">
                <span class="material-symbols-outlined text-6xl text-primary">explore</span>
                <p class="text-sm uppercase tracking-[0.2em] mt-2 font-medium">Navigating to the One Piece</p>
            </div>
        </main>
    </NuxtLayout>
</template>