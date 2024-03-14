<template>
  <q-page>
    <ReportFilterPanel />
  </q-page>
</template>

<script>
import ReportFilterPanel from '../components/Reports/Index.vue';
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'AuctionedList',
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
