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
console.log(props.conversation);
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
        :class="`grid grid-flow-col grid-cols-6 p-4  ${data.id == props.activeIdConversation ? 'bg-[#2a3942]' : 'bg-[#111b21]'}`"
        @click="clickConversation"
        >
            <span class="pi pi-user col-span-1" style="font-size: 1.5rem;"></span>
            <div class="text-center  sm:text-left col-span-4">
                <p class="text-xs text-white font-semibold">
                    {{ data.contact.name }}
                </p>
                <p class="text-[11px] text-gray-300">
                    {{ data.lastMessage.body.substring(0, 12) }} ...
                </p>
            </div>
            <div class="w-fit col-span-1 text-right" v-if="data.unReadMessages">
                <Badge  :value="data.unReadMessages" severity="contrast" class="mr-1" size="small"></Badge>
            </div>
        </div>


</template>
