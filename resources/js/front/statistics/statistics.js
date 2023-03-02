require('../../bootstrap')

import Vue from 'vue'
import Vuex from 'vuex'
import StatsContainer from './components/StatisticsContainer.vue'
import theStore from './store'

Vue.use(Vuex)
const store = new Vuex.Store(theStore)

// Localization
import _ from 'lodash'
Vue.prototype.trans = string => _.get(window.i18n, string, string);
Vue.prototype.__ = string => _.get(window.i18n, string, string);

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
