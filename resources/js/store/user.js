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
        async fetch(context, user = null) {
            if (!user) {
                const response = await api.get("/user");
            }

            const userForStore = user ? user : response.data

            context.commit("SET_USER", { user: userForStore });
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
