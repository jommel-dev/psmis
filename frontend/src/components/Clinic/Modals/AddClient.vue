<template>
    <div>
        <q-dialog
            v-model="openModal"
            persistent
            transition-show="slide-up"
            transition-hide="slide-down"
        >
            <q-card style="width: 900px; max-width: 80vw;" >
                <q-bar class="q-mb-md">
                    <div class="text-h6">Add New Customer</div>
                    <q-space />
                    <q-btn dense flat icon="close" @click="closeModal">
                        <q-tooltip class="bg-white text-primary">Close</q-tooltip>
                    </q-btn>
                </q-bar>

                <q-card-section style="max-height: 70vh; height: 100%;" class="q-pt-none scroll">
                    <q-form
                        ref="formDetails"
                        class="row"
                    >   
                        <div class="col col-md-12">
                            <span class="text-h5">Customer Details</span>
                            <q-separator />
                        </div>
                        <q-splitter
                            v-model="splitterInfoModel"
                            style="width: 100%;"
                        >

                            <template v-slot:before>
                                <div class="text-center">
                                    <q-icon name="person" size="7rem"/>
                                </div>
                                
                            </template>

                            <template v-slot:after>
                                <div class="row">
                                    <div class="col col-md-3 q-pa-sm">
                                        <q-input
                                            outlined 
                                            v-model="form.firstName" 
                                            label="First Name" 
                                            stack-label 
                                            dense
                                        />
                                    </div>
                                    <div class="col col-md-3 q-pa-sm">
                                        <q-input
                                            outlined 
                                            v-model="form.middleName" 
                                            label="Middle Name" 
                                            stack-label 
                                            dense
                                        />
                                    </div>
                                    <div class="col col-md-3 q-pa-sm">
                                        <q-input
                                            outlined 
                                            v-model="form.lastName" 
                                            label="Last Name" 
                                            stack-label 
                                            dense
                                        />
                                    </div>
                                    <div class="col col-md-3 q-pa-sm">
                                        <q-input
                                            outlined 
                                            v-model="form.suffix" 
                                            label="Suffix" 
                                            stack-label 
                                            dense
                                        />
                                    </div>


                                    <div class="col col-md-3 q-pa-sm">
                                        <q-input 
                                            outlined 
                                            type="date"
                                            v-model="form.birthDate" 
                                            @change="computeAge"
                                            label="Birthdate" 
                                            stack-label 
                                            dense
                                        />
                                    </div>
                                    <div class="col col-md-3 q-pa-sm">
                                        <q-select 
                                            outlined 
                                            v-model="form.sex" 
                                            :options="genderOption" 
                                            label="Gender" 
                                            stack-label 
                                            dense
                                            options-dense
                                        />
                                    </div>
                                    <div class="col col-md-3 q-pa-sm">
                                        <q-input
                                            outlined 
                                            v-model="form.otherDetails.occupation" 
                                            label="Occupation" 
                                            stack-label 
                                            dense
                                        />
                                    </div>
                                    <div class="col col-md-3 q-pa-sm">
                                        <q-select 
                                            outlined 
                                            v-model="form.otherDetails.civilStatus" 
                                            :options="civilOption" 
                                            label="Civil Status" 
                                            stack-label 
                                            dense
                                            options-dense
                                        />
                                    </div>
                                </div>
                            </template>

                        </q-splitter>

                        

                        <div class="col col-md-12 q-mt-md">
                            <span class="text-h5">Address Details</span>
                            <q-separator />
                        </div>
                        <q-input
                            autogrow
                            class="col col-xs-12 col-md-12 q-pa-sm"
                            type="textarea"
                            outlined 
                            v-model="form.addressLine" 
                            label="Address Line1" 
                            stack-label 
                            dense
                        />
                        <q-select
                            outlined 
                            class="col col-xs-12 col-md-3 q-pa-sm"
                            bottom-slots
                            v-model="form.addressDetails.region" 
                            :options="regionList" 
                            label="Region"
                            dense 
                            :options-dense="true"
                            @update:model-value="regionChanged"
                        />
                        

                        <q-select
                            outlined
                            class="col col-xs-12 col-md-3 q-pa-sm"
                            bottom-slots
                            v-model="form.addressDetails.province" 
                            :options="provinceList" 
                            label="Province"
                            dense 
                            :options-dense="true"
                            @update:model-value="provinceChanged"
                        />

                        <q-select
                            outlined
                            class="col col-xs-12 col-md-3 q-pa-sm"
                            bottom-slots
                            v-model="form.addressDetails.city" 
                            :options="cityList" 
                            label="City"
                            dense 
                            :options-dense="true"
                            @update:model-value="cityChange"
                        />

                        <q-select
                            outlined
                            class="col col-xs-12 col-md-3 q-pa-sm"
                            bottom-slots
                            v-model="form.addressDetails.barangay" 
                            :options="brgyList" 
                            label="Baranagay"
                            dense 
                            :options-dense="true"
                        />

                        <div class="col col-md-12 q-mt-md">
                            <span class="text-h5">Identification Details</span>
                            <q-separator />
                        </div>
                        <q-splitter
                            v-model="splitterModel"
                            style="height: 100px; width: 100%;"
                        >

                            <template v-slot:before>
                                Signature
                            </template>

                            <template v-slot:after>
                                ID's
                            </template>

                        </q-splitter>
                    </q-form>
                </q-card-section>

                <q-separator />

                <q-card-actions align="right">
                    <q-btn
                        flat 
                        label="Submit" 
                        color="primary"
                        @click="submitModalClick" 
                    />
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
import addresses from '../../../utilities/addresses.json';

