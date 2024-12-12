<script setup>
</script>
<template>
    <div class="w-fit bg-teal-900 p-3 shadow-sm shadow-black rounded-l-lg">
        <div class="rounded-full bg-teal-800 p-3 mt-1" v-tooltip="'Iniciar nova conversa'">
            <i class="pi pi-plus m-2"></i>
        </div>
        <div class="rounded-full bg-teal-800 p-3 mt-1" v-tooltip="'Conversas'">
            <i class="pi pi-tags m-2"></i>
        </div>
        <div class="rounded-full bg-teal-800 p-3 mt-1" v-tooltip="'Contatos'">
            <i class="pi pi-id-card m-2"></i>
        </div>
        <div class="rounded-full bg-teal-800 p-3 mt-1" v-tooltip="'Configurações'">
            <i class="pi pi-cog m-2"></i>
        </div>
    </div>
</template>
