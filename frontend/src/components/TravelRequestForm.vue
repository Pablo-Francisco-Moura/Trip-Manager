<template>
  <div style="margin-bottom: 1rem">
    <h3>Create Request</h3>
    <form @submit.prevent="submit">
      <input v-model="destination" placeholder="Destination" />
      <input v-model="departure_date" type="date" />
      <input v-model="return_date" type="date" />
      <button type="submit">Create</button>
    </form>
    <p v-if="message">{{ message }}</p>
  </div>
</template>

<script>
import axios from "axios";
export default {
  data() {
    return {
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
          destination: this.destination,
          departure_date: this.departure_date,
          return_date: this.return_date,
        });
        this.message = "Created";
        this.destination = "";
        this.departure_date = "";
        this.return_date = "";
        this.$emit("created");
      } catch (e) {
        this.message = e.response?.data?.message || "Error creating request";
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>
