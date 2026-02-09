<script setup lang="ts">
    import { useUser } from '../../../../stores/user';

    useHead({
        title: `Admin Dashboard - Users`
    })

    const store = useUser();

    onMounted(async() => {
        await store.fetchUsers();
    })

    function openArchiveModal(id: number): void {
        const modal = document.getElementById('archive-modal') as HTMLElement;
        modal.classList.remove('hidden');

        const form = modal.querySelector('form') as HTMLFormElement;
        form.onsubmit = async (e) => {
            e.preventDefault();
            await store.archiveUser(id);
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
            await store.restoreUser(id);
            closeResurrectModal();
        }
    }

    function closeResurrectModal(): void {
        const modal = document.getElementById('resurrect-modal') as HTMLElement;
        modal.classList.add('hidden');
    }
</script>

<template>
    <NuxtLayout name="admin">
        <main v-if="store.users?.length as number > 0" class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Breadcrumbs & Heading Section -->
            <div class="p-8 pb-0">
                <!-- Breadcrumbs -->
                <nav class="flex items-center gap-2 text-sm font-medium text-primary/50 mb-4">
                    <a class="hover:text-primary transition-colors" href="#">Main Deck</a>
                    <span class="material-symbols-outlined text-xs">chevron_right</span>
                    <span class="text-primary font-bold">Crew Registry</span>
                </nav>
                <!-- PageHeading -->
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
                    <div>
                        <h2 class="text-4xl font-black tracking-tight text-white mb-2">Crew Registry</h2>
                        <p class="text-primary/60 max-w-lg">Manage your fleet of scholars and navigators. Assign roles,
                            verify statuses, and monitor boarding logs.</p>
                    </div>
                    <NuxtLink to="/admin/user/create"
                        class="flex items-center justify-center gap-2 bg-primary hover:bg-primary/90 text-background-dark px-6 py-3 rounded-lg font-bold transition-transform active:scale-95">
                        <span class="material-symbols-outlined">person_add</span>
                        Recruit New Member
                    </NuxtLink>
                </div>
                <!-- Main Table Section -->
                <div class="bg-primary/5 border border-primary/10 rounded-xl overflow-hidden shadow-2xl">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-primary/10 border-b border-primary/10">
                                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-primary/70">
                                        Name</th>
                                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-primary/70">
                                        Role</th>
                                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-primary/70">
                                        Email Address</th>
                                    <th
                                        class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-primary/70 text-center">
                                        Status</th>
                                    <th
                                        class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-primary/70 text-right">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-primary/5">
                                <template v-for="user in store.users" :key="user.id">
                                    <tr v-if="user.is_active" class="hover:bg-primary/5 transition-colors group">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="size-10 rounded-full bg-cover bg-center border border-primary/20"
                                                    data-alt="Avatar of Monkey D. Luffy"
                                                    style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuAAvFZsSLchKhZapCh9uaFUIqAhXayOUbqp-DMO7tTomui43odUOQ6ehlQljC9IFY1NNGoTMlWv2eNJv0tRPlIqBz7PJFCxlVX9HUhVazZmUKNCuPuBd627c68l4jmfTv5wtTObpX78GQZeV6sxrwKAfKEC3Uw6DNIa6pDKUsb63jqyKN09Qj13qu34JNC7tyolt-IA2pbn74l23ApbTz5gMIzOGmVWeVuxG55vwEU91HyKbf5nEuht1kYr2q-NivViTzv5oVTQdeY');">
                                                </div>
                                                <div>
                                                    <p
                                                        class="font-bold text-white group-hover:text-primary transition-colors">
                                                        {{ user.first_name }} {{ user.last_name }}</p>
                                                    <p class="text-xs text-primary/40 italic">Joined
                                                        {{ user.created_at }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span v-if="user.role === 'ADMIN'"
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-admin-gold/20 text-admin-gold ring-1 ring-inset ring-admin-gold/30">
                                                Administrator
                                            </span>
                                            <span v-else-if="user.role === 'TEACHER'"
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-teacher-turquoise/20 text-teacher-turquoise ring-1 ring-inset ring-teacher-turquoise/30">
                                                Teacher
                                            </span>
                                            <span v-else-if="user.role === 'STUDENT'"
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-student-blue/20 text-student-blue ring-1 ring-inset ring-student-blue/30">
                                                Student
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-primary/60">{{ user.email }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <span
                                                class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-400">
                                                <span class="size-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                                Active
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <NuxtLink :to="`/admin/user/${user.role.toLowerCase()}/${user.id}`"
                                                    class="p-2 hover:bg-primary/20 rounded-lg text-primary/60 hover:text-primary transition-all">
                                                    <span
                                                        class="material-symbols-outlined text-[20px]">visibility</span>
                                                </NuxtLink>
                                                <NuxtLink :to="`/admin/user/${user.role.toLowerCase()}/${user.id}/edit`"
                                                    class="p-2 hover:bg-primary/20 rounded-lg text-primary/60 hover:text-primary transition-all">
                                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                                </NuxtLink>
                                                <button @click="openArchiveModal(user.id)"
                                                    class="p-2 hover:bg-red-500/10 rounded-lg text-primary/60 hover:text-red-400 transition-all">
                                                    <span class="material-symbols-outlined text-[20px]">archive</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-else class="hover:bg-primary/5 transition-colors group opacity-70">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="size-10 rounded-full bg-cover bg-center border border-primary/20"
                                                    data-alt="Avatar of Monkey D. Luffy"
                                                    style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuAAvFZsSLchKhZapCh9uaFUIqAhXayOUbqp-DMO7tTomui43odUOQ6ehlQljC9IFY1NNGoTMlWv2eNJv0tRPlIqBz7PJFCxlVX9HUhVazZmUKNCuPuBd627c68l4jmfTv5wtTObpX78GQZeV6sxrwKAfKEC3Uw6DNIa6pDKUsb63jqyKN09Qj13qu34JNC7tyolt-IA2pbn74l23ApbTz5gMIzOGmVWeVuxG55vwEU91HyKbf5nEuht1kYr2q-NivViTzv5oVTQdeY');">
                                                </div>
                                                <div>
                                                    <p
                                                        class="font-bold text-white group-hover:text-primary transition-colors">
                                                        {{ user.first_name }} {{ user.last_name }}</p>
                                                    <p class="text-xs text-primary/40 italic">Joined
                                                        {{ user.created_at }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span v-if="user.role === 'ADMIN'"
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-admin-gold/20 text-admin-gold ring-1 ring-inset ring-admin-gold/30">
                                                Administrator
                                            </span>
                                            <span v-else-if="user.role === 'TEACHER'"
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-teacher-turquoise/20 text-teacher-turquoise ring-1 ring-inset ring-teacher-turquoise/30">
                                                Teacher
                                            </span>
                                            <span v-else-if="user.role === 'STUDENT'"
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-student-blue/20 text-student-blue ring-1 ring-inset ring-student-blue/30">
                                                Student
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-primary/60">{{ user.email }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <span
                                                class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-500">
                                                <span class="size-1.5 rounded-full bg-slate-500"></span>
                                                Inactive
                                            </span>
                                        </td>
                                        <td class="px-8 py-6 text-right">
                                            <div class="flex items-center justify-end gap-3">
                                                <button @click="openResurrectModal(user.id)"
                                                    class="p-2 rounded-lg text-rose-400 bg-rose-400/10 transition-all"
                                                    title="Restore Grade">
                                                    <span class="material-symbols-outlined">unarchive</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Empty Space at bottom -->
            <div class="p-8 mt-auto">
                <div
                    class="border-2 border-dashed border-primary/10 rounded-xl p-12 text-center flex flex-col items-center gap-4">
                    <span class="material-symbols-outlined text-4xl text-primary/20">anchor</span>
                    <p class="text-primary/30 font-medium">No archived members in this view. Use the archive box icon
                        to store profiles.</p>
                </div>
            </div>
        </main>
        <main v-else class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation Bar -->
            <header
                class="h-16 flex items-center justify-between px-8 bg-white dark:bg-[#1a170d]/50 border-b border-slate-200 dark:border-[#3a331d] backdrop-blur-md">
                <div class="flex items-center gap-4">
                    <nav class="flex items-center gap-2 text-sm">
                        <a class="text-muted-gold hover:text-primary transition-colors" href="#">Fleet</a>
                        <span class="text-muted-gold/50">/</span>
                        <span class="font-semibold text-slate-900 dark:text-white">Crew Management</span>
                    </nav>
                </div>
            </header>
            <!-- Dashboard Content -->
            <div class="flex-1 overflow-y-auto p-8 flex flex-col items-center justify-center">
                <!-- Empty State Component -->
                <div
                    class="max-w-xl w-full flex flex-col items-center text-center animate-in fade-in slide-in-from-bottom-4 duration-1000">
                    <!-- Soft Illustration -->
                    <div class="relative w-full aspect-video mb-10 rounded-2xl overflow-hidden group">
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-background-dark via-transparent to-transparent z-10">
                        </div>
                        <div class="absolute inset-0 bg-[url('https://placeholder.pics/svg/800x450')] bg-cover bg-center transition-transform duration-700 group-hover:scale-105"
                            data-alt="Quiet wooden ship deck at night under stars"
                            style='background-image: linear-gradient(rgba(34, 29, 16, 0.4), rgba(34, 29, 16, 0.4)), url("https://lh3.googleusercontent.com/aida-public/AB6AXuAONwl7k_2pEOZS6RF_6qU2xDcisAqPQ-11y9TABoOJmaUbRR87MtTG5ztXLM2l_x4Di42d7DFMATuv8UV1XNQT4fC3tpTJOVtbdLZ_VnFL0IlBCheaN5sJRSb0hpwfvDoGM_HxgqcuGv7EUVlxb38MpHQ2k6ue7eu2tSVNub2ZXi3anQYT63H9n_agN5gy-OmEri-ZZh585z-t7yBojwor1GY4u1n6iiim91XoFQ7muLvlOBByiMM7RsMvm5FkFDZe6LG_5iEHBe0");'>
                        </div>
                        <!-- Decorative floating elements -->
                        <div class="absolute top-1/4 right-1/4 z-20 animate-bounce">
                            <span class="material-symbols-outlined text-primary/40 text-4xl">star</span>
                        </div>
                        <div class="absolute bottom-1/4 left-1/3 z-20 text-white/20">
                            <span class="material-symbols-outlined text-6xl">waves</span>
                        </div>
                    </div>
                    <!-- Content -->
                    <div class="space-y-4 mb-8">
                        <h2 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">The Deck is
                            Quiet...</h2>
                        <p class="text-slate-600 dark:text-muted-gold text-lg max-w-md mx-auto leading-relaxed">
                            No crew members recruited yet... start building your fleet to begin the voyage across
                            the Grand Line!
                        </p>
                    </div>
                    <!-- CTA Actions -->
                    <div class="flex flex-col items-center gap-6">
                        <NuxtLink to="/admin/user/create"
                            class="group flex items-center justify-center gap-3 min-w-[240px] bg-primary hover:bg-primary/90 text-background-dark py-4 px-8 rounded-xl font-bold text-lg transition-all shadow-xl shadow-primary/20 hover:-translate-y-1">
                            <span class="material-symbols-outlined font-bold">person_add</span>
                            <span>Recruit New Member</span>
                        </NuxtLink>
                    </div>
                </div>
            </div>
            <!-- Footer Stats (Subtle) -->
            <footer
                class="p-6 border-t border-slate-200 dark:border-[#3a331d] flex justify-between items-center text-[11px] uppercase tracking-[0.2em] text-muted-gold font-bold">
                <div class="flex gap-6">
                    <span>Fleet Status: Anchored</span>
                    <span>Territory: East Blue</span>
                </div>
                <div>
                    <span>System Version: 1.0.4-Luffy</span>
                </div>
            </footer>
        </main>
        <div id="archive-modal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-40 flex items-center justify-center p-4 hidden">
            <!-- Archive User Modal -->
            <div
                class="relative w-full max-w-[500px] bg-white dark:bg-[#231010] border border-slate-200 dark:border-[#b50909]/30 rounded-xl shadow-2xl overflow-hidden z-50">
                <!-- Skull Watermark -->
                <span class="material-symbols-outlined skull-watermark dark:text-[#b50909]">skull</span>
                <!-- Top Danger Bar -->
                <div class="h-1.5 w-full bg-[#b50909]"></div>
                <div class="p-8 flex flex-col items-center text-center">
                    <!-- Icon -->
                    <div class="size-16 bg-[#b50909]/10 rounded-full flex items-center justify-center mb-6 text-[#b50909]">
                        <span class="material-symbols-outlined" style="font-size: 40px;">sailing</span>
                    </div>
                    <!-- HeadlineText Component -->
                    <h1 class="text-slate-900 dark:text-white tracking-tight text-[32px] font-bold leading-tight pb-3">
                        Walk the Plank?
                    </h1>
                    <!-- BodyText Component -->
                    <p class="text-slate-600 dark:text-white text-base font-normal leading-relaxed pb-8 max-w-[400px]">
                        This pirate will be sent to the archives. They will lose access to the ship’s logs and navigation
                        tools immediately. This action can be undone by the Fleet Admiral.
                    </p>
                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 w-full">
                        <!-- Cancel Button -->
                        <button @click="closeArchiveModal()"
                            class="flex-1 flex cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-6 border border-slate-200 dark:border-[#492222] bg-transparent hover:bg-slate-50 dark:hover:bg-[#341818] transition-colors text-slate-900 dark:text-white text-sm font-bold gap-2">
                            <span class="material-symbols-outlined text-[20px]">anchor</span>
                            Keep on Board
                        </button>
                        <!-- Confirm Button -->
                        <form>
                            <button
                                class="flex-1 flex cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-6 bg-[#b50909] hover:bg-primary transition-colors text-white text-sm font-bold gap-2 shadow-lg shadow-[#b50909]/20">
                                <span class="material-symbols-outlined text-[20px]">skull</span>
                                Send to the Depths
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div id="resurrect-modal" class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center z-50 p-6 hidden">
            <div
                class="bg-background-dark border border-primary/40 rounded-xl max-w-lg w-full shadow-[0_0_50px_-12px_rgba(13,204,242,0.3)] relative overflow-hidden">
                <!-- Decorative Compass/Log Pose Icon -->
                <div class="absolute -top-10 -right-10 opacity-5 pointer-events-none">
                    <span class="material-symbols-outlined text-[200px] text-primary">explore</span>
                </div>
                <!-- Modal Header -->
                <div class="pt-10 pb-4 px-8 text-center relative z-10">
                    <div class="flex justify-center mb-6">
                        <div
                            class="h-16 w-16 bg-primary/10 rounded-full flex items-center justify-center border border-primary/20">
                            <span class="material-symbols-outlined text-primary text-4xl">history_edu</span>
                        </div>
                    </div>
                    <h2 class="text-white tracking-tight text-3xl font-bold leading-tight font-display">
                        Resurrect Crew Member?
                    </h2>
                </div>
                <!-- Modal Body -->
                <div class="px-8 pb-8 text-center relative z-10">
                    <p class="text-[#90c1cb] text-lg font-normal leading-relaxed">
                        The sea is vast, but this soul isn't finished yet. Restoring this user will grant them full
                        access to the fleet's resources and reactivate their status. Are you sure you want to bring
                        them back?
                    </p>
                </div>
                <!-- Modal Footer Actions -->
                <div class="bg-[#182f34] p-8 flex flex-col gap-3 relative z-10">
                    <form>
                        <button
                            class="flex w-full cursor-pointer items-center justify-center rounded-lg h-14 bg-primary text-background-dark gap-3 text-lg font-bold transition-all hover:bg-primary/90 hover:scale-[1.02] shadow-lg shadow-primary/20">
                            <span class="material-symbols-outlined">anchor</span>
                            <span>Rejoin the Fleet</span>
                        </button>
                    </form>
                    <button @click="closeResurrectModal()"
                        class="flex w-full cursor-pointer items-center justify-center rounded-lg h-12 bg-transparent border border-[#315f68] text-[#90c1cb] text-base font-medium hover:bg-[#224249] hover:text-white transition-all">
                        Stay in the Depths
                    </button>
                </div>
                <!-- Bottom Accent Line -->
                <div class="h-1 w-full bg-gradient-to-r from-transparent via-primary to-transparent opacity-50">
                </div>
            </div>
        </div>
    </NuxtLayout>
</template>