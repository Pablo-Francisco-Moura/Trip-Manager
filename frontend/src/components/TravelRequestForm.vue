<template>
  <div style="margin-bottom: 1rem">
    <h3>Create Request</h3>
    <form @submit.prevent="submit" :class="{ loading: loading }">
      <div style="display: flex; gap: 0.5rem; align-items: center">
        <input v-model="requester_name" placeholder="Requester name" />
        <input v-model="destination" placeholder="Destination" />
        <input v-model="departure_date" type="date" />
        <input v-model="return_date" type="date" />
        <button type="submit">Create</button>
      </div>
    </form>
    <p v-if="message">{{ message }}</p>
  </div>
</template>

<script>
import axios from "axios";
import { addToast } from "../stores/ui";
export default {
  data() {
    return {
      requester_name: "",
      destination: "",
      departure_date: "",
      return_date: "",
      message: null,
      loading: false,
    };
  },
  methods: {
    async submit() {
      this.loading = true;
      try {
        await axios.post("/api/travel-requests", {
          requester_name: this.requester_name,
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
form {
  display: flex;
  gap: 0.5rem;
  align-items: center;
}
input {
  padding: 0.4rem;
  border: 1px solid #ccc;
  border-radius: 4px;
}
button {
  padding: 0.4rem 0.6rem;
  border-radius: 4px;
  border: 1px solid #888;
  background: #fff;
}
.loading {
  opacity: 0.6;
}
</style>
