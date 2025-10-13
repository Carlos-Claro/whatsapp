<script setup>

import WhatsappLayout from '@/Layouts/WhatsappLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import Options from './Partials/Sidebar/Options.vue';
import Conversations from './Partials/Sidebar/Conversations.vue';
import Conversation from './Partials/Conversation.vue';
import { computed, onMounted, reactive, ref, watch } from 'vue';
import { Toast, useToast } from 'primevue';
import { useFetch } from '@vueuse/core';

const page = usePage()
defineProps({
    logo: {
        type: String,
    },
});


const activeIdConversation = ref(null)
const selectConversation = (item) => {
    // console.log(item)
    activeIdConversation.value = item
}

const toast = useToast()
const url = ref('')
const { execute, isFetching, data, onFetchError, onFetchResponse, isFinished} = useFetch(
    url, {
        refetch:false,
        immediate:false
    }).get().json()
onFetchResponse((e) => {
    // console.log(e)
    // toast.add({ severity: 'success', summary: 'Status de fetch: ' + e.status, 'detail' : 'Mensagens retornadas com sucesso.', life: 3000})

})
onFetchError((e) => {
    // console.log(e)
    // toast.add({ severity: 'danger', summary: 'Status de fetch: ' + e.status, 'detail' : 'Erro, verifique.', life: 3000})
})
let conversations = reactive({
    isFetching,
    data: computed(() => {

        try{
            return data.value
        }catch(e){
            return null
        }
    })
})
onMounted(() => {
    url.value = route('get_resume')
    execute()
})
watch(isFinished, (value) => {
    console.log(value)
    if ( value ){
        console.log(conversations.data)

    }
})
</script>
<template>
    <Toast/>
    <Head title="(3) Whatsapp" />
    <WhatsappLayout >
        <div class="sidebar basis-1/4">
            <div class="flex flex-row h-full">
                <Options :logo="logo" />
                <Conversations
                    :conversations="conversations.data"
                    @update:selectConversation="selectConversation"
                    :activeIdConversation="activeIdConversation ? activeIdConversation.id : null"
                    />
            </div>
        </div>
        <div class="basis-3/4 grid grid-flow-row grid-rows-12">
            <Conversation :conversation="activeIdConversation" />
        </div>
    </WhatsappLayout>
</template>
