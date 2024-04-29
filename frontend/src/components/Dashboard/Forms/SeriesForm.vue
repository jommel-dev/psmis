<template>
    <div class="q-pa-md">
        <q-form
            ref="formDetails"
            class="row"
        >
            <div class="col col-xs-12 col-sm-12 col-md-12">
                <span class="text-h6">ACCOUNT DETAILS</span>
            </div>
            <q-select
                class="col col-xs-12 col-md-6 q-pa-sm "
                dense
                v-model="form.userId"
                use-input
                input-debounce="0"
                label="User Account"
                :options="typeList"
            />

            <!-- <div class="col col-xs-12 col-sm-12 col-md-12 q-mt-lg">
                <span class="text-h6">SERIES DETAILS</span>
            </div>
            
            <q-input
                class="col col-xs-12 col-md-6  q-pa-sm q-mt-sm"
                dense
                v-model="form.start"
                label="Start Series *"
                :rules="[ val => val && val.length > 0 || 'This field is required']"
            />
            <q-input
                class="col col-xs-12 col-md-6 q-pa-sm  q-mt-sm"
                dense
                v-model="form.end"
                label="End Series *"
                :rules="[ val => val && val.length > 0 || 'This field is required']"
            /> -->

            
            <q-select
                class="col col-xs-12 col-md-6 q-pa-sm"
                bottom-slots
                v-model="form.reportStatus" 
                :options="reportOptions" 
                label="Status Report"
                dense 
                :options-dense="true"
            >
            </q-select>

        </q-form>
    </div>
</template>
<script>
import moment from 'moment';
import { LocalStorage } from 'quasar'
import jwt_decode from 'jwt-decode'
import { api } from 'boot/axios'


export default{
    name: 'RegisterForm',
    data(){
        return {
            isPwd: true,
            form: {
                userId: "",
                start: "0",
                end: "0",
                reportStatus: "",
                status: 1
            },
            typeList: [],
        }
    },
    props: {
        appId: {
            type: Number
        },
        userDetails: {
            type: Object
        }
    },
    computed: {
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return jwt_decode(profile);
        },
        reportOptions: function(){
            let options = [
                {
                    label: "Reported",
                    value: "1"
                },
                {
                    label: "Record",
                    value: "0"
                },
            ]
            return options;
        },
    },
    created(){
        this.getUserList();
    },
    methods: {
        async getUserList(){
            api.get('users/getAllUserList').then((response) => {
                const data = {...response.data};
                let sList = [];

                if(!data.error){
                    data.list.forEach((el, key) => {
                        sList.push({
                            label: el.name,
                            value: el.key
                        })
                    });

                    this.typeList = response.status < 300 ? sList : [];
                } else {
                    this.$q.notify({
                        color: 'negative',
                        position: 'top-right',
                        message: data.message,
                        icon: 'report_problem'
                    })
                }

            })
        },
        resetForm(){
            this.form = {
                start: "",
                end: "",
                reportStatus: "",
                status: ""
            }
        }

        // ending method
    },
    
}
</script>
