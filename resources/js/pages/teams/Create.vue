<template>
    <v-row justify="center" align="center">
        <v-col>
            <v-card outline max-width="500" class="mx-auto">
                <v-card-title>Create Merchant<v-spacer /></v-card-title>
                <v-card-text>
                    <ValidationObserver
                        ref="observer"
                        v-slot="{ validate, reset }"
                    >
                        <form class="flex-grow-1">
                            <ValidationProvider
                                v-slot="{ errors }"
                                name="organization"
                                rules="required|max:100"
                            >
                                <v-text-field
                                    v-model="organization"
                                    :counter="100"
                                    :error-messages="errors"
                                    label="Organization"
                                    required
                                ></v-text-field>
                            </ValidationProvider>
                            <ValidationProvider
                                v-slot="{ errors }"
                                name="name"
                                rules="required|max:100"
                            >
                                <v-text-field
                                    v-model="name"
                                    :counter="100"
                                    :error-messages="errors"
                                    label="Manager Name"
                                    required
                                ></v-text-field>
                            </ValidationProvider>
                            <ValidationProvider
                                v-slot="{ errors }"
                                name="email"
                                rules="required|email"
                            >
                                <v-text-field
                                    v-model="email"
                                    :error-messages="errors"
                                    label="Owner E-mail"
                                    required
                                ></v-text-field>
                            </ValidationProvider>
                            <ValidationProvider
                                v-slot="{ errors, valid }"
                                name="invite"
                            >
                                <v-checkbox
                                    v-model="invite"
                                    :error-messages="errors"
                                    :value="true"
                                    label="E-mail User Invite"
                                    type="checkbox"
                                    required
                                ></v-checkbox>
                            </ValidationProvider>

                            <v-btn
                                class="mr-4"
                                @click="submit"
                                :loading="loading"
                                outlined
                                >submit</v-btn
                            >
                            <v-btn @click="clear" :disabled="loading" outlined
                                >clear</v-btn
                            >
                        </form>
                    </ValidationObserver>
                </v-card-text>
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
import { mapActions } from "vuex";

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
        organization: "",
        name: "",
        email: "",
        invite: true,
        loading: false
    }),

    methods: {
        ...mapActions("team", ["create", "fetch"]),
        async submit() {
            if (!this.$refs.observer.validate()) return;

            this.loading = true;

            try {
                const { team, owner } = await this.create({
                    organization: this.organization,
                    name: this.name,
                    email: this.email,
                    invite: this.invite
                });

                this.fetch();

                this.$router.push({
                    name: "team-detail",
                    params: { teamId: team.id }
                });
            } catch (e) {
                console.error(e);
            } finally {
                this.loading = false;
            }
        },
        clear() {
            this.organization = "";
            this.name = "";
            this.email = "";
            this.invite = true;
            this.$refs.observer.reset();
        }
    }
};
</script>
