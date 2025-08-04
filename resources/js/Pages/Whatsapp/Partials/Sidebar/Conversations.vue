<script setup>
import { Badge, Button, Divider } from 'primevue';
import { onMounted, ref, watch, watchEffect } from 'vue';
import ConversationTitle from './ConversationTitle.vue';

const props = defineProps({
    conversations: Object|null,
    selectConversation: Function,
    activeIdConversation: Number,
})
// console.log(props.conversations)
const buttons = {
    'open': 'Abertas',
    'all': 'Abertas Outras',
    'closed': 'Fechadas',
    'nobody': 'Não atendidas',
}

const model = defineModel('selectConversation')
const emit = defineEmits(['update.selectConversation'])
const activeConversations = ref(null)
const activeButton = ref('open')
const updateConversations = (item) => {
    activeButton.value = item
    activeConversations.value = null
    setTimeout(() => {
        activeConversations.value = props.conversations[item]
    }, 100)
    // activeConversations.value = props.conversations[item]
}
onMounted(() => {
    if ( props.conversations ){
        activeConversations.value = props.conversations.open
    }
})
watchEffect(() => {
    if ( props.conversations ){
        activeConversations.value = props.conversations.open
    }
})
const clickConversation = (talk) => {
    emit('update:selectConversation', talk)
}
</script>
<template>
    <div class="w-full bg-[#111b21] relative flex flex-col h-screen">
        <div class="w-full pb-1">
            <p class="text-2xl pl-2 pt-2 mt-2 font-extrabold"> Conversas {{ buttons[activeButton]  }}</p>
        </div>
        <Divider />
        <div class="flex flex-col flex-1 min-h-0 overflow-hidden">
            <div class="flex-1 overflow-y-auto border-b">
                <div
                    v-if="activeConversations && activeConversations.items.length > 0"
                    v-for="item in activeConversations.items"
                    class=" w-full border-b border-[#2a3942] space-y-2"
                    :ref="item.id"
                     >
                     <ConversationTitle
                        :conversation="item"
                        @update:selectConversation="clickConversation"
                        :activeIdConversation="activeIdConversation"
                        />
                </div>
                <div v-else>
                    <div class="flex flex-col place-content-start">
                        <div class="w-full h-full flex items-center justify-center">
                            <p class="text-sm text-white">Nenhuma conversa encontrada</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid grid-flow-row gap-1 relative inset-x-0 bottom-0">
                <Button type="button" label="Abertas" @click="updateConversations('open')" :variant="activeButton == 'open' ? 'outlined' : 'secondary'" class="transition-colors"/>
                <Button type="button" label="Abertas Outras" @click="updateConversations('all')" :variant="activeButton == 'all' ? 'outlined' : 'secondary'" class="transition-colors"/>
                <Button type="button" label="Fechadas"  @click="updateConversations('closed')" :variant="activeButton == 'closed' ? 'outlined' : 'secondary'" class="transition-colors"/>
                <Button type="button" label="Não atendidas" @click="updateConversations('nobody')" :variant="activeButton == 'nobody' ? 'outlined' : 'secondary'" class="transition-colors"/>
            </div>
    </div>
</template>
