<template>

    <div>
        <div class="latest-results py-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <event-overview :event="event" :is-detail="true" />
                    </div>
                </div>
            </div>
        </div>

        <div class="py-5" id="complete-results">
            <div class="container">
                <div v-if="!showResults && event.place && checkin" class="s22-map row justify-content-center">
                    <div class="col-md-10 px-4">
                        <div class="mb-3 p-3" >
                            <h2 class="font-weight-bold text-uppercase text-blue text-nowrap mr-0 mr-md-4">Rent a place to stay at during the event</h2>
                            <p class="fs-5">
                                <b>Skimostats</b> has partnered with <a href="https://www.stay22.com/"><b>Stay22</b></a> to provide you with all available accommodations (hotels, rentals, hostels, etc.) at the lowest price online.
                            </p>
                            <p>
                                This pricing is equal or better than what you’ll find on any discount travel or hotel website. Book directly from the map below!
                            </p>
                        </div>
                        <stay22-embed
                            :location="event.place"
                            :checkin="checkin"
                            :checkout="checkout"
                        />
                    </div>
                </div>

                <div v-if="showResults || !event.place || !checkin"  class="row justify-content-center">
                    <div class="col-md-10 col-lg-9">

                        <div class="d-flex justify-content-between align-items-center mb-3 flex-column flex-md-row">
                            <h1 class="font-weight-bold text-uppercase text-blue text-nowrap mr-0 mr-md-4">Results</h1>
                            <div class="text-center text-md-left">
                                <router-link
                                    :to="{ name: 'event.category', params: { event: event.slug, category: category.slug }}"
                                    class="badge badge--custom ml-1 my-1"
                                    :class="{'badge-active': selectedCategory.id === category.id && !isGCscreen}"
                                    v-for="category in results"
                                    :key="`catz-${category.id}`"
                                >{{ category.name }}</router-link>

                                <label class="switch btn-switch">
                                    <input @click="showTimeDifference = !showTimeDifference" type="checkbox" :checked="showTimeDifference ? 'checked' : ''" name="time_mode" id="time_mode" value="1">
                                    <label for="time_mode" data-on="DIFF" data-off="ABS" class="btn-switch-inner"></label>
                                </label>


                                <!-- <div class="inline" v-if="showGeneralClassification">
<router-link
:to="{ name: 'event.category.gc', params: { event: event.slug, category: category.slug }}"
class="badge badge--custom ml-1 my-1"
:class="{'badge-active': selectedCategory.id === category.id && isGCscreen}"
v-for="category in gcCategories"
:key="`catzgc-${category.id}`"
>
General Classification - {{ category.name }}
</router-link>
</div>
-->
                            </div>

                        </div>

                        <div class="table-responsive">
                            <table class="table table--races table--races-striped text-uppercase">
                                <thead>
                                    <tr>
                                        <th style="width:70px;0">Rnk</th>

                                        <th>Athlete</th>

                                        <th v-if="!isGeneralClassification" style="text-align:center;">
                                            <span v-if="stage">
                                                <a href="#"
                                                    @click.prevent="sortEntriesByStageTime">
                                                    Stage Time
                                                    <span v-if="!sortedByGcTime" class="fa fa-caret-down"></span>
                                                </a>
                                            </span>
                                            <span v-else>Time</span>
                                        </th>

                                        <th v-if="stage" style="text-align:center;">
                                            <a href="#"
                                                @click.prevent="sortEntriesByGcTime">
                                                GC Time
                                                <span v-if="sortedByGcTime" class="fa fa-caret-down"></span>
                                            </a>
                                        </th>

                                        <th v-if="isGeneralClassification" style="text-align:center;">
                                            TIME
                                        </th>

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

                                        <td v-if="!isGeneralClassification" class="text-nowrap" style="text-align:center;">
                                            <div v-if="isSprintRace">
                                                {{ entry.prependTime ? entry.prependTime + ' ' : '' }}
                                                {{ entry.timeFormatted }}
                                            </div>
                                            <div v-else>
                                                {{ entry.prependTime ? entry.prependTime + ' ' : '' }}
                                                <span class="font-weight-bold" v-if="entry.id === firstEntry.id || !showTimeDifference">
                                                    {{ entry.timeFormatted }}
                                                </span>
                                                <time-difference
                                                    v-if="entry.id !== firstEntry.id && entry.time != 0 && showTimeDifference"
                                                    :time="entry.time"
                                                    :firstTime="firstEntry.time"
                                                    :title="entry.timeFormatted" />
                                            </div>
                                        </td>

                                        <td v-if="stage || isGeneralClassification" style="text-align:center;" >
                                            <span v-if="relativeTime(entry.gcTime, entry.category) != 0 && showTimeDifference">
                                                +{{relativeTime(entry.gcTime, entry.category)}}
                                            </span>
                                            <strong v-else>{{entry.gcTimeFormatted}}</strong>
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
import Stay22Embed from "../../shared/components/Stay22Embed.vue"

