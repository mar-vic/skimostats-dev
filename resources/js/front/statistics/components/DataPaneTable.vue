<template>
  <div>
    <div>
      <table class="table table--races table--races-striped">
        <thead>
          <tr>
            <th style="width:10%">#</th>
            <th style="width:70%">Name</th>
            <th style="width:20%;text-align:right">No. of wins</th>
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
            <td style="text-align:right">{{datum.winsCount}}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import { mapState, mapGetters } from 'vuex'
export default {
  // components: {
  //   AthleteRaces,
  //   CareerWins,
  //   RankingStrip
  // }
  computed: {
    ...mapState(['highlightedPosition']),
    ...mapGetters(['filteredData', 'highlightedAthlete'])
  },

  methods: {
    highlight (event, position) {
      this.$store.commit("SET_HIGHLIGHTED_POSITION", position)
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
