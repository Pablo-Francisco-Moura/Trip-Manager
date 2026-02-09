<template>
  <div>
    <h3>Requests</h3>
    <div v-if="loading">Loading...</div>
    <table v-else border="1" cellpadding="6">
      <thead>
        <tr>
          <th>ID</th>
          <th>Destination</th>
          <th>Departure</th>
          <th>Return</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="r in requests" :key="r.id">
          <td>{{ r.id }}</td>
          <td>{{ r.destination }}</td>
          <td>{{ r.departure_date }}</td>
          <td>{{ r.return_date }}</td>
          <td>{{ r.status }}</td>
          <td>
            <button
              v-if="isAdmin"
              @click="updateStatus(r.id, 'approved')"
              :disabled="updatingId === r.id"
            >
              {{ updatingId === r.id ? "Updating..." : "Approve" }}
            </button>
            <button
              @click="updateStatus(r.id, 'canceled')"
              :disabled="updatingId === r.id"
            >
              {{ updatingId === r.id ? "Updating..." : "Cancel" }}
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import axios from "axios";
import { addToast } from "../stores/ui";

export default {
  data() {
    return { requests: [], isAdmin: false, loading: false, updatingId: null };
  },
  methods: {
    async fetch() {
      this.loading = true;
      try {
        const res = await axios.get("/api/travel-requests");
        this.requests = res.data.data || res.data;
      } finally {
        this.loading = false;
      }
    },
    async updateStatus(id, status) {
      try {
        this.updatingId = id;
        await axios.patch(`/api/travel-requests/${id}/status`, { status });
        this.updatingId = null;
        addToast("Status atualizado", "success");
        this.fetch();
      } catch (e) {
        const msg = e.response?.data?.message || "Error updating status";
        addToast(msg, "error");
        this.updatingId = null;
      }
    },
  },
  mounted() {
    const token = localStorage.getItem("token");
    if (token)
      axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
    axios
      .get("/api/user")
      .then((r) => {
        this.isAdmin = !!r.data.is_admin;
      })
      .catch(() => {});
    this.fetch();
  },
};
</script>

<style scoped>
table {
  width: 100%;
  border-collapse: collapse;
}
th,
td {
  padding: 0.5rem;
  text-align: left;
}
button {
  padding: 0.25rem 0.5rem;
  margin-right: 0.25rem;
}
</style>
