<script setup>
import { Badge, Button, Divider } from 'primevue';
import { onMounted, ref, watch } from 'vue';
import ConversationTitle from './ConversationTitle.vue';

const props = defineProps({
    conversations: Object,
    selectConversation: Function,
    activeIdConversation: Number,
})
// console.log(props.conversations)

const model = defineModel('selectConversation')
const emit = defineEmits(['update.selectConversation'])
const activeConversations = ref(null)
const activeButton = ref('open')
const updateConversations = (item) => {
    activeButton.value = item
    activeConversations.value = props.conversations[item]
}
onMounted(() => {
    activeConversations.value = props.conversations.open
})
const clickConversation = (talk) => {
    emit('update:selectConversation', talk)
}
</script>
<template>
    <div class="w-full bg-[#111b21] relative">
        <div class="w-full pb-1">
            <p class="text-2xl pl-2 pt-2 mt-2 font-extrabold">Conversas</p>
        </div>
        <Divider />
        <div class="grid grid-flow-row">
            <div
                v-if="activeConversations"
                v-for="item in activeConversations.items"
                class=" w-full border-b border-[#2a3942] "
                :ref="item.id"
                 >
                 <ConversationTitle
                    :conversation="item"
                    @update:selectConversation="clickConversation"
                    :activeIdConversation="activeIdConversation"
                    />
            </div>
        </div>
        <div class="grid grid-flow-row gap-1 absolute inset-x-0 bottom-0">
                <Button type="button" label="Abertas" @click="updateConversations('open')" :variant="activeButton == 'open' ? 'outlined' : 'secondary'" />
                <Button type="button" label="Fechadas"  @click="updateConversations('closed')" :variant="activeButton == 'closed' ? 'outlined' : 'secondary'" />
                <Button type="button" label="Não atendidas" @click="updateConversations('nobody')" :variant="activeButton == 'nobody' ? 'outlined' : 'secondary'" />
            </div>
    </div>
</template>
