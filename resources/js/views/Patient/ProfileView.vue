<template>
<div class="row">
  <b-col sm="12">
    <b-card class="p-0 shadow" body-class="p-0" header-tag="header" footer-tag="footer">
      <template v-slot:header>
        <b-row class="align-items-center">
          <b-col sm="6" md="8" lg="8">
            <h3 class="mb-0">{{ $t('common.profile') }}</h3>
          </b-col>
          <b-col sm="6" md="4" lg="4" >
            <div class="d-flex justify-content-end">
              <button type="button" class="btn btn-sm btn-primary" @click="$router.go(-1);">
                <i class="fa fa-angle-double-left"></i> {{$t('common.back')}}
              </button>
            </div>
          </b-col>
        </b-row>
      </template>
      <div class="page-loader-section" v-if="pageLoader">
        <loader-component-2></loader-component-2>
      </div>
      <div v-else>
        <div class="row">
          <div class="col-md-3 col-12">
            <div class="row">
              <div class="col-12">
                <div class="card card-profile">
                  <div class="row justify-content-center mt-4">
                    <div class="kivicare-avatar-upload">
                      <div class="kivicare-avatar-edit">
                        <input  id="file" type="button"  class="p-2"
                                @click.prevent="uploadProfile()" style="color: transparent;">
                        <label for="file" v-b-tooltip.hover :title="formTranslation.clinic.edit_profile_img">
                          <i class="fas fa-pencil-alt fa-2x"></i>
                        </label>
                      </div>
                      <div class="kivicare-avatar-preview">
                        <div id="imagePreview"
                             :style="'background-image: url('+ (patientData.user_profile ? patientData.user_profile : profileImage)+');'" >
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="card-body pt-0 mt-2">
                    <div class="text-center">
                      <h5 class="h3">
                        {{ patientData.display_name }}
                      </h5>
                      <div class="h5 font-weight-300">
                        {{ patientData.user_email }}
                      </div>
                      <div class="h5 mt-4">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card-body">
                  <ul class="navbar-nav iq-navbar-ul flex-md-column flex-row" >
                    <li class="mt-2 p-1" :class="currentRouteModule === 'profile' ? 'active-li' : ' '"  @click="currentRouteModule = 'profile'">
                      <i  class="fa fa-user text-primary m-2" aria-hidden="true" v-b-tooltip.hover :title="formTranslation.common.profile"></i>
                      <span >{{$t('common.profile')}}</span>
                    </li>
                    <li class="mt-2 p-1" :class="(currentRouteModule === 'report' ? 'active-li' : '' )"  @click="currentRouteModule = 'report'" v-if="userData.addOns.kiviPro == true  && kcCheckPermission('patient_report')">
                      <i class="fa fa-file text-primary m-2" aria-hidden="true" v-b-tooltip.hover :title="formTranslation.reports.reports"></i>
                      <span>{{$t('reports.reports')}}</span>
                    </li>
                    <li class="mt-2 p-1"  :class="currentRouteModule === 'appointment' ? 'active-li' : ' '"  @click="currentRouteModule = 'appointment'"  v-if="kcCheckPermission('appointment_list')">
                      <i class="fas fa-calendar-week text-primary m-2" aria-hidden="true" v-b-tooltip.hover :title="formTranslation.common.profile"></i>
                      <span>{{$t('appointments.appointments')}}</span>
                    </li>

