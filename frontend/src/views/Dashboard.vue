<template>
  <div>
    <div class="topbar">
      <div class="topbar-inner">
        <h1>Trip Manager</h1>
        <button class="logout-btn" @click="logout">Logout</button>
      </div>
    </div>

    <main style="padding: 1rem; max-width: 1100px; margin: 0 auto">
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

<style scoped>
.topbar {
  background: linear-gradient(90deg, #ffffff, #f4f8ff);
  border-bottom: 1px solid #e6ecff;
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
  color: #123a8a;
}
.logout-btn {
  background: #ff5b5b;
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
</style>
