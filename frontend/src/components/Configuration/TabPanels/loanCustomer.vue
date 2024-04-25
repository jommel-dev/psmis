<template>
  <div class="">
    <q-card
      flat
    >
      <q-card-section>
        <q-form
          ref="formDetails"
          class="row"
        >
          <div class="col col-12 q-mt-md q-mb-sm">
            <span class="text-h6"> Customer Settings</span>

            <div class="row">
              <div class="col col-md-3 q-pa-sm">
                <q-input
                  outlined 
                  label="Customer Number" 
                  stack-label 
                  dense
                />
              </div>
            </div>
          </div>

          <div class="col col-12">
            <q-separator />
          </div>
          
          <div class="col col-12 q-mt-sm">
            <span class="text-h6"> Ticket Settings</span>
            <q-btn class="float-right" flat color="primary" rounded dense icon="add" @click="addNewSeries" />
            <q-table
              class="q-mt-sm"
              flat
              :grid="false"
              hide-bottom
              hide-header
              :rows="setting.loanReference"
              :columns="loanColumns"
              row-key="name"
            >
              <template v-slot:body-cell-action="props">
                <q-td key="action" :props="props">
                  <q-btn :disabled="true" @click="removeId(props.row)" color="red" size="12px" flat dense round icon="delete" />
                </q-td>
              </template>
              <template v-slot:body-cell-start="props">
                <q-td key="start" :props="props">
                  {{props.row.start}}
                  <q-popup-edit v-model="props.row.start" v-slot="scope">
                    <q-input 
                      v-model="scope.value" 
                      dense 
                      autofocus 
                      counter 
                      @keyup.enter="scope.set"
                      @update:model-value="updateReference(props.row.id, scope.value)"
                    />
                  </q-popup-edit>
                </q-td>
              </template>
              <template v-slot:body-cell-reportStatus="props">
                <q-td key="reportStatus" :props="props">
                  <q-chip v-if="props.row.reportStatus === '1'" color="teal" text-color="white">
                    Report Available
                  </q-chip>
                  <q-chip v-else color="red" text-color="white" >
                    Unavilable Report
                  </q-chip>
                </q-td>
              </template>
              <template v-slot:body-cell-status="props">
                <q-td key="status" :props="props">
                  <q-chip v-if="props.row.status === '1'" color="green" text-color="white">
                    Active
                  </q-chip>
                  <q-chip v-else color="red" text-color="white" >
                    Inactive
                  </q-chip>
                </q-td>
              </template>
            </q-table>
          </div>

          <div class="col col-12 q-mt-sm">
            <span class="text-h6"> Category, Units, Types</span>

            <div class="row">
              <div
                v-for="(item, idx) in categoryList"
                :key="idx"
                class="col-3 col-xs-12 col-sm-3 col-md-3 q-pa-sm"
              >
                <q-card
                  flat
                  class="my-card-item"
                >
                  <q-card-section>
                    <q-avatar
                      size="md"
                      icon="category"
                    />
                    <br/>
                    <span class="text-bold text-blue-grey-9" >{{item.label}}</span>
                    <br/>
                  </q-card-section>
                </q-card>
              </div>
            </div>
          </div>
          
        </q-form>
      </q-card-section>
    </q-card>


    <!-- Modals -->
    <AddSeriesModal 
        v-bind="modalComponents" 
        @updateStatus="updateModalStatus"
        @updateTable="getSchedules"
    />
  </div>
</template>

<script>
import { LocalStorage } from 'quasar'
import moment from 'moment';
import jwt_decode from 'jwt-decode'
import { api } from 'boot/axios'
import AddSeriesModal from '../../Dashboard/Modal/AddSeriesModal.vue'


export default {
    name: 'AuctionList',
    components: {
      AddSeriesModal
    },
    data(){
      return {
        // Print
        openSellModal: false,
        openItem: null,
        price: 0,
        discount: 0,
        totalPrice: 0,
        officialReceipt: '',
        isReported: {label: 'Recorded', value: 0},

        isLoading: false,
        filter: '',
        rows: [],
        usersList:[],
        categoryList: [],
        setting:{
          loanReference: []
        },
        modalComponents: {
          modalStatus: false,
          appId: 0,
          userDetails: {},
          modalTitle: 'Add Ticket No. Reference to User'
        }

      }
    },
    computed: {
      user: function(){
          let profile = LocalStorage.getItem('userData');
          return jwt_decode(profile);
      },
      loanColumns: function(){
        return [
          {
            name: 'name',
            required: true,
            label: 'Name',
            align: 'left',
            field: row => row.userInfo,
            format: val => `${val.firstName} ${val.lastName}`,
            sortable: true
          },
          { name: 'start', label: 'Ticket No.', field: 'start' },
          { name: 'reportStatus', label: 'Report Privileges', field: 'reportStatus' },
          { name: 'status', label: 'Status', field: 'status' },
          { name: 'action',  field: 'action' }
        ]
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
      this.getSchedules().then(() => {
        this.getCategories()
      });

    },
    methods:{
      moment,
      updateModalStatus(){
        this.modalComponents.modalStatus = false;
      },
      addNewSeries(){
        this.modalComponents.modalStatus = true;
      },
      async updateReference(id, value){
        let payload = {
          sid: id,
          start: value
        }

        api.post('misc/update/reference', payload).then((response) => {
            const data = {...response.data};
            if(!data.error){
              this.getSchedules();
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
      async getSchedules(){
          let payload = {
            date: ""
          }

          api.post('misc/set/reference/list', payload).then((response) => {
              const data = {...response.data};
              if(!data.error){
                  this.setting.loanReference = data.list
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
      async getCategories(){
        api.get('misc/getCategories').then((response) => {
            const data = {...response.data};
            if(!data.error){
              this.categoryList = data.list
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
.modalItemBorder{
  border-radius: 10px;
  border: 1px solid grey;
}

.my-card-item{
  border-radius: 10px;
  box-shadow: 0px 0px 3px -2px !important;
}
</style>
