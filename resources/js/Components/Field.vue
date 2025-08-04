<script setup>
import { Calendar, Checkbox, InputMask, InputNumber, InputText, Select, SelectButton, ToggleButton } from 'primevue';
import { ref } from 'vue';
import MyEditor from './MyEditor.vue';
import ImageUpload from './ImageUpload.vue';


defineProps({
    type:{
        type: [String, Number, Boolean],
        default: 'InputText'
    },
    attr:{
        type: [Array,String],
    },
    title: String,
    keyValue: String,
    autoCols: Boolean,
    mask: String,
    value: Array|Object,
    item: Object,
    image: Array|Object,
})
const modelValue = defineModel('modelValue')
const emit = defineEmits(['update:modelValue']);
const input = ref(null);
const emitCheck = (e) => {
    emit('update:modelValue', e.target.value)
}

</script>
<template>
    <template v-if="type === 'InputText'" >
        <InputText
            ref="input"
            :id="keyValue"
            v-model="modelValue"
            type="text"
            :placeholder="title"
            :disabled="attr.indexOf('disabled') > -1 ? true : false"
            :readonly="attr.indexOf('readonly') > -1 ? true : false"
            :autofocus="attr.indexOf('autofocus') > -1 ? true : false"
            @input="$emit('update:modelValue', $event.target.value)"
            class="w-full"
            />

    </template>
    <template v-if="type === 'InputNumber'" >
        <InputNumber
            ref="input"
            :id="keyValue"
            v-model="modelValue"
            :placeholder="title"
            :disabled="attr.indexOf('disabled') > -1 & !autoCols ? true : false"
            :readonly="attr.indexOf('readonly') > -1 & !autoCols ? true : false"
            :autofocus="attr.indexOf('autofocus') > -1 & !autoCols ? true : false"
            @input="$emit('update:modelValue', $event.target.value)"
            class="w-full"
            />

    </template>
    <template v-if="type === 'InputMask'" >
        <InputMask
            ref="input"
            :id="keyValue"
            v-model="modelValue"
            :placeholder="title"
            :mask="value.mask"
            :disabled="attr.indexOf('disabled') > -1 & !autoCols ? true : false"
            :readonly="attr.indexOf('readonly') > -1 & !autoCols ? true : false"
            :autofocus="attr.indexOf('autofocus') > -1 & !autoCols ? true : false"
            @input="$emit('update:modelValue', $event.target.value)"
            class="w-full"
            />

    </template>
    <template v-if="type === 'Calendar'" >
        <Calendar
            ref="input"
            :id="keyValue"
            @input="$emit('update:modelValue', $event.target.value)"
            v-model="modelValue"
            :placeholder="title"
            :disabled="attr.indexOf('disabled') > -1 & !autoCols ? true : false"
            :readonly="attr.indexOf('readonly') > -1 & !autoCols ? true : false"
            :autofocus="attr.indexOf('autofocus') > -1 & !autoCols ? true : false"
            dateFormat="yy-mm-dd"
            showIcon
            iconDisplay="input"
            showButtonBar
            class="w-full"
            />

    </template>
    <template v-else-if="type === 'Checkbox'">
        <template v-if="value.type[1] == 'checkbox'">
            <Checkbox
                v-model="modelValue"
                :id="keyValue"

                :binary="true"
                />
        </template>
        <template v-else>
            <ToggleButton
                v-model="modelValue"
                onLabel="Liberado"
                offLabel="Bloqueado"
                onIcon="pi pi-lock"
                offIcon="pi pi-lock-open"
                :id="keyValue"
                @click="emitCheck"
                :disabled="attr.indexOf('disabled') > -1 ? true : false"
                :readonly="attr.indexOf('readonly') > -1 ? true : false"
                :autofocus="attr.indexOf('autofocus') > -1 ? true : false"
                aria-label=""
                class="w-full"
                />
        </template>
    </template>
    <template v-if="type === 'Select'">
        <Select
            :id="keyValue"
            :dataKey="keyValue"
            :placeholder="`Selecione ${title}`"
            v-model="modelValue"
            :options="value.items"
            checkmark
            optionLabel="text"
            optionValue="value"
            :disabled="attr.indexOf('disabled') > -1 ? true : false"
            :readonly="attr.indexOf('readonly') > -1 ? true : false"
            :autofocus="attr.indexOf('autofocus') > -1 ? true : false"
            class="w-full md:w-14rem"
            ></Select>
    </template>
    <template v-if="type === 'SelectButton'">
        <SelectButton
            ref="input"
            :id="keyValue"
            @input="$emit('update:modelValue', $event.target.value)"
            v-model="modelValue"
            :options="value.items"
            optionLabel="text"
            optionValue="value"
            :disabled="attr.indexOf('disabled') > -1 ? true : false"
            :readonly="attr.indexOf('readonly') > -1 ? true : false"
            :autofocus="attr.indexOf('autofocus') > -1 ? true : false"
            class="w-full"
        />
    </template>
    <template v-if="type === 'Editor'">
        <MyEditor
            v-model="modelValue"
            :id="keyValue"
            />
    </template>
    <template v-if="type === 'ImageUpload'">
        <ImageUpload
            :title="title"
            :data="item[keyValue] ? item[keyValue] : false"
            :keyValue="keyValue"
            :item="item"
            v-model="modelValue"
            :isUpload="item ? true : false"
            :field="image"
            />
    </template>

</template>
