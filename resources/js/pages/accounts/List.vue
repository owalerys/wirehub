<template>
    <v-row justify="center" align="start">
        <v-col>
            <AccountsTable :loading="loading" :accounts="accounts" :actions="true" />
        </v-col>
    </v-row>
</template>

<script>
import { mapActions, mapState } from "vuex";

import AccountsTable from '../../components/tables/Accounts'

export default {
    components: {
        AccountsTable
    },
    data() {
        return {
            loading: false
        };
    },
    methods: {
        ...mapActions("account", ["fetch"]),
        async refresh() {
            this.loading = true;

            try {
                await this.fetch();
            } catch (e) {
                console.error(e);
            }

            this.loading = false;
        },
    },
    computed: {
        ...mapState("account", {
            accounts: "accounts"
        })
    },
    mounted() {
        this.fetch();
    }
};
</script>
