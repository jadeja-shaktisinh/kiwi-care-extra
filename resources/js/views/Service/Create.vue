<template>
  <b-row>
    <b-col sm="12">
      <form id="serviceForm" @submit.prevent="handleSubmit" :novalidate="true" enctype="multipart/form-data">
        <b-card class="p-0 shadow" header-tag="header" footer-tag="footer">
          <div class="row">
            <div class="col-md-9">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="type" class="form-control-label">
                      {{$t('service.service_category')}} <span class="text-danger">*</span>
                    </label>
                    <multi-select
                      v-model="service.type"
                      deselect-label=""
                      select-label=""
                      :class="{ ' is-invalid': submitted && $v.service.type.$error }"
                      :tag-placeholder="formTranslation.service.tag_select_service_plh" id="type"
                      :placeholder="formTranslation.service.select_service_plh"
                      label="label"
                      track-by="id"
                      :taggable="true"
                      @tag="addNewServiceCategory"
                      :options="types"
                      :loading="categoryMultiselectLoader && isServiceEdit"
                      :disabled="!isServiceEdit"
                    ></multi-select>
                    <span class="text-primary small font-weight-bold">{{$t('service.note_category')}}</span>
                    <div v-if="submitted && !$v.service.type.required" class="invalid-feedback"> {{$t('service.service_category_required')}} </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="name" class="form-control-label"> {{$t('service.service_name')}} <span class="text-danger">*</span></label>
                    <input
                        id="name"
                      :class="{ ' is-invalid': submitted && $v.service.name.$error }"
                      v-model="service.name"
                      :placeholder="formTranslation.service.service_name_plh"
                      required="required" name="name" type="text" class="form-control">
                    <div v-if="submitted && !$v.service.name.required"
                        class="invalid-feedback">{{$t('service.service_name_required')}}
                    </div>
                    <div v-else-if="submitted && (!$v.service.name.minLength || !$v.service.name.maxLength)"
                        class="invalid-feedback">{{$t('service.service_validation')}}
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="price" class="form-control-label">{{$t('service.charges')}} <span
                        class="text-danger">*</span></label>
                        <b-input-group size="md" :prepend="currencyPrefix" :append="currencyPostfix">
                          <b-form-input
                          id="price"
                          :class="{ ' is-invalid': submitted && $v.service.price.$error }"
                          v-model="service.price" 
                          :placeholder="formTranslation.service.charges_plh"
                          required="required" name="price" type="number"
                            min="0"
                          max="100000000000"
                          ></b-form-input>
                        </b-input-group>
                    <div v-if="submitted && !$v.service.price.required"
                        class="invalid-feedback">{{$t('service.service_charges_required')}}
                    </div>
                    <div v-else-if="submitted && (!$v.service.price.minValue || !$v.service.price.maxValue)"
                        class="invalid-feedback">{{$t('service.service_charge_length')}}
                    </div>
                  </div>
                </div>                
                <div class="col-md-4" v-if="getUserRole() !== 'doctor' && !hideDoctor" >
                  <div class="form-group">
                    <div class="select-all-doctor d-flex" >
                      <div class="doctor-select-all-2" style="width: 100%">
                        <label for="doctor-select-all" class="form-control-label">
                          {{$t('common.doctor')}} <span class="text-danger">*</span>
                        </label>
                        <b-form-checkbox
                          v-if="(serviceId == 0 || serviceId == -1 || serviceId == '' ) &&  $route.params.id === undefined"
                          id="doctor-select-all"
                          v-model="doctorSelectAll"
                          name="doctor-select-all"
                          value="accepted"
                          unchecked-value="not_accepted"
                          class="float-right text-primary"
                          > {{$t('service.select_all')}} </b-form-checkbox>
                      </div>
                    </div>
                    <div class="kivi-pr">
                      <multi-select
                        v-model="service.doctor_id"
                        :tag-placeholder="formTranslation.service.tag_select_doc_plh"
                        id="doctor_id"
                        :placeholder="formTranslation.service.select_doc_plh"
                        label="label"
                        track-by="id"
                        :loading="doctorMultiselectLoader"
                        :multiple="isServiceEdit"
                        :options="doctors"
                      ></multi-select>
                    </div>
                    <div v-if="submitted && !$v.service.doctor_id.required"
                        class="invalid-feedback">{{$t('appointments.doc_required')}}
                    </div>
                  </div>
                </div>                
                <div class="col-md-4" v-if="userData.addOns.telemed || userData.addOns.googlemeet">
                  <div class="form-group ">
                    <label for="telemed_service" class="form-control-label">
                      {{$t('service.is_telemed_service')}} <span class="text-danger">*</span></label>
                    <div class="kivi-pr">
                      <multi-select
                          v-model="service.telemed_service"
                          :tag-placeholder="formTranslation.service.telemed_service"
                          id="status"
                          name="telemed_service"
                          :placeholder="formTranslation.service.telemed_service"
                          :options="['yes','no']"
                          @select="telemedEnableChange"
                          @remove="telemedEnableChange"
                          :disabled= "getUserRole() == 'doctor' && !userData.telemedConfigOn"
                      ></multi-select>
                      <span class="select_clear_btn"  @click="service.status = ''">×</span>
                    </div>
                    <span v-if="getUserRole() == 'doctor' && !userData.telemedConfigOn" class="text-primary small font-weight-bold">{{$t('service.note_telemed')}}</span>
                    <div v-if="submitted && !$v.service.status.required" class="invalid-feedback">
                      {{$t('appointments.status_required')}}
                    </div>
                  </div>
                </div>
                <div class="col-md-4" v-if="userData.addOns.kiviPro">
                  <div class="form-group">
                    <label for="duration" class="form-control-label"> {{$t('patient_encounter.duration')}} </label>
                    <div class="">
                      <b-select
                          id="time_slot"
                          v-model="service.duration"
                          class="form-control text-capitalize"
                          name="duration"
                      >
                        <option v-for="(slot, index) in time_slots" :key="index" :value="slot.value"> {{ slot.label }}</option>
                      </b-select>
                    </div>
                  </div>
                </div>
                <div :class="userData.addOns.telemed || userData.addOns.googlemeet ? 'col-md-4' : 'col-md-4'" >
                  <div class="form-group ">
                    <label for="status" class="form-control-label">
                      {{$t('common.status')}} <span class="text-danger">*</span></label>
                    <div class="kivi-pr">
                        <multi-select
                          v-model="service.status"
                          :tag-placeholder="formTranslation.service.tag_select_doc_plh"
                          id="status"
                          name="status"
                          :placeholder="formTranslation.service.select_status_plh"
                          label="label"
                          track-by="id"
                          :options="[{ id: 1, label: 'Active' }, { id:0 , label: 'Inactive'}]"
                        ></multi-select>
                        <span class="select_clear_btn"  @click="service.status = ''">×</span>
                    </div>
                    <div v-if="submitted && !$v.service.status.required" class="invalid-feedback">
                      {{$t('appointments.status_required')}}
                    </div>
                  </div>
                </div>
                <div :class="userData.addOns.telemed || userData.addOns.googlemeet ? 'col-md-4' : 'col-md-4'">
                  <div class="form-group ">
                    <label for="multiservice" class="form-control-label">
                      {{$t('common.include_in_multiservice')}} <span class="text-danger">*</span></label>
                    <div class="kivi-pr">
                      <multi-select
                          v-model="service.multiservice"
                          :tag-placeholder="formTranslation.common.tag_select_doc_plh"
                          id="multiservice"
                          name="multiservice"
                          :placeholder="formTranslation.service.select_status_plh"
                          label="label"
                          track-by="id"
                          :options="multiServiceOptions"
                      ></multi-select>
                      <span class="select_clear_btn"  @click="service.multiservice = ''">×</span>
                    </div>
                    <div v-if="submitted && !$v.service.multiservice.required" class="invalid-feedback">
                      {{$t('appointments.status_required')}}
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="kivicare-avatar-upload">
                <div class="kivicare-avatar-edit">
                  <input  id="file" type="button" ref="file" class="p-2"
                          @click="uploadProfile()" style="color: transparent;">
                  <label for="file" v-b-tooltip.hover :title="formTranslation.clinic.edit_profile_img">
                    <i class="fas fa-pencil-alt fa-2x"></i>
                  </label>
                </div>
                <div class="kivicare-avatar-preview">
                  <div id="imagePreview" :style="'background-image: url('+ (service.image ? service.image : profileImage)+');'">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-md-12 p-0">
              <div class="d-flex justify-content-end">
                <button v-if="!loading" class="btn btn-primary rtl-ml-2" type="submit">
                 <i class="fa fa-save"></i> {{ formTranslation.service.save_btn }}
                </button>
                <button v-else class="btn btn-primary" type="submit" disabled>
                  <i class="fa fa-sync fa-spin"></i> &nbsp; {{$t('common.loading')}}
                </button>
                <button type="button" class="btn btn-outline-primary" @click="closeForm" > {{$t('common.cancel')}} </button>
              </div>
            </div>
          </div>
        </b-card>
      </form>
    </b-col>
  </b-row>
