import Vue from 'vue'
import Vuex from 'vuex'

import account from './store/account'
import link from './store/link'
import sanctum from './store/sanctum'
import team from './store/team'

Vue.use(Vuex)

const store = new Vuex.Store({
    modules: {
        account,
        link,
        sanctum,
        team
    }
})

export default store
