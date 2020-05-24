<template>
    <v-btn :loading="loading" @click="action" outlined>{{ label || 'Download' }}</v-btn>
</template>

<script>
import { json2csvAsync } from 'json-2-csv'

import api from '../api'

export default {
    props: ['url', 'label', 'fileName'],
    data() {
        return {
            loading: false
        }
    },
    methods: {
        async action() {
            try {
                this.loading = true

                const response = await api.get(this.url)

                const data = response.data

                const csv = await json2csvAsync(data)

                const hiddenEl = document.getElementById('hiddenDownloadEl')
                const blob = new Blob(["\ufeff", csv])
                const url = URL.createObjectURL(blob)
                hiddenEl.href = url
                hiddenEl.download = this.fileName ? this.fileName : 'Export.csv'
                hiddenEl.click()
            } catch (e) {
                console.error(e)
            } finally {
                this.loading = false
            }
        }
    }
}
</script>
