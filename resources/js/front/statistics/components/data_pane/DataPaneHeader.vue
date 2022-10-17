<template>
  <div class="row mb-4">
    <div class="col-3">
      <div class="row">
        <div class="my-auto col col-auto font-weight-bold text-uppercase text-blue">Select {{filters[0]}}:</div>
        <div class="col col-auto pl-1 pr-1">
          <button class="badge my-1 badge--custom" @click="openFilterDropdown = !openFilterDropdown">
            {{selectedFilter != 0 ? selectedFilter : "All"}}
            <i class="fas fa-caret-down" aria-hidden="true"></i>
          </button>

          <div class="dropdown__menu" :class="{opened:openFilterDropdown}">

            <a
              href="#"
              :key="`s-all`"
              class="pr-4"
              :class="{'font-weight-bold': selectedFilter === 0}"
              @click.prevent="loadData(0)"
            >
              All
            </a>

            <a
              href="#"
              v-for="filter in filters[1]"
              :key="`s-${filter}`"
              class="pr-4"
              :class="{'font-weight-bold': filter === selectedFilter}"
              @click.prevent="loadData(filter);"
            >
              {{filter}}
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="col-9 justify-content-right">
      <div v-if="noDataForFilter" class="row alert alert-danger mb-0">Sorry, but we have no data for selected filter.</div>
      <div v-else class="row justify-content-right">
        <div class="col col-auto ml-1 mr-1 pl-0 pr-0" v-for="athCat in athleteCategories" :key="athCat">
          <a v-if="athCat === athleteCategory"  @click.prevent="changeAthleteCategory($event, athCat)" class="badge badge-active my-1 badge--custom" href="#">{{athCat}}</a>
          <a v-else @click.prevent="changeAthleteCategory($event, athCat)" class="badge my-1 badge--custom" href="#">{{athCat}}</a>
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
      openFilterDropdown: false,
    }
  },

  computed: {
    ...mapState(['error', 'loading']),
    ...mapState('dataPaneStore', ['athleteCategory', 'highlightedPosition', 'filters', 'selectedFilter', 'noDataForFilter']),
    ...mapGetters('dataPaneStore', ['selectedStatsCategory', 'athleteCategories'])
  },

  methods: {
    changeAthleteCategory (event, cat) {
      this.$store.commit('dataPaneStore/SET_ATHLETE_CATEGORY', cat)
      this.$store.commit('dataPaneStore/SET_HIGHLIGHTED_POSITION', 0)
    },

    loadData (filter) {
      this.$store.commit('dataPaneStore/SET_SELECTED_FILTER', filter)
      this.$store.dispatch('dataPaneStore/loadData')
      this.openFilterDropdown = false
    },
  }
}
</script>

<style scoped>

</style>
