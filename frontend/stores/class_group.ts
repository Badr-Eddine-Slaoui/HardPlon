import { defineStore } from 'pinia';
import { ref } from 'vue';
import { api } from '~/utils/api';
import type { ReturnData } from '../types/api';
import type { ClassGroup, ClassGroupData } from '../types/class_group';

export const useClassGroup = defineStore(
    'class_group',
    () => {
        const class_groups = ref<ClassGroup[] | null>(null)
        const class_group = ref<ClassGroup | null>(null)
        const archived_class_groups = ref<ClassGroup[] | null>(null)

        async function fetchClassGroups(): Promise<void> {
            const res = await api<ReturnData<ClassGroupData>>('/admin/class-groups')
            class_groups.value = res.data?.class_groups as ClassGroup[]
            archived_class_groups.value = res.data?.archived_class_groups as ClassGroup[]
        }

        async function fetchClassGroup(id: number): Promise<void> {
            const res = await api<ReturnData<{ class_group: ClassGroup}>>(`/admin/class-groups/${id}`)
            class_group.value = res.data?.class_group as ClassGroup
        }

        async function fetchTeacherClassGroups(): Promise<ClassGroup[]> {
            const res = await api<ReturnData<ClassGroupData>>(`/teacher/class-groups`)
            return res.data?.class_groups as ClassGroup[]
        }

        async function createClassGroup(data: { formation_id: number, name: string, description: string, capacity: number, image_url: string, main_teacher_id: number, sub_teacher_id: number, students_ids: number[] }): Promise<ReturnData<any>> {
            try{
                const res = await api<ReturnData<{ class_group: ClassGroup, message: string}>>('/admin/class-groups', {
                    method: 'POST',
                    body: data
                })
                class_groups.value?.push(res.data?.class_group as ClassGroup)
                return {
                    success: true,
                    data: res.data?.message
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
                        return {
                            success: false,
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
                return {
                    success: false,
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

        async function updateClassGroup(id: number, data: { formation_id: number, name: string, description: string, capacity: number, image_url: string, main_teacher_id: number, sub_teacher_id: number, students_ids: number[] }): Promise<ReturnData<any>> {
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
                return {
                    success: true,
                    data: res.data?.message
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
                        return {
                            success: false,
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
                return {
                    success: false,
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
            const res = await api<ReturnData<{ class_group: ClassGroup }>>(`/admin/class-groups/${id}`, {
                method: 'DELETE'
            })

            if(!class_groups.value?.length){
                await fetchClassGroups()
            }

            const restoredClassGroup = res.data?.class_group as ClassGroup
            const class_group = class_groups.value?.find(y => y.id === id) as ClassGroup

            Object.assign(class_group, restoredClassGroup)
        }

        async function restoreClassGroup(id: number): Promise<void> {                                                                                                                                                    
            const res = await api<ReturnData<{ class_group: ClassGroup }>>(`/admin/class-groups/${id}/restore`, {
                method: 'POST'
            })

            if(!class_groups.value?.length){
                await fetchClassGroups()
            }                                                                                                                                                                   

            const restoredClassGroup = res.data?.class_group as ClassGroup
            const class_group = class_groups.value?.find(y => y.id === id) as ClassGroup

            Object.assign(class_group, restoredClassGroup)
        }

        return {
            class_groups,
            class_group,
            archived_class_groups,
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
