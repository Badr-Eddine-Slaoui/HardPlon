import { defineStore } from 'pinia';
import { reactive, ref } from 'vue';
import type { meta, ReturnData } from '../types/api';
import type { Correction } from '../types/correction';
import { useToastStore } from './toast';

export const useCorrection = defineStore(
    'correction',
    () => {
        const api = useApi()
        const corrections = ref<Correction[] | null>(null)
        const correction = ref<Correction | null>(null)
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

        async function fetchStudentCorrections(): Promise<void> {
            try {
                const res = await api<ReturnData<{ corrections: Correction[]}>>('/student/corrections')
                corrections.value = res.data?.corrections as Correction[]
                Object.assign(meta, res.data?.corrections)
            } catch (err: any) {
                if(err.status !== 404){
                    toast.push({
                        message: 'Something went wrong. Please try again.',
                        type: 'error',
                    })
                }
            }
        }

        async function fetchCorrection(brief_id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ correction: Correction}>>(`/student/corrections/${brief_id}`)
                correction.value = res.data?.correction as Correction
            } catch (err: any) {
                if(err.status !== 404){
                    toast.push({
                        message: 'Something went wrong. Please try again.',
                        type: 'error',
                    })
                }
            }
        }

        async function fetchStudentCorrection(brief_id: number, student_id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ correction: Correction}>>(`/teacher/corrections/${brief_id}/${student_id}`)
                correction.value = res.data?.correction as Correction
            } catch (err: any) {
                correction.value = null
                if (err.status !== 404) {
                    toast.push({
                        message: 'Something went wrong. Please try again.',
                        type: 'error',
                    })
                }
            }
        }

        async function fetchStudentCorrectionsById(student_id: number): Promise<void> {
            try {
                const res = await api<ReturnData<{ corrections: Correction[]}>>(`/teacher/corrections/students/${student_id}`)
                corrections.value = res.data?.corrections as Correction[]
            } catch (err) {
                toast.push({
                    message: 'Something went wrong. Please try again.',
                    type: 'error',
                })
            }
        }


        return {
            corrections,
            correction,
            meta,
            fetchStudentCorrections,
            fetchStudentCorrection,
            fetchStudentCorrectionsById,
            fetchCorrection,
        }
    },
    {
        persist: {
            pick: ['corrections'],
        },
    }
)
