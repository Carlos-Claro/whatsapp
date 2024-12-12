<script setup>
import { Badge, Button, Divider } from 'primevue';

</script>
<template>
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
                    <div class="text-center  sm:text-left col-span-4">
                        <p class="text-sm text-black font-semibold">
                            Nome
                        </p>
                        <p class="text-sm text-gray-500">
                            Texto da mensagem
                        </p>
                    </div>
                    <div class="w-fit col-span-1 text-right">
                        <Badge value="2" severity="contrast" class="mr-1" size="large"></Badge>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
