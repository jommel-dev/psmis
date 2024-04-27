<template>
    <div>
        <q-dialog
            v-model="openModal"
            :maximized="maximizedToggle"
            transition-show="slide-up"
            transition-hide="slide-down"
        >
            <q-card class="">
                <q-bar>
                    <q-space />
                    <q-btn dense flat icon="minimize" @click="maximizedToggle = false" :disable="!maximizedToggle">
                        <q-tooltip v-if="maximizedToggle" class="bg-white text-primary">Minimize</q-tooltip>
                    </q-btn>
                    <q-btn dense flat icon="crop_square" @click="maximizedToggle = true" :disable="maximizedToggle">
                        <q-tooltip v-if="!maximizedToggle" class="bg-white text-primary">Maximize</q-tooltip>
                    </q-btn>
                    <q-btn dense flat icon="close" @click="closeModal">
                        <q-tooltip class="bg-white text-primary">Close</q-tooltip>
                    </q-btn>
                </q-bar>

                <q-card-section class="q-pt-none" style="height: 90vh;">
                    <iframe id="pdf" style="width: 100%; height: 100%; border: none;"></iframe>
                </q-card-section>
            </q-card>
        </q-dialog>
    </div>
</template>
<script>
import moment from 'moment';
import { LocalStorage } from 'quasar'
import { PDFDocument, StandardFonts, rgb } from 'pdf-lib'
import jwt_decode from 'jwt-decode'
import { api } from 'boot/axios'

const dateNow = moment().format('MM/DD/YYYY');

