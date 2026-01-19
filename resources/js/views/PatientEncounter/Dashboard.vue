<template>
    <b-row id="encounterPage">
        <b-col md="3" >
            <b-row>
                <b-col sm="12" >
                    <b-card class="p-0 shadow " header-tag="header" footer-tag="footer">
                        <template v-slot:header>
                            <b-row>
                                <b-col sm="12" md="6" lg="12">
                                    <h3 class="encounter-title">{{$t('patient_encounter.encounter_details')}}</h3>
                                </b-col>
                            </b-row>
                        </template>
                        <div class="row patient-details">
                            <div class="col-md-12">
                                <div class="mb-0"><strong>{{$t('common.name')}} : </strong> {{ encounterData.patient_name }}</div>
                                <div class="mb-0"><strong class="font-weight-bold"> {{ $t('common.email')}} : </strong>{{ encounterData.patient_email }} </div>
                                <div class="mb-0"><strong class="font-weight-bold"> {{ $t('patient_encounter.encounter_date')}} : </strong>{{   singleDateFormat(encounterData.encounter_date) }}</div>
                                <div class="mb-0"><strong class="font-weight-bold"> {{ 'Address' }} : </strong>{{ encounterData.patient_address }}</div>
                                <div class="mb-0"><strong class="font-weight-bold"> {{ 'Phone' }} : </strong>{{ encounterData.contact_no }}</div>
                            </div>
                        </div>
                        <hr class="m-2 ml-0">
                        <div class="row clinic-details">
                            <div class="col-md-12">
                                <div class="mb-0"><strong class="font-weight-bold">{{$t('clinic.clinic_name')}} : </strong>{{ encounterData.clinic_name }}</div>
                                <div class="mb-0"><strong class="font-weight-bold">{{$t('doctor.doctor_name')}} : </strong>{{ encounterData.doctor_name }}</div>
                                <div class="mb-0"><strong class="font-weight-bold">{{$t('appointments.description')}} : </strong>{{ encounterData.description }}</div>
                                <div class="mb-0 mt-1">
                                <p class="mb-0 float-left">
                                  <span class="badge badge-success p-2" v-if="encounterData.status == 1" > {{$t('common.active')}}</span>
                                  <span class="badge badge-danger" v-if="encounterData.status == 0" > {{$t('common.closed')}}</span>
                                </p>
                              </div>
                            </div>
                        </div>
                        <ModalPopup
                        v-if="billModel"
                        modalId="bill-modal"
                        modalSize="lg"
                        :openModal="billModel"
                        :modalTitle="formTranslation.patient_bill.generate_invoice"
                        @closeModal="billModel = false"
                        >
                        <BillForm :encounterId="encounterId" :checkOutVal="checkOutVal" @onBillSaved="handleBillSave" @onBillCancel="handleBillCancel" :appointmentData="encounterData" :clinic_extra="encounterData.clinic_extra !== undefined ? encounterData.clinic_extra : {} " :doctorId="encounterData.doctor_id"/>
                      </ModalPopup>
                        <ModalPopup
                        v-if="billDetailsModel"
                        modalId="bill-details-modal"
                        modalSize="lg"
                        :openModal="billDetailsModel"
                        :modalTitle="formTranslation.patient_bill.invoice_detail"
                        @closeModal="billDetailsModel = false"
                        >
                        <BillDetails :encounterId="encounterId" :clinic_extra="encounterData.clinic_extra !== undefined ? encounterData.clinic_extra : {} " @onBillCancel="billDetailsModel = false;" />
                      </ModalPopup>
                    </b-card>
                </b-col>
            </b-row>
          <b-row v-if="encounterData.appointment_id
          && ((encounterData.appointment_report && encounterData.appointment_report.length > 0 ) || (encounterData.appointment_custom_field && encounterData.appointment_custom_field.length > 0))">
            <b-col sm="12" >
              <b-card class="p-0 shadow" header-tag="header" footer-tag="footer">
                <template v-slot:header>
                  <b-row>
                    <b-col sm="12" md="6" lg="12">
                      <h3> {{$t('patient_encounter.patient_extra_details')}}</h3>
                    </b-col>
                  </b-row>
                </template>
                <div class="row" v-if="encounterData.appointment_report && encounterData.appointment_report.length > 0">
                  <div class="col-md-12">
                    <div class="mb-0 text-center"><strong>{{$t('patient.medical_report')}} </strong></div>
                    <div v-for="(report, index) in encounterData.appointment_report" :key="index">
                      <div class="mt-2">
                        <a variant="outline-primary" v-b-tooltip.hover title="view report" :href="report.url" target="_blank">
                          <i class="fas fa-external-link-alt mr-1"></i>{{report.name}}
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
                <hr class="m-2 ml-0" v-if="encounterData.appointment_report && encounterData.appointment_report.length > 0">
                <div class="row" v-if="encounterData.appointment_custom_field && encounterData.appointment_custom_field.length > 0">
                  <div class="col-md-12">
                    <div class="mb-0 text-center"><strong>{{$t('patient_bill.other_info')}} </strong></div>
                    <div v-html="customFieldContent(encounterData.appointment_custom_field)"></div>
                  </div>
                </div>
                <hr class="m-2 ml-0 print-class d-none">
              </b-card>
            </b-col>
          </b-row>
        </b-col>
        <b-col md="9">
          <b-card class="p-0 shadow" header-tag="header" footer-tag="footer" header-class="sticky-header">
            <template v-slot:header >
              <b-row>
                <b-col sm="12" md="3" lg="3">
                  <h3 class="mb-0"> {{ kcCheckPermission('medical_records_list') && !hideClinicalDetailsToPatient ? $t('encounter_dashboard.title') : '' }} </h3>
                </b-col>
                <b-col sm="12" md="9" lg="9">
                  <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-sm btn-primary" @click="$router.go(-1);">
                      <i class="fa fa-angle-double-left"></i> {{$t('common.back')}}
                    </button>
                    <button v-if="userData.addOns.kiviPro == true" type="button" class="btn btn-sm btn-primary" @click="printEncounter">
                      <i class="fa fa-print"></i> {{ $t('patient_bill.print') + ' ' + $t('patient_encounter.encounters')}}
                    </button>
                     <!-- patient_id: encounterData.patient_id, -->
                    <router-link :to="{ name: 'patient-medical-report',params:{ encounter_id: encounterId  } }" class="btn btn-sm btn-primary" v-if="userData.addOns.kiviPro == true &&  kcCheckPermission('patient_report')">
                      <i :class="encounterData.status != 0 ? 'fa fa-upload' : 'fa fa-eye'"></i> {{ encounterData.status != 0 ? $t('patient.upload_report') : $t('common.view')+' '+$t('reports.reports')}}
                    </router-link>
                    <button class="btn btn-sm btn-outline-danger mr-0" v-if="kcCheckPermission('patient_bill_add') && encounterData.status != 0" @click="handleEncounterStatus('0')">
                      <i class="fa fa-check"></i> {{$t('patient_bill.encounter_close')}}
                    </button>
                    <button class="btn btn-sm btn-outline-danger ml-2 " v-if="kcCheckPermission('patient_bill_add') && encounterData.status != 0 && encounterData.appointment_id !== undefined && encounterData.appointment_id !== null" @click="handleEncounterStatus('1')">
                      <i class="fa fa-check"></i> {{$t('patient_bill.encounter_close_checkout')}}
                    </button>
                    <button class="btn btn-sm btn-primary" v-if="encounterData.status == 0 && checkEnableModule('billing')" @click="handleBillDetails">
                        <i class="fa fa-file-invoice"></i> {{$t('patient_bill.bill_details')}}
                    </button>
                  </div>
                </b-col>
              </b-row>
            </template>
            <div id="print_clinical_details" v-if="kcCheckPermission('medical_records_list') && !hideClinicalDetailsToPatient">
              <b-row>
                  <b-col  :md="getEnableCount" v-if="userData.addOns.kiviPro == true ? getEnableEncounter[0]['status'] == 1 : true" >
                      <MedicalHistory ref="medical_history_problems" :recordData="problems" :ecounterStatus="encounterData.status" />
                  </b-col>
                  <b-col  :md="getEnableCount" v-if="userData.addOns.kiviPro == true ? (getEnableEncounter[1]['status'] == 1 ) :true"  >
                      <MedicalHistory ref="medical_history_observation" :recordData="observation" :ecounterStatus="encounterData.status" />
                  </b-col>
                  <b-col :md="getEnableCount"  v-if="userData.addOns.kiviPro == true ? getEnableEncounter[2]['status'] == 1 : true" >
                      <MedicalHistory ref="medical_history_note"  :recordData="note" :ecounterStatus="encounterData.status"  />
                  </b-col>
              </b-row>
              <b-row v-if="extraClinicalData.length > 0">
                <b-col :md="4" v-for="(data, index) in extraClinicalData" :key="index" >
                  <MedicalHistory :ref="data.ref"  :recordData="data" :ecounterStatus="encounterData.status"  />
                </b-col>
              </b-row>
            </div>
            <b-row class="">
                <b-col md="12">
                    <Prescription v-if="showCustomField" ref="prescription_ref" :encounterData="{
                        encounter_id: encounterId,
                        status: encounterData.status,
                    }"
                    :encounterId="encounterId"
                    />
                </b-col>
                <b-col md="12" v-if="customFieldsLength > 0" :style="encounterData.status == 0 ?' pointer-events: none; cursor: default;' : ''">
                    <form id="encounterDataForm" @submit.prevent="handleSubmit" :novalidate="true" >
                        <div class="card shadow p-0">
                            <div class="card-header">
                                <h4 class="mb-0">{{$t('patient_bill.other_info')}}</h4>
                            </div>
                            <div class="card-body">
                                <edit-custom-fields 
                                    v-if="showCustomField"
                                    :key="componentKey"
                                    module_type="patient_encounter_module"
                                    @bindCustomField="getCustomFieldsValues"
                                    :module_id="customFieldEncounterId"
                                    :customFieldsObj="customFieldsObj"
                                    :fieldsValue="customFieldsData"
                                    @requiredCustomField="getRequireFields"
                                    :doctor_id="encounterData.doctor_id"
                                ></edit-custom-fields>
                            </div>
                            <div class="card-footer" v-if="encounterData.status === '1' && getUserRole() !== 'patient'">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="float-right">
                                            <button v-if="!loading" class="btn btn-primary" type="submit">
                                                <i class="fa fa-save"></i> {{ formTranslation.encounter_dashboard.presciption_save_btn }}
                                            </button>
                                            <button v-else class="btn btn-primary" type="submit" disabled>
                                                <i class="fa fa-sync fa-spin"></i> &nbsp; {{$t('common.loading')}}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </b-col>
            </b-row>
            <div class="print-class mr-3 d-none">
              <hr>
              <div class="row">
                <div class="col-12 d-flex justify-content-end align-items-center">
                  <p  class="nameheading">
                    <strong>{{ $t('common.doctor')+ ' ' + $t('common.signature') }}:</strong>
                  </p>
                  <div class="border-bottom col-4" v-if="encounterData.doctor_signature !== ''">
                    <img :src="encounterData.doctor_signature">
                  </div>
                  <div v-else class="border-bottom col-4">
                  </div>
                </div>
              </div>
            </div>
          </b-card>
        </b-col>
    </b-row>
