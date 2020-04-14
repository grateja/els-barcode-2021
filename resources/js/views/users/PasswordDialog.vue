<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="submit">
            <v-card v-if="user">
                <v-card-title class="title grey--text">Change password</v-card-title>
                <v-card-text>
                    <v-text-field :value="user.name" outline label="Name"></v-text-field>
                    <v-text-field v-model="formData.password" :error-messages="errors.get('password')" outline label="Password" type="password"></v-text-field>
                    <v-text-field v-model="formData.password_confirmation" :error-messages="errors.get('password_confirmation')" outline label="Retype password" type="password"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-btn class="primary" type="submit" round :loading="saving">Save</v-btn>
                    <v-btn @click="close" round>Close</v-btn>
                </v-card-actions>
            </v-card>
        </form>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value', 'user'
    ],
    data() {
        return {
            formData: {
                password: null,
                password_confirmation: null
            }
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
        },
        submit() {
            this.$store.dispatch(`user/changePassword`, {
                userId: this.user.id,
                formData: this.formData
            }).then((res, rej) => {
                this.close();
                this.$emit('save', {
                    user: res.data.user,
                    mode: this.mode
                });
            });
        }
    },
    computed: {
        errors() {
            return this.$store.getters['user/getErrors'];
        },
        saving() {
            return this.$store.getters['user/isSaving'];
        }
    },
    watch: {
        value(val) {
            if(val && this.user) {
                this.formData.name = this.user.name;
            } else {
                this.formData.name = null;
            }
            this.formData.password = null;
            this.formData.password_confirmation = null;
        }
    }
}
</script>
