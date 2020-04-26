<template>
    <v-row justify="center" align="start">
        <v-col>
            <v-card>
                <v-card-title>Accounts<v-spacer/><v-btn color="primary" :to="{ name: 'account-create' }">Create</v-btn></v-card-title>
                <v-data-table
                    :loading="loading"
                    :headers="headers"
                    :items="accounts"
                    :items-per-page="5"
                ></v-data-table>
            </v-card>
        </v-col>
    </v-row>
</template>

<script>
import { mapActions, mapState } from 'vuex'

export default {
    data() {
        return {
            headers: [
                {
                    text: "Name",
                    align: "start",
                    value: "name"
                },
                { text: "Institution", value: "item.institution.name" },
                { text: "Account Number", value: "mask" },
                { text: "Balance", value: "balances.current" },
                { text: "Currency", value: "balances.iso_currency_code" },
                { text: "Type", value: "type" }
            ],
            loading: false
        };
    },
    methods: {
        ...mapActions('account', ['fetch']),
        async refresh() {
            this.loading = true

            try {
                await this.fetch()
            } catch(e) {
                console.error(e)
            }

            this.loading = false
        }
    },
    computed: {
        ...mapState('account', {
            accounts: 'accounts'
        })
    },
    mounted() {
        this.fetch()
    }
};
</script>
