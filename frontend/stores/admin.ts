import { defineStore } from 'pinia';
import { ref } from 'vue';
import { api } from '~~/utils/api';

interface Statistics {
    students_count: number;
    teachers_count: number;
    class_groups_count: number;
    briefs_count: number;
}

export const useAdminStore = defineStore(
    'admin',
    () => {
        const statisticts = ref<Statistics | null>(null)

        async function fetchStatistics() {
            const res = await api<{statistics: Statistics}>('/admin')
            statisticts.value = res.statistics
        }


        return {
            statisticts,
            fetchStatistics
        }
    },
    {
        persist: {
            pick: ['statisticts'],
        },
    }
)
