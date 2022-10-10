<template>
  <div class="row mb-4">
    <div class="col-3">
      <div class="row">
        <div class="my-auto col col-auto font-weight-bold text-uppercase text-blue">Select season:</div>
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
      </div>
    </div>

    <div class="col-9 justify-content-right">
      <div v-if="noDataForSeason" class="row alert alert-danger mb-0">Sorry, but we have no data for selected season.</div>
      <div v-else class="row justify-content-right">
        <div class="col col-auto ml-1 mr-1 pl-0 pr-0" v-for="rcCat in raceCategories" :key="rcCat">
          <a v-if="rcCat === raceCategory"  @click.prevent="changeRaceCategory($event, rcCat)" class="badge badge-active my-1 badge--custom" href="#">{{rcCat}}</a>
          <a v-else @click.prevent="changeRaceCategory($event, rcCat)" class="badge my-1 badge--custom" href="#">{{rcCat}}</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapState } from 'vuex'

export default {
  data () {
    return {
      openSeasonsDropdown: false,
    }
  },

  computed: {
    ...mapState(['error', 'loading']),
    ...mapState('dataPaneStore', ['raceCategory', 'seasons', 'selectedSeason', 'highlightedPosition', 'noDataForSeason']),
    ...mapGetters('dataPaneStore', ['selectedStatsCategory', 'raceCategories'])
  },

  methods: {
    changeRaceCategory (event, cat) {
      this.$store.commit('dataPaneStore/SET_RACE_CATEGORY', cat)
      this.$store.commit('dataPaneStore/SET_HIGHLIGHTED_POSITION', 0)
    },

    loadData (season) {
      this.$store.commit('dataPaneStore/SET_SELECTED_SEASON', season)
      this.$store.dispatch('dataPaneStore/loadData')
      this.openSeasonsDropdown = false
    },
  }
}
</script>

<style scoped>

</style>
