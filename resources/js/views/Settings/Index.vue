<template>
  <div>
    <b-row>
      <div class="col-3">
        <div class="card">
          <div class="card-header">
            <div class="row" id="dashboard-setting-panel">
              <div class="col-md-12">
                <ul class="navbar-nav iq-navbar-ul">
                  <b-nav-item class="mt-2" v-if="getUserRole() === 'doctor' || getUserRole() === 'receptionist' && kcCheckPermission('doctor_session_list')" :to="{ name: 'doctor.session.setting'}" :link-classes="(currentRouteModule === 'doctor_session' ? activeRouteClass : '' )" >
                    <i class="fa fa-calendar text-primary m-2" aria-hidden="true" v-b-tooltip.hover :title="formTranslation.doctor_session.doc_sessions"></i>
                    <span>{{$t('doctor_session.doc_sessions')}}</span>
                  </b-nav-item>
                  <b-nav-item  class="mt-2" v-if=" getUserRole() === 'administrator'" :to="{ name: 'setting.general-setting'}"  :link-classes="(currentRouteModule === 'general_setting' ? activeRouteClass : '' )" style="position: relative;" >
                    <i class="fa fa-cogs text-primary m-2" v-b-tooltip.hover :title="formTranslation.settings.general + ' ' +formTranslation.common.settings" ></i>
                    <span>{{$t('settings.general') + ' '+$t('common.settings')}}</span>
                  </b-nav-item>
                  <b-nav-item class="mt-2" v-if="getUserRole() === 'administrator' || getUserRole() === 'clinic_admin' || getUserRole() === 'doctor' || getUserRole() === 'receptionist'" :to="{ name: 'clinic.schedule'}" :link-classes="(currentRouteModule === 'clinic_schedule' ? activeRouteClass : '' )" >
                    <i class="fa fa-home text-primary m-2" v-b-tooltip.hover :title="formTranslation.settings.holidays"></i>
                    <span>{{$t('settings.holidays')}}</span>
                  </b-nav-item>
                  <b-nav-item :title=" !userData.addOns.telemed && !userData.addOns.googlemeet? 'Available in Kivicare Telemed or googlemeet addon ': '' " class="mt-2" v-if="getUserRole() === 'doctor'" :to="{ name: 'doctor.telemed.setting'}" :link-classes="(currentRouteModule === 'telemed' ? activeRouteClass : '' )" :disabled="!userData.addOns.telemed && !userData.addOns.googlemeet">
                    <i class="fa fa-video text-primary m-2" v-b-tooltip.hover :title="formTranslation.common.telemed"></i>
                    <span>{{$t('common.telemed')}}</span>
                    <span v-if="!userData.addOns.telemed && !userData.addOns.googlemeet"  v-html="kivicareTelemedIcon">
                    </span>
                  </b-nav-item>
                  <b-nav-item class="mt-2" v-if="getUserRole() === 'administrator'" :to="{ name: 'clinic.configuration'}" >
                    <i class="fa fa-toggle-on text-primary m-2" v-b-tooltip.hover :title="formTranslation.settings.configurations"></i>
                    <span>{{$t('settings.configurations')}}</span>
                  </b-nav-item>
                  <b-nav-item class="mt-2" v-if="getUserRole() === 'administrator'" :to="{ name: 'email.template'}">
                    <i class="fa fa-envelope text-primary m-2" v-b-tooltip.hover :title="formTranslation.settings.email_template"></i>
                    <span>{{$t('settings.email_template')}}</span>
                  </b-nav-item>
                  <b-nav-item :title="userData.addOns.kiviPro != true ? 'Available in Kivicare Pro' : '' " class="mt-2" v-if=" (getUserRole() === 'administrator')" :to="{ name: 'sms.template'}" >
                    <i class="fas fa-sms text-primary m-2" v-b-tooltip.hover :title="formTranslation.settings.sms_template"></i>
                    <span>{{$t('settings.sms_template')}}</span>
                    <span v-if="userData.addOns.kiviPro != true" v-html="kivicareProIcon">
                    </span>
                  </b-nav-item>
                  <b-nav-item class="mt-2" v-if="getUserRole() === 'administrator'" :to="{ name: 'setting.patient_setting'}">
                    <i class="fas fa-user-injured text-primary m-2" v-b-tooltip.hover :title="formTranslation.settings.patient_setting"></i>
                    <span>{{$t('settings.patient_setting')}}</span>
                  </b-nav-item>
                  <b-nav-item class="mt-2" v-if="(getUserRole() === 'administrator')" :to="{ name: 'setting.widget_setting'}">
                    <i class="fa fa-calendar text-primary m-2" v-b-tooltip.hover :title="formTranslation.widget_setting.widget_setting"></i>
                    <span>{{$t('widget_setting.widget_setting')}}</span>
                  </b-nav-item>
                  <b-nav-item :title="userData.addOns.kiviPro != true ? 'Available in Kivicare Pro' : '' " class="mt-2" v-if="getUserRole() === 'administrator'" :to="{ name: 'google.event.template'}" >
                    <i class="fa fa-calendar-day text-primary m-2" v-b-tooltip.hover :title="formTranslation.google_event.template"></i>
                    <span>{{$t('google_event.template')}}</span>
                    <span v-if="userData.addOns.kiviPro != true" v-html="kivicareProIcon">
                    </span>
                  </b-nav-item>
                  <b-nav-item :title="userData.addOns.googlemeet != true ? 'Available in Kivicare Googlemeet Addon' : '' " class="mt-2" v-if=" getUserRole() === 'administrator'" :to="{ name: 'setting.googlemeet'}" >
                    <i class="fa fa-video text-primary m-2" v-b-tooltip.hover :title="formTranslation.googlemeet.googlemeet"></i>
                    <span>{{$t('googlemeet.googlemeet')}}</span>
                    <span v-if="userData.addOns.googlemeet != true" v-html="kivicareGoogleMeetIcon">
                    </span>
                  </b-nav-item>
                  <b-nav-item class="mt-2" v-if="getUserRole() !== 'patient' && kcCheckPermission('static_data_list')" :to="{ name: 'static.data'}" :link-classes="(currentRouteModule === 'static_data' ? activeRouteClass : '' )" >
                    <i class="fa fa-list-alt text-primary m-2" v-b-tooltip.hover :title="formTranslation.settings.listings"></i>
                    <span>{{$t('settings.listings')}}</span>
                  </b-nav-item>
                  <b-nav-item class="mt-2" v-if="checkEnableModule('custom_fields') && getUserRole() === 'administrator' || getUserRole() === 'clinic_admin'  " :to="{ name: 'custom.field'}" >
                    <i class="fa fa-magic text-primary m-2" v-b-tooltip.hover :title="formTranslation.settings.custom_field"></i>
                    <span>{{$t('settings.custom_field')}}</span>
                  </b-nav-item>
                  <b-nav-item :title="userData.addOns.kiviPro != true ? 'Available in Kivicare Pro' : '' " class="mt-2" v-if=" getUserRole() === 'administrator' " :to="{ name: 'setting.comman_settings'}" :link-classes="(currentRouteModule === 'comman_settings' ? activeRouteClass : '' )" >
                    <i class="fa fa-wrench text-primary m-2" v-b-tooltip.hover :title="formTranslation.settings.pro_settings"></i>
                    <span>{{$t('settings.pro_settings')}}</span>
                    <span v-if="userData.addOns.kiviPro != true" v-html="kivicareProIcon">
                    </span>
                  </b-nav-item>
                  <b-nav-item  class="mt-2" v-if="(getUserRole() === 'administrator')" :to="{ name: 'payment.setting'}" >
                    <i class="fa fa-wallet text-primary m-2" v-b-tooltip.hover :title="formTranslation.settings.payment"></i>
                    <span>{{$t('settings.payment')}}</span>
                  </b-nav-item>
                  <b-nav-item :title="userData.addOns.kiviPro != true ? 'Available in Kivicare Pro' : '' " class="mt-2" v-if=" getUserRole() !== 'doctor' && newUser == 'false'" :to="{ name: 'setting.language_settings'}" :link-classes="(currentRouteModule === 'language_settings' ? activeRouteClass : '' )" :disabled="userData.addOns.kiviPro != true">
                    <i class="fa fa-language text-primary m-2" v-b-tooltip.hover :title="formTranslation.settings.language_settings"></i>
                    <span>{{ $t('settings.language_settings') }}</span>
                    <span v-if="userData.addOns.kiviPro != true" v-html="kivicareProIcon">
                    </span>
                  </b-nav-item>
                  <b-nav-item :title="userData.addOns.kiviPro != true ? 'Available in Kivicare Pro' : '' " class="mt-2" v-if="userData.is_enable_google_cal == 'on' && (getUserRole() === 'doctor'  || getUserRole() === 'receptionist')" :to="{ name: 'setting.google_calender'}" :link-classes="(currentRouteModule === 'google_calender' ? activeRouteClass : '' )" :disabled="userData.addOns.kiviPro != true">
                    <i class="fa fa-calendar-day text-primary m-2" v-b-tooltip.hover :title="formTranslation.common.google_calendar_intergration"></i>
                    <span>{{$t('common.google_calendar_intergration')}}</span>
                    <span v-if="userData.addOns.kiviPro != true" v-html="kivicareProIcon">
                    </span>
                  </b-nav-item>
                  <b-nav-item  :title="userData.addOns.googlemeet != true ? 'Available in Kivicare Googlemeet Addon' : '' " class="mt-2" v-if=" getUserRole() === 'doctor' && userData.is_enable_googleMeet == 'on'" :to="{ name: 'setting.google_meet_config'}" :link-classes="(currentRouteModule === 'google_meet_config' ? activeRouteClass : '' )" :disabled="userData.addOns.googlemeet != true">
                    <i class="fa fa-video text-primary m-2" v-b-tooltip.hover :title="formTranslation.googlemeet.google_meet_intergration"></i>
                    <span>{{$t('googlemeet.google_meet_intergration')}}</span>
                    <span v-if="userData.addOns.googlemeet != true" v-html="kivicareGoogleMeetIcon">
                    </span>
                  </b-nav-item>
                  <b-nav-item class="mt-2" v-if="getUserRole() === 'administrator'" :to="{ name: 'setting.appointment-setting'}">
                    <i class="fas fa-calendar-week text-primary m-2" v-b-tooltip.hover :title="formTranslation.appointments.restrict_appointment"></i>
                    <span>{{$t('appointments.restrict_appointment')}}</span>
                  </b-nav-item>
                  <!--<b-nav-item class="mt-2" v-if="getUserRole() === 'administrator'" :to="{ name: 'setting.permission-setting'}">
                    <i class="fas fa-lock text-primary m-2" v-b-tooltip.hover :title="formTranslation.settings.permission_setting"></i>
                    <span>{{$t('settings.permission_setting')}}</span>
                  </b-nav-item>-->
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-9">
        <div>
            <div>
              <transition name="fade" mode="out-in">
                <router-view></router-view>
              </transition>
            </div>
        </div>
      </div>
    </b-row>
  </div>
