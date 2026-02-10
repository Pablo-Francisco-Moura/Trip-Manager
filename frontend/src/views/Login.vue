<template>
  <div class="login-wrap">
    <div class="brand">Trip Manager</div>
    <div class="login-card">
      <h2 class="login-title">Login</h2>
      <form @submit.prevent="submit" class="login-form">
        <div class="field">
          <label class="field-label">Email</label>
          <input
            class="field-input"
            v-model="email"
            type="email"
            autocomplete="username"
          />
        </div>
        <div class="field">
          <label class="field-label">Password</label>
          <input
            class="field-input"
            v-model="password"
            type="password"
            autocomplete="current-password"
          />
        </div>
        <div class="actions">
          <button class="btn" :disabled="loading" type="submit">
            {{ loading ? "Logging..." : "Login" }}
          </button>
        </div>
      </form>
      <p v-if="error" class="error">{{ error }}</p>
    </div>
  </div>
</template>

<script>
import axios from "axios";
export default {
  data() {
    return { email: "", password: "", error: null, loading: false };
  },
  methods: {
    async submit() {
      this.error = null;
      this.loading = true;
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
        this.error = e.response?.data?.message || e.message || "Login failed";
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>

<style scoped>
.login-wrap {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 2.5rem 1rem;
  min-height: 80vh;
}
.login-card {
  width: 100%;
  max-width: 420px;
  background: var(--card, #ffffff);
  border: 1px solid var(--table-head, #eef4ff);
  box-shadow: 0 6px 18px rgba(11, 38, 72, 0.06);
  padding: 1.5rem;
  border-radius: 12px;
}
.login-title {
  margin: 0 0 1rem 0;
  font-size: 1.25rem;
  font-weight: 800;
  color: var(--primary, #123a8a);
}
.brand {
  text-align: center;
  font-size: 1.6rem;
  font-weight: 900;
  color: var(--primary, #123a8a);
  margin-bottom: 0.6rem;
}
.login-form .field {
  display: flex;
  flex-direction: column;
  margin-bottom: 0.75rem;
}
.field-label {
  font-weight: 700;
  margin-bottom: 0.35rem;
  color: var(--fg, #213547);
}
.field-input {
  padding: 0.55rem 0.6rem;
  border-radius: 8px;
  border: 1px solid var(--table-head, #d7e3ff);
  background: var(--bg, #fff);
  color: var(--fg, #213547);
  font-weight: 600;
  box-sizing: border-box;
}
.actions {
  display: flex;
  justify-content: flex-end;
}
.btn {
  background: var(--primary, #2b7cff);
  color: var(--bg, #fff);
  border: none;
  padding: 0.55rem 0.9rem;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 700;
}
.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
.error {
  color: var(--danger, #c0392b);
  margin-top: 0.75rem;
  font-weight: 600;
}

@media (max-width: 480px) {
  .login-card {
    padding: 1rem;
    border-radius: 10px;
  }
  .actions {
    justify-content: stretch;
  }
  .btn {
    width: 100%;
  }
}
</style>
