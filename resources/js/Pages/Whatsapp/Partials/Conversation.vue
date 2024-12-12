<script setup>
import { Button, InputText, Menu } from 'primevue';
import { ref } from 'vue';
const menu = ref()
const itemsMenu = ref([
    {
        label: 'Anexos',
        items: [
            {
                label: 'Imagem',
                icon: 'pi pi-images',
            },
            {
                label: 'Document',
                icon: 'pi pi-book',
            },
        ],
    },
])
const toggle = (event) => {
    menu.value.toggle(event);
};
</script>
<template>
    <div class="bg-teal-900 p-4 w-full row-span-1 self-start">
        <div class="flex flex-row">
            <span class="pi pi-user w-16" style="font-size: 2.5rem;"></span>
            <div class="text-center grow">
                <p class="text-lg text-left text-white font-sans">
                    Nome
                </p>
            </div>
        </div>
    </div>
    <div class=" bg-gray-800 row-span-10 h-full self-center overflow-y-scroll scroll-smooth">
        <div class="flex flex-col place-content-end">
            <template v-for="n in 25" >
                <div :class="`box-conversa ${n%2 == 0 ? 'place-items-start' : 'place-items-end'} `">
                    <div :class="`flex  flex-col text-right text-black h-auto w-fit px-4 py-2 my-2 mx-1 ${n%2 == 0 ? 'bg-green-300' : 'bg-green-200'}`"   >
                        <p :class="`font-base ${n%2 == 0 ? 'text-left' : 'text-right'}`">
                            Olá, tudo bem? blablabla bla bla bla, blablabla {{ n }}
                        </p>
                        <div :class="`flex flex-row gap-2 ${n%2 == 0 ? 'place-content-start' : 'place-content-end'}`">
                            <p class="text-gray-600 font-xs ">Hoje, 10:{{ n }}</p>
                            <p class="text-pink-700 font-xs ">lido</p>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
    <div class=" bg-teal-800 w-full row-span-1 h-full self-end">
        <div class="flex gap-1">
            <InputText placeholder="Digite sua mensagem" type="text" class="grow mx-2 my-1" />
            <Button type="button" icon="pi pi-paperclip" @click="toggle" aria-haspopup="true" aria-controls="overlay_menu" class="mx-2 my-1" />
            <Menu ref="menu" id="overlay_menu" :model="itemsMenu" :popup="true" />
        </div>
    </div>
</template>
