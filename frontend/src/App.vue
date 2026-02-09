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
</style>
