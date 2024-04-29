<template>
    <div>
        <q-dialog
            v-model="openModal"
            persistent
            transition-show="slide-up"
            transition-hide="slide-down"
        >
            <q-card style="width: 100vw; max-width: 100vw;" >
                <q-bar>
                    <div class="text-h6">Customer Signature</div>
                    <q-space />
                    <q-btn dense flat icon="close" @click="closeModal">
                        <q-tooltip class="bg-white text-primary">Close</q-tooltip>
                    </q-btn>
                </q-bar>

                <q-card-section style="max-height: 70vh; height: 100%;" class="q-pt-none scroll">
                    <canvas id="signature-pad" class="signPad" width=1080 height="500"></canvas>
                </q-card-section>

                <q-separator />

                <q-card-actions align="right">
                    <q-btn dense flat icon="restart_alt" size="md" @click="clearSignature">
                        Reset
                    </q-btn>
                    <q-btn dense flat icon="draw" size="md" @click="getSignature">
                        Submit
                    </q-btn>
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
import SignaturePad from 'signature_pad'

export default {
    name: 'SignModal',
    data(){
        return {
            openModal: false,
            signPadCont: {}
        }
    },
    watch:{
        modalStatus(newVal, oldVal){
            this.openModal = newVal
            let vm = this

            if(newVal){
                this.$q.loading.show()
                setTimeout(() => {
                    vm.initSignPad()
                }, 2000);
            }
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
        }
    },
    methods: {
        initSignPad(){
            let canvas = document.querySelector("canvas");
            
            this.signPadCont = new SignaturePad(canvas);

            this.$q.loading.hide()
        },
        getSignature(){
            this.$emit('setSignature', this.signPadCont.toDataURL())
            this.closeModal();
        },
        clearSignature(){
            this.signPadCont.clear();
        },
        async closeModal(){
            this.$emit('updateSignModalStatus', false);
        },
    }
}
</script>

<style scoped>
.signPad{
    border: 1px solid;
    width: 94vw;
}
</style>
