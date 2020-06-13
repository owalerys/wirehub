<template>
    <v-card>
        <v-card-title
            >Accounts<v-spacer /><v-btn
                v-if="actions"
                color="primary"
                :to="{ name: 'account-create' }"
                outlined
                >Create</v-btn
            ></v-card-title
        >
        <v-data-table
            :loading="loading"
            :headers="headers"
            :items="filteredAccounts"
            :items-per-page="perPage ? perPage : 10"
            @click:row="detail"
        >
            <template v-slot:item.item.institution.name="slotProps">
                <v-avatar size="32">
                    <img
                        :src="
                            slotProps.item.item.institution_meta ? `data:image/png;base64, ${slotProps.item.item.institution_meta.logo}` : logos[slotProps.item.item.institution]
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
import logos from '../../logo'

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
                { text: "Currency", value: "currency_code" },
                { text: "Type", value: "type" }
            ]
        };
    },
    computed: {
        logos() {
            return logos
        },
        filteredAccounts() {
            return this.accounts.filter(account => account.is_depository)
        }
    },
    methods: {
        async detail(row) {
            this.$router.push({
                name: "account-detail",
                params: { accountId: row.id }
            });
        }
    }
};
</script>
