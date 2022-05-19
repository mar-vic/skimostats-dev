/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('../../bootstrap')

import Vue from 'vue'
import Vuex from 'vuex'
import VueRouter from 'vue-router'
import RacesContainer from './components/RacesContainer.vue'
import theStore from './store'

Vue.use(Vuex)
Vue.use(VueRouter)
const store = new Vuex.Store(theStore)
const router = new VueRouter({
    mode: 'history',
    routes: [
        { path: '/races', name: 'races', component: RacesContainer },
        { path: '/races/:year', name: 'races.year', component: RacesContainer },
        { path: '/races/:year/:month', name: 'races.month', component: RacesContainer },
    ],
})

window.racesVM = new Vue({
    components: {
        RacesContainer,
    },
    router,
    store,
    template: '<router-view></router-view>',
    methods: {
        setData(data) {
            if (data.races) {
                this.$store.commit('SET_RACES', data.races)
            }
        },
    },
}).$mount('#races-container')
