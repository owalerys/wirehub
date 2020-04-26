import api from '../api'

export default {
    namespaced: true,
    state() {
        return {}
    },
    actions: {
        async exchangeToken(context, { publicToken }) {
            const response = await api.post('/item/exchange', { public_token: publicToken })

            console.log(response)
        }
    }
}
