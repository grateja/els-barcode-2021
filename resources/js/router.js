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
            },
            {
                path: 'spare-parts',
                component: require('./views/incoming-reports/spare-parts/Index.vue').default,
            }
        ]
    },
    {
        path: '/outgoing-reports',
        component: require('./views/outgoing-reports/Index.vue').default,
        children: [
            {
                path: 'finished-goods',
                component: require('./views/outgoing-reports/finished-goods/Index.vue').default,
            },
            {
                path: 'spare-parts',
                component: require('./views/outgoing-reports/spare-parts/Index.vue').default,
            }
        ]
    },
    {
        path: '/reservations',
        component: require('./views/reservations/Index.vue').default
    },
    {
        path: '/finished-goods',
        component: require('./views/profiles/finished-goods/Index.vue').default,
        children: [
            {
                path: 'profiles',
                component: require('./views/profiles/finished-goods/ByModels.vue').default
            },
            {
                path: 'items',
                component: require('./views/profiles/finished-goods/BySerialNumbers.vue').default
            }
        ]
    },
    {
        path: '/spare-parts',
        component: require('./views/profiles/spare-parts/Index.vue').default,
        children: [
            {
                path: 'profiles',
                component: require('./views/profiles/spare-parts/ByPartnumbers.vue').default
            },
            {
                path: 'items',
                component: require('./views/profiles/spare-parts/BySerialNumbers.vue').default
            }
        ]
    },
    {
        path: '/fixed-assets',
        component: require('./views/fixed-assets/Index.vue').default,
        children: [
            {
                path: 'accounts',
                component: require('./views/fixed-assets/accounts/Index.vue').default
            },
            {
                path: 'items',
                component: require('./views/fixed-assets/items/Index.vue').default
            }
        ]
    }
];

export default new VueRouter({
    routes,
    mode: 'history',
    linkActiveClass: 'active'
});
