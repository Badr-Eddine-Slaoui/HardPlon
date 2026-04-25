import { defineStore } from 'pinia';
import { ref } from 'vue';
import { api } from '~/utils/api';
import type { ReturnData } from '~~/types/api';
import { useToastStore } from './toast';

interface TeacherStatistics {
    students_count: number;
    average_haki: number;
    weekly_metrics: { label: string; value: number }[];
    recent_logs: {
        id: number;
        type: string;
        title: string;
        description: string;
        time: string;
        icon: string;
        color: string;
    }[];
    upcoming_events: {
        id: number;
        title: string;
        date: string;
        time: string;
        type: string;
        color: string;
    }[];
}

export const useTeacherStore = defineStore(
    'teacher',
    () => {
        const statistics = ref<TeacherStatistics | null>(null)
        const toast = useToastStore()

        async function fetchStatistics() {
            try {
                const res = await api<ReturnData<TeacherStatistics>>('/teacher')
                statistics.value = res.data
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        return {
            statistics,
            fetchStatistics
        }
    },
    {
        persist: {
            pick: ['statistics'],
        },
    }
)
