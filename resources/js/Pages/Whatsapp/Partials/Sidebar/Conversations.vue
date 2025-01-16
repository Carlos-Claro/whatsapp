<script setup>
import { Badge, Button, Divider } from 'primevue';
import { onMounted, ref, watch } from 'vue';

const props = defineProps({
    conversations: Object,
    selectConversation: Function,
    activeIdConversation: Number,
})
console.log(props.conversations)

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
            <div :class="`grid grid-flow-col grid-cols-6 p-4  ${item.id == props.activeIdConversation ? 'bg-[#2a3942]' : 'bg-[#111b21]'}`" @click="emit('update:selectConversation', item)">
                    <span class="pi pi-user col-span-1" style="font-size: 1.5rem;"></span>
                    <div class="text-center  sm:text-left col-span-4">
                        <p class="text-xs text-white font-semibold">
                            {{ item.contact.name }}
                        </p>
                        <p class="text-[11px] text-gray-300">
                            {{ item.lastMessage.body.substring(0, 12) }} ...
                        </p>
                    </div>
                    <div class="w-fit col-span-1 text-right" v-if="item.unReadMessages">
                        <Badge  :value="item.unReadMessages" severity="contrast" class="mr-1" size="small"></Badge>
                    </div>
                </div>

            </div>
        </div>
        <div class="grid grid-flow-row gap-1 absolute inset-x-0 bottom-0">
                <Button type="button" label="Abertas" @click="updateConversations('open')" :variant="activeButton == 'open' ? 'outlined' : 'secondary'" />
                <Button type="button" label="Fechadas"  @click="updateConversations('closed')" :variant="activeButton == 'closed' ? 'outlined' : 'secondary'" />
                <Button type="button" label="Não atendidas" @click="updateConversations('nobody')" :variant="activeButton == 'nobody' ? 'outlined' : 'secondary'" />
            </div>
    </div>
</template>
