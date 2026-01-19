<template>
<div>
  <template>
    <div>
      <div class="page-loader-section" v-if="formLoader">
        <loader-component-2></loader-component-2>
      </div>
      <div v-else class="accordion" role="tablist">
        <b-card no-body class="mb-1" v-if="userData.addOns.telemed">
          <b-card-header header-tag="header"  role="tab">
            <b-button block v-b-toggle.zoom-collapse variant="white" class="d-flex align-items-center justify-content-between" > <span class="text-primary">  Zoom </span> <i class="fas fa-sort-down p-0" v-if="isTabActive.zoomCollapse" ></i> <i v-else class="fas fa-caret-right p-0" ></i> </b-button>
          </b-card-header>
          <b-collapse id="zoom-collapse" :visible="userData.addOns.telemed" accordion="my-accordion" role="tabpanel">
            <b-card-body>
              <div class="row mt-3">
                <div class="col-md-5">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
                        <b-form-checkbox
                            size="lg"
                            v-model="configurationData.enableTeleMed"
                            name="check-button"
                            @change="zoomStatusChangeEvent"
                            switch>
                        <span style="top: 0px !important;">
                          <small>
                            ({{$t('common.status')}}: {{
                              configurationData.enableTeleMed === true || configurationData.enableTeleMed === 'true' ? "On" : "Off"
                            }})
                          </small></span>
                        </b-form-checkbox>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="form-group">
                        <label for="api_key" class="form-control-label">{{$t('doctor.api_key')}} <span class="text-danger">*</span></label>
                        <input id="api_key"
                               :class="{ ' is-invalid': submitted && $v.configurationData.api_key.$error }"
                               v-model="configurationData.api_key"
                               :placeholder="formTranslation.zoom_config.plh_api_key"
                               name="api_key" type="text" class="form-control">
                        <div v-if="submitted && !$v.configurationData.api_key.required"
                             class="invalid-feedback">{{$t('doctor.api_key_required')}}
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="form-group">
                        <label for="api_secret" class="form-control-label">{{$t('doctor.api_secret')}} <span
                            class="text-danger">*</span></label>
                        <input id="api_secret"
                               :class="{ ' is-invalid': submitted && $v.configurationData.api_secret.$error }"
                               v-model="configurationData.api_secret" :placeholder="formTranslation.zoom_config.plh_api_secret"
                               name="api_secret" type="text" class="form-control">
                        <div v-if="submitted && !$v.configurationData.api_secret.required"
                             class="invalid-feedback">{{$t('doctor.api_secret_required')}}
                        </div>
                      </div>
                    </div>
