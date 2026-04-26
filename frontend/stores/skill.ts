import { defineStore } from 'pinia';
import { reactive, ref } from 'vue';
import type { meta, ReturnData } from '../types/api';
import type { Skill } from '../types/skill';
import { useToastStore } from './toast';
import type { PaginatedData } from '../types/pagination';

export const useSkill = defineStore(
    'skill',
    () => {
        const api = useApi()
        const skills = ref<Skill[] | null>(null)
        const skill = ref<Skill | null>(null)
        const archived_skills = ref<Skill[] | null>(null)
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

        async function fetchSkills(page: number = 1, per_page: number = 5): Promise<void> {
            try {
                const res = await api<ReturnData<{ skills: PaginatedData<Skill[]>, archived_skills: PaginatedData<Skill[]>}>>(`/admin/skills?page=${page}&per_page=${per_page}`)
                skills.value = res.data?.skills?.data as Skill[]
                archived_skills.value = res.data?.archived_skills?.data as Skill[]
                Object.assign(meta, res.data?.skills)
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function fetchByFormation(formation_id: number): Promise<Skill[] | null> {
            try {
                const res = await api<ReturnData<{ skills: Skill[] }>>('/admin/skills/formation/' + formation_id)
                return res.data?.skills ?? null
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
                return null
            }
        }

        async function fetchSkill(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ skill: Skill}>>(`/admin/skills/${id}`)
                skill.value = res.data?.skill as Skill
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function createSkill(data: { formation_id: number, code: string, title: string, description: string, skill_levels: { level_id: number, criteria: string}[] }): Promise<ReturnData> {
            try{
                const res = await api<ReturnData<{ skill: Skill, message: string}>>('/admin/skills', {
                    method: 'POST',
                    body: data
                })

                if(!skills.value?.length){
                    await fetchSkills()
                }

                skills.value?.push(res.data?.skill as Skill)

                toast.push({
                    message: res?.message || 'Skill created successfully.',
                    type: 'success',
                })

                return {
                    success: true,
                    message: res?.message || 'Skill created successfully.',
                    data: null
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    if (err.data.errors.formation_id || err.data.errors.code || err.data.errors.title || err.data.errors.description || err.data.errors.skill_levels) {
                        let { formation_id, code, title, description, skill_levels } = err.data.errors;
                        formation_id = formation_id ? formation_id[0] : "";
                        code = code ? code[0] : "";
                        title = title ? title[0] : "";
                        description = description ? description[0] : "";
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
                                formation_id,
                                code,
                                title,
                                description,
                                skill_levels
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
                        code: '',
                        title: '',
                        description: '',
                        skill_levels: '',
                        message: err?.message || 'Something went wrong. Please try again.'
                    },
                }
            }
        }

        async function updateSkill(id: number, data: { formation_id: number, code: string, title: string, description: string, skill_levels: { level_id: number, criteria: string}[] }): Promise<ReturnData> {
            try{
                const res = await api<ReturnData<{ skill: Skill, message: string}>>(`/admin/skills/${id}`, {
                    method: 'PUT',
                    body: data
                })

                if(!skills.value?.length){
                    await fetchSkills()
                }

                const restoredSkill = res.data?.skill as Skill
                const year = skills.value?.find(y => y.id === id) as Skill

                Object.assign(year, restoredSkill)

                toast.push({
                    message: res?.message || 'Skill updated successfully.',
                    type: 'success',
                })

                return {
                    success: true,
                    message: res?.message || 'Skill updated successfully.',
                    data: null
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    if (err.data.errors.formation_id || err.data.errors.code || err.data.errors.title || err.data.errors.description || err.data.errors.skill_levels) {
                        let { formation_id, code, title, description, skill_levels } = err.data.errors;
                        formation_id = formation_id ? formation_id[0] : "";
                        code = code ? code[0] : "";
                        title = title ? title[0] : "";
                        description = description ? description[0] : "";
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
                                formation_id,
                                code,
                                title,
                                description,
                                skill_levels
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
                        code: '',
                        title: '',
                        description: '',
                        skill_levels: '',
                        message: err.message || 'Something went wrong. Please try again.'
                    },
                }
            }
        }

        async function archiveSkill(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ skill: Skill }>>(`/admin/skills/${id}`, {
                    method: 'DELETE'
                })

                if(!skills.value?.length){
                    await fetchSkills()
                }

                const restoredSkill = res.data?.skill as Skill
                const year = skills.value?.find(y => y.id === id) as Skill

                Object.assign(year, restoredSkill)

                toast.push({
                    message: res?.message || 'Skill archived successfully.',
                    type: 'success',
                })
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function restoreSkill(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ skill: Skill }>>(`/admin/skills/${id}/restore`, {
                    method: 'POST'
                })

                if(!skills.value?.length){
                    await fetchSkills()
                }
                
                const restoredSkill = res.data?.skill as Skill
                const year = skills.value?.find(y => y.id === id) as Skill

                Object.assign(year, restoredSkill)

                toast.push({
                    message: res?.message || 'Skill restored successfully.',
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
            skills,
            skill,
            archived_skills,
            meta,
            fetchSkills,
            fetchByFormation,
            fetchSkill,
            createSkill,
            updateSkill,
            archiveSkill,
            restoreSkill
        }
    },
    {
        persist: {
            pick: ['skills', 'archived_skills'],
        },
    }
)
