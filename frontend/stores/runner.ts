import { defineStore } from 'pinia';
import { reactive, ref } from 'vue';
import type { meta, ReturnData } from '../types/api';
import type { Runner } from '../types/runner';
import { useToastStore } from './toast';
import type { PaginatedData } from '../types/pagination';

export const useRunner = defineStore(
    'runner',
    () => {
        const api = useApi()
        const runners = ref<Runner[] | null>(null)
        const runner = ref<Runner | null>(null)
        const archived_runners = ref<Runner[] | null>(null)
        const all_runners = ref<Runner[] | null>(null)
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


        async function fetchRunners(page: number = 1, per_page: number = 5): Promise<void> {
            try {
                const res = await api<ReturnData<{ runners: PaginatedData<Runner[]>, archived_runners: PaginatedData<Runner[]>}>>(`/admin/runners?page=${page}&per_page=${per_page}`)
                runners.value = res.data?.runners?.data as Runner[]
                archived_runners.value = res.data?.archived_runners?.data as Runner[]
                Object.assign(meta, res.data?.runners)
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function fetchAllRunners(): Promise<void> {
            try {
                const res = await api<ReturnData<{ runners: Runner[] }>>(`/admin/runners/all`)
                all_runners.value = res.data?.runners as Runner[]
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function fetchRunner(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ runner: Runner}>>(`/admin/runners/${id}`)
                runner.value = res.data?.runner as Runner
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function createRunner(data: { name: string, description: string, type: string, status: string }): Promise<ReturnData> {
            try{
                const res = await api<ReturnData<{ runner: Runner, message: string}>>('/admin/runners', {
                    method: 'POST',
                    body: data
                })

                if(!runners.value?.length){
                    await fetchRunners()
                }

                runners.value?.unshift(res.data?.runner as Runner)

                toast.push({
                    message: res?.message || 'Runner created successfully.',
                    type: 'success',
                })

                return {
                    success: true,
                    message: res?.message || 'Runner created successfully.',
                    data: null
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    if (err.data.errors.name || err.data.errors.description || err.data.errors.type || err.data.errors.status) {
                        let { name, description, type, status } = err.data.errors;
                        name = name ? name[0] : "";
                        description = description ? description[0] : "";
                        type = type ? type[0] : "";
                        status = status ? status[0] : "";

                        toast.push({
                            message: "Validation error. Please check your input.",
                            type: 'error',
                        })

                        return {
                            success: false,
                            message: "Validation error. Please check your input.",
                            data: null,
                            errors: {
                                name,
                                description,
                                type,
                                status
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
                        type: '',
                        status: '',
                        message: err.message || 'Something went wrong. Please try again.'
                    },
                }
            }
        }

        async function updateRunner(id: number, data: { name: string, description: string, type: string, status: string }): Promise<ReturnData<any>> {
            try{
                const res = await api<ReturnData<{ runner: Runner, message: string}>>(`/admin/runners/${id}`, {
                    method: 'PUT',
                    body: data
                })

                if(!runners.value?.length){
                    await fetchRunners()
                }

                const updatedRunner = res.data?.runner as Runner
                const existing = runners.value?.find(r => r.id === id) as Runner

                Object.assign(existing, updatedRunner)

                toast.push({
                    message: res.data?.message || 'Runner updated successfully.',
                    type: 'success',
                })

                return {
                    success: true,
                    data: null,
                    message: res.data?.message || 'Runner updated successfully.'
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    if (err.data.errors.name || err.data.errors.description || err.data.errors.type || err.data.errors.status) {
                        let { name, description, type, status } = err.data.errors;
                        name = name ? name[0] : "";
                        description = description ? description[0] : "";
                        type = type ? type[0] : "";
                        status = status ? status[0] : "";

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
                                type,
                                status
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
                        type: '',
                        status: '',
                        message: err.message || 'Something went wrong. Please try again.'
                    },
                }
            }
        }

        async function deleteRunner(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ runner: Runner }>>(`/admin/runners/${id}`, {
                    method: 'DELETE'
                })

                if(runners.value?.length){
                    runners.value = runners.value.filter(r => r.id !== id)
                }

                toast.push({
                    message: res?.message || 'Runner deleted successfully.',
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
            runners,
            runner,
            archived_runners,
            all_runners,
            meta,
            fetchRunners,
            fetchAllRunners,
            fetchRunner,
            createRunner,
            updateRunner,
            deleteRunner
        }
    },
    {
        persist: {
            pick: ['runners', 'archived_runners', 'all_runners'],
        },
    }
)
