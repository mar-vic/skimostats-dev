import axios from 'axios'

export default {
  state () {
    return {
      error: '',
      loading: true,
      data: null,
      raceCategory: null,
      seasons: null,
      selectedSeason: 0,
      resultsPerPage: 25,
      highlightedPosition: 0,
      statsCategories: [
        { id: 'cat1', isSelected: true, shortName: 'victories', longName: 'Victories', dataSource: '/v1/statistics/mostWins', path: '/victories' },
        { id: 'cat2', isSelected: false, shortName: 'race days', longName: 'Most race days', dataSource: '/racedays', path: '/victories' },
        { id: 'cat3', isSelected: false, shortName: 'points per month', longName: 'Points per month', dataSource: '/ppm', path: '/victories' },
        { id: 'cat4', isSelected: false, shortName: 'points per age', longName: 'Points per age', dataSource: '/ppa', path: '/victories' },
        { id: 'cat5', isSelected: false, shortName: 'vertical meters', longName: 'Most vertical meters', dataSource: '/vermeters', path: '/victories' },
        { id: 'cat6', isSelected: false, shortName: 'grand course victories', longName: 'Grand Course Victories', dataSource: '/gcvics', path: '/victories' },
        { id: 'cat7', isSelected: false, shortName: 'world cup victories', longName: 'World Cup Victories', dataSource: '/wcvic', path: '/victories' },
        { id: 'cat8', isSelected: false, shortName: 'wins by countries', longName: 'Most wins by countries', dataSource: '/mwc', path: '/victories' },
        { id: 'cat9', isSelected: false, shortName: 'chocolates', longName: 'Most chocolates', dataSource: '/mchocs', path: '/victories' },
        { id: 'cat10', isSelected: false, shortName: 'Top 10 finishes', longName: 'Most Top 10 finishes', dataSource: '/toptens', path: '/victories' },
        { id: 'cat11', isSelected: false, shortName: 'number of nations', longName: 'The number of nations...', dataSource: '/nations', path: '/victories' },
        { id: 'cat12', isSelected: false, shortName: 'active athletes', longName: 'Active athletes', dataSource: '/active_athletes', path: '/victories' },
        { id: 'cat13', isSelected: false, shortName: 'different', longName: 'Number of different', dataSource: '/diffs', path: '/victories' },
        { id: 'cat14', isSelected: false, shortName: 'nations raced in', longName: 'Most nations raced in', dataSource: '/nations', path: '/victories' },
        { id: 'cat15', isSelected: false, shortName: 'youngest athletes', longName: 'Youngest athletes', dataSource: '/youngest', path: '/victories' },
        { id: 'cat16', isSelected: false, shortName: 'oldest athletes', longName: 'Oldest athletes', dataSource: '/oldest', path: '/victories' },
        { id: 'cat17', isSelected: false, shortName: 'Points per raceday', longName: 'Most points per raceday', dataSource: '/ppr', path: '/victories' }
      ]
    }
  },

  mutations: {
    SET_ERROR (state, e) {
      state.error = e
    },

    SET_LOADING (state, loading) {
      state.loading = loading
    },

    SET_STATS_CATEGORY (state, categoryId) {
      state.statsCategories = state.statsCategories.map(category => {
        if (category.id === categoryId) {
          category.isSelected = true
        } else {
          category.isSelected = false
        }
        return category
      })
    },

    SET_RACE_CATEGORY (state, raceCategory) {
      state.raceCategory = raceCategory
    },

    SET_DATA (state, data) {
      state.data = data
    },

    SET_SEASONS (state, seasons) {
      state.seasons = seasons
    },

    SET_SELECTED_SEASON (state, season) {
      state.selectedSeason = season
    },

    SET_RESULTS_PER_PAGE (state, count) {
      state.resultsPerPage = count
    },

    SET_HIGHLIGHTED_POSITION (state, position) {
      state.highlightedPosition = position
    },

    SET_HIGHLIGHTED_ATHLETE (state, athlete) {
      state.highlightedAthlete = athlete
    }
  },

  getters: {
    selectedStatsCategory (state) {
      return state.statsCategories.find(category => category.isSelected)
    },

    raceCategories (state) {
      return Object.keys(state.data)
    },

    filteredData (state) {
      return state.data[state.raceCategory]
    },

    highlightedAthlete (state) {
      return state.data[state.raceCategory][state.highlightedPosition]
    }
  },

  actions: {
    async initStatistics ({ dispatch, commit, getters }) {
      commit('SET_SELECTED_SEASON', new Date().getFullYear())
      try {
        await Promise.all([
          dispatch('loadSeasons'),
          dispatch('selectStatsCategory', 'cat1')
        ])
      } catch (e) {
        commit('SET_ERROR', e)
      }
    },

    async selectStatsCategory ({ dispatch, commit, getters }, categoryId) {
      commit('SET_LOADING', true)
      commit('SET_STATS_CATEGORY', categoryId)
      try {
        await Promise.all([
          dispatch('loadData')
        ])
      } catch (e) {
        commit('SET_ERROR', e)
      }
      commit('SET_LOADING', false)
    },

    async loadData ({ commit, getters }) {
      commit('SET_HIGHLIGHTED_POSITION', 0)
      try {
        const endPoint = getters.selectedStatsCategory.dataSource + (this.state.selectedSeason === 0 ? '' : '/' + this.state.selectedSeason)
        const { data } = await axios.get(endPoint)
        commit('SET_DATA', data)
        commit('SET_RACE_CATEGORY', getters.raceCategories[0])
        commit('SET_ERROR', '')
      } catch (e) {
        commit('SET_ERROR', e)
      }
    },

    async loadSeasons ({ commit }) {
      try {
        const { data } = await axios.get("/v1/statistics/years")
        commit('SET_SEASONS', data)
        commit('SET_ERROR', '')
      } catch (e) {
        commit('SET_ERROR', e)
      }
    }
  }
}
