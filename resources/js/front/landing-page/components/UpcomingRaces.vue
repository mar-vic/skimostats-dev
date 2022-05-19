<template>
    <div class="homepage__upcoming-races">
        <div>
            <div class="position-relative pt-5 pt-xl-0">
                <div class="d-flex align-items-center justify-content-center homepage__upcoming-races-title">
                    <div class="latest-results__left-text font-family-heading text-blue">
                        <div>Upcoming</div>
                        Races
                    </div>
                </div>
                <div class="pb-5 pt-0 pt-xl-5">
                    <div class="container py-5">
                        <div class="row justify-content-center">
                            <div class="col-md-9">
                                <div class="mb-3">
                                    <span class="text-uppercase font-weight-bold mr-2">Filter:</span><br>
                                    <a href="#"
                                        @click.prevent="selectedCategory = null"
                                        class="badge badge--custom mr-1 my-1 text-uppercase"
                                        :class="{'badge-active': selectedCategory === null}">all</a>
                                    <a href="#"
                                        v-for="c in filteredCategories"
                                        :key="`up-${c.name}`"
                                        @click.prevent="selectedCategory = c"
                                        class="badge badge--custom mr-1 my-1 text-uppercase"
                                        :class="{'badge-active': selectedCategory && selectedCategory.name === c.name}">{{ c.name }}</a>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table--races table--races-striped text-uppercase">
                                        <thead>
                                            <tr>
                                                <th style="width:70px;0">Date</th>
                                                <th>Race</th>
                                                <th style="width: 100px;">Categ.</th>
                                                <!-- <th style="width: 120px;">Startlist</th>
                                                <th class="text-center" style="width: 90px;">Class</th> -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <upcoming-race-row
                                                v-for="event in filteredRaces"
                                                :event="event"
                                                :category="category"
                                                :key="`ev-${event.id}`"
                                            />
                                        </tbody>
                                    </table>
                                </div>
                                <div class="pt-2">
                                    <a href="/races" class="d-inline-flex align-items-center">Show all races<img src="/images/arrow-right-black.png" class="mt-1 ml-2"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import moment from 'moment'
import { mapGetters } from 'vuex'

import UpcomingRaceRow from './upcoming-races/UpcomingRaceRow.vue'

const shownCategories = [
    {
        name: 'Senior',
        slug: 'men',
        age: 'adult',
    },
    {
        name: 'Junior',
        slug: 'junior-men',
        age: 'junior',
    },
    {
        name: 'Cadet',
        slug: 'cadet-men',
        age: 'cadet',
    },
    {
        name: 'Masters',
        slug: 'masters-men',
        age: 'master',
    },
]

export default {
    data() {
        return {
            selectedCategory: null,
        }
    },
    components: {
        UpcomingRaceRow,
    },
    computed: {
        ...mapGetters('upcoming', ['races', 'categories']),

        filteredRaces() {
            return this.races.filter(race => {
                return race.categories.find(cat => !this.selectedCategory || this.selectedCategory.age === cat.age)
            }).sort((a,b) => moment(a.startDate).diff(b.startDate)).slice(0, 10)
        },

        filteredCategories() {
            return shownCategories
        },

        category() {
            return this.selectedCategory
        }
    },
    watch: {
        categories(newVal) {
            if (newVal.length) {
                this.selectedCategory = null
            }
        }
    },
}
</script>
