<template>
  <div class="requests-table">
    <div class="requests-header">
      <h3>Requests</h3>
      <button
        class="create-btn"
        :title="showCreateForm ? 'Fechar formulário' : 'Criar nova requisição'"
        @click="toggleCreateForm"
      >
        <span v-if="!showCreateForm">+</span>
        <span v-else>&times;</span>
      </button>
    </div>
    <div class="user-info-row">
      <div v-if="isAdmin" class="admin-badge">
        Você é administrador — pode alterar o status das requisições
      </div>
      <div v-else class="user-badge">
        Você não é administrador — apenas pode visualizar seus pedidos
      </div>
    </div>
    <div v-if="showCreateForm" class="create-form-wrap">
      <TravelRequestForm @created="handleCreated" />
    </div>

    <div class="filter-row">
      <label>Filter:</label>
      <select class="filter-select" v-model="filterStatus">
        <option value="">All</option>
        <option value="requested">Requested</option>
        <option value="approved">Approved</option>
        <option value="canceled">Canceled</option>
      </select>
      <input
        class="filter-input"
        v-model="destinationFilter"
        placeholder="Destination"
      />
      <div class="filter-field">
        <label class="filter-field-label">Data de ida</label>
        <input
          class="date-input"
          type="date"
          v-model="departureFrom"
          title="Departure from"
          :max="filterMaxDeparture"
          @change="onFilterDepartureChange"
        />
      </div>
      <div class="filter-field">
        <label class="filter-field-label">Data de volta</label>
        <input
          class="date-input"
          type="date"
          v-model="departureTo"
          title="Departure to"
          :min="filterMinReturn"
          @change="onFilterReturnChange"
        />
      </div>
      <div class="filter-actions">
        <button class="btn" @click="applyFilters">Apply</button>
        <button class="btn" @click="clearFilters">Clear</button>
      </div>
    </div>

    <div v-if="loading" class="loading">Loading...</div>

    <div v-else class="table-wrap">
      <table class="styled-table" cellpadding="6">
        <thead>
          <tr>
            <th>ID</th>
            <th>Requester</th>
            <th>Destination</th>
            <th>Departure</th>
            <th>Return</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="r in requests" :key="r.id" class="table-row">
            <td data-label="ID">{{ r.id }}</td>
            <td data-label="Requester">
              {{ (r.user && r.user.name) || r.requester_name }}
            </td>
            <td data-label="Destination">{{ r.destination }}</td>
            <td data-label="Departure">{{ formatDate(r.departure_date) }}</td>
            <td data-label="Return">{{ formatDate(r.return_date) }}</td>
            <td data-label="Status">
              <span :class="['status-chip', 'status-' + (r.status || '')]">
                {{ r.status }}
              </span>
            </td>
            <td data-label="Actions">
              <template v-if="isAdmin && r.status === 'requested'">
                <button
                  @click="openConfirm(r.id, 'approved')"
                  :disabled="updatingId === r.id || r.status === 'approved'"
                  class="action-btn approve"
                >
                  {{ updatingId === r.id ? "Updating..." : "Approve" }}
                </button>
                <button
                  @click="openConfirm(r.id, 'canceled')"
                  :disabled="updatingId === r.id || r.status === 'approved'"
                  title="Não é possível cancelar um pedido aprovado"
                  class="action-btn cancel"
                >
                  {{ updatingId === r.id ? "Updating..." : "Cancel" }}
                </button>
              </template>
              <button class="action-btn view" @click="viewRequest(r.id)">
                View
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <!-- Modal for viewing details -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-card">
        <h4>Request #{{ modalData.id }}</h4>
        <div class="modal-body">
          <p><strong>Requester:</strong> {{ modalData.requester_name }}</p>
          <p><strong>Destination:</strong> {{ modalData.destination }}</p>
          <p>
            <strong>Departure:</strong>
            {{ formatDate(modalData.departure_date) }}
          </p>
          <p>
            <strong>Return:</strong> {{ formatDate(modalData.return_date) }}
          </p>
          <p><strong>Status:</strong> {{ modalData.status }}</p>
          <p v-if="modalData.created_at">
            <strong>Created:</strong> {{ formatDate(modalData.created_at) }}
          </p>
        </div>
        <div class="modal-actions">
          <button class="btn" @click="closeModal">Close</button>
        </div>
      </div>
    </div>
    <!-- Confirmation modal for approve/cancel -->
    <div v-if="confirmVisible" class="modal-overlay" @click.self="closeConfirm">
      <div class="modal-card">
        <h4>Confirmação</h4>
        <div class="modal-body">
          <p>
            Tem certeza que deseja <strong>{{ confirmData.status }}</strong> o
            pedido #{{ confirmData.id }}?
          </p>
        </div>
        <div class="modal-actions">
          <button class="btn" @click="confirmExecute">Confirmar</button>
          <button class="btn" @click="closeConfirm" style="margin-left: 0.5rem">
            Cancelar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { addToast } from "../stores/ui";
