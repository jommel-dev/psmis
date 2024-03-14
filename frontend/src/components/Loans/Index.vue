<template>
    <div>
        <div class="q-pa-md" style="width: 100%;">
            <div class="row">
                <div v-if="isLoading" class="col text-center">
                    <q-spinner-orbit
                        color="primary"
                        size="3em"
                    />
                </div>
                
                <div class="col col-md-12">
                    <!-- <q-btn-group class="q-mb-md" flat>
                        <q-btn rounded size="md" color="positive" @click="openModal" label="Add new user" />
                    </q-btn-group> -->
                    <div v-if="tableRow.length == 0" class="col text-center">
                        <noData />
                    </div>
                    <div v-if="tableRow.length > 0">
                        <q-table
                            title="Loan List"
                            :rows="tableRow"
                            :columns="tableColumns"
                            :filter="filter"
                            row-key="orNumber"
                        >
                            <template v-slot:top-right>
                                <q-input outlined dense debounce="300" v-model="filter" placeholder="Search">
                                    <template v-slot:append>
                                        <q-icon name="search" />
                                    </template>
                                </q-input>
                            </template>
                            <template v-slot:body-cell-status="props">
                                <q-td :props="props">
                                    {{ props.row.status == 1 ? `Active`:`Deactive` }}
                                </q-td>
                            </template>
                            <template v-slot:body-cell-items="props">
                                <q-td :props="props">
                                    <q-chip 
                                        v-for="(itm, idx) in props.row.itemDetails" 
                                        :key="idx" 
                                        color="primary"
                                        outline 
                                        text-color="white"
                                        icon="diamond">
                                        {{itm.type.value}}: {{itm.property}} ({{itm.weight}})
                                    </q-chip>
                                </q-td>
                            </template>
                        </q-table>
                    </div>
                </div>
            </div>
        </div>

        <!-- <addUserModal 
            v-bind="modalComponents" 
            @updateStatus="updateModalStatus" 
            @updateTable="getList"
        /> -->
    </div>
</template>

<script>
import moment from 'moment';
import { LocalStorage, SessionStorage } from 'quasar'
import noData from '../Templates/NoData.vue';
// import addUserModal from './Modal/AddUserModal.vue';
import jwt_decode from 'jwt-decode'
import { api } from 'boot/axios'

export default{
    name: 'SavePatientList',
    data(){
        return {
            isLoading: false,
            submitting: false,
            tableRow: [],
            saveDetails: null,
            saveId: null,
            insertedID: null,
            filter: '',
            modalComponents: {
                modalStatus: false,
                appId: 0,
                userDetails: {},
                modalTitle: 'Add New User'
            }
        }
    },
    components: {
        // addUserModal,
        noData
    },
    created(){
        this.getList();
    },
    methods: {
        openModal(){
            this.modalComponents.modalStatus = true;
        },
        updateModalStatus(){
            this.modalComponents.modalStatus = false;
        },
        async getList(){
            this.tableRow = [];
            this.isLoading = true;
            
            api.get('loan/get/loans/list').then((response) => {
                const data = {...response.data};

                if(!data.error){
                    this.tableRow = response.status < 300 ? data.list : [];
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
    },
    computed: {
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return jwt_decode(profile);
        },
        tableColumns: function(){
            return [
                {
                    name: 'orNumber',
                    required: true,
                    label: 'Reference Number',
                    align: 'left',
                    field: row => row.orNumber,
                    format: val => `${val}`,
                    sortable: true
                },
                { 
                    name: 'items', 
                    label: 'Items', 
                    field: 'items',
                    align: 'left',
                },
                {
                    name: 'loanAmount',
                    required: true,
                    label: 'Principal Amount',
                    align: 'left',
                    field: row => row.loanAmount,
                    format: val => `${val}`,
                    sortable: true
                },
                {
                    name: 'loanInterest',
                    required: true,
                    label: 'Interest',
                    align: 'left',
                    field: row => row.computationDetails.amountPercentage,
                    format: val => `${val}`,
                    sortable: true
                },
                {
                    name: 'maturityDate',
                    required: true,
                    label: 'Maturity',
                    align: 'left',
                    field: row => row.maturityDate,
                    format: val => `${val}`,
                    sortable: true
                },
                {
                    name: 'expirationDate',
                    required: true,
                    label: 'Expiry',
                    align: 'left',
                    field: row => row.expirationDate,
                    format: val => `${val}`,
                    sortable: true
                },
                {
                    name: 'status',
                    required: true,
                    label: 'Expiry',
                    align: 'left',
                    field: row => row.status,
                    format: val => `${val}`,
                    sortable: true
                },
                
            ]
        }
    }
}
</script>