<!--                    <li class="mt-2 p-1"  :class="currentRouteModule === 'encounter' ? 'active-li' : ' '"  @click="currentRouteModule = 'encounter'" >-->
<!--                      <i class="far fa-calendar-times text-primary m-2" aria-hidden="true" v-b-tooltip.hover :title="formTranslation.patient_encounter.encounters"></i>-->
<!--                      <span>{{$t('patient_encounter.encounters')}}</span>-->
<!--                    </li>-->
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-9 col-12">
            <div class="row">
              <div class="col-6">
                <div class="card-body shadow">
                  <div class="row">
                    <div class="col">
                      <h6 class="heading-small text-muted mb-4">{{  $t('appointments.appointments') }}</h6>
                    </div>
                    <div class="col-auto">
                      <div class="icon icon-shape bg-gradient-green text-white rounded-circle shadow">
                        <i class="fas fa-calendar-check"></i>
                      </div>
                    </div>
                  </div>
                  <p class="mt-3 mb-0 text-sm">
                    <span class="text-nowrap">{{ $t('dashboard.total_appointments') }}
                      <span class="font-weight-bold mb-0">{{ appointment_encounter_data.total_appointment }}
                      </span>
                    </span>
                  </p>
                  <p class="mt-3 mb-0 text-sm">
                    <span class="text-nowrap">{{ $t('appointments.upcoming') + ' ' + $t('appointments.appointments') }}
                      <span class="font-weight-bold mb-0">{{ appointment_encounter_data.upcoming_appointment }}
                      </span>
                    </span>
                  </p>
                </div>
              </div>
              <div class="col-6">
                <div class="card-body shadow">
                  <div class="row">
                    <div class="col">
                      <h6 class="heading-small text-muted mb-4">{{  $t('patient_encounter.encounters') }}</h6>
                    </div>
                    <div class="col-auto">
                      <div class="icon icon-shape bg-gradient-green text-white rounded-circle shadow">
                        <i class="far fa-calendar-times"></i>
                      </div>
                    </div>
                  </div>
                  <p class="mt-3 mb-0 text-sm">
                    <span class="text-nowrap">{{ $t('common.total') + ' ' + $t('patient_encounter.encounters') }}
                      <span class="font-weight-bold mb-0">{{ appointment_encounter_data.total_encounters }}
                      </span>
                    </span>
                  </p>
                  <p class="mt-3 mb-0 text-sm">
                    <span class="text-nowrap">{{ $t('appointments.upcoming') + ' ' + $t('patient_encounter.encounters') }}
                      <span class="font-weight-bold mb-0">{{ appointment_encounter_data.upcoming_encounters }}
                      </span>
                    </span>
                  </p>
                </div>
              </div>
            </div>
            <div v-if="currentRouteModule === 'profile'">
              <div class="card-body">
                <h6 class="heading-small text-muted mb-4">{{ $t('doctor.basic_information') }}</h6>
                <div class="pl-lg-4">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="first_name" class="form-control-label">
                          {{ $t('common.fname') }} <span class="text-danger">*</span>
                        </label>
                        <input id="first_name"
                               :class="{ ' is-invalid': submitted && $v.patientData.first_name.$error }"
                               v-model="patientData.first_name" :placeholder="formTranslation.patient.fname_plh"
                               required="required" name="name" type="text" class="form-control">
                        <div v-if="submitted && !$v.patientData.first_name.required"
                             class="invalid-feedback">{{ $t('common.fname_required') }}
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="last_name" class="form-control-label">
                          {{ $t('common.lname') }} <span class="text-danger">*</span>
                        </label>
                        <input id="last_name"
                               :class="{ ' is-invalid': submitted && $v.patientData.last_name.$error }"
                               v-model="patientData.last_name" :placeholder="formTranslation.patient.lname_placeholder"
                               required="required" name="name" type="text" class="form-control">
                        <div v-if="submitted && !$v.patientData.last_name.required" class="invalid-feedback">
                          {{ $t('common.lname_required') }}
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="email" class="form-control-label">
                          {{ $t('common.email_address') }} <span class="text-danger">*</span>
                        </label>
                        <input id="email"
                               :class="{ ' is-invalid': submitted && $v.patientData.user_email.$error }"
                               v-model="patientData.user_email" :placeholder="formTranslation.patient.address_placeholder"
                               required="required" name="email" type="email" class="form-control">
                        <div v-if="submitted && !$v.patientData.user_email.required"
                             class="invalid-feedback">{{ $t('common.email_required') }}
                        </div>
                        <div v-else-if="submitted && !$v.patientData.user_email.emailValidate"
                             class="invalid-feedback">{{ $t('common.invalid_email') }}
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-3">
                      <div class="form-group ">
                        <label for="telephone_no" class="form-control-label">
                          {{ $t('common.contact_no') }}. <span class="text-danger">*</span>
                        </label>
                        <input id="telephone_no"
                               :class="{ ' is-invalid': submitted && $v.patientData.mobile_number.$error }"
                               v-model="patientData.mobile_number"
                               :placeholder="formTranslation.patient.contact_placeholder"
                               required="required" name="telephone_no" type="text" class="form-control">
                        <div v-if="submitted && !$v.patientData.mobile_number.required"
                             class="invalid-feedback">{{ $t('common.contact_num_required') }}
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3" v-if="!hideFields.includes('dob')">
                      <div class="form-group">
                        <label for="dob" class="form-control-label">{{ $t('common.dob') }}.
                        </label>
                        <input type="date" v-model="patientData.dob" class="form-control" name="doc_birthdate" id="doc_birthdate"
                               :max="new Date().toISOString().slice(0, 10)">
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="gender" class="form-control-label">{{ $t('common.gender') }} <span
                            class="text-danger">*</span></label>
                        <div class="col-md-12">
                          <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="male" name="gender" v-model="patientData.gender"
                                   value="male" class="custom-control-input">
                            <label class="custom-control-label" for="male">{{ $t('common.male') }}</label>
                          </div>
                          <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="female" name="gender" v-model="patientData.gender"
                                   value="female" class="custom-control-input">
                            <label class="custom-control-label" for="female">{{ $t('common.female') }}</label>
                          </div>
                          <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="other" name="gender" v-model="patientData.gender"
                                   value="other" class="custom-control-input">
                            <label class="custom-control-label" for="other">{{ $t('common.other') }}</label>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3" v-if="!hideFields.includes('blood_group')">
                      <div class="form-group">
                        <label for="blood_group" class="form-control-label">{{ $t('clinic.blood_group') }} </label>
                        <b-select name="blood_group" id="blood_group" class="form-control"
                                  v-model="patientData.blood_group">
                          <option value="default"> {{ $t('clinic.select_blood_group') }}</option>
                          <option v-for="(group, index) in bloodGroups" :value="group" :key="index">{{ group }}</option>
                        </b-select>
                      </div>
                    </div>
                  </div>
                </div>
                <hr class="my-4" v-if="!hideFields.includes('address') || !hideFields.includes('city')
                     || !hideFields.includes('country') || !hideFields.includes('postal_code')"/>
                <h6 class="heading-small text-muted mb-4" v-if="!hideFields.includes('address') || !hideFields.includes('city')
                     || !hideFields.includes('country') || !hideFields.includes('postal_code')">
                  {{ $t('common.contact_info') }}</h6>
                <div class="pl-lg-4">
                  <div class="row">
                    <div class="col-md-12" v-if="!hideFields.includes('address')">
                      <div class="form-group">
                        <label for="address" class="form-control-label">{{ $t('common.address') }} </label>
                        <textarea
                            name="address" id="address" class="form-control"
                            v-model="patientData.address"
                            :placeholder="formTranslation.patient.address_placeholder"></textarea>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-4" v-if="!hideFields.includes('city')">
                      <div class="form-group">
                        <label for="city" class="form-control-label"> {{ $t('common.city') }} </label>
                        <input id="city"
                               v-model="patientData.city"
                               :placeholder="formTranslation.patient.city_placeholder"
                               name="city" type="text"
                               class="form-control">
                      </div>
                    </div>
                    <div class="col-md-4" v-if="!hideFields.includes('country')">
                      <div class="form-group">
                        <label for="country" class="form-control-label"> {{ $t('common.country') }} </label>
                        <input id="country"
                               v-model="patientData.country" :placeholder="formTranslation.patient.country_placeholder"
                               name="country"
                               type="text" class="form-control">
                      </div>
                    </div>
                    <div class="col-md-4" v-if="!hideFields.includes('postal_code')">
                      <div class="form-group">
                        <label for="postal_code" class=" form-control-label"> {{ $t('common.postal_code') }} </label>
                        <input id="postal_code"
                               v-model="patientData.postal_code" :placeholder="formTranslation.patient.pcode_placeholder"
                               name="postal_code" type="text" class="form-control">
                      </div>
                    </div>
                  </div>
                </div>
                <div v-if="showCustomField">
                  <hr class="my-4"/>
                  <h6 class="heading-small text-muted mb-4">{{ $t('doctor.extra_detail') }}</h6>
                  <div class="pl-lg-4">
                    <edit-custom-fields
                        module_type="patient_module"
                        :module_id="String(patientData.ID)"
                        @bindCustomField="getCustomFieldsValues"
                        :fieldsValue="custom_fields !== undefined ? custom_fields : []"
                        :customFieldsObj="custom_fields !== undefined ? custom_fields : []"
                    ></edit-custom-fields>
                  </div>
                </div>
              </div>
              <div class="card-footer">
                <div class="row">
                  <div class="col-md-12">
                    <div class="float-right">
                      <button v-if="!loading" @click="handleSubmit" class="btn btn-primary" type="submit">
                        <i class="fa fa-save"></i>{{$t('common.save')}}
                      </button>
                      <button v-else class="btn btn-primary" type="submit" disabled> <i class="fa fa-sync fa-spin"></i>&nbsp; {{$t('common.loading')}}</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div v-if="currentRouteModule === 'report' && userData.addOns.kiviPro == true  && kcCheckPermission('patient_report')">
              <patientReport :patient_profile_id="$route.params.id"></patientReport>
            </div>
            <div v-if="currentRouteModule === 'appointment' && userData.addOns.kiviPro == true  && kcCheckPermission('appointment_list')">
              <Appointment :patient_profile_id="$route.params.id"></Appointment>
            </div>
          </div>
        </div>
      </div>
    </b-card>
  </b-col>
