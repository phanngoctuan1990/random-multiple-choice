require("./bootstrap");
import App from "./components/App";
import Vue from "vue";
import VueRouter from "vue-router";
import routes from "./routes";
import VueBootstrapToasts from "vue-bootstrap-toasts";

Vue.use(VueRouter);
Vue.use(VueBootstrapToasts);

const router = new VueRouter({
    routes,
    mode: "history"
});

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
const app = new Vue({
    el: "#app",
    render: h => h(App),
    router
});
