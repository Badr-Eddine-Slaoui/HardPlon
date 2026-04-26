import { defineStore } from 'pinia';
import type { ReturnData } from '../types/api';
import { useToastStore } from './toast';

export const useProblemSubmission = defineStore('problem_submission', () => {
    const toast = useToastStore()
    const api = useApi()

    async function storeProblemSubmissions(submission_id: number, submissions: Array<{ problem_id: number, code: string }>): Promise<ReturnData> {
        try {
            const res = await api<ReturnData>('/student/problem-submissions', {
                method: 'POST',
                body: {
                    submission_id,
                    submissions
                }
            })

            toast.push({
                message: res.message || 'Problem submissions stored successfully.',
                type: 'success',
            })

            return {
                success: true,
                message: res.message || 'Problem submissions stored successfully.',
                data: res.data
            }
        } catch (err: any) {
            toast.push({
                message: err.data?.message || 'Something went wrong. Please try again.',
                type: 'error',
            })

            return {
                success: false,
                message: err.data?.message || 'Something went wrong. Please try again.',
                data: null,
                errors: err.data?.errors
            }
        }
    }

    return {
        storeProblemSubmissions
    }
})
