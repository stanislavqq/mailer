
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

Vue.component('example-component', require('./components/ExampleComponent.vue').default);


import Vuex from 'vuex'

import routes from './router';
import VueRouter from "vue-router";

import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
//import i18n from 'vue-i18n'
import elementLangRu from 'element-ui/lib/locale/lang/ru-RU';
import elementLocale from 'element-ui/lib/locale';
elementLocale.use(elementLangRu);

//Vue.use(i18n);
Vue.use(VueRouter);
Vue.use(Vuex);
Vue.use(ElementUI);
// Vue.use(ElementUI, {
//     i18n: (key, value) => i18n.t(key, value)
// });

global.Router = new VueRouter({
    routes,
});

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import AppMailer from './components/mailer/AppMailer';
import storeMailer from './storeMailer'

const app = new Vue({
    el: '#app',
    components: {
        AppMailer
    },
    created() {
        this.$store.dispatch("SET_TEMPLATES");
        //this.$store.dispatch("SET_CLIENTS");
        this.$store.dispatch("SET_CONTACT_LISTS");
        this.$store.dispatch("SET_DISTRIBUTIONS");
        this.$store.dispatch("SET_MAILER_DRIVERS");
    },
    router: Router,
    store: storeMailer
});
