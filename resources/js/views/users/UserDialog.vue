<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="submit">
            <v-card>
                <v-card-title class="title grey--text">User info</v-card-title>
                <v-card-text>
                    <v-text-field v-model="formData.name" :error-messages="errors.get('name')" outline label="Name"></v-text-field>
                    <v-text-field v-model="formData.contactNumber" :error-messages="errors.get('contactNumber')" outline label="Contact number"></v-text-field>
                    <v-text-field v-model="formData.email" :error-messages="errors.get('email')" outline label="Email"></v-text-field>
                    <template>
                        <div v-if="mode == 'insert'">
                            <v-text-field v-model="formData.password" :error-messages="errors.get('password')" outline label="Password" type="password"></v-text-field>
                            <v-text-field v-model="formData.password_confirmation" :error-messages="errors.get('password_confirmation')" outline label="Retype password" type="password"></v-text-field>
                        </div>
                    </template>
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
            mode: 'insert',
            formData: {
                name: null,
                contactNumber: null,
                email: null,
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
            this.$store.dispatch(`user/${this.mode}User`, {
                userId: this.user ? this.user.id : null,
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
        user(val) {
            if(val) {
                this.mode = 'update';
                this.formData.name = val.name;
                this.formData.contactNumber = val.contact_number;
                this.formData.email = val.email;
            } else {
                this.mode = 'insert';
                this.formData.name = null;
                this.formData.contactNumber = null;
                this.formData.email = null;
                this.formData.password = null;
                this.formData.password_confirmation = null;
            }
        }
    }
}
</script>
