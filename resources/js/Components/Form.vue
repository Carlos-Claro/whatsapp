<script setup>
import { useForm } from '@inertiajs/vue3';
import Field from '@/Components/Field.vue';
import InputError from '@/Components/InputError.vue';
import { computed } from 'vue';
import FloatLabel from 'primevue/floatlabel';
import Button from 'primevue/button';

const props = defineProps({
    fields: {type: Object},
    item: {type: Object},
    titleSubmit: {type: String, default: "Cadastro"},
    action: {type: String},
    method: {type: String, default: "post"},
    autoCols: {type: Boolean, default: false},
    groups: {default:false},
    image: {type: Object, default: false},
    float: {type: Boolean, default: false},
    submitReturn: {type: Boolean, default: false},
    submitValue: {type: Boolean, default: false},
})
const submitValue = defineModel(['submitValue'])
const emit = defineEmits(['click:submitValue'])
const widthClass = computed(() => {
    let size = Object.keys(props.fields).length
    return `w-1/${size}`
})
let formData = computed(() => {
    return Object.keys(props.fields).filter((key) => true).reduce((acc, curr) => {
        if ( props.item[curr]){
            acc[curr] = props.item[curr]
        }else{
            acc[curr] = props.fields[curr].default
        }
        return acc
    }, {});
})
let form = useForm(formData.value)
const submit = () => {
    if ( props.action.indexOf('store') > -1 ){
        form.post(route(props.action), {
            preserveScroll: true,
            onError: (errors) => {
                let errorKeys = Object.keys(errors)
                errorKeys.map(value => {
                    toast.add({ severity: 'error', summary: 'Problemas para salvar os itens.', 'detail' : errors[value], life: 7000})

                })
            },
            onFinish: (e) => {
                if ( props.submitReturn ){
                    emit('click:submitValue', true)
                }
            }
        });
    }else if ( props.action.indexOf('update') > -1 ) {
        form.put(route(props.action, props.item.id), {
            preserveScroll: false,
            onError: (errors) => {
                let errorKeys = Object.keys(errors)
                errorKeys.map(value => {
                    toast.add({ severity: 'error', summary: 'Problemas para salvar os itens.', 'detail' : errors[value], life: 7000})

                })
            },
        });
    }else{
        form.get(route(props.action), {
            preserveScroll: true,
            onError: (errors) => {
                let errorKeys = Object.keys(errors)
                errorKeys.map(value => {
                    toast.add({ severity: 'error', summary: 'Problemas para salvar os itens.', 'detail' : errors[value], life: 7000})

                })
            },
        });
    }
};
const reset = () => {
    form = useForm({})
    submit()
}
</script>
<template>
    <form @submit.prevent="submit" >
        <template v-if="props.groups" v-for="group in props.groups">
            <div class="sm:w-1/3 w-full inline-block align-top">
                <h2 class="font-semibold pb-4">
                    {{ group.title }}
                </h2>
                <p class="pb-4">
                    {{ group.description }}
                </p>
            </div>
            <div class="sm:w-2/3 w-full inline-block ">
                <div  v-for="(value,key) in group.fields" :className="`py-4 px-2 card block`" :key="key">
                    <label :for="key" class="block mb-2">{{ value.title }}</label>
                    <Field
                    :type="value.type[0]"
                    v-model="form[key]"
                    :attr="value.attrs.attr"
                    :key-value="key"
                    :title="value.title"
                    :auto-cols="props.autoCols"
                    :value="value"
                    :item="props.item"
                    :image="props.image"
                />
                    <InputError class="mt-2" :message="form.errors[key]" />
                </div>
            </div>
        </template>
        <template v-else>
            <div class="grid grid-cols-3 gap-2">
                <div  v-for="(value,key) in props.fields" :key="key" class="card mt-4 mb-4">
                    <template v-if="props.float">
                        <FloatLabel>
                            <Field
                            :type="value.type[0]"
                            v-model="form[key]"
                            :attr="value.attrs.attr"
                            :key-value="key"
                            :title="value.title"
                            :auto-cols="props.autoCols"
                            :value="value"
                            :image="props.image"
                            />
                            <label :for="key">{{ value.title }}</label>
                        </FloatLabel>
                    </template>
                    <template v-else>
                        <label :for="key" class="block mb-2">{{ value.title }}</label>
                        <Field
                            :type="value.type[0]"
                            v-model="form[key]"
                            :attr="value.attrs.attr"
                            :key-value="key"
                            :title="value.title"
                            :auto-cols="props.autoCols"
                            :value="value"
                            :image="props.image"
                            />
                    </template>
                    <InputError class="mt-2" :message="form.errors[key]" />
                </div>
            </div>
        </template>
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
                    <Button class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" type="submit">
                        {{ props.titleSubmit }}
                    </Button>
                </div>
            </div>
        </template>
        <template v-else>
            <div class="grid grid-flow-col auto-cols-max mt-4">
                <Button class="mr-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" :label="props.titleSubmit" type="submit" icon="pi pi-search" />
                <Button @click="reset" label="Limpar" icon="pi pi-refresh" v-if="props.action.indexOf('index') > -1" />
            </div>
        </template>
    </form>
</template>
