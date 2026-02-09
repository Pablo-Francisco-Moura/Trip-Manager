<template>
  <div class="trip-form">
    <h3>Create Request</h3>
    <form @submit.prevent="submit" :class="{ loading: loading }" class="form">
      <div class="form-row">
        <label class="form-label">Requester</label>
        <input
          class="form-input"
          :value="requester_name"
          name="requester_name"
          placeholder="Nome do solicitante"
          disabled
        />
      </div>

      <div class="form-row">
        <label class="form-label">Destination</label>
        <input
          class="form-input"
          v-model="destination"
          name="destination"
          placeholder="Destino"
        />
      </div>

      <div class="form-row">
        <label class="form-label">Departure</label>
        <input
          class="form-input"
          v-model="departure_date"
          name="departure_date"
          type="date"
          @change="onDepartureChange"
          :max="maxDepartureDate"
        />
      </div>

      <div class="form-row">
        <label class="form-label">Return</label>
        <input
          class="form-input"
          v-model="return_date"
          name="return_date"
          type="date"
          :min="minReturnDate"
          @change="onReturnChange"
        />
      </div>

      <div class="form-actions">
        <button class="btn" type="submit">Create</button>
        <span class="message" v-if="message">{{ message }}</span>
      </div>
    </form>
  </div>
</template>

<script>
import axios from "axios";
import { addToast } from "../stores/ui";
export default {
  data() {
    return {
      requester_name: "",
      requester_id: null,
      destination: "",
      departure_date: "",
      return_date: "",
      message: null,
      loading: false,
      minReturnDate: null,
      maxDepartureDate: null,
    };
  },
  // using native date inputs bound to ISO strings
  methods: {
    onDepartureChange() {
      // set minimum allowed return date to departure date (ISO)
      if (this.departure_date) {
        this.minReturnDate = this.departure_date;
        // if current return date is before departure, reset it
        if (this.return_date && this.return_date < this.departure_date) {
          this.return_date = this.departure_date;
        }
      } else {
        this.minReturnDate = null;
      }
    },
    onReturnChange() {
      // ensure departure_date is not after return_date
      if (this.return_date) {
        this.maxDepartureDate = this.return_date;
        if (this.departure_date && this.departure_date > this.return_date) {
          this.departure_date = this.return_date;
        }
      } else {
        this.maxDepartureDate = null;
      }
    },
    // parseDate removed; using native date inputs which provide ISO YYYY-MM-DD
    formatDate(value) {
      if (!value) return "";
      try {
        const s = String(value);
        const datePart = s.split("T")[0].split(" ")[0];
        const parts = datePart.split("-");
        if (parts.length === 3) return `${parts[2]}/${parts[1]}/${parts[0]}`;
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
    async submit() {
      this.loading = true;
      // Validate dates: return_date must not be before departure_date
      if (
        this.departure_date &&
        this.return_date &&
        this.return_date < this.departure_date
      ) {
        const msg = "A data de volta não pode ser anterior à data de ida.";
        this.message = msg;
        addToast(msg, "error");
        this.loading = false;
        return;
      }
      try {
        await axios.post("/api/travel-requests", {
          destination: this.destination,
          departure_date: this.departure_date,
          return_date: this.return_date,
        });
        this.message = "Created";
        addToast("Pedido criado com sucesso", "success");
        this.destination = "";
        this.departure_date = "";
        this.return_date = "";
        this.$emit("created");
      } catch (e) {
        const msg = e.response?.data?.message || "Error creating request";
        this.message = msg;
        addToast(msg, "error");
      } finally {
        this.loading = false;
      }
    },
    async fetchUser() {
      try {
        const r = await axios.get("/api/user");
        this.requester_name = r.data.name || "";
        this.requester_id = r.data.id || null;
      } catch (e) {
        // ignore
      }
    },
  },
  mounted() {
    this.fetchUser();
  },
};
</script>

<style scoped>
.trip-form {
  margin-bottom: 1rem;
  max-width: 700px;
  background: #fafafa;
  border: 1px solid #eee;
  padding: 1rem;
  border-radius: 8px;
}

/* Ensure elements calculate widths including padding/borders to avoid overflow */
.trip-form,
.form,
.form-row,
.form-input {
  box-sizing: border-box;
}
.trip-form h3 {
  margin: 0 0 0.75rem 0;
}
.form {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.75rem;
  align-items: center;
}
.form-row {
  display: flex;
  flex-direction: column;
}
.form-label {
  font-size: 0.85rem;
  color: #555;
  margin-bottom: 0.25rem;
}
.form-input {
  padding: 0.5rem;
  border: 1px solid #d0d0d0;
  border-radius: 6px;
  font-size: 0.95rem;
}
.form-actions {
  grid-column: 1 / -1;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-top: 0.5rem;
}
.btn {
  padding: 0.5rem 0.8rem;
  background: #2b7cff;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}
.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
.message {
  color: #333;
  font-size: 0.95rem;
}
.loading {
  opacity: 0.6;
}
.small-muted {
  font-size: 0.85rem;
  color: #666;
  margin-top: 0.25rem;
}

@media (max-width: 600px) {
  .form {
    grid-template-columns: 1fr;
  }
  .form-row {
    width: 100%;
  }
  .form-input {
    width: 100%;
  }
  .form-actions {
    flex-direction: column;
    align-items: stretch;
  }
  .btn {
    width: 100%;
  }
}
</style>
