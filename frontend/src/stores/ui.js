import { reactive } from "vue";

const state = reactive({ toasts: [] });

export function addToast(message, type = "info", timeout = 4000) {
  const id = Date.now() + Math.random();
  state.toasts.push({ id, message, type });
  if (timeout > 0) setTimeout(() => removeToast(id), timeout);
}

export function removeToast(id) {
  const idx = state.toasts.findIndex((t) => t.id === id);
  if (idx !== -1) state.toasts.splice(idx, 1);
}

export default state;
