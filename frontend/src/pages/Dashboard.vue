<template>
  <div>
    <Dashboard />
  </div>
</template>

<script>
import { defineComponent } from 'vue';
import Dashboard from '../components/Dashboard/NewDashboard';

export default defineComponent({
  name: 'PageDashboard',
  components: {
    Dashboard
  },
})
</script>