export default {
    name: 'PrintModal',
    data(){
        return {
            openModal: false,
            maximizedToggle: true,
        }
    },
    watch:{
        modalStatus: function(newVal){
            this.openModal = newVal
            if(newVal){
                this.appData.charge = Number(this.appData.charge).toFixed(2)
                this.createPDF(this.appData)
                this.getHistoryList(this.appData)
            }
        }
    },
    props: {
        appData: {
            type: Object
        },
        modalStatus: {
            type: Boolean
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
        async closeModal(){
            this.$emit('updatePrintStatus', false);
        },
        async createPDF(data){
            const url = 'files/printReceipt.pdf'
            // const url = 'files/draftInvoice.pdf'
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

            // Draw a string of text toward the top of the page
            // OR Number

            fpage.drawText(`${data.oldTicket}`, {
              x: 430,
              y: 765,
              size: 14,
              color: rgb(0, 0, 0),
            })

            let dateList = [];
            data.datesOfMaturity.forEach((el, indx) => {
                let pay = Number(data.computationDetails.amountPercentage) * (indx + 1)
                let res = `${moment(el.dateFormatted).format("MM/DD/YY")} - ${Number(pay).toLocaleString('en-US', {
                style: 'decimal',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
                })}`;
                dateList.push(res);
            });
            fpage.drawText(dateList.join("\r\n"), {
                x: 450,
                y: 735,
                size: 8,
                spacing: 1,
                lineHeight: 11,
                maxWidth: 230,
                color: rgb(0, 0, 0),
            })



            // Loan Status
            fpage.drawText(`${data.loanStatus}`, {
                x: 50,
                y: 705,
                size: 30,
                color: rgb(0, 0, 0),
            })

            // Dates
            // Date Granted
            let grantDate = data.createdDate !== undefined ?  moment(data.createdDate).format("LL") : moment().format("LL"); //Need to clarify
            fpage.drawText(`${grantDate}`, {
                x: 140,
                y: 660,
                size: 11,
                color: rgb(0, 0, 0),
            })

            // Date Maturity
            fpage.drawText(`${moment(data.maturityDate).format("LL")}`, {
                x: 430,
                y: 660,
                size: 11,
                color: rgb(0, 0, 0),
            })

            // Date Expiry
            fpage.drawText(`${moment(data.expirationDate).format("LL")}`, {
                x: 430,
                y: 645,
                size: 11,
                color: rgb(0, 0, 0),
            })

            // Name
            fpage.drawText(`${data.customerInfo.firstName} ${data.customerInfo.middleName} ${data.customerInfo.lastName}`, {
                x: 93,
                y: 632,
                size: 11,
                color: rgb(0, 0, 0),
            })
            // Address
            fpage.drawText(`${data.customerInfo.addressLine} ${data.customerInfo.addressDetails?.barangay.label} ${data.customerInfo.addressDetails?.city.label}`, {
                x: 340,
                y: 632,
                size: 9,
                color: rgb(0, 0, 0),
            })

            // Word Amount
            fpage.drawText(`${data.amountWord.toUpperCase()} PESOS ONLY`, {
                x: 154,
                y: 604,
                size: 11,
                color: rgb(0, 0, 0),
            })
            fpage.drawText(`${Number(data.loanAmount).toLocaleString('en-US', {
                style: 'decimal',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
                })}`, {
                x: 466,
                y: 604,
                size: 11,
                color: rgb(0, 0, 0),
            })
            fpage.drawText(`${Number(data.loanAmount).toLocaleString('en-US', {
                style: 'decimal',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
                })}`, {
                x: 470,
                y: 575,
                size: 11,
                color: rgb(0, 0, 0),
            })

            // Interest & months
            fpage.drawText(`${data.interestWord}`, {
                x: 137,
                y: 585,
                size: 11,
                color: rgb(0, 0, 0),
            })
            fpage.drawText(`${data.interest}`, {
                x: 210,
                y: 585,
                size: 11,
                color: rgb(0, 0, 0),
            })
            fpage.drawText(`${data.terms}`, {
                x: 260,
                y: 585,
                size: 11,
                color: rgb(0, 0, 0),
            })

            // Pay Details
            // Interest 2%
            fpage.drawText(`2%`, {
                x: 480,
                y: 560,
                size: 11,
                color: rgb(0, 0, 0),
            })
            // Principal
            fpage.drawText(`${data.loanAmount}`, {
                x: 450,
                y: 535,
                size: 11,
                color: rgb(0, 0, 0),
            })
            // Interest per month
            fpage.drawText(`${data.computationDetails.amountPercentage}/MONTH`, {
                x: 450,
                y: 522,
                size: 11,
                color: rgb(0, 0, 0),
            })
            // Charge

            fpage.drawText(`${data.charge}`, {
                x: 480,
                y: 508,
                size: 11,
                color: rgb(0, 0, 0),
            })
            // Interest 5%
            fpage.drawText(`${data.interest}%`, {
                x: 480,
                y: 470,
                size: 11,
                color: rgb(0, 0, 0),
            })


            // Description of Pawn
            let list = [];
            data.itemDetails.forEach(el => {
                let res = `${el.qty} ${el.unit.value} ${el.type.value}, ${el.description} with ${el.weight}, ${el.property}`;
                list.push(res);
            });
            fpage.drawText(list.join(", "), {
                x: 60,
                y: 530,
                size: 11,
                spacing: 1,
                lineHeight: 11,
                maxWidth: 230,
                color: rgb(0, 0, 0),
            })

            // ID & Contact
            fpage.drawText(`${data.identification.type} (${data.identification.idNumber})`, {
                x: 130,
                y: 450,
                size: 11,
                spacing: 1,
                lineHeight: 11,
                maxWidth: 230,
                color: rgb(0, 0, 0),
            })
            fpage.drawText(`${data.customerInfo.contactNo}`, {
                x: 369,
                y: 434,
                size: 11,
                color: rgb(0, 0, 0),
            })


            fpage.drawText(moment().format("l LT"), {
                x: 50,
                y: 348,
                size: 11,
                color: rgb(0, 0, 0),
            })



            const pdfDataUri = await pdfDoc.saveAsBase64({ dataUri: true });
            document.getElementById('pdf').src = pdfDataUri;

            this.selectLoading = false;
        },

        getHistoryList(data){
            let months = data.terms;
            let list = [];
            for (let index = 0; index < months; index++) {
                let monthly = data.computationDetails.amountPercentage;
                let payment = Number(monthly) * (index + 1)
                let maturity = moment(data.maturityDate).add(index, 'M').format('YYYY-MM-DD')
                let redeem = Number(data.loanAmount) + Number(payment)
                let obj = {
                    dateFormatted: maturity,
                    dateString: moment(maturity).format('LL'),
                    maturityPayment: payment.toLocaleString("en-US", {style:"currency", currency:"PHP"}),
                    redeemValue: redeem.toLocaleString("en-US", {style:"currency", currency:"PHP"}),
                }

                list.push(obj)
            }

            console.log(list)
        },

        // ending method
    },

}
</script>
