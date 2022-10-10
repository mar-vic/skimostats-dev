<template>
  <div>
    <div>
      <table class="table table--races table--races-striped">
        <thead>
          <tr>
            <th style="width:10%">#</th>
            <th style="width:70%">Name</th>
            <th style="width:20%;text-align:right">{{selectedStatsCategory.metric}}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(datum, index) in filteredData"
              :key="datum.athleteId"
              :class="{'highlighted-row': index === highlightedPosition}"
              @click.prevent="highlight($event, index)"
          >
            <td>{{index + 1}}.</td>
            <td>{{datum.firstName}} {{datum.lastName}}</td>
            <td style="text-align:right">{{datum.qty}}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import { mapState, mapGetters } from 'vuex'
export default {
  computed: {
    ...mapState('dataPaneStore', ['highlightedPosition']),
    ...mapGetters(['selectedStatsCategory']), // map a getter from store root
    ...mapGetters('dataPaneStore', ['filteredData', 'highlightedAthlete']) // map getters from the 'dataPaneStore' module
  },

  methods: {
    highlight (event, position) {
      this.$store.commit("dataPaneStore/SET_HIGHLIGHTED_POSITION", position)
    }
  }

}
</script>

<style scoped>
tr:hover {
  cursor: pointer;
}

.highlighted-row {
  background-color: #fce000 !important;
  font-weight: bold;
}
</style>
