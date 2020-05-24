<template>
    <v-row justify="center" align="start">
        <v-col>
            <v-card
                outline
                :loading="!!linkHandler"
                max-width="500"
                class="mx-auto"
            >
                <v-card-title
                    >Link Account<v-spacer /><v-btn
                        v-if="!linked && !linkHandler"
                        @click="openLink"
                        :disabled="!!linkHandler"
                        color="error"
                        outlined
                        >Retry</v-btn
                    ></v-card-title
                >
                <v-card-text
                    ><p>
                        We're linking your account... Hold tight.
                    </p></v-card-text
                >
            </v-card>
        </v-col>
    </v-row>
</template>

<script>
import { mapActions } from "vuex";

export default {
    data() {
        return {
            linkHandler: null,
            publicToken: "",
            metadata: {},
            error: {},
            item: {}
        };
    },
    computed: {
        linked() {
            return !!this.publicToken;
        }
    },
    methods: {
        ...mapActions("link", ["exchangeToken"]),
        initializeLink() {
            this.linkHandler = Plaid.create({
                clientName: "WireHub",
                env: process.env.MIX_PLAID_ENV,
                apiVersion: "v2",
                key: process.env.MIX_PLAID_PUBLIC_KEY,
                product: ["auth", "transactions"],
                onSuccess: this.onSuccess,
                onExit: this.onExit,
                webhook: process.env.MIX_PLAID_WEBHOOK_URL
            });

            this.publicToken = "";
            this.metadata = {};
            this.error = {};
        },
        openLink() {
            if (!this.linkHandler) this.initializeLink();

            this.linkHandler.open();
        },
        destroyLink() {
            this.$nextTick(vm => {
                this.linkHandler.destroy();
                this.linkHandler = null;
            });
        },
        async onSuccess(publicToken, metadata) {
            this.publicToken = publicToken;
            this.metadata = metadata;

            await this.exchangeToken({ publicToken });

            this.destroyLink();

            this.$router.push({ name: "account-list" });
        },
        onExit(error, metadata) {
            this.error = error;
            this.metadata = metadata;

            this.destroyLink();
        }
    },
    mounted() {
        this.openLink();
    }
};
</script>
