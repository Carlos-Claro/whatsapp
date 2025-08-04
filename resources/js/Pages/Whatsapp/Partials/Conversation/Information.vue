<script setup>
import { useFetch } from '@vueuse/core';
import { computed, reactive, ref, watch, watchEffect } from 'vue';

const props = defineProps({
    conversation_id: {
        type: Number,
    },
    activeInformation: {
        type: Boolean,
        default: false,
    },
})
const url = ref('')
const { execute, isFetching, data, onFetchError, onFetchResponse, isFinished} = useFetch(
    url, {
        refetch:false,
        immediate:false
    }).get().json()
let conversation = reactive({
    isFetching,
    data: computed(() => {
        try{
            return data.value
        }catch(e){
            return null
        }
    })
})
watch(isFinished, (value) => {
    if (value) {
        console.log(conversation.data)

    }
})
watchEffect(() => {
    if (props.activeInformation) {
        url.value = route('get_conversation', {id: props.conversation_id})
        execute()
    }
});
</script>
<template>
    <div class="flex flex-col gap-2 p-2">
        <div v-if="conversation.data?.status">
            <h3>Conversa Finalizada</h3>
            <p v-if="conversation.data?.rate_annotation">Satisfação: {{ conversation.data?.rate_annotation }}</p>
        </div>
        <div v-else>
            <h3>Conversa Aberta</h3>
        </div>
        <div v-if="conversation.data?.company">
            <h3>Informações da empresa:</h3>
            <img v-if="conversation.data?.company?.logo_address" :src="conversation.data?.company?.logo_address" alt="Logo" class="" />
            <p>ID: {{ conversation.data?.company?.id }}</p>
            <p>Nome: {{ conversation.data?.company?.empresa_nome_fantasia }}</p>
        </div>
        <div v-if="conversation.data?.contact">
            <h3>Informações do contato:</h3>
            <p>ID: {{ conversation.data?.contact?.contact.id }}</p>
            <p>Nome: {{ conversation.data?.contact?.contact.name }}</p>
            <p>Telefone: {{ conversation.data?.contact?.contact.phone }}</p>
        </div>
        <div v-if="conversation.data?.user">
            <h3>Informações do atendente:</h3>
            <p>ID: {{ conversation.data?.user?.user.id }}</p>
            <p>Nome: {{ conversation.data?.user?.user.name }}</p>
        </div>
        <div>
            <h3>Informações da conversa:</h3>
            <p>Iniciada em: {{ conversation.data?.created_at }}</p>
            <p>Última mensagem em: {{ conversation.data?.last_message?.created_at }}</p>
        </div>

    </div>
</template>
<style lang="css" scoped>
    div {
        @apply
            bg-white
            dark:bg-gray-800
            shadow-md
            rounded-lg
            p-4
            border
            border-gray-300
            dark:border-gray-700;
    }
    div > h3 {
        @apply
            text-lg
            font-semibold
            text-gray-800
            dark:text-gray-200;
    }
</style>
