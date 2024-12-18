<script setup>

import WhatsappLayout from '@/Layouts/WhatsappLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import Options from './Partials/Sidebar/Options.vue';
import Conversations from './Partials/Sidebar/Conversations.vue';
import Conversation from './Partials/Conversation.vue';
import { ref } from 'vue';

const page = usePage()
const activeIdConversation = ref(null)
const selectConversation = (id) => {
    console.log(id)

    activeIdConversation.value = id
}
</script>
<template>
    <Head title="Whatsapp (3)" />
    <WhatsappLayout >
        <div class="sidebar basis-1/4">
            <div class="flex flex-row h-full">
                <Options />
                <Conversations
                    :conversations="page.props.conversations"
                    @update:selectConversation="selectConversation"
                    />
            </div>
        </div>
        <div class="basis-3/4 grid grid-flow-row grid-rows-12">
            <Conversation />
        </div>
    </WhatsappLayout>
</template>
