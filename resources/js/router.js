import Vue from "vue";
import VueRouter from "vue-router";

Vue.use(VueRouter);

import AccountCreate from "./pages/accounts/Create.vue";
import AccountDetail from "./pages/accounts/Detail.vue";
import AccountList from "./pages/accounts/List.vue";

import TeamCreate from "./pages/teams/Create.vue";
import TeamDetail from "./pages/teams/Detail.vue";
import TeamList from "./pages/teams/List.vue";

const router = new VueRouter({
    mode: "history",
    base: "app",
    routes: [
        {
            path: "/",
            redirect: {
                name: "account-list"
            }
        },
        {
            path: "/accounts",
            component: AccountList,
            name: "account-list"
        },
        {
            path: "/accounts/create",
            component: AccountCreate,
            name: "account-create"
        },
        {
            path: "/accounts/:accountId",
            component: AccountDetail,
            name: "account-detail"
        },
        {
            path: "/teams",
            component: TeamList,
            name: "team-list"
        },
        {
            path: "/teams/create",
            component: TeamCreate,
            name: "team-create"
        },
        {
            path: "/teams/:teamId",
            component: TeamDetail,
            name: "team-detail"
        }
    ]
});

export default router;
