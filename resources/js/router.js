import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

import AccountCreate from './pages/accounts/Create.vue'
import AccountList from './pages/accounts/List.vue'

const router = new VueRouter({
    mode: 'history',
    base: 'app',
    routes: [
        {
            path: '/',
            redirect: {
                name: 'account-list'
            }
        },
        {
            path: '/accounts',
            component: AccountList,
            name: 'account-list'
        },
        {
            path: '/accounts/create',
            component: AccountCreate,
            name: 'account-create'
        }
    ]
})

export default router
