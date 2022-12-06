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
      loading: false
    };
  },

  mutations: {

    SET_LOADING (state, flag) {
      state.loading = flag
    },

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
    },

    selectedFilterSlug (state) {
      return state.selectedFilter.toString().split(" ").join("-").toLowerCase().replace(',', '/')
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
      } else if (filters === 'racecats') {
        commit('SET_SELECTED_FILTER', 0)
        commit('SET_FILTERS', ['race category', ['World Cup', 'Grand Course']])
      } else if (filters === 'months') {
        const currentDate = new Date()
        commit('SET_SELECTED_FILTER', [currentDate.getFullYear(), currentDate.getMonth() + 1])
        commit('SET_FILTERS', ['month', []])
      }
    },

    async loadData ({ commit, getters, state, rootGetters }) {

      commit('SET_HIGHLIGHTED_POSITION', 0)
      commit('SET_LOADING', true)

      try {
        const fullEndPoint = rootGetters.activeEndPoint + (getters.selectedFilterSlug === '0' ? '' : '/' + getters.selectedFilterSlug)

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
        commit('SET_ERROR', e, { root: true  })
      }

      commit('SET_LOADING', false)
    }
  }
}
