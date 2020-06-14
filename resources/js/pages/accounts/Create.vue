<template>
    <v-row justify="center" align="start">
        <FlinksConnect v-if="provider === 'flinks' && flinks.show" v-model="flinks.visible" @error="onFlinksError" @success="onFlinksSuccess" />
        <PlaidConnect v-if="provider === 'plaid' && plaid.show" @error="onPlaidExit" @success="onPlaidSuccess" />
        <v-col>
            <v-card
                outline
                :loading="loading"
                max-width="500"
                class="mx-auto"
            >
                <v-card-title
                    >Link Account<v-spacer /><v-btn
                        v-if="error"
                        @click="tryAgain"
                        :disabled="loading"
                        color="error"
                        outlined
                        >Retry</v-btn
                    ></v-card-title
                >
                <v-card-text
                    ><p v-if="loading">
                        We're linking your account... Hold tight.
                    </p><div v-else>
                        <v-btn outlined @click="tryFlinks">Connect Canadian Account</v-btn>
                        <br/>
                        <br/>
                        <v-btn outlined @click="tryPlaid">Connect Other Account</v-btn>
                        </div></v-card-text
                >
            </v-card>
        </v-col>
    </v-row>
</template>

<script>
import { mapActions } from "vuex";

import FlinksConnect from '../../components/connect/Flinks'
import PlaidConnect from '../../components/connect/Plaid'

export default {
    data() {
        return {
            error: false,
            provider: 'flinks',
            plaid: {
                show: false,
                error: {},
                metadata: {},
                item: {},
                publicToken: ""
            },
            flinks: {
                show: false,
                visible: false,
                link: null
            }
        };
    },
    components: {
        FlinksConnect,
        PlaidConnect
    },
    computed: {
        loading() {
            return this.plaid.show || (this.flinks.show && this.flinks.visible);
        },
    },
    methods: {
        ...mapActions("link", ["exchangeToken", "saveLogin"]),
        async onPlaidSuccess({ publicToken, metadata }) {
            this.plaid.publicToken = publicToken;
            this.plaid.metadata = metadata;

            try {
                await this.exchangeToken({ publicToken });
            } catch (e) {
                this.error = true
                console.error(e)
            } finally {
                this.plaid.show = false;
            }

            if (!this.error) this.$router.push({ name: "account-list" });
        },
        onPlaidExit({ error, metadata }) {
            this.plaid.error = error;
            this.plaid.metadata = metadata;

            this.plaid.show = false;
            this.error = true;
        },
        tryPlaid() {
            this.provider = 'plaid'

            this.tryAgain()
        },
        tryFlinks() {
            this.provider = 'flinks'

            this.tryAgain()
        },
        tryAgain() {
            this.error = false

            this.flinks.show = false
            this.plaid.show = false

            if (this.provider === 'plaid') this.plaid.show = true
            else if (this.provider === 'flinks') {
                this.flinks.show = true
                this.flinks.visible = true
            }
        },
        async onFlinksSuccess({ loginId, institution }) {
            this.flinks.loginId = loginId;
            this.flinks.institution = institution;

            try {
                await this.saveLogin({ loginId, institution });
            } catch (e) {
                this.error = true
                console.error(e)
            } finally {
                this.flinks.show = false;
            }

            if (!this.error) this.$router.push({ name: "account-list" });
        },
        onFlinksError() {
            this.flinks.show = false

            this.error = true
        },
    },
};
</script>
