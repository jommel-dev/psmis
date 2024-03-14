<template>
    <div>
        <q-dialog
            v-model="openModal"
            persistent
            transition-show="slide-up"
            transition-hide="slide-down"
        >
            <q-card style="width: 40vw; max-width: 80vw;" >
                <q-bar >
                    <div class="text-h6">Loan History</div>
                    
                    <q-space />
                    <q-btn dense flat icon="close" @click="closeModal">
                        <q-tooltip class="bg-white text-primary">Close</q-tooltip>
                    </q-btn>
                </q-bar>

                <q-card-section style="max-height: 70vh; height: 100%;" class="q-pa-lg scroll">
                    <q-timeline>

                        <q-timeline-entry
                            v-for="(item, index) in historyList"
                            :key="index"
                            :title="item.actionTaken"
                            color="green"
                            :subtitle="item.createdDate"
                            icon="done_all"
                        >
                            <!-- (0)Create/Insert Action Type -->
                            <q-card v-if="item.actionType === '0'" class="my-card" flat bordered>
                                <q-card-section class="q-pt-none">
                                    <div class="text-subtitle1">
                                        OR Number: {{item.snapShot.orNumber}}
                                    </div>
                                    <div class="text-subtitle2">
                                        Principal: {{item.snapShot.loanAmount}}
                                    </div>
                                    <div class="text-subtitle2">
                                        Interest: {{item.snapShot.computationDetails.amountPercentage}}
                                    </div>
                                    <div class="text-subtitle2">
                                        Loan Amount: {{item.snapShot.computationDetails.principal}}
                                    </div>
                                </q-card-section>
                                <q-separator />
                                <q-card-action>
                                    <div class="text-caption text-grey">
                                        <q-chip 
                                            v-for="(itm, idx) in item.snapShot.itemDetails" 
                                            :key="idx" 
                                            color="primary"
                                            outline 
                                            text-color="white"
                                            icon="diamond">
                                            {{itm.type.value}}: {{itm.property}} ({{itm.weight}})
                                        </q-chip>
                                    </div>
                                </q-card-action>
                            </q-card>

                            <!-- Transaction Action Type -->
                            <q-card v-else class="my-card" flat bordered>
                                <q-card-section class="q-pt-none">
                                    <div class="text-subtitle1">
                                        OR Number: {{item.snapShot.orNumber}}
                                    </div>
                                    <div class="text-subtitle2">
                                        Amount: {{item.snapShot.amount}}
                                    </div>
                                    <div class="text-subtitle2">
                                        Date Maturity: {{item.snapShot.dateMaturity}}
                                    </div>
                                </q-card-section>
                            </q-card>
                        </q-timeline-entry>
                    </q-timeline>
                </q-card-section>

                <q-separator />

                <!-- <q-card-actions align="right">
                    asda
                </q-card-actions> -->
            </q-card>
        </q-dialog>
    </div>
</template>
<script>
import moment from 'moment';
import { LocalStorage } from 'quasar'
import jwt_decode from 'jwt-decode'
import { api } from 'boot/axios'

export default {
    name: 'LoanHistoryModal',
    data(){
        return {
            status: true,
            openModal: false,
            historyList: []
        }
    },
    watch:{
        modalStatus(newVal){
            this.openModal = newVal
            if(newVal){
                this.getHistoryList()
            }
            
        }
    },
    props: {
        appId: {
            type: Object
        },
        modalStatus: {
            type: Boolean
        },
        processType: {
            type: String
        }
    },
    computed: {
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return jwt_decode(profile);
        }
    },

    methods: {
        getHistoryList(){
            this.$q.loading.show();
            let payload = {
                lid: this.appId
            }
            api.post('loan/get/history', payload).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.historyList = data.list
                    this.$q.loading.hide();
                } else {
                    this.$q.notify({
                        color: 'negative',
                        position: 'top-right',
                        title:data.title,
                        message: this.$t(`errors.${data.error}`),
                        icon: 'report_problem'
                    })
                    this.$q.loading.hide();
                }

            })
        },
        async closeModal(){
            this.$emit('updateModalStatus', false);
        },
    }
    
}
</script>
