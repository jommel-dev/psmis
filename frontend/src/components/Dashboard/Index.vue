<template>
    <div class="q-pa-md" style="width: 100%;">
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

                    <q-btn flat round dense @click="addNewSeries">
                        <q-icon name="post_add" />
                    </q-btn>
                </q-toolbar>
                <q-list v-if="dateSereiesList.length > 0">
                    <q-item  
                        v-for="(item, index) in dateSereiesList" 
                        :key="index" 
                        v-ripple
                    >
                        <q-item-section avatar>
                            <q-avatar>
                                <q-icon name="account_circle" color="green" size="lg" />
                            </q-avatar>
                        </q-item-section>

                        <q-item-section>
                            <q-item-label class="text-weight-bold" lines="1">
                                <span class="text-weight-bold">Reference #: </span> 
                                <span>{{ item.start }}</span> to <span >{{ item.end }}</span>
                            </q-item-label>
                            <q-item-label caption lines="2">
                                {{ `${item.userInfo.firstName} ${item.userInfo.lastName}` }}
                            </q-item-label>
                        </q-item-section>

                        <!-- <q-item-section side top>
                            1 min ago
                        </q-item-section> -->
                    </q-item>
                </q-list>
                <div v-else>No Series Found</div>
            </div>
            <div class="col col-md-5 q-pa-sm">
                <q-card class="my-card q-mb-sm" flat bordered>
                    <q-item>
                        <q-item-section>
                        <q-item-label>Title</q-item-label>
                        <q-item-label caption>
                            Subhead
                        </q-item-label>
                        </q-item-section>
                    </q-item>

                    <q-separator />

                    <q-card-section horizontal class="row">
                        <q-card-section class="q-pt-xs col">
                            <div class="text-h6 q-mt-sm q-mb-xs">0</div>
                            <div class="text-caption text-grey">
                                Today's Sales
                            </div>
                        </q-card-section>
                        <q-card-section class="q-pt-xs col">
                            <div class="text-h6 q-mt-sm q-mb-xs">0</div>
                            <div class="text-caption text-grey">
                                Loan Accomodated
                            </div>
                        </q-card-section>
                        <q-card-section class="q-pt-xs col">
                            <div class="text-h6 q-mt-sm q-mb-xs">0</div>
                            <div class="text-caption text-grey">
                                Loan Applications
                            </div>
                        </q-card-section>
                        <q-card-section class="q-pt-xs col">
                            <div class="text-h6 q-mt-sm q-mb-xs">0</div>
                            <div class="text-caption text-grey">
                                Overall Sales
                            </div>
                        </q-card-section>
                    </q-card-section>

                    <q-separator />

                    <q-card-actions>
                        <q-btn flat round icon="event" />
                        <q-btn flat>
                        7:30PM
                        </q-btn>
                    </q-card-actions>
                </q-card>
            </div>
        </div>


        <!-- Modals -->
        <AddSeriesModal 
            v-bind="modalComponents" 
            @updateStatus="updateModalStatus"
            @updateTable="getSchedules"
        />
    </div>
</template>

<script>
import jwt_decode from 'jwt-decode'
import { api } from 'boot/axios'
import {LocalStorage} from 'quasar'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import AddSeriesModal from './Modal/AddSeriesModal.vue'
import moment from 'moment'


const dateNow = moment().format('YYYY-MM-DD');

export default{
    name: 'CardWidgets',
    components: {
        FullCalendar,
        AddSeriesModal
    },
    data(){
        return {
            calendarOptions: {
                plugins: [ dayGridPlugin, interactionPlugin ],
                dayMaxEvents: true,
                initialView: 'dayGridMonth',
                // Date Action Handler
                dateClick: (args) => { return this.handleDateClick(args) },
                selectable: true,
                events: this.eventList,
                eventContent: 'Show Details',
            },
            activities: [
                {
                    active: false,
                    title: "Loans on Due",
                    caption: "Items of Loan is on Due today",
                    action: () => { return false }
                },
                {
                    active: false,
                    title: "Booking",
                    caption: "Proceeds with client order transactions",
                    action: (val) => { return false }
                },
                {
                    active: false,
                    title: "Onsite Activity",
                    caption: "Vaccines, Inspections, Etc.",
                    action: () => { return false }
                },
            ],
            eventList: [],

            // Calendar Sereies
            dateSereiesList: [],
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
    created(){
        this.getSchedules().then((res) => {
            this.getDashboard()
        })
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
        async getSchedules(){
            this.calendarOptions.events = [];
            this.$q.loading.show();

            let payload = {
                date: this.dateClicked
            }

            api.post('misc/set/reference/list', payload).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.dateSereiesList = data.list
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
        getDashboard(){
            
            let payload = {
                userType: this.user.userType
            }

            api.post('misc/dashboard', payload).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    let res = []
                    let dash = Object.keys(data).map((key) => {
                        let obj = {
                            
                        }
                   })
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
        }
    }
}
</script>