export default {
    data() {
        return {
            openYearDropdown: false,
            showTimeDifference: true,
            sortedByGcTime: false
        }
    },
    components: {
        EventOverview,
        TimeDifference,
        Stay22Embed,
    },
    computed: {
        ...mapState(['event', 'results', 'stage', 'gcWinningTimes', 'isGeneralClassification', 'showGeneralClassification', 'generalClassificationResults']),
        showResults() {
            // Returns true, if there are some result entries.
            return this.results.reduce((previous, current) => {
                return previous || current.entries.length > 0;
            }, false)
        },
        checkin() {
            return this.event.startDate.split(" ")[0]
        },
        checkout() {
            return this.event.endDate.split(" ")[0]
        },
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

        relativeTime(absTime, category) {

            if (!absTime) {
                return ''
            }

            const milliseconds = absTime - this.gcWinningTimes[category];

            if (milliseconds == 0) {
                return milliseconds
            }

            const seconds = Math.floor((milliseconds / 1000) % 60);
            const minutes = Math.floor((milliseconds / 1000 / 60) % 60);
            const hours = Math.floor((milliseconds / 1000 / 60 / 60) % 24);

            const formattedTime = [
                hours.toString().padStart(2, "0"),
                minutes.toString().padStart(2, "0"),
                seconds.toString().padStart(2, "0")
            ].join(":");

            return formattedTime
        },

        sortEntriesByGcTime() {
            this.results.forEach(result => {
                result.entries.sort((a, b) => {
                    if (!b.gcTime) {
                        return -1
                    }
                    return a.gcTime - b.gcTime
                })
            })
            this.sortedByGcTime = true
        },

        sortEntriesByStageTime() {
            this.results.forEach(result => {
                result.entries.sort((a, b) => {
                    if (!b.gcTime) {
                        return -1
                    }

                    return a.time - b.time
                })
            })
            this.sortedByGcTime = false
        }
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

.btn-switch{
    display: inline-block;
    margin: 0px;
    margin-left: 40px;
    position: relative;
    top: 10px;
}

.btn-switch > label.btn-switch-inner{
    margin: 0px;
    width: 80px;
    height: 29px;
    background: #E0E0E0;
    border-radius: 3px;
    overflow: hidden;
    position: relative;
    transition: all 0.3s ease;
    /*box-shadow: 0px 0px 8px 0px rgba(17, 17, 17, 0.34) inset;*/
    display: block;
}

.btn-switch > label.btn-switch-inner:after{
    content: attr(data-on);
    position: absolute;
    font-size: 75%;
    font-weight: bold;
    top: 6px;
    right: 8px;
    /* background-color: red; */
}

.btn-switch > label.btn-switch-inner:before{
    content: attr(data-off);
    width: 40px;
    height: 25px;
    background: #fff;
    border-radius: 3px;
    position: absolute;
    right: 2px;
    top: 2px;
    text-align: center;
    transition: all 0.3s ease;
    font-weight: bold;
    font-size: 75%;
    box-shadow: 0px 0px 6px -2px #111;
    padding: 4px 0px;
}

.btn-switch input[type="checkbox"]{
    cursor: pointer;
    width: 80px;
    height: 25px;
    opacity: 0;
    position: absolute;
    top: 0;
    z-index: 1;
    margin: 0px;
}

.btn-switch input[type="checkbox"]:not(:checked) + label.btn-switch-inner:after{
    content: attr(data-on);
    left: 8px;
}

.btn-switch input[type="checkbox"]:checked + label.btn-switch-inner:after{
    content: attr(data-off);
    right: 8px;
}

.btn-switch input[type="checkbox"]:checked + label.btn-switch-inner:before{
    content: attr(data-on);
    right: auto;
    /* top: -10px; */
    left: 2px;
}

.btn-switch input[type="checkbox"]:checked + label.btn-switch-inner{
    /*background: #66BB6A; */
    /*color: #fff;*/
}

.btn-switch input[type="checkbox"]:checked ~ .alert{
    display: block;
}

.s22-map > div > div, #stay22-widget {
    box-shadow: 0 0 .8em rgba(21, 23, 74, .2);
}
</style>
