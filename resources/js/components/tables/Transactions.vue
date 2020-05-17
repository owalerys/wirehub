<template>
    <v-card>
        <v-card-title>Transactions<v-spacer /></v-card-title>
        <v-data-table
            :loading="loading"
            :headers="headers"
            :items="transactions"
            :items-per-page="5"
        >
            <template v-slot:item.amount="slotProps">
                {{ slotProps.value | money }}
            </template>
            <template v-slot:item.confirmed="slotProps">
                {{ slotProps.item.confirmed ? 'Yes' : 'No' }}
            </template>
            <template v-slot:item.actions="slotProps">
                <Confirmation :transaction="slotProps.item" @confirmed="updateConfirmedStatus(slotProps.item.external_id, $event)" />
            </template>
        </v-data-table>
    </v-card>
</template>

<script>
import Confirmation from '../Confirmation'

export default {
    components: {
        Confirmation
    },
    props: ['loading', 'transactions', 'actions'],
    computed: {
        headers() {
            const headers = [
                {
                    text: "Date",
                    align: "start",
                    value: "date"
                },
                { text: "Name", value: "name" },
                { text: "Amount", value: "amount" },
                { text: "Currency", value: "iso_currency_code" },
                { text: "Confirmed", value: "confirmed" },
            ]

            if (this.actions) {
                headers.push({ text: "Actions", value: "actions", sortable: false })
            }

            return headers
        }
    },
    methods: {
        updateConfirmedStatus(externalId, value) {
            this.$emit('confirmed', { externalId, confirmed: value })
        }
    }
}
</script>
