<script setup>
import Paginator from 'primevue/paginator';
import { reactive, ref, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
const props = defineProps({
    links: {
        type: Object,
        required: true
    },
})
const perPage = ref(props.links.per_page)
watch(perPage, (pPNew) => {
    let url = props.links.path + '?per_page=' + pPNew
    routeTo(url)
})
const page = ref(props.links.from)
watch(page, (pNew) => {
    let url = props.links.links[(pNew/perPage.value)+1].url + '&per_page=' + perPage.value
    routeTo(url)
})
const routeTo = (url) => {
    router.visit(url)
}
</script>
<template>
    <Paginator
                v-model:first="page"
                v-model:rows="perPage"
                :totalRecords="links.total"
                :rowsPerPageOptions="[10,20,50]"
                >
    </Paginator>
</template>
