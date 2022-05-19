import Vue from 'vue';
import axios from 'axios';

export default {
     namespaced: true,

     state() {
         return {
             careerWins: [],
             error: '',
         };
     },

    mutations: {
        SET_WINS(state, payload) {
            state.careerWins = payload;
        },

        SET_ERROR(state, e) {
            state.error = e;
        }
     },

     actions: {
         async init({ dispatch, commit }, athleteId) {
             try {
                 await Promise.all([
                     dispatch('loadWins', athleteId)
                 ]);
             } catch(e) {
                 commit('SET_ERROR', e);
             }
         },

         async loadWins({commit}, athleteId) {
             const { data } = await axios.get(`/v1/athlete/${athleteId}/career-wins`);
             commit ('SET_WINS', data);
         }
     },
}
