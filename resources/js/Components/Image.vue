<script setup>
import { useImage } from '@vueuse/core';
import { ref } from 'vue';
import { onMounted } from 'vue';
import Image from 'primevue/image';
import ProgressSpinner from 'primevue/progressspinner';

const props = defineProps({
    image: {
        type: [Array, Object, String],
        required: true,
    },
    class: String,
})
// console.log(props)
let image = ref('')
let alt = ref('')
const { isLoading } = useImage({ src: image })
onMounted(() => {
    if ( props.image ){
        image.value = props.image.get_image_address
        alt.value = props.image.title
    }
})
</script>
<template>
    <span v-if="isLoading"><ProgressSpinner  /> </span>
    <Image v-else :src="image" :imageClass="`${props.class ? props.class : 'w-20 m-2'}`" :alt="alt" preview />
</template>
