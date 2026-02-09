<template>
  <div style="max-width: 400px; margin: 2rem auto">
    <h2>Login</h2>
    <form @submit.prevent="submit">
      <div>
        <label>Email</label>
        <input v-model="email" type="email" />
      </div>
      <div>
        <label>Password</label>
        <input v-model="password" type="password" />
      </div>
      <button type="submit">Login</button>
    </form>
    <p v-if="error" style="color: red">{{ error }}</p>
  </div>
</template>

<script>
import axios from "axios";
export default {
  data() {
    return { email: "", password: "", error: null };
  },
  methods: {
    async submit() {
      try {
        const res = await axios.post("/api/login", {
          email: this.email,
          password: this.password,
        });
        const token = res.data.token;
        localStorage.setItem("token", token);
        axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
        this.$router.push("/dashboard");
      } catch (e) {
        // Prefer server message, fallback to network/error message
        this.error = e.response?.data?.message || e.message || "Login failed";
      }
    },
  },
};
</script>
