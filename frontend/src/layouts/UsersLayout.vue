
<template>
  <q-layout view="lHh LpR lFf">

    <q-header reveal bordered class="bg-header-custom">
      <q-toolbar>
        
        <!-- <q-btn 
          dense 
          flat 
          round 
          icon="menu"
          @click="toggleLeftDrawer" 
        /> -->
        
        <q-btn 
          dense
          color="indigo-10"
          round
          unelevated
          :icon="miniState ? 'navigate_next' : 'chevron_left'"
          class="drawerBtn"
          @click="toggleLeftDrawer"
        />
        <span class="q-ml-lg">
          Cut-off Date: {{cutOffDate}}
        </span>
        
        <q-space />
        <q-btn class="q-mr-sm" round dense flat icon="notifications">
            <q-badge floating color="red" rounded transparent>
                0
            </q-badge>
        </q-btn>
      </q-toolbar>
      <!-- <q-bar dense>
        <Crumbs :contentLink.sync="menuCrumbs" />
      </q-bar> -->
    </q-header>

    <q-drawer 
      show-if-above 
      v-model="leftDrawerOpen"
      :mini="miniState"
      side="left" 
      bordered
    >
      <!-- drawer content -->
      <Profile v-bind="userProfile" />
      <q-separator dark />
      <SideNav 
        v-for="link in filteredMenus"
        :key="link.title"
        v-bind="link"
        @linkCrumbs="setCrumbsItem"
      />
    </q-drawer>

    <q-page-container>
      <div v-if="notifStartDay" class="q-pa-md q-gutter-sm">
        <q-banner inline-actions rounded class="bg-orange text-black">
          The cutoff is not yet started, do you want to take action on this process? <br>
          <span class="text-caption"><i>Note: if <strong>NO</strong> kindly reach out to your Administrator.</i></span>

          <template v-slot:action>
            <q-btn @click="startDay" flat label="START THE DAY" />
            <q-btn @click="notifStartDay = false" flat label="Dismiss" />
          </template>
        </q-banner>
      </div>
      <q-page class="q-pa-sm">
        <div style="height: 88vh;">
          <router-view />
        </div>
      </q-page>
    </q-page-container>

    <!-- <q-footer reveal class="bg-text-weight-thin text-blue-white-9 text-center q-pt-lg q-pb-lg">
      <div>{{ `Centralize Distribution and Sales Inventory System ©2023 Created by FWDS` }}</div>
    </q-footer> -->

  </q-layout>
</template>

<script>
import { LocalStorage, SessionStorage } from 'quasar'
import jwt_decode from 'jwt-decode'
import SideNav from '../components/Templates/Sidenav.vue';
import Profile from '../components/Templates/Profile.vue';
import Crumbs from '../components/Templates/Breadcrumbs.vue';
import { api } from 'boot/axios'
import moment from 'moment';

const dateNow = moment().format('YYYY-MM-DD');

const linksList = [
  {
    title: 'Dashboard',
    icon: 'dashboard',
    link: 'userDashboard',
    code: 101,
    crumbs: [
      {label: '', icon: 'home', link: '/'},
      {label: 'Dashboard', icon: 'dashboard', link: 'dashboard'}
    ]
  },
  {
    title: 'Applications',
    icon: 'date_range',
    link: 'userApplications',
    code: 102,
    crumbs: [
      {label: '', icon: 'home', link: '/'},
      {label: 'Dashboard', icon: 'dashboard', link: 'dashboard'}
    ]
  },
  {
    title: 'Auction',
    icon: 'sell',
    link: 'auctionList',
    code: 102,
    crumbs: [
      {label: '', icon: 'home', link: '/'},
      {label: 'Dashboard', icon: 'dashboard', link: 'dashboard'}
    ]
  },
  {
    title: 'Spoiled Ticket',
    icon: 'subtitles_off',
    link: 'spoiledTicket',
    code: 102,
    crumbs: [
      {label: '', icon: 'home', link: '/'},
      {label: 'Dashboard', icon: 'dashboard', link: 'dashboard'}
    ]
  },
  {
    title: 'Print Pawn Tickets',
    icon: 'print',
    link: 'printList',
    code: 102,
    crumbs: [
      {label: '', icon: 'home', link: '/'},
      {label: 'Dashboard', icon: 'dashboard', link: 'dashboard'}
    ]
  },
];

export default {
  data(){
    return {
      linksList,
      userProfile: null,
      menuCrumbs: [
        {label: '', icon: 'home', link: '/'},
        {label: 'Dashboard', icon: 'dashboard', link: 'dashboard'}
      ],
      leftDrawerOpen: true,
      miniState: false,
      cutOffDate: "",
      notifStartDay: false
    }
  },
  mounted(){},
  components:{
    SideNav,
    Profile,
    Crumbs
  },
  computed: {
    filteredMenus: function(){
      return this.linksList;
    }
  },
  created(){
    // SessionStorage.set('userDataLogin', data.jwt);
    let profile = SessionStorage.getItem('userDataLogin');

    if(profile){
      this.userProfile = jwt_decode(profile);
      this.checkCutOffDates();
    } else {
      this.$router.push('/')
    }
    
  },
  methods: {
    checkCutOffDates(){
      api.get('misc/check/user/cutoffDate').then((response) => {
        const data = {...response.data};
        if(!data.error){
          SessionStorage.set("cutoff", JSON.stringify(data))
          this.cutOffDate = data.startDate
        }
      }).catch(e => {
        this.notifStartDay = true
        console.log(e)
      })
    },
    startDay(){
      let payload = {
        currDate: dateNow,
        userId: this.userProfile.userId
      }

      api.post('misc/cutoff/startDate', payload).then((response) => {
        const data = {...response.data};
        if(!data.error){
          this.notifStartDay = false;

          this.$nextTick(() => {
            this.checkCutOffDates()
          })
        }
      }).catch(e => {
        console.log(e)
      })
    },
    toggleLeftDrawer () {
      this.miniState = !this.miniState
    },
    setCrumbsItem(val){
      this.menuCrumbs = val;
    },
    logout(){
      localStorage.removeItem('userData');
      SessionStorage.remove('userDataLogin');
      this.$router.push('/')
    }
  }
}
</script>

<style scoped>
.drawerBtn{
  position: absolute;
  left: -15px;
}
.bg-header-custom{
  background: #003978;
}
</style>