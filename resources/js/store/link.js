import api from '../api'

export default {
    namespaced: true,
    state() {
        return {};
    },
    actions: {
        async exchangeToken(context, { publicToken }) {
            const response = await api.post("/items/plaid", {
                public_token: publicToken
            });

            console.log(response);
        },
        async saveLogin(context, { institution, loginId }) {
            const response = await api.post("/items/flinks", {
                login_id: loginId,
                institution: institution
            });

            console.log(response);
        }
    }
};
