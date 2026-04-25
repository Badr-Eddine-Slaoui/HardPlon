import { defineStore } from 'pinia';
import { reactive, ref } from 'vue';
import { api } from '~/utils/api';
import type { meta, ReturnData } from '../types/api';
import type { Brief, BriefSkillLevel } from '../types/brief';
import type { PaginatedData } from '../types/pagination';
import { useToastStore } from './toast';

export const useBrief = defineStore(
    'brief',
    () => {
        const briefs = ref<Brief[] | null>(null)
        const brief = ref<Brief | null>(null)
        const archived_briefs = ref<Brief[] | null>(null)
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

        async function fetchBriefs(page: number = 1, per_page: number = 5): Promise<void> {
            try {
                const res = await api<ReturnData<{ briefs: PaginatedData<Brief[]>, archived_briefs: PaginatedData<Brief[]>}>>(`/teacher/get_teacher_briefs?page=${page}&per_page=${per_page}`)
                briefs.value = res.data?.briefs?.data as Brief[]
                archived_briefs.value = res.data?.archived_briefs?.data as Brief[]
                Object.assign(meta, res.data?.briefs)
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function fetchBrief(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ brief: Brief}>>(`/teacher/briefs/${id}`)
                brief.value = res.data?.brief as Brief
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function fetchStudentBrief(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ brief: Brief}>>(`/student/briefs/${id}`)
                brief.value = res.data?.brief as Brief
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function fetchStudentBriefs(): Promise<Brief[] | []> {
            try {
                const res = await api<ReturnData<{ briefs: Brief[] }>>('/student/briefs')
                return res.data?.briefs as Brief[]
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
                return []
            }
        }

        async function createBrief(data: { sprint_id: number, class_group_id: number, stack_id: number, title: string, description: string, is_collective: boolean, content: string, start_date: string, end_date: string, skill_levels: { level_id: number, skill_id: number}[] }): Promise<ReturnData> {
            try{
                const res = await api<ReturnData<{ brief: Brief}>>('/teacher/briefs', {
                    method: 'POST',
                    body: data
                })

                briefs.value?.unshift(res.data?.brief as Brief)

                toast.push({
                    message: res?.message || 'Brief created successfully.',
                    type: 'success',
                })

                return {
                    success: true,
                    data: null,
                    message: res.message
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    if (err.data.errors.sprint_id || err.data.errors.class_group_id || err.data.errors.stack_id || err.data.errors.title || err.data.errors.description || err.data.errors.is_collective || err.data.errors.content || err.data.errors.start_date || err.data.errors.end_date || err.data.errors.skill_levels) {
                        let { sprint_id, class_group_id, stack_id, title, description, is_collective, content, start_date, end_date, skill_levels } = err.data.errors;
                        sprint_id = sprint_id ? sprint_id[0] : "";
                        class_group_id = class_group_id ? class_group_id[0] : "";
                        stack_id = stack_id ? stack_id[0] : "";
                        title = title ? title[0] : "";
                        description = description ? description[0] : "";
                        is_collective = is_collective ? is_collective[0] : "";
                        content = content ? content[0] : "";
                        start_date = start_date ? start_date[0] : "";
                        end_date = end_date ? end_date[0] : "";
                        skill_levels = skill_levels ? skill_levels[0] : "";

                        toast.push({
                            message: "Validation error. Please check your input.",
                            type: 'error',
                        })

                        return {
                            success: false,
                            message: "Validation error. Please check your input.",
                            data: null,
                            errors: {
                                sprint_id,
                                class_group_id,
                                stack_id,
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

                toast.push({
                    message: err?.data?.message || 'Something went wrong. Please try again.',
                    type: 'error',
                })

                return {
                    success: false,
                    data: null,
                    message: err?.data?.message || 'Something went wrong. Please try again.',
                    errors: {
                        sprint_id: '',
                        class_group_id: '',
                        stack_id: '',
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

        async function updateBrief(id: number, data: { sprint_id: number, class_group_id: number, stack_id: number, title: string, description: string, is_collective: boolean, content: string, start_date: string, end_date: string, skill_levels: { level_id: number, skill_id: number}[] }): Promise<ReturnData> {
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

                toast.push({
                    message: res?.message || 'Brief updated successfully.',
                    type: 'success',
                })

                return {
                    success: true,
                    message: res?.message || 'Brief updated successfully.',
                    data: null
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    if (err.data.errors.sprint_id || err.data.errors.class_group_id || err.data.errors.stack_id || err.data.errors.title || err.data.errors.description || err.data.errors.is_collective || err.data.errors.content || err.data.errors.start_date || err.data.errors.end_date || err.data.errors.skill_levels) {
                        let { sprint_id, class_group_id, stack_id, title, description, is_collective, content, start_date, end_date, skill_levels } = err.data.errors;
                        sprint_id = sprint_id ? sprint_id[0] : "";
                        class_group_id = class_group_id ? class_group_id[0] : "";
                        stack_id = stack_id ? stack_id[0] : "";
                        title = title ? title[0] : "";
                        description = description ? description[0] : "";
                        is_collective = is_collective ? is_collective[0] : "";
                        content = content ? content[0] : "";
                        start_date = start_date ? start_date[0] : "";
                        end_date = end_date ? end_date[0] : "";
                        skill_levels = skill_levels ? skill_levels[0] : "";

                        toast.push({
                            message: "Validation error. Please check your input.",
                            type: 'error',
                        })

                        return {
                            success: false,
                            message: "Validation error. Please check your input.",
                            data: null,
                            errors: {
                                sprint_id,
                                class_group_id,
                                stack_id,
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

                toast.push({
                    message: err?.data?.message || 'Something went wrong. Please try again.',
                    type: 'error',
                })

                return {
                    success: false,
                    data: null,
                    message: err?.data?.message || 'Something went wrong. Please try again.',
                    errors: {
                        sprint_id: '',
                        class_group_id: '',
                        stack_id: '',
                        title: '',
                        description: '',
                        is_collective: '',
                        content: '',
                        start_date: '',
                        end_date: '',
                        skill_levels: '',
                        message: err.message || 'Something went wrong. Please try again.'
                    },
                }
            }
        }

        async function archiveBrief(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ brief: Brief }>>(`/teacher/briefs/${id}`, {
                    method: 'DELETE'
                })

                if(!briefs.value?.length){
                    await fetchBriefs()
                }

                const restoredBrief = res.data?.brief as Brief
                const year = briefs.value?.find(y => y.id === id) as Brief

                Object.assign(year, restoredBrief)

                toast.push({
                    message: res?.message || 'Brief archived successfully.',
                    type: 'success',
                })
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function restoreBrief(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ brief: Brief }>>(`/teacher/briefs/${id}/restore`, {
                    method: 'POST'
                })

                if(!briefs.value?.length){
                    await fetchBriefs()
                }
                
                const restoredBrief = res.data?.brief as Brief
                const year = briefs.value?.find(y => y.id === id) as Brief

                Object.assign(year, restoredBrief)

                toast.push({
                    message: res?.message || 'Brief restored successfully.',
                    type: 'success',
                })
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function fetchBriefSkillLevels(briefId: number): Promise<BriefSkillLevel[]> {
            try {
                const res = await api<ReturnData<BriefSkillLevel[]>>(`/teacher/briefs/${briefId}/skill-levels`)
                return res.data as BriefSkillLevel[]
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
                return []
            }
        }

        async function fetchClassGroupBriefs(classGroupId: number): Promise<Brief[]> {
            try {
                const res = await api<ReturnData<{ briefs: Brief[] }>>(`/teacher/briefs/class-group/${classGroupId}`)
                return res.data?.briefs as Brief[]
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
                return []
            }
        }

        return {
            briefs,
            brief,
            archived_briefs,
            meta,
            fetchBriefs,
            fetchStudentBriefs,
            fetchBrief,
            fetchStudentBrief,
            createBrief,
            updateBrief,
            archiveBrief,
            restoreBrief,
            fetchBriefSkillLevels,
            fetchClassGroupBriefs
        }
    },
    {
        persist: {
            pick: ['briefs', 'archived_briefs'],
        },
    }
)
