<template>
  <div>
    <div class="container position-relative py-5">
      <div class="bg-text">Statistics</div>
      <h1 class="page-heading font-weight-bold text-uppercase pt-0 pt-md-5 text-blue mb-4">{{ selectedCategory.shortName }}</h1>
      <categories></categories>
      <data-pane></data-pane>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

import Categories from './Categories.vue'
import DataPane from './DataPane.vue'

export default {
  components: {
    Categories,
    DataPane
  },

  computed: {
    ...mapGetters(['selectedCategory']) // mapping the selectedCategory getter from the store
  }

}
</script>
