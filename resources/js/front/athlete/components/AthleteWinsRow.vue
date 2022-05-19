<template>
    <tr :class="{dnf: Boolean(item.status)}">

        <td>{{ date }}</td> <!-- EVENT DATE -->

        <td class="text-nowrap"> <!-- EVENT NAME -->
            <a :href="`/event/${item.eventSlug}/${item.categorySlug}`" class="d-inline-flex align-items-center">
                <flag v-if="item.countryCode" :code="item.countryCode" :name="item.countryName" class="flex-shrink-0 mr-2" />
                <div>
                    {{ item.eventName }}
                </div>
            </a>
        </td> <!-- END EVENT NAME -->

        <td class="text-nowrap">{{ item.raceType }}</td> <!-- EVENT TYPE -->

        <td class="text-center">{{ item.points || '-' }}</td> <!-- SKIMO POINTS -->

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
            return moment(this.item.eventStartDate).format('DD/MM/YY')
        }
    }
}
</script>
