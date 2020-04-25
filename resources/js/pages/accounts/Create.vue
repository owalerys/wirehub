<template>
    <div></div>
</template>

<script>
export default {
    mounted() {
        let linkHandler = Plaid.create({
            clientName: 'WireHub',
            env: 'sandbox',
            apiVersion: 'v2',
            key: process.env.MIX_PLAID_PUBLIC_KEY,
            product: ['auth', 'transactions'],
            onSuccess(public_token, metadata) {
                console.log(public_token, metadata)
            }
        })

        linkHandler.open()
    }
}
</script>
