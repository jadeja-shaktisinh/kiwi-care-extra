<template>
    <b-row v-if="userData.addOns.kiviPro == true ? getEnablePrescription[0]['status'] == 1 : true">
        <b-col sm="12">
            <div class="card p-0 shadow">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-4">
                            <h3 class="mb-0">{{$t('patient_encounter.prescription')}}</h3>
                        </div>
                        <div class="col-md-8">
                          <div class="d-flex justify-content-end align-items-center">
                            <button id="send_prescription" v-if="prescriptionList.length > 0" @click="mailPrescriptionAdd"  class="btn btn-sm btn-primary"  type="button">
                              <i class="fas fa-paper-plane"></i> {{ formTranslation.clinic.dt_lbl_email }}
                            </button>
                            <button v-if="userData.addOns.kiviPro && kivicareCompareVersion(requireProVersion,userData.pro_version) && (getUserRole() !== 'patient') && encounterStatus === '1'" class="btn btn-sm btn-primary"  @click="$refs.module_data_import.openModel = true" >
                                    <i class="fas fa-file-import"></i>{{$t('common.import_data')}}
                                </button>
                            <button v-if="encounterData.status != 0 && kcCheckPermission('prescription_add')" @click="handlePrescriptionAdd" class="btn btn-sm btn-primary" :to="{ name: 'prescription_add' }">
                              <i v-if="!showAddForm" class="fa fa-plus"></i> <i v-else class="fa fa-minus"></i> {{ ( showAddForm ? formTranslation.common.close_form_btn : formTranslation.encounter_dashboard.add_prescription_btn ) }}
                            </button>
                            <module-data-export :module_data="exportPrescription" :module_name="formTranslation.patient_encounter.prescription"  module_type="prescription"> </module-data-export>
                            <button v-b-tooltip.hover :title="formTranslation.widget_setting.print" class="btn  btn-sm pr-0 pl-2 ml-1"  v-if="userData.addOns.kiviPro == true && prescriptionList.length > 0 && encounterData.status == 0" @click.prevent="printPrescription" :disabled="printPrescriptionLoader"> <i :class="printPrescriptionLoader ? 'fa fa-sync fa-spin':  'fa fa-print'"></i></button>
                          </div>
                        </div>
                    </div>
                    <module-data-import :encounter_id="encounterData.encounter_id" v-if="userData.addOns.kiviPro" ref="module_data_import" @reloadList="getPrescriptionRecords"
                        :required_data="[
                        {label:formTranslation.common.prescription_name,value:'name'},
                        {label:formTranslation.common.prescription_frequency,value:'frequency'},
                        {label:formTranslation.common.prescription_duration,value:'duration'},
                    ]" :module_name="formTranslation.common.prescription" module_type="prescription"></module-data-import>
                  
                  <ModalPopup
                  v-if="showAddForm"
                  modalId="appointment-details-modal"
                  modalSize="lg"
                  :openModal="showAddForm"
                  :modalTitle="prescriptionData.id ? formTranslation.patient_encounter.edit_prescription : formTranslation.patient_encounter.add_prescription"
                  @closeModal="showAddForm = false"
                  >
                  <div class="m-4" v-if="kcCheckPermission('prescription_add') && encounterStatus === '1'">
                        <form id="prescriptionDataForm" @submit.prevent="handleSubmit" :novalidate="true">
                          <div class="row">
                            <div class="col-6">
                              <div class="form-group">
                                <label for="prescription" class="form-control-label">{{$t('common.name')}} <span class="text-danger">*</span></label>
                                <multi-select
                                    deselect-label=""
                                    select-label=""
                                    id="prescription"
                                    v-model="prescriptionData.name"
                                    :tag-placeholder="formTranslation.patient_encounter.tag_name_plh"
                                    :placeholder="formTranslation.patient_encounter.prescriptions_plh"
                                    label="label"
                                    @tag="addPrescriptionOption"
                                    track-by="id"
                                    :taggable="true"
                                    :options="prescriptionNames"
                                ></multi-select>
                                <span class="text-primary small"><b>{{$t('patient_encounter.note_prescription')}}</b></span>
                                <div v-if="submitted && !$v.prescriptionData.name.required"
                                     class="invalid-feedback">
                                  {{$t('common.name_required')}}
                                </div>
                              </div>
                            </div>
                            <div class="col-6">
                              <div class="form-group">
                                <label for="frequency" class="form-control-label">
                                  {{$t('patient_encounter.frequency')}} <span class="text-danger">*</span>
                                </label>
                                <input id="frequency" type="text" class="form-control" v-model="prescriptionData.frequency" :placeholder="formTranslation.patient_encounter.frequency_plh">
                                <div v-if="submitted && !$v.prescriptionData.frequency.required"
                                     class="invalid-feedback">
                                  {{$t('patient_encounter.frequency_required')}}
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-6">
                              <div class="form-group">
                                <label for="duration" class="form-control-label">
                                  {{$t('patient_encounter.duration_Days')}} <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="duration" v-model="prescriptionData.duration" id="duration"
                                       class="form-control"/>
                                <div v-if="submitted && !$v.prescriptionData.duration.required"
                                     class="invalid-feedback">
                                  {{$t('patient_encounter.duration_required')}}
                                </div>
                              </div>
                            </div>
                            <div class="col-6">
                              <div class="form-group">
                                <label for="instruction" class="form-control-label">
                                  {{$t('patient_encounter.instruction')}}
                                </label>
                                <textarea name="instruction" v-model="prescriptionData.instruction"
                                          :placeholder="formTranslation.patient_encounter.enter_instruction_plh" class="form-control"
                                          id="instruction">
                                            </textarea>
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-12">
                              <div class="float-right">
                                <button v-if="!loading" class="btn btn-primary" type="submit" >
                                  <i class="fa fa-plus"></i> {{ formTranslation.encounter_dashboard.presciption_save_btn }}
                                </button>
                                <button v-else class="btn btn-primary" type="button" disabled>
                                  <i class="fa fa-sync fa-spin"></i>&nbsp; {{$t('common.loading')}}
                                </button>
                                <button type="button" class="btn btn-outline-primary" @click="close()"> {{$t('common.cancel')}}</button>
                              </div>
                            </div>
                          </div>
                        </form>
                      </div>
                </ModalPopup>
                </div>
                <div class="card-body">
                    <div class="row text-uppercase mb-3">
                        <div class="col-5 text-left">
                            <h5 class="mb-0">{{$t('common.name')}}</h5>
                        </div>
                        <div class="col-3">
                            <h5 class="mb-0">{{$t('patient_encounter.frequency')}}</h5>
                        </div>
                        <div class="col-2">
                            <h5 class="mb-0">{{$t('patient_encounter.duration')}}</h5>
                        </div>
                        <div class="col-2" v-if="getUserRole() !== 'patient'">
                            <h5 class="mb-0">{{$t('common.action')}}</h5>
                        </div>
                    </div>
                    <div v-if="prescriptionList.length > 0">
                      <div class="row animated fadeInUp mb-3" v-for="(prescription, index) in prescriptionList" v-if="prescription !== undefined" :key="index">
                            <div class="col-5">
                                <h3 class="text-primary mb-0">{{ prescription.name.label }}</h3>
                                <p class="mb-0 small">{{ prescription.instruction }}</p>
                            </div>
                            <div class="col-3">
                                <h5 class="mb-0">{{ prescription.frequency }}</h5>
                            </div>
                            <div class="col-2">
                                <h5 class="mb-0">{{ prescription.duration + ' Day'}}</h5>
                            </div>
                            <div class="col-2 " v-if="getUserRole() !== 'patient'">
                                <div  v-if="encounterStatus === '1'" >
                                    <b-button-group size="sm">
                                        <b-button variant="outline-primary"  @click="handlePrescriptionEdit(prescription,prescription.id)" v-b-tooltip.hover :title="formTranslation.common.edit">
                                            <i class="fa fa-edit"></i>
                                        </b-button>
                                        <b-button variant="outline-danger" v-b-tooltip.hover :title="formTranslation.clinic_schedule.dt_lbl_dlt" @click="deletePrescriptionData(index)">
                                            <i class="fa fa-trash"></i>
                                        </b-button>
                                    </b-button-group>
                                </div>
                                <span v-else> - </span>
                            </div>
                        </div>
                    </div>
                    <div v-else-if="prescriptionLoader" class="h2 font-weight-bold mb-0 text-center">
                      <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
                    </div>
                    <div v-else>
                      <div class="row">
                            <div class="col-md-12">
                                <h4 class="text-danger text-center my-3">{{$t('patient_encounter.no_prescription_found')}}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </b-col>
    </b-row>
