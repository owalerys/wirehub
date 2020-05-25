import Vue from 'vue'
import { sync } from 'vuex-router-sync'

import vuetify from './plugins/vuetify'
import router from './router'
import store from './store'

import App from './App.vue'

import './filters'

require('typeface-roboto')

const unsync = sync(store, router)

new Vue({
    router,
    store,
    vuetify,
    render: h => h(App)
}).$mount('#app')
