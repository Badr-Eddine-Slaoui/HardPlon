import { defineStore } from 'pinia';
import { ref } from 'vue';
import { api } from '~/utils/api';
import type { ReturnData } from '../types/api';
import type { Year, YearData } from '../types/scholar_year';

export const useScholarYear = defineStore(
    'scholar_year',
    () => {
        const scholar_years = ref<Year[] | null>(null)
        const scholar_year = ref<Year | null>(null)
        const archived_years = ref<Year[] | null>(null)
        const current_year = ref<Year | null>(null)

        async function fetchScholarYears(): Promise<void> {
            const res = await api<ReturnData<YearData>>('/admin/scholar-years')
            scholar_years.value = res.data?.scholar_years as Year[]
            archived_years.value = res.data?.archived_years as Year[]
            current_year.value = res.data?.current_year as Year
        }

        async function fetchScholarYear(id: number): Promise<void> {
            const res = await api<ReturnData<{ scholar_year: Year}>>(`/admin/scholar-years/${id}`)
            scholar_year.value = res.data?.scholar_year as Year
        }

        async function createScholarYear(data: { start_year: number; end_year: number; capacity: number }): Promise<ReturnData<any>> {
            try{
                const res = await api<ReturnData<{ scholar_year: Year, message: string}>>('/admin/scholar-years', {
                    method: 'POST',
                    body: data
                })
                scholar_years.value?.push(res.data?.scholar_year as Year)
                return {
                    success: true,
                    data: res.data?.message
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    if (err.data.errors.start_year || err.data.errors.end_year || err.data.errors.capacity) {
                        let { start_year, end_year, capacity } = err.data.errors;
                        start_year = start_year ? start_year[0] : "";
                        end_year = end_year ? end_year[0] : "";
                        capacity = capacity ? capacity[0] : "";
                        return {
                            success: false,
                            errors: {
                                start_year,
                                end_year,
                                capacity
                            },
                        }
                    }
                }
                return {
                    success: false,
                    errors: {
                        start_year: '',
                        end_year: '',
                        capacity: '',
                        message: err.data.message
                    },
                }
            }
        }

        async function updateScholarYear(id: number, data: { start_year: number; end_year: number; capacity: number }): Promise<ReturnData<any>> {
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
                return {
                    success: true,
                    data: res.data?.message
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    if (err.data.errors.start_year || err.data.errors.end_year || err.data.errors.capacity) {
                        let { start_year, end_year, capacity } = err.data.errors;
                        start_year = start_year ? start_year[0] : "";
                        end_year = end_year ? end_year[0] : "";
                        capacity = capacity ? capacity[0] : "";
                        return {
                            success: false,
                            errors: {
                                start_year,
                                end_year,
                                capacity
                            },
                        }
                    }
                }
                return {
                    success: false,
                    errors: {
                        start_year: '',
                        end_year: '',
                        capacity: '',
                        message: err.message
                    },
                }
            }
        }

        async function archiveScholarYear(id: number): Promise<void> {
            const res = await api<ReturnData<{ scholar_year: Year }>>(`/admin/scholar-years/${id}`, {
                method: 'DELETE'
            })

            if(!scholar_years.value?.length){
                await fetchScholarYears()
            }

            const restoredYear = res.data?.scholar_year as Year
            const year = scholar_years.value?.find(y => y.id === id) as Year

            Object.assign(year, restoredYear)
        }

        async function restoreScholarYear(id: number): Promise<void> {
            const res = await api<ReturnData<{ scholar_year: Year }>>(`/admin/scholar-years/${id}/restore`, {
                method: 'POST'
            })

            if(!scholar_years.value?.length){
                await fetchScholarYears()
            }
            
            const restoredYear = res.data?.scholar_year as Year
            const year = scholar_years.value?.find(y => y.id === id) as Year

            Object.assign(year, restoredYear)
        }

        return {
            scholar_years,
            scholar_year,
            archived_years,
            current_year,
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
