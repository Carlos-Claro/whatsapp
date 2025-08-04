<script setup>
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import Select from '@/Components/Select.vue';
import ImageUpload from '@/Components/ImageUpload.vue';
import { computed } from 'vue';
import { ref } from 'vue';
import { watch } from 'vue';

const props = defineProps({
    fields: {type: Object},
    item: {type: Object},
    titleSubmit: {type: String, default: "Cadastro"},
    action: {type: String},
    method: {type: String, default: "post"},
    autoCols: {type: Boolean, default: false},
    groups: {default:false},
    image: {type: Object, default: false},
})

const widthClass = computed(() => {
    let size = Object.keys(props.fields).length
    return `w-1/${size}`
})
const mask = ref([])
let formData = {}
const keys = Object.keys(props.fields)
keys.filter((key) => {
        if ( props.item ){
            formData[key] = props.item[key]
        }else{
            formData[key] = props.fields[key].default
        }

});
console.log(formData);

let form = useForm(formData)
const submit = () => {

    if ( props.action.indexOf('store') > -1 ){
        form.post(route(props.action), {
            preserveScroll: true,
        });
    }else if ( props.action.indexOf('update') > -1 ) {
        form.put(route(props.action, props.item.id), {
            preserveScroll: false,
        });
    }else{
        form.get(route(props.action), {
            preserveScroll: true,
        });
    }
};
const reset = () => {
    form = useForm({})
    submit()
}
const setMask = (type) => {
    if ( type ){
        switch (type) {
            case 'CPF/CNPJ':
                if (form.document_info.toLocaleLowerCase() === 'cpf'){
                    return '00.000.000-00'
                }
                return '00.000.000/0000-00'
                break;
            case 'postalCode':
                return "00000-000"
                break;
            default:
                break;
        }
    }
}

