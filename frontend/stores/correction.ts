import { defineStore } from 'pinia';
import { ref } from 'vue';
import { api } from '~~/utils/api';
import type { ReturnData } from '../types/api';
import type { Correction } from '../types/correction';

export const useCorrection = defineStore(
    'correction',
    () => {
        const corrections = ref<Correction[] | null>(null)
        const correction = ref<Correction | null>(null)

        async function fetchStudentCorrections(): Promise<void> {
            const res = await api<ReturnData<{ corrections: Correction[]}>>('/student/corrections')
            corrections.value = res.data?.corrections as Correction[]
        }

        async function fetchCorrection(brief_id: number): Promise<void> {
            const res = await api<ReturnData<{ correction: Correction}>>(`/student/corrections/${brief_id}`)
            correction.value = res.data?.correction as Correction
        }

        async function fetchStudentCorrection(brief_id: number, student_id: number): Promise<void> {
            const res = await api<ReturnData<{ correction: Correction}>>(`/teacher/corrections/${brief_id}/${student_id}`)
            correction.value = res.data?.correction as Correction
        }

        async function fetchStudentCorrectionsById(student_id: number): Promise<void> {
            const res = await api<ReturnData<{ corrections: Correction[]}>>(`/teacher/corrections/students/${student_id}`)
            corrections.value = res.data?.corrections as Correction[]
        }

        async function createCorrection(data: { brief_id: number, student_id: number, message: string, details: { brief_skill_level_id: number, grade: string }[] }): Promise<ReturnData<any>> {
            try{
                const res = await api<ReturnData<{ correction: Correction, message: string }>>(`/teacher/corrections`, {
                    method: 'POST',
                    body: data
                })
                corrections.value?.push(res.data?.correction as Correction)
                return {
                    success: true,
                    data: res.data?.message
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    if (err.data.errors.brief_id || err.data.errors.student_id || err.data.errors.message || err.data.errors.details) {
                        let { brief_id, student_id, message, details } = err.data.errors;
                        brief_id = brief_id ? brief_id[0] : "";
                        student_id = student_id ? student_id[0] : "";
                        message = message ? message[0] : "";
                        details = details ? details[0] : "";
                        return {
                            success: false,
                            errors: {
                                brief_id,
                                student_id,
                                message,
                                details
                            },
                        }
                    }
                }
                return {
                    success: false,
                    errors: {
                        brief_id: '',
                        student_id: '',
                        message: '',
                        details: ''
                    },
                }
            }
        }

        async function updateCorrection(id: number, data: { brief_id: number, student_id: number, message: string, details: { brief_skill_level_id: number, grade: string }[] }): Promise<ReturnData<any>> {
            try{
                const res = await api<ReturnData<{ correction: Correction, message: string }>>(`/teacher/corrections/${id}`, {
                    method: 'PUT',
                    body: data
                })

                if(!corrections.value?.length){
                    await fetchStudentCorrectionsById(data.student_id)
                }

                const correction = corrections.value?.find(y => y.id === id) as Correction

                Object.assign(correction, res.data?.correction as Correction)
                return {
                    success: true,
                    data: res.data?.message
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    if (err.data.errors.brief_id || err.data.errors.student_id || err.data.errors.message || err.data.errors.details) {
                        let { brief_id, student_id, message, details } = err.data.errors;
                        brief_id = brief_id ? brief_id[0] : "";
                        student_id = student_id ? student_id[0] : "";
                        message = message ? message[0] : "";
                        details = details ? details[0] : "";
                        return {
                            success: false,
                            errors: {
                                brief_id,
                                student_id,
                                message,
                                details
                            },
                        }
                    }
                }
                return {
                    success: false,
                    errors: {
                        brief_id: '',
                        student_id: '',
                        message: '',
                        details: ''
                    },
                }
            }
        }

        return {
            corrections,
            correction,
            fetchStudentCorrections,
            fetchStudentCorrection,
            fetchStudentCorrectionsById,
            fetchCorrection,
            createCorrection,
            updateCorrection
        }
    },
    {
        persist: {
            pick: ['corrections'],
        },
    }
)
