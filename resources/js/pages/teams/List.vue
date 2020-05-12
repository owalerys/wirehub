<template>
    <v-row justify="center" align="start">
        <v-col>
            <v-card>
                <v-card-title
                    >Merchants<v-spacer /><v-btn
                        color="primary"
                        :to="{ name: 'team-create' }"
                        >Create</v-btn
                    ></v-card-title
                >
                <v-data-table
                    :loading="loading"
                    :headers="headers"
                    :items="teams"
                    :items-per-page="10"
                    @click:row="detail"
                >
                </v-data-table>
            </v-card>
        </v-col>
    </v-row>
</template>

<script>
import { mapActions, mapState } from "vuex";

export default {
    data() {
        return {
            headers: [
                {
                    text: "Name",
                    align: "start",
                    value: "name"
                },
                { text: "Owner", value: "users[0].name" },
                { text: "Owner", value: "users[0].name" },
            ],
            loading: false
        };
    },
    methods: {
        ...mapActions("team", ["fetch"]),
        async refresh() {
            this.loading = true;

            try {
                await this.fetch();
            } catch (e) {
                console.error(e);
            }

            this.loading = false;
        },
        async detail(row) {
            this.$router.push({
                name: "team-detail",
                params: { teamId: row.id }
            });
        }
    },
    computed: {
        ...mapState("team", {
            teams: "teams"
        })
    },
    mounted() {
        this.fetch();
    }
};
</script>