</script>
<template>
    <form @submit.prevent="submit" >
        <div class="" >
            <template v-if="props.groups" v-for="group in props.groups">
                <div class="box">
                    <div class="sm:w-1/3 w-full inline-block text-black align-top">
                        <h2 class="font-semibold pb-4">
                            {{ group.title }}
                        </h2>
                        <p class="pb-4">
                            {{ group.description }}
                        </p>
                    </div>
                    <div class="sm:w-2/3 w-full rounded border-slate-800 inline-block bg-slate-200">
                        <div  v-for="(value,key) in group.fields" :className="`py-4 px-2`" :key="key">
                            <template v-if="value.type[0] === 'TextInput'">
                                <InputLabel :for="key" :value="value.title"/>
                                <template v-if="value.mask" >
                                    <TextInput
                                        :id="key"
                                        :type="value.type[1]"
                                        class="mt-1 block w-full"
                                        v-model=form[key]
                                        :autocomplete="key"
                                        :disabled="value.attrs.attr.indexOf('disabled') > -1 & !props.autoCols ? true : false"
                                        :readonly="value.attrs.attr.indexOf('readonly') > -1 & !props.autoCols ? true : false"
                                        :autofocus="value.attrs.attr.indexOf('autofocus') > -1 & !props.autoCols ? true : false"
                                        v-mask=setMask(value.mask)
                                        />
                                </template>
                                <template v-else>
                                    <TextInput
                                        :id="key"
                                        :type="value.type[1]"
                                        class="mt-1 block w-full"
                                        v-model=form[key]
                                        :autocomplete="key"
                                        :disabled="value.attrs.attr.indexOf('disabled') > -1 & !props.autoCols ? true : false"
                                        :readonly="value.attrs.attr.indexOf('readonly') > -1 & !props.autoCols ? true : false"
                                        :autofocus="value.attrs.attr.indexOf('autofocus') > -1 & !props.autoCols ? true : false"
                                        />
                                </template>
                            </template>
                            <template v-else-if="value.type[0] === 'Checkbox'">
                                <InputLabel :for="key" >
                                    <Checkbox
                                        :name="key"
                                        :disabled="value.attrs.attr.indexOf('disabled') > -1 ? true : false"
                                        :readonly="value.attrs.attr.indexOf('readonly') > -1 ? true : false"
                                        :autofocus="value.attrs.attr.indexOf('autofocus') > -1 ? true : false"
                                        v-model:checked="form[key]" /> {{ value.title }}
                                </InputLabel>
                            </template>
                            <template v-if="value.type[0] === 'Select'">
                                <InputLabel :for="key" :value="value.title" />
                                <Select
                                    :id="key"
                                    :class="`mt-1 block w-full`"
                                    v-model="form[key]"
                                    :options="value.items"
                                    :disabled="value.attrs.attr.indexOf('disabled') > -1 ? true : false"
                                    :readonly="value.attrs.attr.indexOf('readonly') > -1 ? true : false"
                                    :autofocus="value.attrs.attr.indexOf('autofocus') > -1 ? true : false"
                                />
                            </template>
                            <template v-if="value.type[0] === 'ImageUpload'">
                                <ImageUpload
                                    :title="value.title"
                                    :data="props.image"
                                    :keyValue="key"
                                    :item="props.item"
                                    v-model="form[key]"
                                    :isUpload="props.item ? true : false"
                                    />
                            </template>
                            <InputError class="mt-2" :message="form.errors[key]" />

                        </div>
                    </div>
                </div>
            </template>
            <template v-else>
                <div  v-for="(value,key) in props.fields" :className="`${ props.autoCols ? widthClass : value.attrs.divClass} inline-block py-4 px-2`" :key="key">
                    <template v-if="value.type[0] === 'TextInput'">
                        <InputLabel :for="key" :value="value.title" />
                        <TextInput
                            :id="key"
                            :type="value.type[1]"
                            class="mt-1 block w-full"
                            v-model="form[key]"
                            :autocomplete="key"
                            :disabled="value.attrs.attr.indexOf('disabled') > -1 & !props.autoCols ? true : false"
                            :readonly="value.attrs.attr.indexOf('readonly') > -1 & !props.autoCols ? true : false"
                            :autofocus="value.attrs.attr.indexOf('autofocus') > -1 & !props.autoCols ? true : false"
                        />
                    </template>
                    <template v-else-if="value.type[0] === 'Checkbox'">
                        <InputLabel :for="key" >
                            <Checkbox
                                :name="key"
                                :disabled="value.attrs.attr.indexOf('disabled') > -1 ? true : false"
                                :readonly="value.attrs.attr.indexOf('readonly') > -1 ? true : false"
                                :autofocus="value.attrs.attr.indexOf('autofocus') > -1 ? true : false"
                                v-model:checked="form[key]" /> {{ value.title }}
                        </InputLabel>
                    </template>
                    <template v-if="value.type[0] === 'Select'">
                        <InputLabel :for="key" :value="value.title" />
                        <Select
                            :id="key"
                            :class="`mt-1 block w-full`"
                            v-model="form[key]"
                            :options="value.items"
                            :disabled="value.attrs.attr.indexOf('disabled') > -1 ? true : false"
                            :readonly="value.attrs.attr.indexOf('readonly') > -1 ? true : false"
                            :autofocus="value.attrs.attr.indexOf('autofocus') > -1 ? true : false"
                        />
                    </template>
                    <InputError class="mt-2" :message="form.errors[key]" />

                </div>
            </template>
        </div>
        <template v-if="props.groups">
            <div class="box">
                <div class="sm:w-1/3 w-full inline-block text-black font-semibold align-top">
                    <h4 class="pb-4">
                        Ações
                    </h4>
                    <p>

                    </p>
                </div>
                <div class="sm:w-2/3 w-full rounded border-slate-800 inline-block">
                    <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        {{ props.titleSubmit }}
                    </PrimaryButton>
                </div>
            </div>
        </template>
        <template v-else>
            <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                {{ props.titleSubmit }}
            </PrimaryButton>
            <template v-if="props.action.indexOf('index') > -1">
                <PrimaryButton class="ms-4" type="button" @click="reset" >
                    Limpar
                </PrimaryButton>
            </template>
        </template>
        </form>
    </template>
