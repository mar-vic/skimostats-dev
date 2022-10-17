<template>
  <div>
      <div class="alert alert-info">
          Sorry, but this part is under construction.
      </div>
  </div>
</template>

<script>

import { mapState } from 'vuex'

export default {
  computed: {
    ...mapState(['loading'])
  }
}
</script>

<style scoped>

</style>
