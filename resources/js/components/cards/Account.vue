<template>
    <v-card :loading="loading"
        ><v-card-title v-if="loading">Account</v-card-title
        ><v-card-title v-else
            ><v-avatar>
                <img
                    :src="
                        account.item.institution_meta ? `data:image/png;base64, ${account.item.institution_meta.logo}` : logos[account.item.institution]
                    "
                />
            </v-avatar>
            <v-spacer
        /></v-card-title>
        <v-card-text v-if="!loading && actions">
            <b>Name:</b> {{ account.name }}
            <br />
            <b>Last 4:</b> ***{{ account.mask }}<br />
            <b>Institution:</b> {{ account.item.institution }}
            <br />
            <b>Currency:</b> {{ account.currency_code }}
            <br />
            <b>Current Balance:</b>
            {{ account.balances.current | money }}
            <br />
            <b v-if="account.balances.available">Available Balance:</b>
            <span v-if="account.balances.available">{{ account.balances.available | money }}</span>
        </v-card-text>
        <v-card-text v-else-if="!loading">
            <b>Name:</b> {{ account.name }}
            <br />
            <b>Last 4:</b> ***{{ account.mask }}<br />
            <b>Currency:</b> {{ account.currency_code }}
            <br />
        </v-card-text>
    </v-card>
</template>

<script>
import logos from '../../logo'

export default {
    props: ['account', 'loading', 'actions'],
    computed: {
        logos() {
            return logos
        }
    }
}
</script>
