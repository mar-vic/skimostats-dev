import Vue from 'vue'
import axios from 'axios'

export default {
  namespaced: true,

  state () {
    return {
      year: null,
      races: [],
      years: [],
      cachedRaces: {},
      error: '',
      loading: false,
      raceDays: null,
      cachedRaceDays: {},
      seasonSummary: {},
      cachedSummary: {}
    }
  },

  mutations: {
    SET_ERROR (state, e) {
      state.error = e
    },

    SET_RACES (state, { year, races }) {
      state.year = year
      state.races = races
      state.cachedRaces[year] = races
    },

    SET_RACE_DAYS (state, { year, raceDays }) {
      state.raceDays = raceDays
      state.cachedRaceDays[year] = raceDays
    },

    SET_SEASON_SUMMARY (state, { year, seasonSummary }) {
      state.seasonSummary = seasonSummary
      state.cachedSummary[year] = seasonSummary
    },

    SET_YEARS (state, years) {
      state.years = years
    },

    SET_YEAR (state, year) {
      state.year = year
    },

    SET_LOADING (state, loading) {
      state.loading = Boolean(loading)
    }
  },

  actions: {
    async init ({ commit, dispatch }, athleteId) {
      commit('SET_LOADING', true)
      try {
        const { data: years } = await axios.get(`/v1/athlete/${athleteId}/race-year-list`)

        if (years.length) {
          commit('SET_YEARS', years)
          await dispatch('loadRaces', { athleteId, year: years[0] })
          await dispatch('loadSeasonSummary', { athleteId, year: years[0] })
        }
        commit('SET_LOADING', false)
      } catch (e) {
        commit('SET_ERROR', e)
        commit('SET_LOADING', false)
      }
    },

    async loadRaces({ commit, state }, { athleteId, year }) {
      if (state.cachedRaces[year]) {
        commit('SET_RACES', {
          year,
          races: state.cachedRaces[year]
        })
        return
      }
      const { data: racesResult } = await axios.get(`/v1/athlete/${athleteId}/races/${year}`)
      commit('SET_RACES', racesResult)
    },

    async loadRaceDays ({ commit, state }, { athleteId, year }) {
      if (state.cachedRaceDays[year]) {
        commit('SET_RACE_DAYS', {
          year,
          races: state.cachedRaceDays[year]
        })
      }
      const { data } = await axios.get(`/v1/athlete/${athleteId}/race-days/${year}`)
      commit('SET_RACE_DAYS', { year, raceDays: data.raceDays })
    },

    async loadSeasonSummary ({ commit, state }, { athleteId, year }) {
      if (state.cachedSummary[year]) {
        commit('SET_SEASON_SUMMARY', {
          year,
          seasonSummary: state.cachedSummary[year]
        })
      }
      const { data } = await axios.get(`/v1/athlete/${athleteId}/seasonSummary/${year}`)
      commit('SET_SEASON_SUMMARY', { year, seasonSummary: data })
    }
  }
}
