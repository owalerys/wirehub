<template>
    <v-card>
        <v-card-title>Transactions<v-spacer /></v-card-title>
        <v-data-table
            :loading="loading"
            :headers="headers"
            :items="transactions"
            :items-per-page="5"
        >
        </v-data-table>
    </v-card>
</template>

<script>
export default {
    props: ['loading', 'transactions'],
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
        }
    }
}
</script>
