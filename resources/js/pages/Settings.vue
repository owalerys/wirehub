<template>
    <v-row justify="center" align="center">
        <v-col cols="12">
            <v-card outline>
                <v-card-title>Account</v-card-title>
                <ValidationObserver ref="accountObserver" v-slot="{ validate, reset }">
                    <form>
                        <v-container fluid>
                            <v-row>
                                <v-col cols="12" md="6">
                                    <ValidationProvider
                                        v-slot="{ errors }"
                                        name="name"
                                        rules="max:100"
                                    >
                                        <v-text-field
                                            v-model="name"
                                            :counter="100"
                                            :error-messages="errors"
                                            label="Name"
                                            required
                                        ></v-text-field>
                                    </ValidationProvider>
                                </v-col>

                                <v-col cols="12" md="6">
                                    <ValidationProvider
                                        v-slot="{ errors }"
                                        name="email"
                                        rules="max:100|email"
                                    >
                                        <v-text-field
                                            v-model="email"
                                            :counter="100"
                                            :error-messages="errors"
                                            label="E-mail"
                                            required
                                        ></v-text-field>
                                    </ValidationProvider>
                                </v-col>
                            </v-row>
                        </v-container>
                    </form>
                </ValidationObserver>
                <v-card-actions>
                    <v-btn outlined :loading="accountLoading" @click="submitAccount">Save</v-btn>
                    <v-btn outlined :disabled="accountLoading" @click="clearAccount">Clear</v-btn>
                </v-card-actions>
            </v-card>
        </v-col>
        <v-col cols="12">
            <v-card outline>
                <v-card-title>Password</v-card-title>
                <ValidationObserver ref="passwordObserver" v-slot="{ validate, reset }">
                    <form>
                        <v-container fluid>
                            <v-row>
                                <v-col cols="12" md="4">
                                    <ValidationProvider
                                        v-slot="{ errors }"
                                        name="old_password"
                                        rules="required|max:100"
                                    >
                                        <v-text-field
                                            v-model="oldPassword"
                                            type="password"
                                            :counter="100"
                                            :error-messages="errors"
                                            label="Current Password"
                                            required
                                        ></v-text-field>
                                    </ValidationProvider>
                                </v-col>

                                <v-col cols="12" md="4">
                                    <ValidationProvider
                                        v-slot="{ errors }"
                                        name="new_password"
                                        rules="required|max:100"
                                    >
                                        <v-text-field
                                            v-model="newPassword"
                                            type="password"
                                            :counter="100"
                                            :error-messages="errors"
                                            label="New Password"
                                            required
                                        ></v-text-field>
                                    </ValidationProvider>
                                </v-col>

                                <v-col cols="12" md="4">
                                    <ValidationProvider
                                        v-slot="{ errors }"
                                        name="new_password_confirmation"
                                        rules="required|max:100"
                                    >
                                        <v-text-field
                                            v-model="newPasswordConfirmation"
                                            type="password"
                                            :counter="100"
                                            :error-messages="errors"
                                            label="Confirm New Password"
                                            required
                                        ></v-text-field>
                                    </ValidationProvider>
                                </v-col>
                            </v-row>
                        </v-container>
                    </form>
                </ValidationObserver>
                <v-card-actions>
                    <v-btn outlined :loading="passwordLoading" @click="submitPassword">Save</v-btn>
                    <v-btn outlined :disabled="passwordLoading" @click="clearPassword">Clear</v-btn>
                </v-card-actions>
            </v-card>
        </v-col>
    </v-row>
</template>

<script>
import { required, email, max } from "vee-validate/dist/rules";
import {
    extend,
    ValidationObserver,
    ValidationProvider,
    setInteractionMode
} from "vee-validate";
import { mapActions, mapState } from "vuex";

import api from '../api';

setInteractionMode("eager");

extend("required", {
    ...required,
    message: "{_field_} can not be empty."
});

extend("max", {
    ...max,
    message: "{_field_} may not be greater than {length} characters."
});

extend("email", {
    ...email,
    message: "Email must be valid."
});

export default {
    components: {
        ValidationProvider,
        ValidationObserver
    },
    data: () => ({
        name: "",
        email: "",
        oldPassword: "",
        newPassword: "",
        newPasswordConfirmation: "",
        accountLoading: false,
        passwordLoading: false
    }),
    computed: {
        ...mapState('user', {
            user: 'user'
        })
    },
    methods: {
        async submitAccount() {
            if (!this.$refs.accountObserver.validate()) return;

            this.accountLoading = true;

            try {
                const data = {}

                if (this.name) data.name = this.name
                if (this.email && this.email !== this.user.email) data.email = this.email

                const response = await api.patch('/user', data)
            } catch (e) {
                if (e.response.status === 422 && e.response.data.errors) {
                    this.$refs.accountObserver.setErrors(e.response.data.errors)
                }

                console.error(e);
            } finally {
                this.accountLoading = false;
            }
        },
        clearAccount() {
            this.name = this.user.name;
            this.email = this.user.email;
            this.$refs.accountObserver.reset();
        },
        async submitPassword() {
            if (!this.$refs.passwordObserver.validate()) return;

            this.passwordLoading = true;

            try {
                const data = {
                    old_password: this.oldPassword,
                    new_password: this.newPassword,
                    new_password_confirmation: this.newPasswordConfirmation
                }

                const response = await api.put('/user/password', data)
            } catch (e) {
                if (e.response.status === 422 && e.response.data.errors) {
                    this.$refs.passwordObserver.setErrors(e.response.data.errors)
                }

                console.error(e);
            } finally {
                this.passwordLoading = false;
            }
        },
        clearPassword() {
            this.newPassword = '';
            this.oldPassword = '';
            this.newPasswordConfirmation = '';
            this.$refs.passwordObserver.reset();
        }
    },
    created() {
        this.name = this.user.name
        this.email = this.user.email
    }
};
</script>
