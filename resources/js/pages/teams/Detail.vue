<template>
    <v-row justify="start" align="start">
        <v-col cols="6">
            <TeamCard label="Merchant" :team="team" :loading="loading" :manager="manager" :invite="true" />
        </v-col>
        <v-col cols="12">
            <AccountsTable :accounts="accounts" :loading="loading" :per-page="5" />
        </v-col>
    </v-row>
</template>

<script>
import { mapActions, mapState } from "vuex";
import api from '../../api'

import TeamCard from '../../components/cards/Team'
import AccountsTable from '../../components/tables/Accounts'

export default {
    components: {
        AccountsTable,
        TeamCard
    },
    data() {
        return {
            loading: false,
            team: {}
        };
    },
    computed: {
        teamId() {
            return this.$route.params.teamId
        },
        accounts() {
            return this.team && this.team.accounts ? this.team.accounts : []
        },
        manager() {
            return this.team && this.team.users && this.team.users.length ? this.team.users[0] : null
        }
    },
    methods: {
        ...mapActions("team", ["fetch"]),
        async load() {
            this.loading = true

            try {
                const response = await api.get(`/teams/${this.teamId}`)

                this.team = response.data
            } finally {
                this.loading = false
            }
        }
    },
    created() {
        if (!this.team) {
            this.fetch()
        }

        this.load()
    }
};
</script>
