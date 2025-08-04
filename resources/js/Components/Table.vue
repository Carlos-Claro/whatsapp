<script setup>
import Paginate from "@/Components/Paginate.vue";
import Image from "./Image.vue";
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import { ref, watch } from "vue";

const props = defineProps({
    list: {
        type: [Array, Object],
        required: true,
    },
    items: {
        type: [Array, Object],
        required: true,
    },
    selected: Array,
    paginate: {
        type: Boolean,
        default: true,
    },
    });
    
console.log(props.items)
const selected = defineModel(['selected'])
const emit = defineEmits(['update:selected'])
watch(selected, (newS) => {
    emit('update:selected', newS)
})
const data = props.items.data ? props.items.data : props.items;
const setData = (item, index) => {
    if ( index.indexOf('.') >= 0 ){
        var i = index.split('.')
        switch (i.length) {
            case 2:
                return item[i[0]][i[1]]
                break;
            case 3:
                return item[i[0]][i[1]][i[2]]
                break;
            case 4:
                return item[i[0]][i[1]][i[2]][i[3]]
                break;
            case 5:
                return item[i[0]][i[1]][i[2]][i[3]][i[4]]
                break;
            default:
                return item[index]
                break;
        }
    }
    return item[index]
}

</script>
<template>
    <DataTable :value="data" dataKey="id" v-model:selection="selected" >
        <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
        <template v-for="item, key in props.list.data">
            <template v-if="item.type[1] == 'image'">
                <Column :field="key" :header="item.title">
                    <template #body="slotProps">
                        <Image :image="setData(slotProps.data ,slotProps.field)" />
                    </template>
                </Column>
            </template>
            <template v-else>
                <Column :field="key" :header="item.title"></Column>
            </template>
        </template>
        <template v-if="props.list.listActions">
            <Column field="action" header="">
                <template #body="slotProps">
                    <template v-for="action in props.list.listActions">
                        <a :method="action.method" :href="route(action.route, slotProps.data[props.list.key])" class="p-2">
                            <Button :icon="action.icon" :aria-label="action.title" severity="primary" outlined />
                        </a>
                    </template>
                </template>
            </Column>
        </template>
        <template #footer v-if="props.paginate">
            <Paginate :links="props.items" />
        </template>
    </DataTable>    
</template>
