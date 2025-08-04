<script setup>
import Form from '@/Components/Form.vue';
import Paginate from '@/Components/Paginate.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { Button, Column, ConfirmDialog, DataTable, Menu, Panel, Toast, useConfirm, useToast } from 'primevue';
import { computed, ref, watch } from 'vue';

const page = usePage()
console.log(page.props)

const selected = ref(null)
const menu = ref(null)
const toggle = (event) => {
    menu.value.toggle(event)
}
const confirm = useConfirm()
const toast = useToast()
const deleteItems = (e) => {
    if (selected.value.length){
        const message = 'Tem certeza que deseja deletar a(s) ' + selected.value.length + ' ' + page.props.title + '(s) e os seus vinculos?'
        confirm.require({
            message: message,
            icon: 'pi pi-exclamation-triangle',
            rejectClass: 'p-button-secondary p-button-outlined p-button-sm',
            acceptClass: 'p-button-sm',
            rejectLabel: 'Cancelar',
            acceptLabel: 'Deletar',
            accept: () => {
                selected.value.map((item) => {
                    router.delete(route(page.props.actions.main.delete.action, item.id),{
                        onSuccess: (e) => {
                            toast.add({ severity: 'success', summary: 'Ok', detail: page.props.title + ' ' + item.title + ' deletado com sucesso', life: 3000 })
                        },
                        onError: (e) => {
                            console.log(e)
                            toast.add({ severity: 'error', summary: 'Erro', detail: e.messages, life: 3000 })
                        },
                    })
                })
            },
            reject: () => {
                toast.add({ severity: 'error', summary: 'Cancelou', detail: 'Nada foi modificado nada', life: 3000 })
            }
        })
    }else{
        toast.add({ severity: 'error', summary: 'Nenhum item selecionado', detail: 'Selecione itens para deletar', life: 4000 })
    }
}
const actions = computed(() => {
    return Object.keys(page.props.actions.main).map((item) => {
        var i = page.props.actions.main[item]
        if ( i.route || i.url ){
            return {...i}
        }else{
            return {
                ...i,
                'command': () => {
                    if ( item == "delete" ){
                        deleteItems()
                    }
                }
            }
        }
    })
})
const collapsed = computed(() => {
    let data = false
    data = Object.keys(page.props.filters.items).filter((key) => {
        if ( page.props.filters.items[key] ){
            return true
        }
    }).reduce((acc, curr) => {
        return true
    }, {});
    return typeof data == 'boolean' ? false : true
})
console.log(page.props.list)
const print = (item) => { console.log(item) };
</script>
<template>
    <ConfirmDialog></ConfirmDialog>
    <Toast />
    <Head :title="page.props.title" />
    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                {{ page.props.title }}
            </h2>
        </template>
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 py-6">
            <div class="card">
                <Panel
                    toggleable
                    :collapsed="collapsed"
                    >
                    <template #header>
                        Filtros
                    </template>
                    <Form
                            :action="page.props.filters.action"
                            method="get"
                            :fields="page.props.filters.fields"
                            :item="page.props.filters.items"
                            :autoCols="true"
                            titleSubmit="Pesquisar"
                            :group="false"
                            :float="true"
                            />
                </Panel>
            </div>
        </div>
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 py-6">
            <Panel toggleable>
                <template #header>
                    Lista
                </template>
                <template #icons>
                    <Menu ref="menu" id="options" :model="actions" popup />
                    <Button icon="pi pi-cog" severity="secondary" rounded text @click="toggle" />
                </template>
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <DataTable :value="page.props.items.data" v-model:selection="selected" dataKey="id" stripedRows class="" >
                        <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
                        <Column v-for="item, key in page.props.list" :field="key" :header="item.title"></Column>
                        <Column field="action" header="" v-if="page.props.actions.field">
                            <template #body="slotProps">
                                <a v-for="action in page.props.actions.field" :method="action.method" :href="route(action.route, slotProps.data[page.props.key])" class="p-2">
                                    <Button :icon="action.icon" :aria-label="action.label" severity="primary" outlined />
                                </a>
                            </template>
                        </Column>
                        <template #footer >
                            <Paginate :links="page.props.items" />
                        </template>
                    </DataTable>
                </div>
            </Panel>
        </div>
    </AuthenticatedLayout>
</template>
