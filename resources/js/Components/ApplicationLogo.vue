<script setup>
import { usePage } from '@inertiajs/vue3';
const page = usePage();
</script>
<template>
    <img :src="page.props?.init.logo" alt="Logo" class="block h-9 w-auto" />
</template>
