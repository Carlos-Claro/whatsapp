<script setup>
import { PhotoIcon} from '@heroicons/vue/24/solid'
import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import FileUpload from 'primevue/fileupload';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import UploadItem from '@/Components/UploadItem.vue';
const props = defineProps({
    title:{type:String},
    keyValue: String,
    data:{type:[Array,Object]},
    item:{type:Object},
    modelValue: {type: [String, Object]},
    field:Array|Object,
    isUpload: Boolean,
})
const toast = useToast()
const showUpload = ref(false)
const showItem = ref(false)
onMounted(
    () => {
        props.data ? changeShows([true, false]) : changeShows([false, true])
    }
)
const formData = {
    'item_id': props.data.archive ? props.data.attachmentable_id : props.item.id,
    'subtype': props.data.archive ? props.data.subtype : props.field.subtype,
    'title': props.data.archive ? props.data.title : props.field.title,
    'archive': null,
}
const form = useForm(formData)
const upload = (e) => {
    form.archive = e.files[0]
    form.post(route(props.field.action.url, props.field.action.addon), {
        preserveScroll:true,
        forceFormData: true,
        onSuccess: () => {
            changeShows([true, false])
        }
    })
}
const changeShows = (items) => {
    showItem.value = items[0]
    showUpload.value = items[1]
}
</script>
<template>
    <Toast />
    <div class="col-span-full">
        <div class="w-full">
            <template v-if="showItem">
                <UploadItem 
                :image="props.data" 
                @updateShows="changeShows"
                :showSubtypeSelect="false"
                />
            </template>
            <template v-if="showUpload">
                <FileUpload 
                    :name="keyValue" 
                    :customUpload="true"
                    @uploader="upload"
                    :multiple="false" 
                    accept="image/*" 
                    :maxFileSize="100000000" 
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
            </template>
        </div>
    </div>

</template>
