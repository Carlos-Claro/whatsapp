<script setup>
import { useFetch, useScroll } from '@vueuse/core';
import { Button, ConfirmDialog, InputText, Menu, Toast, useConfirm, useToast } from 'primevue';
import { computed, onMounted, onUpdated, reactive, ref, toRefs, watch, watchEffect } from 'vue';
import Message from './Message.vue';
import Send from './Send.vue';
import MenuTop from './Conversation/MenuTop.vue';


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
        assigned()
        execute()
    }
})
const updateConversation = () => {
    execute()
}
const conversationElement = ref(null)
const { x, y, isScrolling, arrivedState, directions } = useScroll(conversationElement, { smooth: true })
watch(isFinished, (value) => {
    y.value = Number.parseFloat(conversationElement.value.scrollHeight)
})
watchEffect(() => {
    if (props.conversation){

        assigned()
    }
})
const assigned = () => {
    console.log('assigned')
    console.log(props.conversation.id)


    window.Echo.channel(`conversation.${props.conversation.id}`)
        .listen('WhatsappNewMessage', (e) => {
            console.log('e', e);
            if ( props.conversation.id == e.conversation.id ){
                updateConversation()
            }
        })

}


</script>
<template>

    <Toast />
    <template v-if="props.conversation">
        <div class="bg-[#202c33] p-4 w-full row-span-1 self-start">
            <div class="flex flex-row">
                <span class="pi pi-user w-16" style="font-size: 2.5rem;"></span>
                <div class="text-center grow">
                    <p class="text-lg text-left text-white font-sans">
                        {{ props.conversation.id }} - {{  props.conversation.contact.name  }}
                    </p>
                </div>
                <div class="">
                    <MenuTop :id="props.conversation.id" />
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
            v-if="props.conversation.status == 0"
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
