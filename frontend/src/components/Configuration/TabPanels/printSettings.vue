<template>
  <div class="">
    <q-card flat>
      <q-card-section class="q-pt-none">
        <div class="row">
          <div class="col col-xs-12 col-md-3 q-pa-sm ">
            <label>Loan Status Type</label>
            <q-select
              outlined 
              bottom-slots
              v-model="previewType" 
              :options="previewTypes"
              dense 
              :options-dense="true"
            />
           
          </div>
          
          <div class="col col-xs-12 col-md-12 q-pa-md">
            <q-stepper
              v-model="step"
              color="primary"
              animated
            >
              <q-step
                :name="1"
                title="Select print preview values"
                icon="settings"
                :done="step > 1"
              >
                <div class="row">
                  <div class="col col-xs-12 col-md-3 q-pa-sm">
                    <label>Loan Key Parameter</label>
                    <q-select
                      outlined 
                      bottom-slots
                      v-model="paramSelected" 
                      :options="printParamsOptions"
                      dense 
                      :options-dense="true"
                      @update:model-value="keyParamChange"
                    />
                  </div>
                  <div class="col col-xs-12 col-md-3 q-pa-sm">
                    <label>Default Value</label>
                    <q-input
                      hint="Do not fill if its not required"
                      outlined 
                      stack-label 
                      dense
                      v-model="paramObjectVal.default"
                    />
                  </div>
                </div>

                <q-stepper-navigation>
                  <q-btn @click="step = 2" color="primary" label="Continue" />
                </q-stepper-navigation>
              </q-step>

              <q-step
                :name="2"
                title="Configuration"
                icon="settings"
                :done="step > 2"
              >
                For each ad campaign that you create, you can control how much you're willing to
                spend on clicks and conversions, which networks and geographical locations you want
                your ads to show on, and more.

                <q-stepper-navigation>
                  <q-btn flat @click="step = 1" color="primary" label="Back" class="q-mr-sm" />
                  <q-btn @click="step = 2" color="primary" label="Continue" />
                </q-stepper-navigation>
              </q-step>
            </q-stepper>
          </div>
          
        </div>
      </q-card-section>
      <q-card-section v-show="previewPDF" class="q-pt-none" style="height: 90vh;">
        <iframe id="pdf" style="width: 100%; height: 100%; border: none;"></iframe>
      </q-card-section>
    </q-card>
  </div>
</template>

<script>
import { LocalStorage } from 'quasar'
import moment from 'moment';
import jwt_decode from 'jwt-decode'
import { PDFDocument, StandardFonts, rgb } from 'pdf-lib'
import printSettingJSON from '../ConfigJSON/printSettings.json'
import { api } from 'boot/axios'

export default {
    name: 'AuctionList',
    components: {},
    data(){
      return {
        // Print
        step: 1,
        isLoading: false,
        rows: [],
        previewPDF: false,
        previewType: {value:"new", label:"New"},
        paramSelected: {value:"oldTicket", label:"Old Ticket Number"},
        paramSelectedOpt:[],
        paramObjectVal: {
          name: "Old Ticket Number",
          value: "",
          type: "text"
        },
        printSettings:[],
        previewTypes: [
          {
            label: "New",
            value: "new"
          },
          {
            label: "Renew",
            value: "renew"
          },
          {
            label: "Spoiled",
            value: "spoiled"
          },
        ],

      }
    },
    computed: {
      user: function(){
          let profile = LocalStorage.getItem('userData');
          return jwt_decode(profile);
      },
      printParamsOptions(){
        const keyParams = [...Object.keys(printSettingJSON.origData)]
        return keyParams.map((el) => {
          return {
            value: el,
            label: printSettingJSON.origData[el].name,
          }
        })
      }
    },
    created(){},
    methods:{
      moment,
      keyParamChange(val){
        const keyParams = {...printSettingJSON.origData}
        this.paramObjectVal = keyParams[val.value]
      },
      async createPDF(){
        const url = 'files/printReceipt.pdf'
        const existingPdfBytes = await fetch(url).then(res => res.arrayBuffer())
        // Create a new PDFDocument
        const pdfDoc = await PDFDocument.load(existingPdfBytes)
        // Add a blank page to the document
        const pages = pdfDoc.getPages()
        const fpage = pages[0];
        // Get the width and height of the page
        const { width, height } = fpage.getSize()
        const fontSize = 9
        let curdateYear = moment().format("YY");

        const pdfDataUri = await pdfDoc.saveAsBase64({ dataUri: true });
        document.getElementById('pdf').src = pdfDataUri;
      },
      
    }
}
</script>
<style scoped>
.itemCursor{
    cursor: pointer;
}
.itemHover:hover{
    background: aliceblue;
}
.my-bordered{
    border-radius: 10px;
    box-shadow: 0px 0px 3px -2px !important;
}
</style>
