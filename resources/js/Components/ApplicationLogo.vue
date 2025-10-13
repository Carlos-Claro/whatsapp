<script setup>
defineProps({
    logo: {
        type: String,
    },
});
</script>
<template>
    <img :src="logo" alt="Logo" class="block h-9 w-auto" />
</template>
