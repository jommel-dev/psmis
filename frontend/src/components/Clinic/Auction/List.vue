<template>
  <div class="q-pa-xs">
    <q-card
        flat
        class="my-card bg-white"
    >
        <q-card-section>
            <span class="text-h5 text-bold">Auction List</span><br/>
            <span class="text-caption text-grey">List of items that is already expired and reach thier maturity date</span>
            <br/><br/>
            <q-table
                :rows="rows"
                :columns="tableColumns"
                row-key="orNumber"
                :filter="filter"
                :rows-per-page-options="[20]"
                :loading="isLoading"
            >
                <template v-slot:top-right>
                    <q-input outlined dense debounce="300" v-model="filter" placeholder="Search">
                        <template v-slot:append>
                            <q-icon name="search" />
                        </template>
                    </q-input>
                </template>
                <template v-slot:body-cell-actions="props">
                    <q-td :props="props">
                        <q-btn-group  rounded>
                            <q-btn @click="openModal(props.row)" rounded size="sm" color="green" label="Sell Item" />
                            <!-- <q-btn rounded size="sm" color="primary" label="Update Price" /> -->
                        </q-btn-group>
                    </q-td>
                </template>
            </q-table>
        </q-card-section>
    </q-card>

    <q-dialog v-model="openSellModal" transition-show="scale" transition-hide="scale">
      <q-card class="" style="width: 100vw">
        <q-card-section>
          <div class="text-h6">Sell Auction Item</div>
        </q-card-section>

        <q-card-section class="q-pt-none">
            <div class="row">
              <div class="col-12 col-sm-12 q-pa-sm">
                <span class="text-h5">Item Price: {{ openItem.principal }}</span>
              </div>
              <div class="col-12 col-sm-6 q-pa-sm">
                <q-input
                  class="q-mb-sm"
                  type="number"
                  outlined
                  v-model="price"
                  label="Sell Price"
                  stack-label
                  dense
                />
                <q-input
                  class="q-mb-sm"
                  type="number"
                  outlined
                  v-model="discount"
                  label="Item Discount"
                  stack-label
                  dense
                />
                <q-input
                  class="q-mb-sm"
                  outlined
                  v-model="officialReceipt"
                  label="OR Number"
                  stack-label
                  dense
                />
                <q-select
                  dense
                  outlined
                  v-model="isReported"
                  :options="typeOpt"
                  stack-label
                  label="Status"
                />
              </div>
              <div class="col-12 col-sm-6 q-pa-sm modalItemBorder">
                <span class="text-title">Summary</span><br>
                <q-separator />
                <span class="text-caption"><strong>Selling Price: </strong> {{ this.price }}</span><br>
                <span class="text-caption"><strong>Discount: </strong> {{ this.discount }}</span><br><br>
                <q-separator />
                <span class="text-caption"><strong>Total Sell Price: </strong> {{ this.totalPrice }}</span><br>
              </div>
            </div>
        </q-card-section>

        <q-card-actions align="right" class="bg-white text-teal">
          <q-btn flat label="Cancel" v-close-popup />
          <q-btn flat label="Submit" @click="sellItem" />
        </q-card-actions>
      </q-card>
    </q-dialog>
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
    watch:{
      tab(newVal){
        if(newVal){
            this.getTabCallDetails(newVal)
        }
      },
      price(){
        this.computeTotalPrice();
      },
      discount(){
        this.computeTotalPrice();
      },
    },
    created(){
        this.getList();
    },
    computed: {
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return jwt_decode(profile);
        },
        typeOpt(){
            return [
                {label: 'Recorded', value: 0},
                {label: 'Reported', value: 1},
            ]
        },
        tableColumns: function(){
            return [
                {
                    name: 'id',
                    required: true,
                    label: '',
                    align: 'left',
                    field: row => row.key,
                    format: val => `${val}`,
                    sortable: true
                },
                {
                    name: 'customerName',
                    required: true,
                    label: 'Customer',
                    align: 'left',
                    field: row => row.customerName,
                    format: val => `${val}`,
                    sortable: true
                },
                {
                    name: 'orNumber',
                    required: true,
                    label: 'Ticket No.',
                    align: 'left',
                    field: row => row.orNumber,
                    format: val => `${val}`,
                    sortable: true
                },
                { name: 'category', align: 'left', label: 'Category', field: 'category'},
                { name: 'item', align: 'left', label: 'Item', field: 'item' },
                { name: 'principal', align: 'left', label: 'Principal', field: 'principal' },
                // { name: 'soldPrice', align: 'left', label: 'Selling Price', field: 'soldPrice' },
                { name: 'actions', label: 'Action', field: 'actions' }
            ]
        }
    },
    methods:{
        moment,
        computeTotalPrice(){
          this.totalPrice = Number(this.price) - Number(this.discount)
        },
        async getList(){
            this.rows = [];
            this.isLoading = true;

            api.post('loan/get/auction/list', {uid: this.user.userId, utype: Number(this.user.userType)}).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.rows = response.status < 300 ? data.list : [];
                } else {
                    this.$q.notify({
                        color: 'negative',
                        position: 'top-right',
                        title:data.title,
                        message: this.$t(`errors.${data.error}`),
                        icon: 'report_problem'
                    })
                }

            })

            this.isLoading = false;
        },
        openModal(itemData){
            this.openItem = itemData,
            this.openSellModal = true;
        },
        sellItem(){
            // Confirm
            this.$q.dialog({
                title: 'Sell Auction Item',
                message: 'Take this action?',
                ok: {
                    label: 'Confirm'
                },
                cancel: {
                    label: 'Cancel',
                    color: 'negative'
                },
                persistent: true
            }).onOk(() => {

                if(Number(this.price) <= Number(this.openItem.principal)){
                  this.$q.notify({
                    color: 'negative',
                    position: 'top-right',
                    message: 'Price inserted is lower than principal',
                    icon: 'report_problem'
                  })
                  return false;
                }

                let payload = {
                    auctionId: Number(this.openItem.key),
                    customerId: Number(this.openItem.customerId),
                    loanId: Number(this.openItem.loanId),
                    auction: {
                        soldPrice:  Number(this.price),
                        discount:  Number(this.discount),
                        totalPrice:  Number(this.totalPrice),
                        officialReceipt:  this.officialReceipt,
                        soldDate: moment().format('YYYY-MM-DD  h:mm A'),
                        soldBy: this.user.userId,
                        status: 1,
                    },
                    transaction: {
                        loanId: Number(this.openItem.loanId),
                        dateMaturity: '--',
                        orNumber: this.openItem.orNumber,
                        officialReceipt: this.officialReceipt,
                        orStatus: Number(this.isReported.value),
                        discount:  Number(this.discount),
                        cashOnHand: this.totalPrice,
                        amount: this.price,
                        status: 'sell',
                        transactionType: 0,
                        createdBy: this.user.userId
                    }
                }


                api.post('loan/sold/item', payload).then((response) => {
                    const data = {...response.data};
                    if(!data.error){
                        this.price = 0;
                        this.openSellModal = false
                        this.getList();
                    } else {
                        this.$q.notify({
                            color: 'negative',
                            position: 'top-right',
                            title:data.title,
                            message: this.$t(`errors.${data.error}`),
                            icon: 'report_problem'
                        })
                    }
                })



            })
        },
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
