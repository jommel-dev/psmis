<template>
  <div>
    <Records />
  </div>
</template>

<script>
import { defineComponent } from 'vue';
import Records from '../../components/Clinic/Spoiled/index';

export default defineComponent({
  name: 'PageSpoiled',
  components: {
    Records
  },
})
</script>
