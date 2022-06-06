import axios from 'axios';
import races from './modules/races';
import rankingStrip from './modules/rankingStrip';
import careerWins from './modules/careerWins';

export default {
    state() {
        return {
            error: '',
            athleteId: null,
            athlete: null,
            opponent: null,
        }
    },

    mutations: {
        SET_ERROR(state, e) {
            state.error = e
        },

        SET_ATHLETE_ID(state, id) {
            state.athleteId = id
        },

        SET_ATHLETE: (state, data) => state.athlete = data,

        SET_OPPONENT: (state, data) => state.opponent = data,
    },

    actions: {
        async fetchAthlete({ commit }, athleteId) {

            const { data } = await axios.get(`/v1/athlete/${athleteId}`)

            commit('SET_ATHLETE', data)
        },

        async fetchOpponent({ commit }, opponentId) {
            const { data } = await axios.get(`/v1/athlete/${opponentId}`)

            commit('SET_OPPONENT', data)
        },

        async getPredictions(_, query) {
            const { data } = await axios.post('/v1/athlete/predict', { q: query })
            return data
        },

        async initAthlete({ dispatch, commit }, { athleteId }) {
            commit('SET_ATHLETE_ID', athleteId)
            await Promise.all([
                dispatch('fetchAthlete', athleteId),
                dispatch('races/init', athleteId),
                dispatch('rankingStrip/init', athleteId),
                dispatch('careerWins/init', athleteId)
            ]);
        },
    },

    modules: {
        races,
        rankingStrip,
        careerWins,
    },
}
