<template>
  <div>
    <Records />
  </div>
</template>

<script>
import { defineComponent } from 'vue';
import Records from '../../components/Clinic/Records/List';

export default defineComponent({
  name: 'PageDashboard',
  components: {
    Records
  },
})
</script>
