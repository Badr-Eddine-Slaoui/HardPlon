<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
    meta: {
        current_page: number
        last_page: number
        next_page_url: string | null
        prev_page_url: string | null
        from: number
        to: number
        total: number
    },
    per_page: number
}>()

const emit = defineEmits<{
    (e: 'change', page: number): void
    (e: 'perPage', value: number): void
}>()

function go(page: number | string) {
    if (typeof page !== 'number') return
    emit('change', page)
}

function changePerPage(event: Event) {
    const value = Number((event.target as HTMLSelectElement).value)
    emit('perPage', value)
}

const pages = computed(() => {
    const current = props.meta.current_page;
    const last = props.meta.last_page;

    const range: (number | string)[] = [];

    const start = Math.max(1, current - 1);
    const end = Math.min(last, current + 1);

    if (start > 1) {
        range.push(1);
        if (start > 2) range.push("...");
    }

    for (let i = start; i <= end; i++) {
        range.push(i);
    }

    if (end < last - 1) {
        range.push("...");
    }

    if (end < last) {
        range.push(last);
    }

    return range;
});

</script>

<template>
    <div class="flex flex-col lg:flex-row items-center justify-between gap-4 mt-8">

        <!-- LEFT INFO -->
        <div class="flex items-center gap-4 text-xs text-primary/50">

            <div>
                Showing
                <span class="text-primary font-semibold">{{ meta.from }}</span>
                –
                <span class="text-primary font-semibold">{{ meta.to }}</span>
                of
                <span class="text-primary font-semibold">{{ meta.total }}</span>
                crew members
            </div>

            <!-- PER PAGE -->
            <div class="flex items-center gap-2">
                <span>Per page</span>

                <select :value="per_page" @change="changePerPage"
                    class="bg-academy-panel/60 border border-primary/20 text-primary rounded-lg px-2 py-1 text-xs outline-none focus:border-primary/50">
                    <option :value="5">5</option>
                    <option :value="10">10</option>
                    <option :value="15">15</option>
                    <option :value="25">25</option>
                    <option :value="50">50</option>
                </select>
            </div>

        </div>

        <!-- PAGINATION -->
        <nav
            class="flex items-center gap-2 bg-academy-panel/40 backdrop-blur-md border border-primary/10 rounded-xl px-3 py-2 shadow-lg">

            <!-- Prev -->
            <button class="px-3 py-1.5 text-sm rounded-lg border transition-all" :class="meta.prev_page_url
                ? 'border-primary/20 text-primary/70 hover:bg-primary/10 hover:text-primary hover:border-primary/40'
                : 'opacity-40 cursor-not-allowed border-primary/10 text-primary/40'" :disabled="!meta.prev_page_url"
                @click="go(meta.current_page - 1)">
                ‹
            </button>

            <!-- Pages -->
            <template v-for="(page, i) in pages" :key="i">

                <span v-if="page === '...'" class="px-2 text-primary/40">
                    ...
                </span>

                <button v-else class="min-w-[36px] px-3 py-1.5 text-sm rounded-lg border transition-all" :class="page === meta.current_page
                    ? 'bg-primary text-background-dark font-bold border-primary shadow-[0_0_12px_rgba(13,204,242,0.25)]'
                    : 'border-primary/10 text-primary/60 hover:bg-primary/10 hover:text-primary hover:border-primary/40'"
                    @click="go(page)">
                    {{ page }}
                </button>

            </template>

            <!-- Next -->
            <button class="px-3 py-1.5 text-sm rounded-lg border transition-all" :class="meta.next_page_url
                ? 'border-primary/20 text-primary/70 hover:bg-primary/10 hover:text-primary hover:border-primary/40'
                : 'opacity-40 cursor-not-allowed border-primary/10 text-primary/40'" :disabled="!meta.next_page_url"
                @click="go(meta.current_page + 1)">
                ›
            </button>

        </nav>

    </div>
</template>