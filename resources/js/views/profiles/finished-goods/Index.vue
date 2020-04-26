<template>
    <v-container>
        <h4 class="title grey--text">Finished goods</h4>
        <v-divider class="my-3"></v-divider>
        <v-btn outline color="primary" to="/finished-goods/profiles">View by model</v-btn>
        <v-btn outline color="primary" to="/finished-goods/items">View by serial numbers</v-btn>
        <router-view></router-view>
    </v-container>
</template>
