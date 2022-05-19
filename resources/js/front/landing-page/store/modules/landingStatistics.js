export default {
    namespaced: true,

    state: {
        birthdays: [],
        popularAthletes: [],
    },

    mutations: {
        FILL_DATA(state, { birthdays, popularAthletes }) {
            state.birthdays = birthdays
            state.popularAthletes = popularAthletes
        },
        SET_POPULAR_ATHLETES(state, athletes) {
            state.popularAthletes = athletes
        }
    },

    actions: {
        init({ commit }, data) {
            commit('FILL_DATA', data)
        },
    },

    getters: {
       birthdays(state) {
           return state.birthdays
       },
       popularAthletes(state) {
           return state.popularAthletes
       },
    },
}
