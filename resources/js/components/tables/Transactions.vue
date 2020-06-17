<template>
    <v-card>
        <v-card-title
            >Transactions<v-spacer /><slot name="actions"></slot
        ></v-card-title>
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
                {{ slotProps.item.confirmed ? "Yes" : "No" }}
            </template>
            <template v-slot:item.currency_code="slotProps">
                {{ slotProps.item.currency_code ? slotProps.item.currency_code : account.currency_code }}
            </template>
            <template v-slot:item.actions="slotProps">
                <Confirmation
                    :transaction="slotProps.item"
                    @confirmed="
                        updateConfirmedStatus(
                            slotProps.item.id,
                            $event
                        )
                    "
                />
            </template>
        </v-data-table>
    </v-card>
</template>

<script>
import Confirmation from "../Confirmation";

export default {
    components: {
        Confirmation
    },
    props: ["loading", "transactions", "actions", "account"],
    computed: {
        headers() {
            const headers = [
                {
                    text: "ID",
                    align: "start",
                    value: "internal_id"
                },
                {
                    text: "Date",
                    align: "start",
                    value: "date"
                },
                { text: "Name", value: "name" },
                { text: "Amount", value: "amount", align: "end" },
                { text: "Currency", value: "currency_code", align: "end" },
                { text: "Confirmed", value: "confirmed", align: "end" }
            ];

            if (this.actions) {
                headers.push({
                    text: "Credited To User?",
                    value: "actions",
                    sortable: false,
                    align: "end"
                });
            }

            return headers;
        }
    },
    methods: {
        updateConfirmedStatus(id, value) {
            this.$emit("confirmed", { id, confirmed: value });
        }
    }
};
</script>
