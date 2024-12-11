<script setup>
import { Button, Divider } from 'primevue';

const props = defineProps([])
</script>
<template>
    <div class="main w-full h-screen " >
        <main class="text-slate-50">
            <div class="flex flex-row ">
                <div class="sidebar basis-1/4">
                    <div class="flex flex-row">
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
                        <div class="w-full bg-teal-700">
                            <div class="w-full bg-teal-800 pb-1">
                                <p class="text-2xl pl-2 pt-2 font-mono">Conversas</p>
                                <Divider />
                                <div class="grid grid-flow-col gap-1">
                                    <Button type="button" label="Tudo" badge="2" />
                                    <Button type="button" label="Iniciadas" badge="2" />
                                    <Button type="button" label="Fechadas" badge="2" />
                                </div>
                            </div>
                            <div class="grid grid-flow-row">
                                <div v-for="n in 3" :class="`p-2 shadow-inner shadow-lg w-full border border-slate-500 ${ n==1 ? 'bg-slate-200' : 'bg-slate-300'}`"  >
                                    <div class="grid grid-flow-col grid-cols-6">
                                        <span class="pi pi-user col-span-1" style="font-size: 2.5rem;"></span>
                                        <div class="text-center  sm:text-left col-span-5">
                                            <p class="text-sm text-black font-semibold">
                                                Nome
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                Texto da mensagem
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="basis-3/4">
                    <div class="w-full">
                        <div class="bg-teal-900 p-4 absolute top-0 w-svw">
                            <div class="flex flex-row">
                                <span class="pi pi-user w-16" style="font-size: 2.5rem;"></span>
                                <div class="text-center grow">
                                    <p class="text-lg text-left text-white font-sans">
                                        Nome
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="w-fit bg-teal-500 ">
                            mensagens
                        </div>
                        <div class="w-fit bg-teal-800 absolute bottom-0 h-20">
                            input mensagens
                        </div>
                    </div>
                </div>
            </div>
        </main>

    </div>
</template>
<style scoped>
.main {
    background-color: #0c1317;
}
</style>
