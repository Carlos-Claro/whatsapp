<script setup>
import { router } from '@inertiajs/vue3';
import { Button, InputText, Select } from 'primevue';
import { ref } from 'vue';

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => []
    },
    type: {
        type: String,
        default: ''
    },
    addon: {
        type: Object,
        default: () => []
    },
    id: {
        type: Number,
        default: 0
    },
    department_id: {
        type: Number|undefined,
        default: undefined
    }
})
console.log(props.addon);

const modelValue = defineModel('modelValue')
console.log(modelValue.value)

const departmentId = defineModel('department_id')
const emit = defineEmits(['update:modelValue', 'save', 'update:departmentId'])

const deleteModelValue = (key) => {
    const newModelValue = modelValue.value.filter((item, index) => index !== key)
    emit('update:modelValue', newModelValue)
}
const addModelValue = () => {
    const newModelValue = [...modelValue.value, newItem.value]
    emit('update:modelValue', newModelValue)
    newItem.value = {}
}
const newItem = ref({})
const nextQuestion = (key) => {
    emit('save')
    router.get(route('start_conversation.create',{'tag': modelValue.value[key].tag, 'start_conversation_id': props.id}))
}
</script>
<template>
    <Fieldset legend="Respostas">
        <div class="grid grid-cols-1 gap-2">
            <label v-if="props.type == 'button'">Cadastre as respostas para a pergunta.</label>
            <label v-else-if="props.type == 'department'">Selecione o Departamento de encaminhamento.</label>
            <label v-else-if="props.type == 'text'">Defina uma ação para a resposta em texto</label>
        </div>
        <div v-if="props.type == 'button'">
            <template v-for="(answer, key) in modelValue.resume" >
                <div class="grid grid-cols-3 gap-2 my-2">
                    <div v-for="(field, keyFields) in addon.button_fields">
                        <InputText
                            v-model="answer[keyFields]"
                            :placeholder="field.title"
                            class="w-full"
                            />
                    </div>
                    <div>
                        <Button label="Implementar resposta" @click="nextQuestion(key)"  severity="info" class="mx-2" v-if="! answer['related']" />
                        <Button label="Verificar Resposta" @click="relatedQuestion(key)"  severity="info" class="mx-2" v-if="answer['related']" />
                        <Button label="Deletar" @click="deleteModelValue(key)" severity="danger" class="mx-2" />
                    </div>
                </div>
            </template>
            <div class="grid grid-cols-3 gap-2 my-2">
                <div v-for="(field, keyFields) in addon.button_fields">
                    <InputText
                        v-model="newItem[keyFields]"
                        :placeholder="field.title"
                        class="w-full"
                        />
                </div>
                <div>
                    <Button label="Adicionar" severity="success" @click="addModelValue"/>
                </div>
            </div>
        </div>
        <div v-if="props.type == 'text'">
            <div class="grid grid-cols-4 gap-2 my-2" v-for="(answer, key) in modelValue" >
                <div v-for="(field, keyFields) in addon.text_fields">
                    <InputText
                        v-model="answer[keyFields]"
                        :placeholder="field.title"
                        class="w-full"
                        />
                    </div>
                    <Button label="Deletar" @click="deleteModelValue(key)" severity="danger" class="mx-2" />
            </div>
            <div class="grid grid-cols-4 gap-2 my-2">
                <div v-for="(field, keyFields) in addon.text_fields">
                    <InputText
                        v-model="newItem[keyFields]"
                        :placeholder="field.title"
                        class="w-full"
                        />
                </div>
                    <Button label="Adicionar" severity="success" @click="addModelValue"/>
            </div>
        </div>
        <div v-if="props.type == 'department'">
            <Select
                id="department"
                dataKey="department"
                :placeholder="`Selecione o Departamento`"
                v-model="departmentId"
                :options="addon.departments"
                checkmark
                optionLabel="text"
                optionValue="value"
                class="w-full md:w-14rem"
                @change="emit('update:departmentId', $event)"
                ></Select>
        </div>

    </Fieldset>
</template>
