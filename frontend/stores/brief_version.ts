import { defineStore } from 'pinia';
import { reactive, ref } from 'vue';
import { api } from '~/utils/api';
import type { meta, ReturnData } from '../types/api';
import type { BriefVersion } from '../types/brief_version';
import type { PaginatedData } from '../types/pagination';
import { useToastStore } from './toast';

export const useBriefVersion = defineStore(
    'brief_version',
    () => {
        const brief_versions = ref<BriefVersion[] | null>(null)
        const brief_version = ref<BriefVersion | null>(null)
        const archived_brief_versions = ref<BriefVersion[] | null>(null)
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

        async function fetchBriefVersions(page: number = 1, per_page: number = 5): Promise<void> {
            try {
                const res = await api<ReturnData<{ brief_versions: PaginatedData<BriefVersion[]>, archived_brief_versions: PaginatedData<BriefVersion[]>}>>(`/teacher/brief-versions?page=${page}&per_page=${per_page}`)
                brief_versions.value = res.data?.brief_versions?.data as BriefVersion[]
                archived_brief_versions.value = res.data?.archived_brief_versions?.data as BriefVersion[]
                Object.assign(meta, res.data?.brief_versions)
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function fetchBriefVersionsForBrief(briefId: number, page: number = 1, per_page: number = 5): Promise<void> {
            try {
                const res = await api<ReturnData<{ brief_versions: PaginatedData<BriefVersion[]>, archived_brief_versions: PaginatedData<BriefVersion[]>}>>(`/teacher/briefs/${briefId}/versions?page=${page}&per_page=${per_page}`)
                brief_versions.value = res.data?.brief_versions?.data as BriefVersion[]
                archived_brief_versions.value = res.data?.archived_brief_versions?.data as BriefVersion[]
                Object.assign(meta, res.data?.brief_versions)
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function fetchBriefVersion(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ brief_version: BriefVersion}>>(`/teacher/brief-versions/${id}`)
                brief_version.value = res.data?.brief_version as BriefVersion
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function createBriefVersion(data: any): Promise<ReturnData> {
            try{
                const res = await api<ReturnData<{ brief_version: BriefVersion}>>('/teacher/brief-versions', {
                    method: 'POST',
                    body: data
                })

                brief_versions.value?.unshift(res.data?.brief_version as BriefVersion)

                toast.push({
                    message: res?.message || 'Brief version created successfully.',
                    type: 'success',
                })

                return {
                    success: true,
                    data: null,
                    message: res.message
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    return {
                        success: false,
                        message: "Validation error. Please check your input.",
                        data: null,
                        errors: err.data.errors,
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
                        message: err.data.message
                    },
                }
            }
        }

        async function archiveBriefVersion(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ brief_version: BriefVersion }>>(`/teacher/brief-versions/${id}`, {
                    method: 'DELETE'
                })

                if(!brief_versions.value?.length){
                    await fetchBriefVersions()
                }

                const archivedVersion = res.data?.brief_version as BriefVersion
                const target = brief_versions.value?.find(v => v.id === id) as BriefVersion

                if (target) {
                    Object.assign(target, archivedVersion)
                }

                toast.push({
                    message: res?.message || 'Brief version archived successfully.',
                    type: 'success',
                })
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function restoreBriefVersion(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ brief_version: BriefVersion }>>(`/teacher/brief-versions/${id}/restore`, {
                    method: 'POST'
                })

                if(!brief_versions.value?.length){
                    await fetchBriefVersions()
                }
                
                const restoredVersion = res.data?.brief_version as BriefVersion
                const target = brief_versions.value?.find(v => v.id === id) as BriefVersion

                if (target) {
                    Object.assign(target, restoredVersion)
                }

                toast.push({
                    message: res?.message || 'Brief version restored successfully.',
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
            brief_versions,
            brief_version,
            archived_brief_versions,
            meta,
            fetchBriefVersions,
            fetchBriefVersionsForBrief,
            fetchBriefVersion,
            createBriefVersion,
            archiveBriefVersion,
            restoreBriefVersion
        }
    },
    {
        persist: {
            pick: ['brief_versions', 'archived_brief_versions'],
        },
    }
)
