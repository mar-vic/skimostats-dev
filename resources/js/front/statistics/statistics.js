require('../../bootstrap')

import Vue from 'vue'
import Vuex from 'vuex'
import StatsContainer from './components/StatisticsContainer.vue'
import theStore from './store'

Vue.use(Vuex)
const store = new Vuex.Store(theStore)

window.statisticsVM = new Vue({

  components: {
    StatsContainer
  },

  store,

  template: '<stats-container></stats-container>',

  methods: {
    init () {
      this.$store.dispatch('initStatistics')
    }
  }

}).$mount('#statistics-vm-container')
