import { defineStore } from 'pinia';
import { reactive, ref } from 'vue';
import { api } from '~/utils/api';
import type { meta, ReturnData } from '../types/api';
import type { Year } from '../types/scholar_year';
import { useToastStore } from './toast';
import { PaginatedData } from '../types/pagination';

export const useScholarYear = defineStore(
    'scholar_year',
    () => {
        const scholar_years = ref<Year[] | null>(null)
        const scholar_year = ref<Year | null>(null)
        const archived_years = ref<Year[] | null>(null)
        const current_year = ref<Year | null>(null)
        const toast = useToastStore()
        const meta: meta = reactive({
            current_page: 0,
            last_page: 0,
            next_page_url: null,
            prev_page_url: null,
            total: 0,
            per_page: 0,
            from: 0,
            to: 0
        })

        async function fetchScholarYears(page: number = 1, per_page: number = 5): Promise<void> {
            try {
                const res = await api<ReturnData<{ scholar_years: PaginatedData<Year[]>, archived_years: PaginatedData<Year[]>, current_year: Year }>>(`/admin/scholar-years?page=${page}&per_page=${per_page}`)
                scholar_years.value = res.data?.scholar_years?.data as Year[]
                archived_years.value = res.data?.archived_years?.data as Year[]
                current_year.value = res.data?.current_year as Year
                Object.assign(meta, res.data?.scholar_years)
            } catch (err: any) {
                toast.showToast({
                    message: err.data?.message || 'Failed to fetch scholar years',
                    type: 'error'
                })
            }
        }

        async function fetchScholarYear(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ scholar_year: Year}>>(`/admin/scholar-years/${id}`)
                scholar_year.value = res.data?.scholar_year as Year
            } catch (err: any) {
                toast.showToast({
                    message: err.data?.message || 'Failed to fetch scholar year',
                    type: 'error'
                })
            }
        }

        async function createScholarYear(data: { start_year: number; end_year: number; capacity: number }): Promise<ReturnData> {
            try{
                const res = await api<ReturnData<{ scholar_year: Year, message: string}>>('/admin/scholar-years', {
                    method: 'POST',
                    body: data
                })

                if(!scholar_years.value?.length){
                    await fetchScholarYears()
                }

                scholar_years.value?.unshift(res.data?.scholar_year as Year)

                toast.push({
                    message: res?.message || 'Scholar year created successfully.',
                    type: 'success',
                })

                return {
                    success: true,
                    message: res?.message || 'Scholar year created successfully.',
                    data: null
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    if (err.data.errors.start_year || err.data.errors.end_year || err.data.errors.capacity) {
                        let { start_year, end_year, capacity } = err.data.errors;
                        start_year = start_year ? start_year[0] : "";
                        end_year = end_year ? end_year[0] : "";
                        capacity = capacity ? capacity[0] : "";

                        toast.push({
                            message: "Validation error. Please check your input.",
                            type: 'error',
                        })

                        return {
                            success: false,
                            message: "Validation error. Please check your input.",
                            data: null,
                            errors: {
                                start_year,
                                end_year,
                                capacity
                            },
                        }
                    }
                }

                toast.push({
                    message: err.message || 'Failed to create scholar year',
                    type: 'error',
                })

                return {
                    success: false,
                    message: err.message || 'Failed to create scholar year',
                    data: null,
                    errors: {
                        start_year: '',
                        end_year: '',
                        capacity: '',
                        message: err.message || 'Failed to create scholar year'
                    },
                }
            }
        }

        async function updateScholarYear(id: number, data: { start_year: number; end_year: number; capacity: number }): Promise<ReturnData> {
            try{
                const res = await api<ReturnData<{ scholar_year: Year, message: string}>>(`/admin/scholar-years/${id}`, {
                    method: 'PUT',
                    body: data
                })

                if(!scholar_years.value?.length){
                    await fetchScholarYears()
                }

                const restoredYear = res.data?.scholar_year as Year
                const year = scholar_years.value?.find(y => y.id === id) as Year

                Object.assign(year, restoredYear)

                toast.push({
                    message: res?.message || 'Scholar year updated successfully.',
                    type: 'success',
                })

                return {
                    success: true,
                    message: res?.message || 'Scholar year updated successfully.',
                    data: null
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    if (err.data.errors.start_year || err.data.errors.end_year || err.data.errors.capacity) {
                        let { start_year, end_year, capacity } = err.data.errors;
                        start_year = start_year ? start_year[0] : "";
                        end_year = end_year ? end_year[0] : "";
                        capacity = capacity ? capacity[0] : "";

                        toast.push({
                            message: "Validation error. Please check your input.",
                            type: 'error',
                        })

                        return {
                            success: false,
                            message: "Validation error. Please check your input.",
                            data: null,
                            errors: {
                                start_year,
                                end_year,
                                capacity
                            },
                        }
                    }
                }

                toast.push({
                    message: err.message || 'Failed to update scholar year',
                    type: 'error',
                })

                return {
                    success: false,
                    message: err.message || 'Failed to update scholar year',
                    data: null,
                    errors: {
                        start_year: '',
                        end_year: '',
                        capacity: '',
                        message: err.message || 'Failed to update scholar year'
                    },
                }
            }
        }

        async function archiveScholarYear(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ scholar_year: Year }>>(`/admin/scholar-years/${id}`, {
                    method: 'DELETE'
                })

                if(!scholar_years.value?.length){
                    await fetchScholarYears()
                }

                const restoredYear = res.data?.scholar_year as Year
                const year = scholar_years.value?.find(y => y.id === id) as Year

                Object.assign(year, restoredYear)

                toast.push({
                    message: res?.message || 'Scholar year archived successfully.',
                    type: 'success',
                })
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function restoreScholarYear(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ scholar_year: Year }>>(`/admin/scholar-years/${id}/restore`, {
                    method: 'POST'
                })

                if(!scholar_years.value?.length){
                    await fetchScholarYears()
                }
                
                const restoredYear = res.data?.scholar_year as Year
                const year = scholar_years.value?.find(y => y.id === id) as Year

                Object.assign(year, restoredYear)

                toast.push({
                    message: res?.message || 'Scholar year restored successfully.',
                    type: 'success',
                })
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        return {
            scholar_years,
            scholar_year,
            archived_years,
            current_year,
            meta,
            fetchScholarYears,
            fetchScholarYear,
            createScholarYear,
            updateScholarYear,
            archiveScholarYear,
            restoreScholarYear
        }
    },
    {
        persist: {
            pick: ['scholar_years', 'archived_years', 'current_year'],
        },
    }
)
