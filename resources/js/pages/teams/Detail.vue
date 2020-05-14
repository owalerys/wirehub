<template>
    <v-row justify="center" align="start">
        <v-col>
            <v-card v-if="team"
                ><v-card-title
                    >
                    {{ team.name }}
                    <v-spacer
                /></v-card-title>
                <!-- <v-card-text>
                    Currency: {{ team.balances.iso_currency_code }} <br>
                    Balance: {{ team.balances.current | money }}
                </v-card-text> -->
            </v-card>
            <v-card>
                <!-- <v-card-title
                    >Transactions<v-spacer /></v-card-title
                >
                <v-data-table
                    :loading="loading"
                    :headers="headers"
                    :items="transactions"
                    :items-per-page="5"
                >
                </v-data-table> -->
            </v-card>
        </v-col>
    </v-row>
</template>

<script>
import { mapActions, mapState } from "vuex";
import api from '../../api'

export default {
    data() {
        return {
            headers: [
                {
                    text: "Date",
                    align: "start",
                    value: "date"
                },
                { text: "Name", value: "name" },
                { text: "Amount", value: "amount" },
                { text: "Currency", value: "iso_currency_code" }
            ],
            loading: false,
            team: {}
        };
    },
    computed: {
        teamId() {
            return this.$route.params.teamId
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
