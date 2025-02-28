<template>
  <div class="q-pa-md">

    <div class="row">
        <div class="col col-md-12">
            <span class="text-h5">
                Customer Details
                <q-chip
                    v-bind="statusItem(loanDetails.status)"
                    outline
                    text-color="white"
                >
                    {{statusLabel(loanDetails.status)}}
                </q-chip>
            </span>
        </div>
        <div class="q-mb-md col col-md-3 q-pa-sm">
            <q-card class="my-card" flat bordered>
                <q-card-section>
                    <q-img 
                        v-if="userProfile.profile !== ''"
                        class="imageStyleView q-mb-sm" 
                        :src="userProfile.profile" 
                    />
                    <!-- <q-img 
                        v-if="loanDetails.customerInfo.identifications[0].image !== ''" 
                        class="imageStyleView q-mb-sm" 
                        :src="loanDetails.customerInfo.identifications[0].image" 
                    /> -->
                    <q-img 
                        v-if="userProfile.eSignature !== ''" 
                        class="imageStyleView q-mb-sm" 
                        :src="userProfile.eSignature" 
                    />
                </q-card-section>
            </q-card>
        </div>
        <div class=" q-mb-md col col-md-9 q-pa-sm">
            <q-card class="my-card" flat bordered>
                <q-item>
                    <q-item-section avatar>
                        <q-avatar>
                            <q-icon name="account_box" size="lg" />
                        </q-avatar>
                        
                    </q-item-section>

                    <q-item-section>
                        <q-item-label>{{`${loanDetails.customerInfo.firstName} ${loanDetails.customerInfo.lastName}`}}</q-item-label>
                        <q-item-label caption>
                            {{ `${loanDetails.customerInfo.addressLine} ${loanDetails.customerInfo.addressDetails?.barangay.label} ${loanDetails.customerInfo.addressDetails?.city.label} ${loanDetails.customerInfo.addressDetails?.province.label}` }}
                        </q-item-label>
                        
                    </q-item-section>
                </q-item>

                <q-separator />

                <q-card-section horizontal>
                    <q-card-section>
                        <span class="text-bold">Contact Number: </span>
                    </q-card-section>
                    <q-separator vertical />
                    <q-card-section class="col-4">
                        <span>{{loanDetails.customerInfo.contactNo || "N/A"}}</span>
                    </q-card-section>
                    <q-separator vertical />
                    <q-card-section>
                        <span class="text-bold">Presented ID: </span>
                    </q-card-section>
                    <q-separator vertical />
                    <q-card-section class="col-4">
                        <!-- <span>{{`${loanDetails.customerInfo.identifications[0].type} (${loanDetails.customerInfo.identifications[0].idNumber})` || "N/A"}}</span> -->
                    </q-card-section>
                </q-card-section>

                <q-separator />

                <q-card-section >
                    <span class="text-h5">Loan Items</span>
                    <q-table
                        flat
                        bordered
                        dense
                        :rows="loanDetails.itemDetails"
                        :columns="columns"
                        row-key="name"
                    />
                </q-card-section>
                <q-card-section >
                    <q-tabs
                        inline-label
                        v-model="tab"
                        indicator-color="transparent"
                        active-color="blue"
                        class="text-grey-5"
                    >
                        <q-tab name="renewal" icon="restart_alt" label="Renewal" />
                        <q-tab name="transaction" icon="receipt_long" label="Transactions" />
                    </q-tabs>

                    <q-tab-panels v-model="tab" animated>

                        <q-tab-panel name="renewal" >
                            <div class="row" v-if="loanDetails.status !== '4'">
                                <div class="col-12 col-md-12 q-pl-md">
                                    <div class="text-subtitle1 text-bold">
                                    Loan Amount: {{convertCurrency(Number(loanDetails.loanAmount))}}
                                    </div>
                                    <div class="text-subtitle1 text-bold">
                                    Loan Interest per Month: {{convertCurrency(Number(loanData.computationDetails.amountPercentage))}}
                                    </div>
                                </div>
                                <div class="col col-md-3 q-pl-md q-mt-sm">
                                    <div class="text-subtitle1">
                                    Amount Interest: {{convertCurrency(computeRenewAmount())}}
                                    </div>
                                    <div v-show="isExpired" class="text-subtitle1">
                                    Amount Penalty (2%): {{convertCurrency(computeRenewPenaltyAmount())}}
                                    </div>
                                    <div class="text-caption text-grey">
                                    Maturity Date: {{loanDetails.maturityDate}}
                                    </div>
                                    <q-btn
                                        v-if="(loanDetails.status !== '4' || loanDetails.status !== '5') && Number(user.userId) === Number(loanDetails.createdBy)"
                                        @click="openRenewLoan"
                                        class="full-width"
                                        color="primary"
                                    >
                                        Renew Loan
                                    </q-btn>
                                </div>
                                <div class="col col-md-3 q-pl-md q-mt-sm">
                                    <div class="text-subtitle1">
                                    Amount Loan in Full: {{convertCurrency(computeFullAmount())}}
                                    </div>
                                    <div v-show="isExpired" class="text-subtitle1">
                                    Amount Penalty (2%): {{convertCurrency(computeRenewPenaltyAmount())}}
                                    </div>
                                    <div class="text-caption text-grey">
                                    Expiration Date: {{loanDetails.expirationDate}}
                                    </div>
                                    <q-btn
                                        v-if="(loanDetails.status !== '4' || loanDetails.status !== '5' || loanDetails.status !== '0') && Number(user.userId) === Number(loanDetails.createdBy)"
                                        @click="openFullPayLoan" 
                                        class="full-width" 
                                        color="green"
                                    >
                                        Pay in Full (Redeem)
                                    </q-btn>
                                </div>
                                <!-- <div class="col col-md-6 q-pl-md q-mt-sm">
                                    <div class="text-subtitle1">
                                    Invalid Transaction
                                    </div>
                                    <div class="text-caption text-grey">
                                    <i>Note:</i> Invalid, Wrong details, and accidentally printed.
                                    </div>
                                    <q-btn
                                        v-if="Number(user.userId) === Number(loanDetails.createdBy)"
                                        @click="openSpoiledTicket" 
                                        class="full-width" 
                                        color="red"
                                    >
                                        Spoiled Ticket
                                    </q-btn>
                                </div> -->
                                
                            </div>
                            <div class="row">
                                <div class="col col-md-12 q-pl-md q-mt-md">
                                    <q-table
                                        flat
                                        bordered
                                        dense
                                        :rows="renewTransaction"
                                        :columns="tableColumns"
                                        row-key="key"
                                        separator="cell"
                                    />
                                </div>
                            </div>
                        </q-tab-panel>

                        <q-tab-panel name="transaction">
                            <q-table
                                flat
                                bordered
                                dense
                                :rows="allTransaction"
                                :columns="tableColumns"
                                row-key="name"
                                separator="cell"
                            />
                        </q-tab-panel>
                    </q-tab-panels>
                </q-card-section>

            </q-card>
        </div>
    </div>
    <printModal
      :modalStatus="openPrintModal"
      :appData="formDetails"
      @updatePrintStatus="closePrintModal"
      @refreshData="getList"
    />

    <q-dialog v-model="openModal" transition-show="scale" transition-hide="scale">
      <q-card class="my-card" style="width: 700px; max-width: 80vw;">
        <q-card-section>
          <div class="text-h6">Renew Loan</div>
        </q-card-section>

        <q-card-section class="q-pt-none">
            <div class="row">
              <div class="col-12 col-sm-12 q-pa-sm">
                <span class="text-h5">Loan Renew Amount: {{computeRenewAmount()}}</span><br/>
                <span class="text-h5">Past Due Months: {{pastDueCount}}</span><br/>
                <span class="text-h5">Past Due Amount: {{getPastDue()}}</span><br/>
                <span class="text-h5">Penalty: {{computeRenewPenaltyAmount()}}</span>
              </div>
              <div class="col-12 col-sm-6 q-pa-sm">
                <q-input
                    type="date"
                    class="q-mb-sm"
                    outlined
                    v-model="redeemDate"
                    label="Date Renew"
                    stack-label
                    dense
                    hint="Note: Only modify this if needed."
                />
                <q-input
                  class="q-mb-sm"
                  outlined
                  v-model="officialReceipt"
                  label="OR Number"
                  stack-label
                  dense
                />
                <q-input
                  class="q-mb-sm"
                  type="number"
                  outlined
                  v-model="price"
                  label="Cash Payable"
                  stack-label
                  dense
                />
                <q-input
                  class="q-mb-sm"
                  type="number"
                  outlined
                  v-model="discount"
                  label="Discount"
                  stack-label
                  dense
                />
              </div>
              <div class="col-12 col-sm-6 q-pa-sm modalItemBorder">
                <span class="text-title">Summary</span><br>
                <q-separator />
                <span class="text-title"><strong>Renew Amount: </strong> {{convertCurrency(computeRenewAmount() + computeRenewPenaltyAmount() + getPastDue())}}</span><br>
                <span class="text-title"><strong>Cash On Hand: </strong> {{ this.price }}</span><br>
                <span class="text-title"><strong>Discount: </strong> {{ this.discount }}</span><br><br>
                <q-separator />
                <span class="text-title"><strong>Total Cash Receive: </strong> {{ this.totalPrice }}</span><br>
              </div>
            </div>
        </q-card-section>

        <q-card-actions align="right" class="bg-white text-teal">
          <q-btn flat label="Cancel" v-close-popup />
          <q-btn flat label="Submit" @click="renewLoan" />
        </q-card-actions>
      </q-card>
    </q-dialog>
    <!-- Fullpayment -->
    <q-dialog v-model="openModalFull" transition-show="scale" transition-hide="scale">
      <q-card class="my-card" style="width: 700px; max-width: 80vw;">
        <q-card-section>
          <div class="text-h6">Redeem Loan Item</div>
        </q-card-section>

        <q-card-section class="q-pt-none">
            <div class="row">
              <div class="col-12 col-sm-12 q-pa-sm">
                <span class="text-h5">Loan Amount To Be Paid: {{convertCurrency(computeFullAmount())}}</span><br/>
                <span class="text-h5">Expired Due Months: {{pastDueCount}}</span><br/>
                <span class="text-h5">Past Due Amount: {{convertCurrency(getPastDue())}}</span><br/>
                <span class="text-h5">Penalty: {{convertCurrency(computeRenewPenaltyAmount())}}</span>
              </div>
              <div class="col-12 col-sm-6 q-pa-sm">
                <q-input
                    type="date"
                    class="q-mb-sm"
                    outlined
                    v-model="redeemDate"
                    label="Date Redeem"
                    stack-label
                    dense
                    hint="Note: Only modify this if needed."
                />
                <q-input
                  class="q-mb-sm"
                  outlined
                  v-model="officialReceipt"
                  label="OR Number"
                  stack-label
                  dense
                />
                <q-input
                  class="q-mb-sm"
                  type="number"
                  outlined
                  v-model="price"
                  label="Cash Payable"
                  stack-label
                  dense
                />
                <q-input
                  class="q-mb-sm"
                  type="number"
                  outlined
                  v-model="discount"
                  label="Discount"
                  stack-label
                  dense
                />
              </div>
              <div class="col-12 col-sm-6 q-pa-sm modalItemBorder">
                <span class="text-title">Summary</span><br>
                <q-separator />
                <span class="text-title"><strong>Amount in Full: </strong> {{convertCurrency(computeFullAmount() + computeRenewPenaltyAmount() + getPastDue())}}</span><br>
                <span class="text-title"><strong>Cash On Hand: </strong> {{ this.price }}</span><br>
                <span class="text-title"><strong>Discount: </strong> {{ this.discount }}</span><br><br>
                <q-separator />
                <span class="text-title"><strong>Total Cash Receive: </strong> {{ this.totalPrice }}</span><br>
              </div>
            </div>
        </q-card-section>

        <q-card-actions align="right" class="bg-white text-teal">
          <q-btn flat label="Cancel" v-close-popup />
          <q-btn flat label="Submit" @click="fullPaidLoan" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script>