</template>


<script>

import {post,get} from "../../config/request";
import {validateForm} from "../../config/helper";
import {required} from "vuelidate/lib/validators";
import ModalPopup from '../Modal/Index'
export default {
    props: {
        encounterId : {
              type: [ String, Number ],
              default() {
                  return 0
              }
          },
        encounterData: {
            type: [Object, Array],
            default() {
                return {}
            }
        }
    },
    validations: {
        prescriptionData: {
            name: {required},
            frequency: {required},
            duration: {required},
        },
    },
    data: () => {
        return {
            showAddForm:false,
            prescriptionList: [],
            prescriptionData: {},
            durations: [],
            formTitle: 'Add prescription',
            loading: false,
            submitted: false,
            componentKey: 0,
            prescriptionLoader:true,
            prescriptionNames:[],
            printPrescriptionLoader:false,
            exportPrescription:[]

        }
    },
    mounted() {
        this.init();
    },
    components:{
      ModalPopup
    },
    methods: {
        init: function () {
            this.durations = this.getDurations()
            this.prescriptionData = this.defaultFormData()
            setTimeout(() => {
                this.getPrescriptionRecords()
            },200)
        },
        forceRerender() {
          this.componentKey += 1;
        },
        defaultFormData: function () {
            return {
                encounter_id: this.encounterData.encounter_id,
                name: '',
                frequency: '',
                duration: '',
                instruction: '',
            }
        },
        getDurations: function () {
            var day = 1;
            var durations = [];
            do {
                durations.push(day + ' Days');
                if (day < 5) {
                    day++
                } else {
                    day = day + 5
                }
            }
            while (day <= 60);

            return durations;
        },
        getPrescriptionRecords: function () {
            if (this.encounterData.encounter_id !== undefined) {
                get('prescription_list', this.encounterData )
                    .then((data) => {
                      this.prescriptionNames = data.data.prescriptionNames;
                      this.prescriptionLoader = false;
                      if (data.data.status !== undefined && data.data.status === true) {
                            if(data.data.data !== undefined && data.data.data.length > 0){
                              this.prescriptionList = data.data.data;
                            }
                        }
                    })
                    .catch((error) => {
                        this.prescriptionLoader = false;
                        console.log(error);
                        displayErrorMessage(this.formTranslation.common.internal_server_error);;
                    })
            }
        },
        handleSubmit: function () {
            this.loading = true;
            this.submitted = true;

            // stop here if form is invalid
            this.$v.$touch();
            if (this.$v.prescriptionData.$invalid) {
                this.loading = false;
                return;
            }

            if (validateForm("prescriptionDataForm")) {
                post('prescription_save', this.prescriptionData)
                    .then((response) => {
                        this.loading = false;
                        if (response.data.status !== undefined && response.data.status === true) {
                            displayMessage(response.data.message);
                              this.showAddForm = false;
                            if (this.prescriptionData.id === undefined) {
                                this.prescriptionList.push(response.data.data)
                            }
                            this.prescriptionData = this.defaultFormData()
                            this.submitted = false;
                            this.prescriptionData.encounter_id = this.encounterData.encounter_id;
                            this.forceRerender()
                        } else {
                            displayErrorMessage(response.data.message)
                        }

                    })
                    .catch((error) => {
                        console.log(error);
                        this.loading = false;
                        displayErrorMessage(this.formTranslation.common.internal_server_error);
                    })
            }
        },
      mailPrescriptionAdd:function (){
          if(this.prescriptionData.encounter_id !== undefined) {
            var element =$('#send_prescription').find("i");
            element.removeClass('fa fa-paper-plane');
            element.addClass("fa fa-spinner fa-spin");
            get('prescription_mail', this.prescriptionData)
              .then((response) => {
                element.removeClass("fa fa-spinner fa-spin");
                element.addClass("fa fa-paper-plane")
                if (response.data.status !== undefined && response.data.status === true) {
                  displayMessage(response.data.message);
                } else {
                  displayErrorMessage(response.data.message)
                }
              })
              .catch((error) => {
                console.log(error);
                displayErrorMessage(this.formTranslation.common.internal_server_error);
              })
          }
       },
        close(){
          this.showAddForm = false
          this.prescriptionData = this.defaultFormData()
        },
        handlePrescriptionAdd: function (){
          this.showAddForm = !this.showAddForm
          this.prescriptionData = this.defaultFormData()
        },
        handlePrescriptionEdit: function (prescription,collapseID){
            this.showAddForm = true
            this.prescriptionData = prescription;
        },
        deletePrescriptionData: function (index) {
            if (this.prescriptionList[index] !== undefined) {
                $.confirm({
                    title: this.formTranslation.clinic_schedule.dt_are_you_sure,
                    content: this.formTranslation.common.py_delete_prescription,
                    type: 'red',
                    buttons: {
                        ok: {
                            text: this.formTranslation.common.yes,
                            btnClass: 'btn-danger',
                            keys: ['enter'],
                            action: () => {
                                get('prescription_delete', {
                                    id: this.prescriptionList[index].id
                                })
                                    .then((data) => {
                                        if (data.data.status !== undefined && data.data.status === true) {
                                            this.prescriptionList.splice((index), 1);
                                            displayMessage(data.data.message);
                                        }
                                    })
                                    .catch((error) => {

                                        if (error.response.data !== undefined && error.response.data.message !== undefined) {
                                            displayErrorMessage(error.response.data.message);
                                        } else {
                                            displayErrorMessage(this.formTranslation.common.internal_server_error);;
                                        }
                                    })
                            }
                        },
                        cancel: {
                          text:this.formTranslation.common.cancel
                        }
                    }
                });
            }
        },
        addPrescriptionOption: function (newTag) {
            const tag = {
                id: newTag,
                label: newTag
            }
          let tagObj = {
            label: newTag,
            type: 'prescription_medicine',
            value: newTag.replace(' ', '_'),
            status: 1,
          }
          let _this = this;
          post('static_data_save', tagObj)
              .then((response) => {
                if (response.data.status !== undefined && response.data.status === true) {
                  _this.prescriptionNames.push(tag)
                  _this.prescriptionData.name = tag;
                }
              })
              .catch((error) => {
                console.log(error);
                displayErrorMessage(this.formTranslation.common.internal_server_error)
              })

        },
      printPrescription(){
        if (this.encounterData.encounter_id !== undefined) {
          this.printPrescriptionLoader =true
          get('get_prescription_print', {id: this.encounterData.encounter_id})
              .then((response) => {
                this.printPrescriptionLoader =false
                if (response.data.status !== undefined && response.data.status === true) {
                    $(response.data.data).printArea({});
                }
              })
              .catch((error) => {
                this.printPrescriptionLoader =false
                console.log(error);
                displayErrorMessage(this.formTranslation.common.internal_server_error);
              })
        }
      }
    },
    computed: {
        userData() {
            return this.$store.state.userDataModule.user;
        },
        encounterStatus() {
            return this.encounterData.status !== undefined ? this.encounterData.status : '1'
        },
        getEnablePrescription(){
            if(this.userData.prescription_module_config !== undefined){
                return this.userData.prescription_module_config
            }
        },
        // formTranslation: function () {
        //     return this.$store.state.staticDataModule.langTranslateData ;
        // }
    },
    watch: {
        encounterData(val, oldVal) {
            this.prescriptionData.encounter_id = val.encounter_id
        },
       prescriptionList(val,old){
          let _this = this;
         val = val.map(function(val){
           return Object.keys(val).reduce((acc, elem) => {
             switch (elem){
               case 'name':
                 acc[_this.formTranslation.common.name] = val[elem].label
                 break;
               case 'frequency':
                 acc[_this.formTranslation.patient_encounter.frequency] = val[elem];
                 break;
               case 'duration':
                 acc[_this.formTranslation.patient_encounter.duration_Days] = val[elem];
                 break;
               case 'instruction':
                 acc[_this.formTranslation.patient_encounter.instruction] = val[elem];
                 break;
               case 'default':
                 break;
             }
             return acc;
           }, {});
         });
         this.exportPrescription = val;
       }
    }
}
</script>

<style media="print" scoped>
@page {
  margin-bottom: 0;
}
</style>
