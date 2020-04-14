<template>
    <!-- <div>
        <v-content>
            <toolbar />
            <v-progress-linear v-if="isCheckingUser" indeterminate></v-progress-linear>
            <router-view v-else />
            <flash-message />
        </v-content>
    </div> -->
    <div>
        <menus />
        <v-content>
            <!-- <breadcrumbs></breadcrumbs> -->
            <v-progress-linear v-if="isCheckingUser" indeterminate></v-progress-linear>
            <router-view v-else />
        </v-content>
    </div>
</template>

<script>
import Menus from './layout/Menu.vue';
//import Toolbar from './layout/Toolbar.vue';
//import Breadcrumbs from './layout/Breadcrumbs.vue';
//import FlashMessage from './layout/FlashMessage';
export default {
    components: {
        Menus//,
        //Breadcrumbs,
        //Toolbar,
        //FlashMessage
    },
    computed: {
        isCheckingUser() {
            return this.$store.getters['auth/isLoading'];
        }
    }
}
</script>
