require('./app/bootstrap');

// Load Vue based dependencies
import Vue from 'vue';
import Vuetify from 'vuetify';
import { store } from './store/index';

// Register Vue based dependencies
Vue.use(Vuetify);

// Set up Router
import Router from './app/router';

/** Register Vue filters */
import MoneyFilter from './filters/money';
import GstFilter from './filters/gst';
import DateFilter from './filters/date';
Vue.filter('money', MoneyFilter);
Vue.filter('gst', GstFilter);
Vue.filter('date', DateFilter);

// Register Vue components
Vue.component('dashboard', require('./Dashboard.vue'));

const app = new Vue({
    el: '#app',
    router: Router,
    store: store
});
