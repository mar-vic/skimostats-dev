export default {
  state: {
    event: {},
    results: [],
    category: {},
    stage: false,
    isGeneralClassification: false,
    gcWinningTimes: {},
  },

  mutations: {
    SET_EVENT(state, event) {
      state.event = event
    },

    SET_RESULTS(state, results) {
      state.results = results

      if (results.length) {
        state.category = state.results[0]
      }
    },

    SET_CATEGORY(state, category) {
      state.category = category
    },

    SET_STAGE(state, stage) {
      state.stage = stage
    },

    SET_IS_GENERAL_CLASSIFICATION(state, isGeneralClassification) {
      state.isGeneralClassification = isGeneralClassification
    },

    SET_GC_WINNING_TIMES(state, gcWinningTimes) {
      state.gcWinningTimes = gcWinningTimes
    },

    // SHOW_GENERAL_CLASSIFICATION(state) {
    //   state.showGeneralClassification = true
    // },

    // SET_GENERAL_CLASSIFICATION_RESULTS(state, res) {
    //   state.generalClassificationResults = res
    // }
  },

  getters: {
    selectedCategory(state) {
      return state.category
    },

    // gcWinningTime(state) {
    //   return state.gcWinningTimes[state.category.name]
    // }
  },
}
