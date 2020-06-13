<template>
    <v-row justify="start" align="start">
        <v-col md="6" cols="12">
            <AccountCard :loading="loading" :account="account" />
        </v-col>
        <v-col v-if="isAdmin" md="6" cols="12">
            <TeamCard
                label="Merchant"
                :loading="loading"
                :team="team"
                :manager="manager"
                @change="merchantDialog = true"
                :actions="isAdmin"
            />
        </v-col>
        <v-col cols="12">
            <TransactionsTable
                :loading="loading"
                :transactions="transactions"
                @confirmed="updateTransactionConfirmation"
                :actions="isTeamMember"
                :account="account"
                ><template v-slot:actions
                    ><Download
                        :url="
                            `/accounts/${accountId}/transactions/historical`
                        "/></template
            ></TransactionsTable>
        </v-col>
        <v-col v-if="isAdmin" cols="12">
            <v-card>
                <v-card-title
                    >Settings <v-spacer />
                    <v-btn color="red" outlined :loading="deleteLoading" @click="preDeleteModal">Delete Account</v-btn></v-card-title
                >
            </v-card>
        </v-col>
        <v-dialog v-model="deleteDialog" max-width="500px">
            <v-card v-if="itemWithAccounts">
                <v-card-title>Delete Accounts at {{ itemWithAccounts.institution.name }}</v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <br/>
                    <v-alert type="error">
                        All accounts at {{ itemWithAccounts.institution }} connected under the same login will also be deleted.
                    </v-alert>
                    <v-list two-line>
                        <v-list-item v-for="account in filteredItemAccounts" :key="account.id">
                            <v-list-item-content>
                                <v-list-item-title>Account ***{{ account.mask }}: {{ account.name }}</v-list-item-title>
                                <v-list-item-subtitle v-if="account.teams[0]">Linked to Merchant: {{ account.teams[0].name }}</v-list-item-subtitle>
                                <v-list-item-subtitle v-else>Not shared with Merchant</v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                    </v-list>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-btn
                        color="blue darken-1"
                        text
                        @click="deleteDialog = false"
                        :disabled="deleteLoading"
                        >Cancel</v-btn
                    >
                    <v-spacer />
                    <v-btn
                        color="red"
                        text
                        @click="deleteItem"
                        :loading="deleteLoading"
                        >Delete</v-btn
                    >
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog v-model="merchantDialog" scrollable max-width="300px">
            <v-card>
                <v-card-title>Change Merchant</v-card-title>
                <v-divider></v-divider>
                <v-card-text style="height: 300px;">
                    <v-radio-group v-model="selectedMerchant" column>
                        <v-radio
                            v-for="team in teams"
                            :key="team.id"
                            :label="team.name"
                            :value="team.id"
                        ></v-radio>
                    </v-radio-group>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-btn
                        color="blue darken-1"
                        text
                        @click="merchantDialog = false"
                        :disabled="loading"
                        >Close</v-btn
                    >
                    <v-btn
                        color="blue darken-1"
                        text
                        @click="linkMerchant()"
                        :loading="loading"
                        >Save</v-btn
                    >
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>

<script>
import { mapActions, mapState, mapGetters } from "vuex";
import api from "../../api";
import Vue from "vue";

import AccountCard from "../../components/cards/Account";
import Download from "../../components/Download";
import TeamCard from "../../components/cards/Team";
import TransactionsTable from "../../components/tables/Transactions";

export default {
    components: {
        AccountCard,
        Download,
        TeamCard,
        TransactionsTable
    },
    data() {
        return {
            loading: false,
            transactions: [],
            account: {},
            merchantDialog: false,
            selectedMerchant: null,
            deleteLoading: false,
            itemWithAccounts: null,
            deleteDialog: false
        };
    },
    computed: {
        accountId() {
            return this.$route.params.accountId;
        },
        ...mapState("team", {
            teams: "teams"
        }),
        ...mapGetters("user", ["isTeamMember", "isAdmin"]),
        team() {
            return this.account &&
                this.account.teams &&
                this.account.teams.length
                ? this.account.teams[0]
                : null;
        },
        manager() {
            return this.team ? this.team.users[0] : null;
        },
        filteredItemAccounts() {
            if (this.itemWithAccounts) return this.itemWithAccounts.accounts.filter(account => account.is_depository)
        }
    },
    methods: {
        async load() {
            this.loading = true;

            try {
                const [
                    transactionsResponse,
                    accountResponse
                ] = await Promise.all([
                    api.get(`/accounts/${this.accountId}/transactions`),
                    api.get(`/accounts/${this.accountId}`)
                ]);

                this.transactions.splice(0);
                this.transactions.push(...transactionsResponse.data);

                Vue.set(this, "account", accountResponse.data);

                this.selectedMerchant =
                    this.account &&
                    this.account.teams &&
                    this.account.teams.length
                        ? this.account.teams[0].id
                        : null;
            } finally {
                this.loading = false;
            }
        },
        async linkMerchant() {
            this.loading = true;

            try {
                const response = await api.put(
                    `/accounts/${this.accountId}/team`,
                    { team_id: this.selectedMerchant }
                );

                await this.load();

                this.merchantDialog = false;
            } catch (e) {
                console.error(e);
            } finally {
                this.loading = false;
            }
        },
        async preDeleteModal() {
            this.deleteLoading = true;

            try {
                const response = await api.get(`/items/${this.account.parent_id}`)

                this.itemWithAccounts = response.data

                this.deleteDialog = true
            } catch (e) {
                console.error(e)
            } finally {
                this.deleteLoading = false
            }
        },
        async deleteItem() {
            this.deleteLoading = true;

            try {
                const response = await api.delete(`/items/${this.account.parent_id}`)

                this.deleteDialog = false

                this.$router.push({
                    name: 'account-list'
                })
            } catch (e) {
                console.error(e)
            } finally {
                this.deleteLoading = false
            }
        },
        updateTransactionConfirmation({ externalId, confirmed }) {
            const transaction = this.transactions.find(
                transaction => transaction.external_id === externalId
            );

            transaction.confirmed = confirmed;
        }
    },
    created() {
        this.load();
    }
};
</script>
