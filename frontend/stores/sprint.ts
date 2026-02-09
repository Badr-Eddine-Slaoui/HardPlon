import { defineStore } from 'pinia';
import { ref } from 'vue';
import { api } from '~~/utils/api';
import type { ReturnData } from '../types/api';
import type { Sprint, SprintData, SprintSkill } from '../types/sprint';

export const useSprint = defineStore(
    'sprint',
    () => {
        const sprints = ref<Sprint[] | null>(null)
        const sprint = ref<Sprint | null>(null)
        const archived_sprints = ref<Sprint[] | null>(null)

        async function fetchSprints(): Promise<void> {
            const res = await api<ReturnData<SprintData>>('/admin/sprints')
            sprints.value = res.data?.sprints as Sprint[]
            archived_sprints.value = res.data?.archived_sprints as Sprint[]
        }

        async function fetchSprint(id: number): Promise<void> {
            const res = await api<ReturnData<{ sprint: Sprint}>>(`/admin/sprints/${id}`)
            sprint.value = res.data?.sprint as Sprint
        }

        async function fetchSprintsByFormationId(formation_id: number): Promise<Sprint[]> {
            const res = await api<ReturnData<SprintData>>(`/teacher/sprints/formation/${formation_id}`,)
            return res.data?.sprints as Sprint[]
        }

        async function fetchSprintSkills(id: number): Promise<SprintSkill[]> {
            const res = await api<ReturnData<{ skills: SprintSkill[]}>>(`/teacher/sprints/${id}/skills`)
            return res.data?.skills as SprintSkill[]
        }

        async function createSprint(data: { formation_id: number, name: string, description: string, start_date: string, end_date: string, skills: number[] }): Promise<ReturnData<any>> {
            try{
                const res = await api<ReturnData<{ sprint: Sprint, message: string}>>('/admin/sprints', {
                    method: 'POST',
                    body: data
                })
                sprints.value?.push(res.data?.sprint as Sprint)
                return {
                    success: true,
                    data: res.data?.message
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
                        return {
                            success: false,
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
                    errors: {
                        formation_id: '',
                        name: '',
                        description: '',
                        start_date: '',
                        end_date: '',
                        skills: '',
                        message: err.data.message
                    },
                }
            }
        }

        async function updateSprint(id: number, data: { formation_id: number, name: string, description: string, start_date: string, end_date: string, skills: number[] }): Promise<ReturnData<any>> {
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
                return {
                    success: true,
                    data: res.data?.message
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
                        return {
                            success: false,
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
                    errors: {
                        formation_id: '',
                        name: '',
                        description: '',
                        start_date: '',
                        end_date: '',
                        skills: '',
                        message: err.message
                    },
                }
            }
        }

        async function archiveSprint(id: number): Promise<void> {
            const res = await api<ReturnData<{ sprint: Sprint }>>(`/admin/sprints/${id}`, {
                method: 'DELETE'
            })

            if(!sprints.value?.length){
                await fetchSprints()
            }

            const restoredSprint = res.data?.sprint as Sprint
            const year = sprints.value?.find(y => y.id === id) as Sprint

            Object.assign(year, restoredSprint)
        }

        async function restoreSprint(id: number): Promise<void> {
            const res = await api<ReturnData<{ sprint: Sprint }>>(`/admin/sprints/${id}/restore`, {
                method: 'POST'
            })

            if(!sprints.value?.length){
                await fetchSprints()
            }
            
            const restoredSprint = res.data?.sprint as Sprint
            const year = sprints.value?.find(y => y.id === id) as Sprint

            Object.assign(year, restoredSprint)
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
