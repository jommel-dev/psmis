<template>
  <div class="q-pa-xs">
    <q-card flat>
      <q-card-section class="q-pt-none">
        <div class="row">
          <div class="col col-xs-12 col-md-3 q-pa-sm">
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
          <div class="col col-xs-12 col-md-3 q-pa-sm">
            <label>Loan Key Parameter</label>
            <q-select
              outlined 
              bottom-slots
              v-model="paramSelected" 
              :options="printParamsOptions"
              dense 
              :options-dense="true"
            />
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
        
        isLoading: false,
        rows: [],
        previewPDF: false,
        previewType: {value:"new", label:"New"},
        paramSelected: {value:"oldTicket", label:"oldTicket"},
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
            label: el,
          }
        })
      }
    },
    created(){
      this.createPDF();
      this.printParamsOptions
    },
    methods:{
      moment,
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

</style>
