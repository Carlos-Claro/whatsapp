<script setup>
import { Badge } from 'primevue';
import { computed, reactive, ref, watch } from 'vue';

const props = defineProps({
    conversation: Object,
    selectConversation: Function,
    activeIdConversation: Number,
})
const model = defineModel('selectConversation')
const emit = defineEmits(['update.selectConversation'])
const clickConversation = () => {
    emit('update:selectConversation', props.conversation)
}
window.Echo.channel(`conversation_received.${props.conversation.id}`).listen('WhatsappReceived', (e) => {
    if ( props.conversation.id == e.conversation.id ){
        data.value = e.conversation
    }
})
let data = ref(props.conversation)

</script>
<template>
    <div
        :class="`relative grid grid-flow-col grid-cols-6 px-2 py-6  ${data.id == props.activeIdConversation ? 'bg-[#2a3942]' : 'bg-[#111b21]'} transition-colors`"
        @click="clickConversation"
        >
            <span class="pi pi-user col-span-1" style="font-size: 1.5rem;"></span>
            <div class="text-center  sm:text-left col-span-4 w-fit">
                <p class="text-xs text-white font-semibold">
                    {{ data.contact.name }}
                </p>
                <p class="text-[11px] text-gray-300">
                    {{ data.lastMessage?.body.substring(0, 30) }} ...
                </p>
            </div>
            <p class="text-[8px] text-gray-300 text-right col-span-1">
                {{ data.lastMessage?.created_at }}
            </p>
            <div class="w-fit col-span-1 text-right mx-2" v-if="data.unReadMessages">
                <Badge  :value="data.unReadMessages" severity="contrast" class="mr-1" size="small"></Badge>
            </div>
        </div>


</template>
