<template>
    <v-card :loading="loading"
        ><v-card-title
            >{{ label }}<v-spacer /><v-btn
                v-if="team && actions"
                outlined
                :disabled="loading"
                :to="{ name: 'team-detail', params: { teamId: team.id } }"
                >View Merchant</v-btn
            ></v-card-title
        >
        <v-list disabled dense>
            <v-list-item>
                <v-list-item-icon
                    ><v-icon>mdi-office-building</v-icon></v-list-item-icon
                >
                <v-list-item-content>
                    <v-list-item-title>{{
                        this.team ? this.team.name : "None"
                    }}</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item>
                <v-list-item-icon
                    ><v-icon>mdi-account</v-icon></v-list-item-icon
                >
                <v-list-item-content>
                    <v-list-item-title>{{
                        this.manager ? this.manager.name : "None"
                    }}</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item>
                <v-list-item-icon><v-icon>mdi-email</v-icon></v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>{{
                        this.manager ? this.manager.email : "None"
                    }}</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
        </v-list>
        <v-card-actions>
            <v-btn
                text
                v-if="!loading && actions"
                color="primary"
                @click="change"
                >Change Merchant</v-btn
            >
            <v-spacer />
        </v-card-actions>
    </v-card>
</template>

<script>
export default {
    props: ['loading', 'label', 'team', 'manager', 'actions'],
    methods: {
        change() {
            this.$emit('change')
        }
    }
}
</script>
