<script setup>

const props = defineProps({
    item: Object,
})
// console.log(window.Echo);
Echo.private(`messages.${props.item.id}`)
    .listen('WhatsappDelivered', (e) => {
        console.log(e)
    })

</script>
<template>
    <div :class="`box-conversa ${(props.item.memberable_type).indexOf('Contacts') >= 0 ? 'place-items-start' : 'place-items-end'} `" :ref="props.item.id">
        <div :class="`flex  flex-col text-right text-black h-auto w-fit px-4 py-2 my-2 mx-1 ${(props.item.memberable_type).indexOf('Contacts') >= 0 ? 'bg-green-300' : 'bg-green-200'}`"   >
            <p :class="`font-base ${(props.item.memberable_type).indexOf('Contacts') >= 0 ? 'text-left' : 'text-right'}`">
                {{ props.item.body }}
            </p>
            <div :class="`flex flex-row gap-2 ${(props.item.memberable_type).indexOf('Contacts') >= 0 ? 'place-content-start' : 'place-content-end'}`">
                <p class="text-gray-600 font-xs ">{{ props.item.created_at }}</p>
                <p class="text-pink-700 font-xs ">{{ props.item.status }}</p>
            </div>
        </div>
    </div>
</template>
