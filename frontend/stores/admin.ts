import { defineStore } from 'pinia';
import { ref } from 'vue';
import { api } from '~/utils/api';
import { useAuthStore } from './auth';
import type { ReturnData } from '~~/types/api';
import { useToastStore } from './toast';

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
        const toast = useToastStore()

        async function fetchStatistics() {
            try {
                const res = await api<ReturnData<Statistics>>('/admin')
                statisticts.value = res.data
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
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
