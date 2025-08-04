<script setup>
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

import { ref } from 'vue';
import Field from './Field.vue';
import { router } from '@inertiajs/vue3';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from "primevue/useconfirm";
import ConfirmPopup from 'primevue/confirmpopup';

const props = defineProps({
    data: {
        type: [Array, Object],
        required: true,
    },
    item: {
        type: [Object],
    }
});
const toast = useToast()
const confirm = useConfirm()
defineEmits(['update:selected'])
const selected = defineModel('selected')

const editingRows = ref([])
const saveRow = (event) => {
    let {newData, index} = event
    console.log(props.data.list.fields.key, newData, index)
    const credentials = { client: newData[props.data.list.fields.key], contact: newData.id}
    router.visit(route(props.data.list.action, credentials),{ 
        method: props.data.list.method,
        data: newData,
        preserveScroll: true,
        preserveState:true,
        onSuccess: (e) => {
            toast.add({ severity: 'success', summary: 'Ok', 'detail' : 'Item ' + newData.title + ' alterado com sucesso', life: 3000})
        }
    })
}
const deleteRow = (e) => {
    
}
</script>
<template>
    <Toast />
    <ConfirmPopup></ConfirmPopup>
    <DataTable 
        :value="data.list.items" 
        dataKey="id" 
        v-model:selection="selected" 
        v-model:editingRows="editingRows" 
        editMode="row"
        @row-edit-save="saveRow"
        >
        <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
        <Column v-for="fieldItem, key in data.list.fields.data" :field="key" :header="fieldItem.title">
            <template #editor="{ data, field }">
                <Field 
                    :type="fieldItem.type[0]" 
                    v-model="data[field]" 
                    :attr="fieldItem.attrs.attr" 
                    :key-value="key" 
                    :title="fieldItem.title" 
                    :value="fieldItem"
                    :item="props.item"
                    
                />
                <!-- <InputText v-model="data[field]" /> -->
            </template>
        </Column>
        <Column :rowEditor="true" style="width: 10%; min-width: 8rem" bodyStyle="text-align:center"></Column>
    </DataTable>
</template>
