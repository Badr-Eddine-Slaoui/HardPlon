<script setup lang="ts">
    import { useUser } from '~~/stores/user';

    useHead({
        title: `Admin Dashboard - Admin Member`
    })

    const store = useUser();

    const id: number = parseInt(useRoute().params?.id as string)

    onMounted(async() => {
        await store.fetchUser('ADMIN', id);
    })
</script>

<template>
    <NuxtLayout name="admin">
        <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <header
                class="h-16 border-b border-primary/10 flex items-center justify-between px-8 bg-deep-blue/80 backdrop-blur-md sticky top-0 z-10">
                <div class="flex items-center gap-4">
                    <span class="text-xs font-bold tracking-widest text-primary uppercase">Official Documentation // Top
                        Secret</span>
                </div>
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <p class="text-xs font-bold">{{ store.user?.first_name }} {{ store.user?.last_name }}</p>
                            <p class="text-[10px] text-primary/60 uppercase tracking-tighter">Fleet Commander</p>
                        </div>
                        <div class="size-9 rounded-full bg-cover bg-center ring-2 ring-primary/20"
                            style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBM7gOYLlXxzQZdPHqNkv-gdfkm51gNkrCe-Af52ZcTnByB4DCN2mhthndGGPxEN2MP-bRh2lm5Q6ql6IejH_4m3UIIBv_RK9KqjeLcJeKly00CFEUSI0YUFnEiVLfQ8PTzFGONagdP0uYjBs79QsQLBAOKq21oJDsW1QSmN5TlMiN7HTlSwCcDbEFSB6D0JcLlv707SiuQpUX2H5UYCNGMbyV3Q2QTZm4JAzmxyW5whljqXaQVuIdzEvJkrPanduAkbpG2JwjB9Yc');">
                        </div>
                    </div>
                </div>
            </header>
            <div class="flex-1 p-8 lg:p-12 overflow-y-auto">
                <div class="max-w-4xl mx-auto">
                    <div class="flex flex-col md:flex-row items-center md:items-start gap-8 mb-12 relative">
                        <div class="relative group">
                            <div class="size-48 rounded-2xl bg-cover bg-center border-4 border-primary shadow-[0_0_30px_rgba(236,182,19,0.2)]"
                                style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBM7gOYLlXxzQZdPHqNkv-gdfkm51gNkrCe-Af52ZcTnByB4DCN2mhthndGGPxEN2MP-bRh2lm5Q6ql6IejH_4m3UIIBv_RK9KqjeLcJeKly00CFEUSI0YUFnEiVLfQ8PTzFGONagdP0uYjBs79QsQLBAOKq21oJDsW1QSmN5TlMiN7HTlSwCcDbEFSB6D0JcLlv707SiuQpUX2H5UYCNGMbyV3Q2QTZm4JAzmxyW5whljqXaQVuIdzEvJkrPanduAkbpG2JwjB9Yc');">
                            </div>
                            <div
                                class="absolute -bottom-4 -right-4 bg-dark-red text-white p-2 rounded-lg border-2 border-primary rotate-12 shadow-lg">
                                <span class="material-symbols-outlined">military_tech</span>
                            </div>
                        </div>
                        <div class="flex-1 text-center md:text-left pt-4">
                            <div
                                class="inline-flex items-center gap-2 bg-primary/10 border border-primary/30 px-3 py-1 rounded-full text-xs font-bold text-primary mb-4 tracking-widest uppercase">
                                <span class="material-symbols-outlined text-sm">verified</span>
                                Fleet Commander Badge
                            </div>
                            <h2 class="text-5xl font-black text-white mb-2 tracking-tight">{{ store.user?.first_name }} {{ store.user?.last_name }}</h2>
                            <p class="text-primary/70 text-lg font-medium mb-6">Supreme Administrator of the Grand Line
                                Academy</p>
                            <div class="flex flex-wrap justify-center md:justify-start gap-3">
                                <NuxtLink :to="`/admin/user/admin/${store.user?.id}/edit`"
                                    class="bg-primary hover:bg-primary/90 text-background-dark px-6 py-2.5 rounded-lg font-bold transition-all flex items-center gap-2">
                                    <span class="material-symbols-outlined">edit</span>
                                    Update Dossier
                                </NuxtLink>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
                        <div class="bg-marine-blue/40 border border-primary/20 rounded-2xl p-8 backdrop-blur-sm">
                            <div class="flex items-center gap-3 mb-8">
                                <span class="material-symbols-outlined text-primary">badge</span>
                                <h3 class="text-xl font-bold text-white tracking-wide">Officer Identity</h3>
                            </div>
                            <div class="space-y-6">
                                <div class="flex flex-col gap-1">
                                    <span class="text-[10px] uppercase font-bold text-primary/50 tracking-widest">Full
                                        Name</span>
                                    <span class="text-lg font-medium text-slate-200">{{ store.user?.first_name }} {{ store.user?.last_name }}</span>
                                </div>
                                <div class="flex flex-col gap-1">
                                    <span class="text-[10px] uppercase font-bold text-primary/50 tracking-widest">Assigned
                                        Role</span>
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg font-medium text-slate-200">Fleet Commander</span>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-1">
                                    <span class="text-[10px] uppercase font-bold text-primary/50 tracking-widest">Identification
                                        (CIN)</span>
                                    <span
                                        class="text-lg font-medium text-slate-200 font-mono tracking-tighter">{{ store.user?.cin }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-marine-blue/40 border border-primary/20 rounded-2xl p-8 backdrop-blur-sm">
                            <div class="flex items-center gap-3 mb-8">
                                <span class="material-symbols-outlined text-primary">contact_mail</span>
                                <h3 class="text-xl font-bold text-white tracking-wide">Communication Array</h3>
                            </div>
                            <div class="space-y-6">
                                <div class="flex flex-col gap-1">
                                    <span class="text-[10px] uppercase font-bold text-primary/50 tracking-widest">Snail-Mail
                                        (Email)</span>
                                    <span class="text-lg font-medium text-slate-200">{{ store.user?.email }}</span>
                                </div>
                                <div class="flex flex-col gap-1">
                                    <span class="text-[10px] uppercase font-bold text-primary/50 tracking-widest">Den
                                        Den Mushi (Phone)</span>
                                    <span class="text-lg font-medium text-slate-200">{{ store.user?.phone }}</span>
                                </div>
                                <div class="flex flex-col gap-1">
                                    <span
                                        class="text-[10px] uppercase font-bold text-primary/50 tracking-widest">Location</span>
                                    <span class="text-lg font-medium text-slate-200">New World - Hachinosu Base</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-12 text-center opacity-10 flex flex-col items-center">
                        <span class="material-symbols-outlined text-6xl">anchor</span>
                        <p class="text-xs italic mt-2">Authentic Academy Records - Established 1522</p>
                    </div>
                </div>
            </div>
        </main>
    </NuxtLayout>
</template>