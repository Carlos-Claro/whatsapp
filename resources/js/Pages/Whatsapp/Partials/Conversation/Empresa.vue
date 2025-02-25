<script setup>
import { useFetch } from '@vueuse/core';
import { AutoComplete, Button } from 'primevue';
import { computed, reactive, ref, watch } from 'vue';

const props = defineProps({

})
const emit = defineEmits(['update:visible'])
const empresa = ref("")
const searchEmpresas = (e) => {
    url.value = route('search_empresa', {search: e.query})
    console.log(e)
    execute()

}
const url = ref('')
const { execute, isFetching, data, onFetchError, onFetchResponse, isFinished} = useFetch(
    url, {
        refetch:false,
        immediate:false
    }).get().json()
let filteredEmpresas = reactive({
    isFetching,
    data: computed(() => {
        try{
            return data.value
        }catch(e){
            return null
        }
    })
})
watch(isFinished, (value) => {
    console.log(value)
    if ( value ){
        console.log(filteredEmpresas.data)

    }
})
watch(empresa, (value) => {
    console.log(value)
})
</script>
<template>

    <div class="card flex justify-center">
        <AutoComplete
                v-model="empresa"
                :suggestions="filteredEmpresas.data"
                field="Pesquise"
                @complete="searchEmpresas"
                placeholder="Pesquisar empresas"
                >
            <template #option="slotProps">
                <div class="p-clearfix">
                    <div>{{slotProps.option.name}}</div>
                </div>
            </template>
        </AutoComplete>
    </div>
    <Button @click="emit('update:visible', false)">Fechar</Button>

</template>
