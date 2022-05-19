import Vue from 'vue'
import axios from 'axios'

export default {
     namespaced: true,

     state() {
         return {
             year: null,
             races: [],
             years: [],
             cachedRaces: {},
             error: '',
             loading: false,
         }
     },

     mutations: {
        SET_ERROR(state, e) {
            state.error = e
        },

        SET_RACES(state, { year, races }) {
            state.year = year
            state.races = races
            state.cachedRaces[year] = races
        },

        SET_YEARS(state, years) {
            state.years = years
        },

        SET_YEAR(state, year) {
            state.year = year
        },

        SET_LOADING(state, loading) {
            state.loading = Boolean(loading)
        },
     },

     actions: {
         async init({commit, dispatch}, athleteId) {
            commit ('SET_LOADING', true)
            try {
                const { data: years } = await axios.get(`/v1/athlete/${athleteId}/race-year-list`)

                if (years.length) {
                    commit('SET_YEARS', years)
                    await dispatch('loadRaces', { athleteId, year: years[0]})
                }
                commit ('SET_LOADING', false)
            } catch(e) {
                commit('SET_ERROR', e)
                commit ('SET_LOADING', false)
            }
         },

         async loadRaces({ commit, state }, { athleteId, year }) {
            if (state.cachedRaces[year]) {
                commit('SET_RACES', {
                    year,
                    races: state.cachedRaces[year],
                })
                return
            }
            const { data: racesResult } = await axios.get(`/v1/athlete/${athleteId}/races/${year}`)
            commit('SET_RACES', racesResult)
         },
     },
}
