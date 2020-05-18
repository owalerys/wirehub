<template>
    <v-navigation-drawer v-model="binding" app clipped color="grey lighten-4">
        <v-list dense class="grey lighten-4">
            <template v-for="(item, i) in items">
                <v-row v-if="item.heading" :key="i" align="center">
                    <v-col cols="6">
                        <v-subheader v-if="item.heading">
                            {{ item.heading }}
                        </v-subheader>
                    </v-col>
                    <v-col cols="6" class="text-right">
                        <v-btn small text>edit</v-btn>
                    </v-col>
                </v-row>
                <v-divider
                    v-else-if="item.divider"
                    :key="i"
                    dark
                    class="my-4"
                />
                <v-list-item v-else-if="item.link" :key="i" :to="item.link">
                    <v-list-item-action>
                        <v-icon>{{ item.icon }}</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title class="grey--text">
                            {{ item.text }}
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
                <v-list-item v-else :key="i" link>
                    <v-list-item-action>
                        <v-icon>{{ item.icon }}</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title class="grey--text">
                            {{ item.text }}
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </template>
        </v-list>
    </v-navigation-drawer>
</template>

<script>
import { mapGetters } from "vuex";

export default {
    props: {
        value: {
            type: Boolean
        }
    },
    methods: {
        input(val) {
            this.$emit("input", val);
        }
    },
    computed: {
        binding: {
            get() {
                return this.value;
            },
            set(val) {
                this.input(val);
            }
        },
        ...mapGetters("user", ["isAdmin"]),
        items() {
            if (this.isAdmin)
                return [
                    {
                        icon: "mdi-bank",
                        text: "Bank Accounts",
                        link: { name: "account-list" }
                    },
                    { divider: true },
                    {
                        icon: "mdi-account-group",
                        text: "Merchants",
                        link: { name: "team-list" }
                    },
                    { divider: true },
                    {
                        icon: "mdi-settings",
                        text: "Settings",
                        link: { name: "settings" }
                    },
                    {
                        icon: "mdi-logout",
                        text: "Log out",
                        link: { name: "logout" }
                    }
                ];

            return [
                {
                    icon: "mdi-bank",
                    text: "Bank Accounts",
                    link: { name: "account-list" }
                },
                { divider: true },
                {
                    icon: "mdi-settings",
                    text: "Settings",
                    link: { name: "settings" }
                },
                {
                    icon: "mdi-logout",
                    text: "Log out",
                    link: { name: "logout" }
                }
            ];
        }
    }
};
</script>
