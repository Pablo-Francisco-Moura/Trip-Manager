<template>
  <router-view />
</template>

<script>
export default {
  name: "App",
};
</script>

<style>
:root {
  /* light theme colors: slightly off-white background so cards stand out */
  --bg: #f6f8fb;
  --fg: #213547;
  --card: #ffffff;
  --muted: #666666;
  --primary: #2b7cff;
  --danger: #ff5b5b;
  --table-head: #eef6ff;
}
.theme-dark {
  --bg: #071028;
  --fg: #d6e8ff;
  --card: #071a2a;
  --muted: #9fb0c8;
  --primary: #4aa3ff;
  --danger: #ff7b7b;
  --table-head: #0b2030;
}

/* Global font + bold styling for improved readability */
html,
body {
  font-family:
    "Inter",
    system-ui,
    -apple-system,
    "Segoe UI",
    Roboto,
    "Helvetica Neue",
    Arial;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color: var(--fg);
  background: var(--bg);
}

/* Make labels, table headers and buttons more prominent */
button,
.form-label,
.filter-row label,
.styled-table th {
  font-weight: 700;
}

/* Slightly heavier input text for clarity */
input,
select,
textarea {
  font-weight: 600;
}

/* Hover styles for all interactive controls to make them more visible */
button,
.btn,
.action-btn,
.filter-select {
  transition:
    transform 140ms ease,
    box-shadow 140ms ease,
    filter 140ms ease;
}

button:not(:disabled):hover,
.btn:not(:disabled):hover,
.action-btn:not(:disabled):hover,
.filter-select:not(:disabled):hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 22px rgba(33, 53, 71, 0.12);
  filter: brightness(0.98);
}

/* Make some components pick variables */
.topbar {
  background: linear-gradient(90deg, var(--bg), var(--table-head));
}
.topbar h1 {
  color: var(--primary);
}
.logout-btn {
  background: var(--danger);
}
.admin-badge {
  background: rgba(74, 163, 255, 0.09);
  color: var(--primary);
}
.user-badge {
  background: rgba(255, 123, 0, 0.06);
  color: var(--muted);
}
</style>