import { LocalStorage } from 'quasar'
import moment from 'moment';
import historyModal from '../Modals/HistoryModal.vue'
import termsModal from '../Modals/TermsModal.vue'
import jwt_decode from 'jwt-decode'
import { api } from 'boot/axios'
import printModal from '../Modals/PrintModal.vue'

const dateNow = moment().format('YYYY-MM-DD');

export default {
    name: 'LoanDetails',
    components: {
        historyModal,
        termsModal,
        printModal
    },
    props: {
        loanData: {
            type: Object
        }
    },
    data(){
        return {
            // Print
            openPrintModal: false,
            formDetails: {},
            // tabs controller
            tab: 'renewal',
            // Data
            tableRow: [],
            renewTransaction: [],
            allTransaction: [],
            loanDetails: {},
            pastDueCount: 0,
            seriesDetatils:{},
            // Renew and Pay in Full
            openModal: false,
            openItem: null,
            price: 0,
            discount: 0,
            totalPrice: 0,
            officialReceipt: '',
            redeemDate: moment().format('YYYY-MM-DD'),
            // Full Payment
            openModalFull: false,
            userProfile: {
                profile: "",
                eSignature: "",
            },
            renewForm: {
                datesOfMaturity: [],
                maturityDate: "",
                expirationDate: "",
                gracePeriodDate: ""
            }
        }
    },
    watch:{
      price(){
        this.computeTotalPrice();
      },
      discount(){
        this.computeTotalPrice();
      },
    },
    created(){
        // this.getReference();
        this.getRenewHistoryList();
        this.getAllHistoryList();
        this.getProfile();
        this.setDetails
    },
    computed: {
        user: function(){
          let profile = LocalStorage.getItem('userData');
          return jwt_decode(profile);
        },
        setDetails(){
          this.loanDetails = this.loanData
        },
        isExpired(){
            return this.loanData.terms <= this.loanData.payStatus && dateNow > this.loanData.expirationDate 
        },
        columns(){
            return [
                {
                    name: 'type',
                    required: true,
                    label: 'Item Type',
                    align: 'left',
                    field: row => row.type,
                    format: val => `${val.value}`,
                    sortable: true
                },
                { name: 'description', align: 'left', label: 'Description', field: 'description', sortable: true },
                { name: 'property', align: 'left', label: 'Property', field: 'property' },
                { name: 'qty', align: 'left', label: 'qty', field: 'qty', sortable: true },
                {
                    name: 'unit',
                    label: 'Unit',
                    align: 'left',
                    field: row => row.unit,
                    format: val => `${val.value}`,
                    sortable: true
                },
                { name: 'weight', align: 'left', label: 'Weight', field: 'weight' },
                { name: 'remarks', align: 'left', label: 'Remarks', field: 'remarks' },

            ]
        },
        tableColumns(){
            return [
                {
                    name: 'dateTransaction',
                    required: true,
                    label: 'Transaction Date',
                    align: 'left',
                    field: row => row.dateTransaction,
                    format: val => `${val}`,
                    sortable: true
                },
                {
                    name: 'description',
                    required: true,
                    label: 'Description',
                    align: 'left',
                    field: row => row.description,
                    format: val => `${val}`,
                    sortable: true
                },
                {
                    name: 'orNumber',
                    required: true,
                    label: 'New Ticket No.',
                    align: 'left',
                    field: row => row.snapShot.orNumber,
                    format: val => `${val}`,
                    sortable: true
                },
                {
                    name: 'orNumber',
                    required: true,
                    label: 'Old Ticket No.',
                    align: 'left',
                    field: row => row.snapShot.oldTicketNo || '-',
                    format: val => `${val}`,
                    sortable: true
                },
                {
                    name: 'amount',
                    required: true,
                    label: 'Amount',
                    align: 'left',
                    field: row => row.snapShot.amount,
                    format: val => `${val || '--'}`,
                    sortable: true
                },
                {
                    name: 'dateMaturity',
                    required: true,
                    label: 'Maturity',
                    align: 'left',
                    field: row => row.snapShot.dateMaturity,
                    format: val => `${val || '--'}`,
                    sortable: true
                },
            ]
        },
        generateMaturityDates(){
            const dateNow = moment().format('YYYY-MM-DD');
            const dateName = 'd'
            let currentDate = dateNow
            
            let list = []
            for (let index = 0; index < this.loanData.terms; index++) {
                let dayInmonth = moment(currentDate, "YYYY-MM").daysInMonth()
                let dateDays = dayInmonth === 31 ? Number(dayInmonth) - 1 : 30

                let mdate = moment(currentDate).add(dateDays, dateName).format('YYYY-MM-DD')
                let obj = {
                    dateFormatted: mdate,
                    dateString: moment(mdate).format('LL'),
                }
                list.push(obj)
                currentDate = mdate
            }
            this.renewForm.datesOfMaturity = list

            // Set Grace period
            this.renewForm.maturityDate = list.at(0).dateFormatted
            this.renewForm.expirationDate = list.at(-1).dateFormatted
            this.renewForm.gracePeriodDate = moment(list.at(-1).dateFormatted).add(15, dateName).format('YYYY-MM-DD')
        }
    },
    methods:{
        getPastDue(){
            let amount = 0;
            // Get the past due if redeemed late 
            // get the diff. from date created to current date
            let hasRenewals = this.renewTransaction.length > 0;
            let dateOne = hasRenewals ? 
            moment(this.renewTransaction[0].createdDate).format('YYYY-MM-DD') :
            moment(this.loanData.origData.createdDate).format('YYYY-MM-DD')
            let duration = moment(this.redeemDate).diff(dateOne, 'months');

            let pastdue = duration > this.loanData.terms ? 
            duration - Number(this.loanData.terms) : 
            0;

            
            if(duration > this.loanData.terms && pastdue !== 0){
                this.pastDueCount = pastdue + 1
                amount = Number(this.loanData.computationDetails.amountPercentage) * this.pastDueCount 
            }

            return amount;

        },

        computeRenewAmount(){
            let amount = 0;
            if(this.loanData.terms <= this.loanData.payStatus && dateNow > this.loanData.expirationDate ){
                amount = Number(this.loanData.computationDetails.amountPercentage) * (Number(this.loanData.payStatus))
            } else {
                amount = Number(this.loanData.computationDetails.amountPercentage) * Number(this.loanData.payStatus)
            }
            
            return amount;
        },
        computeFullAmount(){
            let amount = 0;
            
            if(this.loanData.terms <= this.loanData.payStatus && dateNow > this.loanData.expirationDate){
                amount = Number(this.loanDetails.loanAmount) + Number(this.loanData.computationDetails.amountPercentage) * (Number(this.loanData.payStatus))
            } else {
                amount = Number(this.loanDetails.loanAmount) + (Number(this.loanData.computationDetails.amountPercentage) * Number(this.loanData.payStatus))
            }
            
            return amount;
        },
        computeRenewPenaltyAmount(){
            let amount = 0;
            if(this.loanData.terms <= this.loanData.payStatus && dateNow > this.loanData.expirationDate){
                amount = (Number(this.loanData.loanAmount) * 0.02) * this.pastDueCount
            }
            return amount;
        },
        computeTotalPrice(){
          this.totalPrice = Number(this.price) - Number(this.discount)
        },
        closePrintModal(){
            this.$emit('refreshLoanList', this.loanData.customerInfo.id)
            this.openPrintModal = false;
        },
        getRenewHistoryList(){
            let payload = {
                loanId: Number(this.loanData.key),

            }
            api.post('loan/get/renewRecord', payload).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.renewTransaction = data.list
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
        },
        getAllHistoryList(){
            let payload = {
                loanId: Number(this.loanData.key),

            }
            api.post('loan/get/allRecord', payload).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.allTransaction = data.list
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
        },
        convertCurrency(value){
            let amount = Number(value);
            return amount.toLocaleString('en-US', {
                style: 'currency',
                currency: 'PHP',
            })
        },
        statusItem(status){
            if(Number(status) === 1){
                return {
                    color: 'green',
                    icon: 'verified_user',
                }
            } else if(Number(status) === 2){
                return {
                    color: 'orange',
                    icon: 'sell',
                }
            } else if(Number(status) === 3){
                return {
                    color: 'blue',
                    icon: 'inventory',
                }
            } else if(Number(status) === 4){
                return {
                    color: 'green',
                    icon: 'verified',
                }
            } else {
                return {
                    color: 'red',
                    icon: 'gpp_bad',
                }
            }
        },
        statusLabel(status){
            if(Number(status) === 1){
                return 'Active'
            } else if(Number(status) === 2){
                return 'On Auction'
            } else if(Number(status) === 3){
                return 'Sold'
            } else if(Number(status) === 4){
                return 'Redeemed'
            } else {
                return 'Deffered'
            }
        },


        // Actions for Transactions
        getProfile(){
            let payload = {
                uid: this.loanData.origData.customerId
            }
            api.post('loan/get/profile', payload).then((response) => {
                const data = {...response.data};
                this.userProfile = data
            })
        },
        getReference(){
            // loan/get/reference
            let payload = {
                uid: this.user.userId
            }
            api.post('loan/get/reference', payload).then((response) => {
                const data = {...response.data};
                this.seriesDetatils = data
            })
        },
        openRenewLoan(){
          this.openModal = true
        },
        renewLoan(){
            this.getReference();
            this.generateMaturityDates
            // Confirm
            this.$q.dialog({
                title: 'Renew Loan',
                message: 'Would you like to take this action?',
                ok: {
                    label: 'Yes'
                },
                cancel: {
                    label: 'No',
                    color: 'negative'
                },
                persistent: true
            }).onOk(() => {
                let cloneData = Object.assign({}, this.loanData.origData)

                let payload = {
                    loanId: this.loanData.key,
                    createdBy: this.user.userId,
                    updateLoan: {
                        oldTicket: cloneData.orNumber,
                        maturityDate: this.renewForm.maturityDate,
                        expirationDate: this.renewForm.expirationDate,
                        gracePeriodDate: this.renewForm.gracePeriodDate,
                        datesOfMaturity: this.renewForm.datesOfMaturity,
                        loanStatus: "Renew",
                        payStatus: 1,
                        orNumber: this.seriesDetatils.start,
                    },
                    transaction: {
                        loanId: Number(this.loanData.key),
                        oldTicketNo: cloneData.orNumber,
                        dateMaturity: this.loanData.maturityDate,
                        orNumber: this.seriesDetatils.start,
                        officialReceipt:  this.officialReceipt,
                        discount:  this.discount,
                        orStatus: Number(this.loanData.orStatus),
                        pastDue: this.pastDueCount,
                        amount: this.totalPrice,
                        cashOnHand: this.price,
                        status: 'renew',
                        transactionType: 0,
                        createdDate: moment(this.redeemDate).format('YYYY-MM-DD h:mm:ss'),
                        createdBy: this.user.userId
                    }
                }

                if(Number(this.loanData.status) === 0){
                    payload.updateLoan.status = 1
                }

                api.post('loan/renew', payload).then((response) => {
                    const data = {...response.data};
                    if(!data.error){
                        cloneData.oldTicket = payload.updateLoan.oldTicket;
                        cloneData.orNumber = this.seriesDetatils.start;
                        cloneData.maturityDate = this.renewForm.maturityDate;
                        cloneData.expirationDate = this.renewForm.expirationDate;
                        cloneData.gracePeriodDate = this.renewForm.gracePeriodDate;
                        cloneData.datesOfMaturity = this.renewForm.datesOfMaturity
                        cloneData.loanStatus = "RENEW";
                        this.formDetails = cloneData;
                        this.openPrintModal = true;

                        this.tab = 'details'
                        this.getRenewHistoryList();
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
        openFullPayLoan(){
          this.openModalFull = true
        },
        fullPaidLoan(){
            // Confirm
            this.$q.dialog({
                title: 'Fully Paid Loan',
                message: 'Would you like to take this action?',
                ok: {
                    label: 'Yes'
                },
                cancel: {
                    label: 'No',
                    color: 'negative'
                },
                persistent: true
            }).onOk(() => {
                let payload = {
                    loanId: this.loanData.key,
                    createdBy: this.user.userId,
                    updateLoan: {
                        oldTicket: this.loanData.orNumber,
                        loanStatus: "Redeemed",
                        redeemDate: moment(this.redeemDate).format('YYYY-MM-DD'),
                        orNumber: this.loanData.orNumber,
                        status: 4
                    },
                    transaction: {
                        oldTicketNo: this.loanData.orNumber,
                        loanId: Number(this.loanData.key),
                        dateMaturity: this.loanData.maturityDate,
                        orNumber: this.loanData.orNumber,
                        officialReceipt:  this.officialReceipt,
                        orStatus: Number(this.loanData.orStatus),
                        amount: this.totalPrice,
                        discount:  Number(this.discount),
                        cashOnHand: this.price,
                        status: 'redeem',
                        createdDate: moment(this.redeemDate).format('YYYY-MM-DD h:mm:ss'),
                        transactionType: 0,
                        createdBy: this.user.userId
                    }
                }
                // console.log(payload)
                // return
                api.post('loan/paid', payload).then((response) => {
                    const data = {...response.data};
                    if(!data.error){
                        this.$emit('refreshData')
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
        openSpoiledTicket(){
            // Confirm
            this.$q.dialog({
                title: 'Spoiled Ticket',
                message: 'Would you like to take this action?',
                ok: {
                    label: 'Yes'
                },
                cancel: {
                    label: 'No',
                    color: 'negative'
                },
                persistent: true
            }).onOk(() => {
                
                let payload = {
                    loanId: this.loanData.key,
                    createdBy: this.user.userId,
                    orNumber: this.loanData.orNumber,
                    updateLoan: {
                        loanStatus: "Spoiled",
                    },
                    updateTransaction: {
                        status: 'spoiled',
                        transactionType: 5,
                        createdBy: this.user.userId
                    }
                }
                console.log(payload)
                return
                // api.post('loan/paid', payload).then((response) => {
                //     const data = {...response.data};
                //     if(!data.error){
                //         this.$emit('refreshData')
                //     } else {
                //         this.$q.notify({
                //             color: 'negative',
                //             position: 'top-right',
                //             title:data.title,
                //             message: this.$t(`errors.${data.error}`),
                //             icon: 'report_problem'
                //         })
                //     }
                // })
            })
        },
    }
}
</script>

<style scoped>
.my-card{
  border-radius: 15px;
  box-shadow: 0px 0px 3px -2px !important;
}
.modalItemBorder{
  border-radius: 10px;
  border: 1px solid grey;
}
.imageStyleView{
    border-radius: 20px;
}
</style>
