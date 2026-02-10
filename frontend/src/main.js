import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import axios from "axios";

const apiBase =
  (import.meta.env && import.meta.env.VITE_API_BASE_URL) ||
  "http://localhost:8000";
axios.defaults.baseURL = apiBase;

// Set Authorization header if token exists
const token = localStorage.getItem("token");
if (token) axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;

// Initialize theme from localStorage
const savedTheme = localStorage.getItem("theme");
if (savedTheme === "dark") document.documentElement.classList.add("theme-dark");

createApp(App).use(router).mount("#app");
