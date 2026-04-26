import { defineStore } from 'pinia';
import { reactive, ref } from 'vue';
import type { meta, ReturnData } from '../types/api';
import type { Formation } from '../types/formation';
import { useToastStore } from './toast';
import type { PaginatedData } from '../types/pagination';

export const useFormation = defineStore(
    'formation',
    () => {
        const api = useApi()
        const formations = ref<Formation[] | null>(null)
        const formation = ref<Formation | null>(null)
        const archived_formations = ref<Formation[] | null>(null)
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

        async function fetchFormations(page: number = 1, per_page: number = 5): Promise<void> {
            try {
                const res = await api<ReturnData<{ formations: PaginatedData<Formation[]>, archived_formations: PaginatedData<Formation[]>}>>(`/admin/formations?page=${page}&per_page=${per_page}`)
                formations.value = res.data?.formations?.data as Formation[]
                archived_formations.value = res.data?.archived_formations?.data as Formation[]
                Object.assign(meta, res.data?.formations)
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function fetchFormation(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ formation: Formation}>>(`/admin/formations/${id}`)
                formation.value = res.data?.formation as Formation
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function createFormation(data: { grade_level_id: number, title: string, description: string, duration: number, capacity: number }): Promise<ReturnData> {
            try{
                const res = await api<ReturnData<{ formation: Formation, message: string}>>('/admin/formations', {
                    method: 'POST',
                    body: data
                })

                if(!formations.value?.length){
                    await fetchFormations()
                }

                formations.value?.unshift(res.data?.formation as Formation)

                toast.push({
                    message: res?.message || 'Formation created successfully.',
                    type: 'success',
                })

                return {
                    success: true,
                    data: null,
                    message: res.data?.message || 'Formation created successfully.'
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

                        toast.push({
                            message: "Validation error. Please check your input.",
                            type: 'error',
                        })

                        return {
                            success: false,
                            data: null,
                            message: "Validation error. Please check your input.",
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

                toast.push({
                    message: err?.data?.message || 'Something went wrong. Please try again.',
                    type: 'error',
                })

                return {
                    success: false,
                    data: null,
                    message: err?.message || 'Something went wrong. Please try again.',
                    errors: {
                        title: '',
                        description: '',
                        grade_level_id: '',
                        capacity: '',
                        duration: '',
                        message: err?.message || 'Something went wrong. Please try again.'
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

                toast.push({
                    message: res?.message || 'Formation updated successfully.',
                    type: 'success',
                })

                return {
                    success: true,
                    data: null,
                    message: res.data?.message || 'Formation updated successfully.'
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

                        toast.push({
                            message: "Validation error. Please check your input.",
                            type: 'error',
                        })

                        return {
                            success: false,
                            data: null,
                            message: "Validation error. Please check your input.",
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
                    data: null,
                    message: err?.message || 'Something went wrong. Please try again.',
                    errors: {
                        title: '',
                        description: '',
                        grade_level_id: '',
                        capacity: '',
                        duration: '',
                        message: err.message || err.data.message || 'Something went wrong. Please try again.'
                    },
                }
            }
        }

        async function archiveFormation(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ formation: Formation }>>(`/admin/formations/${id}`, {
                    method: 'DELETE'
                })

                if(!formations.value?.length){
                    await fetchFormations()
                }

                const restoredFormation = res.data?.formation as Formation
                const formation = formations.value?.find(y => y.id === id) as Formation

                Object.assign(formation, restoredFormation)

                toast.push({
                    message: res?.message || 'Formation archived successfully.',
                    type: 'success',
                })
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function restoreFormation(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ formation: Formation }>>(`/admin/formations/${id}/restore`, {
                    method: 'POST'
                })

                if(!formations.value?.length){
                    await fetchFormations()
                }

                const restoredFormation = res.data?.formation as Formation
                const formation = formations.value?.find(y => y.id === id) as Formation

                Object.assign(formation, restoredFormation)

                toast.push({
                    message: res?.message || 'Formation restored successfully.',
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
            formations,
            formation,
            archived_formations,
            meta,
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
