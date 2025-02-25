<script setup>
import { useForm } from '@inertiajs/vue3'
import { Button, ConfirmDialog, Dialog, Menu, Toast, useConfirm, useToast } from 'primevue'
import { ref, watch } from 'vue'
import Empresa from './Empresa.vue'

const props = defineProps({
    id: {
        type: Number,
        required: true,
    },
})
const form = useForm({
    id: props.id,
    pesquisa: false,
})
const menu = ref()
const toast = useToast()
const confirm = useConfirm()
const itemsMenu = ref([
    {
        label: 'Ações',
        items: [
            {
                label: 'Transferir de setor',
                icon: 'pi pi-flag',
                command: () => {
                    confirm.require({
                        message: 'Deseja transferir a conversa para outro setor?',
                        accept: () => {
                            toast.add({ severity: 'info', summary: 'Transferir de setor', detail: 'Em desenvolvimento.', life: 3000})
                        },
                        reject: () => {
                            toast.add({ severity: 'info', summary: 'Transferir de setor', detail: 'Cancelado.', life: 3000})
                        }
                    })
                },
            },
            {
                label: 'Vincular Empresa',
                icon: 'pi pi-building-columns',
                command: () => {
                    visibleEmpresa.value = true
                },
            },
            {
                label: 'Finalizar conversa com pesquisa de satisfação',
                icon: 'pi pi-times',
                command: () => {
                    confirm.require({
                        message: 'Deseja finalizar a conversa e enviar a pesquisa de satisfação?',
                        accept: () => {
                            form.pesquisa = true
                            form.post(route('close_conversation'), {
                                onSuccess: () => {
                                    toast.add({ severity: 'success', summary: 'Finalizando conversa', detail: 'Conversa finalizada com sucesso.', life: 3000})
                                },
                                onError: () => {
                                    toast.add({ severity: 'danger', summary: 'Finalizando conversa', detail: 'Erro ao finalizar conversa.', life: 3000})
                                },
                            })
                        },
                        reject: () => {
                            toast.add({ severity: 'info', summary: 'Finalizando conversa', detail: 'Cancelado.', life: 3000})
                        }
                    })
                },
            },
            {
                label: 'Finalizar conversa sem pesquisa',
                icon: 'pi pi-times',
                command: () => {
                    confirm.require({
                        message: 'Deseja finalizar a conversa?',
                        accept: () => {
                            form.pesquisa = false
                            form.post(route('close_conversation'), {
                                onSuccess: () => {
                                    toast.add({ severity: 'success', summary: 'Finalizando conversa', detail: 'Conversa finalizada com sucesso.', life: 3000})
                                },
                                onError: () => {
                                    toast.add({ severity: 'danger', summary: 'Finalizando conversa', detail: 'Erro ao finalizar conversa.', life: 3000})
                                },
                            })
                        },
                        reject: () => {
                            toast.add({ severity: 'info', summary: 'Finalizando conversa', detail: 'Cancelado.', life: 3000})
                        }
                    })
                },
            },
        ],
    },
])
const toogle = (event) => {
    menu.value.toggle(event);
};
const visibleEmpresa = ref(false)
</script>
<template>
    <Toast />
    <ConfirmDialog></ConfirmDialog>
    <Button
        type="button"
        icon="pi pi-ellipsis-v"
        @click="toogle"
        aria-haspopup="true"
        aria-controls="overlay_menu"
        class=" "
        variant="text"
        />
    <Menu
        ref="menu"
        id="overlay_menu"
        :model="itemsMenu"
        :popup="true"
        />
    <Dialog
        v-model:visible="visibleEmpresa"
        header="Vincular Empresa"
        modal
        :closable="true"
        :dismissable="false"
        :showHeader="true"
        :baseZIndex="10000"
        :style="{width: '50vw'}"
        >
        <Empresa
            @update:visible="visibleEmpresa = event"
         />
    </Dialog>
</template>
