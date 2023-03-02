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
                                >{{ category.name }}</router-link>

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

                                        <th v-if="!isSprintRace">
                                          <a class="badge badge--custom ml-2"
                                            :class="{'badge-active' : showTimeDifference}"
                                            style="cursor:pointer"
                                            @click="showTimeDifference = !showTimeDifference">
                                            diff
                                          </a>
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

                                        <td v-if="!isSprintRace">
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
          showTimeDifference: true,
          sortedByGcTime: false
        }
    },
    components: {
        EventOverview,
        TimeDifference,
    },
    computed: {
      ...mapState(['event', 'results', 'stage', 'gcWinningTimes', 'isGeneralClassification', 'showGeneralClassification', 'generalClassificationResults']),
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
</style>
