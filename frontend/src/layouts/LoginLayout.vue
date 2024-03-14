
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

        <q-space />
        <q-btn class="q-mr-sm" round dense flat icon="notifications">
            <q-badge floating color="red" rounded transparent>
                3
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
      <q-page class="q-pa-sm">
        <div style="height: 88vh;">
          <router-view />
        </div>
      </q-page>
    </q-page-container>

    <!-- <q-footer reveal class="bg-grey-5 text-weight-thin text-blue-white-9 text-center q-pt-lg q-pb-lg">
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

const linksList = [
  {
    title: 'Dashboard',
    icon: 'dashboard',
    link: 'dashboard',
    code: 101,
    crumbs: [
      {label: '', icon: 'home', link: '/'},
      {label: 'Dashboard', icon: 'dashboard', link: 'dashboard'}
    ]
  },
  {
    title: 'Manage Users',
    icon: 'manage_accounts',
    link: 'usermanagement',
    code: 102,
    crumbs: [
      {label: '', icon: 'home', link: '/'},
      {label: 'Manage Users', icon: 'manage_accounts', link: 'usermanagement'}
    ]
  },
  {
    title: 'Auction List',
    icon: 'view_list',
    link: 'auctionlist',
    code: 102,
    crumbs: [
      {label: '', icon: 'home', link: '/'},
      {label: 'Manage Users', icon: 'manage_accounts', link: 'usermanagement'}
    ]
  },
  {
    title: 'Loans List',
    icon: 'diamond',
    link: 'loanslist',
    code: 102,
    crumbs: [
      {label: '', icon: 'home', link: '/'},
      {label: 'Manage Users', icon: 'manage_accounts', link: 'usermanagement'}
    ]
  },
  // {
  //   title: 'Sales List',
  //   icon: 'point_of_sale',
  //   link: 'saleslist',
  //   code: 102,
  //   crumbs: [
  //     {label: '', icon: 'home', link: '/'},
  //     {label: 'Manage Users', icon: 'manage_accounts', link: 'usermanagement'}
  //   ]
  // },
  {
    title: 'Reports',
    icon: 'print',
    link: 'reports',
    code: 102,
    crumbs: [
      {label: '', icon: 'home', link: '/'},
      {label: 'Manage Users', icon: 'manage_accounts', link: 'usermanagement'}
    ]
  },
  {
    title: 'Configurations',
    icon: 'tune',
    link: 'configuration',
    code: 102,
    crumbs: [
      {label: '', icon: 'home', link: '/'},
      {label: 'Manage Users', icon: 'manage_accounts', link: 'usermanagement'}
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
      miniState: false
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
    } else {
      this.$router.push('/')
    }
    
  },
  methods: {
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