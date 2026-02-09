<template>
  <div class="requests-table">
    <h3>Requests</h3>
    <div class="filter-row">
      <label>Filter:</label>
      <select class="filter-select" v-model="filterStatus" @change="fetch">
        <option value="">All</option>
        <option value="requested">Requested</option>
        <option value="approved">Approved</option>
        <option value="canceled">Canceled</option>
      </select>
    </div>

    <div v-if="loading" class="loading">Loading...</div>

    <div v-else class="table-wrap">
      <table class="styled-table" cellpadding="6">
        <thead>
          <tr>
            <th>Destination</th>
            <th>Departure</th>
            <th>Return</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="r in requests" :key="r.id" class="table-row">
            <td data-label="Destination">{{ r.destination }}</td>
            <td data-label="Departure">{{ r.departure_date }}</td>
            <td data-label="Return">{{ r.return_date }}</td>
            <td data-label="Status">{{ r.status }}</td>
            <td data-label="Actions">
              <template v-if="isAdmin">
                <button
                  @click="updateStatus(r.id, 'approved')"
                  :disabled="updatingId === r.id"
                  class="action-btn approve"
                >
                  {{ updatingId === r.id ? "Updating..." : "Approve" }}
                </button>
                <button
                  @click="updateStatus(r.id, 'canceled')"
                  :disabled="updatingId === r.id || r.status === 'approved'"
                  title="Não é possível cancelar um pedido aprovado"
                  class="action-btn cancel"
                >
                  {{ updatingId === r.id ? "Updating..." : "Cancel" }}
                </button>
              </template>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { addToast } from "../stores/ui";

export default {
  data() {
    return {
      requests: [],
      isAdmin: false,
      loading: false,
      updatingId: null,
      filterStatus: "",
    };
  },
  methods: {
    async fetch() {
      this.loading = true;
      try {
        const params = {};
        if (this.filterStatus) params.status = this.filterStatus;
        const res = await axios.get("/api/travel-requests", { params });
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

.requests-table {
  max-width: 1100px;
  margin: 0.5rem auto 1rem;
}
.filter-row {
  display: flex;
  gap: 0.5rem;
  align-items: center;
  margin-bottom: 0.5rem;
}

/* Filter control styled like a compact button */
.filter-select {
  padding: 0.45rem 0.7rem;
  border-radius: 8px;
  border: 1px solid #d7e3ff;
  background: linear-gradient(180deg, #f8fbff, #eef6ff);
  cursor: pointer;
  font-weight: 600;
  color: #123a8a;
}
.filter-row label {
  font-weight: 700;
  color: #123a8a;
}
.table-wrap {
  overflow-x: auto;
}
.styled-table {
  width: 100%;
  border-collapse: collapse;
  border: 1px solid #e8eefc;
  background: #fff;
}
.styled-table thead {
  background: #f4f8ff;
}
.styled-table th {
  padding: 0.6rem;
  color: #123a8a;
  font-weight: 600;
}
.styled-table td {
  border-top: 1px solid #f0f4ff;
  padding: 0.6rem;
}
.action-btn {
  padding: 0.4rem 0.6rem;
  border-radius: 6px;
  border: none;
  cursor: pointer;
}
.action-btn.approve {
  background: #2b7cff;
  color: #fff;
}
.action-btn.cancel {
  background: #ff5b5b;
  color: #fff;
}

/* Responsive: convert rows to card-like blocks */
@media (max-width: 700px) {
  .styled-table thead {
    display: none;
  }
  .table-row {
    display: block;
    margin-bottom: 0.75rem;
    border: 1px solid #eef4ff;
    border-radius: 8px;
    padding: 0.5rem;
    background: #fff;
  }
  .table-row td {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0.6rem;
    border: none;
  }
  .table-row td[data-label]::before {
    content: attr(data-label) ": ";
    font-weight: 600;
    color: #555;
    margin-right: 0.5rem;
  }
  .action-btn {
    margin: 0.25rem 0 0 0;
    width: 48%;
  }
  .table-row td[data-label="Actions"] {
    display: flex;
    gap: 0.5rem;
    flex-direction: row;
  }
}
</style>
