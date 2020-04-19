import VueRouter from 'vue-router';
import Vue from 'vue';

Vue.use(VueRouter);

const routes = [
    {
        path: '/login',
        component: require('./views/auth/LoginPage.vue').default
    },
    {
        path: '/incoming-reports',
        component: require('./views/incoming-reports/Index.vue').default,
        children: [
            {
                path: 'finished-goods',
                component: require('./views/incoming-reports/finished-goods/Index.vue').default,
            }
        ]
    }
];

export default new VueRouter({
    routes,
    mode: 'history',
    linkActiveClass: 'active'
});
