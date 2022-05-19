<template>
    <div>
        <h1 class="font-weight-bold text-uppercase pt-0 pt-md-2 text-blue mb-4">Races per country</h1>

        <table class="table table--races">
            <thead>
                <tr class="text-uppercase">
                    <th>Country</th>
                    <th style="width:30px;" class="text-right">Races</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="country in racesPerCountry" :key="`cnt-${country.id}`">
                    <td>
                        <a :href="`/athletes/?country=${country.code}`" class="d-inline-flex align-items-center">
                            <flag v-if="country.code" :code="country.code" :name="country.name" class="mr-2 flex-shrink-0" />
                            <div>{{ country.name }}</div>
                        </a>
                    </td>
                    <td class="text-right">{{ country.participationCount }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import Flag from '../../shared/components/Flag.vue'
export default {
    components: {
        Flag,
    },
    computed: {
        ...mapState('rankingStrip', ['racesPerCountry']),
    }
}
</script>
