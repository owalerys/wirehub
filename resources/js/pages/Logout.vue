<template>
    <v-card outline max-width="500" class="mx-auto" :loading="true">
                <v-card-title>Logging Out<v-spacer /></v-card-title>
            </v-card>
</template>

<script>
import { mapActions } from 'vuex';

export default {
    methods: {
        ...mapActions('user', ['logout']),
        async handleLogout() {
            await this.logout()

            window.location = '/'
        }
    },
    created() {
        this.handleLogout()
    }
}
</script>
