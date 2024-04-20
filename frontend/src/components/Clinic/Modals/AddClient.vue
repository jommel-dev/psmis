<template>
    <div>
        <q-dialog
            class="modalIndex"
            v-model="openModal"
            persistent
            transition-show="slide-up"
            transition-hide="slide-down"
        >
            <q-card style="width: 70vw; max-width: 80vw;" >
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
                            style="width: 100%;border-bottom: 1px solid grey;"
                        >

                            <template v-slot:before>
                                <div class="q-mb-md q-pa-sm">
                                    <div v-if="form.profile === ''" class="text-center">
                                        <q-icon name="person" size="6.5rem"/>
                                    </div>
                                    <q-img
                                        v-else
                                        :src="form.profile"
                                        spinner-color="white"
                                        style="width: 100%;"
                                        class="q-mb-sm"
                                        fit
                                    />
                                    <q-btn
                                        class="q-ml-sm q-pl-sm q-pr-sm vertical-middle"
                                        rounded
                                        @click="openProfileCamera()"
                                        dense
                                        color="primary"
                                        size="sm"
                                        label="Upload Customer Picture"
                                    />
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
                                            v-model="form.contactNo" 
                                            label="Contact Number" 
                                            stack-label 
                                            dense
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
                            style="height: 200px; width: 100%;"
                        >

                            <template v-slot:before>
                                <div class="q-pa-sm">
                                    <q-img
                                        fit
                                        :src="form.eSignature"
                                        spinner-color="white"
                                        style="height: 140px; width: 100%;"
                                    />
                                    <q-btn
                                        @click="renderSignModal"
                                        class="q-ml-sm q-pl-sm q-pr-sm vertical-middle full-width"
                                        rounded
                                        dense
                                        color="primary"
                                        size="sm"
                                        label="Signature"
                                    />
                                </div>
                                
                            </template>

                            <template v-slot:after>
                                <q-toolbar class="bg-primary text-white">
                                    <q-btn flat round dense icon="badge">
                                        <q-badge floating color="red">{{form.identifications.length}}</q-badge>
                                    </q-btn>
                                    <q-toolbar-title>
                                        Presented ID's
                                    </q-toolbar-title>
                                    <q-btn flat round dense icon="add_photo_alternate" @click="openAddIDCamera" />
                                </q-toolbar>
                                <!-- ID Content -->
                                <div v-if="form.identifications.length !== 0">
                                    <q-list bordered class="rounded-borders" style="max-width: 100%">

                                        <q-item v-for="(item, index) in form.identifications" :key="index">
                                            <q-item-section avatar top>
                                                <q-icon name="badge" color="black" size="34px" />
                                            </q-item-section>

                                            <q-item-section top class="col-2 gt-sm">
                                                <q-item-label class="q-mt-sm"> {{ item.type }}</q-item-label>
                                                <q-popup-edit v-model="item.type" :validate="val => val.length > 5" v-slot="scope">
                                                    <q-input
                                                    autofocus
                                                    dense
                                                    v-model="scope.value"
                                                    :model-value="scope.value"
                                                    >
                                                    <template v-slot:after>
                                                        <q-btn
                                                        flat dense color="negative" icon="cancel"
                                                        @click.stop.prevent="scope.cancel"
                                                        />

                                                        <q-btn
                                                        flat dense color="positive" icon="check_circle"
                                                        @click.stop.prevent="scope.set"
                                                        :disable="scope.validate(scope.value) === false || scope.initialValue === scope.value"
                                                        />
                                                    </template>
                                                    </q-input>
                                                </q-popup-edit>
                                            </q-item-section>

                                            <q-item-section top>
                                                <q-item-label lines="1">
                                                    <span class="text-weight-medium">ID Number - </span>
                                                    <span class="text-grey-8">
                                                        {{ item.idNumber }}
                                                        <q-popup-edit v-model="item.idNumber" :validate="val => val.length > 5" v-slot="scope">
                                                            <q-input
                                                            autofocus
                                                            dense
                                                            v-model="scope.value"
                                                            :model-value="scope.value"
                                                            >
                                                            <template v-slot:after>
                                                                <q-btn
                                                                flat dense color="negative" icon="cancel"
                                                                @click.stop.prevent="scope.cancel"
                                                                />

                                                                <q-btn
                                                                flat dense color="positive" icon="check_circle"
                                                                @click.stop.prevent="scope.set"
                                                                :disable="scope.validate(scope.value) === false || scope.initialValue === scope.value"
                                                                />
                                                            </template>
                                                            </q-input>
                                                        </q-popup-edit>
                                                    </span>
                                                </q-item-label>
                                                <q-item-label caption lines="1">
                                                    Validity Until: {{item.validUntil}}
                                                    <q-popup-edit v-model="item.validUntil" :validate="val => val.length > 5" v-slot="scope">
                                                        <q-input
                                                        autofocus
                                                        dense
                                                        v-model="scope.value"
                                                        :model-value="scope.value"
                                                        >
                                                        <template v-slot:after>
                                                            <q-btn
                                                            flat dense color="negative" icon="cancel"
                                                            @click.stop.prevent="scope.cancel"
                                                            />

                                                            <q-btn
                                                            flat dense color="positive" icon="check_circle"
                                                            @click.stop.prevent="scope.set"
                                                            :disable="scope.validate(scope.value) === false || scope.initialValue === scope.value"
                                                            />
                                                        </template>
                                                        </q-input>
                                                    </q-popup-edit>
                                                </q-item-label>
                                            </q-item-section>

                                            <q-item-section top side>
                                            <div class="text-grey-8 q-gutter-xs">
                                                <q-btn @click="removeId(index)" class="gt-xs" color="red" size="12px" flat dense round icon="delete" />
                                            </div>
                                            </q-item-section>

                                            <q-separator spaced />
                                        </q-item>
                                        
                                    </q-list>
                                </div>
                                <div v-else>
                                    No Identification Added
                                </div>
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


        <signModal 
            :modalStatus="openSignModal"
            @updateSignModalStatus="closeSignModal"
            @setSignature="getSignature"
        />
    </div>
