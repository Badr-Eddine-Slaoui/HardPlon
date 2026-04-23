import { defineStore } from 'pinia';
import { reactive, ref } from 'vue';
import { api } from '~/utils/api';
import type { meta, ReturnData } from '../types/api';
import type { ClassGroup } from '../types/class_group';
import { useToastStore } from './toast';
import { PaginatedData } from '../types/pagination';

export const useClassGroup = defineStore(
    'class_group',
    () => {
        const class_groups = ref<ClassGroup[] | null>(null)
        const class_group = ref<ClassGroup | null>(null)
        const archived_class_groups = ref<ClassGroup[] | null>(null)
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

        async function fetchClassGroups(page: number = 1, per_page: number = 5): Promise<void> {
            try {
                const res = await api<ReturnData<{ class_groups: PaginatedData<ClassGroup[]>, archived_class_groups: PaginatedData<ClassGroup[]> }>>(`/admin/class-groups?page=${page}&per_page=${per_page}`)
                class_groups.value = res.data?.class_groups?.data as ClassGroup[]
                archived_class_groups.value = res.data?.archived_class_groups?.data as ClassGroup[]
                Object.assign(meta, res.data?.class_groups)
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function fetchClassGroup(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ class_group: ClassGroup}>>(`/admin/class-groups/${id}`)
                class_group.value = res.data?.class_group as ClassGroup
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function fetchTeacherClassGroups(): Promise<ClassGroup[]> {
            try {
                const res = await api<ReturnData<{ class_groups: ClassGroup[] }>>('/teacher/class-groups')
                return res.data?.class_groups as ClassGroup[]
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
                return []
            }
        }

        async function createClassGroup(data: { formation_id: number, name: string, description: string, capacity: number, image_url: string, main_teacher_id: number, sub_teacher_id: number, students_ids: number[] }): Promise<ReturnData>{
            try{
                const res = await api<ReturnData<{ class_group: ClassGroup, message: string}>>('/admin/class-groups', {
                    method: 'POST',
                    body: data
                })

                if(!class_groups.value?.length){
                    await fetchClassGroups()
                }

                class_groups.value?.unshift(res.data?.class_group as ClassGroup)

                toast.push({
                    message: res?.message || 'Class group created successfully.',
                    type: 'success',
                })

                return {
                    success: true,
                    data: null,
                    message: res.message || 'Class group created successfully.'
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    if (err.data.errors.formation_id || err.data.errors.name || err.data.errors.description || err.data.errors.capacity || err.data.errors.image_url || err.data.errors.main_teacher_id || err.data.errors.sub_teacher_id || err.data.errors.students_ids) {
                        let { formation_id, name, description, capacity, image_url, main_teacher_id, sub_teacher_id, students_ids } = err.data.errors;
                        formation_id = formation_id ? formation_id[0] : "";
                        name = name ? name[0] : "";
                        description = description ? description[0] : "";
                        capacity = capacity ? capacity[0] : "";
                        image_url = image_url ? image_url[0] : "";
                        main_teacher_id = main_teacher_id ? main_teacher_id[0] : "";
                        sub_teacher_id = sub_teacher_id ? sub_teacher_id[0] : "";
                        students_ids = students_ids ? students_ids[0] : "";

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
                                capacity,
                                image_url,
                                main_teacher_id,
                                sub_teacher_id,
                                students_ids
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
                        formation_id: '',
                        name: '',
                        description: '',
                        capacity: '',
                        image_url: '',
                        main_teacher_id: '',
                        sub_teacher_id: '',
                        students_ids: '',
                        message: err.data.message
                    },
                }
            }
        }

        async function updateClassGroup(id: number, data: { formation_id: number, name: string, description: string, capacity: number, image_url: string, main_teacher_id: number, sub_teacher_id: number, students_ids: number[] }): Promise<ReturnData> {
            try{
                const res = await api<ReturnData<{ class_group: ClassGroup, message: string}>>(`/admin/class-groups/${id}`, {
                    method: 'PUT',
                    body: data
                })

                if(!class_groups.value?.length){
                    await fetchClassGroups()
                }

                const restoredClassGroup = res.data?.class_group as ClassGroup
                const class_group = class_groups.value?.find(y => y.id === id) as ClassGroup
                
                Object.assign(class_group, restoredClassGroup)

                toast.push({
                    message: res?.message || 'Class group updated successfully.',
                    type: 'success',
                })

                return {
                    success: true,
                    message: res.message,
                    data: null
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    if (err.data.errors.formation_id || err.data.errors.name || err.data.errors.description || err.data.errors.capacity || err.data.errors.image_url || err.data.errors.main_teacher_id || err.data.errors.sub_teacher_id || err.data.errors.students_ids) {
                        let { formation_id, name, description, capacity, image_url, main_teacher_id, sub_teacher_id, students_ids } = err.data.errors;
                        formation_id = formation_id ? formation_id[0] : "";
                        name = name ? name[0] : "";
                        description = description ? description[0] : "";
                        capacity = capacity ? capacity[0] : "";
                        image_url = image_url ? image_url[0] : "";
                        main_teacher_id = main_teacher_id ? main_teacher_id[0] : "";
                        sub_teacher_id = sub_teacher_id ? sub_teacher_id[0] : "";
                        students_ids = students_ids ? students_ids[0] : "";

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
                                capacity,
                                image_url,
                                main_teacher_id,
                                sub_teacher_id,
                                students_ids
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
                        title: '',
                        description: '',
                        grade_level_id: '',
                        capacity: '',
                        duration: '',
                        image_url: '',
                        main_teacher_id: '',
                        sub_teacher_id: '',
                        students_ids: '',
                        message: err.message
                    },
                }
            }
        }

        async function archiveClassGroup(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ class_group: ClassGroup }>>(`/admin/class-groups/${id}`, {
                    method: 'DELETE'
                })

                if(!class_groups.value?.length){
                    await fetchClassGroups()
                }

                const restoredClassGroup = res.data?.class_group as ClassGroup
                const class_group = class_groups.value?.find(y => y.id === id) as ClassGroup

                Object.assign(class_group, restoredClassGroup)

                toast.push({
                    message: res?.message || 'Class group archived successfully.',
                    type: 'success',
                })
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function restoreClassGroup(id: number): Promise<void> {                                                                                                                                                    
            try {
                const res = await api<ReturnData<{ class_group: ClassGroup }>>(`/admin/class-groups/${id}/restore`, {
                    method: 'POST'
                })

                if(!class_groups.value?.length){
                    await fetchClassGroups()
                }                                                                                                                                                                   

                const restoredClassGroup = res.data?.class_group as ClassGroup
                const class_group = class_groups.value?.find(y => y.id === id) as ClassGroup

                Object.assign(class_group, restoredClassGroup)

                toast.push({
                    message: res?.message || 'Class group restored successfully.',
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
            class_groups,
            class_group,
            archived_class_groups,
            meta,
            fetchClassGroups,
            fetchTeacherClassGroups,
            fetchClassGroup,
            createClassGroup,
            updateClassGroup,
            archiveClassGroup,
            restoreClassGroup
        }
    },
    {
        persist: {
            pick: ['class_groups', 'archived_class_groups'],
        },
    }
)
