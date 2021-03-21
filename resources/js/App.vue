<template>
    <v-app>
        <v-app-bar app clipped-left color="secondary">
            <v-app-bar-nav-icon class="white--text" @click="drawer = !drawer" />
            <div class="flex items-center">
                <img class="logo" src="/dist/img/logo-bolt.png" />
                <div class="flex flex-col items-end">
                    <span class="title ml-1 mt-3 white--text open-sans font-bold"
                        ><i>{{ appName }}</i></span
                    >
                    <span class="font-weight-light white--text open-sans subtext font-semibold"><i>{{ isAdmin ? 'Admin' : 'Merchant' }}</i></span>
                </div>
            </div>
            <v-spacer />
        </v-app-bar>

        <Navigation v-model="drawer" />

        <v-content
            ><v-container
                fluid
                class="grey lighten-4 fill-height"
                justify="start"
            >
                <router-view></router-view>
            </v-container>
        </v-content>
    </v-app>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import Navigation from "./components/Navigation";

const APP_NAME = process.env.MIX_APP_NAME;

export default {
    components: {
        Navigation
    },
    props: {
        source: String
    },
    data: () => ({
        drawer: null
    }),
    computed: {
        appName() {
            return APP_NAME
        },
        ...mapGetters("user", ["isAdmin"]),
    },
    methods: {
        ...mapActions("sanctum", ["initializeCSRF"]),
        ...mapActions("team", {
            fetchTeams: "fetch"
        }),
        ...mapActions("account", {
            fetchAccounts: "fetch"
        }),
        ...mapActions("user", {
            fetchUser: "fetch"
        }),
        async initLoad() {
            this.initializeCSRF();
            await Promise.all([
                this.fetchAccounts(),
                this.fetchUser(window.user ? window.user : null)
            ])
            this.fetchTeams();
        }
    },
    created() {
        this.initLoad()
    }
};
</script>

<style scoped>
.flex {
    display: flex;
    flex-grow: 0;
}

.flex-col {
    flex-direction: column;
}

.items-end {
    align-items: flex-end;
}

.items-center {
    align-items: center;
}

.justify-center {
    justify-content: center;
}

.logo {
    height: 40px;
}

.title {
    font-size: 28px !important;
    line-height: 28px;
}

.subtext {
    font-size: 12px;
    line-height: 14px;
}

.font-bold {
    font-weight: bold !important;
}

.font-semibold {
    font-weight: 700 !important;
}

.open-sans {
    font-family: "Open Sans", sans-serif !important;
}
</style>
