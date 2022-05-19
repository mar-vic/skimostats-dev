<template>
    <img
        :src="`/images/flags/flags-mini/${code.toLowerCase()}.png`"
        :alt="name ? name : ''"
        :title="name ? name : ''"
        :class="`latest-results__${theSize}-flag`" />
</template>
<script>
export default {
    props: ['code', 'name', 'id', 'size'],
    computed: {
        theSize() {
            return this.size || 'micro'
        },
    },
}
</script>
