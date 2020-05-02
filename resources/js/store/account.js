import Vue from 'vue'

import api from '../api'

export default {
    namespaced: true,
    state() {
        return {
            accounts: [],
        }
    },
    actions: {
        async fetch(context) {
            const response = await api.get('/accounts')

            context.commit('SET_ACCOUNTS', { accounts: response.data })
        }
    },
    mutations: {
        SET_ACCOUNTS(state, { accounts }) {
            state.accounts.splice(0)
            state.accounts.push(...accounts)
        },
    }
}
