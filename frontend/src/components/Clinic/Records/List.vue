<template>
  <div class="q-pa-md">
    <q-toolbar class="text-primary">
        <q-toolbar-title>
            <span class="text-h4">Records</span>
        </q-toolbar-title>
        <q-btn
            @click="openAddClient"
            flat 
            rounded 
            dense 
            icon="create_new_folder" 
            label="Add New Application" 
            class="q-pa-sm" 
        />

        <!-- Filter Section -->
        

    </q-toolbar>
    <q-toolbar>
        <!-- <span>Filter By:</span> -->

        <q-expansion-item
            v-model="expanded"
            icon="tune"
            label="Record Filter"
            style="width: 100%; border-radius: 30px;"
            header-class="bg-primary text-white"
        >
            <q-card>
                <q-card-section>
                    <div class="row">
                        <div class="col col-sm-4 col-md-3">
                            <q-input
                                class="q-ml-md"
                                outlined 
                                v-model="filterOR.customerNo" 
                                label="Customer Number" 
                                stack-label 
                                dense
                            />
                        </div>
                        <div class="col col-sm-4 col-md-3">
                            <q-input
                                class="q-ml-md"
                                outlined 
                                v-model="filterOR.orNumber" 
                                label="OR Number" 
                                stack-label 
                                dense
                            />
                        </div>
                        <div class="col col-sm-4 col-md-3">
                            <q-btn
                                @click="searchFilter"
                                dense
                                rounded
                                color="deep-purple"
                                label="Apply" 
                                class="q-ml-md q-pl-md q-pr-md" 
                            />
                        </div>
                    </div>
                </q-card-section>
                <q-separator />
                <q-card-section>
                    <!-- search filter chips -->
                    <q-chip
                        v-for="(item, index) in filterList"
                        :key="index"
                        color="deep-orange" 
                        text-color="white" 
                        icon="search"
                    >
                        {{`${item.customerNo} : ${item.orNumber}`}}
                    </q-chip>
                </q-card-section>
            </q-card>
        </q-expansion-item>
        
        
    </q-toolbar>

    <q-table
        :rows="rows"
        row-key="name"
        selection="multiple"
        :filter="filter"
        grid
        :rows-per-page-options="[20]"
        hide-header
        :loading="isLoading"
    >
        <template v-slot:top-left>
            <q-breadcrumbs 
                class="text-grey" 
                active-color="primary"
            >
                <template v-slot:separator>
                    <q-icon
                        size="1.2em"
                        name="arrow_forward_ios"
                        color="primary"
                    />
                </template>

                <q-breadcrumbs-el
                    v-for="(itms, index) in breadCrumbs"
                    :key="index"
                    :label="itms.label" 
                    :icon="itms.icon"
                    @click="itms.action"
                />
            </q-breadcrumbs>
        </template>
        <template v-slot:top-right>
            <q-input outlined dense debounce="300" v-model="filter" placeholder="Search">
                <template v-slot:append>
                    <q-icon name="search" />
                </template>
            </q-input>
        </template>

        <template v-slot:item="props">
            <div
                v-if="tableView === 'clientList'"
                class="q-pa-xs col-xs-12 col-sm-6 col-md-3 col-lg-2 itemCursor"
            >
                <q-card 
                    flat
                    class="itemHover"
                    @click="openFolder(props.row)"
                >
                    <q-card-section v-if="tableView === 'clientList'">
                        <q-icon 
                            name="folder" 
                            size="lg" 
                            color="warning" 
                        /> 
                        {{`(${props.row.customerNo}) - ${props.row.firstName} ${props.row.lastName}`}}
                    </q-card-section>
                    <q-popup-proxy context-menu>
                        <q-list dense  style="min-width: 100px">
                            <q-item 
                                clickable
                                @click="openFolder(props.row)" 
                            >
                                <q-item-section side>
                                    <q-icon name="folder_open" size="xs" />
                                </q-item-section>
                                <q-item-section> Open</q-item-section>
                            </q-item>
                            <q-item 
                                clickable 
                                v-close-popup
                            >
                                <q-item-section side>
                                    <q-icon name="help" size="xs" />
                                </q-item-section>
                                <q-item-section>Show Details</q-item-section>
                            </q-item>
                            <q-separator />
                            <q-item 
                                clickable
                                v-close-popup
                                @click="openAddLoan(props.row)"
                            >
                                <q-item-section side>
                                    <q-icon name="credit_score" size="xs" />
                                </q-item-section>
                                <q-item-section>Add Loan</q-item-section>
                            </q-item>
                            <q-separator />
                            <q-item clickable v-close-popup>
                                <q-item-section side>
                                    <q-icon name="menu_book" size="xs" />
                                </q-item-section>
                                <q-item-section>History</q-item-section>
                            </q-item>
                        </q-list>
                    </q-popup-proxy>
                </q-card>
            </div>
            <div
                v-if="tableView === 'patientList'"
                class="q-pa-xs col-xs-12 col-sm-6 col-md-3 col-lg-2 itemCursor"
            >
                <q-card 
                    flat
                    class="itemHover"
                    @click="openFolder(props.row)"
                >
                    <q-card-section v-if="tableView === 'patientList'">
                        <q-icon 
                            name="folder" 
                            size="lg" 
                            :color="statusColor(props.row.status)" 
                        /> 
                        {{`${props.row.orNumber} - ${props.row.itemDetails.length} Item(s)`}}
                    </q-card-section>
                    <q-popup-proxy context-menu>
                        <q-list dense  style="min-width: 100px">
                            <q-item 
                                clickable
                                @click="openFolder(props.row)" 
                            >
                                <q-item-section side>
                                    <q-icon name="folder_open" size="xs" />
                                </q-item-section>
                                <q-item-section> Open</q-item-section>
                            </q-item>
                            <q-item 
                                clickable 
                                @click="openPrintModalWithData(props.row.origData)"
                            >
                                <q-item-section side>
                                    <q-icon name="print" size="xs" />
                                </q-item-section>
                                <q-item-section>Reprint OR</q-item-section>
                            </q-item>
                            <q-separator />
                            <q-item
                                clickable 
                                @click="openItemTerms(props.row)" 
                            >
                                <q-item-section side>
                                    <q-icon name="account_tree" size="xs" />
                                </q-item-section>
                                <q-item-section>Loan Terms</q-item-section>
                            </q-item>
                            <q-separator />
                            <q-item 
                                clickable 
                                @click="openItemHistory(props.row)" 
                            >
                                <q-item-section side>
                                    <q-icon name="history" size="xs" />
                                </q-item-section>
                                <q-item-section>History</q-item-section>
                            </q-item>
                        </q-list>
                    </q-popup-proxy>
                </q-card>
            </div>
            <div
                v-if="tableView === 'patientInfo' && props.row.key === openId"
                class="q-pa-xs col-xs-12 col-sm-12 col-md-12 col-lg-12"
            >
                <loanDetails 
                    :loanData="props.row" 
                    @refreshData="filterRemove"
                    @refreshLoanList="loanListRefresh"
                />
            </div>
        </template>
    </q-table>

    <addClient
        :modalStatus="openAddClientModal"
        @updateModalStatus="closeClientModal"
        @refreshData="getList"
    />
    <addLoan
        :modalStatus="openAddLoanModal"
        :appId="appId"
        @updateModalStatus="closeLoanModal"
        @refreshData="getList"
        @openPrintData="openPrintModalWithData"
    />
    <printModal
        :modalStatus="openPrintModal"
        :appData="formDetails"
        @updatePrintStatus="closePrintModal"
        @refreshData="getList"
    />
    <historyModal
        :modalStatus="openLoanHistoryModal"
        :appId="loanId"
        @updateModalStatus="closeLoanHistoryModal"
    />
    <termsModal
        :modalStatus="openLoanTermsModal"
        :loanData="loanData"
        @updateModalStatus="closeLoanTermsModal"
    />
  </div>
