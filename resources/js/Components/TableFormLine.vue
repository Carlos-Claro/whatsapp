<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import TextInput from './TextInput.vue';
import InputLabel from './InputLabel.vue';
import Checkbox from './Checkbox.vue';
import Select from './Select.vue';
import InputError from './InputError.vue';
import { reactive } from 'vue';
import SecondaryButton from './SecondaryButton.vue';

const props = defineProps({
    item: {type: Object},
    fields: {type: [Array, Object]},
    keys:{type: Array},
    keyClient:{type: String},
    action:{type: String, required: true},
    method: {type: String, default: 'put'}
})
let formData = {}
props.keys.filter((key) => {
    if ( props.item ){
        formData[key] = props.item[key]
    }else{
        formData[key] = props.fields[key].default
    }
});
let form = useForm(formData)
const editavel = props.method == 'put' ? true : false
const edit = ref(editavel)
const loading = ref('')
const titleSubmit = ref("Editar")
const state = reactive({edit,titleSubmit})
let submit = () => {
    if ( state.edit ){
        state.edit = false
        state.titleSubmit = "Salvar"
    }else{
        state.titleSubmit = "Aguardando"
        let credenciais = {}
        if ( editavel)
        {
            credenciais = { client: props.item[props.keyClient], contact: props.item.id}
        }else{
            credenciais = { client: props.keyClient }
        }

        form.transform((data) => ({...data})).submit(props.method, route(props.action, credenciais),{ preserveScroll: true,})
        state.edit = true
        state.titleSubmit = "Editar"
        if ( ! editavel ){
            form.reset()
        }
    }
}
let destroy = () => {
    let credenciais = { client: props.item[props.keyClient], contact: props.item.id}
    form.delete(route('client.contacts.destroy', credenciais), {})
}
</script>

<template>
    <tr class="hover:bg-slate-600 hover:text-slate-400 ">
        <template v-for="(field, key) in props.fields">
            <td class="px-3 border border-slate-200" >
                <template v-if="field.type[0] === 'TextInput'">
                    <TextInput
                    :id="key"
                    :type="field.type[1]"
                    class="mt-1 block w-full"
                    v-model=form[key]
                    :autocomplete="key"
                    :readonly="field.attrs.attr.indexOf('disabled') > -1 & !props.autoCols ? true : false"
                    v-model:disabled="edit"
                    />
                </template>
                <template v-else-if="field.type[0] === 'Checkbox'">
                    <InputLabel :for="key" >
                        <Checkbox
                            :name="key"
                            v-model:disabled="edit"
                            v-model:checked="form[key]"
                             />
                    </InputLabel>
                </template>
                <template v-if="field.type[0] === 'Select'">
                    <Select
                        :id="key"
                        :class="`mt-1 block w-full`"
                        v-model="form[key]"
                        :selected="form[key]"
                        :options="field.items"
                        v-model:disabled="edit"
                    />
                </template>
                <InputError class="mt-2" :message="form.errors[key]" />
            </td>
        </template>
        <td class="px-3 py-5 border border-slate-200">
            <SecondaryButton type="button" @click="submit" :disabled="form.processing" >{{ editavel ? titleSubmit : "Adicionar" }}</SecondaryButton>
            <SecondaryButton type="button" @click="destroy" :disabled="form.processing" v-if="editavel">Deletar</SecondaryButton>

        </td>
    </tr>
</template>
