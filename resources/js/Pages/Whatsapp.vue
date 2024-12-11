<script setup>

import WhatsappLayout from '@/Layouts/WhatsappLayout.vue';
import { Head } from '@inertiajs/vue3';

</script>
<template>
    <Head title="Whatsapp" />
    <WhatsappLayout >
        <template #sidebar>
            teste
        </template>
        <template #conversation>
            conversation
        </template>
    </WhatsappLayout>
</template>
