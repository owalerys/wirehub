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
            <v-btn
                text
                v-if="!loading && invite"
                color="primary"
                @click="resendInvite"
                :loading="requestLoading"
                >Re-send Invite</v-btn
            >
            <v-snackbar v-model="requestFailed" :timeout="2000">
                The invite e-mail failed to send.
                <template v-slot:action="{ attrs }">
                    <v-btn
                        color="blue"
                        text
                        v-bind="attrs"
                        @click="resendInvite"
                    >
                        Re-Try
                    </v-btn>
                </template>
            </v-snackbar>
        </v-card-actions>
    </v-card>
</template>

<script>
import api from "../../api";

export default {
    data() {
        return {
            requestLoading: false,
            requestFailed: false
        };
    },
    props: ["loading", "label", "team", "manager", "actions", "invite"],
    methods: {
        change() {
            this.$emit("change");
        },
        async resendInvite() {
            this.requestFailed = false;
            this.requestLoading = true;
            try {
                const response = await api.post(
                    `/teams/${this.team.id}/resend-invite`
                );
            } catch (e) {
                this.requestFailed = true;
            } finally {
                this.requestLoading = false;
            }
        }
    }
};
</script>
