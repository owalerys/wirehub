<template>
    <v-app>
        <v-app-bar app clipped-left color="secondary">
            <v-app-bar-nav-icon class="white--text" @click="drawer = !drawer" />
            <span class="title ml-3 mr-5 white--text"
                >Wire<span class="font-weight-light">Hub</span></span
            >
            <v-spacer />
        </v-app-bar>

        <Navigation v-model="drawer" />

        <v-content
            ><v-container fluid class="grey lighten-4 fill-height" justify="start">
                <router-view></router-view>
            </v-container>
        </v-content>
    </v-app>
</template>

<script>
import Navigation from "./components/Navigation";

export default {
    components: {
        Navigation
    },
    props: {
        source: String
    },
    data: () => ({
        drawer: null
    })
};
</script>
