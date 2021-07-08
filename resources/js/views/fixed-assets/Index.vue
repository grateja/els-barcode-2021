<template>
    <v-container>
        <h4 class="title grey--text">Finished goods</h4>
        <v-divider class="my-3"></v-divider>
        <v-btn outline color="primary" to="/fixed-assets/accounts">View by account</v-btn>
        <v-btn outline color="primary" to="/fixed-assets/items">View by items</v-btn>
        <router-view></router-view>
    </v-container>
</template>
