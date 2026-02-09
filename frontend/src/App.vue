<template>
  <router-view />
</template>

<script>
export default {
  name: "App",
};
</script>

<style>
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
  color: #213547;
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
</style>
