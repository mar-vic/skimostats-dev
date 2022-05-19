<template>
    <div>
        <div class="latest-results py-5">
            <div class="container">
                <!-- 13 = PIERRA MENTA -->
                <div v-if="event.raceId === 13 && relatedEvents.length" class="row justify-content-center">
                    <div class="col-md-10 text-right">
                        <div class="position-relative d-inline-block mr-3">
                            <button class="badge badge--custom " @click="openYearDropdown = !openYearDropdown">
                                {{year}}
                                <i class="fas fa-caret-down"></i>
                            </button>

                            <div class="dropdown__menu" :class="{opened:openYearDropdown}">
                                <a
                                    v-for="y in relatedEvents"
                                    :key="`ys-${y.id}`"
                                    :href="`/event/${y.slug}`"
                                    class="pr-4"
                                    :class="{'font-weight-bold': y.id === event.id}"
                                    :title="y.name"
                                    >
                                    {{y.year}}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <event-overview :event="event" :is-detail="true" />
                    </div>
                </div>
            </div>

        </div>

        <div class="py-5" id="complete-results">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-10 col-lg-9">

                        <div class="d-flex justify-content-between align-items-center mb-3 flex-column flex-md-row">
                            <h1 class="font-weight-bold text-uppercase text-blue text-nowrap mr-0 mr-md-4">Complete results</h1>
                            <div class="text-center text-md-left">
                                <router-link
                                    :to="{ name: 'event.category', params: { event: event.slug, category: category.slug }}"
                                    class="badge badge--custom ml-1 my-1"
                                    :class="{'badge-active': selectedCategory.id === category.id && !isGCscreen}"
                                    v-for="category in results"
                                    :key="`catz-${category.id}`"
                                >{{ category.name }}</router-link><div class="inline" v-if="showGeneralClassification"><router-link
                                    :to="{ name: 'event.category.gc', params: { event: event.slug, category: category.slug }}"
                                    class="badge badge--custom ml-1 my-1"
                                    :class="{'badge-active': selectedCategory.id === category.id && isGCscreen}"
                                    v-for="category in gcCategories"
                                    :key="`catzgc-${category.id}`"
                                >General Classification - {{ category.name }}</router-link></div>
                            </div>
                        </div>


                        <div class="table-responsive">
                            <table class="table table--races table--races-striped text-uppercase">
                                <thead>
                                    <tr>
                                        <th style="width:70px;0">Rnk</th>
                                        <th>Athlete</th>
                                        <th style="width: 100px;">Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="entry in entries" :key="`entry${isGCscreen?'-gc':''}-${entry.id}`" :class="{dnf: Boolean(entry.status)}">
                                        <td><b>{{ entry.status ? entry.status : entry.rank }}</b></td>
                                        <td>
                                            <div class="d-flex align-items-center flex-wrap">
                                                <a :href="`/athlete/${participant.slug}`" class="d-inline-flex align-items-center mr-2" v-for="participant in entry.participants" :key="`pt-${participant.id}`">
                                                    <img
                                                        v-if="participant.country"
                                                        class="latest-results__micro-flag mr-1"
                                                        :src="`/images/flags/mini/${participant.country.toUpperCase()}.png`"
                                                        :alt="participant.country" />
                                                    <b>{{ participant.name }}</b>
                                                </a>
                                            </div>
                                        </td>
                                        <td class="text-nowrap">
                                            <div v-if="isSprintRace">
                                                {{ entry.prependTime ? entry.prependTime + ' ' : '' }}
                                                {{ entry.timeFormatted }}
                                            </div>
                                            <div v-else>
                                                {{ entry.prependTime ? entry.prependTime + ' ' : '' }}
                                                <span class="font-weight-bold" v-if="entry.id === firstEntry.id">
                                                    {{ entry.timeFormatted }}
                                                </span>
                                                <time-difference
                                                    v-if="entry.id !== firstEntry.id && entry.time != 0"
                                                    :time="entry.time"
                                                    :firstTime="firstEntry.time"
                                                    :title="entry.timeFormatted" />
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import {mapGetters,mapState,mapMutations} from 'vuex'
import EventOverview from '../../landing-page/components/latest-results/EventOverview.vue'
import TimeDifference from '../../shared/components/TimeDifference.vue'

export default {
    data() {
        return {
            openYearDropdown: false,
        }
    },
    components: {
        EventOverview,
        TimeDifference,
    },
    computed: {
        ...mapState(['event', 'results', 'showGeneralClassification', 'generalClassificationResults']),
        // ...mapGetters(['selectedCategory']),
        selectedCategory() {
            let category = this.results[0]
            const findIn = this.isGCscreen ? this.generalClassificationResults : this.results
            if (this.results.find(c => c.slug === this.$route.params.category)) {
                category = findIn.find(c => c.slug === this.$route.params.category)
            }
            return category
        },
        gcCategories() {
            return this.generalClassificationResults.filter(c => {
                return c.entries && c.entries.length
            })
        },
        isGCscreen() {
            return this.$route.name === 'event.category.gc'
        },
        entries() {
            return this.selectedCategory && this.selectedCategory.entries ? this.selectedCategory.entries.filter(e => e.participants && e.participants.length) : []
        },
        firstEntry() {
            return this.entries.find(e => Number(e.rank) === 1) || {id:0, time:0}
        },
        isSprintRace() {
            return this.event && this.event.raceType && this.event.raceType.type === 3
        },
        relatedEvents() {
            return this.event && this.event.relatedEvents ? this.event.relatedEvents : []
        },
        year() {
            return this.event.year
        },
    },
    methods: {
        ...mapMutations(['SET_CATEGORY']),
    },
    watch: {
        results() {
            this.$nextTick(() => {
                if (this.$route.params.category) {
                    setTimeout(() => {
                        $('html,body').animate({
                            scrollTop: $('#complete-results').offset().top,
                        }, 300)
                    }, 100)
                }
            })
        },
    },
}
</script>

<style lang="scss" scoped>
.dropdown__menu {
    z-index: 10;
    border: 1px #bebfc1 solid;
    background:#fff;
    border-radius:3px;
    position: absolute;
    right: 0;
    top: 100%;
    min-width: 100%;
    white-space: nowrap;

    a {
        padding: 4px 10px;
        display: block;
    }
}
</style>
