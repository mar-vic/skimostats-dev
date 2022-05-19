<template>

    <div class="container position-relative py-5">
        <div class="bg-logo-el"></div>
        <div class="bg-text">Races</div>
        <div class="row justify-content-center">
            <div class="col-md-11 col-lg-10">
                <h1 class="page-heading font-weight-bold text-uppercase pt-0 pt-md-5 text-blue mb-4">Races per season: {{ year }}</h1>
                <div class="d-flex mb-2">
                    <div class="d-flex align-items-center">
                        <div class="font-weight-bold mr-2">Select season:</div>
                        <div class="position-relative mr-2" v-click-outside="closeYearDropdown">
                            <button class="badge badge--custom " @click="openYearDropdown = !openYearDropdown; openMonthDropdown = false">
                                {{year}}
                                <i class="fas fa-caret-down"></i>
                            </button>

                            <div class="dropdown__menu" :class="{opened: openYearDropdown}">
                                <router-link
                                    v-for="y in yearsReversed"
                                    :key="`ys-${y}`"
                                    :to="{name:'races.year', params: {year: y}}"
                                    class="pr-4"
                                    :class="{'font-weight-bold': y === year}"
                                    >
                                    {{y}}
                                </router-link>
                            </div>
                        </div>
                        <div class="position-relative mr-2" v-click-outside="closeMonthDropdown">
                            <button class="badge badge--custom " @click="openMonthDropdown = !openMonthDropdown; openYearDropdown = false;">
                                {{ month && month.text ? month.text : 'all months' }}
                                <i class="fas fa-caret-down"></i>
                            </button>

                            <div class="dropdown__menu" :class="{opened: openMonthDropdown}">
                                <router-link
                                    :key="`ms-all`"
                                    :to="{name:'races.year', params: {year}}"
                                    class="pr-4"
                                    :class="{'font-weight-bold': !month}"
                                >
                                    all
                                </router-link>
                                <router-link
                                    v-for="m in months"
                                    :key="`ms-${m.numeric}`"
                                    :to="{name:'races.month', params: { year, month: m.text }}"
                                    class="pr-4"
                                    :class="{'font-weight-bold': month && m.numeric === month.numeric}"
                                    >
                                    {{m.text}}
                                </router-link>
                            </div>
                        </div>
                        <div class="position-relative mr-2" v-click-outside="closeCountriesDropdown">
                            <button class="badge badge--custom " @click="openCountriesDropdown = !openCountriesDropdown;">
                                {{ country ? country.name : 'all countries' }}
                                <i class="fas fa-caret-down"></i>
                            </button>

                            <div class="dropdown__menu" style="white-space: nowrap;" :class="{opened: openCountriesDropdown}">
                                <a href="#"
                                    @click.prevent="country = null; openCountriesDropdown = false;"
                                    :key="`ms-all`"
                                    class="pr-4"
                                    :class="{'font-weight-bold': !country}"
                                >
                                    all
                                </a>
                                <a href="#"
                                    v-for="c in countries"
                                    :key="`ms-${c.code}`"
                                    class="pr-4 d-flex align-items-center"
                                    :class="{'font-weight-bold': country && c.name === country.name}"
                                    @click.prevent="country = c; openCountriesDropdown = false;"
                                >
                                    <flag
                                            v-if="c"
                                            :code="c.code"
                                            :name="c.name" class="mr-1" />
                                    <div>
                                        {{c.name}}
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table--races table--races-striped text-uppercase">
                        <thead>
                            <tr>
                                <th style="width:70px;0">Date</th>
                                <th>Race</th>
                                <th style="width: 100px;">Results</th>
                                <th style="width: 100px;" class="text-center">Class</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="event in countryFilteredRaces" :key="`ev-${event.id}`">
                                <td><b>{{ event.startDate | date }}</b></td>
                                <td class="text-nowrap-mobile">
                                    <a :href="`/event/${event.slug}`" class="d-inline-flex align-items-center">
                                        <flag
                                            v-if="event.country"
                                            :code="event.country.code"
                                            :name="event.country.name" class="mr-1" />
                                        <b>{{ event.name }}</b>
                                    </a>
                                    <span v-if="event.race_type">({{ event.race_type.name }})</span>
                                </td>
                                <td>
                                    <a :href="`/event/${event.slug}`">Results</a>
                                </td>
                                <td class="text-center">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</template>
<script>
import moment from 'moment'
import { mapState, mapGetters, mapMutations } from 'vuex'
import ClickOutside from 'vue-click-outside'

import Flag from '../../shared/components/Flag.vue'

export default {
    data() {
        return {
            openYearDropdown: false,
            openMonthDropdown: false,
            openCountriesDropdown: false,

            country: null,
        }
    },
    components: {
        Flag,
    },
    directives: {
        ClickOutside,
    },
    computed: {
        ...mapState(['races', 'month']),
        ...mapGetters(['years', 'months', 'filteredRaces']),
        countryFilteredRaces() {
            return this.filteredRaces.filter(race => {
                if (!this.country) {
                    return true
                }

                return race.country && race.country.code === this.country.code
            })
        },
        year() {
            return Number(this.$route.params.year) || (new Date()).getFullYear()
        },
        yearsReversed() {
            return this.years.reverse()
        },
        monthParam() {
            return this.$route.params.month || null
        },
        countries() {
            const countries = {}

            for (const { country } of this.filteredRaces) {
                if (country && !countries[country.code]) {
                    countries[country.code] = country
                }
            }

            const result = Object.values(countries).sort((a, b) => {
                return a.name.localeCompare(b.name)
            })

            return result
        }
    },
    watch: {
        year(newVal) {
            this.SET_YEAR(newVal)
            this.openYearDropdown = false
        },
        monthParam(newVal) {
            this.SET_MONTH(newVal)
            this.openMonthDropdown = false
        },
    },
    filters: {
        date(val) {
            return moment(val).format('DD/MM/YY')
        },
    },

    methods: {
        ...mapMutations(['SET_YEAR', 'SET_MONTH']),
        closeYearDropdown() {
            this.openYearDropdown = false;
        },
        closeMonthDropdown() {
            this.openMonthDropdown = false;
        },
        closeCountriesDropdown() {
            this.openCountriesDropdown = false;
        }
    },

    mounted() {
        this.SET_YEAR(Number(this.year))
        this.SET_MONTH(this.monthParam)
    },
}
</script>
