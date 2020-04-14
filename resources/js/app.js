require('./bootstrap');

import router from './router.js';
import store from './store/index.js';
import moment from 'moment';

window.Vue = require('vue');
window.moment = moment;
Vue.prototype.moment = moment;


import Vuetify from 'vuetify';
import Breadcrumbs from 'vue-2-breadcrumbs';

Vue.use(Vuetify);
Vue.use(Breadcrumbs);
Vue.component('main-body', require('./views/MainBody.vue').default);
Vue.component('vuetify-autocomplete', require('./components/VuetifyAutocomplete.vue').default);

Vue.filter('uppercase', str => str.toUpperCase());

const app = new Vue({
    el: '#app',
    router,
    store
});
