import api from "../api";

export default {
    namespaced: true,
    state() {
        return {
            teams: []
        };
    },
    actions: {
        async fetch(context) {
            if (!context.rootGetters['user/isAdmin']) return;

            const response = await api.get("/teams");

            context.commit("SET_TEAMS", { teams: response.data });
        },
        async create(context, { organization, name, email, invite }) {
            const response = await api.post('/teams', { organization, name, email, invite: !!invite })

            /**
             * { team, owner }
             */
            return { ...response.data }
        }
    },
    mutations: {
        SET_TEAMS(state, { teams }) {
            state.teams.splice(0);
            state.teams.push(...teams);
        },
    }
};
