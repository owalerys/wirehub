<template>
    <v-card :loading="loading"
        ><v-card-title v-if="loading">Account</v-card-title
        ><v-card-title v-else
            ><v-avatar>
                <img
                    :src="
                        `data:image/png;base64, ${account.item.institution.logo}`
                    "
                />
            </v-avatar>
            <v-spacer
        /></v-card-title>
        <v-card-text v-if="!loading">
            <b>Name:</b> {{ account.name }}
            <br />
            <b>Official Name:</b> {{ account.official_name }}<br />
            <b>Last 4:</b> ***{{ account.mask }}<br />
            <b>Institution:</b> {{ account.item.institution.name }}
            <br />
            <b>Currency:</b> {{ account.balances.iso_currency_code }}
            <br />
            <b>Current Balance:</b>
            {{ account.balances.current | money }}
            <br />
            <b>Available Balance:</b>
            {{ account.balances.available | money }}
        </v-card-text>
    </v-card>
</template>

<script>
export default {
    props: ['account', 'loading']
}
</script>
