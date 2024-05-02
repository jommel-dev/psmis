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
                    <div v-if="tableRow.length == 0" class="col text-center">
                        <noData />
                    </div>
                    <div v-if="tableRow.length > 0">
                        <q-table
                            title="Print List"
                            :rows="tableRow"
                            :columns="tableColumns"
                            :filter="filter"
                            row-key="name"
                        >
                            <template v-slot:top-right>
                                <q-input outlined dense debounce="300" v-model="filter" placeholder="Search">
                                    <template v-slot:append>
                                        <q-icon name="search" />
                                    </template>
                                </q-input>
                            </template>
                            <template v-slot:body-cell-customerInfo="props">
                                <q-td :props="props">
                                    {{ `${props.row.customerInfo.firstName} ${props.row.customerInfo.lastName}` }}
                                </q-td>
                            </template>
                            <template v-slot:body-cell-actions="props">
                                <q-td :props="props">
                                    <q-btn-group  rounded>
                                        <q-btn @click="openPrintModalWithData(props.row.origData)" rounded size="md" color="info" label="Print" />
                                    </q-btn-group>
                                </q-td>
                            </template>
                        </q-table>
                    </div>
                </div>
            </div>
        </div>

        <printModal
            :modalStatus="openPrintModal"
            :appData="formDetails"
            @updatePrintStatus="closePrintModal"
            @refreshData="getList"
        />
    </div>
</template>

<script>
import moment from 'moment';
import { LocalStorage, SessionStorage } from 'quasar'
import noData from '../../Templates/NoData.vue';
import jwt_decode from 'jwt-decode'
import printModal from '../Modals/PrintModal.vue'
import { api } from 'boot/axios'

export default{
    name: 'SavePatientList',
    data(){
        return {
            // Print
            openPrintModal: false,
            formDetails: {},

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
        noData,
        printModal
    },
    created(){
        this.getLoanList();
    },
    methods: {
        openPrintModalWithData(data){
            this.formDetails = data;
            this.openPrintModal = true;
        },
        closePrintModal(){
            this.openPrintModal = false;
        },
        async getLoanList(){
            this.tableRow = [];
            this.isLoading = true;

            api.get('loan/get/list/printing').then((response) => {
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
                { name: 'orNumber', label: 'Ticket No.', field: 'orNumber'},
                { name: 'customerInfo', label: 'Customer Name', field: 'customerInfo'},
                { name: 'actions', label: 'Action', field: 'actions' }
            ]
        }
    }
}
</script>
