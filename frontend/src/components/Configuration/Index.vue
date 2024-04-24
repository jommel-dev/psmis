<template>
    <div class="row">
        <div class="col-12 col-xs-12 col-sm-12 col-md-3 q-pa-xl">
            <span class="text-h6 text-bold">Settings</span><br/>
            <span class="text-caption text-grey">System configuration and settings for transactions and appearance</span><br/>
            <br />
            <q-list >
                <settingsMenu
                    v-for="link in linksList"
                    :key="link.title"
                    v-bind="link"
                    :selectedMenu="selected"
                    @menuClicked="setMenuClicked"
                />
            </q-list>
        </div>
        <div class="col-12 col-xs-12 col-sm-12 col-md-9 q-pa-sm">
            <q-card
                flat
                class="my-card bg-white"
            >
                <q-card-section>
                    <q-tab-panels vertical v-model="selected" animated>
                        <q-tab-panel v-for="(el) in linksList" :key="el.link" :name="el.link">
                            <div class="text-h6">{{el.title}}</div>
                            <span class="text-caption text-grey">{{el.description}}</span>
                            <q-separator /><br/>
                            <component :is="el.component" />
                        </q-tab-panel>
                    </q-tab-panels>
                </q-card-section>
            </q-card>
            
        </div>
    </div>
</template>

<script>
import moment from 'moment';
import { LocalStorage, exportFile } from 'quasar'
import noData from '../Templates/NoData.vue';
import jwt_decode from 'jwt-decode'
import settingsMenu from './SettingsMenu.vue';
import loanCustomer from './TabPanels/loanCustomer.vue'
import appSettings from './TabPanels/appSettings.vue'
import userRoles from './TabPanels/userRoles.vue'
import printSettings from './TabPanels/printSettings.vue'

const linksList = [
    {
        title: 'Application Settings',
        description: 'Lorem ipsum dolor sit amet consectetur adipisicing elit.',
        icon: 'settings_applications',
        link: 'setting',
        component: appSettings
    },
    {
        title: 'Loan & Customer Settings',
        description: 'Configure the Loan Ticket No., Customer Number, and Record Base System',
        icon: 'manage_accounts',
        link: 'loanAndCustomer',
        component: loanCustomer
    },
    {
        title: 'User Roles',
        description: 'Lorem ipsum dolor sit amet consectetur adipisicing elit.',
        icon: 'widgets',
        link: 'userRoles',
        component: userRoles
    },
    {
        title: 'Print Settings',
        description: 'Cofigure the Print Preview Settings of the Loan Ticket',
        icon: 'print',
        link: 'printSettings',
        component: printSettings
    },
]

export default {
    name: 'ReportGenerator',
    data(){
        return {
            linksList,
            rowResult: [],
            rowColumns: [],

            rowSchema: [],
            dataReport: [],

            reportShowResult: false,
            dateSelected: {
                from: "",
                to: ""
            },
            formFilter: {
                status: null,
                type: null
            },
            selected: "setting",


        }
    },
    components: {
        noData,
        settingsMenu
    },
    methods: {
        moment,
        setMenuClicked(val){
            this.selected = val
        },
        checkTypeOf(value){
            return typeof value
        },
        generateItemList(list){
          let res = [];
          list.forEach(el => {
            let str = `${el.qty} ${el.unit.value} ${el.type.value}, ${el.description || 'N/A'}, ${el.weight}/${el.property} with ${el.remarks || ''}`;
            res.push(str);
          });

          return res.join(", ")
        },
        wrapCsvValue (val, formatFn, row) {
            let formatted = formatFn !== void 0
                ? formatFn(val, row)
                : val

            formatted = formatted === void 0 || formatted === null
                ? ''
                : String(formatted)

            formatted = formatted.split('"').join('""')
            /**
             * Excel accepts \n and \r in strings, but some other CSV parsers do not
             * Uncomment the next two lines to escape new lines
             */
            // .split('\n').join('\\n')
            // .split('\r').join('\\r')

            return `"${formatted}"`
        },
        async generateReport(){
            this.rowColumns
            this.rowResult
            // naive encoding to csv format
            const content = [this.rowColumns.map(col => this.wrapCsvValue(col.label))].concat(
            this.rowResult.map(row => this.rowColumns.map(col => this.wrapCsvValue(
                typeof col.field === 'function'
                ? col.field(row)
                : row[ col.field === void 0 ? col.name : col.field ],
                col.format,
                row
            )).join(','))
            ).join('\r\n')

            // Generate Filename
            let filename = "";
            if(this.dateSelected.from === this.dateSelected.to){
                filename = `${this.selected}_${this.dateSelected.from}`
            } else {
                filename = `${this.selected}_${this.dateSelected.from}_${this.dateSelected.to}`
            }

            // export report
            exportFile(
                filename,
                content,
                'text/csv'
            )
        }
    },
    computed: {
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return jwt_decode(profile);
        },
    }
}
</script>
<style scoped>
.my-card{
    border-radius: 6px;
    box-shadow: 0px 0px 3px -2px !important;
}
</style>
