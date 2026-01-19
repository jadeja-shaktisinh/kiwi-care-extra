<template>
  <div>
    <div class="page-loader-section" v-if="formLoader">
      <loader-component-2></loader-component-2>
    </div>
    <div>
      <b-card class="p-0 shadow" header-tag="header" footer-tag="footer">
        <template v-slot:header>
          <b-row>
            <b-col sm="8" md="8" lg="8">
              <h2 class="mb-0">{{ $t('appointments.restrict_appointment_detail') }} <a v-if="request_status == 'off'" href="https://wordpress.iqonic.design/docs/product/kivicare/clinic-admin/settings/#appointment-setting" target="_blank"><i class="fa fa-question-circle"></i></a></h2> 
            </b-col>
          </b-row>
        </template>
        <form
            id="patient form"
            name="patient form"
            @submit.prevent="handleSubmit"
            :novalidate="true"
            enctype="multipart/form-data"
        >
          <div class="row">
            <div class="col-md-12">
              <div class="row">
                <div class="col-md-4 ">
                  <div class="form-group">
                    <label for="post_appointment" class="form-control-label"
                    > {{$t('appointments.post_appointment')}}
                    </label><span
                      class="text-danger">*</span>
                    <b-input-group size="md">
                      <b-form-input
                          id="post_appointment"
                          :class="{ ' is-invalid': submitted && $v.restrictAppointment.post_book.$error }"
                          v-model="restrictAppointment.post_book"
                          required="required" name="price" type="number"
                          min="0"
                          max="100000000000"
                      ></b-form-input>
                    </b-input-group>
                    <div v-if="submitted && !$v.restrictAppointment.post_book.required"
                         class="invalid-feedback">{{$t('appointments.post_appointment_required')}}
                    </div>
                    <div v-else-if="submitted && (!$v.restrictAppointment.post_book.minValue || !$v.restrictAppointment.post_book.maxValue )"
                         class="invalid-feedback">{{$t('appointments.post_appointment_length')}}
                    </div>
                    <div v-if="prevValCheck" class="invalid-feedback">
                      {{$t('appointments.post_day_must_be_greater_then_pre_day')}}
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="pre_appointment" class="form-control-label"
                    >{{$t('appointments.pre_appointment')}}
                    </label><span
                      class="text-danger">*</span>
                    <b-input-group size="md">
                      <b-form-input
                          id="pre_appointment"
                          :class="{ ' is-invalid': submitted && $v.restrictAppointment.pre_book.$error }"
                          v-model="restrictAppointment.pre_book"
                          required="required" name="price" type="number"
                          min="0"
                          max="100000000000"
                      ></b-form-input>
                    </b-input-group>
                    <div v-if="submitted && !$v.restrictAppointment.pre_book.required"
                         class="invalid-feedback">{{$t('appointments.pre_appointment_required')}}
                    </div>
                    <div v-else-if="submitted && (!$v.restrictAppointment.pre_book.minValue || !$v.restrictAppointment.pre_book.maxValue )"
                         class="invalid-feedback">{{$t('appointments.pre_appointment_length')}}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div>
            <div class="col-md-12">
              <p> {{$t('appointments.pre_post_note')}} </p>
            </div>
          </div>
          <div class="row" >
            <div class="col-md-12" >
              <div class="d-flex justify-content-end">
                <button type="submit" :disabled="prevValCheck" class="btn btn-primary">
                  <i class="fa fa-save"></i> {{ $t("common.save") }}
                </button>
              </div>
            </div>
          </div>
        </form>
      </b-card>
      <b-card class="card-body" header-tag="header" footer-tag="footer">
        <template v-slot:header>
          <b-row>
            <b-col sm="12" md="8" lg="8">
              <h2 class="mb-0"> {{$t('appointments.multi_file_upload')}}  </h2>
            </b-col>
          </b-row>
        </template>
        <b-row class="mt-2 ml-2">
          <b-col sm="12" md="12" lg="12">
            <div class="form-group">
              <b-form-checkbox
                  size="md"
                  id="checkbox-1"
                  v-model="status"
                  name="checkbox-1"
                  value="on"
                  unchecked-value="off"
                  @change="onFileUploadStatusChange"
                  switch
              >
                <b> {{$t('appointments.appointment_multi_file_upload')}} </b>
              </b-form-checkbox>
            </div>
          </b-col>
        </b-row>
      </b-card>
      <b-card class="card-body" header-tag="header" footer-tag="footer">
        <template v-slot:header>
          <b-row>
            <b-col sm="12" md="8" lg="8">
              <h2 class="mb-0"> {{$t('appointments.appointment_daily_reminder')}}</h2>
            </b-col>
          </b-row>
        </template>
        <b-row class="mt-2 ml-3">
          <b-col sm="12" md="4" lg="4">
            <div class="form-group">
              <b-form-checkbox
                  size="md"
                  id="checkbox-2"
                  v-model="reminder.status"
                  value="on"
                  name="checkbox-2"
                  unchecked-value="off"
                  switch
              >
                <b> {{$t('appointments.appointment_email_reminder')}} </b>
              </b-form-checkbox>
            </div>
          </b-col>
          <b-col sm="12" md="4" lg="4" v-if="userData.addOns.kiviPro == true">
            <div class="form-group">
              <b-form-checkbox
                  size="md"
                  id="checkbox-3"
                  v-model="reminder.sms_status"
                  value="on"
                  name="checkbox-2"
                  unchecked-value="off"
                  switch
              >
                <b> {{$t('appointments.appointment_sms_reminder')}} </b>
              </b-form-checkbox>
            </div>
          </b-col>
          <b-col sm="12" md="4" lg="4" v-if="userData.addOns.kiviPro == true && userData.pro_version >= '1.2.0'">
            <div class="form-group">
              <b-form-checkbox
                  size="md"
                  id="checkbox-4"
                  v-model="reminder.whatapp_status"
                  value="on"
                  name="checkbox-2"
                  unchecked-value="off"
                  switch
              >
                <b> {{$t('appointments.appointment_whatsapp_reminder')}} </b>
              </b-form-checkbox>
            </div>
          </b-col>
        </b-row>
        <!-- <b-row class="mt-2 ml-2" v-if="userData.addOns.kiviPro == true">
          <b-col sm="12" md="4" lg="4">
            <div class="form-group">
              <b-form-checkbox
                  size="md"
                  id="checkbox-3"
                  v-model="reminder.sms_status"
                  value="on"
                  name="checkbox-2"
                  unchecked-value="off"
                  switch
              >
                <b> {{$t('appointments.appointment_sms_reminder')}} </b>
              </b-form-checkbox>
            </div>
          </b-col>
          <b-col sm="12" md="4" lg="4">
            <div class="form-group">
              <b-form-checkbox
                  size="md"
                  id="checkbox-4"
                  v-model="reminder.whatapp_status"
                  value="on"
                  name="checkbox-2"
                  unchecked-value="off"
                  switch
              >
                <b> {{$t('appointments.appointment_whatsapp_reminder')}} </b>
              </b-form-checkbox>
            </div>
          </b-col>
        </b-row> -->
        <!-- <b-row class="mt-2 ml-2" v-if="userData.addOns.kiviPro == true">
          <b-col sm="12" md="4" lg="4">
            <div class="form-group">
              <b-form-checkbox
                  size="md"
                  id="checkbox-4"
                  v-model="reminder.whatapp_status"
                  value="on"
                  name="checkbox-2"
                  unchecked-value="off"
                  switch
              >
                <b> {{$t('appointments.appointment_whatsapp_reminder')}} </b>
              </b-form-checkbox>
            </div>
          </b-col>
        </b-row> -->
        <b-row class="mt-2" v-if="reminder.status == 'on' || reminder.whatapp_status =='on' || reminder.sms_status =='on' ">
          <b-col sm="2" md="2" lg="2">
            <div class="form-group">
              <label> <b> {{ $t('appointments.notice_of_appointment_reminder') }} </b> </label>
              <b-select  class="form-control text-capitalize" name="option" v-model="reminder.time" >
                <option v-bind:key="i.id" v-for="i in 48" :value=" i < 10 ? '0'+i : i " > {{   i < 10 ? '0'+i+':00' : i+':00'}}</option>
              </b-select>
            </div>
          </b-col>
          <b-col sm="8" md="8" lg="8">
            <div class="form-group m-2">
              <p>
                <b>
                  <!-- {{ $t('appointments.notice_of_appointment_reminder') }} -->
                </b>
              </p>
            </div>
          </b-col>
        </b-row>
        <!-- <b-row>
          <b-col sm="12" md="12" lg="12">
            <p>
              {{$t('appointments.appointment_reminder_info')}}
            </p>
          </b-col>
        </b-row> -->
        <div class="row" >
          <div class="col-md-12" >
            <div class="d-flex justify-content-end">
              <button type="button" @click="appointmentReminder" class="btn btn-primary">
                <i class="fa fa-save"></i> {{ $t("common.save") }}
              </button>
            </div>
          </div>
        </div>
      </b-card>
      <b-card class="card-body" header-tag="header" footer-tag="footer">
        <template v-slot:header>
          <b-row>
            <b-col sm="12" md="8" lg="8">
              <h2 class="mb-0"> {{$t('appointments.appointment_time_format')}}  </h2>
            </b-col>
          </b-row>
        </template>
        <b-row class="mt-2 ml-2">
          <b-col sm="12" md="12" lg="12">
            <div class="form-group">
              <b-form-checkbox
                  size="md"
                  id="checkbox-timeformat"
                  v-model="onAppointmentTimeFormatStatus"
                  name="checkbox-timeformat"
                  value="on"
                  unchecked-value="off"
                  @change="onAppointmentTimeFormat"
                  switch
              >
                <b> {{$t('appointments.appointment_time_24_format')}} </b>
              </b-form-checkbox>
            </div>
          </b-col>
        </b-row>
      </b-card>
      <b-card class="card-body" header-tag="header" footer-tag="footer">
        <template v-slot:header>
          <b-row>
            <b-col sm="12" md="8" lg="8">
              <h2 class="mb-0">{{$t('appointments.appointment_description_notes')}}</h2>
            </b-col>
          </b-row>
        </template>
        <b-row class="mt-2 ml-2">
          <b-col sm="6" md="6" lg="6">
            <div class="form-group">
              <b-form-checkbox
                  size="md"
                  id="checkbox-appointment-description"
                  v-model="onAppointmentDescription"
                  name="checkbox-appointment-description"
                  value="on"
                  unchecked-value="off"
                  @change="onAppointmentDescriptionChange"
                  switch
              >
                <b>{{$t('appointments.appointment_description')}}</b>
              </b-form-checkbox>
            </div>
          </b-col>
          <b-col sm="6" md="6" lg="6">
            <div class="form-group">
              <b-form-checkbox
                  size="md"
                  id="checkbox-appointment-patient_info"
                  v-model="onAppointmentPatientInfo"
                  name="checkbox-appointment-patient_info"
                  value='on'
                  unchecked-value='off'
                  @change="onAppointmentPatientInfoChange"
                  switch
              >
                <b>{{$t('appointments.show_patient_information')}}</b>
              </b-form-checkbox>
            </div>
          </b-col>
        </b-row>
      </b-card>
    </div>
  </div>
