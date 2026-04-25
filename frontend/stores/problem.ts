import { defineStore } from 'pinia';
import { reactive, ref } from 'vue';
import { api } from '~/utils/api';
import type { meta, ReturnData } from '~~/types/api';
import type { Problem } from '~~/types/problem';
import { useToastStore } from './toast';
import type { PaginatedData } from '~~/types/pagination';

export const useProblem = defineStore(
    'problem',
    () => {
        const problems = ref<Problem[] | null>(null)
        const problem = ref<Problem | null>(null)
        const archived_problems = ref<Problem[] | null>(null)
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

        async function fetchProblems(page: number = 1, per_page: number = 5): Promise<void> {
            try {
                const res = await api<ReturnData<{ problems: PaginatedData<Problem[]>, archived_problems: PaginatedData<Problem[]>}>>(`/teacher/problems?page=${page}&per_page=${per_page}`)
                problems.value = res.data?.problems?.data as Problem[]
                archived_problems.value = res.data?.archived_problems?.data as Problem[]
                Object.assign(meta, res.data?.problems)
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function fetchProblem(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ problem: Problem }>>(`/teacher/problems/${id}`)
                problem.value = res.data?.problem as Problem
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function createProblem(data: any): Promise<ReturnData> {
            try {
                const res = await api<ReturnData<{ problem: Problem }>>('/teacher/problems', {
                    method: 'POST',
                    body: data
                })

                toast.push({
                    message: res.message || 'Problem created successfully.',
                    type: 'success',
                })

                return {
                    success: true,
                    message: res.message || 'Problem created successfully.',
                    data: res.data
                }
            } catch (err: any) {
                return {
                    success: false,
                    message: err.data?.message || 'Validation error. Please check your input.',
                    data: null,
                    errors: err.data?.errors
                }
            }
        }

        async function updateProblem(id: number, data: any): Promise<ReturnData> {
            try {
                const res = await api<ReturnData<{ problem: Problem }>>(`/teacher/problems/${id}`, {
                    method: 'PUT',
                    body: data
                })

                toast.push({
                    message: res.message || 'Problem updated successfully.',
                    type: 'success',
                })

                return {
                    success: true,
                    message: res.message || 'Problem updated successfully.',
                    data: res.data
                }
            } catch (err: any) {
                return {
                    success: false,
                    message: err.data?.message || 'Validation error. Please check your input.',
                    data: null,
                    errors: err.data?.errors
                }
            }
        }

        async function archiveProblem(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ problem: Problem }>>(`/teacher/problems/${id}`, {
                    method: 'DELETE'
                })

                if (problems.value) {
                    problems.value = problems.value.filter(p => p.id !== id)
                }
                
                await fetchProblems(meta.current_page, meta.per_page)

                toast.push({
                    message: res.message || 'Problem archived successfully.',
                    type: 'success',
                })
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function restoreProblem(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ problem: Problem }>>(`/teacher/problems/${id}/restore`, {
                    method: 'POST'
                })

                await fetchProblems(meta.current_page, meta.per_page)

                toast.push({
                    message: res.message || 'Problem restored successfully.',
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
            problems,
            problem,
            archived_problems,
            meta,
            fetchProblems,
            fetchProblem,
            createProblem,
            updateProblem,
            archiveProblem,
            restoreProblem
        }
    },
    {
        persist: {
            pick: ['problems', 'archived_problems'],
        },
    }
)
