import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import axios from "axios";

const apiBase = import.meta.env.VITE_API_BASE_URL || "http://backend:8000";
axios.defaults.baseURL = apiBase;

createApp(App).use(router).mount("#app");
