<script setup>
import { ref } from 'vue';
import Image from './Image.vue';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import FloatLabel from 'primevue/floatlabel';
import { router, useForm, usePage } from '@inertiajs/vue3';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from "primevue/useconfirm";
import ConfirmPopup from 'primevue/confirmpopup';

import Dropdown from 'primevue/dropdown';
const page = usePage()

const props = defineProps({
    image: Object,
    updateShows: Boolean,
    updateOrder: Object,
    keyOrder:Number,
    showSubtypeSelect: {type:Boolean, default:false}
})
const subtypes = [
    {'value': 'images','text': 'Imagem',},
    {'value': 'galery','text': 'Galeria Imagem',},
    {'value': 'videos','text': 'Videos',},
    {'value': 'banner','text': 'Banner',},
    {'value': 'document','text': 'Documentos',},
]
const form = useForm({
    title: props.image.title,
    order: props.image.order,
    subtype: props.image.subtype,
})
const emit = defineEmits(['updateShows', 'updateOrder'])
const toast = useToast()
const confirm = useConfirm()
const deleteArchive = (event) => {
    confirm.require({
        target: event.currentTarget,
        message: 'Tem certeza que deseja deletar o item?',
        icon: 'pi pi-exclamation-triangle',
        rejectClass: 'p-button-secondary p-button-outlined p-button-sm',
        acceptClass: 'p-button-sm',
        rejectLabel: 'Cancela',
        acceptLabel: 'Deletar',
        accept: () => {
            router.delete(route('attachments.destroy', props.image.id), {
                preserveScroll:true,
                onSuccess: () => {
                    emit('updateShows', [false, true])
                    toast.add({ severity: 'success', summary: 'Ok', 'detail' : 'Imagem deletada com sucesso', life: 3000})
                    },
            })
        },
        reject: () => {
            toast.add({ severity: 'error', summary: 'Cancelou', detail: 'Nada foi modificado nada', life: 3000 })
        }
    })
    
}
const SaveTitle = () => {
    form.patch(route('attachments.update', props.image.id), {
        preserveScroll:true,
        onFinish: visit => {
            toast.add({ severity: 'success', summary: 'Ok', 'detail' : page.props.flash, life: 3000})
        }
    })
}
</script>
<template>
    <ConfirmPopup></ConfirmPopup>
    <Toast />
    <div class="p-fileupload">
        {{ props.image.id }} - {{ props.keyOrder }}
        <div class="p-fileupload-file">
            <template v-if="props.image.subtype == 'images'">
                <Image :image="props.image" class="p-fileupload-file-thumbnail w-36"/>
            </template>
            <template v-else>
                <video width="200" controls>
                    <source :src="props.image.get_image_address">
                </video>
            </template>
            <div class="p-fileupload-file-details grid grid-cols-2 gap-6">
                <p class="p-fileupload-file-name">
                    <FloatLabel>
                        <InputText v-model="form.title" id="title" class="w-full md:w-14rem"/>
                        <label for="title">Titulo Image</label>
                    </FloatLabel>
                </p>
                <template v-if="props.showSubtypeSelect">
                    <p class="p-fileupload-file-name">
                        <FloatLabel>
                            <Dropdown 
                                id="subtype"
                                v-model="form.subtype"
                                :options="subtypes"
                                optionLabel="text" 
                                optionValue="value"
                                :placeholder="`Selecione tipo de imagem, vídeo`"
                                class="w-full md:w-14rem" 
                                />
                            <label for="subtype">Tipo de Imagem, Vídeo</label>
                        </FloatLabel>

                    </p>
                </template>
            </div>
            <div class="p-fileupload-file-actions">
                <div class="p-fileupload-file-remove">
                    <Button
                    v-if="props.image && props.keyOrder > 0"
                    type="button"
                    class="mt-2"
                    text
                    icon="pi pi pi-arrow-up"
                    @click.prevent="emit('updateOrder', ['up', props.image.id, keyOrder])"
                    v-tooltip.bottom="'Reordene as imagens Up'"
                    />
                    <Button
                    v-if="props.image"
                    type="button"
                    class="mt-2"
                    text
                    icon="pi pi pi-arrow-down"
                    @click.prevent="emit('updateOrder',['down', props.image.id, keyOrder])"
                    v-tooltip.bottom="'Reordene as imagens Down'"
                    />
                    <Button
                    v-if="props.image"
                    type="button"
                    class="mt-2"
                    text
                    icon="pi pi-pen-to-square"
                    @click.prevent="SaveTitle"
                    v-tooltip.bottom="'Editar Campos'"
                    />
                    <Button
                    v-if="props.image"
                    type="button"
                    class="mt-2"
                    text
                    icon="pi pi-times"
                    @click="deleteArchive($event)"
                    v-tooltip.bottom="'Deletar Image'"
                    />
                </div>
            </div>
        </div>
        
    </div>
</template>