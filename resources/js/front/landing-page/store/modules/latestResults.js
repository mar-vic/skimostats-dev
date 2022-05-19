export default {
    namespaced: true,

    state: {
        events: [],
        activeEvent: null,
    },

    mutations: {
        FILL_EVENTS(state, events) {
            state.events = events
        },
        SET_ACTIVE_EVENT(state, index) {
            state.activeEvent = index
        },
    },

    actions: {
        init({ commit }, events) {
            commit('FILL_EVENTS', events)
            if (events.length) {
                commit('SET_ACTIVE_EVENT', 0)
            }
        },
        nextEvent({ commit, state }) {
            commit('SET_ACTIVE_EVENT', state.events.length - 1 === state.activeEvent ? 0 : state.activeEvent + 1 )
        },
        prevEvent({ commit, state }) {
            commit('SET_ACTIVE_EVENT', state.activeEvent === 0 ? state.events.length - 1 : state.activeEvent - 1 )
        },
    },

    getters: {
        eventCount(state) {
            return state.events.length
        },
        events(state) {
            return state.events
        },
        activeEvent(state) {
            return state.events[state.activeEvent]
        },
        activeEventPosition(state) {
            return state.activeEvent + 1
        }
    },
}