</template>

<script>
import {post, get} from "../../config/request";
import MedicalHistory from "../../components/PatientEncounter/MedicalHistory";
import Prescription from "../../components/PatientEncounter/Prescription";
import {validateForm} from "../../config/helper";
import BillForm from "../../components/PatientBill/BillForm";
import BillDetails from "../../components/PatientBill/BillDetails";
import ModalPopup from "../../components/Modal/Index"

export default {
    components: {
        MedicalHistory,
        Prescription,
        BillForm,
        BillDetails,
        ModalPopup
    },
    data: () => {
        return {
            encounterId: 0,
            patient_id: 0,
            encounterData: {},
            patientBillData: {},
            createBillButton: true,
            billModel: false,
            billDetailsModel: false,
            loading: false,
            submitted: false,
            buttonText: '<i class="fa fa-save"></i> Save',
            customFieldsLength: 0,
            customFieldsObj: {},
            cardTitle: 'Clinical Detail',
            componentKey: 0,
            showCustomField:false,
            problems: {},
            observation: {},
            note: {},
            hideClinicalDetailsToPatient:false,
            checkOutVal: 0,
            extraClinicalData: []
        }
    },
    mounted() {
        this.encounterData = this.defaultEncounterDetails();
        this.init();
        this.hideClinicalDetailsToPatient = this.getUserRole() === 'patient' ? true : false;
    },
    methods: {
        init: function () {
            this.encounterId = this.$route.params.encounter_id
            this.problems = {
                encounter_id: this.encounterId,
                type: 'problem',
                title: this.formTranslation.encounter_dashboard.problems,
                status: this.encounterData.status,
                data:[]
            } ;

            this.observation = {
                encounter_id: this.encounterId,
                type: 'observation',
                title: this.formTranslation.encounter_dashboard.observation,
                status: this.encounterData.status,
                data:[]
            } ;

            this.note = {
                encounter_id: this.encounterId,
                type: 'note',
                title: this.formTranslation.encounter_dashboard.notes,
                status: this.encounterData.status,
                data:[]
            } ;

            if (this.$route.params.encounter_id !== undefined) {
                this.encounterId = this.$route.params.encounter_id
                get('patient_encounter_details', {
                    id: this.encounterId
                })
                .then((data) => {
                    if (data.data.status !== undefined && data.data.status === true) {
                        this.showCustomField = true;
                        this.encounterData = data.data.data
                        if(this.encounterData.clinic_extra !== undefined){
                          this.encounterData.clinic_extra = JSON.parse(this.encounterData.clinic_extra)
                        }
                        this.patient_id = this.encounterData.patient_id
                        this.getEncounterCustomField();
                        this.getEncounterBill();
                        this.hideClinicalDetailsToPatient = (data.data.hideInPatient == 'true' || data.data.hideInPatient == true) && this.getUserRole() === 'patient' ? true : false
                        if(!this.hideClinicalDetailsToPatient){
                          this.getMedicalRecords(this.encounterId)
                        }
                        //this.getExtraClinicalDetail(this.encounterId,this.encounterData.status);
                    }
                })
                .catch((error) => {
                    console.log(error);
                    displayErrorMessage(this.formTranslation.common.internal_server_error);
                })

            }

        },
      //future use
      getExtraClinicalDetail(encounterId,encounter_status){
        get('encounter_extra_clinical_detail_fields', {
          id: encounterId,
          status:encounter_status
        })
            .then((data) => {
              if (data.data.status !== undefined && data.data.status === true) {
                this.extraClinicalData = data.data.data
              }
              if(!this.hideClinicalDetailsToPatient){
                this.getMedicalRecords(this.encounterId)
              }
            })
            .catch((error) => {
              if(!this.hideClinicalDetailsToPatient){
                this.getMedicalRecords(this.encounterId)
              }
              console.log(error);
              displayErrorMessage(this.formTranslation.common.internal_server_error);
            })
      },
        forceRerender() {
          this.componentKey += 1;
        },
        defaultEncounterDetails: function () {
            return {
                patient_id: 0,
                patient_name: '',
                clinic_name: '',
                doctor_name: '',
                custom_fields: {}
            }
        },
        getEncounterCustomField: function () {
          get('get_custom_fields', {
            module_type: 'patient_encounter_module',
            module_id: this.encounterId,
            doctor_id:this.encounterData.doctor_id
          } )
            .then((response) => {
              if (response.data.status !== undefined && response.data.status === true) {
                this.customFieldsObj = response.data.data;
                this.customFieldsLength = response.data.data.length;
              } else {
                displayErrorMessage(response.data.message)
              }
            })
            .catch((error) => {
              console.log(error);
              displayErrorMessage(this.formTranslation.common.internal_server_error)
            })
        },
        getCustomFieldsValues: function (fieldsObj) {
          if(!fieldsObj || fieldsObj === undefined){
            return false;
          }
          this.encounterData.custom_fields = fieldsObj;
        },
        getRequireFields: function (validateRequired) {
            this.requiredFields = validateRequired;
        },
        getEncounterBill:  function () {
            if (parseInt(this.encounterId) !== 0) {
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
            }
        },
        handleSubmit: function () {
            if(this.getUserRole() == 'patient' ){ return; }
            this.loading = true;
            this.submitted = true;
            if (validateForm("encounterDataForm")) {
                post('save_custom_patient_encounter_field', this.encounterData)
                    .then((response) => {
                        this.loading = false;
                        if (response.data.status !== undefined && response.data.status === true) {
                            displayMessage(response.data.message);
                        } else {
                            displayErrorMessage(response.data.message)
                        }

                    })
                    .catch((error) => {
                        console.log(error);
                        this.loading = false;
                        displayErrorMessage(this.formTranslation.common.internal_server_error)
                    })
            }
        },
        handleEncounterStatus: function (status) {
            this.checkOutVal = status;
            if(this.isBillModuleActive) {
                this.handleGenerateBill();
                return ;
            } else {
                this.handleEncounterstatusUpdate({ id: this.encounterId, status: 0});
            }
        },
        handleEncounterstatusUpdate: function (requestData) {
            post('patient_encounter_update_status', { id: requestData.id, status: requestData.status, checkOutVal: this.checkOutVal })
            .then((response) => {
              if (response.data.status !== undefined && response.data.status === true) {
                  this.encounterData.status = requestData.status ;
                  displayMessage(response.data.message);
              } else {
                displayErrorMessage(response.data.message)
              }
            })
            .catch((error) => {
                console.log(error);
                displayErrorMessage(this.formTranslation.common.internal_server_error)
            })
        },
        handleGenerateBill: function () {
            this.billModel = true;
        },
        handleBillDetails: function () {
            this.billDetailsModel = true;
        },
        handleBillSave: function (data) {
            this.billModel = false;
            this.createBillButton = false;
            // encounter close
            this.encounterData.status = data.payment_status && data.payment_status === 'paid' ?  0 : 1 ;
            this.getEncounterBill();
        },
        handleBillCancel:function(type){
          this.billModel = false;
        },
        defaultBillRecordData: function () {
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
        getMedicalRecords: function (encounter_id) {
        get('medical_history_list', {encounter_id: encounter_id})
            .then((response) => {
                try{
                    if(this.$refs.medical_history_note !== undefined){
                      if(this.userData.addOns.kiviPro !== true){
                        this.$refs.medical_history_note.medicalHistoryListLoader = false;
                      }else if( this.getEnableEncounter[2]['status'] == 1){
                        this.$refs.medical_history_note.medicalHistoryListLoader = false;
                      }
                    }
                    if(this.$refs.medical_history_observation !== undefined){
                      if(this.userData.addOns.kiviPro !== true){
                        this.$refs.medical_history_observation.medicalHistoryListLoader = false;
                      }else if(this.getEnableEncounter[1]['status'] == 1){
                        this.$refs.medical_history_observation.medicalHistoryListLoader = false;
                      }
                    }
                    if(this.$refs.medical_history_problems !== undefined){
                      if(this.userData.addOns.kiviPro !== true){
                        this.$refs.medical_history_problems.medicalHistoryListLoader = false;
                      }else if(this.getEnableEncounter[0]['status'] == 1){
                        this.$refs.medical_history_problems.medicalHistoryListLoader = false;
                      }
                    }

                  this.extraClinicalData.map((index,key)=>{
                    this.$refs[index.ref][0].medicalHistoryListLoader = false;
                  });
                } catch (error){
                    console.log(error);
                }
              if (response.data.status !== undefined && response.data.status === true) {
                if(response.data.data.problem !== undefined && response.data.data.problem.length > 0 ){
                  if(this.userData.addOns.kiviPro !== true){
                    this.$refs.medical_history_problems.medicalHistoryList = response.data.data.problem;
                  }else if(this.getEnableEncounter[0]['status'] == 1){
                    this.$refs.medical_history_problems.medicalHistoryList = response.data.data.problem;
                  }
                }
                if(response.data.data.observation !== undefined && response.data.data.observation.length > 0 ){
                  if(this.userData.addOns.kiviPro !== true){
                    this.$refs.medical_history_observation.medicalHistoryList = response.data.data.observation;
                  }else if(this.getEnableEncounter[1]['status'] == 1){
                    this.$refs.medical_history_observation.medicalHistoryList = response.data.data.observation;
                  }
                }
                if(response.data.data.note !== undefined && response.data.data.note.length > 0 ){
                  if(this.userData.addOns.kiviPro !== true){
                    this.$refs.medical_history_note.medicalHistoryList = response.data.data.note;
                  }else if(this.getEnableEncounter[2]['status'] == 1){
                    this.$refs.medical_history_note.medicalHistoryList = response.data.data.note;
                  }
                }
                this.extraClinicalData.map((index,key)=>{
                  if(response.data.data[index.type] !== undefined && response.data.data[index.type].length > 0 ){
                    this.$refs[index.ref][0].medicalHistoryList = response.data.data[index.type];
                  }
                });
              }
            })
            .catch((error) => {
                try{
                    this.$refs.medical_history_note.medicalHistoryListLoader = false;
                    this.$refs.medical_history_observation.medicalHistoryListLoader = false;
                    this.$refs.medical_history_problems.medicalHistoryListLoader = false;
                } catch (error){
                    console.log(error);
                }
              console.log(error);
              displayErrorMessage(this.formTranslation.common.internal_server_error);
              ;
            })
      },
      customFieldContent(custom_fields){
        let customContent = ''
        custom_fields.forEach(function(key,index) {
          customContent += `<div class="mt-2"> <p class="mb-0"><span class="font-weight-bold">`
          customContent +=  key.label;
          customContent +=  `: </span>`;
          customContent +=  key.field_data !== null ? (Array.isArray(key.field_data) ? key.field_data.join(', ') : key.field_data) : ' ';
          customContent += `</p></div>`
        });
        return customContent
      },
      printEncounter(){
        let pageData = $('#encounterPage').clone();
        $(pageData).find('button').remove();
        $(pageData).find('.multiselect').remove();
        $(pageData).find('.clinical_details_notes').remove();
        $(pageData).find('.fa-eye').parent().remove();
        $(pageData).find('.medical-history-card li i').remove();
        let clinical_detail = '<div data-v-269365c0="" class="card-header">' +
            '<div data-v-269365c0="" class="row">' +
            '<div data-v-269365c0="" class="col-12">' +
            '<h3 data-v-269365c0="" class="mb-0">'+this.formTranslation.encounter_dashboard.title+'</h3>' +
            '</div> </div> <!----></div><div class="row">'
        $(pageData).find('.medical-history-card').map((key,value,row)=>{
          $(value).css('max-height','unset').css('min-height','unset')
          $(value).find('.list-group-item').css('border','unset')
          clinical_detail += '<div class="col-4 ">'
          clinical_detail += $(value).parent().clone().html()
          clinical_detail += '</div>'
        });
        clinical_detail += '</div>'
        $(pageData).find('#print_clinical_details').html(clinical_detail)
        $(pageData).find('.encounter-title').addClass('text-center text-primary').css('font-size','24px');
        $(pageData).find('.fa-upload').parent().remove();
        $(pageData).find('#historyDataForm .form-group').remove();
        let patient_details = $(pageData).find('.patient-details div').clone().html()
        let clinic_details = $(pageData).find('.clinic-details div').clone().html()
        $(pageData).find('.clinic-details div').remove()
        $(pageData).find('.patient-details div').remove()
        $(pageData).find('.patient-details').append(`<div class="col-6">`+patient_details+`</div>`)
        $(pageData).find('.patient-details').append(`<div class="col-6">`+clinic_details+`</div>`)
        $(pageData).find('.print-class').removeClass('d-none')
        $(pageData).append(`<style>@media print{@page {margin: 0;}.pagebreak { page-break-before: always; } .badge{border:unset} #encounterPage{padding:5px;}</style>`)
        $(pageData).printArea({});
      }
    },
    computed: {
        userData() {
            return this.$store.state.userDataModule.user;
        },
        customFieldEncounterId () {
            return this.$route.params.encounter_id ;
        },
        encounterDoctorId() {
          return  this.encounterData.doctor_id ;
        },
        customFieldsData() {
            return this.encounterData.custom_fields
        },
        isCustomeFieldExist () {
         return (this.encounterData.custom_fields !== undefined && this.encounterData.custom_fields.length > 0) ;
        },
        isBillModuleActive () {
            const module = this.$store.state.userDataModule.user.module.module_config.filter(thing => thing.name === 'billing' && thing.status === '1');
            if (module.length > 0) {
                return true ;
            } else {
                return false ;
            }
        },
        getEnableEncounter(){
            if(this.userData.encounter_enable_module !== undefined){
                return this.userData.encounter_enable_module
            }
        },
        getEnableCount(){
           if(this.userData.addOns.kiviPro == true){
               return this.userData.encounter_enable_count
           }else{
               return "4"
           }
        },
        // formTranslation: function () {
        //     return this.$store.state.staticDataModule.langTranslateData ;
        // }
    },
}
</script>
<style scoped>
.nameheading{
  margin: 0;
  font-weight: 500;
  font-size: 20px;
}
</style>

