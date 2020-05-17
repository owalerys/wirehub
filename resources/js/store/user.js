import api, { withoutBase } from "../api";
import Vue from 'vue';

export default {
    namespaced: true,
    state() {
        return {
            user: {}
        };
    },
    getters: {
        roles: state => {
            return state.user.roles.map(role => role.name);
        },
        isAdmin: (state, getters) => {
            return (
                getters.roles.includes("admin") ||
                getters.roles.includes("super-admin")
            );
        },
        isTeamMember: (state, getters) => {
            return (
                getters.roles.includes("owner") ||
                getters.roles.includes("member")
            );
        }
    },
    actions: {
        async fetch(context, user = null) {
            if (!user) {
                const response = await api.get("/user");
            }

            const userForStore = user ? user : response.data;

            context.commit("SET_USER", { user: userForStore });
        },
        async logout() {
            const respones = await withoutBase.post("/logout");
        }
    },
    mutations: {
        SET_USER(state, { user }) {
            Vue.set(state, "user", user);
        }
    }
};