<!--                    <div class="col-md-12">-->
<!--                      <label for="video_price" class="form-control-label">-->
<!--                        {{ $t('doctor.video_consultation_fees') }}<span-->
<!--                          class="text-danger">*</span></label>-->
<!--                      <div>-->
<!--                        <input-->
<!--                            id="video_price"-->
<!--                            v-model="configurationData.video_price"-->
<!--                            :placeholder="formTranslation.doctor.consultation_fees" name="video_price"-->
<!--                            min="0"-->
<!--                            type="number"-->
<!--                            class="form-control"-->
<!--                            :class="{ ' is-invalid': submitted && $v.configurationData.video_price.$error }"-->
<!--                        >-->
<!--                        <div v-if="submitted && !$v.configurationData.video_price.required"-->
<!--                             class="invalid-feedback">{{ $t('doctor.doctor_fees_required') }}-->
<!--                        </div>-->
<!--                      </div>-->
<!--                    </div>-->
                  </div>
                </div>
                <div class="col-md-7">
                  <div class="row">
                    <div class="col-md-12">
                      <h4 class="text-primary mb-3">{{$t('doctor.zoom_configuration_guide')}}</h4>
                    </div>
                  </div>
                  <b-list-group>
                    <b-list-group-item>{{$t('doctor.zoom_step1')}} : <a href="https://marketplace.zoom.us/"
                                                                        target="_blank"> {{$t('doctor.zoom_market_place_portal')}}</a>
                    </b-list-group-item>
                    <b-list-group-item>{{$t('doctor.zoom_step2')}}
                      <a href="https://marketplace.zoom.us/develop/create" target="_blank">{{$t('doctor.create_app')}}</a>
                    </b-list-group-item>
                    <b-list-group-item>{{$t('doctor.zoom_step3')}}</b-list-group-item>
                    <b-list-group-item>{{$t('doctor.zoom_step4')}}
                    </b-list-group-item>
                    <b-list-group-item>{{$t('doctor.zoom_step5')}}
                    </b-list-group-item>
                  </b-list-group>
                </div>
              </div>
              <div class="row mt-3">
                <div class="col-md-12 p-0">
                  <div class="float-right">
                    <button
                        v-if="!loading"
                        @click="handleSubmit"
                        class="btn btn-primary" type="submit"
                        v-html="buttonText"></button>
                    <button v-else class="btn btn-primary" type="submit" disabled>
                      <i class="fa fa-sync fa-spin"></i>&nbsp; {{$t('common.loading')}}
                    </button>
                  </div>
                </div>
              </div>
            </b-card-body>
          </b-collapse>
        </b-card>
        <b-card no-body  v-if="userData.addOns.googlemeet" class="mb-1">
          <b-card-header header-tag="header" role="tab">
            <b-button block v-b-toggle.google-meet-collapse variant="white" class="d-flex align-items-center justify-content-between" > <span class="text-primary"> Google Meet </span>  <i class="fas fa-sort-down p-0" v-if="isTabActive.googleMeetCollapse"></i> <i v-else class="fas fa-caret-right p-0" ></i> </b-button>
          </b-card-header>
          <b-collapse id="google-meet-collapse" :visible="userData.addOns.googlemeet" accordion="my-accordion" role="tabpanel">
            <b-card-body>
              <div class="row p-3"  v-if="userData.is_enable_doctor_gmeet == 'off'" >
                <b-col class="d-flex justify-content-between" md="12" >
                  <p style="font-weight: 500;">{{$t('common.please_connect_google_meet_automatically')}}</p>
                  <b-button
                      class="float-right btn btn-sm btn-primary" type="button"
                      variant="primary"
                      @click ="connect">
                    {{$t('pro_setting.connect_with_google')}}
                  </b-button>
                </b-col>
              </div>
              <b-row v-else>
                <b-col md="12" class="p-3 pb-0" >
                  <div class="d-flex justify-content-between" style="margin: 30px 15px;">
                    <p style="font-weight: 500;">{{$t('common.connected_with_google_meet')}} </p>
                    <b-button
                        class="float-right btn btn-sm btn-primary"
                        type="button"
                        variant="primary"
                        @click ="closeConnection">
                      {{$t('common.disconnect')}}
                    </b-button>
                  </div>
                </b-col>
<!--                <b-col md="12" class="p-3 pb-0">-->
<!--                  <p for="video_price" class="pl-3" style="font-weight: 500;">-->
<!--                    {{ $t('doctor.video_consultation_fees') }}<span-->
<!--                      class="text-danger"></span></p>-->
<!--                  <div class="d-flex justify-content-between" style="margin: 30px 15px;">-->
<!--                    <input-->
<!--                        id="video_price"-->
<!--                        v-model="googlemeet.video_price"-->
<!--                        :placeholder="formTranslation.doctor.consultation_fees" name="video_price"-->
<!--                        min="0"-->
<!--                        type="number"-->
<!--                        class="form-control w-50"-->
<!--                        :class="{ ' is-invalid': googlemeetSubmitted && $v.googlemeet.video_price.$error }"-->
<!--                    >-->
<!--                    <div v-if="googlemeetSubmitted && !$v.googlemeet.video_price.required"-->
<!--                         class="invalid-feedback">{{ $t('doctor.doctor_fees_required') }}-->
<!--                    </div>-->
<!--                    <b-button-->
<!--                        class="btn-sm"-->
<!--                        type="button"-->
<!--                        variant="primary"-->
<!--                        @click ="savePrice">-->
<!--                      {{ googlemeetloading ? $t('common.loading') : $t('common.save')}}-->
<!--                    </b-button>-->
<!--                  </div>-->

<!--                </b-col>-->
              </b-row>
            </b-card-body>
          </b-collapse>
        </b-card>
      </div>
    </div>
  </template>
</div>

</template>
<script>
import {required} from "vuelidate/lib/validators";
import {validateForm} from "../../config/helper";
import {post,get} from "../../config/request";