export default {
    name: 'PrintModal',
    data(){
        return {
            splitterModel: 40,
            splitterInfoModel: 20,
            openModal: false,
            regionList: [],
            provinceList: [],
            cityList: [],
            brgyList: [],
            form: {
                firstName: '',
                lastName: '',
                middleName: '',
                suffix: '',
                addressLine: '',
                addressDetails: {
                    region: "",
                    province: "",
                    city: "",
                    barangay: "",
                },
                birthDate: '',
                sex: '',
                species: '',
                contactNo: '',
                otherDetails: {
                    occupation: '',
                    civilStatus: '',
                    email: '',
                },
                eSignature: ''
            },
            genderOption: [
                {
                    label: 'MALE',
                    value: 'MALE',
                },
                {
                    label: 'FEMALE',
                    value: 'FEMALE',
                },
                {
                    label: 'PREFER NOT TO SAY',
                    value: 'PREFER NOT TO SAY',
                },
            ],
            civilOption: [
                {
                    label: 'Single',
                    value: 'Single',
                },
                {
                    label: 'Married',
                    value: 'Married',
                },
                {
                    label: 'Widowed',
                    value: 'Widowed',
                },
                {
                    label: 'Separated',
                    value: 'Separated',
                },
            ]
        }
    },
    watch:{
        modalStatus(newVal, oldVal){
            this.openModal = newVal
            this.loadRegion();
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
        },
    },

    methods: {
        computeAge(val){
            var today = new Date();
            var birthDate = new Date(val);

            const monthDiff = today.getMonth() - birthDate.getMonth();
            const yearDiff = today.getYear() - birthDate.getYear();

            let ageMonth = monthDiff + yearDiff * 12;

            this.form.age = ageMonth
        },
        fillTheDetails(){
            this.form = {
                sku: this.appId.sku,
                productName: this.appId.productName,
                productCost: this.appId.productCost,
                productSRP: this.appId.productSRP,
                unit: JSON.parse(this.appId.unit),
                category: JSON.parse(this.appId.category),
                description: this.appId.description,
                hasPriceGroup: false,
                costGroup: {},
            }
        },
        
        async closeModal(){
            this.$emit('updateModalStatus', false);
        },
        async submitModalClick(){
            let vm = this;

            this.$refs.formDetails.validate().then(success => {
                if(!success){
                    this.$q.notify({
                        color: 'negative',
                        position: 'top-right',
                        title: 'Incomplete Form',
                        message: 'Please fill the required fields',
                        icon: 'report_problem'
                    })
                    return false;
                } else {
                    // Confirm
                    this.$q.dialog({
                        title: 'Submit details',
                        message: 'Would you like to finalize and save your data?',
                        ok: {
                            label: 'Yes'
                        },
                        cancel: {
                            label: 'No',
                            color: 'negative'
                        },
                        persistent: true
                    }).onOk(() => {
                        // this.$emit('submitModalClick', vm.form);
                        this.addNewPatient();
                    })
                }
            })
            
        },

        async addNewPatient(){
            this.$q.loading.show();
            let payload = this.form
            payload.clientId = this.appId.id
            payload.sex = payload.sex.value
            payload.createdBy = this.user.userId
            
            api.post('patient/add/new', payload).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.$emit('refreshData')
                    this.clearForm();
                    this.closeModal();
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
        

        clearForm(){
            this.form = {
                clientId: 0,
                petName: '',
                birthDate: '',
                age: 0,
                sex: '',
                species: '',
                breed: '',
            }
        },


        loadRegion(){
            const regionsList = addresses.region;
            this.regionList = regionsList.map((el, _index) => {
                let obj = {
                    label: el.region_name,
                    value: el.region_code
                }

                return obj
            })
        },
        regionChanged(val){
            const provList = addresses.province
            let filterProv = provList.filter(el => {return el.region_code === val.value})
            this.provinceList = filterProv.map((el, _index) => {
                let obj = {
                    label: el.province_name,
                    value: el.province_code
                }
                return obj
            })
        },
        provinceChanged(val){
            const cityList = addresses.city
            let filterCity = cityList.filter(el => {return el.province_code === val.value})
            this.cityList = filterCity.map((el, _index) => {
                let obj = {
                    label: el.city_name,
                    value: el.city_code
                }

                return obj
            })
        },
        cityChange(val){
            const brgyList = addresses.barangay
            let filterCity = brgyList.filter(el => {return el.city_code === val.value})
            this.brgyList = filterCity.map((el, _index) => {
                let obj = {
                    label: el.brgy_name,
                    value: el.brgy_code
                }

                return obj
            })
        },
    }
    
}
</script>
