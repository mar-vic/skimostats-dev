<template>
  <div class="card" style="">
    <img class="card-img-top" :src="'/images/athletes/' + highlightedAthlete.profilePic" alt="Card image cap">
    <div class="card-body">
      <h5 class="card-title">
        {{highlightedAthlete.firstName}} {{highlightedAthlete.lastName}}
      </h5>
      <p class="card-text">{{highlightedAthlete.winsCount}} victories</p>
      <a :href="'/athlete/' + highlightedAthlete.slug" class="btn btn-primary">View profile</a>
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
    ...mapGetters(['highlightedAthlete'])
  }
}
</script>

<style scoped>

</style>
