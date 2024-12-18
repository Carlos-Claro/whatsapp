<script setup>
import { Badge, Button, Divider } from 'primevue';
import { onMounted, ref, watch } from 'vue';

const props = defineProps({
    conversations: Object,
    selectConversation: Function,
    activeIdConversation: Number,
})
console.log(props.conversations);

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
    <div class="w-full bg-teal-700">
        <div class="w-full bg-teal-800 pb-1">
            <p class="text-2xl pl-2 pt-2 font-mono">Conversas</p>
            <Divider />
            <div class="grid grid-flow-row gap-1">
                <Button type="button" label="Abertas" :badge="props.conversations.open.items.length" @click="updateConversations('open')" :variant="activeButton == 'open' ? 'outlined' : 'secondary'" />
                <Button type="button" label="Fechadas" :badge="props.conversations.closed.items.length"  @click="updateConversations('closed')" :variant="activeButton == 'closed' ? 'outlined' : 'secondary'" />
                <Button type="button" label="Não atendidas" :badge="props.conversations.nobody.items.length"  @click="updateConversations('nobody')" :variant="activeButton == 'nobody' ? 'outlined' : 'secondary'" />
            </div>
        </div>
        <div class="grid grid-flow-row">
            <div
                v-if="activeConversations"
                v-for="item in activeConversations.items"
                :class="`p-2 shadow-inner shadow-lg w-full border border-slate-500 ${item.id == props.activeIdConversation ? 'bg-slate-200' : 'bg-slate-300'}`"
                :ref="item.id"
                 >
                <div class="grid grid-flow-col grid-cols-6" @click="emit('update:selectConversation', item)">
                    <span class="pi pi-user col-span-1" style="font-size: 2.5rem;"></span>
                    <div class="text-center  sm:text-left col-span-4">
                        <p class="text-sm text-black font-semibold">
                            {{ item.contact.name }}
                        </p>
                        <p class="text-sm text-gray-500">
                            {{ item.lastMessage.body.substring(0, 12) }} ...
                        </p>
                    </div>
                    <div class="w-fit col-span-1 text-right">
                        <Badge value="2" severity="contrast" class="mr-1" size="large"></Badge>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
