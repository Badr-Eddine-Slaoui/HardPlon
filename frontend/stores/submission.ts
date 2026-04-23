import { defineStore } from 'pinia';
import { reactive, ref } from 'vue';
import { api } from '~/utils/api';
import type { meta, ReturnData } from '../types/api';
import type { Submission } from '../types/submission';
import { useToastStore } from './toast';

export const useSubmission = defineStore(
    'submission',
    () => {
        const submissions = ref<Submission[] | null>(null)
        const submission = ref<Submission | null>(null)
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

        async function fetchStudentSubmissions(): Promise<void> {
            try {
                const res = await api<ReturnData<{ submissions: Submission[]}>>('/student/submissions')
                submissions.value = res.data?.submissions as Submission[]
                Object.assign(meta, res.data?.submissions)
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function fetchSubmission(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ submission: Submission}>>(`/student/submissions/${id}`)
                submission.value = res.data?.submission as Submission
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function fetchSubmissionsByStudentId(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ submissions: Submission[]}>>(`/teacher/submissions/student/${id}`)
                submissions.value = res.data?.submissions as Submission[]
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function createSubmission(data: { brief_id: number, message: string, link: string }): Promise<ReturnData> {
            try{
                const res = await api<ReturnData<{ submission: Submission }>>(`/student/submissions`, {
                    method: 'POST',
                    body: data
                })

                if (!submissions.value?.length) {
                    await fetchStudentSubmissions()
                }

                submissions.value?.push(res.data?.submission as Submission)

                toast.push({
                    message: res?.message || 'Submission created successfully.',
                    type: 'success',
                })

                return {
                    success: true,
                    message: res?.message || 'Submission created successfully.',
                    data: null
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    if (err.data.errors.brief_id || err.data.errors.message || err.data.errors.link) {
                        let { brief_id, message, link } = err.data.errors;
                        brief_id = brief_id ? brief_id[0] : "";
                        message = message ? message[0] : "";
                        link = link ? link[0] : "";

                        toast.push({
                            message: "Validation error. Please check your input.",
                            type: 'error',
                        })

                        return {
                            success: false,
                            message: "Validation error. Please check your input.",
                            data: null,
                            errors: {
                                brief_id,
                                message,
                                link
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
                    message: err.message || 'Something went wrong. Please try again.',
                    data: null,
                    errors: {
                        brief_id: '',
                        message: '',
                        link: ''
                    },
                }
            }
        }

        return {
            submissions,
            submission,
            meta,
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