</template>
<script>
  import { post, get } from "../../config/request";
  export default {
      data: () => {
        return {
          activeRouteClass: 'router-link-exact-active router-link-active',
          newUser:window.request_data.new_user,
          showHardtLink: 'off',
          kivicareProIcon:'',
          kivicareTelemedIcon:'',
          kivicareGoogleMeetIcon:'',
          kivicareProAndAddonIcon:'',
        }
      },
      methods: {
        isWoocoommercePaymentEnabled: function () {
          get('service_list', {})
          .then((data) => {
          })
        },
        getRequestHelper:function(){
           if(window.request_data.link_show_hide !== undefined && window.request_data.link_show_hide !== ''){
             this.showHardtLink = window.request_data.link_show_hide;
           }
        },
      },
      mounted(){
         this.getRequestHelper();
         this.kivicareProIcon = this.kivicareProFeatureIcon('pro');
         this.kivicareTelemedIcon = this.kivicareProFeatureIcon('telemed');
         this.kivicareGoogleMeetIcon = this.kivicareProFeatureIcon('googlemeet');
         this.kivicareProAndAddonIcon = this.kivicareProFeatureIcon('pro');
      },
      computed: {
        currentRouteModule: function () {
          if (window.innerWidth < 1500) {
            let wrapperMenu = document.querySelector('.main-content .sidenav-toggler')
            if (wrapperMenu) {
              wrapperMenu.classList.remove('active')
              document.querySelector('.main-content .sidenav-toggler').classList.remove('active')
              document.querySelector('body').classList.add('g-sidenav-hidden')
              localStorage.setItem('sidebarToggle', false)
            }
          }
          return this.$route.meta.module;
        },
        isWoocommerceEnabled () {
          if (this.$store.state.userDataModule !== undefined && this.$store.state.userDataModule.user) {
            return this.$store.state.userDataModule.user.woocommercePayment ;
          }
        },
        userData () {
          return this.$store.state.userDataModule.user;
        }
      }
  }
</script>