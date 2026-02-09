<template>
  <div style="padding: 1rem">
    <h1>Trip Requests</h1>
    <TravelRequestForm @created="reload" />
    <TravelRequestsTable ref="table" />
  </div>
</template>

<script>
import TravelRequestForm from "../components/TravelRequestForm.vue";
import TravelRequestsTable from "../components/TravelRequestsTable.vue";

export default {
  components: { TravelRequestForm, TravelRequestsTable },
  methods: {
    reload() {
      this.$refs.table.fetch();
    },
  },
};
</script>
