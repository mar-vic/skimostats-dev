<template>
    <div class="bg-grey-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div v-if="error" class="alert alert-danger mb-0">{{error}}</div>
                    <div v-if="loading" class="p-5 text-center">
                        <img src="/images/loading.svg" alt="Loading...">
                    </div>
                    <div v-else class="py-5">
                        <h1 class="page-heading font-weight-bold text-uppercase pt-0 pt-md-2 text-blue mb-4">Results per season: {{ year }}</h1>
                        <h2 class="font-weight-bold text-uppercase pt-0 pt-md-2 text-blue mb-4">racedays in the season: {{ raceDays }}</h2>

                        <div class="d-flex mb-2">
                            <div class="font-weight-bold text-uppercase text-blue">Select season:</div>
                            <div class="position-relative d-inline-block ml-3">
                                <button class="badge badge--custom " @click="openYearDropdown = !openYearDropdown">
                                    {{year}}
                                    <i class="fas fa-caret-down"></i>
                                </button>

                                <div class="dropdown__menu" :class="{opened:openYearDropdown}">
                                    <a
                                        href="#"
                                        v-for="y in years"
                                        :key="`ys-${y}`"
                                        class="pr-4"
                                        :class="{'font-weight-bold': y === year}"
                                        @click.prevent="loadRaces(y);"
                                        >
                                        {{y}}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="position-relative">
                            <div v-if="races" :style="{opacity:loadingRaces?0.4:1}" class="table-responsive">
                                <table class="table table--races table--races-striped">
                                    <thead>
                                        <tr>
                                            <th style="width:40px">
                                                <table-head-sort
                                                    propName="startDate"
                                                    name="Date"
                                                    @change="sortChange"
                                                    :sort="sort"
                                                    :orderBy="orderBy"
                                                />
                                            </th>
                                            <th style="width:50px">
                                                <table-head-sort
                                                    propName="rank"
                                                    name="Result"
                                                    @change="sortChange"
                                                    :sort="sort"
                                                    :orderBy="orderBy"
                                                />
                                            </th>
                                            <th>
                                                <table-head-sort
                                                    propName="name"
                                                    name="Race event"
                                                    @change="sortChange"
                                                    :sort="sort"
                                                    :orderBy="orderBy"
                                                />
                                            </th>
                                            <th style="width:150px">
                                                <table-head-sort
                                                    propName="raceTypeName"
                                                    name="Type"
                                                    @change="sortChange"
                                                    :sort="sort"
                                                    :orderBy="orderBy"
                                                />
                                            </th>
                                            <th style="width:60px">
                                                <table-head-sort
                                                    propName="points"
                                                    name="Points"
                                                    @change="sortChange"
                                                    :sort="sort"
                                                    :orderBy="orderBy"
                                                />
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <athlete-races-row v-for="event in filteredRaces" :item="event" :key="`e-${event.id}-${event.categorySlug}`" />
                                    </tbody>
                                </table>
                            </div>
                            <img v-if="loadingRaces"
                                src="/images/loading.svg"
                                alt="Loading..."
                                style="position:absolute;left:50%;top:50px;transform:translateY(-50%) translateX(-50%);z-index:9">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>

import { mapState } from 'vuex'

// SKIMO COMPONENTS
import AthleteRacesRow from './AthleteRacesRow.vue'
import TableHeadSort from './TableHeadSort.vue'

const compareString = (a,b,sort,orderBy) => sort === 'asc' ? String(a[orderBy]).localeCompare(b[orderBy]) : String(b[orderBy]).localeCompare(a[orderBy])
const compareNumber = (a,b,sort,orderBy) => sort === 'desc' ? Number(b[orderBy]) - Number(a[orderBy]) : Number(a[orderBy]) - Number(b[orderBy])

export default {
    components: {
        AthleteRacesRow,
        TableHeadSort,
    },
    data() {
        return {
            openYearDropdown: false,
            loadingRaces: false,
            orderBy: 'startDate',
            sort: 'desc',
        }
    },
    computed: {
        ...mapState(['athleteId']),
        ...mapState('races', ['races', 'error', 'year', 'years', 'loading', 'raceDays']),

        filteredRaces() {
            return this.races.sort((a, b) => {
                let cmp = []

                if (['points', 'rank'].includes(this.orderBy)) {
                    cmp.push((this.orderBy === 'rank' ? -1 : 1) * compareString(a, b, this.sort, 'status'))
                    cmp.push(compareNumber(a, b, this.sort, this.orderBy))
                }

                return cmp.reduce((pv, cv) => {
                    return pv || cv
                }, false) || compareString(a, b, this.sort, this.orderBy) || compareString(a, b, 'desc', 'startDate')
            })
        }
    },

    methods: {
        sortChange({ sort, orderBy }) {
            this.sort = sort
            this.orderBy = orderBy
        },

        async loadRaces(year) {
            if (this.loadingRaces) {
                return
            }

            this.loadingRaces = true
            try {
                await this.$store.dispatch('races/loadRaces', {
                    athleteId: this.athleteId,
                    year,
                })

                await this.$store.dispatch('races/loadRaceDays', {
                    athleteId: this.athleteId,
                    year,
                })
            } catch(e) {
                this.$store.commit('SET_ERROR', e)
            }
            this.openYearDropdown = false
            this.loadingRaces = false
        },
    },
}
</script>
