<template>
    <div>
        <athlete-races />
        <career-wins />
        <ranking-strip />
    </div>
</template>

<script>
import RankingStrip from './RankingStrip.vue'
import AthleteRaces from './AthleteRaces.vue'
import CareerWins from './CareerWins.vue'

export default {
  components: {
    AthleteRaces,
    CareerWins,
    RankingStrip
  }
}
</script>
