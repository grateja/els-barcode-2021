import VueRouter from 'vue-router';
import Vue from 'vue';

Vue.use(VueRouter);

const routes = [
    {
        path: '/login',
        component: require('./views/auth/LoginPage.vue').default
    }
];

export default new VueRouter({
    routes,
    mode: 'history',
    linkActiveClass: 'active'
});
