import { defineStore } from 'pinia';
import { ref } from 'vue';
import { api } from '~/utils/api';
import type { ReturnData } from '../types/api';
import type { Formation, FormationData } from '../types/formation';

export const useFormation = defineStore(
    'formation',
    () => {
        const formations = ref<Formation[] | null>(null)
        const formation = ref<Formation | null>(null)
        const archived_formations = ref<Formation[] | null>(null)

        async function fetchFormations(): Promise<void> {
            const res = await api<ReturnData<FormationData>>('/admin/formations')
            formations.value = res.data?.formations as Formation[]
            archived_formations.value = res.data?.archived_formations as Formation[]
        }

        async function fetchFormation(id: number): Promise<void> {
            const res = await api<ReturnData<{ formation: Formation}>>(`/admin/formations/${id}`)
            formation.value = res.data?.formation as Formation
        }

        async function createFormation(data: { grade_level_id: number, title: string, description: string, duration: number, capacity: number }): Promise<ReturnData<any>> {
            try{
                const res = await api<ReturnData<{ formation: Formation, message: string}>>('/admin/formations', {
                    method: 'POST',
                    body: data
                })
                formations.value?.push(res.data?.formation as Formation)
                return {
                    success: true,
                    data: res.data?.message
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    if (err.data.errors.title || err.data.errors.description || err.data.errors.grade_level_id || err.data.errors.capacity || err.data.errors.duration) {
                        let { title, description, grade_level_id, capacity, duration } = err.data.errors;
                        title = title ? title[0] : "";
                        description = description ? description[0] : "";
                        grade_level_id = grade_level_id ? grade_level_id[0] : "";
                        capacity = capacity ? capacity[0] : "";
                        duration = duration ? duration[0] : "";
                        return {
                            success: false,
                            errors: {
                                title,
                                description,
                                grade_level_id,
                                capacity,
                                duration
                            },
                        }
                    }
                }
                return {
                    success: false,
                    errors: {
                        title: '',
                        description: '',
                        grade_level_id: '',
                        capacity: '',
                        duration: '',
                        message: err.data.message
                    },
                }
            }
        }

        async function updateFormation(id: number, data: { grade_level_id: number, title: string, description: string, duration: number, capacity: number }): Promise<ReturnData<any>> {
            try{
                const res = await api<ReturnData<{ formation: Formation, message: string}>>(`/admin/formations/${id}`, {
                    method: 'PUT',
                    body: data
                })

                if(!formations.value?.length){
                    await fetchFormations()
                }

                const restoredFormation = res.data?.formation as Formation
                const formation = formations.value?.find(y => y.id === id) as Formation

                Object.assign(formation, restoredFormation)
                return {
                    success: true,
                    data: res.data?.message
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    if (err.data.errors.title || err.data.errors.description || err.data.errors.grade_level_id || err.data.errors.capacity || err.data.errors.duration) {
                        let { title, description, grade_level_id, capacity, duration } = err.data.errors;
                        title = title ? title[0] : "";
                        description = description ? description[0] : "";
                        grade_level_id = grade_level_id ? grade_level_id[0] : "";
                        capacity = capacity ? capacity[0] : "";
                        duration = duration ? duration[0] : "";
                        return {
                            success: false,
                            errors: {
                                title,
                                description,
                                grade_level_id,
                                capacity,
                                duration
                            },
                        }
                    }
                }
                return {
                    success: false,
                    errors: {
                        title: '',
                        description: '',
                        grade_level_id: '',
                        capacity: '',
                        duration: '',
                        message: err.message
                    },
                }
            }
        }

        async function archiveFormation(id: number): Promise<void> {
            const res = await api<ReturnData<{ formation: Formation }>>(`/admin/formations/${id}`, {
                method: 'DELETE'
            })

            if(!formations.value?.length){
                await fetchFormations()
            }

            const restoredFormation = res.data?.formation as Formation
            const formation = formations.value?.find(y => y.id === id) as Formation

            Object.assign(formation, restoredFormation)
        }

        async function restoreFormation(id: number): Promise<void> {
            const res = await api<ReturnData<{ formation: Formation }>>(`/admin/formations/${id}/restore`, {
                method: 'POST'
            })

            if(!formations.value?.length){
                await fetchFormations()
            }

            const restoredFormation = res.data?.formation as Formation
            const formation = formations.value?.find(y => y.id === id) as Formation

            Object.assign(formation, restoredFormation)
        }

        return {
            formations,
            formation,
            archived_formations,
            fetchFormations,
            fetchFormation,
            createFormation,
            updateFormation,
            archiveFormation,
            restoreFormation
        }
    },
    {
        persist: {
            pick: ['formations', 'archived_formations'],
        },
    }
)
