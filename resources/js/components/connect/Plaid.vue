<template>
    <div></div>
</template>

<script>
export default {
    data() {
        return {
            linkHandler: null,
        };
    },
    methods: {
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
        },
        openLink() {
            if (!this.linkHandler) this.initializeLink();

            this.linkHandler.open();
        },
        destroyLink() {
            if (this.linkHandler) {
                this.$nextTick(vm => {
                    this.linkHandler.destroy();
                    this.linkHandler = null;
                });
            }
        },
        async onSuccess(publicToken, metadata) {
            this.$emit("success", { publicToken, metadata });
        },
        onExit(error, metadata) {
            this.$emit("error", { error, metadata });
        }
    },
    beforeDestroy() {
        this.destroyLink();
    },
    mounted() {
        this.openLink();
    }
};
</script>
