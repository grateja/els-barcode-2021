<template>
    <v-container>
        <h4 class="title grey--text">Spare parts</h4>
        <v-divider class="my-3"></v-divider>
        <v-btn outline color="primary" to="/spare-parts/profiles">View by part number</v-btn>
        <v-btn outline color="primary" to="/spare-parts/items">View by serial numbers</v-btn>
        <router-view></router-view>
    </v-container>
</template>
