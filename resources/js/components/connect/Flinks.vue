<template>
    <v-dialog v-model="binding" max-width="1280px">
        <iframe
            class="flinksconnect"
            height="760"
            scrolling="no"
            frameBorder="0"
            :src="flinksConnect"
        >
        </iframe>
    </v-dialog>
</template>

<script>
export default {
    props: ['value'],
    computed: {
        binding: {
            get() {
                return this.value
            },
            set(val) {
                this.$emit('input', val)
            }
        },
        flinksConnect() {
            return process.env.MIX_FLINKS_CONNECT_BASE + '?theme=light&desktopLayout=true&fixedHeightEnable=false&institutionFilterEnable=true&daysOfTransactions=Days365&demo=' + this.demo + '&innerRedirect=true&consentEnable=true&accountSelectorEnable=false&enhancedMFA=true'
        },
        demo() {
            return process.env.MIX_FLINKS_DEMO === true || process.env.MIX_FLINKS_DEMO === 'true'
        }
    },
    methods: {
        windowEvent(e) {
            const data = e.data;
            const step = data.step || "";
            if (step === "REDIRECT") {
                this.$emit("success", {
                    loginId: data.loginId,
                    institution: data.institution
                });
                this.$emit("input", false);
            } else if (
                [
                    "COMPONENT_DENY_CONSENT",
                    "COMPONENT_DENY_TERMS",
                    "MAXIMUM_RETRY_REACHED"
                ].includes(step)
            ) {
                this.$emit("error");
                this.$emit("input", false);
            }

            const requestId = data.requestId || null;
            if (requestId) {
                console.log('FLINKS - CONNECT REQUEST', requestId)
            }
        }
    },
    mounted() {
        window.addEventListener("message", this.windowEvent);
    },
    beforeDestroy() {
        window.removeEventListener("message", this.windowEvent);
    }
};
</script>

<style scoped>
@media (min-width: 768px) {
    .flinksconnect {
        width: 100%;
    }
}
</style>
