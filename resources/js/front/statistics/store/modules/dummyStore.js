import Vue from 'vue';
import axios from 'axios';

export default {
  namespaced: true,

  state() {
    return {
    };
  },

  mutations: {
  },

  getters: {
  },

  actions: {
    async init({ dispatch, commit }) {
      console.log('At dummy store')
    }
  }
}
