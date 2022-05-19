<template>
    <tr>
        <td><b>{{ event.startDate | date }}</b></td>
        <td class="text-nowrap">
            <a :href="`/event/${event.slug}${categorySlug}`" class="d-flex align-items-center">
            <flag
                v-if="event.country"
                :code="event.country.code"
                :name="event.country.name"
                class="mr-1"
            />
                <b>{{ event.name }}</b>
            </a>
        </td>
        <td class="text-nowrap">{{ event.race_type ? event.race_type.name : '' }}</td>
    </tr>
</template>
<script>
import moment from 'moment'
import Flag from '../../../shared/components/Flag.vue'

export default {
    props: ['event', 'category'],
    components: {
        Flag,
    },
    computed: {
        categorySlug() {
            if (!this.category || !this.category.age) {
                return ''
            }

            const foundCategory = this.event.categories.find(c => c.age === this.category.age)
            if (!foundCategory || !foundCategory.slug) {
                return ''
            }

            return `/${foundCategory.slug}`
        },
    },

    filters: {
        date(val) {
            return moment(val).format('DD/MM/YY')
        },
    },
}
</script>
