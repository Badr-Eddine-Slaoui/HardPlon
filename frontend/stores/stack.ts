import { defineStore } from 'pinia';
import { reactive, ref } from 'vue';
import { api } from '~/utils/api';
import type { meta, ReturnData } from '../types/api';
import type { Stack } from '../types/stack';
import { useToastStore } from './toast';
import { PaginatedData } from '../types/pagination';

export const useStack = defineStore(
    'stack',
    () => {
        const stacks = ref<Stack[] | null>(null)
        const stack = ref<Stack | null>(null)
        const archived_stacks = ref<Stack[] | null>(null)
        const all_stacks = ref<Stack[] | null>(null)
        const errs = ref<any>({})
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

        async function fetchStacks(page: number = 1, per_page: number = 5): Promise<void> {
            try {
                const res = await api<ReturnData<{ stacks: PaginatedData<Stack[]>, archived_stacks: PaginatedData<Stack[]>}>>(`/admin/stacks?page=${page}&per_page=${per_page}`)
                stacks.value = res.data?.stacks?.data as Stack[]
                archived_stacks.value = res.data?.archived_stacks?.data as Stack[]
                Object.assign(meta, res.data?.stacks)
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function fetchStack(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ stack: Stack}>>(`/admin/stacks/${id}`)
                stack.value = res.data?.stack as Stack
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function createStack(data: { name: string, description: string, runner_versions: { runner_version_id: number, priority: number }[] }): Promise<ReturnData> {
            try{
                const res = await api<ReturnData<{ stack: Stack, message: string}>>('/admin/stacks', {
                    method: 'POST',
                    body: data
                })

                if(!stacks.value?.length){
                    await fetchStacks()
                }

                stacks.value?.unshift(res.data?.stack as Stack)

                toast.push({
                    message: res?.message || 'Stack created successfully.',
                    type: 'success',
                })

                return {
                    success: true,
                    message: res?.message || 'Stack created successfully.',
                    data: null
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    return {
                        success: false,
                        message: "Validation error. Please check your input.",
                        data: null,
                        errors: err.data.errors,
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
                        message: err.message || 'Something went wrong. Please try again.'
                    },
                }
            }
        }

        async function updateStack(id: number, data: { name: string, description: string, runner_versions: { runner_version_id: number, priority: number }[] }): Promise<ReturnData<any>> {
            try{
                const res = await api<ReturnData<{ stack: Stack, message: string}>>(`/admin/stacks/${id}`, {
                    method: 'PUT',
                    body: data
                })

                if(!stacks.value?.length){
                    await fetchStacks()
                }

                const updatedStack = res.data?.stack as Stack
                const target = stacks.value?.find(s => s.id === id) as Stack

                if (target) {
                    Object.assign(target, updatedStack)
                }

                toast.push({
                    message: res.data?.message || 'Stack updated successfully.',
                    type: 'success',
                })

                return {
                    success: true,
                    data: null,
                    message: res.data?.message || 'Stack updated successfully.'
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    return {
                        success: false,
                        data: null,
                        message: "Validation error. Please check your input.",
                        errors: err.data.errors,
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
                        message: err.message || 'Something went wrong. Please try again.'
                    },
                }
            }
        }

        async function archiveStack(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ stack: Stack }>>(`/admin/stacks/${id}`, {
                    method: 'DELETE'
                })

                if(!stacks.value?.length){
                    await fetchStacks()
                }

                const archivedStack = res.data?.stack as Stack
                const target = stacks.value?.find(s => s.id === id) as Stack

                if (target) {
                    Object.assign(target, archivedStack)
                }

                toast.push({
                    message: res?.message || 'Stack archived successfully.',
                    type: 'success',
                })
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function restoreStack(id: number): Promise<void> {
            try{
                const res = await api<ReturnData<{ stack: Stack }>>(`/admin/stacks/${id}/restore`, {
                    method: 'POST'
                })

                if(!stacks.value?.length){
                    await fetchStacks()
                }
                
                const restoredStack = res.data?.stack as Stack
                const target = stacks.value?.find(s => s.id === id) as Stack

                if (target) {
                    Object.assign(target, restoredStack)
                }

                toast.push({
                    message: res?.message || 'Stack restored successfully.',
                    type: 'success',
                })
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function fetchAllStacks(): Promise<void> {
            try {
                const res = await api<ReturnData<{ stacks: Stack[] }>>('/teacher/stacks')
                all_stacks.value = res.data?.stacks as Stack[]
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        return {
            stacks,
            stack,
            archived_stacks,
            all_stacks,
            errs,
            meta,
            fetchStacks,
            fetchStack,
            fetchAllStacks,
            createStack,
            updateStack,
            archiveStack,
            restoreStack
        }
    },
    {
        persist: {
            pick: ['stacks', 'archived_stacks', 'all_stacks'],
        },
    }
)
