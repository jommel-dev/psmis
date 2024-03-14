<template>
    <div>
        <q-dialog
            v-model="openModal"
            persistent
            transition-show="slide-up"
            transition-hide="slide-down"
        >
            <q-card style="width: 30vw; max-width: 80vw;" >
                <q-bar >
                    <div class="text-h6">Loan Terms</div>
                    
                    <q-space />
                    <q-btn dense flat icon="close" @click="closeModal">
                        <q-tooltip class="bg-white text-primary">Close</q-tooltip>
                    </q-btn>
                </q-bar>

                <q-card-section style="max-height: 100vh; height: 100%;" class="q-pa-lg scroll">
                    <q-timeline>
                        <q-timeline-entry
                            v-for="(item, index) in historyList"
                            :key="index"
                            :title="item.dateString"
                            :color="item.color"
                            :subtitle="item.status"
                            :icon="item.icon"
                        >
                            <div class="text-subtitle1">
                                        Payment: {{item.maturityPayment}}
                                    </div>
                                    <div class="text-caption">
                                        {{item.description}}
                                        <!-- <q-chip size="sm" square>
                                            <q-avatar :icon="item.statusIcon" color="red" text-color="white" />
                                            <span class="text-italic">{{item.status}}</span>
                                        </q-chip> -->
                                    </div>
                        </q-timeline-entry>
                    </q-timeline>
                </q-card-section>

                <q-separator />

                <q-card-actions align="left" class="text-bold">
                    GRACE PERIOD: <span class="">{{moment(this.loanData.gracePeriodDate).format('LL')}}</span>
                </q-card-actions>
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
    name: 'TermsLoanModal',
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
        loanData: {
            type: Object
        }
    },
    computed: {
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return jwt_decode(profile);
        }
    },

    methods: {
        moment,
        getHistoryList(){
            let months = this.loanData.terms;
            let list = [];
            for (let index = 0; index < months; index++) {
                let monthly = this.loanData.computationDetails.amountPercentage;
                let payment = Number(monthly) * (index + 1)
                let maturity = moment(this.loanData.maturityDate).add(index, 'M').format('YYYY-MM-DD')
                let terms = index + 1
                let otherProps = this.otherPatterns(this.loanData.payStatus, terms)
                let obj = {
                    ...otherProps,
                    dateFormatted: maturity,
                    dateString: moment(maturity).format('LL'),
                    maturityPayment: payment.toLocaleString("en-US", {style:"currency", currency:"PHP"}),
                    termsValue: terms
                }

                list.push(obj)
            }
            this.historyList = list
        },
        otherPatterns(terms, indx){
            // if terms is still one
            if(Number(terms) === indx){
                return {
                    color: "orange",
                    icon: "schedule",
                    status: "To Pay",
                }
            } else if(Number(terms) !== 1 && indx < Number(terms) ){
                return {
                    color: "red",
                    icon: "event_busy",
                    status: "Past Due",
                }
            } else {
                return {
                    color: "grey",
                    icon: "history_toggle_off",
                    status: "Pending",
                }
            }
        },
        async closeModal(){
            this.$emit('updateModalStatus', false);
        },
    }

}
</script>
