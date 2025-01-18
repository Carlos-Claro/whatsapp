<script setup>
import { useForm } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

const form = useForm({
    id: '',
    wam_id: '',
    status: '',
})
const props = defineProps({
    item: Object,
})
// console.log(window.Echo);
window.Echo.channel(`messages.${props.item.id}`)
    .listen('WhatsappDelivered', (e) => {
        if ( props.item.id == e.message.id ){
            form.status = e.message.status
        }
    })

onMounted(() => {
    form.status = props.item.status
    if ( (props.item.memberable_type) && (props.item.memberable_type).indexOf('Contacts') >= 0 && props.item.status == 'delivered' ){
        form.id = props.item.id
        form.wam_id = props.item.wam_id
        form.post(route('mark_message_as_read'), {
            onSuccess: () => {
                form.status = 'read'
            },
            onError: () => {
                console.log('Erro ao entregar mensagem')
            },
        })
    }
})
const messageSide = computed(() => {
    return (props.item.memberable_type) && (props.item.memberable_type).indexOf('Contacts') >= 0 ? true : false
})
// console.log(props.item);

</script>
<template>
    <div :class="`box-conversa ${messageSide ? 'place-items-start' : 'place-items-end'} `" :ref="props.item.id">
        <div :class="`flex flex-col rounded text-right text-white h-auto w-1/2 px-4 py-2 my-2 mx-1 ${messageSide ? 'bg-[#202c33]' : 'bg-[#005c4b]'}`"   >
            <div
                :class="`flex flex-col rounded text-right text-white h-auto w-5/6 px-4 py-2 my-2 mx-1 border-l-4 ${messageSide ? 'bg-[#1d282f] border-l-[#e26ab6]' : 'bg-[#025144] border-l-[#a5b337]'}`"
                v-if="props.item.related">
                {{ props.item.related.body }}
            </div>
            <p :class="`font-base ${messageSide ? 'text-left' : 'text-right'}`">
                {{ props.item.body }}
            </p>
            <div :class="`flex flex-row gap-2 place-content-end`">
                <p class="text-gray-300 text-[12px] "><small>{{ props.item.created_at }}</small></p>
                <p class="text-pink-200 text-[12px] " v-if="(props.item.memberable_type) && (props.item.memberable_type).indexOf('Contacts') < 0 ">{{ form.status }}</p>
            </div>
        </div>
    </div>
</template>
