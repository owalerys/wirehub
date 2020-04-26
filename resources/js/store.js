import Vue from 'vue'
import Vuex from 'vuex'

import account from './store/account'
import link from './store/link'
import sanctum from './store/sanctum'

Vue.use(Vuex)

const store = new Vuex.Store({
    modules: {
        account,
        link,
        sanctum
    }
})

export default store
