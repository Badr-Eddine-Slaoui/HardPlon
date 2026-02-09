<template>
    <QuillEditor
        :content="modelValue"
        @update:content="onUpdate"
        theme="snow"
        toolbar="full"
        content-type="html"
    />
</template>

<script setup lang="ts">
    import { QuillEditor } from '@vueup/vue-quill'
    import '@vueup/vue-quill/dist/vue-quill.snow.css'

    const props = defineProps<{
        modelValue: string
    }>()

    const emit = defineEmits<{
        (e: 'update:modelValue', value: string): void
    }>()

    const onUpdate = (value: string) => {
        emit('update:modelValue', value)
    }
</script>
