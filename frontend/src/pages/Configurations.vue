<template>
  <q-page>
    <ReportFilterPanel />
  </q-page>
</template>

<script>
import ReportFilterPanel from '../components/Configuration/Index.vue';
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'ConfigurationsList',
  components: {
    ReportFilterPanel
  },
  data(){
    return {}
  },
  methods: {
    
  }
})
</script>