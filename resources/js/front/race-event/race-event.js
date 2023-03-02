/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('../../bootstrap')

import Vue from 'vue'
import Vuex from 'vuex'
import VueRouter from 'vue-router'
import RaceEvent from './components/RaceEvent.vue'
import theStore from './store'

Vue.use(Vuex)
Vue.use(VueRouter)

const store = new Vuex.Store(theStore)

// Localization
import _ from 'lodash'
Vue.prototype.trans = string => _.get(window.i18n, string, string);
Vue.prototype.__ = string => _.get(window.i18n, string, string);

const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/event/:event/:category/gc',
            name: 'event.category.gc',
            component: RaceEvent,
        },
        {
            path: '/event/:event/:category',
            name: 'event.category',
            component: RaceEvent,
        },
        {
            path: '*',
            component: RaceEvent,
        },
    ],
})

window.raceEventVM = new Vue({
  components: {
    RaceEvent,
  },

  router,

  store,

  template: '<router-view></router-view>',

  methods: {

    setData(data) {
      if (data.event) {
        this.$store.commit('SET_EVENT', data.event)
      }

      if (data.results) {
        this.$store.commit('SET_RESULTS', data.results)

        // Generate a dictionary of gc winning times where applicable
        if (data.stage || data.isGeneralClassification) {
          let gcWinningTimes = {}

          data.results.forEach(element => {
            gcWinningTimes[element.name] = element.entries.map(element => element.gcTime).filter(element => element).sort((a, b) => a - b)[0]
          })

          this.$store.commit('SET_GC_WINNING_TIMES', gcWinningTimes)
        }
      }

      if (data.stage) {
        this.$store.commit('SET_STAGE', data.stage)
      }

      // if (data.isGeneralClassification == 1) {
      //   this.$store.commit('SET_IS_GENERAL_CLASSIFICATION', true)
      // }

      // if (data.showGeneralClassification) {
      //   this.$store.commit('SHOW_GENERAL_CLASSIFICATION')
      // }

      // if (data.generalClassificationResults) {
      //   this.$store.commit('SET_GENERAL_CLASSIFICATION_RESULTS', data.generalClassificationResults)
      // }
    }
  }
}).$mount('#race-event-container')
