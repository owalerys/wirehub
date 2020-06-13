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
        <v-card-text v-if="!loading">
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
            {{ account.balances.available ? (account.balances.available | money) : '' }}
        </v-card-text>
    </v-card>
</template>

<script>
import logos from '../../logo'

export default {
    props: ['account', 'loading'],
    computed: {
        logos() {
            return logos
        }
    }
}
</script>
