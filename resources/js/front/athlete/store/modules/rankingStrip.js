import axios from 'axios'

export default {
    namespaced: true,

    state() {
        return {
            rankingPerSeason: [],
            racesPerCountry: [],
            error: '',
        }
    },

    mutations: {
        SET_RANKING_PER_SEASON(state, payload) {
            state.rankingPerSeason = payload
        },

        SET_RACES_PER_COUNTRY(state, payload) {
            state.racesPerCountry = payload
        },

        SET_ERROR(state, payload) {
            state.error = payload
        }
    },

    actions: {
        async init({ dispatch, commit }, athleteId) {
            try {
                await Promise.all([
                    dispatch('loadRankingPerSeason', athleteId),
                    dispatch('loadRacesPerCountry', athleteId)
                ])
            } catch(e) {
                commit('SET_ERROR', e)
            }
        },

        async loadRankingPerSeason({ commit }, athleteId) {
            const { data } = await axios.get(`/v1/athlete/${athleteId}/ranking-per-season`)
            commit('SET_RANKING_PER_SEASON', data)
        },

        async loadRacesPerCountry({ commit }, athleteId) {
            const { data } = await axios.get(`/v1/athlete/${athleteId}/races-per-country`)
            commit('SET_RACES_PER_COUNTRY', data)
        },
    },
}
