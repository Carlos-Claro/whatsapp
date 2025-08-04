<script setup>
import { useForm } from '@inertiajs/vue3';
import { Badge } from 'primevue';
import { computed, onMounted, ref } from 'vue';

const form = useForm({
    id: '',
    wam_id: '',
    status: '',
})
const props = defineProps({
    item: Object,
})
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
const primaryType = (type) => {
    if ( type == 'text' ||
         type == 'welcome' ||
         type == 'finish' ||
         type == 'rate' ){
        return 'text'
    }
    return type
}
const messageType = computed(() => {
    return [primaryType(props.item.type), props.item.type]
})
const parseJson = (key) => {
    let data = JSON.parse(props.item.data)
    return data[key]
}
console.log(props.item.data)

</script>
<template>
    <div :class="`box-conversa ${messageSide ? 'place-items-start' : 'place-items-end'} `" :ref="props.item.id" v-if="messageType[0] !== 'reaction'">
        <div :class="`flex flex-col rounded text-right text-white h-auto w-1/2 px-4 py-2 my-2 mx-1 ${messageSide ? 'bg-[#202c33]' : 'bg-[#005c4b]'}`" >
            <div
                :class="`flex flex-col rounded text-right text-white h-auto w-5/6 px-4 py-2 my-2 mx-1 border-l-4 ${messageSide ? 'bg-[#1d282f] border-l-[#e26ab6]' : 'bg-[#025144] border-l-[#a5b337]'}`"
                v-if="props.item.related">
                {{ props.item.related.body }}
            </div>
            <p
                v-if="messageType[0] == 'text'"
                :class="`font-base ${messageSide ? 'text-left' : 'text-right'}`">
                {{ props.item.body }}
            </p>
            <div
                v-if="messageType[0] == 'button'"
                :class="`font-base ${messageSide ? 'text-left' : 'text-right'}`">
                <p>{{ props.item.body }}</p>
                <div class="grid gap-1 grid-cols-3">
                    <template v-for="(button, index) in JSON.parse(props.item.data)" :key="index">
                        <div class="border runded-sm border-gray-800  text-center">{{ button.title }}</div>
                    </template>

                </div>
            </div>
            <div
                v-if="messageType[0] == 'contact'"
                :class="`flex flex-col flex flex-col rounded text-right text-white h-auto w-5/6 px-4 py-2 my-2 mx-1 border-l-4 ${messageSide ? 'bg-[#1d282f] border-l-[#e26ab6]' : 'bg-[#025144] border-l-[#a5b337]'} `">
                <p class="bold text-left">Contato: </p>
                <p>
                    {{ parseJson('name') }}
                </p>
                <p>
                    {{ parseJson('phones')[0].phone }}
                </p>
            </div>

            <div
                v-if="messageType[0] == 'location'"
                :class="`flex flex-col flex flex-col rounded text-right text-white h-auto w-5/6 px-4 py-2 my-2 mx-1 border-l-4 ${messageSide ? 'bg-[#1d282f] border-l-[#e26ab6]' : 'bg-[#025144] border-l-[#a5b337]'} `">
                <p class="bold text-left">Localização: </p>
                <p>
                    {{ parseJson('name') }}
                </p>
                <p>
                    {{ parseJson('latitude') }}, {{ parseJson('longitude') }}
                </p>
            </div>

            <div
                v-if="messageType[0] == 'image'"
                >
                <img
                    :src="`${props.item.image_address[1]}`"
                    v-if="props.item.image_address[0].indexOf('image') >= 0"
                    />
                <video
                    v-if="props.item.image_address[0].indexOf('video') >= 0"
                    controls
                    >
                    <source :src="`${props.item.image_address[1]}`" :type="props.item.image_address[0]">
                </video>
                <audio
                    v-if="props.item.image_address[0].indexOf('audio') >= 0"
                    controls>
                    <source :src="`${props.item.image_address[1]}`" :type="props.item.image_address[0]">
                </audio>
                <p
                    :class="`font-base ${messageSide ? 'text-left' : 'text-right'}`">
                    {{ props.item.caption }}
                </p>
            </div>
            <div :class="`flex flex-row gap-2 place-content-end`">
                <p class="text-gray-300 text-[12px] "><small>{{ props.item.created_at }}</small></p>
                <p class="text-pink-200 text-[12px] " v-if="(props.item.memberable_type) && (props.item.memberable_type).indexOf('Contacts') < 0 ">{{ form.status }}</p>
            </div>
        </div>
        <Badge
            v-if="props.item.reaction"
            :value="props.item.reaction.data"
            severity="danger"
            :class="`relative bottom-3 ${messageSide ? ' -right-3' : ' -left-3'}`"
            />
    </div>
</template>
