<script setup>
import { useFetch, useScroll } from '@vueuse/core';
import { Button, InputText, Menu, Toast, useToast } from 'primevue';
import { computed, onMounted, onUpdated, reactive, ref, toRefs, watch, watchEffect } from 'vue';
import Message from './Message.vue';
import Send from './Send.vue';


const props = defineProps({
    conversation: {
        type: Object,
        default: 0,
    },
})
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
let messages = reactive({
    isFetching,
    data: computed(() => {
        try{
            return data.value
        }catch(e){
            return null
        }
    })
})
watchEffect(() => {
    if ( props.conversation ){
        url.value = route('get_messages', {'id': props.conversation.id})
        execute()
    }
})
onMounted(() => {
    if ( props.conversation ){
        url.value = route('get_messages', {'id': props.conversation.id})
        execute()
    }
})
const updateConversation = () => {
    execute()
}
const conversationElement = ref(null)
const { x, y, isScrolling, arrivedState, directions } = useScroll(conversationElement, { smooth: true })
watch(isFinished, (value) => {
    console.log(conversationElement)
    y.value = Number.parseFloat(conversationElement.value.scrollHeight)

})

</script>
<template>
    <Toast />
    <template v-if="props.conversation">
        <div class="bg-[#202c33] p-4 w-full row-span-1 self-start">
            <div class="flex flex-row">
                <span class="pi pi-user w-16" style="font-size: 2.5rem;"></span>
                <div class="text-center grow">
                    <p class="text-lg text-left text-white font-sans">
                        {{  props.conversation.contact.name  }}
                    </p>
                </div>
            </div>
        </div>
        <div class=" bg-[#0b141a] row-span-10 h-full self-center overflow-y-scroll scroll-smooth" ref="conversationElement">
            <div class="flex flex-col place-content-end" >
                <template v-for="item in messages.data" v-if="messages.data != null" ref="isFinished">
                    <Message :item="item" />
                </template>
                <template v-else>
                    <Message :item="props.conversation.lastMessage" />
                </template>
            </div>
        </div>
        <Send
            :conversation="props.conversation"
            @update:update-conversation="updateConversation"
            />
    </template>
    <template v-else>
        <div class="bg-teal-900 p-4 w-full row-span-1 self-start">
            <div class="flex flex-row">
                <!-- <span class="pi pi-user w-16" style="font-size: 2.5rem;"></span> -->
                <div class="text-center grow">
                    <p class="text-lg text-left text-white font-sans">
                        Nenhuma mensagem carregada.
                    </p>
                </div>
            </div>
        </div>
        <div class=" bg-gray-800 row-span-10 h-full self-center overflow-y-scroll scroll-smooth">
            <div class="flex flex-col place-content-end">
            </div>
        </div>
    </template>

</template>
