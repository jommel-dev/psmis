<template>
    <div>
        <q-dialog
            v-model="openModal"
            persistent
            transition-show="slide-up"
            transition-hide="slide-down"
        >
            <q-card style="width: 90vw; max-width: 100vw;" >
                <q-bar class="q-mb-md">
                    <div class="text-h6">Add New Loan</div>

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
                        </div>
                        <q-card class="my-card q-mb-md col col-md-12" flat bordered>
                            <q-item>
                                <q-item-section avatar>
                                    <q-avatar>
                                        <q-icon name="account_box" size="lg" />
                                    </q-avatar>
                                </q-item-section>

                                <q-item-section>
                                    <q-item-label>{{`${appId.firstName} ${appId.lastName}`}}</q-item-label>
                                    <q-item-label caption>
                                        {{ `${appId.addressLine} ${appId.addressDetails.barangay.label} ${appId.addressDetails.city.label} ${appId.addressDetails.province.label}` }}
                                    </q-item-label>
                                </q-item-section>
                            </q-item>

                            <q-separator />

                            <q-card-section horizontal>
                                <q-card-section>
                                    <span class="text-bold">Contact Number: </span>
                                </q-card-section>
                                <q-separator vertical />
                                <q-card-section class="col-4">
                                    <span>{{appId.contactNo || "N/A"}}</span>
                                </q-card-section>
                                <q-separator vertical />
                                <q-card-section>
                                    <span class="text-bold">Presented ID: </span>
                                </q-card-section>
                                <q-separator vertical />
                                <q-card-section class="col-4">
                                    ID not yet available as of the moment
                                    <!-- <span>{{`${appId.identifications[0].type} (${appId.identifications[0].idNumber})` || "N/A"}}</span> -->

                                    <!-- <q-popup-edit v-model="form.catId" v-slot="scope">
                                        <q-select
                                            outlined
                                            v-model="scope.value"
                                            :model-value="scope.value"
                                            :options="categoryOptions"
                                            stack-label
                                            dense
                                            options-dense
                                        >
                                            <template v-slot:after>
                                                <q-btn
                                                    flat dense color="negative" icon="cancel"
                                                    @click.stop.prevent="scope.cancel"
                                                />

                                                <q-btn
                                                    flat dense color="positive" icon="check_circle"
                                                    @click.stop.prevent="scope.set"
                                                    :disable="scope.initialValue === scope.value"
                                                />
                                            </template>
                                        </q-select>

                                    </q-popup-edit> -->
                                </q-card-section>
                            </q-card-section>

                        </q-card>


                        <div class="col col-md-12">
                            <span class="text-h6 q-mr-lg">Loan Details </span>
                        </div>
                        <q-card class="my-card q-mb-md col col-md-9" flat bordered>
                            <q-card-section class="row">
                                <div class="col col-md-12 q-mb-md">

                                    <q-chip
                                        size="md"
                                        :color="seriesDetatils.reportStatus === '1' ? 'green' : 'red'"
                                        text-color="white"
                                        icon="receipt_long"
                                        style="margin-top: -2px;"
                                    >
                                        {{form.orNumber}}
                                    </q-chip>
                                    <q-icon
                                        class="q-mr-lg"
                                        name="fiber_manual_record"
                                        :color="form.status === '1' ? 'green' : 'red'"
                                        size="md"
                                        style="margin-top: -7px;"
                                    />

                                    <!-- selection of Category -->
                                    <span class="text-bold q-mr-lg">
                                        Category: <span class="text-blue">{{form.catId.label}}</span>
                                        <q-popup-edit v-model="form.catId" v-slot="scope">
                                            <q-select
                                                outlined
                                                v-model="scope.value"
                                                :model-value="scope.value"
                                                :options="categoryOptions"
                                                stack-label
                                                dense
                                                options-dense
                                            >
                                                <template v-slot:after>
                                                    <q-btn
                                                        flat dense color="negative" icon="cancel"
                                                        @click.stop.prevent="scope.cancel"
                                                    />

                                                    <q-btn
                                                        flat dense color="positive" icon="check_circle"
                                                        @click.stop.prevent="scope.set"
                                                        :disable="scope.initialValue === scope.value"
                                                    />
                                                </template>
                                            </q-select>

                                        </q-popup-edit>
                                    </span>

                                    <q-btn
                                        size="sm"
                                        :label="openAddItem ? 'Cancel' : 'Add Item'"
                                        :color="openAddItem ? 'red' : 'primary'"
                                        :icon="openAddItem ? 'close' : 'add'"
                                        :disabled="form.catId.value === ''"
                                        @click="openAddItem = !openAddItem"
                                    />

                                </div>
                                <div v-if="openAddItem" class="col col-md-12 q-mb-lg q-mt-md">
                                    <div class="row">
                                        <div class="col col-md-2 q-pa-sm">
                                            <q-select
                                                outlined
                                                v-model="item.unit"
                                                :options="form.catId.unitsList"
                                                stack-label
                                                label="Unit"
                                                dense
                                                options-dense
                                            ></q-select>
                                        </div>
                                        <div class="col col-md-2 q-pa-sm">
                                            <q-select
                                                outlined
                                                v-model="item.type"
                                                :options="form.catId.typeList"
                                                stack-label
                                                label="Type"
                                                dense
                                                options-dense
                                            ></q-select>
                                        </div>
                                        <div class="col col-md-2 q-pa-sm">
                                            <q-input
                                                class="q-mb-sm"
                                                outlined
                                                v-model="item.qty"
                                                label="Quantity"
                                                stack-label
                                                dense
                                            />
                                        </div>
                                        <div class="col col-md-2 q-pa-sm">
                                            <q-input
                                                class="q-mb-sm"
                                                outlined
                                                v-model="item.description"
                                                label="Description"
                                                stack-label
                                                dense
                                            />
                                        </div>
                                        <div class="col col-md-2 q-pa-sm">
                                            <q-input
                                                class="q-mb-sm"
                                                outlined
                                                v-model="item.weight"
                                                label="Weight"
                                                stack-label
                                                dense
                                            />
                                        </div>
                                        <div class="col col-md-2 q-pa-sm">
                                            <q-input
                                                class="q-mb-sm"
                                                outlined
                                                v-model="item.property"
                                                label="Property"
                                                stack-label
                                                dense
                                            />
                                        </div>
                                        <div class="col col-md-12 q-pa-sm">
                                            <q-input
                                                class="q-mb-sm"
                                                outlined
                                                v-model="item.remarks"
                                                label="Remarks"
                                                stack-label
                                                dense
                                                type="textarea"
                                            />
                                        </div>
                                        <div class="col col-md-2 q-pa-sm">
                                            <q-btn
                                                dense
                                                icon="post_add"
                                                color="primary"
                                                label="Add Item"
                                                class="q-pa-sm"
                                                @click="addItemToList"
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div class="col col-md-8 q-mr-sm q-pa-sm" style="border-right: 1px solid grey;">
                                    <div class="row">
                                        <div class="col col-md-12 q-mr-sm">
                                            <q-table
                                                flat
                                                bordered
                                                title=""
                                                :rows="form.itemDetails"
                                                :columns="columns"
                                                row-key="name"
                                            >
                                                <template v-slot:body-cell-action="props">
                                                    <q-td key="action" :props="props">
                                                        <q-btn @click="removeId(props.row)" color="red" size="12px" flat dense round icon="delete" />
                                                    </q-td>
                                                </template>
                                            </q-table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col col-md-3 q-pl-sm">
                                  <q-input
                                      class="q-mb-sm"
                                      outlined
                                      v-model="form.officialReceipt"
                                      label="OR Number"
                                      stack-label
                                      dense
                                  />
                                    <span class="text-h6">Computation</span>
                                    <q-separator />

                                    <q-input
                                        class="q-mb-sm"
                                        outlined
                                        v-model="form.loanAmount"
                                        label="Loan Principal"
                                        stack-label
                                        dense
                                        lazy-rules
                                    />
                                    <q-input
                                        class="q-mb-sm"
                                        outlined
                                        v-model="form.terms"
                                        label="Terms"
                                        stack-label
                                        dense
                                        lazy-rules
                                    />
                                    <q-input
                                        class="q-mb-sm"
                                        outlined
                                        v-model="form.interest"
                                        label="Interest (%)"
                                        stack-label
                                        dense
                                    />
                                    <q-separator />
                                    <span class="text-bold">Charge: </span>{{form.charge}}
                                    <q-space />
                                    <span class="text-bold">% Amount: </span>{{form.computationDetails.amountPercentage}}
                                    <q-space />
                                    <span class="text-bold">Total Amount w/ %: </span>{{form.computationDetails.principal}}
                                    <q-space />
                                    <span class="text-bold">Amount Loan: </span>{{form.totalAmount}}
                                </div>
                            </q-card-section>
                        </q-card>
                        <q-card class="my-card q-mb-md q-pa-md col col-md-3" flat bordered>
                            <span class="text-h6 q-mr-lg">Loan Dates of Maturity</span>
                            <q-timeline>
                                <q-timeline-entry
                                    v-for="(item, index) in form.datesOfMaturity"
                                    :key="index"
                                    :title="item.dateString"
                                    color="primary"
                                > 
                                </q-timeline-entry>
                            </q-timeline>
                            <span class="text-bold q-mr-lg">Grace: <span class="text-red">{{moment(form.gracePeriodDate).format("LL")}}</span> </span>   
                        </q-card>
                    </q-form>
                </q-card-section>

                <q-separator />

                <q-card-actions align="right">
                    <q-btn
                        flat
                        label="Submit"
                        color="primary"
                        :disabled="enableContinue"
                        @click="submitModalClick"
                    />
                </q-card-actions>
            </q-card>
        </q-dialog>
    </div>
