import { defineStore } from 'pinia';
import { reactive, ref } from 'vue';
import { api } from '~/utils/api';
import type { meta, ReturnData } from '../types/api';
import type { Level } from '../types/level';
import { useToastStore } from './toast';
import { PaginatedData } from '../types/pagination';

export const useLevel = defineStore(
    'level',
    () => {
        const levels = ref<Level[] | null>(null)
        const level = ref<Level | null>(null)
        const archived_levels = ref<Level[] | null>(null)
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


        async function fetchLevels(page: number = 1, per_page: number = 5): Promise<void> {
            try {
                const res = await api<ReturnData<{ levels: PaginatedData<Level[]>, archived_levels: PaginatedData<Level[]>}>>(`/admin/levels?page=${page}&per_page=${per_page}`)
                levels.value = res.data?.levels?.data as Level[]
                archived_levels.value = res.data?.archived_levels?.data as Level[]
                Object.assign(meta, res.data?.levels)
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function fetchLevel(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ level: Level}>>(`/admin/levels/${id}`)
                level.value = res.data?.level as Level
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function createLevel(data: { name: string, description: string, order: number }): Promise<ReturnData> {
            try{
                const res = await api<ReturnData<{ level: Level, message: string}>>('/admin/levels', {
                    method: 'POST',
                    body: data
                })

                if(!levels.value?.length){
                    await fetchLevels()
                }

                levels.value?.unshift(res.data?.level as Level)

                toast.push({
                    message: res?.message || 'Level created successfully.',
                    type: 'success',
                })

                return {
                    success: true,
                    message: res?.message || 'Level created successfully.',
                    data: null
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    if (err.data.errors.name || err.data.errors.description || err.data.errors.order) {
                        let { name, description, order } = err.data.errors;
                        name = name ? name[0] : "";
                        description = description ? description[0] : "";
                        order = order ? order[0] : "";

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
                                order
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
                        order: '',
                        message: err.message || 'Something went wrong. Please try again.'
                    },
                }
            }
        }

        async function updateLevel(id: number, data: { name: string, description: string, order: number }): Promise<ReturnData<any>> {
            try{
                const res = await api<ReturnData<{ level: Level, message: string}>>(`/admin/levels/${id}`, {
                    method: 'PUT',
                    body: data
                })

                if(!levels.value?.length){
                    await fetchLevels()
                }

                const restoredLevel = res.data?.level as Level
                const year = levels.value?.find(y => y.id === id) as Level

                Object.assign(year, restoredLevel)

                toast.push({
                    message: res.data?.message || 'Level updated successfully.',
                    type: 'success',
                })

                return {
                    success: true,
                    data: null,
                    message: res.data?.message || 'Level updated successfully.'
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    if (err.data.errors.name || err.data.errors.description || err.data.errors.order) {
                        let { name, description, order } = err.data.errors;
                        name = name ? name[0] : "";
                        description = description ? description[0] : "";
                        order = order ? order[0] : "";

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
                                order
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
                        order: '',
                        message: err.message || 'Something went wrong. Please try again.'
                    },
                }
            }
        }

        async function archiveLevel(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ level: Level }>>(`/admin/levels/${id}`, {
                    method: 'DELETE'
                })

                if(!levels.value?.length){
                    await fetchLevels()
                }

                const restoredLevel = res.data?.level as Level
                const year = levels.value?.find(y => y.id === id) as Level

                Object.assign(year, restoredLevel)

                toast.push({
                    message: res?.message || 'Level archived successfully.',
                    type: 'success',
                })
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function restoreLevel(id: number): Promise<void> {
            try{
                const res = await api<ReturnData<{ level: Level }>>(`/admin/levels/${id}/restore`, {
                    method: 'POST'
                })

                if(!levels.value?.length){
                    await fetchLevels()
                }
                
                const restoredLevel = res.data?.level as Level
                const year = levels.value?.find(y => y.id === id) as Level

                Object.assign(year, restoredLevel)

                toast.push({
                    message: res?.message || 'Level restored successfully.',
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
            levels,
            level,
            archived_levels,
            fetchLevels,
            fetchLevel,
            createLevel,
            updateLevel,
            archiveLevel,
            restoreLevel
        }
    },
    {
        persist: {
            pick: ['levels', 'archived_levels'],
        },
    }
)
