<template>
  <div class="toasts" aria-live="polite">
    <div v-for="t in toasts" :key="t.id" :class="['toast', t.type]">
      <span>{{ t.message }}</span>
      <button @click="dismiss(t.id)">×</button>
    </div>
  </div>
</template>

<script>
import ui, { removeToast } from "../stores/ui";
export default {
  setup() {
    return { toasts: ui.toasts, dismiss: removeToast };
  },
};
</script>

<style scoped>
.toasts {
  position: fixed;
  right: 1rem;
  top: 1rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  z-index: 1000;
}
.toast {
  padding: 0.5rem 0.75rem;
  border-radius: 4px;
  color: #fff;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.toast.info {
  background: #2196f3;
}
.toast.success {
  background: #4caf50;
}
.toast.error {
  background: #f44336;
}
.toast button {
  background: transparent;
  border: none;
  color: inherit;
  font-weight: bold;
  cursor: pointer;
}
</style>
