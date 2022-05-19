<template>
    <tr :class="{dnf: Boolean(item.status)}">
        <td>{{ date }}</td>
        <td class="text-center">
            <span v-if="item.status">{{ item.status }}</span>
            <span v-else>{{ item.rank }}</span>
        </td>
        <td class="text-nowrap">
            <a :href="`/event/${item.slug}/${item.categorySlug}`" class="d-inline-flex align-items-center">
                <flag v-if="item.countryCode" :code="item.countryCode" :name="item.countryName" class="flex-shrink-0 mr-2" />
                <div>
                    {{ item.name }}
                </div>
            </a>
        </td>
        <td class="text-nowrap">{{ item.raceTypeName }}</td>
        <td class="text-center">{{ item.points || '-' }}</td>
    </tr>
</template>
<script>
import moment from 'moment'
import Flag from '../../shared/components/Flag.vue'

export default {
    props: ['item'],
    components: {
        Flag,
    },
    computed: {
        date() {
            return moment(this.item.startDate).format('DD/MM/YY')
        }
    }
}
</script>
