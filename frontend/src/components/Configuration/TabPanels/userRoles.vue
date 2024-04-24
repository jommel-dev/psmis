<template>
  <div class="q-pa-xs">
    this iss user role
  </div>
</template>

<script>
import { LocalStorage } from 'quasar'
import moment from 'moment';
import jwt_decode from 'jwt-decode'
import { api } from 'boot/axios'

export default {
    name: 'AuctionList',
    components: {},
    data(){
      return {
        // Print
        openSellModal: false,
        openItem: null,
        price: 0,
        discount: 0,
        totalPrice: 0,
        officialReceipt: '',
        isReported: {label: 'Recorded', value: 0},

        isLoading: false,
        filter: '',
        rows: [],

      }
    },
    computed: {
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return jwt_decode(profile);
        },
    },
    methods:{
        moment,
    }
}
</script>
<style scoped>
.itemCursor{
    cursor: pointer;
}
.itemHover:hover{
    background: aliceblue;
}
.my-card{
    border-radius: 15px;
    box-shadow: 0px 0px 3px -2px !important;
}
.my-card-item{
    border-radius: 10px;
}
.modalItemBorder{
  border-radius: 10px;
  border: 1px solid grey;
}
</style>
