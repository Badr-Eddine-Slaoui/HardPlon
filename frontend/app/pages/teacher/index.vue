<script setup lang="ts">
    useHead({
        title: 'Teacher Dashboard'
    })
</script>

<template>
    <NuxtLayout name="teacher">
        <main class="flex-1 flex flex-col overflow-y-auto">
            <header
                class="h-16 border-b border-border-teal bg-ocean-deep/80 backdrop-blur-md flex items-center justify-between px-8 sticky top-0 z-10">
                <div class="flex flex-col">
                    <h2 class="text-lg font-bold tracking-tight text-white">Welcome Back, Captain Rayleigh</h2>
                    <p class="text-[10px] text-gold-accent font-bold uppercase tracking-[0.2em]">Voyage Day 142 • New
                        World Sector</p>
                </div>
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-4 text-xs font-bold text-slate-400">
                        <div class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sm text-gold-accent">air</span>
                            <span>WIND: 14 KTS</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sm text-gold-accent">water_drop</span>
                            <span>TIDE: RISING</span>
                        </div>
                    </div>
                    <div class="h-6 w-[1px] bg-border-teal"></div>
                    <button class="relative group">
                        <span
                            class="material-symbols-outlined text-slate-400 group-hover:text-gold-accent transition-colors">notifications</span>
                        <span class="absolute -top-1 -right-1 size-2 bg-red-500 rounded-full border-2 border-ocean-deep"></span>
                    </button>
                </div>
            </header>
            <div class="p-8 space-y-8 max-w-7xl mx-auto w-full">
                <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div
                        class="bg-card-dark border border-gold-accent/20 rounded-2xl p-6 relative overflow-hidden group gold-glow">
                        <div
                            class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity text-gold-accent">
                            <span class="material-symbols-outlined text-9xl">groups</span>
                        </div>
                        <div class="flex items-center gap-4 mb-4">
                            <div
                                class="size-10 rounded-lg bg-gold-accent/10 flex items-center justify-center text-gold-accent border border-gold-accent/20">
                                <span class="material-symbols-outlined">groups</span>
                            </div>
                            <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400">Crew Under Command
                            </h3>
                        </div>
                        <div class="flex items-end gap-2">
                            <span class="text-4xl font-black text-white">142</span>
                            <span class="text-xs text-emerald-500 font-bold mb-1.5 flex items-center gap-0.5">
                                <span class="material-symbols-outlined text-sm">trending_up</span> +5
                            </span>
                        </div>
                        <div class="mt-4 h-1 w-full bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full bg-gold-accent w-[85%]"></div>
                        </div>
                    </div>
                    <div
                        class="bg-card-dark border border-gold-accent/20 rounded-2xl p-6 relative overflow-hidden group gold-glow">
                        <div
                            class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity text-gold-accent">
                            <span class="material-symbols-outlined text-9xl">bolt</span>
                        </div>
                        <div class="flex items-center gap-4 mb-4">
                            <div
                                class="size-10 rounded-lg bg-gold-accent/10 flex items-center justify-center text-gold-accent border border-gold-accent/20">
                                <span class="material-symbols-outlined">bolt</span>
                            </div>
                            <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400">Average Fleet Haki
                            </h3>
                        </div>
                        <div class="flex items-end gap-2">
                            <span class="text-4xl font-black text-white">84.2%</span>
                            <span class="text-xs text-gold-accent font-bold mb-1.5 flex items-center gap-0.5">
                                <span class="material-symbols-outlined text-sm">stars</span> Elite
                            </span>
                        </div>
                        <div class="mt-4 h-1 w-full bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full bg-gold-accent w-[84%]"></div>
                        </div>
                    </div>
                    <div
                        class="bg-card-dark border border-gold-accent/20 rounded-2xl p-6 relative overflow-hidden group gold-glow">
                        <div
                            class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity text-gold-accent">
                            <span class="material-symbols-outlined text-9xl">request_quote</span>
                        </div>
                        <div class="flex items-center gap-4 mb-4">
                            <div
                                class="size-10 rounded-lg bg-gold-accent/10 flex items-center justify-center text-gold-accent border border-gold-accent/20">
                                <span class="material-symbols-outlined">priority_high</span>
                            </div>
                            <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400">Pending Bounties
                            </h3>
                        </div>
                        <div class="flex items-end gap-2">
                            <span class="text-4xl font-black text-white">28</span>
                            <span class="text-[10px] text-slate-500 mb-1.5 uppercase font-bold tracking-tight">to
                                review</span>
                        </div>
                        <div class="mt-4 flex -space-x-2">
                            <div class="size-6 rounded-full border-2 border-card-dark bg-slate-700"></div>
                            <div class="size-6 rounded-full border-2 border-card-dark bg-slate-600"></div>
                            <div class="size-6 rounded-full border-2 border-card-dark bg-slate-500"></div>
                            <div
                                class="size-6 rounded-full border-2 border-card-dark bg-gold-accent flex items-center justify-center text-[8px] font-bold text-ocean-deep">
                                +25</div>
                        </div>
                    </div>
                </section>
                <section class="bg-card-dark border border-border-teal rounded-2xl overflow-hidden shadow-2xl">
                    <div class="p-6 border-b border-border-teal flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-gold-accent">explore</span>
                            <h2 class="text-lg font-bold text-white">Voyage Progress: Success Metrics</h2>
                        </div>
                        <div class="flex items-center gap-2">
                            <select
                                class="bg-ocean-deep border-border-teal text-[10px] font-bold uppercase py-1 pl-3 pr-8 rounded-lg focus:ring-gold-accent focus:border-gold-accent text-slate-400">
                                <option>Last 30 Days</option>
                                <option>Current Semester</option>
                            </select>
                            <button class="p-1.5 bg-border-teal rounded-lg hover:text-gold-accent transition-colors">
                                <span class="material-symbols-outlined text-sm">filter_list</span>
                            </button>
                        </div>
                    </div>
                    <div class="p-8 h-[360px] relative bg-ocean-dark/50">
                        <div class="absolute inset-x-8 inset-y-12 flex flex-col justify-between pointer-events-none">
                            <div class="border-t border-border-teal/30 w-full h-0"></div>
                            <div class="border-t border-border-teal/30 w-full h-0"></div>
                            <div class="border-t border-border-teal/30 w-full h-0"></div>
                            <div class="border-t border-border-teal/30 w-full h-0"></div>
                        </div>
                        <div class="absolute inset-0 p-8 flex items-end justify-between gap-4">
                            <div class="w-full flex items-end justify-around h-full pt-4">
                                <div class="w-16 flex flex-col items-center gap-3">
                                    <div
                                        class="w-12 bg-gold-accent/5 border-t-2 border-gold-accent/40 rounded-t h-[65%] relative group transition-all hover:bg-gold-accent/20">
                                        <div
                                            class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gold-accent text-ocean-deep px-2 py-0.5 rounded text-[10px] font-bold opacity-0 group-hover:opacity-100 transition-opacity">
                                            65%</div>
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-500 uppercase">Wk 1</span>
                                </div>
                                <div class="w-16 flex flex-col items-center gap-3">
                                    <div
                                        class="w-12 bg-gold-accent/5 border-t-2 border-gold-accent/40 rounded-t h-[45%] relative group transition-all hover:bg-gold-accent/20">
                                        <div
                                            class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gold-accent text-ocean-deep px-2 py-0.5 rounded text-[10px] font-bold opacity-0 group-hover:opacity-100 transition-opacity">
                                            45%</div>
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-500 uppercase">Wk 2</span>
                                </div>
                                <div class="w-16 flex flex-col items-center gap-3">
                                    <div
                                        class="w-12 bg-gold-accent/5 border-t-2 border-gold-accent/40 rounded-t h-[85%] relative group transition-all hover:bg-gold-accent/20">
                                        <div
                                            class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gold-accent text-ocean-deep px-2 py-0.5 rounded text-[10px] font-bold opacity-0 group-hover:opacity-100 transition-opacity">
                                            85%</div>
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-500 uppercase">Wk 3</span>
                                </div>
                                <div class="w-16 flex flex-col items-center gap-3">
                                    <div
                                        class="w-12 bg-gold-accent/5 border-t-2 border-gold-accent/40 rounded-t h-[72%] relative group transition-all hover:bg-gold-accent/20">
                                        <div
                                            class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gold-accent text-ocean-deep px-2 py-0.5 rounded text-[10px] font-bold opacity-0 group-hover:opacity-100 transition-opacity">
                                            72%</div>
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-500 uppercase">Wk 4</span>
                                </div>
                                <div class="w-16 flex flex-col items-center gap-3">
                                    <div
                                        class="w-12 bg-gold-accent/20 border-t-4 border-gold-accent rounded-t h-[92%] relative group transition-all hover:bg-gold-accent/30">
                                        <div
                                            class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gold-accent text-ocean-deep px-2 py-0.5 rounded text-[10px] font-bold opacity-0 group-hover:opacity-100 transition-opacity">
                                            92%</div>
                                    </div>
                                    <span class="text-[10px] font-bold text-gold-accent uppercase">Live</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="bg-ocean-dark p-4 flex justify-center gap-8 text-[10px] font-bold text-slate-500 border-t border-border-teal">
                        <div class="flex items-center gap-2">
                            <span class="size-2 rounded-full bg-gold-accent"></span>
                            <span class="uppercase">Active Fleet Participation</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="size-2 rounded-full bg-border-teal"></span>
                            <span class="uppercase">Standard Baseline</span>
                        </div>
                    </div>
                </section>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 pb-8">
                    <div class="bg-card-dark border border-border-teal rounded-2xl p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="font-bold flex items-center gap-2 text-white">
                                <span class="material-symbols-outlined text-gold-accent">history</span>
                                Recent Fleet Logs
                            </h3>
                            <button class="text-[10px] font-bold text-gold-accent uppercase hover:underline">Full
                                Log</button>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-center gap-4 p-3 rounded-xl bg-ocean-deep/50 border border-border-teal">
                                <div
                                    class="size-8 rounded-lg bg-orange-500/10 text-orange-500 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-lg">flag</span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-white">Class 3-A reached Log Pose 4</p>
                                    <p class="text-[10px] text-slate-500 uppercase">90% completion rate</p>
                                </div>
                                <span class="text-[10px] text-slate-500 font-medium">12m ago</span>
                            </div>
                            <div class="flex items-center gap-4 p-3 rounded-xl bg-ocean-deep/50 border border-border-teal">
                                <div
                                    class="size-8 rounded-lg bg-gold-accent/10 text-gold-accent flex items-center justify-center">
                                    <span class="material-symbols-outlined text-lg">school</span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-white">New Recruit: Tony Chopper</p>
                                    <p class="text-[10px] text-slate-500 uppercase">Medical Science Fleet</p>
                                </div>
                                <span class="text-[10px] text-slate-500 font-medium">2h ago</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-card-dark border border-border-teal rounded-2xl p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="font-bold flex items-center gap-2 text-white">
                                <span class="material-symbols-outlined text-gold-accent">anchor</span>
                                Upcoming Landfalls
                            </h3>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between group cursor-pointer">
                                <div class="flex gap-4">
                                    <div
                                        class="flex flex-col items-center justify-center size-10 rounded-lg bg-gold-accent/10 text-gold-accent border border-gold-accent/20 font-bold">
                                        <span class="text-[10px] leading-none">OCT</span>
                                        <span class="text-sm leading-none">24</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold group-hover:text-gold-accent transition-colors text-white">
                                            Grand Line History Exam</p>
                                        <p class="text-[10px] text-slate-500 uppercase tracking-wider">All Fleets •
                                            09:00 AM</p>
                                    </div>
                                </div>
                                <span class="material-symbols-outlined text-slate-600">chevron_right</span>
                            </div>
                            <div class="flex items-center justify-between group cursor-pointer">
                                <div class="flex gap-4">
                                    <div
                                        class="flex flex-col items-center justify-center size-10 rounded-lg bg-red-500/10 text-red-500 border border-red-500/20 font-bold">
                                        <span class="text-[10px] leading-none">OCT</span>
                                        <span class="text-sm leading-none">28</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold group-hover:text-red-400 transition-colors text-white">
                                            Vessel Design Submission</p>
                                        <p class="text-[10px] text-slate-500 uppercase tracking-wider">Shipwrights •
                                            11:59 PM</p>
                                    </div>
                                </div>
                                <span class="material-symbols-outlined text-slate-600">chevron_right</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <footer
                class="mt-auto border-t border-border-teal bg-ocean-dark px-8 py-3 flex items-center justify-between text-[11px] font-medium text-slate-500">
                <div class="flex gap-6">
                    <span class="flex items-center gap-1.5"><span class="size-2 bg-emerald-500 rounded-full"></span>
                        Fleet Status: Synchronized</span>
                    <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[14px]">anchor</span>
                        Anchored: Marine HQ Sector
                        7</span>
                </div>
                <div class="flex gap-4 items-center">
                    <span
                        class="px-2 py-0.5 bg-gold-accent/10 text-gold-accent rounded border border-gold-accent/20 font-bold uppercase">v2.4.0-ARABASTA</span>
                    <a class="hover:text-gold-accent transition-colors" href="#">System Protocols</a>
                    <a class="hover:text-gold-accent transition-colors" href="#">Support Signal</a>
                </div>
            </footer>
        </main>
    </NuxtLayout>
</template>