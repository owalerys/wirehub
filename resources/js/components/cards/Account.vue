<template>
    <v-card :loading="loading"
        ><v-card-title v-if="loading">Account</v-card-title
        ><v-card-title v-else
            ><v-avatar>
                <img
                    :src="
                        account.item.institution_meta
                            ? `data:image/png;base64, ${account.item.institution_meta.logo}`
                            : logos[account.item.institution]
                    "
                />
            </v-avatar>
            <v-spacer
        /></v-card-title>
        <v-card-text v-if="!loading && actions">
            <b>Name:</b> {{ account.name }}
            <br />
            <template v-if="account.has_account_meta"
                ><b>Last 4:</b> ***{{ account.mask }}<br
            /></template>
            <b>Institution:</b> {{ account.item.institution }}
            <br />
            <template v-if="account.has_account_meta">
                <b>Currency:</b> {{ account.currency_code }}
                <br />
                <b>Current Balance:</b>
                {{ account.balances.current | money }}
                <br />
                <b v-if="account.balances.available">Available Balance:</b>
                <span v-if="account.balances.available">{{
                    account.balances.available | money
                }}</span>
            </template>
        </v-card-text>
        <v-card-text v-else-if="!loading">
            <b>Name:</b> {{ account.name }}
            <br />
            <template v-if="account.has_account_meta"
                ><b>Last 4:</b> ***{{ account.mask }}<br />
                <b>Currency:</b> {{ account.currency_code }}
            </template>
        </v-card-text>
        <v-card-actions>
            <v-btn
                text
                v-if="!loading && actions"
                color="primary"
                @click="rename"
                >Rename Account</v-btn
            >
            <v-spacer />
        </v-card-actions>
    </v-card>
</template>

<script>
import logos from "../../logo";

export default {
    props: ["account", "loading", "actions"],
    computed: {
        logos() {
            return logos;
        }
    },
    methods: {
        rename() {
            this.$emit("rename");
        }
    }
};
</script>
