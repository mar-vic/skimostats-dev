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
          <tr v-for="(datum, index) in filteredData" :key="datum.athleteId">
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
    ...mapState(['data', 'error', 'loading']),
    ...mapGetters(['filteredData'])
  }
}
</script>

<style scoped>

</style>
