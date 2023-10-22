<template>
    <div class="q-pa-md" style="width: 100%;">
        <div class="row">
            <!-- Small cards -->
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
                    <!-- <q-space /> -->
                    <q-icon class="float-right" :color="item.iconColor" :name="item.icon" size="lg" />
                    </div>
                </q-card-section>
                </q-card>
            </div>
        </div>
        <div class="row">
            <!-- Cards -->
            <div class="col col-md-4 q-pa-sm">
                <FullCalendar 
                    :options="calendarOptions"
                >
                    <!-- <template v-slot:eventContent='arg'>
                        <b>{{ arg.event.title }}</b>
                    </template> -->
                </FullCalendar>
            </div>
            <div class="col col-md-3 text-left q-pa-sm bordered"> 
                <q-toolbar class="bg-teal-8 text-white q-mb-sm">
                    <q-toolbar-title>
                        {{ dateClicked }}
                    </q-toolbar-title>

                    <!-- <q-btn flat round dense @click="addNewSeries">
                        <q-icon name="post_add" />
                    </q-btn> -->
                </q-toolbar>
                
                <div v-if="dateSereies.start">
                    <span class="text-weight-bold">Reference #: </span> 
                    <span>{{ dateSereies.start }}</span> to <span >{{ dateSereies.end }}</span>
                </div>
                <div v-else>No Series Found</div>
            </div>
            
        </div>


        <!-- Modals -->
        
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

export default{
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
                    title: 'Today Sales',
                    value: '0.00',
                    subVal: '',
                    type: '',
                    info: '',
                    color: 'text-green-5',
                    icon: 'payments',
                    iconColor: 'primary',
                    valueType: '',
                    chartType: ''
                },
                {
                    title: 'Applicants',
                    value: 0,
                    subVal: '',
                    type: '',
                    info: '',
                    color: '',
                    icon: 'contact_emergency',
                    iconColor: 'green',
                    valueType: '',
                    chartType: ''
                },
                {
                    title: 'Loan Process',
                    value: 0,
                    subVal: '',
                    type: '',
                    info: '',
                    color: '',
                    icon: 'devices_other',
                    iconColor: 'blue-7',
                    valueType: '',
                    chartType: ''
                },
                {
                    title: 'Total Sales',
                    value: 0.00,
                    subVal: '',
                    type: '',
                    info: '',
                    color: '',
                    icon: 'attach_money',
                    iconColor: 'yellow-6',
                    valueType: '',
                    chartType: ''
                }
            ],
            eventList: [],

            // Calendar Sereies
            dateSereies: {},
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
        this.getSchedules()
    },
    methods: {
        eventClick(val){
            console.log(val)
        },
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
        getSchedules(){
            this.calendarOptions.events = [];
            this.$q.loading.show();

            let payload = {
                userId: this.user.userId,
                date: this.dateClicked
            }

            api.post('misc/get/user/reference', payload).then((response) => {
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

            this.$q.loading.hide();
        },
        getDashboard(){}
    }
}
</script>