</template>
<script>
import moment from 'moment';
import { LocalStorage } from 'quasar'
import jwt_decode from 'jwt-decode'
import { api } from 'boot/axios'
import signModal from './AddSignature.vue'
import addresses from '../../../utilities/addresses.json';
import miscJson from '../../../utilities/identifications.json';
import { defineCustomElements } from '@ionic/pwa-elements/loader';
import { Camera, CameraResultType, CameraSource } from '@capacitor/camera'

export default {
    name: 'AddCustomerModal',
    components: {
        signModal
    },
    data(){
        return {
            splitterModel: 40,
            splitterInfoModel: 20,
            openModal: false,
            openSignModal: false,
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
                contactNo: '',
                otherDetails: {
                    occupation: '',
                    civilStatus: '',
                    email: '',
                },
                profile:'',
                eSignature: '',
                identifications: []
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
        identifcationList(){
            let opt = miscJson.list;

            opt.map((item) => {
                let obj  = {
                    label: item,
                    value: item
                }

                return obj;
            })

            return opt
        },
    },

    methods: {
        renderSignModal(){
            this.openSignModal = true;
        },
        closeSignModal(){
            this.openSignModal = false;
        },
        getSignature(val){
            this.form.eSignature = val
        },
        openProfileCamera(){
            let vm = this;
            defineCustomElements(window).then(()=>{
                vm.playCamera()
            })
        },
        removeId(index){
            this.form.identifications.splice(index, 1)
        },
        async playCamera(){
            const image = await Camera.getPhoto({
                quality: 15,
                source: CameraSource.Camera,
                allowEditing: false,
                resultType: CameraResultType.DataUrl,
                presentationStyle:'popover',
                saved: true
            }).catch((err) => {
                alert(err)
            })
            
            this.form.profile = image.dataUrl
        },
        openAddIDCamera(){
            let vm = this;
            defineCustomElements(window).then(()=>{
                vm.addIdentificationCamera()
            })
        },
        async addIdentificationCamera(){
            const image = await Camera.getPhoto({
                quality: 15,
                source: CameraSource.Camera,
                allowEditing: false,
                resultType: CameraResultType.DataUrl,
                presentationStyle:'popover',
                saved: true
            }).catch((err) => {
                alert(err)
            })

            this.form.identifications.push({
                type: "--",
                idNumber: "--",
                validUntil: "--",
                image: image.dataUrl
            })

        },
        computeAge(val){
            var today = new Date();
            var birthDate = new Date(val);

            const monthDiff = today.getMonth() - birthDate.getMonth();
            const yearDiff = today.getYear() - birthDate.getYear();

            let ageMonth = monthDiff + yearDiff * 12;

            this.form.age = ageMonth
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
            // this.$q.loading.show();
            let payload = this.form
            payload.sex = payload.sex.value
            payload.createdBy = this.user.userId
            
            api.post('application/register', payload).then((response) => {
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

            // this.$q.loading.hide();
        },
        

        clearForm(){
            this.form = {
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
                contactNo: '',
                otherDetails: {
                    occupation: '',
                    civilStatus: '',
                    email: '',
                },
                profile:'',
                eSignature: '',
                identifications: []
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

<style>
.hydrated{
    z-index: 9999999 !important;
}
</style>