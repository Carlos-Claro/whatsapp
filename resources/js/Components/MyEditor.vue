<script setup>

import Editor from 'primevue/editor';
import { ref } from 'vue';

const props = defineProps({
    title: String,
    id: String,
    isLabel: Boolean,
})
const modelValue = defineModel('modelValue')
console.log(modelValue)
const emit = defineEmits(['update:modelValue'])
const editor = ref()
const editorLoad = () => {
    const delta = editor.value.quill.clipboard.convert({ html: modelValue.value });
    editor.value.quill.setContents(delta, 'silent');
}
const changeValue = (e) => {
    emit('update:modelValue', e.htmlValue)
}
</script>
<template>
    <label :for="props.id" v-if="props.title">{{ props.title }}</label>
        <Editor
            @load="editorLoad"
            ref="editor"
            v-model="modelValue"
            @text-change="changeValue($event)"
            editorStyle="height: 250px;"
            :id="props.id"
            :placeholder="props.title"
            content-type="text"
            >
            <template v-slot:toolbar>
                <span class="ql-formats">
                    <button v-tooltip.bottom="'Bold'" class="ql-bold"></button>
                    <button v-tooltip.bottom="'Italic'" class="ql-italic"></button>
                    <button v-tooltip.bottom="'Underline'" class="ql-underline"></button>
                    <button v-tooltip.bottom="'Strike'" class="ql-strike"></button>
                </span>
                <span class="ql-formats">
                    <select v-tooltip.bottom="'Color'" class="ql-color"></select>
                    <select v-tooltip.bottom="'Background Color'" class="ql-background"></select>
                </span>
                <span class="ql-formats">
                    <button v-tooltip.bottom="'List Ordered'" class="ql-list" value="ordered"></button>
                    <button v-tooltip.bottom="'List Bullet'" class="ql-list" value="bullet"></button>
                    <button v-tooltip.bottom="'Indent'" class="ql-indent" value="-1"></button>
                    <button v-tooltip.bottom="'Indent'" class="ql-indent" value="+1"></button>
                </span>
                <span class="ql-formats">
                    <select v-tooltip.bottom="'Align'" class="ql-align"></select>
                </span>
                <span class="ql-formats">
                    <button v-tooltip.bottom="'Link'" class="ql-link"></button>
                </span>
                <span class="ql-formats">
                    <button v-tooltip.bottom="'Clean'" class="ql-clean"></button>
                </span>
            </template>
        </Editor>
</template>
