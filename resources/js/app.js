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

if (process.env.MIX_ENV_MODE === "production") {
    Vue.config.devtools = false;
    Vue.config.debug = false;
    Vue.config.silent = true;
}

const app = new Vue({
    el: "#app",
    render: h => h(App),
    router
});
