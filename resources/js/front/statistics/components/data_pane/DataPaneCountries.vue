<template>
  <div>
    <div v-if="error" class="alert alert-danger mb-0">{{error}}</div>
    <div v-else-if="loading" class="p-5 text-center">
      <img src="/images/loading.svg" alt="Loading...">
    </div>
    <div v-else>
      <data-pane-header></data-pane-header>
      <div v-if="!noDataForFilter" class="row">
        <div class="col-12">
          <data-pane-table></data-pane-table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import DataPaneHeader from './DataPaneHeader.vue'
import DataPaneTable from './DataPaneTableCountries.vue'

import { mapState } from 'vuex'

export default {
  components: {
    DataPaneHeader,
    DataPaneTable
  },

  computed: {
    ...mapState(['error', 'loading']),
    ...mapState('dataPaneStore', ['noDataForFilter'])
  }
}
</script>

<style scoped>

</style>
