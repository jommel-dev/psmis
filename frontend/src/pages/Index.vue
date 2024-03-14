<template>
  <q-page class="flex flex-center background-vikings">
    <Login />
  </q-page>
</template>

<script>
import { defineComponent } from 'vue';
import Login from '../components/Login/Index';

export default defineComponent({
  name: 'PageIndex',
  components: {
    Login
  },
  data(){
    return {
      sample: "Here it is"
    }
  }
})
</script>

<style scoped>
.background-vikings{
    /* height: 88vh; */
    background: url('/imgs/backimage.png') no-repeat;
}
</style>