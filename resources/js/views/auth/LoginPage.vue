<template>
    <v-container>
        <login-form></login-form>
    </v-container>
</template>

<script>
import LoginForm from './LoginForm.vue';

export default {
    components: {
        LoginForm
    },
    created() {
        let currentUser = this.$store.getters.getCurrentUser;
        if(currentUser) {
            this.$router.push('/');
        }
    }
}
</script>
