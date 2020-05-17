<template>
    <v-card>
        <v-card-title
            >Accounts<v-spacer /><v-btn
                v-if="actions"
                color="primary"
                :to="{ name: 'account-create' }"
                >Create</v-btn
            ></v-card-title
        >
        <v-data-table
            :loading="loading"
            :headers="headers"
            :items="accounts"
            :items-per-page="perPage ? perPage : 10"
            @click:row="detail"
        >
            <template v-slot:item.item.institution.name="slotProps">
                <v-avatar size="32">
                    <img
                        :src="
                            `data:image/png;base64, ${slotProps.item.item.institution.logo}`
                        "
                    />
                </v-avatar>
                {{ slotProps.item.item.institution.name }}
            </template>
            <template v-slot:item.mask="slotProps">
                ***{{ slotProps.value }}
            </template>
            <template v-slot:item.balances.current="slotProps">
                {{ slotProps.value | money }}
            </template>
        </v-data-table>
    </v-card>
</template>

<script>
export default {
    props: ["actions", "loading", "accounts", 'perPage'],
    data() {
        return {
            headers: [
                {
                    text: "Name",
                    align: "start",
                    value: "name"
                },
                { text: "Institution", value: "item.institution.name" },
                { text: "Last 4", value: "mask" },
                { text: "Balance", value: "balances.current", sortable: false },
                { text: "Currency", value: "balances.iso_currency_code" },
                { text: "Type", value: "type" }
            ]
        };
    },
    methods: {
        async detail(row) {
            this.$router.push({
                name: "account-detail",
                params: { accountId: row.external_id }
            });
        }
    }
};
</script>
