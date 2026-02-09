import { defineStore } from 'pinia';
import { ref } from 'vue';
import { api } from '~~/utils/api';
import type { ReturnData } from '../types/api';
import type { Level, LevelData } from '../types/level';

export const useLevel = defineStore(
    'level',
    () => {
        const levels = ref<Level[] | null>(null)
        const level = ref<Level | null>(null)
        const archived_levels = ref<Level[] | null>(null)

        async function fetchLevels(): Promise<void> {
            const res = await api<ReturnData<LevelData>>('/admin/levels')
            levels.value = res.data?.levels as Level[]
            archived_levels.value = res.data?.archived_levels as Level[]
        }

        async function fetchLevel(id: number): Promise<void> {
            const res = await api<ReturnData<{ level: Level}>>(`/admin/levels/${id}`)
            level.value = res.data?.level as Level
        }

        async function createLevel(data: { name: string, description: string, order: number }): Promise<ReturnData<any>> {
            try{
                const res = await api<ReturnData<{ level: Level, message: string}>>('/admin/levels', {
                    method: 'POST',
                    body: data
                })
                levels.value?.push(res.data?.level as Level)
                return {
                    success: true,
                    data: res.data?.message
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    if (err.data.errors.name || err.data.errors.description || err.data.errors.order) {
                        let { name, description, order } = err.data.errors;
                        name = name ? name[0] : "";
                        description = description ? description[0] : "";
                        order = order ? order[0] : "";
                        return {
                            success: false,
                            errors: {
                                name,
                                description,
                                order
                            },
                        }
                    }
                }
                return {
                    success: false,
                    errors: {
                        name: '',
                        description: '',
                        order: '',
                        message: err.data.message
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
                return {
                    success: true,
                    data: res.data?.message
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    if (err.data.errors.name || err.data.errors.description || err.data.errors.order) {
                        let { name, description, order } = err.data.errors;
                        name = name ? name[0] : "";
                        description = description ? description[0] : "";
                        order = order ? order[0] : "";
                        return {
                            success: false,
                            errors: {
                                name,
                                description,
                                order
                            },
                        }
                    }
                }
                return {
                    success: false,
                    errors: {
                        name: '',
                        description: '',
                        order: '',
                        message: err.message
                    },
                }
            }
        }

        async function archiveLevel(id: number): Promise<void> {
            const res = await api<ReturnData<{ level: Level }>>(`/admin/levels/${id}`, {
                method: 'DELETE'
            })

            if(!levels.value?.length){
                await fetchLevels()
            }

            const restoredLevel = res.data?.level as Level
            const year = levels.value?.find(y => y.id === id) as Level

            Object.assign(year, restoredLevel)
        }

        async function restoreLevel(id: number): Promise<void> {
            const res = await api<ReturnData<{ level: Level }>>(`/admin/levels/${id}/restore`, {
                method: 'POST'
            })

            if(!levels.value?.length){
                await fetchLevels()
            }
            
            const restoredLevel = res.data?.level as Level
            const year = levels.value?.find(y => y.id === id) as Level

            Object.assign(year, restoredLevel)
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
