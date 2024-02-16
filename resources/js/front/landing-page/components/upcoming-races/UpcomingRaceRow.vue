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
        <td class="text-nowrap" style="font-weight:700;"><em>{{ event.race_type ? event.race_type.name : '' }}</em></td>
        <td class="text-center">
            <span v-if="event.place != null"
                class="px-1"
                data-bs-toggle="tooltip"
                data-bs-placement="right"
                :title="'Rent a place to stay at in ' + event.place + ' during the race. (we get commisions for click-throughs)'"
            >
                <i @click="setStay22Data(event)"
                    type="button"
                    data-bs-toggle="modal"
                    data-bs-target="#stay22ModalContainer"
                    class="fas fa-map-marked-alt fs-6"
                    style="color:#15174a;"
                ></i>
            </span>
            <span v-else>
                -
            </span>
        </td>
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

    methods: {
        setStay22Data(event) {
            this.$parent.stay22Data = {
                location: event.place,
                checkin: event.startDate.split(" ")[0],
                checkout: event.endDate.split(" ")[0]
            }
        }
    },

    filters: {
        date(val) {
            return moment(val).format('DD/MM/YY')
        },
    },
}
</script>
