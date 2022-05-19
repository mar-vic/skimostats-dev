require('../../bootstrap')

import Vue from 'vue'
import Vuex from 'vuex'
import AthleteComponent from './components/Athlete.vue'
import theStore from './store'

Vue.use(Vuex)
const store = new Vuex.Store(theStore)

window.athleteVM = new Vue({
    components: {
        AthleteComponent,
    },
    store,
    template: '<athlete-component></athlete-component>',
    methods: {
        init(athleteId) {
            this.$store.dispatch('initAthlete', {
                athleteId,
            })
        },
    },
}).$mount('#athlete-vm-container')
