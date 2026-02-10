<template>
  <div>
    <div class="topbar">
      <div class="topbar-inner">
        <h1>Trip Manager</h1>
        <div class="topbar-controls">
          <div
            class="theme-switch"
            :data-theme="theme"
            role="switch"
            :aria-checked="theme === 'dark'"
            @click="toggleTheme"
          >
            <span class="icon sun" aria-hidden="true">☀️</span>
            <span class="icon moon" aria-hidden="true">🌙</span>
            <span class="knob"></span>
          </div>
          <button class="logout-btn" @click="logout">Logout</button>
        </div>
      </div>
    </div>

    <main class="main-wrap">
      <TravelRequestsTable ref="table" />
      <Toast />
    </main>
  </div>
</template>

<script>
import TravelRequestForm from "../components/TravelRequestForm.vue";
import TravelRequestsTable from "../components/TravelRequestsTable.vue";
import auth from "../services/auth";
import Toast from "../components/Toast.vue";

export default {
  components: { TravelRequestForm, TravelRequestsTable, Toast },
  data() {
    return { theme: "light" };
  },
  methods: {
    toggleTheme() {
      this.theme = this.theme === "dark" ? "light" : "dark";
      localStorage.setItem("theme", this.theme);
      if (this.theme === "dark")
        document.documentElement.classList.add("theme-dark");
      else document.documentElement.classList.remove("theme-dark");
      window.dispatchEvent(
        new CustomEvent("pref:themeChange", { detail: this.theme }),
      );
    },
    reload() {
      this.$refs.table.fetch();
    },
    logout() {
      auth.logout();
      this.$router.push("/login");
    },
  },
  mounted() {
    const t = localStorage.getItem("theme");
    this.theme = t === null ? "light" : t;
    if (this.theme === "dark")
      document.documentElement.classList.add("theme-dark");
  },
};
</script>

<style scoped>
.topbar {
  background: linear-gradient(90deg, var(--bg), var(--table-head));
  border-bottom: 1px solid rgba(0, 0, 0, 0.06);
  padding: 0.5rem 0.75rem;
}
.topbar-inner {
  max-width: 1100px;
  margin: 0 auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.topbar h1 {
  margin: 0;
  font-size: 1.25rem;
  color: var(--primary);
}
.topbar-controls {
  display: flex;
  gap: 0.6rem;
  align-items: center;
}
.logout-btn {
  background: var(--danger, #ff5b5b);
  color: #fff;
  border: none;
  padding: 0.5rem 0.75rem;
  border-radius: 6px;
  cursor: pointer;
  box-shadow: 0 1px 0 rgba(0, 0, 0, 0.05);
}
.logout-btn:hover {
  opacity: 0.95;
}

/* Theme switch */
.theme-switch {
  width: 56px;
  height: 28px;
  border-radius: 999px;
  position: relative;
  display: inline-flex;
  align-items: center;
  padding: 3px;
  box-sizing: border-box;
  background: transparent !important;
  border: 1px solid var(--primary);
  cursor: pointer;
}
.theme-switch .icon {
  font-size: 12px;
  line-height: 1;
  width: 16px;
  text-align: center;
  color: var(--fg, #123a8a);
  opacity: 0.85;
  user-select: none;
  transition:
    transform 180ms ease,
    opacity 160ms ease;
}
.theme-switch .sun {
  margin-left: 4px;
}
.theme-switch .moon {
  margin-left: auto;
  margin-right: 4px;
}
.theme-switch .knob {
  position: absolute;
  left: 3px;
  width: 22px;
  height: 22px;
  border-radius: 50%;
  transition:
    transform 180ms ease,
    background 180ms ease;
  transform: translateX(0);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
  background: transparent !important;
}
.theme-switch[data-theme="dark"] {
}
.theme-switch[data-theme="dark"] .knob {
  transform: translateX(28px);
}

/* scale selected icon */
.theme-switch[data-theme="light"] .icon.sun {
  transform: scale(1.4);
}
.theme-switch[data-theme="light"] .icon.moon {
  transform: scale(0.88);
  opacity: 0.65;
}
.theme-switch[data-theme="dark"] .icon.moon {
  transform: scale(1.4);
}
.theme-switch[data-theme="dark"] .icon.sun {
  transform: scale(0.88);
  opacity: 0.65;
}

.main-wrap {
  padding: 1rem;
  max-width: 1100px;
  margin: 0 auto;
}
</style>
