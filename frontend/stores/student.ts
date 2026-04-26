import { defineStore } from 'pinia';
import { ref } from 'vue';
import type { DashboardData } from '../types/student';
import type { ReturnData } from '~~/types/api';
import { useToastStore } from './toast';

export const useStudent = defineStore(
    'student', 
    () => {
        const api = useApi()
        const dashboardData = ref<DashboardData | null>(null);
        const loading = ref(false);
        const toast = useToastStore();

        const fetchDashboardData = async () => {
            loading.value = true;
            try {
                const response = await api<ReturnData<DashboardData>>('/student/dashboard');
                console.log('Dashboard API Response:', response);
                if (response && response.success && response.data) {
                    dashboardData.value = response.data;
                }
            } catch (error) {
                console.error('Error fetching dashboard data:', error);
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                });
            } finally {
                loading.value = false;
            }
        };

        return {
            dashboardData,
            loading,
            fetchDashboardData
        };
    },
    {
        persist: {
            pick: ['dashboardData'],
        },
    }
);
