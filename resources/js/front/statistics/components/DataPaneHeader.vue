<template>
  <div>
    <h2 class="font-weight-bold text-uppercase text-blue">{{ selectedStatsCategory.shortName }}</h2>
    <div class="row mb-4 justify-content-between">
      <div class="col-3">
        <div class="row">
          <div class="col col-auto pl-1 pr-1">
            <button class="badge my-1 badge--custom" @click="openSeasonsDropdown = !openSeasonsDropdown">
              {{selectedSeason != 0 ? selectedSeason : "All time"}}
              <i class="fas fa-caret-down" aria-hidden="true"></i>
            </button>

            <div class="dropdown__menu" :class="{opened:openSeasonsDropdown}">

              <a
                href="#"
                :key="`s-all`"
                class="pr-4"
                :class="{'font-weight-bold': selectedSeason === 0}"
                @click.prevent="loadData(0)"
              >
                All time
              </a>

              <a
                href="#"
                v-for="season in seasons"
                :key="`s-${season}`"
                class="pr-4"
                :class="{'font-weight-bold': season === selectedSeason}"
                @click.prevent="loadData(season);"
              >
                {{season}}
              </a>
            </div>

          </div>
          <div class="col col-auto pl-1 pr-1">
            <button class="badge my-1 badge--custom" @click="openPagiDropdown = !openPagiDropdown">
              {{resultsPerPage}} results / page
              <i class="fas fa-caret-down" aria-hidden="true"></i>
            </button>

            <div class="dropdown__menu" :class="{opened:openPagiDropdown}">

              <a
                href="#"
                class="pr-4"
                :class="{'font-weight-bold': resultsPerPage === 25}"
                @click.prevent="changePagination(25)"
              >
                25 results / page
              </a>

              <a
                href="#"
                class="pr-4"
                :class="{'font-weight-bold': resultsPerPage === 50}"
                @click.prevent="changePagination(50)"
                >
                50 results / page
              </a>

              <a
                href="#"
                class="pr-4"
                :class="{'font-weight-bold': resultsPerPage === 100}"
                @click.prevent="changePagination(100)"
                >
                100 results / page
              </a>

            </div>
          </div>
        </div>
      </div>
      <div class="col-9">
        <div class="row justify-content-right">
          <div class="col col-auto ml-1 mr-1 pl-0 pr-0" v-for="rcCat in raceCategories" :key="rcCat">
            <a v-if="rcCat === raceCategory"  @click="changeRaceCategory($event, rcCat)" class="badge badge-active my-1 badge--custom" href="#">{{rcCat}}</a>
            <a v-else @click.prevent="changeRaceCategory($event, rcCat)" class="badge my-1 badge--custom" href="#">{{rcCat}}</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapState } from 'vuex'

export default {
  // components: {
  //   AthleteRaces,
  //   CareerWins,
  //   RankingStrip
  // }
  data () {
    return {
      openSeasonsDropdown: false,
      openPagiDropdown: false
    }
  },

  computed: {
    ...mapState(['error', 'loading', 'raceCategory', 'seasons', 'selectedSeason', 'resultsPerPage']),
    ...mapGetters(['selectedStatsCategory', 'raceCategories']),
  },

  methods: {
    changeRaceCategory (event, cat) {
      this.$store.commit('SET_RACE_CATEGORY', cat)
    },

    loadData (season) {
      console.log(season)
      this.$store.commit('SET_SELECTED_SEASON', season)
      this.$store.dispatch('loadData')
      this.openSeasonsDropdown = false
    },

    changePagination (count) {
      this.$store.commit('SET_RESULTS_PER_PAGE', count)
      this.openPagiDropdown = false
    }
  }
}
</script>

<style scoped>

</style>
