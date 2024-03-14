<template>
  <q-page>
    <UserList />
  </q-page>
</template>

<script>
import UserList from '../components/Loans/Index.vue';
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'AuctionedList',
  components: {
    UserList
  },
  data(){
    return {}
  },
  methods: {
    
  }
})
</script>
