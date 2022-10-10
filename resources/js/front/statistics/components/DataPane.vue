<template>
  <div>
    <div v-if="error" class="alert alert-danger mb-0">{{error}}</div>
    <div v-else-if="loading" class="p-5 text-center">
      <img src="/images/loading.svg" alt="Loading...">
    </div>
    <div v-else>
      <data-pane-header></data-pane-header>
      <div v-if="!noDataForSeason" class="row">
        <div class="col-3">
          <data-pane-profile-card></data-pane-profile-card>
        </div>
        <div class="col-9">
          <data-pane-table></data-pane-table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import DataPaneHeader from './DataPaneHeader.vue'
import DataPaneTable from './DataPaneTable.vue'
import DataPaneProfileCard from './DataPaneProfileCard.vue'

import { mapState } from 'vuex'

export default {
  components: {
    DataPaneHeader,
    DataPaneTable,
    DataPaneProfileCard
  },

  computed: {
    ...mapState(['error', 'loading']),
    ...mapState('dataPaneStore', ['noDataForSeason'])
  }
}
</script>

<style scoped>

</style>
