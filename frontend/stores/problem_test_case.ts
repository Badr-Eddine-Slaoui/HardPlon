import { defineStore } from 'pinia';
import { reactive, ref } from 'vue';
import type { meta, ReturnData } from '~~/types/api';
import type { ProblemTestCase } from '~~/types/problem_test_case';
import { useToastStore } from './toast';
import type { PaginatedData } from '~~/types/pagination';

export const useProblemTestCase = defineStore(
    'problem_test_case',
    () => {
        const api = useApi()
        const test_cases = ref<ProblemTestCase[] | null>(null)
        const test_case = ref<ProblemTestCase | null>(null)
        const archived_test_cases = ref<ProblemTestCase[] | null>(null)
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

        async function fetchTestCases(page: number = 1, per_page: number = 5): Promise<void> {
            try {
                const res = await api<ReturnData<{ problem_test_cases: PaginatedData<ProblemTestCase[]>, archived_problem_test_cases: PaginatedData<ProblemTestCase[]>}>>(`/teacher/problem-test-cases?page=${page}&per_page=${per_page}`)
                test_cases.value = res.data?.problem_test_cases?.data as ProblemTestCase[]
                archived_test_cases.value = res.data?.archived_problem_test_cases?.data as ProblemTestCase[]
                Object.assign(meta, res.data?.problem_test_cases)
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function fetchTestCase(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ problemTestCase: ProblemTestCase }>>(`/teacher/problem-test-cases/${id}`)
                test_case.value = res.data?.problemTestCase as ProblemTestCase
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function createTestCase(data: any): Promise<ReturnData> {
            try {
                const res = await api<ReturnData<{ problemTestCase: ProblemTestCase }>>('/teacher/problem-test-cases', {
                    method: 'POST',
                    body: data
                })

                toast.push({
                    message: res.message || 'Test case created successfully.',
                    type: 'success',
                })

                return {
                    success: true,
                    message: res.message || 'Test case created successfully.',
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

        async function archiveTestCase(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ problemTestCase: ProblemTestCase }>>(`/teacher/problem-test-cases/${id}`, {
                    method: 'DELETE'
                })

                toast.push({
                    message: res.message || 'Test case archived successfully.',
                    type: 'success',
                })
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function restoreTestCase(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ problemTestCase: ProblemTestCase }>>(`/teacher/problem-test-cases/${id}/restore`, {
                    method: 'POST'
                })

                toast.push({
                    message: res.message || 'Test case restored successfully.',
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
            test_cases,
            test_case,
            archived_test_cases,
            meta,
            fetchTestCases,
            fetchTestCase,
            createTestCase,
            archiveTestCase,
            restoreTestCase
        }
    },
    {
        persist: {
            pick: ['test_cases', 'archived_test_cases'],
        },
    }
)
