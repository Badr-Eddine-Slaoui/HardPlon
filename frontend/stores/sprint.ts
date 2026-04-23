import { defineStore } from 'pinia';
import { reactive, ref } from 'vue';
import { api } from '~/utils/api';
import type { meta, ReturnData } from '../types/api';
import type { Sprint, SprintSkill } from '../types/sprint';
import { useToastStore } from './toast';
import { PaginatedData } from '../types/pagination';

export const useSprint = defineStore(
    'sprint',
    () => {
        const sprints = ref<Sprint[] | null>(null)
        const sprint = ref<Sprint | null>(null)
        const archived_sprints = ref<Sprint[] | null>(null)
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

        async function fetchSprints(page: number = 1, per_page: number = 5): Promise<void> {
            try {
                const res = await api<ReturnData<{ sprints: PaginatedData<Sprint[]>, archived_sprints: PaginatedData<Sprint[]> }>>(`/admin/sprints?page=${page}&per_page=${per_page}`)
                sprints.value = res.data?.sprints?.data as Sprint[]
                archived_sprints.value = res.data?.archived_sprints?.data as Sprint[]
                Object.assign(meta, res.data?.sprints)
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function fetchSprint(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ sprint: Sprint}>>(`/admin/sprints/${id}`)
                sprint.value = res.data?.sprint as Sprint
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function fetchSprintsByFormationId(formation_id: number): Promise<Sprint[] | []> {
            try {
                const res = await api<ReturnData<{ sprints: Sprint[] }>>(`/teacher/sprints/formation/${formation_id}`,)
                return res.data?.sprints as Sprint[]
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
                return []
            }
        }

        async function fetchSprintSkills(id: number): Promise<SprintSkill[] | []> {
            try {
                const res = await api<ReturnData<{ skills: SprintSkill[]}>>(`/teacher/sprints/${id}/skills`)
                return res.data?.skills as SprintSkill[]
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
                return []
            }
        }

        async function createSprint(data: { formation_id: number, name: string, description: string, start_date: string, end_date: string, skills: number[] }): Promise<ReturnData> {
            try{
                const res = await api<ReturnData<{ sprint: Sprint, message: string}>>('/admin/sprints', {
                    method: 'POST',
                    body: data
                })

                if(!sprints.value?.length){
                    await fetchSprints()
                }

                sprints.value?.unshift(res.data?.sprint as Sprint)

                toast.push({
                    message: res.message || 'Sprint created successfully.',
                    type: 'success',
                })

                return {
                    success: true,
                    message: res.message || 'Sprint created successfully.',
                    data: null
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    if (err.data.errors.formation_id || err.data.errors.name || err.data.errors.description || err.data.errors.start_date || err.data.errors.end_date || err.data.errors.skills) {
                        let { formation_id, name, description, start_date, end_date, skills } = err.data.errors;
                        formation_id = formation_id ? formation_id[0] : "";
                        name = name ? name[0] : "";
                        description = description ? description[0] : "";
                        start_date = start_date ? start_date[0] : "";
                        end_date = end_date ? end_date[0] : "";
                        skills = skills ? skills[0] : "";

                        toast.push({
                            message: "Validation error. Please check your input.",
                            type: 'error',
                        })

                        return {
                            success: false,
                            message: "Validation error. Please check your input.",
                            data: null,
                            errors: {
                                formation_id,
                                name,
                                description,
                                start_date,
                                end_date,
                                skills
                            },
                        }
                    }
                }

                toast.push({
                    message: err?.message || 'Something went wrong. Please try again.',
                    type: 'error',
                })

                return {
                    success: false,
                    message: err?.message || 'Something went wrong. Please try again.',
                    data: null,
                    errors: {
                        formation_id: '',
                        name: '',
                        description: '',
                        start_date: '',
                        end_date: '',
                        skills: '',
                        message: err.message || 'Something went wrong. Please try again.'
                    },
                }
            }
        }

        async function updateSprint(id: number, data: { formation_id: number, name: string, description: string, start_date: string, end_date: string, skills: number[] }): Promise<ReturnData> {
            try{
                const res = await api<ReturnData<{ sprint: Sprint, message: string}>>(`/admin/sprints/${id}`, {
                    method: 'PUT',
                    body: data
                })

                if(!sprints.value?.length){
                    await fetchSprints()
                }

                const restoredSprint = res.data?.sprint as Sprint
                const year = sprints.value?.find(y => y.id === id) as Sprint

                Object.assign(year, restoredSprint)

                toast.push({
                    message: res?.message || 'Sprint updated successfully.',
                    type: 'success',
                })

                return {
                    success: true,
                    message: res?.message || 'Sprint updated successfully.',
                    data: null
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    if (err.data.errors.formation_id || err.data.errors.name || err.data.errors.description || err.data.errors.start_date || err.data.errors.end_date || err.data.errors.skills) {
                        let { formation_id, name, description, start_date, end_date, skills } = err.data.errors;
                        formation_id = formation_id ? formation_id[0] : "";
                        name = name ? name[0] : "";
                        description = description ? description[0] : "";
                        start_date = start_date ? start_date[0] : "";
                        end_date = end_date ? end_date[0] : "";
                        skills = skills ? skills[0] : "";

                        toast.push({
                            message: "Validation error. Please check your input.",
                            type: 'error',
                        })

                        return {
                            success: false,
                            message: "Validation error. Please check your input.",
                            data: null,
                            errors: {
                                formation_id,
                                name,
                                description,
                                start_date,
                                end_date,
                                skills
                            },
                        }
                    }
                }
                return {
                    success: false,
                    message: err.message || 'Something went wrong. Please try again.',
                    data: null,
                    errors: {
                        formation_id: '',
                        name: '',
                        description: '',
                        start_date: '',
                        end_date: '',
                        skills: '',
                        message: err.message || 'Something went wrong. Please try again.'
                    },
                }
            }
        }

        async function archiveSprint(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ sprint: Sprint }>>(`/admin/sprints/${id}`, {
                    method: 'DELETE'
                })

                if(!sprints.value?.length){
                    await fetchSprints()
                }

                const restoredSprint = res.data?.sprint as Sprint
                const year = sprints.value?.find(y => y.id === id) as Sprint

                Object.assign(year, restoredSprint)

                toast.push({
                    message: res?.message || 'Sprint archived successfully.',
                    type: 'success',
                })

            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function restoreSprint(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ sprint: Sprint }>>(`/admin/sprints/${id}/restore`, {
                    method: 'POST'
                })

                if(!sprints.value?.length){
                    await fetchSprints()
                }
                
                const restoredSprint = res.data?.sprint as Sprint
                const year = sprints.value?.find(y => y.id === id) as Sprint

                Object.assign(year, restoredSprint)

                toast.push({
                    message: res?.message || 'Sprint restored successfully.',
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
            sprints,
            sprint,
            archived_sprints,
            fetchSprints,
            fetchSprint,
            fetchSprintsByFormationId,
            fetchSprintSkills,
            createSprint,
            updateSprint,
            archiveSprint,
            restoreSprint
        }
    },
    {
        persist: {
            pick: ['sprints', 'archived_sprints'],
        },
    }
)
