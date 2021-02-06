<template>
  <span class="badge badge-striped">
    <i :class="getStatusClass()"></i>&nbsp;
    {{ getStatusText() }}
  </span>
</template>

<script>
export default {
  props: {
    status: {
      type: String,
      required: true,
      default: "",
    },
  },
  methods: {
    getStatusClass: function () {
      return this.status == "sent"
        ? "far fa-paper-plane"
        : this.status == "posted"
        ? "far fa-envelope"
        : "fas fa-ban";
    },
    getStatusText: function () {
      return this.status.charAt(0).toUpperCase() + this.status.slice(1);
    },
  },
};
</script>

<style>
.badge {
  background-color: #e7ebf1c4;
  font-size: 14px;
  min-width: 75px;
  padding: 8px;
}
</style>