</template>
<script>
import moment from 'moment';
import { LocalStorage, SessionStorage } from 'quasar'
import jwt_decode from 'jwt-decode'
import { api } from 'boot/axios'
import chargesJson from '../../../utilities/charges.json'

export default {
    name: 'AddLoanModal',
    data(){
        return {
            status: true,
            openModal: false,
            openAddItem: false,
            item: {
                type: "",
                qty: "",
                unit: "",
                weight: "",
                property: "",
            },
            form: {
                customerId: 0,
                orNumber: '0000000',
                catId: {
                    label:'Select Category',
                    value:'',
                },
                // identification: {},
                itemDetails: [],
                loanAmount: 0,
                terms: 4,
                interest: 5,
                charge: 0,
                computationDetails: {
                    principal: 0,
                    amountPercentage: 0,
                },
                totalAmount: 0,
                datesOfMaturity: [],
                maturityDate: "",
                expirationDate: "",
                gracePeriodDate: "",
                status: '1',
                orStatus: '',
                createdBy: '',
                officialReceipt: '',
                cutoffDate:''
            },
            selectedItems: [],
            seriesDetatils:{},
            categoryOptions: [],
            unitOptions: [],
            listOfDates: []
        }
    },
    watch:{
        modalStatus(newVal, oldVal){
            this.openModal = newVal
            this.getReference()
            this.getCategories()
            this.generateMaturityDates
        },
        'form.loanAmount'(newVal){
            this.computeStampCharge(newVal)
            this.computeAmountInterest(newVal)
        },
        'form.terms'(newVal){
            this.computeStampCharge(this.form.loanAmount)
            this.computeAmountInterest(this.form.loanAmount)
            this.generateMaturityDates
        },
        'form.interest'(){
            this.computeStampCharge(this.form.loanAmount)
            this.computeAmountInterest(this.form.loanAmount)
        },
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
        enableContinue(){
            let checkItemVal = 0;
            let unvalidate = "customerId,orNumber,computationDetails,datesOfMaturity,status,orStatus,createdBy,officialReceipt,cutoffDate"
            for(const obj in this.form){
                
                if(
                    (this.form[obj] === "" || this.form[obj] ===  0 || this.form[obj].length === 0) &&
                    !unvalidate.includes(obj)
                ){
                    checkItemVal += 1
                }
            }
            return checkItemVal > 1;
        },
        columns(){
            return [
                {
                    name: 'type',
                    required: true,
                    label: 'Item Type',
                    align: 'left',
                    field: row => row.type,
                    format: val => `${val.value}`,
                    sortable: true
                },
                { name: 'qty', align: 'QTY', label: 'qty', field: 'qty', sortable: true },
                {
                    name: 'unit',
                    label: 'Unit',
                    field: row => row.unit,
                    format: val => `${val.value}`,
                    sortable: true
                },
                { name: 'weight', label: 'Weight', field: 'weight' },
                { name: 'description', label: 'Description', field: 'description' },
                { name: 'property', label: 'Property', field: 'property' },
                { name: 'remarks', label: 'Remarks', field: 'remarks' },
                { name: 'action',  field: 'action' }
            ]
        },
        generateMaturityDates(){
            const dateNow = moment().format('YYYY-MM-DD');
            const dateName = 'd'
            let currentDate = dateNow
            
            let list = []
            for (let index = 0; index < this.form.terms; index++) {
                let dayInmonth = moment(currentDate, "YYYY-MM").daysInMonth()
                let dateDays = dayInmonth === 31 ? Number(dayInmonth) - 1 : 30

                let mdate = moment(currentDate).add(dateDays, dateName).format('YYYY-MM-DD')
                let obj = {
                    dateFormatted: mdate,
                    dateString: moment(mdate).format('LL'),
                }
                list.push(obj)
                currentDate = mdate
            }
            this.form.datesOfMaturity = list

            // Set Grace period
            this.form.maturityDate = list.at(0).dateFormatted
            this.form.expirationDate = list.at(-1).dateFormatted
            this.form.gracePeriodDate = moment(list.at(-1).dateFormatted).add(15, dateName).format('YYYY-MM-DD')
        }
    },

    methods: {
        moment,
        removeId(item){
            let indexOfItem = this.form.itemDetails.indexOf(item)
            this.form.itemDetails.splice(indexOfItem, 1)
        },
        computeNewMaturity(term){
            this.form.expirationDate = moment(this.form.maturityDate).add(term, 'M').format('YYYY-MM-DD')
            this.form.gracePeriodDate = moment(this.form.expirationDate).add(15, 'd').format('YYYY-MM-DD')
        },
        computeStampCharge(loanAmount){
            const changes = chargesJson.chargeRates;
            let chargeAmount = 0;
            changes.forEach((charge) => {
                if(loanAmount >= charge.from && loanAmount <= charge.to){
                    chargeAmount = charge.charge
                }
            });
            this.form.charge = chargeAmount;
        },
        computeAmountInterest(loanAmount){
            let percentage = (this.form.interest / 100);
            let totalInterest = (loanAmount * percentage)
            this.form.computationDetails.amountPercentage = totalInterest.toFixed(2);
            this.form.computationDetails.principal = Number(loanAmount) + Number(totalInterest)
            this.form.totalAmount = Number(loanAmount)
        },

        addItemToList(){
            let checkItemVal = 0;
            for(const obj in this.item){
                if(this.item[obj] === ""){
                    checkItemVal += 1
                }
            }
            if(checkItemVal >= 1){
                this.$q.notify({
                    color: 'negative',
                    position: 'top-right',
                    title:``,
                    message: `Please fill all the Item details to proceed`,
                    icon: 'report_problem'
                })
                return false
            }

            this.form.itemDetails.push(this.item)
            this.item = {
                type: "",
                qty: "",
                unit: "",
                description: "",
                weight: "",
                property: "",
                remarks: "",
            }
            this.openAddItem = false
        },
        getReference(){
            // loan/get/reference
            let payload = {
                uid: this.user.userId
            }
            api.post('loan/get/reference', payload).then((response) => {
                const data = {...response.data};
                this.form.orNumber = data.start
                this.form.orStatus = data.reportStatus
                this.seriesDetatils = data
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
        async getCategories(){
            this.$q.loading.show();

            api.get('misc/getCategories').then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.categoryOptions = data.list
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
        async getUnits(){

            api.get('misc/getUnits').then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.unitOptions = data.list
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
                        message: 'Incomplete Form',
                        caption: 'Please fill the required fields',
                        icon: 'report_problem'
                    })
                    return false;
                } else if(vm.form.itemDetails.length === 0){
                    this.$q.notify({
                        color: 'negative',
                        position: 'top-right',
                        message: 'Incomplete Form',
                        caption: 'Loan Item must be added before proceeding',
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
                        this.addNewLoan();
                    })
                }
            })

        },

        async addNewLoan(){
            this.$q.loading.show();
            let cutOffDetails = SessionStorage.getItem("cutoff")
            cutOffDetails = JSON.parse(cutOffDetails)
            let payload = this.form
            payload.customerId = this.appId.key
            // payload.identification = this.appId.identifications.length > 0 ? {
            //     idNumber: this.appId.identifications[0].idNumber,
            //     type: this.appId.identifications[0].type,
            //     validUntil: this.appId.identifications[0].validUntil,
            // } : {
            //     idNumber: "N/A",
            //     type: "N/A",
            //     validUntil: "",
            // }
            payload.catId = this.form.catId.id
            payload.createdBy = this.user.userId
            payload.cutoffDate = cutOffDetails.startDate

            // console.log(payload)
            // return 
            api.post('loan/add/new', payload).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    // this.$emit('refreshData')
                    this.$emit('openPrintData', data.dataPrint)
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
        async updateProduct(){
            this.$q.loading.show();
            let payload = {
                id: this.appId.id,
                ...this.form
            }


            api.post('product/update/details', payload).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.$emit('refreshData')
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
                customerId: 0,
                orNumber: '0000000',
                catId: {
                    label:'Select Category',
                    value:'',
                },
                // identification: {},
                itemDetails: [],
                loanAmount: 0,
                terms: 4,
                interest: 5,
                charge: 0,
                computationDetails: {
                    principal: 0,
                    amountPercentage: 0,
                },
                totalAmount: 0,
                datesOfMaturity: [],
                maturityDate: "",
                expirationDate: "",
                gracePeriodDate: "",
                status: '1',
                orStatus: '',
                createdBy: '',
                officialReceipt: '',
                cutoffDate:''
            }
        }
    }

}
</script>
