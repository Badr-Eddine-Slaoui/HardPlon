import { defineStore } from 'pinia';
import { reactive, ref } from 'vue';
import { api } from '~/utils/api';
import type { meta, ReturnData } from '../types/api';
import type { Grade } from '../types/grade_level';
import { useToastStore } from './toast';
import { PaginatedData } from '../types/pagination';

export const useGradeLevel = defineStore(
    'grade_level',
    () => {
        const grade_levels = ref<Grade[] | null>(null)
        const grade_level = ref<Grade | null>(null)
        const archive_grade_levels = ref<Grade[] | null>(null)
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

        async function fetchGradeLevels(page: number = 1, per_page: number = 5): Promise<void> {
            try {
                const res = await api<ReturnData<{ grade_levels: PaginatedData<Grade[]>, archived_grade_levels: PaginatedData<Grade[]>}>>(`/admin/grade-levels?page=${page}&per_page=${per_page}`)
                grade_levels.value = res.data?.grade_levels?.data as Grade[]
                archive_grade_levels.value = res.data?.archived_grade_levels?.data as Grade[]
                Object.assign(meta, res.data?.grade_levels)
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function fetchGradeLevel(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ grade_level: Grade}>>(`/admin/grade-levels/${id}`)
                grade_level.value = res.data?.grade_level as Grade
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function createGradeLevel(data: { name: string; description: string; scholar_year_id: number; capacity: number }): Promise<ReturnData> {
            try{
                const res = await api<ReturnData<{ grade_level: Grade, message: string}>>('/admin/grade-levels', {
                    method: 'POST',
                    body: data
                })

                if(!grade_levels.value?.length){
                    await fetchGradeLevels()
                }

                grade_levels.value?.unshift(res.data?.grade_level as Grade)

                toast.push({
                    message: res.data?.message || 'Grade level created successfully.',
                    type: 'success',
                })

                return {
                    success: true,
                    data: null,
                    message: res.data?.message || 'Grade level created successfully.'
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    if (err.data.errors.name || err.data.errors.description || err.data.errors.scholar_year_id || err.data.errors.capacity) {
                        let { name, description, scholar_year_id, capacity } = err.data.errors;
                        name = name ? name[0] : "";
                        description = description ? description[0] : "";
                        scholar_year_id = scholar_year_id ? scholar_year_id[0] : "";
                        capacity = capacity ? capacity[0] : "";

                        toast.push({
                            message: "Validation error. Please check your input.",
                            type: 'error',
                        })

                        return {
                            success: false,
                            data: null,
                            message: "Validation error. Please check your input.",
                            errors: {
                                name,
                                description,
                                scholar_year_id,
                                capacity
                            },
                        }
                    }
                }

                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })

                return {
                    success: false,
                    data: null,
                    message: err.message || 'Something went wrong. Please try again.',
                    errors: {
                        name: '',
                        description: '',
                        scholar_year_id: '',
                        capacity: '',
                        message: err.message || 'Something went wrong. Please try again.'
                    },
                }
            }
        }

        async function updateGradeLevel(id: number, data: { name: string; description: string; scholar_year_id: number; capacity: number }): Promise<ReturnData> {
            try{
                const res = await api<ReturnData<{ grade_level: Grade, message: string}>>(`/admin/grade-levels/${id}`, {
                    method: 'PUT',
                    body: data
                })

                if(!grade_levels.value?.length){
                    await fetchGradeLevels()
                }

                const restoredGrade = res.data?.grade_level as Grade
                const grade_level = grade_levels.value?.find(y => y.id === id) as Grade

                Object.assign(grade_level, restoredGrade)

                toast.push({
                    message: res.data?.message || 'Grade level updated successfully.',
                    type: 'success',
                })

                return {
                    success: true,
                    data: null,
                    message: res.data?.message || 'Grade level updated successfully.'
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    if (err.data.errors.name || err.data.errors.description || err.data.errors.scholar_year_id || err.data.errors.capacity) {
                        let { name, description, scholar_year_id, capacity } = err.data.errors;
                        name = name ? name[0] : "";
                        description = description ? description[0] : "";
                        scholar_year_id = scholar_year_id ? scholar_year_id[0] : "";
                        capacity = capacity ? capacity[0] : "";

                        toast.push({
                            message: "Validation error. Please check your input.",
                            type: 'error',
                        })

                        return {
                            success: false,
                            data: null,
                            message: "Validation error. Please check your input.",
                            errors: {
                                name,
                                description,
                                scholar_year_id,
                                capacity
                            },
                        }
                    }
                }

                toast.push({
                    message: err.message || 'Something went wrong. Please try again.',
                    type: 'error',
                })

                return {
                    success: false,
                    data: null,
                    message: err.message || 'Something went wrong. Please try again.',
                    errors: {
                        name: '',
                        description: '',
                        scholar_year_id: '',
                        capacity: '',
                        message: err.message || 'Something went wrong. Please try again.'
                    },
                }
            }
        }

        async function archiveGradeLevel(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ grade_level: Grade }>>(`/admin/grade-levels/${id}`, {
                    method: 'DELETE'
                })

                if(!grade_levels.value?.length){
                    await fetchGradeLevels()
                }

                const restoredGrade = res.data?.grade_level as Grade
                const grade_level = grade_levels.value?.find(y => y.id === id) as Grade

                Object.assign(grade_level, restoredGrade)

                toast.push({
                    message: res?.message || 'Grade level archived successfully.',
                    type: 'success',
                })
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function restoreGradeLevel(id: number): Promise<void> {
            try{
                const res = await api<ReturnData<{ grade_level: Grade }>>(`/admin/grade-levels/${id}/restore`, {
                    method: 'POST'
                })

                if(!grade_levels.value?.length){
                    await fetchGradeLevels()
                }

                const restoredGrade = res.data?.grade_level as Grade
                const grade_level = grade_levels.value?.find(y => y.id === id) as Grade

                Object.assign(grade_level, restoredGrade)

                toast.push({
                    message: res?.message || 'Grade level restored successfully.',
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
            grade_levels,
            grade_level,
            archive_grade_levels,
            meta,
            fetchGradeLevels,
            fetchGradeLevel,
            createGradeLevel,
            updateGradeLevel,
            archiveGradeLevel,
            restoreGradeLevel
        }
    },
    {
        persist: {
            pick: ['grade_levels', 'archive_grade_levels'],
        },
    }
)
