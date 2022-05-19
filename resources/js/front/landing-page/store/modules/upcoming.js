export default {
    namespaced: true,

    state: {
        races: [],
        categories: [],
    },

    mutations: {
        SET_RACES(state, races) {
            state.races = races
        },
        SET_CATEGORIES(state, categories) {
            state.categories = categories
        },
    },

    getters: {
        races(state) { return state.races },
        categories(state) { return state.categories },
    },

    actions: {
        init({commit}, {races, categories}) {
            commit('SET_RACES', races)
            commit('SET_CATEGORIES', categories)
        },
    }
}
