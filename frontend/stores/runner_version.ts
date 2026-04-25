import { defineStore } from 'pinia';
import { reactive, ref } from 'vue';
import { api } from '~/utils/api';
import type { meta, ReturnData } from '../types/api';
import type { RunnerVersion } from '../types/runner_version';
import { useToastStore } from './toast';
import { PaginatedData } from '../types/pagination';

export const useRunnerVersion = defineStore(
    'runner_version',
    () => {
        const runner_versions = ref<RunnerVersion[] | null>(null)
        const runner_version = ref<RunnerVersion | null>(null)
        const archived_runner_versions = ref<RunnerVersion[] | null>(null)
        const all_runner_versions = ref<RunnerVersion[] | null>(null)
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


        async function fetchRunnerVersions(page: number = 1, per_page: number = 5): Promise<void> {
            try {
                const res = await api<ReturnData<{ runners: PaginatedData<RunnerVersion[]>, archived_runners: PaginatedData<RunnerVersion[]>}>>(`/admin/runner-versions?page=${page}&per_page=${per_page}`)
                runner_versions.value = res.data?.runners?.data as RunnerVersion[]
                archived_runner_versions.value = res.data?.archived_runners?.data as RunnerVersion[]
                Object.assign(meta, res.data?.runners)
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function fetchAllRunnerVersions(): Promise<void> {
            try {
                const res = await api<ReturnData<{ runner_versions: RunnerVersion[] }>>(`/admin/runner-versions/all`)
                all_runner_versions.value = res.data?.runner_versions as RunnerVersion[]
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function fetchRunnerVersion(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ runnerVersion: RunnerVersion}>>(`/admin/runner-versions/${id}`)
                runner_version.value = res.data?.runnerVersion as RunnerVersion
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function createRunnerVersion(data: {
            runner_id: number,
            version: string,
            docker_image: string,
            mode: string,
            port: number,
            healthcheck_path: string,
            cpu: number,
            memory_mb: number,
            timeout_seconds: number,
            php: boolean,
            node: boolean,
            sqlite: boolean,
            network_enabled: boolean,
            status: string,
            is_default: boolean
        }): Promise<ReturnData> {
            try{
                const res = await api<ReturnData<{ runnerVersion: RunnerVersion, message: string}>>('/admin/runner-versions', {
                    method: 'POST',
                    body: data
                })

                if(!runner_versions.value?.length){
                    await fetchRunnerVersions()
                }

                runner_versions.value?.unshift(res.data?.runnerVersion as RunnerVersion)

                toast.push({
                    message: res?.message || 'Runner version created successfully.',
                    type: 'success',
                })

                return {
                    success: true,
                    message: res?.message || 'Runner version created successfully.',
                    data: null
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    const errorFields = err.data.errors;
                    const mapped: Record<string, string> = {};
                    for (const key of Object.keys(errorFields)) {
                        mapped[key] = Array.isArray(errorFields[key]) ? errorFields[key][0] : errorFields[key];
                    }

                    toast.push({
                        message: "Validation error. Please check your input.",
                        type: 'error',
                    })

                    return {
                        success: false,
                        message: "Validation error. Please check your input.",
                        data: null,
                        errors: mapped,
                    }
                }

                toast.push({
                    message: err.message || 'Something went wrong. Please try again.',
                    type: 'error',
                })

                return {
                    success: false,
                    data: null,
                    message: err.message || 'Something went wrong. Please try again.',
                    errors: {
                        message: err.message || 'Something went wrong. Please try again.'
                    },
                }
            }
        }

        async function deleteRunnerVersion(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ runnerVersion: RunnerVersion }>>(`/admin/runner-versions/${id}`, {
                    method: 'DELETE'
                })

                if(runner_versions.value?.length){
                    runner_versions.value = runner_versions.value.filter(rv => rv.id !== id)
                }

                toast.push({
                    message: res?.message || 'Runner version deleted successfully.',
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
            runner_versions,
            runner_version,
            archived_runner_versions,
            all_runner_versions,
            meta,
            fetchRunnerVersions,
            fetchAllRunnerVersions,
            fetchRunnerVersion,
            createRunnerVersion,
            deleteRunnerVersion
        }
    },
    {
        persist: {
            pick: ['runner_versions', 'archived_runner_versions', 'all_runner_versions'],
        },
    }
)
