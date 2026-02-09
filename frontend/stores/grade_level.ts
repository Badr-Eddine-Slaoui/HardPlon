import { defineStore } from 'pinia';
import { ref } from 'vue';
import { api } from '~/utils/api';
import type { ReturnData } from '../types/api';
import type { Grade, GradeData } from '../types/grade_level';

export const useGradeLevel = defineStore(
    'grade_level',
    () => {
        const grade_levels = ref<Grade[] | null>(null)
        const grade_level = ref<Grade | null>(null)
        const archive_grade_levels = ref<Grade[] | null>(null)

        async function fetchGradeLevels(): Promise<void> {
            const res = await api<ReturnData<GradeData>>('/admin/grade-levels')
            grade_levels.value = res.data?.grade_levels as Grade[]
            archive_grade_levels.value = res.data?.archived_grade_levels as Grade[]
        }

        async function fetchGradeLevel(id: number): Promise<void> {
            const res = await api<ReturnData<{ grade_level: Grade}>>(`/admin/grade-levels/${id}`)
            grade_level.value = res.data?.grade_level as Grade
        }

        async function createGradeLevel(data: { name: string; description: string; scholar_year_id: number; capacity: number }): Promise<ReturnData<any>> {
            try{
                const res = await api<ReturnData<{ grade_level: Grade, message: string}>>('/admin/grade-levels', {
                    method: 'POST',
                    body: data
                })
                grade_levels.value?.push(res.data?.grade_level as Grade)
                return {
                    success: true,
                    data: res.data?.message
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    if (err.data.errors.name || err.data.errors.description || err.data.errors.scholar_year_id || err.data.errors.capacity) {
                        let { name, description, scholar_year_id, capacity } = err.data.errors;
                        name = name ? name[0] : "";
                        description = description ? description[0] : "";
                        scholar_year_id = scholar_year_id ? scholar_year_id[0] : "";
                        capacity = capacity ? capacity[0] : "";
                        return {
                            success: false,
                            errors: {
                                name,
                                description,
                                scholar_year_id,
                                capacity
                            },
                        }
                    }
                }
                return {
                    success: false,
                    errors: {
                        name: '',
                        description: '',
                        scholar_year_id: '',
                        capacity: '',
                        message: err.data.message
                    },
                }
            }
        }

        async function updateGradeLevel(id: number, data: { name: string; description: string; scholar_year_id: number; capacity: number }): Promise<ReturnData<any>> {
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
                return {
                    success: true,
                    data: res.data?.message
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    if (err.data.errors.name || err.data.errors.description || err.data.errors.scholar_year_id || err.data.errors.capacity) {
                        let { name, description, scholar_year_id, capacity } = err.data.errors;
                        name = name ? name[0] : "";
                        description = description ? description[0] : "";
                        scholar_year_id = scholar_year_id ? scholar_year_id[0] : "";
                        capacity = capacity ? capacity[0] : "";
                        return {
                            success: false,
                            errors: {
                                name,
                                description,
                                scholar_year_id,
                                capacity
                            },
                        }
                    }
                }
                return {
                    success: false,
                    errors: {
                        name: '',
                        description: '',
                        scholar_year_id: '',
                        capacity: '',
                        message: err.message
                    },
                }
            }
        }

        async function archiveGradeLevel(id: number): Promise<void> {
            const res = await api<ReturnData<{ grade_level: Grade }>>(`/admin/grade-levels/${id}`, {
                method: 'DELETE'
            })

            if(!grade_levels.value?.length){
                await fetchGradeLevels()
            }

            const restoredGrade = res.data?.grade_level as Grade
            const grade_level = grade_levels.value?.find(y => y.id === id) as Grade

            Object.assign(grade_level, restoredGrade)
        }

        async function restoreGradeLevel(id: number): Promise<void> {
            const res = await api<ReturnData<{ grade_level: Grade }>>(`/admin/grade-levels/${id}/restore`, {
                method: 'POST'
            })

            if(!grade_levels.value?.length){
                await fetchGradeLevels()
            }

            const restoredGrade = res.data?.grade_level as Grade
            const grade_level = grade_levels.value?.find(y => y.id === id) as Grade

            Object.assign(grade_level, restoredGrade)
        }

        return {
            grade_levels,
            grade_level,
            archive_grade_levels,
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