export default {
  name: "ZoomConfiguration",
  validations: {
    configurationData: {
      api_key: {required},
      api_secret: {required},
      // video_price:{required}
    },
    googlemeet:{
      // video_price:{required}
    }
  },
  data: () => {
    return {
      buttonText: '<i class="fa fa-save"></i> Save configuration',
      configurationData: {
        enableTeleMed: false
      },
      googleMeet:{
        enableTeleMed: false
      },
      loading: false,
      submitted: false,
      cardTitle: 'Telemed configuration',
      showLogin: false,
      data: {},
      disconnect: false,
      text: "Anim pariatur asdadsadsadasd asdasdasdasasd asdadasd",
      isTabActive : {
        zoomCollapse: true,
        googleMeetCollapse: false
      },
      formLoader:true,
      googlemeetloading:false,
      googlemeetSubmitted:false,
      googlemeet:{
        video_price:0,
        telemed_service_id:0,
        doctor_id:0
      }
    }
  },
  mounted() {
    if(!['doctor'].includes(this.getUserRole())) {
      this.$router.push({ name: "403"})
    }
    this.init();
    this.telemedCollapseArrow();
    this.isTabActive.googleMeetCollapse = this.userData.addOns.googlemeet && !this.userData.addOns.telemed;
    this.isTabActive.zoomCollapse = this.userData.addOns.telemed;
    this.googlemeet = {
      video_price: this.userData.doctor_telemed_price,
      telemed_service_id:this.userData.telemed_service_id,
      doctor_id:this.userData.ID
    };
  },
  methods: {
    telemedCollapseArrow () {
      this.$root.$on('bv::collapse::state', (collapseId, isJustShown) => {
          if(collapseId === 'zoom-collapse') {
           if(isJustShown) { this.isTabActive.zoomCollapse = true ;  } else { this.isTabActive.zoomCollapse = false ; }
          } else {
            if(isJustShown) { this.isTabActive.googleMeetCollapse = true ;  } else { this.isTabActive.googleMeetCollapse = false ; }
          } 
      });
    },
    zoomStatusChangeEvent (value) {
      if(value && this.userData.is_enable_doctor_gmeet == 'on') {
        $.confirm({
          title: 'Enable Zoom' ,
          content: 'Note: You can use one meeting service at the time. We are disabling Google Meet service.' ,
          type: 'red',
          buttons: {
            ok: {
              text: this.formTranslation.common.yes,
              btnClass: 'btn-danger',
              keys: ['enter'],
              action: () => {
                this.googleMeet.enableTeleMed = false;
              }
            },
            cancel: {
              text:this.formTranslation.common.cancel,
              action: () => {
                this.configurationData.enableTeleMed = false;
              }
            }
          }
        });
      }
    },
    gmeetStatusChange (value) {
      if(value && this.configurationData.enableTeleMed) {
        $.confirm({
          title: 'Enable Google Meet ' ,
          content: 'Note: You can use one meeting service at the time. We are disabling Zoom Service.' ,
          type: 'red',
          buttons: {
            ok: {
              text: this.formTranslation.common.yes,
              btnClass: 'btn-danger',
              keys: ['enter'],
              action: () => {
                this.configurationData.enableTeleMed = false;
              }
            },
            cancel: {
              text:this.formTranslation.common.cancel,
              action: () => {
                this.googleMeet.enableTeleMed = false;
              }
            }
          }
        });
      }
    },
    start() {
      const id = this.userData;
      let calendarConfig ={
        client_id: id.googlemeet_client_id,
        scope: "https://www.googleapis.com/auth/calendar",
      };
      if(id.googlemeet_app_name){
        calendarConfig.plugin_name = id.googlemeet_app_name;
      }
      gapi.load('auth2', function() {
        var auth2 = gapi.auth2.init(calendarConfig).then((response)=> {
        });
      });
    },
    init: function () {
      this.configurationData = this.defaultConfigurationData();
      this.getConfigurationData();
      this.start();
    },
    connectGoogle:function(){
      post('get_all_lang_option', {})
          .then((response) => {
            if (response.data.status !== undefined && response.data.status === true) {
            } else {
              displayErrorMessage(response.data.message)
            }

          })
          .catch((error) => {
            console.log(error);
            displayErrorMessage(this.formTranslation.common.internal_server_error)
          })
    },
    getConfigurationData: function () {
      this.formLoader = true
      get('get_doctor_zoom_configuration', {
        user_id: this.userId
      })
          .then((response) => {
            this.formLoader = false;
            if (response.data.status !== undefined && response.data.status === true) {
              this.configurationData = response.data.data;
            } else {
              displayErrorMessage(response.data.message)
            }

          })
          .catch((error) => {
            console.log(error);
            this.formLoader = false;
            displayErrorMessage(this.formTranslation.common.internal_server_error)
          })
    },
    defaultConfigurationData: function () {
      return {
        api_key: "",
        api_secret: "",
        doctor_id: "",
        enableTeleMed: false,
        video_price:0
      }
    },
    savePrice(){

      this.googlemeetSubmitted = true;

      // stop here if form is invalid
      this.$v.$touch();

      if (this.$v.googlemeet.$invalid) {
        return;
      }
      this.googlemeetloading = true;
      this.googlemeetSubmitted = false;

      post('save_doctor_googlemeet_data_save', this.googlemeet)
          .then((response) => {
            this.googlemeetloading = false;
            if (response.data.status !== undefined && response.data.status === true) {
              this.$store.dispatch("userDataModule/fetchUserData", {});
              displayMessage(response.data.message);
            } else {
              displayErrorMessage(response.data.message)
            }
          })
          .catch((error) => {
            console.log(error);
            this.googlemeetloading = false;
            displayErrorMessage(this.formTranslation.common.internal_server_error)
          })
    },
    handleSubmit: function () {

      this.loading = true;
      this.submitted = true;

      // stop here if form is invalid
      this.$v.$touch();

      if (this.$v.configurationData.$invalid) {
        this.loading = false;
        return;
      }

      if (validateForm("configurationDataForm")) {

        this.configurationData.doctor_id = this.userId

        let configRequest = {
          api_key: this.configurationData.api_key,
          api_secret: this.configurationData.api_secret,
          doctor_id: this.configurationData.doctor_id,
          enableTeleMed: this.configurationData.enableTeleMed,
          video_price:this.configurationData.video_price
        }

        post('save_doctor_zoom_configuration', configRequest)
          .then((response) => {
            this.loading = false;
            this.submitted = false;
            if (response.data.status !== undefined && response.data.status === true) {
              this.$store.dispatch("userDataModule/fetchUserData")
              displayMessage(response.data.message);
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
    connect() {
      if(this.configurationData.enableTeleMed) {
        $.confirm({
          title: 'Enable Google Meet ' ,
          content: 'Note: You can use one meeting service at the time. We are disabling Zoom Service.' ,
          type: 'red',
          buttons: {
            ok: {
              text: this.formTranslation.common.yes,
              btnClass: 'btn-danger',
              keys: ['enter'],
              action: () => {
                this.googleConnetct();
                this.configurationData.enableTeleMed = false;
              }
            },
            cancel: {
              text:this.formTranslation.common.cancel,
              action: () => {
                this.googleMeet.enableTeleMed = false;
              }
            }
          }
        });
      } else {
        this.googleConnetct()
      }
    },
    googleConnetct () {
      let calendarConfig_new ={
        client_id: this.userData.googlemeet_client_id,
        scope: "https://www.googleapis.com/auth/calendar",
      };
      if(this.userData.googlemeet_app_name){
        calendarConfig_new.plugin_name = this.userData.googlemeet_app_name;
      }
      var auth2 = gapi.auth2.init(calendarConfig_new);

      auth2.grantOfflineAccess().then(this.signInCallback);
    },
    closeConnection(){
      let doctor_id = this.userId
      post("diconnect_meet_doctor", {doctor_id: doctor_id})
          .then((response) => {
            if (
                response.data.status !== undefined &&
                response.data.status === true
            ) {
              this.$store.dispatch("userDataModule/fetchUserData", {});
              this.disconnect = false;
              displayMessage(response.data.message);
            }
          })
          .catch((error) => {
            console.log(error);
            displayErrorMessage(this.formTranslation.widgets.record_not_found);
          });
    },
    signInCallback(authResult) {
      if (authResult['code']) {
        let doctor_id = this.userId
        post("connect_meet_doctor", {doctor_id: doctor_id,code:authResult['code']})
          .then((response) => {
            if (
                response.data.status !== undefined &&
                response.data.status === true
            ) {
              this.$store.dispatch("userDataModule/fetchUserData", {});
              this.disconnect = true;
              displayMessage(response.data.message);
            }
          })
          .catch((error) => {
            console.log(error);
            displayErrorMessage(this.formTranslation.widgets.record_not_found);
          });
      } else {
        console.log('error')
      }
    }
  },
  computed: {
    userData() {
      return this.$store.state.userDataModule.user;
    },
    userId() {
      return this.$store.state.userDataModule.user.ID;
    },
    teleMedStatus() {
      return this.$store.state.userDataModule.user.teleMedStatus;
    }
  },
  watch: {}
}
</script>