</template>

<script>
import { LocalStorage } from 'quasar'
import moment from 'moment';
import addPatient from '../Modals/AddPatient.vue'
import addLoan from '../Modals/AddLoan.vue'
import printModal from '../Modals/PrintModal.vue'
import addClient from '../Modals/AddClient.vue'
import historyModal from '../Modals/HistoryModal.vue'
import termsModal from '../Modals/TermsModal.vue'
import loanDetails from './LoanDetails.vue'
import jwt_decode from 'jwt-decode'
import { api } from 'boot/axios'

export default {
    name: 'RecordList',
    components: {
        addPatient,
        addClient,
        addLoan,
        printModal,
        historyModal,
        termsModal,
        loanDetails
    },
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
            breadCrumbs: [
                {
                    label: 'Records',
                    icon: 'dvr',
                    classNames: '',
                    action: () => {}
                },
                {
                    label: 'Customer List',
                    icon: 'view_list',
                    classNames: 'itemCursor', 
                    action: () => { return this.breadCrumbsClick('clientList', []) }
                },
            ]
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
        }
    },
    methods:{
        moment,
        openAddLoan(row){
            this.appId = row
            this.openAddLoanModal = true
        },
        openAddClient(){
            this.openAddClientModal = true
        },
        openItemHistory(row){
            this.loanId = row.key
            this.openLoanHistoryModal = true
        },
        openItemTerms(row){
            this.loanData = row
            this.openLoanTermsModal = true
        },
        closeClientModal(){
            this.openAddClientModal = false;
        },
        closeLoanModal(){
            this.openAddLoanModal = false;
        },
        closePrintModal(){
            this.openPrintModal = false;
        },
        closeLoanHistoryModal(){
            this.openLoanHistoryModal = false;
        },
        closeLoanTermsModal(){
            this.openLoanTermsModal = false;
        },
        openPrintModalWithData(data){
            this.formDetails = data;
            this.openPrintModal = true;
        },
        openFolder(row){
            if(this.tableView === 'clientList'){
                this.appId = row
                this.tableView = 'patientList'
                this.getLoanList(row.key);
                this.breadCrumbs.push({
                    label: `${row.firstName} ${row.lastName}`,
                    icon:'folder_open',
                    classNames: 'itemCursor',
                    action: () => { return this.breadCrumbsClick('patientList', row) }
                })
            } else if(this.tableView === 'patientList'){
                this.tableView = 'patientInfo'
                this.openId = row.key;
                this.patientInfo = row
                this.breadCrumbs.push({
                    label: `${row.orNumber}`,
                    icon:'diamond',
                    classNames: '',
                    action: () => {}
                })
            } else {
                this.tableView = 'clientList'
                this.getList();
                this.breadCrumbs = [
                    {
                        label: 'Records',
                        icon: 'dvr',
                        classNames: '',
                        action: () => {}
                    },
                    {
                        label: 'Client List',
                        icon: 'view_list',
                        classNames: 'itemCursor',
                        action: () => { return this.breadCrumbsClick('clientList', []) }
                    },
                ]
            }
        },
        
        breadCrumbsClick(type, row = []){
            this.breadCrumbs = [
                {
                    label: 'Records',
                    icon: 'dvr',
                    classNames: '',
                    action: () => {}
                },
                {
                    label: 'Client List',
                    icon: 'view_list',
                    classNames: 'itemCursor',
                    action: () => { return this.breadCrumbsClick('clientList', []) }
                },
            ]
            if(type === 'patientList'){
                this.tableView = 'clientList'
                this.openFolder(row)
            } else {
                this.tableView = 'backToList'
                this.openFolder(row)
            }
        },
        filterRemove(){
            this.filterOR = {
                customerNo: "",
                orNumber: ""
            }
            this.tableView = 'clientList'
            this.getList();
            this.breadCrumbs = [
                {
                    label: 'Records',
                    icon: 'dvr',
                    classNames: '',
                    action: () => {}
                },
                {
                    label: 'Client List',
                    icon: 'view_list',
                    classNames: 'itemCursor',
                    action: () => { return this.breadCrumbsClick('clientList', []) }
                },
            ]
        },
        async searchFilter(){
            if(this.filterOR.customerNo === "" || this.filterOR.orNumber === ""){
                this.$q.notify({
                    color: 'negative',
                    position: 'top-right',
                    message: "Filter fields must be complete",
                    icon: 'report_problem'
                })
                return false
            }
            this.rows = [];
            this.isLoading = true;
            let payload = {
                ...this.filterOR
            }
            this.filterList.push({...this.filterOR})

            api.post('application/filter/list', payload).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.filterOR = {
                        customerNo: "",
                        orNumber: ""
                    }
                    this.appId = data.list[0].customerInfo
                    this.tableView = 'patientList'
                    this.breadCrumbs.push({
                        label: `${this.appId.firstName} ${this.appId.lastName}`,
                        icon:'folder_open',
                        classNames: 'itemCursor',
                        action: () => { return this.breadCrumbsClick('patientList', this.appId) }
                    })
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
        async getList(){
            this.rows = [];
            this.isLoading = true;
            
            api.post('application/clientList', {uid: this.user.userId}).then((response) => {
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
        loanListRefresh(id){
            this.breadCrumbs = [
                {
                    label: 'Records',
                    icon: 'dvr',
                    classNames: '',
                    action: () => {}
                },
                {
                    label: 'Client List',
                    icon: 'view_list',
                    classNames: 'itemCursor',
                    action: () => { return this.breadCrumbsClick('clientList', []) }
                },
            ]
            this.tableView = 'patientList'
            this.getLoanList(id);
            this.breadCrumbs.push({
                label: `${this.appId.firstName} ${this.appId.lastName}`,
                icon:'folder_open',
                classNames: 'itemCursor',
                action: () => { return this.breadCrumbsClick('patientList', this.appId) }
            })
        },
        async getLoanList(id){
            this.rows = [];
            this.isLoading = true;
            let payload = {
                uid: id
            }
            api.post('loan/get/list', payload).then((response) => {
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
        getTabCallDetails(tab){
            let payload = {
                pid: this.openId
            }

            api.post(`patient/get/${tab}`, payload).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    console.log(data)
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
        statusColor(status){
           if(Number(status) === 3){
                return 'blue'
            } else if(Number(status) === 4){
                return 'green'
            } else if(Number(status) === 0){
                return 'red'
            } else {
                return 'warning'
            }   
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
</style>