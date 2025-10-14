<script setup>

import InputError from '@/Components/InputError.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { Button, ConfirmDialog, Fieldset, InputText, Panel, Select, Toast, useToast } from 'primevue';
import Answers from './Partials/Answers.vue';
import { onMounted, reactive, readonly, ref, watch } from 'vue';
import { defaultDocument } from '@vueuse/core';

const toast = useToast();
const page = usePage();
console.log(page.props);
let formData = () => {
    return Object.keys(page.props.filters.fields).filter((key) => true).reduce((acc, curr) => {
        if ( page.props.item[curr] ){
            acc[curr] = page.props.item[curr]
        }else{
            acc[curr] = page.props.filters.fields[curr].default
        }
        return acc
    }, {});
}
console.log(formData())

let form = useForm(formData)
const save = () => {
    console.log(page.props.filters.action)
    console.log(form);

    if ( form.id ) {
        form.put(route(page.props.filters.action, form[page.props.key]), {
            preserveState: true,
            preserveScroll: true,
            onError: (errors) => {
                toast.add({ severity: 'danger', summary: 'Problemas para salvar .', 'detail' : errors.messages, life: 3000})
            },
            onFinish: visit => {
                toast.add({ severity: 'success', summary: 'Salvo, pode seguir para as próximas etapas ', 'detail' : '', life: 3000})

            },
        })
    }else{
        form.post(route(page.props.filters.action), {
            preserveState: true,
            onSuccess: (e) => {
                toast.add({ severity: 'success', summary: 'Salvo, pode seguir para as próximas etapas ', 'detail' : '', life: 3000})
            },
            onError: (errors) => {
                let errorKeys = Object.keys(errors)
                errorKeys.map(value => {
                    toast.add({ severity: 'error', summary: 'Problemas para salvar.', 'detail' : errors[value], life: 7000})

                })
            },
        })
    }
}
const type = ref('')
watch(() => form.type, (newValue) => {
    type.value = newValue
})
onMounted(() => {
    type.value = form.type
})
watch(() => form.answer, (newValue) => {
    console.log(newValue)

})
watch(() => form.department_id, (newValue) => {
    console.log(newValue)
})
</script>
<template>
    <ConfirmDialog></ConfirmDialog>
    <Toast />
    <Head :title="page.props.title" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ page.props.title }}
            </h2>
        </template>
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 py-6">
            <Panel>
                <template #header>
                    <div class="grid grid-flow-row bg-slate-300 p-2 rounded w-full">
                        <div class="grid grid-cols-2 gap-2 border-b-2 p-1"><span class="font-bold">ID: </span>{{ form.id }}</div>
                        <div class="grid grid-cols-2 gap-2 border-b-2 p-1"><span class="font-bold">Pergunta: </span>{{ form.question }}</div>
                        <div class="grid grid-cols-2 gap-2 border-b-2 p-1"><span class="font-bold">Tag: </span>{{ form.tag }}</div>
                        <div class="grid grid-cols-2 gap-2 border-b-2 p-1"><span class="font-bold">Criada em: </span>{{ page.props.item.created_at }}</div>
                        <div v-if="page.props.item.related" class="grid grid-cols-2 gap-2 border-b-2 p-1"><span class="font-bold">Ligação: </span>{{ page.props.item.related.question }} : {{ form.tag }}</div>
                    </div>
                </template>
                <div class="grid grid-flow-row gap-2">
                    <div class="grid grid-cols-2 gap-2 w-full bg-slate-300 p-2 rounded" v-if="form[page.props.key] || page.props.item.tag">
                        <div class="grid col-span-2">
                            <h3>Vinculo:</h3>
                        </div>
                        <div v-if="page.props.item.related">
                            <label class="block mb-2">Pergunta anterior:</label>
                            <InputText
                                v-model="page.props.item.related.question"
                                placeholder="Pergunta anterior"
                                :readonly="true"
                                class="w-full"
                                />
                        </div>
                        <div v-if="page.props.item.tag">
                            <label class="block mb-2">{{page.props.filters.fields['tag'].title}}:</label>
                            <InputText
                                v-model="form.tag"
                                :placeholder="page.props.filters.fields['tag'].title"
                                :readonly="true"
                                class="w-full"
                                />
                            <InputError class="mt-2" :message="form.errors?.tag"  />
                        </div>
                    </div>
                    <div class="grid grid-flow-row grid-cols-2 gap-2 w-full bg-slate-300 p-2 rounded">
                        <div class="col-span-2">
                            <label class="block mb-2">{{page.props.filters.fields['question'].title}}:</label>
                            <InputText
                                v-model="form.question"
                                :placeholder="page.props.filters.fields['question'].title"
                                :autofocus="true"
                                class="w-full"
                                />
                            <InputError class="mt-2" :message="form.errors?.question" />
                        </div>
                        <div>
                            <label class="block mb-2">{{page.props.filters.fields['type'].title}}:</label>
                            <Select
                                id="type"
                                dataKey="type"
                                :placeholder="`Selecione o tipo`"
                                v-model="form.type"
                                :options="page.props.filters.fields.type.items"
                                checkmark
                                optionLabel="text"
                                optionValue="value"
                                class="w-full md:w-14rem"
                                ></Select>
                                <InputError class="mt-2" :message="form.errors?.type" />
                        </div>
                        <div>
                            <label class="block mb-2">{{page.props.filters.fields['status'].title}}:</label>
                            <Select
                                id="status"
                                dataKey="status"
                                :placeholder="`Selecione o Status`"
                                v-model="form.status"
                                :options="page.props.filters.fields['status'].items"
                                checkmark
                                optionLabel="text"
                                optionValue="value"
                                class="w-full md:w-14rem"
                                ></Select>
                                <InputError class="mt-2" :message="form.errors?.status" />
                        </div>

                    </div>
                    <Answers
                        :type="form.type"
                        v-model="form.answer"
                        v-model:department_id="form.department_id"
                        :addon="page.props.filters.addon"
                        :id="form.id"
                        class="w-full bg-slate-300 p-2 rounded"
                        @save="save()"
                         />
                </div>

                <template #footer>
                    <div class="flex justify-end">
                        <button type="button" class="btn btn-primary" @click="save">
                            Salvar
                        </button>
                    </div>
                </template>
            </Panel>
        </div>

    </AuthenticatedLayout>
</template>
