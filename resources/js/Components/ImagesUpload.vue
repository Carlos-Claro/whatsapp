<script setup>
import { PhotoIcon} from '@heroicons/vue/24/solid'
import { onUpdated, ref } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import FileUpload from 'primevue/fileupload';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import UploadItem from '@/Components/UploadItem.vue';
import Panel from 'primevue/panel';
import Divider from 'primevue/divider';
const page = usePage()
const props = defineProps({
    title:{type:String},
    keyValue: String,
    data:{type:[Array, Object]},
    item:{type:Object},
    modelValue: {type: String},
    field:Array,
    action: String,
})
const toast = useToast()
const formData = {
    'item_id': props.item.id,
    'subtype': 'images',
    'title': '',
    'archives': null,
}
const form = useForm(formData)
const upload = (e) => {
    form.archives = e.files
    form.post(route(props.field.action.url, props.field.action.addon), {
        preserveScroll:true,
        forceFormData: true,
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Upload efetuado com sucesso', 'detail' : 'A lista vai atualizar automaticamente', life: 3000})
        }
    })
}
const ReOrder = (e) => {
    console.log(e)
    let items;
    if ( e[0] == 'down' ){
        items = sequence.map((item, key) => {
            if ( e[2] == key ){
                item.order = key+1
                return item
            }else if ((e[2] + 1) == key){
                item.order = key-1
                return item
            }else{
                item.order = key
                return item
            }
        })
    }else{
        items = sequence.map((item, key) => {
            if ( e[2] == key ){
                item.order = key-1
                return item
            }else if ((e[2] - 1) == key){
                item.order = key+1
                return item
            }else{
                item.order = key
                return item
            }
        })
    }
    router.patch(route('attachments.updates', {'items':items}), {
        preserveScroll:true,
        preserveState:true,
        reload:true,
        onFinish: visit => {
            toast.add({ severity: 'success', summary: 'Ok', 'detail' : page.props.flash, life: 3000})
        }
    })
}

let sequence = ref([])
const setSequence = () => {
}
onMounted(() => {
    sequence = props.data.map((item) => {return {'order':item.order, 'id': item.id}})    
})
onUpdated(() => {
    sequence = props.data.map((item) => {return {'order':item.order, 'id': item.id}})
})
</script>
<template>
    <Toast />
    <div class="col-span-full">
        <div class="w-full">
            <FileUpload 
                :name="keyValue" 
                :customUpload="true"
                @uploader="upload"
                :multiple="true" 
                accept="image/*, video/*" 
                :maxFileSize="100000000000" 
                mode="advanced"
                :show-upload-button="props.isUpload"
                choose-label="Selecione"
                upload-label="Salvar Arquivo"
                cancel-label="Limpar"
                >
                <template #empty>
                    <div class="card text-center grid grid-flow-row">
                        <PhotoIcon class="mx-auto h-12 w-12 text-gray-300" aria-hidden="true"  />
                        <p class="text-xs leading-5 text-gray-600">Arraste aqui seu PNG, JPG up to 1000MB</p>
                    </div>
                </template>
            </FileUpload>
            <Panel header="Instruções !!" toggleable class="mt-4 mb-4">
                <ul>
                    <li>Utilize o campo de titulo para dar um nome e descrição para a imagem</li>
                    <li v-if="props.field.showSubtypeSelect">Confira se o tipo de imagem condiz com a função que deseja utiliza-la</li>
                    <li>A Primeira imagem é a capa</li>
                    <li><i class="pi pi-arrows-v"></i> - Altera a ordem das images</li>
                    <li><i class="pi pi-pen-to-square"></i> - Salva as alterações de valores de imagem, sempre que alterar algo, utilize-a, as imagens tem caracteristicas isoladas.</li>
                    <li><i class="pi pi-times"></i> - Deleta o item, com confirmação</li>
                </ul>
            </Panel> 
            <template v-for="image, key in props.data">
                <Divider/>
                <UploadItem 
                :image="image" 
                :keyOrder="key"
                @update-order="ReOrder"
                :showSubtypeSelect="props.field.showSubtypeSelect"
                />
            </template>

        </div>
    </div>

</template>
