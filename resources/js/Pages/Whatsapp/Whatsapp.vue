<script setup>

import WhatsappLayout from '@/Layouts/WhatsappLayout.vue';
import { Head } from '@inertiajs/vue3';
import Options from './Partials/Sidebar/Options.vue';
import Conversations from './Partials/Sidebar/Conversations.vue';
import Conversation from './Partials/Conversation.vue';


</script>
<template>
    <Head title="Whatsapp (3)" />
    <WhatsappLayout >
        <div class="sidebar basis-1/4">
            <div class="flex flex-row h-full">
                <Options />
                <Conversations />
            </div>
        </div>
        <div class="basis-3/4 grid grid-flow-row grid-rows-12">
            <Conversation />
        </div>
    </WhatsappLayout>
</template>
