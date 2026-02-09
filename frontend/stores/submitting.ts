import { defineStore } from 'pinia';
import { ref } from 'vue';
import { api } from '~~/utils/api';
import type { ReturnData } from '../types/api';
import type { Submitting } from '../types/submitting';

export const useSubmitting = defineStore(
    'submitting',
    () => {
        const submittings = ref<Submitting[] | null>(null)
        const submitting = ref<Submitting | null>(null)

        async function fetchStudentSubmittings(): Promise<void> {
            const res = await api<ReturnData<{ submittings: Submitting[]}>>('/student/submittings')
            submittings.value = res.data?.submittings as Submitting[]
        }

        async function fetchSubmitting(id: number): Promise<void> {
            const res = await api<ReturnData<{ submitting: Submitting}>>(`/student/submittings/${id}`)
            submitting.value = res.data?.submitting as Submitting
        }

        async function fetchSubmittingsByStudentId(id: number): Promise<void> {
            const res = await api<ReturnData<{ submittings: Submitting[]}>>(`/teacher/submittings/student/${id}`)
            submittings.value = res.data?.submittings as Submitting[]
        }

        async function createSubmitting(data: { brief_id: number, message: string, links: { [key: string]: string}[] }): Promise<ReturnData<any>> {
            try{
                const res = await api<ReturnData<{ submitting: Submitting, message: string }>>(`/student/submittings`, {
                    method: 'POST',
                    body: data
                })
                submittings.value?.push(res.data?.submitting as Submitting)
                return {
                    success: true,
                    data: res.data?.message
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    if (err.data.errors.brief_id || err.data.errors.message || err.data.errors.links) {
                        let { brief_id, message, links } = err.data.errors;
                        brief_id = brief_id ? brief_id[0] : "";
                        message = message ? message[0] : "";
                        links = links ? links[0] : "";
                        return {
                            success: false,
                            errors: {
                                brief_id,
                                message,
                                links
                            },
                        }
                    }
                }
                return {
                    success: false,
                    errors: {
                        brief_id: '',
                        message: '',
                        links: ''
                    },
                }
            }
        }

        return {
            submittings,
            submitting,
            fetchStudentSubmittings,
            fetchSubmittingsByStudentId,
            fetchSubmitting,
            createSubmitting
        }
    },
    {
        persist: {
            pick: ['submittings'],
        },
    }
)
