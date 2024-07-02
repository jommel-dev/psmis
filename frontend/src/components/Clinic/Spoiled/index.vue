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
                     <q-btn @click="openModal" class="q-ml-sm" color="green" label="Spoiled Ticket" />
                </template>
                <template v-slot:body-cell-itemInfo="props">
                    <q-td :props="props">
                        {{generateItemInfo(props.row.itemInfo)}}
                    </q-td>
                </template>
            </q-table>
        </q-card-section>
    </q-card>

    <q-dialog v-model="openSpoilModal" transition-show="scale" transition-hide="scale">
      <q-card class="" style="width: 100vw">
        <q-card-section>
          <div class="text-h6">Spoil Ticket Information</div>
        </q-card-section>

        <q-card-section class="q-pt-none">
            <div class="row">
              <div class="col-12 col-sm-12 q-pa-sm">
                <q-input
                  class="q-mb-sm"
                  outlined
                  v-model="ticketNumber"
                  label="Ticket Number"
                  stack-label
                  dense
                />
                <q-input
                  class="q-mb-sm"
                  type="textarea"
                  outlined
                  v-model="reasonSpoiled"
                  label="Reason"
                  stack-label
                  dense
                />
              </div>
            </div>
        </q-card-section>

        <q-card-actions align="right" class="bg-white text-teal">
          <q-btn flat label="Cancel" v-close-popup />
          <q-btn flat label="Submit" @click="spoileTicket" />
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
        openSpoilModal: false,
        openItem: null,
        ticketNumber: '',
        reasonSpoiled: '',
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
                    name: 'orNumber',
                    required: true,
                    label: 'Ticket No.',
                    align: 'left',
                    field: row => row.orNumber,
                    format: val => `${val}`,
                    sortable: true
                },
                { name: 'itemInfo', align: 'left', label: 'Item', field: 'itemInfo' },
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

            api.post('loan/get/spoiled/list', {uid: this.user.userId, utype: Number(this.user.userType)}).then((response) => {
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
        openModal(){
            this.openSpoilModal = true;
        },
        spoileTicket(){
            // Confirm
            this.$q.dialog({
                title: 'Spoiled Ticket Number',
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
                let payload = {
                    ticketNumber: this.ticketNumber,
                    reason: `Spoiled ticket due to: ${this.reasonSpoiled}`,
                    createdBy:this.user.userId,
                    updateTransaction: {
                        transactionType: 5,
                        status: 'spoiled'
                    }
                }

                api.post('loan/spoiled', payload).then((response) => {
                    const data = {...response.data};
                    if(!data.error){
                        this.openSpoilModal = false
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
        generateItemInfo(data){
            let list = [];
            data.forEach(el => {
                let res = `${el.qty} ${el.unit.value} ${el.type.value}, ${el.description}, ${el.weight}, ${el.property}`;
                list.push(res);
            });
            return list.join(", ")
        }
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
