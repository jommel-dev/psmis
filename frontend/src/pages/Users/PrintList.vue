<template>
  <div>
    <Records />
  </div>
</template>

<script>
import { defineComponent } from 'vue';
import Records from '../../components/Clinic/Records/ListPrint';

export default defineComponent({
  name: 'PagePrint',
  components: {
    Records
  },
})
</script>
