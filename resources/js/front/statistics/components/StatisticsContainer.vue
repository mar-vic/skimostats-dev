<template>
  <div>
    <div class="container position-relative py-5">
      <div class="bg-text">{{__('Statistics')}}</div>
      <h1 class="page-heading font-weight-bold text-uppercase pt-0 pt-md-5 text-blue mb-4">{{ selectedStatsCategory.shortName }}</h1>
      <categories></categories>
      <component :is="activeComponent" />
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

import Categories from './Categories.vue'

export default {
  components: {
    Categories
  },

  computed: {
    ...mapGetters(['selectedStatsCategory', 'activeComponent']) // mapping the selectedCategory getter from the store
  }

}
</script>
