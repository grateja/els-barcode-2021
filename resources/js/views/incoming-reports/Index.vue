<template>
    <v-container>
        <h4 class="title grey--text">Incomming reports</h4>
        <v-divider class="my-3"></v-divider>
        <v-btn outline color="primary" to="/incoming-reports/finished-goods">Finished goods</v-btn>
        <v-btn outline color="primary" to="/incoming-reports/spare-parts">Spare parts</v-btn>
        <router-view></router-view>
    </v-container>
</template>
