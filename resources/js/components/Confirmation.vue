<template>
    <v-row>
        <v-btn v-if="!transaction.confirmed" :loading="loading" @click="confirm(true)" color="success" outlined>Confirm</v-btn>
        <v-btn v-else color="warning" :loading="loading" @click="confirm(false)" outlined>Undo</v-btn>
    </v-row>
</template>

<script>
import api from '../api'

export default {
    props: ['transaction'],
    data() {
        return {
            loading: false
        }
    },
    methods: {
        async confirm(value) {
            this.loading = true
            try {
                const response = await api.put(`/transactions/${this.transaction.id}/confirm`, { confirmed: !!value })

                this.confirmed(!!response.data.confirmed)
            } finally {
                this.loading = false
            }
        },
        confirmed(value) {
            this.$emit('confirmed', value)
        }
    }
}
</script>
