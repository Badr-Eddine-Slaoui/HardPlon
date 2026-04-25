import { defineStore } from 'pinia';
import { reactive, ref } from 'vue';
import { api } from '~/utils/api';
import type { meta, ReturnData } from '../types/api';
import type { Language } from '../types/language';
import { useToastStore } from './toast';
import type { PaginatedData } from '../types/pagination';

export const useLanguage = defineStore(
    'language',
    () => {
        const languages = ref<Language[] | null>(null)
        const language = ref<Language | null>(null)
        const archived_languages = ref<Language[] | null>(null)
        const all_languages = ref<Language[] | null>(null)
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


        async function fetchLanguages(page: number = 1, per_page: number = 5): Promise<void> {
            try {
                const res = await api<ReturnData<{ languages: PaginatedData<Language[]>, archive_languages: PaginatedData<Language[]>}>>(`/admin/languages?page=${page}&per_page=${per_page}`)
                languages.value = res.data?.languages?.data as Language[]
                archived_languages.value = res.data?.archive_languages?.data as Language[]
                Object.assign(meta, res.data?.languages)
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function fetchLanguage(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ language: Language}>>(`/admin/languages/${id}`)
                language.value = res.data?.language as Language
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function fetchAllLanguages(): Promise<void> {
            try {
                const res = await api<ReturnData<Language[]>>('/teacher/languages')
                all_languages.value = res.data as Language[]
            } catch (err) {
                toast.push({
                    message: 'Something went wrong while fetching languages.',
                    type: 'error',
                })
            }
        }

        async function createLanguage(data: { name: string, docker_image: string, compile_command: string | null, run_command: string }): Promise<ReturnData> {
            try{
                const res = await api<ReturnData<{ language: Language, message: string}>>('/admin/languages', {
                    method: 'POST',
                    body: data
                })

                if(!languages.value?.length){
                    await fetchLanguages()
                }

                languages.value?.unshift(res.data?.language as Language)

                toast.push({
                    message: res?.message || 'Language created successfully.',
                    type: 'success',
                })

                return {
                    success: true,
                    message: res?.message || 'Language created successfully.',
                    data: null
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    if (err.data.errors.name || err.data.errors.docker_image || err.data.errors.compile_command || err.data.errors.run_command) {
                        let { name, docker_image, compile_command, run_command } = err.data.errors;
                        name = name ? name[0] : "";
                        docker_image = docker_image ? docker_image[0] : "";
                        compile_command = compile_command ? compile_command[0] : "";
                        run_command = run_command ? run_command[0] : "";

                        toast.push({
                            message: "Validation error. Please check your input.",
                            type: 'error',
                        })

                        return {
                            success: false,
                            message: "Validation error. Please check your input.",
                            data: null,
                            errors: {
                                name,
                                docker_image,
                                compile_command,
                                run_command
                            },
                        }
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
                        name: '',
                        docker_image: '',
                        compile_command: '',
                        run_command: '',
                        message: err.message || 'Something went wrong. Please try again.'
                    },
                }
            }
        }

        async function updateLanguage(id: number, data: { name: string, docker_image: string, compile_command: string | null, run_command: string }): Promise<ReturnData<any>> {
            try{
                const res = await api<ReturnData<{ language: Language, message: string}>>(`/admin/languages/${id}`, {
                    method: 'PUT',
                    body: data
                })

                if(!languages.value?.length){
                    await fetchLanguages()
                }

                const updatedLanguage = res.data?.language as Language
                const lang = languages.value?.find(y => y.id === id) as Language

                Object.assign(lang, updatedLanguage)

                toast.push({
                    message: res.data?.message || 'Language updated successfully.',
                    type: 'success',
                })

                return {
                    success: true,
                    data: null,
                    message: res.data?.message || 'Language updated successfully.'
                }
            } catch (err: any) {
                if (err?.data?.errors) {
                    if (err.data.errors.name || err.data.errors.docker_image || err.data.errors.compile_command || err.data.errors.run_command) {
                        let { name, docker_image, compile_command, run_command } = err.data.errors;
                        name = name ? name[0] : "";
                        docker_image = docker_image ? docker_image[0] : "";
                        compile_command = compile_command ? compile_command[0] : "";
                        run_command = run_command ? run_command[0] : "";

                        toast.push({
                            message: "Validation error. Please check your input.",
                            type: 'error',
                        })

                        return {
                            success: false,
                            data: null,
                            message: "Validation error. Please check your input.",
                            errors: {
                                name,
                                docker_image,
                                compile_command,
                                run_command
                            },
                        }
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
                        name: '',
                        docker_image: '',
                        compile_command: '',
                        run_command: '',
                        message: err.message || 'Something went wrong. Please try again.'
                    },
                }
            }
        }

        async function archiveLanguage(id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ language: Language }>>(`/admin/languages/${id}`, {
                    method: 'DELETE'
                })

                if(!languages.value?.length){
                    await fetchLanguages()
                }

                const archivedLanguage = res.data?.language as Language
                const lang = languages.value?.find(y => y.id === id) as Language

                Object.assign(lang, archivedLanguage)

                toast.push({
                    message: res?.message || 'Language archived successfully.',
                    type: 'success',
                })
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }

        async function restoreLanguage(id: number): Promise<void> {
            try{
                const res = await api<ReturnData<{ language: Language }>>(`/admin/languages/${id}/restore`, {
                    method: 'POST'
                })

                if(!languages.value?.length){
                    await fetchLanguages()
                }
                
                const restoredLanguage = res.data?.language as Language
                const lang = languages.value?.find(y => y.id === id) as Language

                Object.assign(lang, restoredLanguage)

                toast.push({
                    message: res?.message || 'Language restored successfully.',
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
            languages,
            language,
            archived_languages,
            all_languages,
            meta,
            fetchLanguages,
            fetchLanguage,
            fetchAllLanguages,
            createLanguage,
            updateLanguage,
            archiveLanguage,
            restoreLanguage
        }
    },
    {
        persist: {
            pick: ['languages', 'archived_languages', 'all_languages'],
        },
    }
)
