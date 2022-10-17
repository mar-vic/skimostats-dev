import Vue from 'vue';
import axios from 'axios';

export default {
  namespaced: true,

  state() {
    return {
      endPoint: null,
      data: null,
      athleteCategory: null,
      highlightedPosition: 0,
      filters: null,
      selectedFilter: 0,
      noDataForFilter: false,
    };
  },

  mutations: {

    SET_DATA (state, data) {
      state.data = data
    },

    SET_ATHLETE_CATEGORY (state, athleteCategory) {
      state.athleteCategory = athleteCategory
    },

    SET_HIGHLIGHTED_POSITION (state, position) {
      state.highlightedPosition = position
    },

    SET_HIGHLIGHTED_ATHLETE (state, athlete) {
      state.highlightedAthlete = athlete
    },

    SET_FILTERS (state, filters) {
      state.filters = filters
    },

    SET_SELECTED_FILTER (state, filter) {
      state.selectedFilter = filter
    }
  },

  getters: {
    athleteCategories (state) {
      return Object.keys(state.data)
    },

    filteredData (state) {
      return state.data[state.athleteCategory]
    },

    highlightedAthlete (state) {
      return state.data[state.athleteCategory][state.highlightedPosition]
    }
  },

  actions: {
    async init({ dispatch, commit, rootGetters }) {
      try {
        await Promise.all([
          dispatch('loadFilters'),
          dispatch('loadData')
        ])
      } catch (e) {
        commit('SET_ERROR', e, { root: true })
      }
    },

    async loadFilters({ dispatch, commit, rootGetters }) {
      const filters = rootGetters.activeFilters

      if (filters === 'seasons') {
        try {
          commit('SET_SELECTED_FILTER', new Date().getFullYear())
          const { data } = await axios.get("/v1/statistics/years")
          commit('SET_FILTERS', ['season', data])
          commit('SET_ERROR', '', { root: true })
        } catch (e) {
          commit('SET_ERROR', e, { root: true })
        }
      } else if (filter === 'racecats') {
        commit('SET_FILTERS', ['race category', ['All', 'World cup', 'National cup']])
      }
    },

    async loadData ({ commit, getters, state, rootGetters }) {
      commit('SET_HIGHLIGHTED_POSITION', 0)

      try {
        const fullEndPoint = rootGetters.activeEndPoint + (state.selectedFilter === 0 ? '' : '/' + state.selectedFilter)
        const { data } = await axios.get(fullEndPoint)
        if (Array.isArray(data) && data.length === 0) {
          state.noDataForFilter = true
        } else {
          state.noDataForFilter = false
          commit('SET_DATA', data)
          commit('SET_ATHLETE_CATEGORY', getters.athleteCategories[0])
        }
        commit('SET_ERROR', '', { root: true })
      } catch (e) {
        commit('SET_ERROR', { root: true  })
      }
    }
  }
}
