import { defineStore } from 'pinia';
import { ref } from 'vue';
import { api } from '~/utils/api';
import type { ReturnData } from '../types/api';
import type { Brief, BriefData } from '../types/brief';

export const useBrief = defineStore(
    'brief',
    () => {
        const briefs = ref<Brief[] | null>(null)
        const brief = ref<Brief | null>(null)
        const archived_briefs = ref<Brief[] | null>(null)

        async function fetchBriefs(): Promise<void> {
            const res = await api<ReturnData<BriefData>>('/teacher/briefs')
            briefs.value = res.data?.briefs as Brief[]
            archived_briefs.value = res.data?.archived_briefs as Brief[]
        }

        async function fetchBrief(id: number): Promise<void> {
            const res = await api<ReturnData<{ brief: Brief}>>(`/teacher/briefs/${id}`)
            brief.value = res.data?.brief as Brief
        }

        async function fetchStudentBriefs(): Promise<Brief[]> {
            const res = await api<ReturnData<BriefData>>('/student/briefs')
            return res.data?.briefs as Brief[]
        }

        async function createBrief(data: { sprint_id: number, class_group_id: number, title: string, description: string, is_collective: boolean, content: string, start_date: string, end_date: string, skill_levels: { level_id: number, skill_id: number}[] }): Promise<ReturnData<any>> {
            try{
                const res = await api<ReturnData<{ brief: Brief, message: string}>>('/teacher/briefs', {
                    method: 'POST',
                    body: data
                })
                briefs.value?.push(res.data?.brief as Brief)
                return {
                    success: true,
                    data: res.data?.message
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    if (err.data.errors.sprint_id || err.data.errors.class_group_id || err.data.errors.title || err.data.errors.description || err.data.errors.is_collective || err.data.errors.content || err.data.errors.start_date || err.data.errors.end_date || err.data.errors.skill_levels) {
                        let { sprint_id, class_group_id, title, description, is_collective, content, start_date, end_date, skill_levels } = err.data.errors;
                        sprint_id = sprint_id ? sprint_id[0] : "";
                        class_group_id = class_group_id ? class_group_id[0] : "";
                        title = title ? title[0] : "";
                        description = description ? description[0] : "";
                        is_collective = is_collective ? is_collective[0] : "";
                        content = content ? content[0] : "";
                        start_date = start_date ? start_date[0] : "";
                        end_date = end_date ? end_date[0] : "";
                        skill_levels = skill_levels ? skill_levels[0] : "";
                        return {
                            success: false,
                            errors: {
                                sprint_id,
                                class_group_id,
                                title,
                                description,
                                is_collective,
                                content,
                                start_date,
                                end_date,
                                skill_levels
                            },
                        }
                    }
                }
                return {
                    success: false,
                    errors: {
                        sprint_id: '',
                        class_group_id: '',
                        title: '',
                        description: '',
                        is_collective: '',
                        content: '',
                        start_date: '',
                        end_date: '',
                        skill_levels: '',
                        message: err.data.message
                    },
                }
            }
        }

        async function updateBrief(id: number, data: { sprint_id: number, class_group_id: number, title: string, description: string, is_collective: boolean, content: string, start_date: string, end_date: string, skill_levels: { level_id: number, skill_id: number}[] }): Promise<ReturnData<any>> {
            try{
                const res = await api<ReturnData<{ brief: Brief, message: string}>>(`/teacher/briefs/${id}`, {
                    method: 'PUT',
                    body: data
                })

                if(!briefs.value?.length){
                    await fetchBriefs()
                }

                const restoredBrief = res.data?.brief as Brief
                const year = briefs.value?.find(y => y.id === id) as Brief

                Object.assign(year, restoredBrief)
                return {
                    success: true,
                    data: res.data?.message
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    if (err.data.errors.sprint_id || err.data.errors.class_group_id || err.data.errors.title || err.data.errors.description || err.data.errors.is_collective || err.data.errors.content || err.data.errors.start_date || err.data.errors.end_date || err.data.errors.skill_levels) {
                        let { sprint_id, class_group_id, title, description, is_collective, content, start_date, end_date, skill_levels } = err.data.errors;
                        sprint_id = sprint_id ? sprint_id[0] : "";
                        class_group_id = class_group_id ? class_group_id[0] : "";
                        title = title ? title[0] : "";
                        description = description ? description[0] : "";
                        is_collective = is_collective ? is_collective[0] : "";
                        content = content ? content[0] : "";
                        start_date = start_date ? start_date[0] : "";
                        end_date = end_date ? end_date[0] : "";
                        skill_levels = skill_levels ? skill_levels[0] : "";
                        return {
                            success: false,
                            errors: {
                                sprint_id,
                                class_group_id,
                                title,
                                description,
                                is_collective,
                                content,
                                start_date,
                                end_date,
                                skill_levels
                            },
                        }
                    }
                }
                return {
                    success: false,
                    errors: {
                        sprint_id: '',
                        class_group_id: '',
                        title: '',
                        description: '',
                        is_collective: '',
                        content: '',
                        start_date: '',
                        end_date: '',
                        skill_levels: '',
                        message: err.message
                    },
                }
            }
        }

        async function archiveBrief(id: number): Promise<void> {
            const res = await api<ReturnData<{ brief: Brief }>>(`/teacher/briefs/${id}`, {
                method: 'DELETE'
            })

            if(!briefs.value?.length){
                await fetchBriefs()
            }

            const restoredBrief = res.data?.brief as Brief
            const year = briefs.value?.find(y => y.id === id) as Brief

            Object.assign(year, restoredBrief)
        }

        async function restoreBrief(id: number): Promise<void> {
            const res = await api<ReturnData<{ brief: Brief }>>(`/teacher/briefs/${id}/restore`, {
                method: 'POST'
            })

            if(!briefs.value?.length){
                await fetchBriefs()
            }
            
            const restoredBrief = res.data?.brief as Brief
            const year = briefs.value?.find(y => y.id === id) as Brief

            Object.assign(year, restoredBrief)
        }

        return {
            briefs,
            brief,
            archived_briefs,
            fetchBriefs,
            fetchStudentBriefs,
            fetchBrief,
            createBrief,
            updateBrief,
            archiveBrief,
            restoreBrief
        }
    },
    {
        persist: {
            pick: ['briefs', 'archived_briefs'],
        },
    }
)
