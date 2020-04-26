import api from '../api'

export default {
    namespaced: true,
    state() {
        return {}
    },
    actions: {
        async initializeCSRF(context) {
            await api.get('/sanctum/csrf-cookie', { baseURL: '' })
        }
    }
}
