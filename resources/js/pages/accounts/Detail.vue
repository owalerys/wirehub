<template>
    <v-row justify="center" align="start">
        <v-col>
            <v-card v-if="account"
                ><v-card-title
                    ><v-avatar>
                        <img
                            :src="
                                `data:image/png;base64, ${account.item.institution.logo}`
                            "
                        />
                    </v-avatar>
                    <v-spacer
                /></v-card-title>
                <v-card-text>
                    Currency: {{ account.balances.iso_currency_code }} <br>
                    Balance: {{ account.balances.current | money }}
                </v-card-text>
            </v-card>
            <v-card>
                <v-card-title
                    >Transactions<v-spacer /></v-card-title
                >
                <v-data-table
                    :loading="loading"
                    :headers="headers"
                    :items="transactions"
                    :items-per-page="5"
                >
                </v-data-table>
            </v-card>
        </v-col>
    </v-row>
</template>

<script>
import { mapActions, mapState } from "vuex";
import numeral from 'numeral'
import api from '../../api'

export default {
    filters: {
        money(val) {
            return numeral(val).format('$0,0.00')
        }
    },
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
            transactions: []
        };
    },
    computed: {
        ...mapState("account", {
            accounts: "accounts"
        }),
        account() {
            return this.accounts.find(account => {
                return account.external_id === this.$route.params.accountId;
            });
        },
        accountId() {
            return this.$route.params.accountId
        }
    },
    methods: {
        ...mapActions("account", ["fetch"]),
        async load() {
            this.loading = true

            try {
                const response = await api.get(`/accounts/${this.accountId}/transactions`)

                this.transactions.splice(0)
                this.transactions.push(...response.data.data)
            } finally {
                this.loading = false
            }
        }
    },
    created() {
        if (!this.account) {
            this.fetch()
        }

        this.load()
    }
};
</script>