</template>
<script>
import { post, get } from "../../config/request";
import {maxLength, maxValue, minLength, minValue, required} from "vuelidate/lib/validators";
export default {
  name: "UniqueIdSetting",
  components: {
  },
  data: () => {
    return {
      onAppointmentDescription:"on",
      onAppointmentPatientInfo: "on",
      restrictAppointment:{},
      submitted: false,
      status: 'off',
      onAppointmentTimeFormatStatus:'off',
      reminder:{
        time:'24',
        status:'off',
        sms_status:'off',
        whatapp_status:'off'
      },
      storePrevios: {
        pre_book: '',
        post_book: ''
      },
      formLoader:true,
      request_status:'off',
    };
  },
  validations: {
    restrictAppointment:{
      pre_book: {
        required,
        minValue: minValue(0),
        maxValue:maxValue(365)
      },
      post_book: {
        required,
        minValue: minValue(0),
        maxValue:maxValue(365)
      },
    }
  },
  mounted() {
    if(!['administrator'].includes(this.getUserRole())) {
      this.$router.push({ name: "403"})
    }
    this.onAppointmentTimeFormatStatus = window.request_data !== undefined && window.request_data.appointment_time_format === 'on' ? 'on' : 'off';
    this.restrictAppointment = this.defaultData();
    this.edit();
    this.editAppointmentReminder();
    this.getFileUploadStatus();
    this.getAppointmentDescription();
    this.getAppointmentPatientInfo();
    this.getModule();
  },
  methods: {
    defaultData() {
      return {
        pre_book: "0",
        post_book: "365",
      };
    },
    handleSubmit() {

      this.submitted = true;
      // stop here if form is invalid
      this.$v.$touch();

      if (this.$v.restrictAppointment.$invalid) {
        return;
      }

      let pre_book_message = ''; 
      let alert_message = 'Pre booking open before ' + this.restrictAppointment.post_book + ' days ' + pre_book_message   ;

      if(this.restrictAppointment.pre_book != 0) {
        pre_book_message = ' and close before ' + this.restrictAppointment.pre_book + ' days of booking date.'
      } else {
        alert_message += 'before appointment booking date.' ;
      }

      alert_message = 'Appointment pre booking open from ' + this.restrictAppointment.post_book + ' days ' + pre_book_message + ' before appointment booking date. ' ;

      // let alert_message = 'Appointment pre booking starts from ' + this.restrictAppointment.post_book + ' day and stop ' + this.restrictAppointment.pre_book + ' days before appointment booking date. </ br>' + this.formTranslation.appointments.pre_book_are_you_sure ;

      $.confirm({
          title: this.formTranslation.appointments.booking_restriction,
          content: alert_message,
          type: 'red',
          buttons: {
            ok: {
              text: this.formTranslation.common.yes,
              btnClass: 'btn-danger',
              keys: ['enter'],
              action: () => {
                post("restrict_appointment_save",this.restrictAppointment)
                .then((response) => {
                  if (
                      response.data.status !== undefined &&
                      response.data.status === true
                  ) {
                    this.submitted = false;
                    this.storePrevios = Object.assign({}, this.storePrevios, this.restrictAppointment);
                    displayMessage(response.data.message);
                  } else {
                    this.submitted = false;
                    displayErrorMessage(response.data.message);
                  }
                })
                .catch((error) => {
                  this.submitted = false;
                  console.log(error);
                  displayErrorMessage(this.formTranslation.common.internal_server_error);
                });
              }
            },
            cancel: {
              text:this.formTranslation.common.cancel
            }
          }
      });

    },
    getAppointmentDescription: function(){
      this.formLoader = true;
      get('get_appointment_description_status',{})
        .then((res) => {
          this.formLoader = false;
          this.onAppointmentDescription = ((res.data.data == 'on') ? 'on' : 'off') ; 
        })
    },
    getAppointmentPatientInfo: function(){
      this.formLoader = true;
      get('get_appointment_patient_info_status',{})
        .then((res) => {
          this.formLoader = false;
          this.onAppointmentPatientInfo = ((res.data.data == 'on') ? 'on' : 'off') ; 
        })
    },
    getFileUploadStatus:function (){
      this.formLoader = true;
      get('get_multifile_upload_status',{} )
          .then((response) => {
            this.status = response.data.data ;
            this.formLoader = false;
          })
          .catch((error) => {
            this.formLoader = false;
            console.log('error', error);
          })
    },
    onFileUploadStatusChange:function (value){
      get('change_multifile_upload_status', { status: value } )
          .then((response) => {
            if (
                response.data.status !== undefined &&
                response.data.status === true
            ) {
              this.$store.commit("appointmentModule/FILE_UPLOAD_STATUS",{data:value});
              displayMessage(response.data.message);
            } else {
              displayErrorMessage(response.data.message);
            }
          })
          .catch((error) => {
            console.log('error', error);
          })
    },
    edit() {
      this.formLoader = true;
      get("restrict_appointment_edit", {})
          .then((response) => {
            if (
                response.data.status !== undefined &&
                response.data.status === true
            ) {
              setTimeout(() => {
                this.restrictAppointment = response.data.data;
                this.storePrevios = Object.assign({}, this.storePrevios, response.data.data);
              }, 200);
              this.formLoader = false;
            }
          })
          .catch((error) => {
            this.formLoader = false;
            console.log(error);
            displayErrorMessage(this.formTranslation.widgets.record_not_found);
          });
    },
    appointmentReminder:function(){
      post('appointment_reminder_notificatio_save', this.reminder )
          .then((response) => {
            if (
                response.data.status !== undefined &&
                response.data.status === true
            ) {
              displayMessage(response.data.message);
              setTimeout(()=>{
                window.location.reload()
              },1000)
            } else {
              displayErrorMessage(response.data.message);
            }
          })
          .catch((error) => {
            console.log('error', error);
          })
    },
    editAppointmentReminder:function (){
      this.formLoader = true;
      get("get_appointment_reminder_notification", {})
          .then((response) => {
            if (
                response.data.status !== undefined &&
                response.data.status === true
            ) {
              this.reminder = response.data.data;
            }
            this.formLoader = false;
          })
          .catch((error) => {
            console.log(error);
            this.formLoader = false;
            displayErrorMessage(this.formTranslation.widgets.record_not_found);
          });
    },
    onAppointmentTimeFormat:function(){
      post("update_appointment_time_format", {timeFormat: this.onAppointmentTimeFormatStatus})
          .then((response) => {
            if (
                response.data.status !== undefined &&
                response.data.status === true
            ) {
              displayMessage(response.data.message);
              setTimeout(()=>{
                window.location.reload()
              },1000)
            }else{
              displayErrorMessage(response.data.message);
            }
          })
          .catch((error) => {
            console.log(error);
            displayErrorMessage(this.formTranslation.widgets.record_not_found);
          });
    },
    onAppointmentDescriptionChange:function(value){
      post("appointment_description_status_change", {status: this.onAppointmentDescription})
          .then((response) => {
            if (
                response.data.status !== undefined &&
                response.data.status === true
            ) {
              this.$store.commit("appointmentModule/DESCRIPTION_STATUS",{data:value});
              displayMessage(response.data.message);
            }else{
              displayErrorMessage(response.data.message);
            }
          })
          .catch((error) => {
            console.log(error);
            displayErrorMessage(this.formTranslation.widgets.record_not_found);
          });
    },
    onAppointmentPatientInfoChange:function(value){
      post("appointment_patient_info_status_change", {status: this.onAppointmentPatientInfo})
          .then((response) => {
            if (
                response.data.status !== undefined &&
                response.data.status === true
            ) {
              displayMessage(response.data.message);
              this.$store.commit("appointmentModule/PATIENT_INFO_STATUS",{data:value});
            }else{
              displayErrorMessage(response.data.message);
            }
          })
          .catch((error) => {
            console.log(error);
            displayErrorMessage(this.formTranslation.widgets.record_not_found);
          });
    },
    getModule:function (){
        if(window.request_data.link_show_hide !== undefined && window.request_data.link_show_hide !== ''){
        this.request_status = window.request_data.link_show_hide;
        }
    }
  },
  computed:{
    userData () {
      return this.$store.state.userDataModule.user;
    },
    prevValCheck () {
      if(parseInt(this.restrictAppointment.pre_book) > parseInt(this.restrictAppointment.post_book)
          || parseInt(this.restrictAppointment.pre_book) === parseInt(this.restrictAppointment.post_book)) {
        return true ;
      } else {
        return false ;
      }
    }
  }
};
</script>