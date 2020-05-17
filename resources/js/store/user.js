import api, { withoutBase } from "../api";
import Vue from 'vue';

export default {
    namespaced: true,
    state() {
        return {
            user: {}
        };
    },
    actions: {
        async fetch(context) {
            const response = await api.get("/user");

            context.commit("SET_USER", { user: response.data });
        },
        async logout() {
            const respones = await withoutBase.post('/logout');
        }
    },
    mutations: {
        SET_USER(state, { user }) {
            Vue.set(state, 'user', user);
        }
    }
};
