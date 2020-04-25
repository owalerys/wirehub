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
export default {
    props: {
        value: {
            type: Boolean
        }
    },
    computed: {
        binding: {
            get() {
                return this.value
            },
            set(val) {
                this.input(val)
            }
        }
    },
    methods: {
        input(val) {
            this.$emit('input', val)
        }
    },
    data() {
        return {
            items: [
            { icon: "mdi-bank", text: "Bank Accounts", link: { name: 'account-list' } },
            { divider: true },
            { icon: "mdi-account-group", text: "Merchants" },
            { divider: true },
            { icon: "mdi-settings", text: "Settings" },
            { icon: "mdi-logout", text: "Log out" }
        ]
        }
    }
}
</script>
