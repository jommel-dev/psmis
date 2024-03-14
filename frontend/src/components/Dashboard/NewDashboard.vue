<template>
    <div class="q-pa-md" style="width: 100%;">
        <div class="row">
            <!-- Small cards -->
            <!--
            <div
            v-for="(item, index) in dashCards"
            :key="index"
            class="col col-md-3 col-lg-3 q-pa-sm"
            >
                <q-card flat bordered class="my-card q-pa-sm">
                <q-card-section  class="bg-white text-weight-light">
                    <div class="text-subtitle2 text-grey-5">{{item.title}} <q-icon name="info" /></div>
                    <div class="text-h6" :class="[item.color]">
                    <span class="">{{item.value}}</span>
                    <q-icon class="float-right" :color="item.iconColor" :name="item.icon" size="lg" />
                    </div>
                </q-card-section>
                </q-card>
            </div>-->
            <!-- Users Count Overview -->
            <div class="col-12 col-xs-12 col-sm-12 col-md-12 q-pa-sm">
                <q-card
                    flat
                    class="my-card bg-white"
                >
                    <q-card-section>
                        <span class="text-h6 text-bold">Dashboard Overview</span><br/>
                        <span class="text-caption text-grey">User count & Subscription summary</span><br/>

                        <div class="row">
                            <div
                                v-for="(item, idx) in dashCards"
                                :key="idx"
                                class="col-3 col-xs-12 col-sm-3 col-md-3 q-pa-xs"
                            >
                                <q-card
                                    flat
                                    class="my-card-item "
                                    :class="item.color"
                                >

                                    <q-card-section>
                                        <q-avatar
                                            size="md"
                                            :color="item.iconBg"
                                            text-color="white"
                                            :icon="item.icon"
                                        />
                                        <br/>
                                        <span class="text-bold text-h6 text-blue-grey-9" >{{item.value}}</span>
                                        <br/>
                                        <span class="text-subtitle text-blue-grey-9" >{{item.title}}</span>
                                        <br/>
                                        <span class="text-caption ellipsis" :class="item.captionColor" >{{item.captions}}</span>
                                    </q-card-section>
                                </q-card>
                            </div>
                        </div>
                    </q-card-section>
                </q-card>
            </div>
            <!-- Cards -->
            <div class="col-12 col-xs-12 col-sm-12 col-md-6 q-pa-sm">
                <q-card
                    flat
                    class="my-card bg-white"
                >
                    <q-card-section>
                        <q-toolbar >
                            <q-toolbar-title>
                                <span class="text-h6 text-bold">References Overview</span>
                                <q-space />
                                <span class="text-caption text-grey">Operations Current Reference</span>
                            </q-toolbar-title>
                            <q-btn flat round dense icon="more_vert" />
                        </q-toolbar>


                        <div v-if="dateSereies">
                            <div class="row">
                                <div
                                    v-for="(item) in dateSereies"
                                    :key="item.id"
                                    class="col-12 col-xs-12 col-sm-12 col-md-6 q-pa-sm"
                                >
                                    <q-card
                                        class="my-card generatedDash2"
                                        flat
                                    >
                                        <q-card-section horizontal>
                                            <q-card-section class="col">
                                                <span class="text-h6 text-bold text-white">{{`${item.userInfo.firstName} ${item.userInfo.lastName}`}}</span><br/>
                                                <span class="text-caption text-black">Reference Details</span><br/>
                                                <span class="text-h2 text-black">{{item.start}}</span><br/>
                                            </q-card-section>

                                            <q-card-actions vertical class="justify-around q-px-md">
                                                <q-btn flat round color="black" icon="more_vert" />
                                            </q-card-actions>
                                        </q-card-section>
                                    </q-card>
                                </div>
                            </div>

                        </div>
                        <div v-else>No Series Found</div>
                    </q-card-section>
                </q-card>
            </div>




        <!-- Modals -->
        </div>
    </div>
</template>

<script>
import jwt_decode from 'jwt-decode'
import { api } from 'boot/axios'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import moment from 'moment'
import { LocalStorage } from 'quasar'


const dateNow = moment().format('YYYY-MM-DD');

