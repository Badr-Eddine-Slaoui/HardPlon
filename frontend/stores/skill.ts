import { defineStore } from 'pinia';
import { ref } from 'vue';
import { api } from '~~/utils/api';
import type { ReturnData } from '../types/api';
import type { Skill, SkillData } from '../types/skill';

export const useSkill = defineStore(
    'skill',
    () => {
        const skills = ref<Skill[] | null>(null)
        const skill = ref<Skill | null>(null)
        const archived_skills = ref<Skill[] | null>(null)

        async function fetchSkills(): Promise<void> {
            const res = await api<ReturnData<SkillData>>('/admin/skills')
            skills.value = res.data?.skills as Skill[]
            archived_skills.value = res.data?.archived_skills as Skill[]
        }

        async function fetchByFormation(formation_id: number): Promise<Skill[] | null> {
            const res = await api<ReturnData<SkillData>>('/admin/skills/formation/' + formation_id)
            return res.data?.skills ?? null
        }

        async function fetchSkill(id: number): Promise<void> {
            const res = await api<ReturnData<{ skill: Skill}>>(`/admin/skills/${id}`)
            skill.value = res.data?.skill as Skill
        }

        async function createSkill(data: { formation_id: number, code: string, title: string, description: string, skill_levels: { level_id: number, criteria: string}[] }): Promise<ReturnData<any>> {
            try{
                const res = await api<ReturnData<{ skill: Skill, message: string}>>('/admin/skills', {
                    method: 'POST',
                    body: data
                })
                skills.value?.push(res.data?.skill as Skill)
                return {
                    success: true,
                    data: res.data?.message
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
                        return {
                            success: false,
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
                return {
                    success: false,
                    errors: {
                        formation_id: '',
                        code: '',
                        title: '',
                        description: '',
                        skill_levels: '',
                        message: err.data.message
                    },
                }
            }
        }

        async function updateSkill(id: number, data: { formation_id: number, code: string, title: string, description: string, skill_levels: { level_id: number, criteria: string}[] }): Promise<ReturnData<any>> {
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
                return {
                    success: true,
                    data: res.data?.message
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
                        return {
                            success: false,
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
                return {
                    success: false,
                    errors: {
                        formation_id: '',
                        code: '',
                        title: '',
                        description: '',
                        skill_levels: '',
                        message: err.message
                    },
                }
            }
        }

        async function archiveSkill(id: number): Promise<void> {
            const res = await api<ReturnData<{ skill: Skill }>>(`/admin/skills/${id}`, {
                method: 'DELETE'
            })

            if(!skills.value?.length){
                await fetchSkills()
            }

            const restoredSkill = res.data?.skill as Skill
            const year = skills.value?.find(y => y.id === id) as Skill

            Object.assign(year, restoredSkill)
        }

        async function restoreSkill(id: number): Promise<void> {
            const res = await api<ReturnData<{ skill: Skill }>>(`/admin/skills/${id}/restore`, {
                method: 'POST'
            })

            if(!skills.value?.length){
                await fetchSkills()
            }
            
            const restoredSkill = res.data?.skill as Skill
            const year = skills.value?.find(y => y.id === id) as Skill

            Object.assign(year, restoredSkill)
        }

        return {
            skills,
            skill,
            archived_skills,
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
