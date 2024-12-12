<script setup>
import Conversation from '@/Pages/Whatsapp/Partials/Conversation.vue';
import Conversations from '@/Pages/Whatsapp/Partials/Sidebar/Conversations.vue';
import Options from '@/Pages/Whatsapp/Partials/Sidebar/Options.vue';
import { Badge, Button, Divider, InputText, Menu } from 'primevue';
import { ref } from 'vue';

const props = defineProps([])

</script>
<template>
    <main class="w-screen h-screen doc text-slate-50 flex flex-row">
        <slot></slot>
    </main>
</template>
<style scoped>
.doc {
    background-color: #0c1317;
}
</style>