export default {
    name: 'CardWidgets',
    components: {
        FullCalendar,
    },
    data(){
        return {
            calendarOptions: {
                plugins: [ dayGridPlugin, interactionPlugin ],
                dayMaxEvents: true,
                initialView: 'dayGridMonth',
                // Date Action Handler
                // dateClick: (args) => { return this.handleDateClick(args) },
                selectable: true,
                events: this.eventList,
                eventContent: 'Show Details',
            },
            dashCards: [
                {
                    key: 'applicants',
                    title: 'Applicants',
                    value: 0,
                    subVal: '',
                    type: '',
                    info: '',
                    icon: 'contact_emergency',
                    valueType: 'Count',
                    chartType: '',
                    color: 'bg-light-green-2',
                    iconBg: 'light-green-5',
                    captions: 'All customers record',
                    captionColor: 'text-blue-14',
                },
                {
                    key: 'totalLoans',
                    title: 'Loan In-Progress',
                    value: 0,
                    subVal: '',
                    type: '',
                    info: '',
                    icon: 'devices_other',
                    valueType: 'Count',
                    chartType: '',
                    color: 'bg-amber-2',
                    iconBg: 'amber-5',
                    captions: 'Loans is currently running',
                    captionColor: 'text-blue-14',
                },
                {
                    key: 'totalRedeem',
                    title: 'Redeemed',
                    value: 0,
                    subVal: '',
                    type: '',
                    info: '',
                    icon: 'fact_check',
                    valueType: 'Count',
                    chartType: '',
                    color: 'bg-green-2',
                    iconBg: 'green-5',
                    captions: 'Loans/Items has claimed',
                    captionColor: 'text-blue-14',
                },
                {
                    key: 'totalExpired',
                    title: 'Deffered / Auctioned',
                    value: 0,
                    subVal: '',
                    type: '',
                    info: '',
                    icon: 'event_busy',
                    valueType: 'Count',
                    chartType: '',
                    color: 'bg-red-2',
                    iconBg: 'red-5',
                    captions: 'Loans/Items that is Expired',
                    captionColor: 'text-blue-14',
                },
                {
                    key: 'totalSales',
                    title: 'Total Sales',
                    value: 0.00,
                    subVal: '',
                    type: '',
                    info: '',
                    icon: 'attach_money',
                    valueType: 'Number',
                    chartType: '',
                    color: 'bg-cyan-2',
                    iconBg: 'cyan-5',
                    captions: 'All registered user in the app',
                    captionColor: 'text-blue-14',
                },
                {
                    key: 'todaySales',
                    title: 'Today Sales',
                    value: '0.00',
                    subVal: '',
                    type: '',
                    info: '',
                    icon: 'payments',
                    valueType: 'Number',
                    chartType: '',
                    color: 'bg-deep-orange-2',
                    iconBg: 'deep-orange-5',
                    captions: 'All registered user in the app',
                    captionColor: 'text-blue-14',
                },
                {
                    key: 'totalCharge',
                    title: 'Charge Collection',
                    value: 0.00,
                    subVal: '',
                    type: '',
                    info: '',
                    icon: 'remove_circle',
                    valueType: 'Number',
                    chartType: '',
                    color: 'bg-indigo-2',
                    iconBg: 'indigo-5',
                    captions: 'All registered user in the app',
                    captionColor: 'text-blue-14',
                },
                {
                    key: 'totalLoanExpense',
                    title: 'Loans',
                    value: 0.00,
                    subVal: '',
                    type: '',
                    info: '',
                    icon: 'attach_money',
                    valueType: 'Number',
                    chartType: '',
                    color: 'bg-teal-2',
                    iconBg: 'teal-5',
                    captions: 'All registered user in the app',
                    captionColor: 'text-blue-14',
                },
            ],
            eventList: [],

            // Calendar Sereies
            dateSereies: [],
            dateClicked: dateNow,
            modalComponents: {
                modalStatus: false,
                appId: 0,
                userDetails: {},
                modalTitle: 'Add Series of Reference to User'
            }
        }
    },
    computed: {
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return jwt_decode(profile);
        }
    },
    mounted(){
        this.getSchedules().then((res) => {
            this.checkLoanStatuses()
            this.$nextTick(() => {
                this.getDashboard()
            })
        })
    },
    methods: {
        moment,
        handleDateClick(val){
            this.dateClicked = val.dateStr
            this.getSchedules()
        },
        addNewSeries(){
            this.modalComponents.modalStatus = true;
            this.modalComponents.userDetails.dateSelected = this.dateClicked;
        },
        updateModalStatus(){
            this.modalComponents.modalStatus = false;
        },
        async getSchedules(){
            this.calendarOptions.events = [];
            this.$q.loading.show();

            let payload = {
                userId: this.user.userId,
                date: this.dateClicked
            }

            api.post('misc/get/admin/reference', payload).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.dateSereies = data
                } else {
                    this.$q.notify({
                        color: 'negative',
                        position: 'top-right',
                        title:data.title,
                        message: "No Reference Found!",
                        icon: 'report_problem'
                    })
                }
            })

        },
        getDashboard(){
            const vm = this
            let payload = {
                userId: this.user.userId,
                userType: this.user.userType,
                currDate: dateNow
            }

            api.post('misc/dashboard', payload).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.dashCards.map((el) => {
                        Object.keys(data).forEach((key) => {
                            if(el.key === key){
                                el.value = el.valueType === 'Number' ? vm.convertCurrency(data[key]) : Number(data[key])
                            }
                        })

                        return el
                    });
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

            this.$q.loading.hide();
        },
        checkLoanStatuses(){
            const vm = this
            let payload = {
                currDate: dateNow
            }

            api.post('misc/check/loans/expiry', payload).then((response) => {
                const data = {...response.data};
                if(data.error){
                   this.$q.notify({
                        color: 'negative',
                        position: 'top-right',
                        title:data.title,
                        message: 'Application update Failed',
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
    }
}
</script>
<style scoped>
.my-card{
    border-radius: 15px;
    box-shadow: 0px 0px 3px -2px !important;
}
.my-card-item{
    border-radius: 10px;
}
.generatedDash{
  color: white;
  background: rgb(0,177,250);
  background: linear-gradient(122deg, rgb(255 251 176) 0%, rgb(0 55 255 / 61%) 89%);
}
.generatedDash2{
  color: white;
  background: rgb(0,177,250);
  background: linear-gradient(122deg, rgb(38 148 243) 0%, rgb(45 253 215 / 61%) 89%);
}
</style>
