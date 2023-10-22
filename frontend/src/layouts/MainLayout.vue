<template>
  <q-layout view="lHh Lpr lFf">
    <!-- <q-header class="bg-header-custom" elevated>
      <q-toolbar>
        <q-toolbar-title>
          Pawnshop Management Information System
        </q-toolbar-title>
      </q-toolbar>
    </q-header> -->
    <q-page-container>
      <router-view />
    </q-page-container>
    <q-footer reveal class="bg-footer-custom text-weight-thin text-blue-white-9 text-center q-pt-lg q-pb-lg">
      <div>{{ `Pawnshop Management Information System ©2023 Created by FWDS` }}</div>
    </q-footer>
  </q-layout>
</template>

<script>
import { LocalStorage, SessionStorage } from 'quasar'
import jwt_decode from 'jwt-decode'
import { defineComponent, ref } from 'vue'

export default defineComponent({
  name: 'MainLayout',
  created(){
    let profile = SessionStorage.getItem('userDataLogin');
    
    if(profile || profile !== null){
      if(Number(profile.userType) === 1){
        this.$router.push('/dashboard')
      } else {
        this.$router.push('user/dashboard')
      }
      
    } else {
      this.$router.push('/')
    }
  }
})
</script>
<style scoped>
.bg-footer-custom{
  background: #003978;
}
.bg-header-custom{
  background: #fed200;
}
</style>