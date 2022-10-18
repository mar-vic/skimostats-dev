<template>
  <div>
    <div v-if="loading" class="p-1 text-center">
      <img src="/images/loading.svg" alt="Loading...">
    </div>

    <a v-else :href="`/athlete/${highlightedAthlete.slug}`" class="latest-results__first mb-3 profile-pic">
      <div class="latest-results__photo position-relative"
           :style="`background-image:url(${
             highlightedAthlete.profilePic ? '/images/athletes/' + highlightedAthlete.profilePic
             : highlightedAthlete.gender === 'female' ? '/images/woman_silhouette.jpg'
             : '/images/man_silhouette.jpg'
           });`">
        <div class="latest-results__number latest-results__number--first">{{highlightedPosition + 1}}</div>
      </div>
      <div class="latest-results__info">
        <div class="font-weight-bold">{{highlightedAthlete.firstName}} {{highlightedAthlete.lastName}}</div>
        <div class="small mb-2">{{activeMetric}}: {{highlightedAthlete.qty}}</div>
        <div class="d-flex justify-content-between align-items-center">
          <img
            v-if="highlightedAthlete.country"
            class="latest-results__mini-flag"
            :src="`/images/flags/flags-mini/${highlightedAthlete.country.toLowerCase()}.png`"
            :alt="highlightedAthlete.country"
          />
          <div class="latest-results__view-profile">View profile</div>
        </div>
      </div>
    </a>
  </div>
</template>

<script>
import { mapState, mapGetters } from 'vuex'
export default {
  computed: {
    ...mapState('dataPaneStore', ['highlightedPosition', 'loading']),
    ...mapGetters(['activeMetric']),
    ...mapGetters('dataPaneStore', ['highlightedAthlete'])
  }
}
</script>

<style scoped>

.profile-pic {
  position:  -webkit-sticky;
  position: sticky;
  top: 10px;
}

</style>
