/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('../../bootstrap')

import Vue from 'vue'
import Vuex from 'vuex'
import LandingPage from './components/LandingPage.vue'
import theStore from './store'

Vue.use(Vuex)
const store = new Vuex.Store(theStore)

window.landingPageVM = new Vue({
    components: {
        LandingPage,
    },
    store,
    template: '<landing-page></landing-page>',
    methods: {
        setData(data) {
            if (data.events) {
                this.$store.dispatch('latestResults/init', data.events)
            }
            if (data.upcoming) {
                this.$store.dispatch('upcoming/init', data.upcoming)
            }

            this.$store.dispatch('landingStatistics/init', data)
        },
    },
}).$mount('#landing-page-container')