import TravelRequestForm from "./TravelRequestForm.vue";

export default {
  components: { TravelRequestForm },
  data() {
    return {
      showCreateForm: false,
      requests: [],
      isAdmin: false,
      loading: false,
      updatingId: null,
      confirmVisible: false,
      confirmData: { id: null, status: null },
      filterStatus: "",
      destinationFilter: "",
      departureFrom: "",
      departureTo: "",
      filterMinReturn: null,
      filterMaxDeparture: null,
      showModal: false,
      modalData: {},
      currentUser: null,
      refreshIntervalId: null,
    };
  },
  // using native date inputs for filters
  methods: {
    toggleCreateForm() {
      this.showCreateForm = !this.showCreateForm;
    },
    handleCreated() {
      this.showCreateForm = false;
      this.fetch();
    },
    // openPicker removed; using visible native date inputs
    async fetch() {
      this.loading = true;
      // keep profile up-to-date so admin controls render correctly
      await this.refreshUser?.();
      try {
        const params = {};
        if (this.filterStatus) params.status = this.filterStatus;
        if (this.destinationFilter) params.destination = this.destinationFilter;
        // backend expects 'from' and 'to' to filter by departure_date
        if (this.departureFrom) params.from = this.departureFrom;
        if (this.departureTo) params.to = this.departureTo;
        const res = await axios.get("/api/travel-requests", { params });
        this.requests = res.data.data || res.data;
      } finally {
        this.loading = false;
      }
    },
    applyFilters() {
      // Validate date range: departureTo must not be before departureFrom
      if (
        this.departureFrom &&
        this.departureTo &&
        this.departureTo < this.departureFrom
      ) {
        addToast(
          'A data "Departure to" não pode ser anterior à "Departure from".',
          "error",
        );
        return;
      }
      this.fetch();
    },
    onFilterDepartureChange() {
      // when departureFrom is set, ensure departureTo >= departureFrom
      if (this.departureFrom) {
        this.filterMinReturn = this.departureFrom;
        if (this.departureTo && this.departureTo < this.departureFrom) {
          this.departureTo = this.departureFrom;
          addToast("Data de volta ajustada para ser >= data de ida", "info");
        }
      } else {
        this.filterMinReturn = null;
      }
    },
    onFilterReturnChange() {
      // when departureTo is set, ensure departureFrom <= departureTo
      if (this.departureTo) {
        this.filterMaxDeparture = this.departureTo;
        if (this.departureFrom && this.departureFrom > this.departureTo) {
          this.departureFrom = this.departureTo;
          addToast("Data de ida ajustada para ser <= data de volta", "info");
        }
      } else {
        this.filterMaxDeparture = null;
      }
    },
    // parseDate removed; native date inputs provide ISO (YYYY-MM-DD)
    formatDate(value) {
      if (!value) return "";
      try {
        const s = String(value);
        const datePart = s.split("T")[0].split(" ")[0];
        const parts = datePart.split("-");
        if (parts.length === 3) {
          return `${parts[2]}/${parts[1]}/${parts[0]}`;
        }
        const d = new Date(value);
        if (isNaN(d)) return value;
        const dd = String(d.getDate()).padStart(2, "0");
        const mm = String(d.getMonth() + 1).padStart(2, "0");
        const yyyy = d.getFullYear();
        return `${dd}/${mm}/${yyyy}`;
      } catch (e) {
        return value;
      }
    },
    clearFilters() {
      this.filterStatus = "";
      this.destinationFilter = "";
      this.departureFrom = "";
      this.departureTo = "";
      this.filterMinReturn = null;
      this.filterMaxDeparture = null;
      this.fetch();
    },
    async viewRequest(id) {
      try {
        this.loading = true;
        const res = await axios.get(`/api/travel-requests/${id}`);
        this.modalData = res.data.data || res.data;
        this.showModal = true;
      } catch (e) {
        const msg = e.response?.data?.message || "Error fetching request";
        addToast(msg, "error");
      } finally {
        this.loading = false;
      }
    },
    closeModal() {
      this.showModal = false;
      this.modalData = {};
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
    async refreshUser() {
      try {
        const res = await axios.get("/api/user");
        this.currentUser = res.data;
        this.isAdmin = !!res.data.is_admin;
      } catch (e) {
        if (e.response?.status === 401) {
          localStorage.removeItem("token");
          delete axios.defaults.headers.common["Authorization"];
        }
        this.currentUser = null;
        this.isAdmin = false;
      }
    },
    openConfirm(id, status) {
      this.confirmData = { id, status };
      this.confirmVisible = true;
    },
    async confirmExecute() {
      const { id, status } = this.confirmData;
      this.confirmVisible = false;
      await this.updateStatus(id, status);
      this.confirmData = { id: null, status: null };
    },
    closeConfirm() {
      this.confirmVisible = false;
      this.confirmData = { id: null, status: null };
    },
    statusClass(status) {
      return `status-${status}`;
    },
  },
  mounted() {
    const token = localStorage.getItem("token");
    if (token)
      axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
    // load profile and requests, then poll for profile changes
    this.refreshUser().then(() => this.fetch());
    this.refreshIntervalId = setInterval(() => this.refreshUser(), 5000);
  },

  beforeUnmount() {
    if (this.refreshIntervalId) clearInterval(this.refreshIntervalId);
  },

  // Vue 2 compatibility
  beforeDestroy() {
    if (this.refreshIntervalId) clearInterval(this.refreshIntervalId);
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
.requests-header {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.create-btn {
  margin-left: 0.5rem;
  background: #2b7cff;
  color: #fff;
  border: none;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  font-size: 22px;
  line-height: 1;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}
.create-form-wrap {
  margin: 0.5rem 0 1rem;
}
.filter-row {
  display: flex;
  gap: 0.5rem;
  align-items: center;
  margin-bottom: 0.5rem;
  flex-wrap: wrap;
}

.filter-field {
  display: flex;
  flex-direction: column;
  min-width: 180px;
}
.filter-field-label {
  font-size: 0.75rem;
  color: var(--muted, #335);
  margin-bottom: 0.2rem;
  font-weight: 700;
}

.admin-badge {
  display: inline-block;
  background: #e8f4ff;
  color: #0b3b9a;
  padding: 0.45rem 0.7rem;
  border-radius: 8px;
  font-weight: 700;
  margin: 0.4rem 0 0.6rem 0;
}

.user-badge {
  display: inline-block;
  background: #fff6e6;
  color: #6a4a00;
  padding: 0.45rem 0.7rem;
  border-radius: 8px;
  font-weight: 600;
  margin: 0.4rem 0 0.6rem 0;
}

.filter-input {
  padding: 0.45rem 0.6rem;
  border-radius: 8px;
  border: 1px solid var(--table-head, #e6eefc);
  background: var(--card, #fff);
  color: var(--fg, #123a8a);
  font-weight: 600;
}
.date-input {
  padding: 0.35rem 0.5rem;
  border-radius: 8px;
  border: 1px solid var(--table-head, #e6eefc);
  background: var(--card, #fff);
  color: var(--fg, #123a8a);
}
.filter-actions {
  display: flex;
  gap: 0.4rem;
  margin-left: 0.25rem;
  flex-shrink: 0;
}

/* Filter control styled like a compact button */
.filter-select {
  padding: 0.45rem 0.7rem;
  border-radius: 8px;
  border: 1px solid var(--table-head, #d7e3ff);
  background: var(--card, linear-gradient(180deg, #f8fbff, #eef6ff));
  cursor: pointer;
  font-weight: 600;
  color: var(--fg, #123a8a);
}
.filter-row label {
  font-weight: 700;
  color: var(--primary, #123a8a);
}
.table-wrap {
  overflow-x: auto;
}
.styled-table {
  width: 100%;
  border-collapse: collapse;
  border: 1px solid var(--table-head, #e8eefc);
  background: var(--card, #fff);
}
.styled-table thead {
  background: var(--table-head, #f4f8ff);
}
.styled-table th {
  padding: 0.6rem;
  color: var(--primary, #123a8a);
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

/* Status chips */
.status-chip {
  padding: 0.25rem 0.6rem;
  border-radius: 6px;
  font-weight: 700;
  display: inline-block;
  font-size: 0.9rem;
}
.status-requested {
  background: #ffd740; /* yellow */
  color: #5a3b00;
}
.status-approved {
  background: #2b7cff; /* blue */
  color: #fff;
}
.status-canceled {
  background: #ff5b5b; /* red */
  color: #fff;
}

/* Responsive: convert rows to card-like blocks */
@media (max-width: 700px) {
  .styled-table thead {
    display: none;
  }
  .filter-row {
    flex-direction: column;
    align-items: stretch;
    gap: 0.5rem;
  }
  .filter-field {
    width: 100%;
    min-width: 0;
  }
  .filter-actions {
    width: 100%;
    justify-content: flex-start;
    margin-left: 0;
  }
  .filter-input,
  .date-input,
  .filter-select {
    width: 100%;
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

/* Modal */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.45);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  z-index: 1200;
}
.modal-card {
  max-width: 520px;
  width: 100%;
  background: var(--card, #fff);
  color: var(--fg, #213547);
  border-radius: 10px;
  padding: 1rem 1.25rem;
  box-shadow: 0 18px 48px rgba(11, 38, 72, 0.22);
  border: 1px solid rgba(0, 0, 0, 0.04);
}
.modal-card h4 {
  margin: 0 0 0.6rem 0;
  color: var(--primary, #123a8a);
}
.modal-body p {
  margin: 0.25rem 0;
}
.modal-actions {
  margin-top: 0.75rem;
  text-align: right;
}
.action-btn.view {
  background: var(--muted, #6b7280);
  color: var(--bg, #fff);
}
</style>
