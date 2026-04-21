import { defineStore } from 'pinia';
import { ref } from 'vue';
import { api } from '~/utils/api';
import type { ReturnData } from '../types/api';
import type { Submission } from '../types/submission';

export const useSubmission = defineStore(
    'submission',
    () => {
        const submissions = ref<Submission[] | null>(null)
        const submission = ref<Submission | null>(null)

        async function fetchStudentSubmissions(): Promise<void> {
            const res = await api<ReturnData<{ submissions: Submission[]}>>('/student/submissions')
            submissions.value = res.data?.submissions as Submission[]
        }

        async function fetchSubmission(id: number): Promise<void> {
            const res = await api<ReturnData<{ submission: Submission}>>(`/student/submissions/${id}`)
            submission.value = res.data?.submission as Submission
        }

        async function fetchSubmissionsByStudentId(id: number): Promise<void> {
            const res = await api<ReturnData<{ submissions: Submission[]}>>(`/teacher/submissions/student/${id}`)
            submissions.value = res.data?.submissions as Submission[]
        }

        async function createSubmission(data: { brief_id: number, message: string, links: { [key: string]: string}[] }): Promise<ReturnData<any>> {
            try{
                const res = await api<ReturnData<{ submission: Submission, message: string }>>(`/student/submissions`, {
                    method: 'POST',
                    body: data
                })
                submissions.value?.push(res.data?.submission as Submission)
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
            submissions,
            submission,
            fetchStudentSubmissions,
            fetchSubmissionsByStudentId,
            fetchSubmission,
            createSubmission
        }
    },
    {
        persist: {
            pick: ['submissions'],
        },
    }
)
