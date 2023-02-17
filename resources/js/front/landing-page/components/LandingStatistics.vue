<template>
    <div class="homepage__stats">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-11">
                    <div class="d-flex flex-column flex-md-row mb-0 mb-md-3">
                        <div class="homepage__stats-card flex-grow-1 mr-0 mr-md-3">
                            <standing-athletes />
                        </div>
                        <div class="homepage__stats-card">
                          <h2 class="h1 font-weight font-weight-bold text-blue text-uppercase mb-4">{{__('Popular')}}</h2>
                            <div class="text-center p-5" v-show="loadingPopular">Loading...</div>
                            <div class="table-responsive" v-show="!loadingPopular">
                                <table class="table table--races text-uppercase">
                                    <thead>
                                        <tr>
                                          <th class="pl-0">{{__('Athlete')}}</th>
                                          <th style="width: 100px;" class="text-right">{{__('Visits')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="athlete in popularAthletes" :key="`pa-${athlete.id}`">
                                            <td class="text-nowrap pl-0">
                                                <a :href="`/athlete/${athlete.slug}`" class="d-flex align-items-center">
                                                    <img v-if="athlete.countryCode" :src="`/images/flags/flags-mini/${athlete.countryCode.toLowerCase()}.png`" class="flag-icon--micro mr-1" :alt="athlete.countryCode" />
                                                    <div class="font-weight-bold">{{ athlete.firstName }} <span class="text-uppercase">{{ athlete.lastName }}</span></div>
                                                </a>
                                            </td>
                                            <td class="text-right text-nowrap"><b>{{ athlete.visits }}</b></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="pt-2">
                              <a href="/athletes" class="d-inline-flex align-items-center">{{__('Show full list athletes')}}<img src="/images/arrow-right-black.png" class="mt-1 ml-2"></a>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-column flex-md-row">
                        <div class="homepage__stats-card flex-grow-1 mr-0 mr-md-3">
                            <standing-nations />
                        </div>
                        <div class="homepage__stats-card">
                          <h2 class="h1 font-weight font-weight-bold text-blue text-uppercase mb-4">{{__('Birthdays')}}</h2>
                            <div class="table-responsive">
                                <table class="table table--races text-uppercase">
                                    <thead>
                                        <tr>
                                          <th class="pl-0">{{__('Athlete')}}</th>
                                          <th style="width: 100px;" class="text-right">{{__('Birthday')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="athlete in birthdays" :key="`bd-${athlete.id}`">
                                            <td class="text-nowrap pl-0">
                                                <a :href="`/athlete/${athlete.slug}`" class="d-flex align-items-center">
                                                    <img v-if="athlete.countryCode" :src="`/images/flags/flags-mini/${athlete.countryCode.toLowerCase()}.png`" class="flag-icon--micro mr-1" :alt="athlete.countryCode" />
                                                    <div class="font-weight-bold">{{ athlete.firstName }} <span class="text-uppercase">{{ athlete.lastName }}</span></div>
                                                </a>
                                            </td>
                                            <td class="text-right text-nowrap"><b>{{ theDate(athlete.dateOfBirth) }}</b></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>


                            <!-- <div class="pt-2">
                                <a href="#" class="d-inline-flex align-items-center">Show full birthday list<img src="/images/arrow-right-black.png" class="mt-1 ml-2"></a>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import moment from 'moment'
import {mapGetters} from 'vuex'

import StandingAthletes from './StandingAthletes.vue'
import StandingNations from './StandingNations.vue'

export default {
    components: {
        StandingAthletes,
        StandingNations,
    },
    data() {
        return {
            loadingPopular: true,
        }
    },
    computed: {
        ...mapGetters('landingStatistics', [
            'popularAthletes',
            'birthdays',
        ]),
    },
    methods: {
        theDate(date) {
            return moment(date).format('DD. MM.')
        },
    },
    async mounted() {
        try {
            const { data } = await axios.get('/v1/popular-athletes')
            this.$store.commit('landingStatistics/SET_POPULAR_ATHLETES', data)
        } catch(e) {}
        this.loadingPopular = false
    }
}
</script>
