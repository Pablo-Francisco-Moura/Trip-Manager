import axios from "axios";

export default {
  async login(email, password) {
    const res = await axios.post("/api/login", { email, password });
    const token = res.data.token;
    this.setToken(token);
    return res;
  },

  logout() {
    localStorage.removeItem("token");
    delete axios.defaults.headers.common["Authorization"];
  },

  setToken(token) {
    if (token) {
      localStorage.setItem("token", token);
      axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
    }
  },

  token() {
    return localStorage.getItem("token");
  },
};
