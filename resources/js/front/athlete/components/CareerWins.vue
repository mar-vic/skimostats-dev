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
                        <h1 class="page-heading font-weight-bold text-uppercase pt-0 pt-md-2 text-blue mb-4">Career Wins</h1>

                        <div class="position-relative">
                            <table class="table table--races table--races-striped"> <!-- CAREER WINS TABLE -->

                                <thead> <!-- TABLE HEAD -->
                                    <tr>

                                        <!-- DATE -->
                                        <th style="width:40px">
                                            <table-head-sort
                                                propName="eventStartDate"
                                                name="Date"
                                                @change="sortChange"
                                                :sort="sort"
                                                :orderBy="orderBy"
                                            />
                                        </th>

                                        <!-- RACE EVENT NAME -->
                                        <th style="width:40px">
                                            <table-head-sort
                                                propName="eventName"
                                                name="Race Event"
                                                @change="sortChange"
                                                :sort="sort"
                                                :orderBy="orderBy"
                                            />
                                        </th>

                                        <!-- RACE EVENT TYPE -->
                                        <th style="width:40px">
                                            <table-head-sort
                                                propName="raceType"
                                                name="Type"
                                                @change="sortChange"
                                                :sort="sort"
                                                :orderBy="orderBy"
                                            />
                                        </th>

                                        <!-- POINTS ACQUIRED -->
                                        <th style="width:40px">
                                            <table-head-sort
                                                propName="points"
                                                name="Points"
                                                @change="sortChange"
                                                :sort="sort"
                                                :orderBy="orderBy"
                                            />
                                        </th>
                                    </tr>
                                </thead> <!-- END TABLE HEAD -->

                                <tbody> <!-- TABLE BODY -->
                                    <athlete-wins-row v-for="event in sortedWins" :item="event" :key="`e-${event.id}-${event.categorySlug}`" />
                                </tbody> <!-- END TABLE BODY -->

                            </table> <!-- END CAREER WINS TABLE -->
                        </div>
                        <img v-if="loading"
                            src="/images/loading.svg"
                            alt="Loading..."
                            style="position:absolute;left:50%;top:50px;transform:translateY(-50%) translateX(-50%);z-index:9">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>

// VUE Libraries
import moment from 'moment'
import { mapState } from 'vuex'

// SKIMO COMPONENTS
import AthleteWinsRow from './AthleteWinsRow.vue'
import TableHeadSort from './TableHeadSort.vue'

const compareString = (a,b,sort,orderBy) => sort === 'asc' ? String(a[orderBy]).localeCompare(b[orderBy]) : String(b[orderBy]).localeCompare(a[orderBy])
const compareNumber = (a,b,sort,orderBy) => sort === 'desc' ? Number(b[orderBy]) - Number(a[orderBy]) : Number(a[orderBy]) - Number(b[orderBy])


export default {
    components: {
        AthleteWinsRow,
        TableHeadSort,
    },

    data() {
        return {
            orderBy: 'eventStartDate',
            sort: 'desc',
        }
    },

    computed: {
        ...mapState('careerWins', ['careerWins', 'error', 'loading']),

        sortedWins() {
            // compute appropriately sorted array of wins
            return this.careerWins.sort((a, b) => {
                let cmp = []

                if (['points', 'rank'].includes(this.orderBy)) {
                    cmp.push((this.orderBy === 'rank' ? -1 : 1) * compareString(a, b, this.sort, 'status'))
                    cmp.push(compareNumber(a, b, this.sort, this.orderBy))
                }

                return cmp.reduce((pv, cv) => {
                    return pv || cv
                }, false) || compareString(a, b, this.sort, this.orderBy) || compareString(a, b, 'desc', 'eventStartDate')
            })
        }
    },

    methods: {
        sortChange({ sort, orderBy }) {
            this.sort = sort
            this.orderBy = orderBy
        }
    }

}
</script>
