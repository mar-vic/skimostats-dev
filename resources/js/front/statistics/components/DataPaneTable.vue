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
          <tr v-for="(datum, index) in data">
            <td>{{index + 1}}.</td>
            <td>{{datum.firstName}} {{datum.lastName}}</td>
            <td style="text-align:right">{{datum.numberOfWins}}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex'
export default {
  // components: {
  //   AthleteRaces,
  //   CareerWins,
  //   RankingStrip
  // }
  computed: {
    ...mapState(['data', 'error', 'loading'])
  }
}
</script>

<style scoped>

</style>