</div>
</template>

<script>
import {get, post} from "../../config/request";
import {required} from "vuelidate/lib/validators";
import {emailValidate, validateForm} from "../../config/helper";
import patientReport from "./Report";
import Appointment from "../Appointment/AllappointmentList";

export default {
  name: "ProfileView",
  components:{patientReport,Appointment},
  data: () => {
    return {
      currentRouteModule:'profile',
      pageLoader:false,
      loading: false,
      profileImage: window.request_data.kiviCarePluginURL + 'assets/images/kc-demo-img.png',
      patientData:{},
      submitted: false,
      cardTitle: 'Edit Profile',
      qualification: {},
      bloodGroups: [
        'A+', 'B+', 'AB+', 'O+', 'A-', 'B-', 'AB-', 'O-'
      ],
      file:'',
      hideFields:[],
      formLoader:true,
      showCustomField:false,
      custom_fields:[],
    }
  },
  validations: {
    patientData: {
      first_name: {
        required,
      },
      last_name: {
        required,
      },
      user_email: {
        required,
        emailValidate
      },
      mobile_number: {
        required,
      },
    }
  },
  mounted() {
    this.init();
    this.patientData = this.defaultData()
    if(this.$route.params.id !== undefined){
      this.patientDetails(this.$route.params.id)
      this.$store.dispatch("appointmentModule/fetchAppointmentEncounterCount", { id:this.$route.params.id })
    }
    this.getHideFieldsArrayFromFilter()
  },
  methods:{
    init(){

    },
    defaultData(){
      return {
        first_name: '',
        last_name: '',
        username: '',
        user_email: '',
        user_pass: '',
        mobile_number: '',
        gender: '',
        dob: '',
        about_me: '',
        address: '',
        city: '',
        state: '',
        country: '',
        postal_code: '',
        blood_group: 'default',
        profile_image: ''
      };
    },
    patientDetails(id){
      this.pageLoader = true;
      get('patient_edit', {
        id: id,
        request_type:'patient_profile'
      })
          .then((response) => {
            this.pageLoader = false;
            if (response.data.status !== undefined && response.data.status === true) {
              this.patientData = response.data.data
              this.showCustomField = true;
              this.custom_fields = response.data.custom_filed
            }
          })
          .catch((error) => {
            this.pageLoader = false;
            console.log(error);
            displayErrorMessage(this.formTranslation.widgets.record_not_found);
          })
    },
    getCustomFieldsValues: function (fieldsObj) {
      if(!fieldsObj || fieldsObj === undefined){
        return false;
      }
      this.patientData.custom_fields = fieldsObj;
    },
    getHideFieldsArrayFromFilter:function () {
      get('get_hide_fields_array_from_filter',{})
          .then((response) => {
            if (response.data.status !== undefined && response.data.status === true) {
              this.hideFields = response.data.data
            }
          })
          .catch((error) => {
            console.log(error);
            displayErrorMessage(this.formTranslation.common.internal_server_error);
          })
    },
    uploadProfile() {
      let _this = this;

      var custom_uploader = kivicareCustomImageUploader(this.formTranslation);

      custom_uploader.on('select', function () {
        var attachment = custom_uploader.state().get('selection').first().toJSON();
        _this.patientData.user_profile = attachment.url;
        _this.patientData.profile_image = attachment.id;
      });

      //Open the uploader dialog
      custom_uploader.open();

    },
    handleSubmit: function () {
      this.loading = true;

      this.submitted = true;

      // stop here if form is invalid
      this.$v.$touch();
      this.$nextTick(() => {
        if (document.querySelector('.is-invalid') !== null && document.querySelector('.is-invalid') !== undefined) {
          document.querySelector('.is-invalid').scrollIntoView({block: "center", behavior: "smooth"})
        } else if (document.querySelector('.invalid-feedback') !== null && document.querySelector('.invalid-feedback') !== undefined) {
          document.querySelector('.invalid-feedback').scrollIntoView({block: "center", behavior: "smooth"})
        }
      })
      if (this.$v.patientData.$invalid) {
        this.loading = false;
        return;
      }

      if (validateForm("patientDataForm")) {

        post('patient_save', this.patientData)
            .then((response) => {
              this.loading = false;
              if (response.data.status !== undefined && response.data.status === true) {
                // this.patientData.dob = new Date(moment(this.patientData.dob).format("YYYY-MM-DD"));
                displayMessage(response.data.message);
                this.isEditProfile = false ;
              } else {
                // this.patientData.dob = new Date(moment(this.patientData.dob).format("YYYY-MM-DD"));
                displayErrorMessage(response.data.message)
              }

            })
            .catch((error) => {
              // this.patientData.dob = new Date(moment(this.patientData.dob).format("YYYY-MM-DD"));
              console.log(error);
              this.loading = false;
              displayErrorMessage(this.formTranslation.common.internal_server_error)
            })
      }

    },
  },
  computed:{
    userData() {
      return this.$store.state.userDataModule.user;
    },
    appointment_encounter_data(){
      return this.$store.state.appointmentModule.appointment_encounter_data
    }
  }
}

</script>

<style scoped>

li {
  border-radius:4px;
  cursor: pointer;
}
.active-li{
  background-color: var(--primary) !important;
}
.active-li i{
  color: #fff !important;
}

.active-li span{
  color: #fff !important;
}
</style>