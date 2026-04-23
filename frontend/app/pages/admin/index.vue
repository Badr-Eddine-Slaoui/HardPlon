<script setup lang="ts">
    import { useUser } from '~~/stores/user';
import { useAdminStore } from '../../../stores/admin';
    const admin = useAdminStore()
    const user_store = useUser();

    onMounted(async() => {
        await admin.fetchStatistics();
        await user_store.fetchUsers();
    })

    definePageMeta({
        middleware: ['auth', 'admin']
    })
</script>

<template>
    <NuxtLayout name="admin">
        <main class="flex-1 flex flex-col min-w-0 bg-background-light dark:bg-background-dark overflow-y-auto">
            <!-- Content Area -->
            <div class="p-10 space-y-8 max-w-[1400px] mx-auto w-full">
                <!-- Hero -->
                <section class="flex flex-col gap-2">
                    <h1 class="text-4xl font-black tracking-tight text-slate-900 dark:text-white">
                        Set sail for your next sprint <span class="text-primary">⚓</span>
                    </h1>
                    <p class="text-slate-500 dark:text-[#90c1cb] text-lg">Welcome back, Admiral. The Grand Line Academy
                        is
                        operating at full steam today.</p>
                </section>
                <!-- Stats Grid -->
                <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="stat-card-blue p-6 rounded-xl flex flex-col gap-4 shadow-xl">
                        <div class="flex justify-between items-start">
                            <div class="p-2 rounded-lg bg-white/10 text-white">
                                <span class="material-symbols-outlined">school</span>
                            </div>
                            <span class="text-emerald-400 text-sm font-bold flex items-center">+12% <span
                                    class="material-symbols-outlined text-sm">trending_up</span></span>
                        </div>
                        <div>
                            <p class="text-slate-300 text-sm font-medium">Total Students</p>
                            <h3 class="text-3xl font-bold mt-1">{{ admin.statisticts?.students_count ?? "N/A" }}</h3>
                        </div>
                    </div>
                    <div class="stat-card-gold p-6 rounded-xl flex flex-col gap-4 shadow-xl">
                        <div class="flex justify-between items-start">
                            <div class="p-2 rounded-lg bg-white/10 text-white">
                                <span class="material-symbols-outlined">person_pin</span>
                            </div>
                            <span class="text-rose-400 text-sm font-bold flex items-center">-2% <span
                                    class="material-symbols-outlined text-sm">trending_down</span></span>
                        </div>
                        <div>
                            <p class="text-slate-300 text-sm font-medium">Total Teachers</p>
                            <h3 class="text-3xl font-bold mt-1">{{ admin.statisticts?.teachers_count ?? "N/A" }}</h3>
                        </div>
                    </div>
                    <div class="stat-card-blue p-6 rounded-xl flex flex-col gap-4 shadow-xl">
                        <div class="flex justify-between items-start">
                            <div class="p-2 rounded-lg bg-white/10 text-white">
                                <span class="material-symbols-outlined">class</span>
                            </div>
                            <span class="text-emerald-400 text-sm font-bold flex items-center">+5% <span
                                    class="material-symbols-outlined text-sm">trending_up</span></span>
                        </div>
                        <div>
                            <p class="text-slate-300 text-sm font-medium">Active Classes</p>
                            <h3 class="text-3xl font-bold mt-1">{{ admin.statisticts?.class_groups_count ?? "N/A" }}</h3>
                        </div>
                    </div>
                    <div class="stat-card-gold p-6 rounded-xl flex flex-col gap-4 shadow-xl">
                        <div class="flex justify-between items-start">
                            <div class="p-2 rounded-lg bg-white/10 text-white">
                                <span class="material-symbols-outlined">assignment</span>
                            </div>
                            <span class="text-emerald-400 text-sm font-bold flex items-center">+8% <span
                                    class="material-symbols-outlined text-sm">trending_up</span></span>
                        </div>
                        <div>
                            <p class="text-slate-300 text-sm font-medium">Open Briefs</p>
                            <h3 class="text-3xl font-bold mt-1">{{ admin.statisticts?.briefs_count ?? "N/A" }}</h3>
                        </div>
                    </div>
                </section>
                <!-- Quick Actions -->
                <section class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold tracking-tight">Quick Actions</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <NuxtLink to="/admin/user/create"
                            class="flex items-center gap-4 p-4 rounded-xl bg-white dark:bg-[#1a2e33] border border-slate-200 dark:border-[#224249] hover:border-primary transition-all group shadow-sm">
                            <div
                                class="size-12 rounded-lg bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors">
                                <span class="material-symbols-outlined">person_add</span>
                            </div>
                            <div class="text-left">
                                <p class="font-bold">Add New User</p>
                                <p class="text-xs text-slate-500 dark:text-[#90c1cb]">Register student or faculty</p>
                            </div>
                        </NuxtLink>
                        <NuxtLink to="/admin/scholar-year/create"
                            class="flex items-center gap-4 p-4 rounded-xl bg-white dark:bg-[#1a2e33] border border-slate-200 dark:border-[#224249] hover:border-primary transition-all group shadow-sm">
                            <div
                                class="size-12 rounded-lg bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors">
                                <span class="material-symbols-outlined">event_note</span>
                            </div>
                            <div class="text-left">
                                <p class="font-bold">New Scholar Year</p>
                                <p class="text-xs text-slate-500 dark:text-[#90c1cb]">Plan upcoming semesters</p>
                            </div>
                        </NuxtLink>
                        <NuxtLink to="/admin/formation/create"
                            class="flex items-center gap-4 p-4 rounded-xl bg-white dark:bg-[#1a2e33] border border-slate-200 dark:border-[#224249] hover:border-primary transition-all group shadow-sm">
                            <div
                                class="size-12 rounded-lg bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors">
                                <span class="material-symbols-outlined">school</span>
                            </div>
                            <div class="text-left">
                                <p class="font-bold">New Formation</p>
                                <p class="text-xs text-slate-500 dark:text-[#90c1cb]">Create a new formation</p>
                            </div>
                        </NuxtLink>
                    </div>
                </section>
                <!-- Table Section -->
                <section class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold tracking-tight">Recent Crew Updates</h2>
                    </div>
                    <div
                        class="rounded-xl overflow-hidden bg-white dark:bg-[#1a2e33] border border-slate-200 dark:border-[#224249] shadow-sm">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-slate-50 dark:bg-[#224249]">
                                <tr>
                                    <th
                                        class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-[#90c1cb]">
                                        Member</th>
                                    <th
                                        class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-[#90c1cb]">
                                        Role</th>
                                    <th
                                        class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-[#90c1cb]">
                                        Assignment</th>
                                    <th
                                        class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-[#90c1cb]">
                                        Status</th>
                                    <th
                                        class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-[#90c1cb]">
                                        Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-[#224249]">
                                <template v-if="user_store.users?.length as number > 0">
                                    <tr v-for="user in user_store.users" class="hover:bg-slate-50 dark:hover:bg-[#224249]/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="size-8 rounded-full bg-slate-200 bg-cover bg-center"
                                                    :data-alt="`${user.first_name} ${user.last_name} avatar`"
                                                    style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuAEr6C50RayG3x1EuGHheEDzKrTKHL6n68E6TSopvqJ3_J0zfmo65dUtU2tjDO6DYF3Sy86ajVdeWyJnUJR6Hpiae0FR3FJmvLV259Mj-zter-3iVkpYcX6GtWQX7tt4i9yXtxnm-wuqxzo1q2OkZH9Z0g_lDzK7rje9flBXvCFcocYBKQ_Wl0cSLp1ma3UsV7h8V7Kd-PoE1conyr4AJu06N-_MuHQprzUW47_LkVfvKHUH8LTuf4nLg3YVH7g00_DE-y9OsfKUug')">
                                                </div>
                                                <span class="font-semibold text-sm">{{ user.first_name }} {{ user.last_name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm">{{ user.role }}</td>
                                        <td class="px-6 py-4 text-sm">{{ user.email }}</td>
                                        <td class="px-6 py-4">
                                            <span v-if="user.is_active"
                                                class="px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-tighter bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400">Enrolled</span>
                                            <span v-else
                                                class="px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-tighter bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-400">Inactive</span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-500 dark:text-[#90c1cb]">
                                            {{ new Date(user.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) }}
                                        </td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-slate-500 dark:text-[#90c1cb]">
                                            No recent updates available.
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
            <footer
                class="p-10 border-t border-slate-200 dark:border-[#224249] text-center text-slate-400 dark:text-[#90c1cb] text-sm">
                © 1524 Grand Line Academy Management System • Version 4.2.0 "New World"
            </footer>
        </main>
    </NuxtLayout>
</template>