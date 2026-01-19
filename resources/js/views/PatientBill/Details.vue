<template>
    <b-row>
        <b-col sm="12">
            <b-card class="p-0 shadow" header-tag="header" footer-tag="footer">
                <template v-slot:header>
                    <b-row>
                        <b-col sm="12" md="8" lg="8">
                            <h3 class="mb-0">{{ cardTitle }}</h3>
                        </b-col>
                        <b-col sm="12" md="4" lg="4" v-if="kcCheckPermission('patient_bill_list')">
                            <div class="float-right">
                                <router-link class="btn btn-sm btn-primary" :to="{ name: 'patient-bill' }">
                                    <i class="fa fa-angle-double-left"></i> {{$t('common.back')}}
                                </router-link>
                            </div>
                        </b-col>
                    </b-row>
                </template>

                <div id="bill">
                    <div class="row">
                        <div class="col-md-6">
                            <h2 class="mb-0 ">{{ patientBillData.clinic.name }}</h2>
                            <h3 class="mb-0 font-weight-bold">{{$t('patient_bill.invoice_id')}} : <span class="text-primary">#{{ patientBillData.id }}</span> </h3>
                            <h4 class="mb-0 "><span class="font-weight-bold"> {{$t('patient_bill.created_at')}} : </span> {{    singleDateFormat(patientBillData.created_at) }}</h4>
                        </div>
                        <div class="col-md-6 text-right">
                            <p class="mb-0 ">{{ patientBillData.clinic.address }}, {{ patientBillData.clinic.city }}, {{ patientBillData.clinic.postal_code }}</p>
                            <p class="mb-0 ">{{ patientBillData.clinic.email }}</p>
                            <p class="mb-0 mt-2">
                                {{$t('patient_bill.payment_status')}} :
                                <span class="badge badge-success" v-if="patientBillData.status === '1'">{{$t('patient_bill.paid')}}</span>
                                <span class="badge badge-danger" v-if="patientBillData.status === '0'">{{$t('patient_bill.unpaid')}}</span>
                            </p>
                        </div>
                    </div>
                    <hr class="my-3">
                    <div class="row">
                        <div class="col-md-12">
                            <h3>{{$t('patient_bill.patient_details')}}</h3>
                        </div>
                        <div class="col-md-12">
                            <div class="table-responsive mb-0">
                                <table class="table table-sm">
                                    <thead class="thead-light">
                                    <tr>
                                        <th>{{$t('patient.patient_name')}}</th>
                                        <th>{{$t('common.gender')}}</th>
                                        <th>{{$t('common.dob')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <!-- <tr>
                                        <td>Jay Parmar</td>
                                        <td>Male</td>
                                        <td>31 July 1996</td>
                                    </tr> -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">
                    <div class="row">
                        <div class="col-md-12">
                            <h3>{{$t('patient_bill.items')}}</h3>
                        </div>
                        <div class="col-md-12">
                            <table class="table">
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
                                    <td>{{ billing_item.price }}</td>
                                    <td>{{ billing_item.qty }}</td>
                                    <td>{{ billing_item.price * billing_item.qty }}</td>
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
                                    <th>{{ patientBillData.total_amount }}</th>
                                </tr>
                                <tr>
                                    <th colspan="4" class="text-right">{{$t('patient_bill.discount')}}</th>
                                    <th >{{ patientBillData.discount }}</th>
                                </tr>
                                <tr>
                                    <th colspan="4" class="text-right">{{$t('patient_bill.amount_due')}}</th>
                                    <th >{{ patientBillData.actual_amount }}</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="editor"></div>

                <template v-slot:footer>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="float-right">
                                <button class="btn btn-primary" type="button" @click="printBill">
                                    <i class="fa fa-save"></i> {{$t('patient_bill.print_bill')}}
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </b-card>
        </b-col>
    </b-row>
</template>

<script>
import { post, get } from "../../config/request";

export default {
    data: () => {
        return {
            cardTitle: 'Bill details',
            encounter_id: 0,
            patientBillData: {},
            services: [],
            billItem: {},
        }
    },
    mounted() {
        this.cardTitle = this.formTranslation.patient_bill.bill_details;
        this.patientBillData = this.defaultMedicalRecordData();
        this.billing_item = this.defaultBillingItemData();
        this.init();
    },
    methods: {
        init: function () {

            if (this.$route.params.encounter_id !== undefined) {
                this.patientBillData.encounter_id = this.$route.params.encounter_id;
            }

            /// Code for the Edit functionality...
            if (this.$route.params.id !== undefined) {

                get('patient_bill_detail', {
                    id: this.$route.params.id
                })
                    .then((response) => {
                        if (response.data.status !== undefined && response.data.status === true) {
                            this.patientBillData = response.data.data
                        }
                    })
                    .catch((error) => {
                        console.log(error);
                        displayErrorMessage(this.formTranslation.widgets.record_not_found);
                    })
            }

        },
        defaultMedicalRecordData: function () {
            return {
                title: '',
                encounter_id: 0,
                total_amount: 0,
                discount: 0,
                actual_amount: '',
                status: 0,
                billItems: []
            }
        },
        defaultBillingItemData: function () {
            return {
                item_id: {},
                qty: 1,
                price: 0,
                total: this.billItem.price * this.billItem.qty
            }
        },
        printBill: function () {

            var divToPrint=document.getElementById('bill');

            var newWin=window.open('','Print-Window');

            newWin.document.open();

            newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');

            newWin.document.close();

            setTimeout(function(){newWin.close();},10);
        }
    }
}
</script>