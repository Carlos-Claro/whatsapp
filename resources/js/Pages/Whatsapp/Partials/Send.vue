<script setup>
import { useForm } from '@inertiajs/vue3';
import { Button, InputText, Menu, Textarea } from 'primevue';
import { ref, watchEffect } from 'vue';
const props = defineProps({
    conversation: {
        type: Object,
        default: 0,
    },
})
const model = defineModel('updateConversation')
const emit = defineEmits(['update:updateConversation'])
const menu = ref()
const itemsMenu = ref([
    {
        label: 'Anexos',
        items: [
            {
                label: 'Imagem',
                icon: 'pi pi-images',
            },
            {
                label: 'Document',
                icon: 'pi pi-book',
            },
        ],
    },
])
const toogle = (event) => {
    menu.value.toggle(event);
};
const lastestKey = ref(null)
const inputChange = (event) => {
    if (event.keyCode == 13 && lastestKey.value !== 16){
        submit()
    }
    lastestKey.value = event.keyCode
}
const submit = () => {
    if ( form.message.length > 0 ){
        form.post(route('send_message'), {
            onSuccess: () => {
                form.message = ''
                emit('update:updateConversation')
            },
            onError: () => {
                console.log('erro')
            },
        })
    }
}
const form = useForm({
    message: '',
    contact_id: '',
    conversation_id: '',
})
watchEffect(() => {
    if( props.conversation ){
        form.contact_id = props.conversation.contact.id
        form.conversation_id = props.conversation.id
    }
})
</script>
<template>
    <div class=" bg-[#202c33] py-2 w-full row-span-1 h-full self-end">
        <div class="flex gap-1">
            <Button
                type="button"
                icon="pi pi-plus"
                @click="toogle"
                aria-haspopup="true"
                aria-controls="overlay_menu"
                class="ml-3 my-1"
                variant="text"
                />
            <Menu
                ref="menu"
                id="overlay_menu"
                :model="itemsMenu"
                :popup="true"
                />
            <Textarea
                placeholder="Digite sua mensagem"
                type="text"
                class="grow mx-2 my-1 h-12"
                fluid
                v-model="form.message"
                v-on:keydown="inputChange"
                variant="outlined"
                />
        </div>
    </div>
</template>
