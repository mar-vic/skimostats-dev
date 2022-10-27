<template>
  <div class="row mb-4">
    <div class="col-3">
      <div class="row">
        <div class="my-auto col col-auto font-weight-bold text-uppercase text-blue">{{filters[0]}}:</div>

        <div v-if="activeFilters === 'months'" class="col col-auto pl-1 pr-1 mnt-picker">
          <month-picker-input
            :default-year="selectedFilter[0]"
            :default-month="selectedFilter[1]"
            @input="monthPickerLoadData">
          </month-picker-input>
        </div>

        <div v-else class="col col-auto pl-1 pr-1">
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
      <div v-if="loading" class="p-1 text-center">
        <img src="/images/loading.svg" alt="Loading...">
      </div>
      <div v-else-if="noDataForFilter" class="row alert alert-danger mb-0">
        Sorry, but we have no data for selected filter.
      </div>
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
import { MonthPickerInput } from 'vue-month-picker'

export default {
  data () {
    return {
      openFilterDropdown: false,
    }
  },

  components: {
    MonthPickerInput
  },

  computed: {
    ...mapGetters(['activeFilters']),
    ...mapState('dataPaneStore', ['athleteCategory', 'highlightedPosition', 'filters', 'selectedFilter', 'noDataForFilter', 'loading']),
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

    monthPickerLoadData (date) {
      this.$store.commit('dataPaneStore/SET_SELECTED_FILTER', [date.year, date.monthIndex])
      this.$store.dispatch('dataPaneStore/loadData')
    }
  }
}
</script>

<style>

.mnt-picker {
  z-index: 100;
}

/* .month-picker-input {
   padding: 0.4 1em !important;
   } */

.mnt-picker input {
  padding: 0.4rem 0.6rem !important;
}

/* .month-picker-input {
   }

   .month-picker-container input {
   padding: 0.4em 1em !important;
   }

   input {
   padding: 0.4em 1em !important;
   } */

</style>
