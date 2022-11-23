<template>
  <div>
    <div v-if="loading" class="p-1 text-center">
      <img src="/images/loading.svg" alt="Loading...">
    </div>

    <table v-else class="table table--races table--races-striped">
      <thead>
        <tr>
          <th style="width:10%">#</th>
          <th style="width:70%">Country</th>
          <th style="width:20%;text-align:right">
            {{selectedStatsCategory.metric}}
            <span v-if="selectedStatsCategory.tooltip" class="ttip" data-toggle="tooltip" :title="selectedStatsCategory.tooltip">?</span>
          </th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(datum, index) in filteredData"
            :key="datum.athleteId"
            :class="{'highlighted-row': index === highlightedPosition}"
            @click.prevent="highlight($event, index)"
        >
          <td>{{index + 1}}.</td>
          <td>{{datum.countryName}}</td>
          <td style="text-align:right">{{datum.qty}}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import { mapState, mapGetters } from 'vuex'
export default {
  computed: {
    ...mapState('dataPaneStore', ['highlightedPosition', 'loading']),
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
tbody tr:hover {
  cursor: pointer;
}

.highlighted-row {
  background-color: #fce000 !important;
  font-weight: bold;
}

.ttip {
  cursor: help;
  background-color: #15174a;
  color: white;
  font-size: 11px;
  border-radius: 10px;
  padding-left: 5px;
  padding-right: 5px;
  margin-left: 2px;
  font-weight: bold;
}
</style>
