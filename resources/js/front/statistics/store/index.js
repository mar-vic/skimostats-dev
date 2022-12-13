import axios from 'axios'

import dataPaneStore from './modules/dataPaneStore'
import dummyStore from './modules/dummyStore'

// import DataPaneRaceCats from '../components/data_pane/racecat/DataPaneRaceCats.vue'
import DataPane from '../components/data_pane/DataPane.vue'
import DataPaneCountries from '../components/data_pane/DataPaneCountries.vue'
import PlaceHolder from '../components/PlaceHolder.vue'
import Histogram from '../components/Histogram.vue'

export default {

  // STORE MODULES
  modules: {
    dataPaneStore,
    dummyStore
  },

  // VUE COMPONENTS FOR DYNAMIC LOADING
  components: {
    DataPane,
    DataPaneCountries,
    PlaceHolder,
    Histogram,
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
          tooltip: '',
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
          longName: 'Race days',
          metric: 'race days',
          tooltip: '',
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
          tooltip: '',
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
          tooltip: '',
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
          tooltip: '',
          dataSource: '/v1/statistics/mostVerticalMeters',
          path: '/victories',
          component: PlaceHolder,
          dataStore: 'dummyStore',
          // filters: 'seasons'
        },

        {
          id: 'cat6',
          isSelected: false,
          shortName: 'grand course victories',
          longName: 'Grand Course Victories',
          metric: 'GC wins',
          tooltip: '',
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
          tooltip: '',
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
          longName: 'Wins by countries',
          dataSource: '/v1/statistics/winsByCountries',
          metric: 'wins',
          tooltip: '',
          path: '/victories',
          component: DataPaneCountries,
          dataStore: 'dataPaneStore',
          filters: 'seasons'
        },

        {
          id: 'cat9',
          isSelected: false,
          shortName: 'chocolates',
          longName: 'Chocolates',
          metric: 'chocolates',
          tooltip: '',
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
          longName: 'Top 10 finishes',
          metric: 'top tens',
          tooltip: '',
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
          longName: 'Nations scoring skimo stats',
          dataSource: '/nations',
          tooltip: '',
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
          metric: 'recent races',
          tooltip: 'Active athlete is one having at least one raceday in the last 14 months.',
          component: DataPane,
          dataStore: 'dataPaneStore',
          filters: 'racecats'
        },

        {
          id: 'cat13',
          isSelected: false,
          shortName: 'different',
          longName: 'Winners by countries',
          dataSource: '/v1/statistics/winnersByCountries',
          path: '/victories',
          metric: 'winners',
          tooltip: 'The number is the amount of winners representing the country.',
          component: DataPaneCountries,
          dataStore: 'dataPaneStore',
          filters: 'seasons'
        },

        {
          id: 'cat14',
          isSelected: false,
          shortName: 'nations raced in',
          longName: 'Nations raced in',
          dataSource: '/v1/statistics/mostNationsRacedIn',
          metric: 'nations raced in',
          tooltip: '',
          path: '/victories',
          component: DataPane,
          dataStore: 'dataPaneStore',
          filters: 'seasons'
        },

        {
          id: 'cat15',
          isSelected: false,
          shortName: 'youngest athletes',
          longName: 'Youngest athletes',
          dataSource: '/v1/statistics/youngestAthletes',
          metric: 'age',
          tooltip: 'Only athletes having at least one raceday in the last 14 months are considered.',
          path: '/victories',
          component: DataPane,
          dataStore: 'dataPaneStore',
          filters: 'racecats'
        },

        {
          id: 'cat16',
          isSelected: false,
          shortName: 'oldest athletes',
          longName: 'Oldest athletes',
          dataSource: '/v1/statistics/oldestAthletes',
          metric: 'age',
          tooltip: 'Only athletes having at least one raceday in the last 14 months are considered.',
          path: '/victories',
          component: DataPane,
          dataStore: 'dataPaneStore',
          filters: 'racecats'
        },

        {
          id: 'cat17',
          isSelected: false,
          shortName: 'Points per raceday',
          longName: 'Points per raceday',
          dataSource: '/v1/statistics/mostPointsPerRaceDay',
          metric: 'points / raceday',
          tooltip: 'Only athletes with at least 5 racedays are considered.',
          path: '/victories',
          component: DataPane,
          dataStore: 'dataPaneStore',
          filters: 'seasons'
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
