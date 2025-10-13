<script setup>
import { ref } from 'vue';
defineProps({
    logo: {
        type: String,
    },
});
const menus = [
    {
        title: 'Voltar ao Dashboard',
        route: 'dashboard',
        window: '_self',
        icon: 'pi pi-arrow-left',
    },
    {
        title: 'Iniciar uma conversa',
        route: false,
        action: '',
        icon: 'pi pi-plus',
    },
    {
        title: 'Conversas',
        route: false,
        action:'',
        icon: 'pi pi-tags',
    },
    {
        title: 'Contatos',
        route: false,
        action:'',
        icon: 'pi pi-id-card',
    },
    {
        title: 'Configurações',
        route: false,
        action:'',
        icon: 'pi pi-cog',
    },
]
</script>
<template>
    <div class="w-fit bg-teal-900 p-3 shadow-sm shadow-black rounded-l-lg flex flex-col gap-1">

        <template v-for="menu in menus" >
            <a
                v-if="menu.route"
                class="rounded-full bg-teal-800 p-3 mt-1"
                v-tooltip="menu.title"
                :href="route(menu.route)"
                >
                <i :class="`pi ${menu.icon} m-2`"></i>
            </a>
            <a
                v-else
                class="rounded-full bg-teal-800 p-3 mt-1"
                v-tooltip="menu.title"
                @click="menu.action"
                type="button"
                >
                <i :class="`pi ${menu.icon} m-2`"></i>
            </a>
        </template>

        <a href="/">
            <img :src="logo" class="w-10 h-10 mx-auto mb-2 absolute bottom-5 " />
        </a>
    </div>
    <div class="bottom">
    </div>
</template>
