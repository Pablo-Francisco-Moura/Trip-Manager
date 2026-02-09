import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import axios from "axios";

const apiBase = import.meta.env.VITE_API_BASE_URL || "http://backend:8000";
axios.defaults.baseURL = apiBase;

// Set Authorization header if token exists
const token = localStorage.getItem("token");
if (token) axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;

createApp(App).use(router).mount("#app");
