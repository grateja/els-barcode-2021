<template>
    <form action="" method="post" @submit.prevent="login">
        <v-card max-width="400">
            <v-card-title>
                <div class="title">Login</div>
            </v-card-title>

            <v-divider></v-divider>

            <v-card-text>

                <v-text-field
                    name="email"
                    label="Email"
                    id="email"
                    append-icon="people"
                    v-model="email"
                    :error-messages="errors.get('email')"
                ></v-text-field>

                <v-text-field
                    name="password"
                    label="Password"
                    id="password"
                    append-icon="lock"
                    type="password"
                    v-model="password"
                ></v-text-field>

                <v-checkbox label="Remember me" v-model="rememberMe"></v-checkbox>

                <v-btn type="submit" flat class="primary ma-0" :loading="isLoggingIn">Log in</v-btn>

            </v-card-text>
        </v-card>
    </form>
</template>

<script>
export default {
    data() {
        return {
            email: '',
            password: '',
            rememberMe: false
        }
    },
    methods: {
        login() {
            this.$store.dispatch('auth/loginAttempt', this.$data).then((res, rej) => {
                this.$router.push('/');
            }).catch(err => {});
        }
    },
    computed: {
        isLoggingIn() {
            return this.$store.getters['auth/getLoggingIn'];
        },
        errors() {
            return this.$store.getters['auth/getErrors'];
        }
    }
}
</script>
