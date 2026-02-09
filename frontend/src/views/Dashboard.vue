<template>
  <div style="padding: 1rem">
    <div
      style="display: flex; justify-content: space-between; align-items: center"
    >
      <h1>Trip Requests</h1>
      <button @click="logout">Logout</button>
    </div>
    <TravelRequestForm @created="reload" />
    <TravelRequestsTable ref="table" />
  </div>
</template>

<script>
import TravelRequestForm from "../components/TravelRequestForm.vue";
import TravelRequestsTable from "../components/TravelRequestsTable.vue";
import auth from "../services/auth";

export default {
  components: { TravelRequestForm, TravelRequestsTable },
  methods: {
    reload() {
      this.$refs.table.fetch();
    },
    logout() {
      auth.logout();
      this.$router.push("/login");
    },
  },
};
</script>
