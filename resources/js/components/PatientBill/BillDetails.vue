<template>
    <b-row>
        <b-col sm="12">
            <div id="bill">
                <div class="row">
                    <div class="col-md-6">
                        <h2 class="mb-0 ">{{ patientBillData.clinic !== undefined && patientBillData.clinic.name !== undefined ? patientBillData.clinic.name : '' }} </h2>
                        <h3 class="mb-0 font-weight-bold">{{$t('patient_bill.invoice_id')}} <span class="text-primary">#{{ patientBillData.id }}</span> </h3>
                        <h4 class="mb-0 "><span class="font-weight-bold"> {{$t('patient_bill.created_at')}} </span> {{ singleDateFormat(patientBillData.created_at) }}</h4>
                    </div>
                    <div class="col-md-6 text-right">
                        <p class="mb-0 ">{{ patientBillData.clinic.address }}, {{ patientBillData.clinic.city }}, {{ patientBillData.clinic.postal_code }}</p>
                        <p class="mb-0 ">{{ patientBillData.clinic.email }}</p>
                        <p class="mb-0 mt-2">
                            {{$t('patient_bill.payment_status')}}
                            <span class="badge badge-success" v-if="patientBillData.payment_status == 'paid'">{{$t('patient_bill.paid')}}</span>
                            <span class="badge badge-danger" v-if="patientBillData.payment_status == 'unpaid'">{{$t('patient_bill.unpaid')}}</span>
                        </p>
                    </div>
                </div>
                <hr class="my-3">
                <div class="row">
                    <div class="col-md-12">
                        <h3>{{$t('patient_bill.patient_details')}}</h3>
                    </div>
                    <div class="col-md-12">
                        <table class="table table-sm custom-table">
                            <thead class="thead-light">
                            <tr>
                                <th>{{$t('patient.patient_name')}}</th>
                                <th>{{$t('common.gender')}}</th>
                                <th>{{$t('common.dob')}}</th>
                            </tr>
                            </thead>
                            <tbody class="text-capitalize">
                            <tr>
                                <td>{{ patientBillData.patient.display_name }}</td>
                                <td>{{ patientBillData.patient.gender }}</td>
                                <td>{{  singleDateFormat(patientBillData.patient.dob) }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <hr class="my-3">
                <div class="row">
                    <div class="col-md-12">
                        <h3>{{$t('common.services')}}</h3>
                    </div>
                    <div class="col-md-12">
                        <table class="table custom-table">
                            <thead class="thead-light">
                            <tr>
                                <th>{{$t('common.sr_no')}}</th>
                                <th>{{$t('common.item_name')}}</th>
                                <th>{{$t('common.price')}}</th>
                                <th>{{$t('common.quantity')}}</th>
                                <th>{{$t('common.total')}}</th>
                            </tr>
                            </thead>
                            <tbody v-if="patientBillData.billItems !== undefined && patientBillData.billItems.length > 0">
                            <tr v-for="(billing_item, index) in patientBillData.billItems" :key="index">
                                <td>{{ ++index }}</td>
                                <td>{{ billing_item.item_id.label }}</td>
                                <td>{{clinic_currency_prefix}}{{ billing_item.price }}{{clinic_currency_postfix}}</td>
                                <td>{{ billing_item.qty }}</td>
                                <td>{{clinic_currency_prefix}}{{ billing_item.price * billing_item.qty }}{{clinic_currency_postfix}}</td>
                            </tr>
                            </tbody>
                            <tbody v-else>
                            <tr>
                                <td colspan="6"><h4 class="text-primary mb-0">{{$t('common.no_records_found')}}</h4></td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th colspan="4" class="text-right">{{$t('common.total')}}</th>
                                <th>{{clinic_currency_prefix}}{{ patientBillData.total_amount }}{{clinic_currency_postfix}}</th>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-right">{{$t('patient_bill.discount')}}</th>
                                <th >{{clinic_currency_prefix}}{{ patientBillData.discount }}{{clinic_currency_postfix}}</th>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-right">{{$t('patient_bill.amount_due')}}</th>
                                <th >{{clinic_currency_prefix}}{{ patientBillData.actual_amount }}{{clinic_currency_postfix}}</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end align-items-center">
<!--              <button  class="btn btn-sm btn-primary  mr-2"  @click="paymentLink" type="button">-->
<!--                <i  class="fas fa-paper-plane"></i>-->
<!--                {{ $t('patient_bill.payment_link' ) }}-->
<!--              </button>-->
            <button  class="btn btn-sm btn-primary" id="billPrint" v-b-tooltip.hover :title="formTranslation.patient.print" @click="printData()"  v-if="userData.addOns.kiviPro == true " >
                <i class="fa fa-print"></i>{{$t('patient_bill.print')}}
            </button>
              <button class="btn btn-sm btn-primary" v-b-tooltip.hover :title="formTranslation.common.cancel" @click="$emit('onBillCancel')">
                <i class="fa fa-times"></i> {{$t('common.cancel')}}</button>
            </div>
            <div id="editor"></div>
        </b-col>
    </b-row>
</template>

<script>
import {post,get} from "../../config/request";
export default {
    data: () => {
        return {
            services: [],
            patientBillData: {
                clinic:{
                    address:'',
                    city:'',
                    postal_code:''
                },
                patient:{
                    display_name:'',
                    gender:'',
                    dobL:new Date(),
                },
                total_amount:'',
                discount:'',
                actual_amount:''
            },
            woocommerceActive:'off',
            clinic_currency_prefix:'',
            clinic_currency_postfix:'',
        }
    },
    mounted() {
        this.init();
        if(this.clinic_extra.currency_prefix !== undefined && this.clinic_extra.currency_prefix != null){
            this.clinic_currency_prefix = this.clinic_extra.currency_prefix;
        }else if(this.clinic_extra.prefix !== undefined && this.clinic_extra.prefix != null){
            this.clinic_currency_prefix = this.clinic_extra.prefix;
        }else{
            this.clinic_currency_prefix = '';
        }

        if(this.clinic_extra.currency_postfix !== undefined && this.clinic_extra.currency_postfix != null){
            this.clinic_currency_postfix = this.clinic_extra.currency_postfix;
        }else if(this.clinic_extra.postfix !== undefined && this.clinic_extra.postfix != null){
            this.clinic_currency_postfix = this.clinic_extra.postfix;
        }else{
            this.clinic_currency_postfix = '';
        }
    },
    props: {
        encounterId : {
            type: [ String, Number ],
            default() {
                return 0
            }
        },
       clinic_extra:{
         type: [Object, Array,String],
       },
    },
    methods: {
        init: function () {
              get('patient_bill_detail', {
                encounter_id: this.encounterId
            })
                .then((response) => {
                    if (response.data.status !== undefined && response.data.status === true) {
                        if (response.data.data.length === 0) {
                            this.createBillButton = true;
                        } else {
                            this.createBillButton = false;
                            this.patientBillData = response.data.data
                        }
                    }
                })
                .catch((error) => {
                    console.log(error);
                    displayErrorMessage(this.formTranslation.widgets.record_not_found);
                })
        },
        printData(){
          let but = $('#billPrint');
          but.prop('disabled', true);
          but.html(`<i class='fa fa-sync fa-spin'> </i>`);
          get('print_encounter_bill_detail', { id: this.encounterId,data:this.patientBillData } )
              .then((response) => {
                if (response.data.status !== undefined && response.data.status === true) {
                  $(response.data.data).printArea({});
                }
                setTimeout(() => {
                  but.prop('disabled', false);
                  but.html(`<i class='fa fa-print'> </i>`+this.formTranslation.patient_bill.print);
                }, 1000)
              }).catch((error) => {
            but.prop('disabled', false);
            but.html(`<i class='fa fa-print'> </i>`+this.formTranslation.patient_bill.print);
            console.log(error);
            displayErrorMessage(this.formTranslation.common.internal_server_error);
          })
        },
        checkWoocommerceActive(){
        get('get_payment_status', { status: '' } )
            .then((response) => {
              this.woocommerceActive = response.data.data ;
            })
            .catch((error) => {
              console.log('error', error);
            })
      },
        paymentLink(){

        if( this.woocommerceActive == 'off'){
          displayErrorMessage(this.formTranslation.patient_bill.payment_or_bill_item_error)
          return
        }
        post('send_payment_link', {id:this.encounterId})
            .then((response) => {
              displayMessage(response.data.message)
            })
            .catch((error) => {
              console.log('error', error);
            })
      }
 
    },
    computed: {
        userData() {
            return this.$store.state.userDataModule.user;
        },
      }
}
</script>

<style scoped>
 @media print {
   @page {
     margin: 10px;
   }
 }
</style>