</template>

<script>

import {maxLength, maxValue, minLength, minValue, required} from "vuelidate/lib/validators";
import {post, get} from "../../config/request";
import {validateForm } from "../../config/helper";

export default {
  data: () => {
    return {
      curency: {},
      oldDoctorList: [],
      serviceSelectAll: [],
      doctorSelectAll: '',
      cardTitle: 'Add service',
      encounter_id: 0,
      types: [],
      serviceList: [],
      outcomes: [],
      service: {},
      loading: false,
      submitted: false,
      buttonText: '<i class="fa fa-save"></i> Save',
      doctors: [],
      clinic_id: 0,
      hideDoctor:false,
      profileImage: '',
      file:'',
      categoryMultiselectLoader:true,
      doctorMultiselectLoader:true,
      time_slots:[],
      multiServiceOptions:[
        {
          id:'no',
          label:'Single'
        },
        {
          id:'yes',
          label:'Multiple'
        }
      ]
    }
  },
  props:{
    serviceId:[Number,String],
    props_doctor_id:{
      type:[Number,String],
      default() {
        return -1
      }
    }
  },
  validations: {
    service: {
      type: {required},
      name: {
        required,
        minLength: minLength(2),
        maxLength: maxLength(50)
      },
      doctor_id: {
        required,
      },
      price: {
        required,
        minValue: minValue(0),
        maxValue: maxValue(1000000000000000000)
      },
      status: {required},
      multiservice:{required}
    },
  },
  mounted() {
    this.service = this.defaultServiceData();
    if(this.props_doctor_id !== undefined && this.props_doctor_id !== -1 && this.props_doctor_id !== '-1'){
      this.hideDoctor = true;
      this.service.doctor_id = [{
        id:this.props_doctor_id
      }]
    }
    this.profileImage = window.request_data.kiviCarePluginURL + 'assets/images/kc-demo-img.png';
    this.init();
  },
  methods: {
    init: function () {
      this.getTimeSlots()
      if(this.serviceId != -1){
        this.cardTitle = this.formTranslation.common.edit_service;
        this.buttonText = '<i class="fa fa-save"></i>'+this.formTranslation.common.save;
        this.get_service(this.serviceId)
      }

      if(this.$route.params.id !== undefined){
        this.get_service(this.$route.params.id)
      }
      this.getServiceType();
      if (this.$store.state.userDataModule.clinic !== undefined) {
        this.clinic_id = this.$store.state.userDataModule.clinic.id;
        if (this.getUserRole() !== 'doctor') {
          if(!this.hideDoctor){
            this.getDoctorDropdown();
          }
        } else {
          this.service.doctor_id = {
            id: this.$store.state.userDataModule.user.ID,
            label: this.$store.state.userDataModule.user.display_name
          }
        }
      } else {
        this.$store.dispatch("userDataModule/fetchUserData", {});
        setTimeout(() => {
          this.clinic_id = this.$store.state.userDataModule.clinic.id;
          if (this.getUserRole() !== 'doctor') {
            if(!this.hideDoctor){
              this.getDoctorDropdown();
            }
          } else {
            this.service.doctor_id = {
              id: this.$store.state.userDataModule.user.ID,
              label: this.$store.state.userDataModule.user.display_name
            }
          }
        }, 1000);
      }
      //this.getClinicServices();
      //this.getDynamicTranslationKey();
      this.multiServiceOptions = this.multiServiceOptions.map( (item) => {
        item.label = this.formTranslation.common[item.id];
        return item;
      })
    },
    getTimeSlots: function () {
      this.time_slots = [
          {value:5,label:'5min'},
        {value:10,label:'10min'},
        {value:15,label:'15min'},
        {value:20,label:'20min'},
        {value:25,label:'25min'},
        {value:30,label:'30min'},
        {value:35,label:'35min'},
        {value:40,label:'40min'},
        {value:45,label:'45min'},
        {value:50,label:'50min'},
        {value:55,label:'55min'},
        {value:60,label:'1hr'},
        {value:75,label:'1hr 15min'},
        {value:90,label:'1hr 30min'},
        {value:105,label:'1hr 45min'},
        {value:120,label:'2hr'},
        {value:150,label:'2hr 30min'}
      ]
    },
    closeForm(){
      if(this.hideDoctor){
        this.$emit('closeServiceModal')
        this.hideDoctor = false;
      }
      this.$emit('closeForm')
      if(this.$route.params.id !== undefined){
        this.$router.push({name: 'service'});
      }
    },
    uploadProfile() {
      let _this = this;

      var custom_uploader = kivicareCustomImageUploader(this.formTranslation)

      custom_uploader.on('select', function () {
        var attachment = custom_uploader.state().get('selection').first().toJSON();
        _this.profileImage = attachment.url;
        _this.service.profile_image = attachment.id;
        _this.service.image = _this.profileImage
      });

      //Open the uploader dialog
      custom_uploader.open();
    },
    handleSubmit: function () {
      this.loading = true;

      this.submitted = true;

      // stop here if form is invalid
      this.$v.$touch();

      if (this.$v.service.$invalid) {
        this.loading = false;
        return;
      }
      
      if (validateForm("serviceForm")) {
        post('service_save', this.service)
          .then((response) => {
            this.loading = false;
            this.submitted = false;
            if (response.data.status !== undefined && response.data.status === true) {
              if(this.getUserRole() === 'administrator'){
                this.$store.dispatch("userDataModule/fetchUserData", {});
              }
              this.service = this.defaultServiceData();
              displayMessage(response.data.message);
              this.$emit('closeForm');
              this.getService()
              if(this.hideDoctor){
                this.$emit('closeServiceModal')
                this.hideDoctor = false;
              }
            } else {
              displayErrorMessage(response.data.message)
            }
          })
          .catch((error) => {
            console.log(error);
            this.loading = false;
            this.submitted = false;
            displayErrorMessage(this.formTranslation.common.internal_server_error)
          })
      }
    },
    defaultServiceData: function () {
      return {
        type: '',
        name: '',
        doctor_id: '',
        service_id: '',
        price: '',
        status: { id: 1, label: 'Active' },
        multiservice:{id:"yes",label:this.formTranslation.common.yes},
        telemed_service:'no',
        duration:''
      }
    },
    getDoctorDropdown: function () {
      this.doctorMultiselectLoader = true;
      get('get_static_data', {
        data_type: 'clinic_doctors',
      }).then((response) => {
        this.doctorMultiselectLoader = false;
        if (response.data.status !== undefined && response.data.status === true) {
          this.doctors = response.data.data;
          this.oldDoctorList = this.doctors;
        } else {
          displayErrorMessage(response.data.message)
        }
      })
        .catch((error) => {
          this.doctorMultiselectLoader = false;
          console.log(error);
          displayErrorMessage(this.formTranslation.common.internal_server_error)
        })
    },
    getService() {
       this.$emit('getServiceData')
    },
    getClinicServices () {
      // get_clinic_service
      post('get_clinic_service', {}).then((response) => {
        if (response.data.status !== undefined && response.data.status === true) {
          this.serviceList = response.data.data;
        } else {
          displayErrorMessage(response.data.message)
        }
      })
        .catch((error) => {
          console.log(error);
          displayErrorMessage(this.formTranslation.common.internal_server_error )
        })

    },
    getServiceType () {
      this.categoryMultiselectLoader = true;
      // Get dropdown data for service
      get('get_static_data', {
        data_type: 'static_data_with_label',
        static_data_type: 'service_type'
      })
        .then((response) => {
          if (response.data.status !== undefined && response.data.status === true) {
            this.types = response.data.data;
            let temp = this.types;
            if(temp.length > 0) {
              let checkExists = false ;
              temp.map(function(value,key){
                  if(value.id == 'system_service'){
                   checkExists = true;
                  }
              })
              if(!checkExists){
                this.types.push({id:'system_service',label:'System Service'})
              }
            }
          } else {
            displayErrorMessage(response.data.message)
          }
          this.categoryMultiselectLoader = false;
        })
        .catch((error) => {
          this.categoryMultiselectLoader = false;
          console.log(error);
          displayErrorMessage(this.formTranslation.common.internal_server_error)
        })
    },
    selectServiceAll () {
      this.service.name = this.serviceList ;
    },
    addNewServiceTag (value) {
      let newService = {
        id: -2,
        name: value
      } ;
      this.serviceList.push(newService);
      // this.service.name.push(newService);
    },
    addNewServiceCategory (value) {
      let specialitiesObj = {
        label: value,
        type: 'service_type',
        value: value.replace('', '_'),
        status: 1,
      }
      let _this = this;
      post('static_data_save', specialitiesObj)
          .then((response) => {
            if (response.data.status !== undefined && response.data.status === true) {
              _this.types.push({id: value.replace('', '_'), label: value})
              _this.service.type =  {id: value.replace('', '_'), label: value} ;
                this.getServiceType();
            }
          })
          .catch((error) => {
            console.log(error);
            displayErrorMessage(this.formTranslation.common.internal_server_error)
          })
    },
    getDynamicTranslationKey: function () {
      if(this.$store.state.staticDataModule !== undefined && this.$store.state.staticDataModule.langTranslateData !== undefined && this.$store.state.staticDataModule.langTranslateData !== []) {
          this.$store.dispatch("staticDataModule/fetchLangTranslateData", {
          filePath: window.request_data.kiviCarePluginURL,
          current: 'temp',
          module: ''
          });
      }
    },
    get_service(serviceId){
      if(!serviceId){
        return
      }
      get('service_edit', {
        id: serviceId
      })
          .then((response) => {
            if (response.data.status !== undefined && response.data.status === true) {
              this.service = response.data.data
              this.telemedEnableChange(this.service.telemed_service)
            }
          })
          .catch((error) => {
            console.log(error);
            displayErrorMessage(this.formTranslation.widgets.record_not_found);
          })
    },
    telemedEnableChange(selectedValue){
      if(selectedValue === 'yes'){
        let selected_doctor_telemed_active = false;
        this.doctors = this.doctors.filter((val, key) => {
          if(val.enableTeleMed && this.service.doctor_id && this.service.doctor_id.id ){
            if(val.id == this.service.doctor_id.id){
              selected_doctor_telemed_active = true
            }
          }
          if(val.enableTeleMed){
            return val
          }
        });
        if(!selected_doctor_telemed_active){
          this.service.doctor_id = {};
        }
      }else{
        this.doctors = this.oldDoctorList;
      }
    }
  },
  computed: {
    currency: function() {
      return  this.currencyData
    },
    currencyPrefix: function () {
      if(this.currency !== undefined && this.currency !== null && this.currency !== '') {
        return this.currency.currency_prefix;
      }
    },
    currencyPostfix: function () {
      if(this.currency !== undefined && this.currency !== null && this.currency !== '') {
        return this.currency.currency_postfix;
      }
    },
    clinicId: function () {
      if (this.$store.state.userDataModule.clinic !== undefined) {
          return this.$store.state.userDataModule.clinic.id
      }
    },
    isServiceEdit: function () {
      return !(this.serviceId !== undefined && this.serviceId !== 0 && this.serviceId !== -1);
    },
    userData () {
      return this.$store.state.userDataModule.user;
    }
    // formTranslation: function () {
    //   return this.$store.state.staticDataModule.langTranslateData ;
    // }
  },
  watch: {
    serviceId(serviceId, oldVal) {
      if (serviceId !== undefined && serviceId !== 0 && serviceId !== -1) {
        this.cardTitle = this.formTranslation.common.edit_service;
        this.buttonText = '<i class="fa fa-save"></i>'+this.formTranslation.common.save;
        this.get_service(serviceId)
      }
    },
    doctorSelectAll(value) {
      if (value === 'not_accepted') {
        this.service.doctor_id = [] ;
      } else {
        this.service.doctor_id = this.doctors ;
      }
    }
  }
}
</script>
