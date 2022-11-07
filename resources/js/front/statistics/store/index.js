import axios from 'axios'

import dataPaneStore from './modules/dataPaneStore'
import dummyStore from './modules/dummyStore'

// import DataPaneRaceCats from '../components/data_pane/racecat/DataPaneRaceCats.vue'
import DataPane from '../components/data_pane/DataPane.vue'
import PlaceHolder from '../components/PlaceHolder.vue'

export default {

  // STORE MODULES
  modules: {
    dataPaneStore,
    dummyStore
  },

  // VUE COMPONENTS FOR DYNAMIC LOADING
  components: {
    DataPane,
    PlaceHolder
  },

  state () {
    return {
      error: '',
      loading: true,
      statsCategories: [

        {
          id: 'cat1',
          isSelected: true,
          shortName: 'victories',
          longName: 'Victories',
          metric: 'wins',
          dataSource: '/v1/statistics/mostWins',
          path: '/victories',
          component: DataPane,
          dataStore: 'dataPaneStore',
          filters: 'seasons'
        },

        {
          id: 'cat2',
          isSelected: false,
          shortName: 'race days',
          longName: 'Most race days',
          metric: 'race days',
          dataSource: '/v1/statistics/mostRaceDays',
          path: '/race-days',
          component: DataPane,
          dataStore: 'dataPaneStore',
          filters: 'seasons'
        },

        {
          id: 'cat3',
          isSelected: false,
          shortName: 'points per month',
          longName: 'Points per month',
          dataSource: '/v1/statistics/pointsPerMonth',
          metric: 'points',
          path: '/victories',
          component: DataPane,
          dataStore: 'dataPaneStore',
          filters: 'months'
        },

        {
          id: 'cat4',
          isSelected: false,
          shortName: 'points per age',
          longName: 'Points per age',
          dataSource: '/ppa',
          path: '/victories',
          component: PlaceHolder,
          dataStore: 'dummyStore'
        },

        {
          id: 'cat5',
          isSelected: false,
          shortName: 'vertical meters',
          longName: 'Vertical meters',
          metric: 'total elevation',
          dataSource: '/v1/statistics/mostVerticalMeters',
          path: '/victories',
          component: PlaceHolder,
          dataStore: 'dummStore',
          // filters: 'seasons'
        },

        {
          id: 'cat6',
          isSelected: false,
          shortName: 'grand course victories',
          longName: 'Grand Course Victories',
          metric: 'GC wins',
          dataSource: '/v1/statistics/mostGrandeCourseWins',
          path: '/victories',
          component: DataPane,
          dataStore: 'dataPaneStore',
          filters: 'seasons'

        },

        {
          id: 'cat7',
          isSelected: false,
          shortName: 'world cup victories',
          longName: 'World Cup Victories',
          metric: 'WC wins',
          dataSource: '/v1/statistics/mostWorldCupWins',
          path: '/victories',
          component: DataPane,
          dataStore: 'dataPaneStore',
          filters: 'seasons'
        },

        {
          id: 'cat8',
          isSelected: false,
          shortName: 'wins by countries',
          longName: 'Most wins by countries',
          dataSource: '/mwc',
          path: '/victories',
          component: PlaceHolder,
          dataStore: 'dummyStore'
        },

        {
          id: 'cat9',
          isSelected: false,
          shortName: 'chocolates',
          longName: 'Most chocolates',
          metric: 'chocolates',
          dataSource: '/v1/statistics/mostChocolates',
          path: '/v1/statistics/mostChocolates',
          component: DataPane,
          dataStore: 'dataPaneStore',
          filters: 'seasons'
        },

        {
          id: 'cat10',
          isSelected: false,
          shortName: 'Top 10 finishes',
          longName: 'Most Top 10 finishes',
          metric: 'toptens',
          dataSource: '/v1/statistics/mostTopTens',
          path: '/victories',
          component: DataPane,
          dataStore: 'dataPaneStore',
          filters: 'seasons'
        },

        {
          id: 'cat11',
          isSelected: false,
          shortName: 'number of nations',
          longName: 'The number of nations...',
          dataSource: '/nations',
          path: '/victories',
          component: PlaceHolder,
          dataStore: 'dummyStore',
        },

        {
          id: 'cat12',
          isSelected: false,
          shortName: 'active athletes',
          longName: 'Active athletes',
          dataSource: '/v1/statistics/activeAthletes',
          path: '/victories',
          component: DataPane,
          dataStore: 'dataPaneStore',
          filters: 'racecats'
        },

        {
          id: 'cat13',
          isSelected: false,
          shortName: 'different',
          longName: 'Number of different',
          dataSource: '/diffs',
          path: '/victories',
          component: PlaceHolder,
          dataStore: 'dummyStore'
        },

        {
          id: 'cat14',
          isSelected: false,
          shortName: 'nations raced in',
          longName: 'Most nations raced in',
          dataSource: '/nations',
          path: '/victories',
          component: PlaceHolder,
          dataStore: 'dummyStore'
        },

        {
          id: 'cat15',
          isSelected: false,
          shortName: 'youngest athletes',
          longName: 'Youngest athletes',
          dataSource: '/youngest',
          path: '/victories',
          component: PlaceHolder,
          dataStore: 'dummyStore'
        },

        {
          id: 'cat16',
          isSelected: false,
          shortName: 'oldest athletes',
          longName: 'Oldest athletes',
          dataSource: '/oldest',
          path: '/victories',
          component: PlaceHolder,
          dataStore: 'dummyStore'
        },

        {
          id: 'cat17',
          isSelected: false,
          shortName: 'Points per raceday',
          longName: 'Most points per raceday',
          dataSource: '/ppr',
          path: '/victories',
          component: PlaceHolder,
          dataStore: 'dummyStore'
        }
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
    }
  },

  getters: {
    selectedStatsCategory (state) {
      return state.statsCategories.find(category => category.isSelected)
    },

    activeComponent (state) {
      return state.statsCategories.find(category => category.isSelected).component
    },

    activeStoreModule(state) {
      return state.statsCategories.find(category => category.isSelected).dataStore
    },

    activeEndPoint(state) {
      return state.statsCategories.find(category => category.isSelected).dataSource
    },

    activeMetric(state) {
      return state.statsCategories.find(category => category.isSelected).metric
    },

    activeFilters(state) {
      return state.statsCategories.find(category => category.isSelected).filters
    }
  },

  actions: {
    async initStatistics ({ dispatch, commit, getters }) {
      commit('SET_LOADING', true)
      try {
        await Promise.all([
          dispatch('selectStatsCategory', 'cat1')
        ])
      } catch (e) {
        commit('SET_ERROR', e)
      }
      commit('SET_LOADING', false)
    },

    async selectStatsCategory ({ dispatch, commit, getters }, categoryId) {
      commit('SET_LOADING', true)
      commit('SET_STATS_CATEGORY', categoryId)
      try {
        await Promise.all([
          dispatch(getters.activeStoreModule + '/init')
        ])
      } catch (e) {
        commit('SET_ERROR', e)
      }
      commit('SET_LOADING', false)
    }
  }
}
