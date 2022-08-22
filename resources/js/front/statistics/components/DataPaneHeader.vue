<template>
  <div>
    <div class="row mb-4 justify-content-between">
      <div class="col-3 mr-auto"><h2 class="font-weight-bold text-uppercase text-blue">{{ selectedCategory.shortName }}</h2></div>
      <div class="col-9 col-auto">
        <div class="row justify-content-end">
          <div class="col col-auto pl-1 pr-1">
            <button class="badge my-1 badge--custom dropdown__toggle ">
              Season 2021 / 2022
              <i class="fas fa-caret-down" aria-hidden="true"></i>
            </button>
          </div>
          <div class="col col-auto pl-1 pr-1">
            <button class="badge my-1 badge--custom dropdown__toggle ">
              25 results / page
              <i class="fas fa-caret-down" aria-hidden="true"></i>
            </button>
          </div>
        </div>
        <div class="row justify-content-end">
          <div class="col col-auto pl-1 pr-1" v-for="raceCategory in raceCategories">
            <a class="badge my-1 badge--custom badge-active" href="#">{{raceCategory}}</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapState } from 'vuex'

export default {
  // components: {
  //   AthleteRaces,
  //   CareerWins,
  //   RankingStrip
  // }
  computed: {
    ...mapState(['error', 'loading']),
    ...mapGetters(['selectedCategory', 'raceCategories'])
  }
}
</script>

<style scoped>

</style>
