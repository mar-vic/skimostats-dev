<template>
    <div class="text-nowrap">
        <a href="#"
            @click.prevent="emitSort">
            {{ name }}

            <span v-if="orderBy === propName && sort == 'desc'" class="fa fa-caret-down"></span>
            <span v-else-if="orderBy === propName" class="fa fa-caret-up"></span>
        </a>
    </div>
</template>
<script>
export default {
    props: ['propName', 'name', 'sort', 'orderBy'],
    methods: {
        emitSort() {
            this.$emit('change', {
                sort: this.orderBy !== this.propName ? 'asc' : (this.sort === 'desc' ? 'asc' : 'desc'),
                orderBy: this.propName,
            })
        },
    },
}
</script>
