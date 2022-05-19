export default {
    state: {
        event: {},
        results: [],
        category: {},
        showGeneralClassification: false,
        generalClassificationResults: [],
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
        SHOW_GENERAL_CLASSIFICATION(state) {
            state.showGeneralClassification = true
        },
        SET_GENERAL_CLASSIFICATION_RESULTS(state, res) {
            state.generalClassificationResults = res
        }
    },

    getters: {
        selectedCategory(state) {
            return state.category
        },
    },
}
