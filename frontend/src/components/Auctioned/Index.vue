<template>
  <div class="q-pa-xs">
    <!-- <q-toolbar class="text-primary">
        <q-toolbar-title>
            <span class="text-h4">Auction List</span>
        </q-toolbar-title>
    </q-toolbar> -->
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
                        <q-btn-group v-if="Number(props.row.status) !== 1" rounded>
                            <q-btn @click="sellItem(props.row)" rounded size="sm" color="green" label="Sell Item" />
                            <!-- <q-btn rounded size="sm" color="primary" label="Update Price" /> -->
                        </q-btn-group>
                        <div v-if="Number(props.row.status) === 1" >
                            <q-chip icon="sell" color="positive" text-color="white">Sold</q-chip>
                        </div>
                    </q-td>
                </template>
            </q-table>
        </q-card-section>
    </q-card>
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
            openPrintModal: false,
            formDetails: {},
            // Other
            expanded: false,
            // tabs controller
            tab: 'owner',
            // Context Controller
            appId: {},
            openAddLoanModal: false,

            loanId: 0,
            openLoanHistoryModal: false,
            loanData: {},
            openLoanTermsModal: false,
            // Add Client Controller
            openAddClientModal: false,

            // Others
            filterOR: {
                customerNo: '',
                orNumber: '',
            },
            filterList: [],
            isLoading: false,
            filter: '',
            rows: [],
            tableView: 'clientList',
            openId: 0,
            patientInfo: {},
        }
    },
    watch:{
        tab(newVal){
            if(newVal){
                this.getTabCallDetails(newVal)
            }
        }
    },
    created(){
        this.getList();
    },
    computed: {
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return jwt_decode(profile);
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
                { name: 'soldPrice', align: 'left', label: 'Selling Price', field: 'soldPrice' },
                { name: 'soldDate', align: 'left', label: 'Sold Date', field: 'soldDate' },
                { name: 'actions', label: 'Action', field: 'actions' }
            ]
        }
    },
    methods:{
        moment,
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
        sellItem(itemData){
            // Confirm
            this.$q.dialog({
                title: 'Sell Auction Item',
                message: 'Please enter Price',
                prompt: {
                    model: '',
                    type: 'number' // optional
                },
                ok: {
                    label: 'Confirm'
                },
                cancel: {
                    label: 'Cancel',
                    color: 'negative'
                },
                persistent: true
            }).onOk((price) => {

                if(Number(price) <= Number(itemData.principal)){
                    this.$q.notify({
                        color: 'negative',
                        position: 'top-right',
                        message: 'Price inserted is lower than principal',
                        icon: 'report_problem'
                    })
                    return false;
                }

                let payload = {
                    auctionId: Number(itemData.key),
                    customerId: Number(itemData.customerId),
                    loanId: Number(itemData.loanId),
                    auction: {
                        soldPrice: price,
                        soldDate: moment().format('YYYY-MM-DD  h:mm A'),
                        soldBy: this.user.userId,
                        status: 1,
                    },
                    transaction: {
                        loanId: Number(itemData.loanId),
                        dateMaturity: '--',
                        orNumber: itemData.orNumber,
                        orStatus: Number(itemData.orStatus),
                        amount: price,
                        transactionType: 0,
                        createdBy: this.user.userId
                    }
                }

                
                api.post('loan/sold/item', payload).then((response) => {
                    const data = {...response.data};
                    if(!data.error){
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
</style>