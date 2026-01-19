<template>
    <nav :class="`sidenav navbar navbar-vertical navbar-expand-xs ${mode == 'true' ? 'fixed-right' : ' fixed-left'} navbar-light bg-white`" id="sidenav-main"
         @mouseenter="handleMouseIn" @mouseleave="handleMouseOut">
        <div id="scrollbar-inner" class="scrollbar-inner">
            <!-- Brand -->
            <div class="sidenav-header  d-flex  align-items-center">
                <b-navbar-brand :to="{ name: 'dashboard' }" class="p-0">
                    <img v-if="getSiteLogo!= -1 && getSiteLogo!= null && getSiteLogo!=''" :src="getSiteLogo" class="navbar-brand-img ml-2" alt="get" >
                    <img :src="logoURL" class="navbar-brand-img ml-2" alt="else" v-else>
                </b-navbar-brand>
                <div class=" ml-auto ">
                    <!-- Sidenav toggler -->
                    <div class="sidenav-toggler active" data-action="sidenav-unpin"
                         data-target="#sidenav-main" @click="handleSideBarToggle">
                        <div class="sidenav-toggler-inner">
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="navbar-inner kivicare-navbar-inner">
                <!-- Collapse -->
                <div class="collapse navbar-collapse" id="sidenav-collapse-main">
                    <!-- Nav items -->
                    <ul class="navbar-nav">
                        <b-nav-item v-if="kcCheckPermission('home_page') && !(getUserRole() === 'administrator')" :href="homeUrl" target="_blank">
                            <i class="fas fa-home text-primary"></i>
                            <span class="nav-link-text">{{$t('widgets.home') }}</span>
                        </b-nav-item>
                        <b-nav-item :to="{ name: 'dashboard' }">
                            <i class="fa fa-tachometer-alt text-primary"></i>
                           <span class="nav-link-text">{{$t('dashboard.dashboard') }} </span>
                        </b-nav-item>
                        <b-nav-item :to="{ name: 'appointment-list.index' }"
                                    :link-classes="(currentRouteModule === 'appointment_list' ? activeRouteClass : '' )"
                                    v-if="kcCheckPermission('appointment_list')">
                            <i class="fas fa-calendar-week text-primary"></i>
                            <span class="nav-link-text"> {{$t('appointments.appointments')}}</span>
                        </b-nav-item>
                        <b-nav-item :to="{ name: 'encounter-list' }"
                                    :link-classes="(currentRouteModule === 'patient_encounter' ? activeRouteClass : '' )"
                                    v-if="kcCheckPermission('patient_encounters')">
                          <i class="far fa-calendar-times text-primary"></i>
                          <span class="nav-link-text"> {{$t('patient_encounter.encounters')}} </span>
                        </b-nav-item>
                        <b-nav-item 
                                :to="{ name: 'clinic' }"
                                :link-classes="(currentRouteModule === 'clinic' ? activeRouteClass : '' )"
                                v-if="getUserRole() === 'administrator'">
                            <i class="fas fa-hospital text-primary"></i>
                           <span class="nav-link-text">{{$t('clinic.clinic')}}</span>
                          <span v-if="userData.addOns.kiviPro != true" v-html="kivicareProFeatureIcon('pro')">
                          </span>
                        </b-nav-item>
                        <b-nav-item :to="{ name: 'patient' }"
                                    :link-classes="(currentRouteModule === 'patient' ? activeRouteClass : '' )"
                                    v-if="kcCheckPermission('patient_list')">
                            <i class="fas fa-hospital-user text-primary"></i>
                            <span class="nav-link-text">{{$t('dashboard.patients')}}</span>
                        </b-nav-item>
                        <b-nav-item :to="{ name: 'doctor' }"
                                    :link-classes="(currentRouteModule === 'doctor' ? activeRouteClass : '' )"
                                    v-if="kcCheckPermission('doctor_list')">
                            <i class="fa fa-user-md text-primary"></i>
                            <span class="nav-link-text">{{$t('common.doctors')}}</span>
                            <i class="fas fa-question-circle ml-1  text-danger" v-if="!userData.doctor_available" v-b-tooltip.hover title="Please Add Doctor"></i>
                        </b-nav-item>
                        <b-nav-item :to="{ name: 'receptionist' }"
                                    :link-classes="(currentRouteModule === 'receptionist' ? activeRouteClass : '' )"
                                    v-if="kcCheckPermission('receptionist_list') && checkEnableModule('receptionist')">
                            <i class="fa fa-users text-primary"></i>
                            <span class="nav-link-text"> {{$t('clinic.receptionist')}} </span>
                        </b-nav-item>
                        <b-nav-item :to="{ name: 'service' }"
                                    :link-classes="(currentRouteModule === 'service' ? activeRouteClass : '' )"
                                    v-if="getUserRole() !== 'patient' && kcCheckPermission('service_list')">
                            <i class="fa fa-server text-primary"></i>
                            <span class="nav-link-text">{{$t('common.services')}} </span>
                          <i class="fas fa-question-circle ml-1  text-danger" v-if="!userData.doctor_service_available" v-b-tooltip.hover title="Please Add Doctor Service"></i>
                        </b-nav-item>
                        <b-nav-item :to="{ name: 'doctor-session.create' }"
                                    :link-classes="(currentRouteModule === 'doctor_session' ? activeRouteClass : '' )"
                                    v-if="kcCheckPermission('doctor_session_list') && getUserRole() !== 'receptionist' && getUserRole() !== 'doctor'">
                          <i class="fa fa-calendar text-primary" aria-hidden="true"></i>
                          <span class="nav-link-text">{{$t('doctor_session.doc_sessions')}}</span>
                          <i class="fas fa-question-circle ml-1  text-danger" v-if="!userData.doctor_session_available" v-b-tooltip.hover title="Please Add Doctor Session"></i>
                        </b-nav-item>
                        <b-nav-item :to="{ name: 'billings' }" v-if="kcCheckPermission('patient_bill_list') && checkEnableModule('billing')">
                          <i class="fa fa-file-invoice text-primary"></i>
                          <span class="nav-link-text">{{$t('patient_bill.billing_records')}}</span>
                          <span v-if="userData.addOns.kiviPro != true" v-html="kivicareProFeatureIcon('pro')">
                          </span>
                      </b-nav-item>
                        <b-nav-item :to="{ name: 'clinic-revenue-reports' }"
                                    v-if="(getUserRole() === 'administrator' || getUserRole() === 'clinic_admin')">
                            <i class="fas fa-chart-line text-primary"></i>
                           <span class="nav-link-text">{{$t('reports.reports')}}</span>
                          <span v-if="userData.addOns.kiviPro != true" v-html="kivicareProFeatureIcon('pro')">
                          </span>
                        </b-nav-item>
                        <b-nav-item
                            v-if="getUserRole() === 'administrator' || getUserRole() === 'clinic_admin'"
                            :to="getUserRole() === 'administrator' && showSupportLink != 'deleted'  ?   { name: 'setting.general-setting' } : { name: 'clinic.schedule' }"
                            :link-classes="(currentRouteModule === 'settings' ? activeRouteClass : '' )" >
                          <i class="fa fa-cogs text-primary"></i>
                          <span class="nav-link-text"> {{$t('common.settings')}} </span>
                        </b-nav-item>
                        <b-nav-item
                            v-if="getUserRole() === 'doctor' || getUserRole() === 'receptionist'"
                            :to="{ name: 'doctor.session.setting' }"
                            :link-classes="(currentRouteModule === 'settings' || currentRouteModule === 'telemed' || currentRouteModule === 'clinic_schedule' ? activeRouteClass : '' )" >
                          <i class="fa fa-cogs text-primary"></i>
                          <span class="nav-link-text"> {{$t('common.settings')}} </span>
                        </b-nav-item>
                        <b-nav-item class="mt-2" 
                            v-if="getUserRole() === 'administrator'" 
                            :to="{ name: 'get_help' }"
                            > <i class="fas fa-question-circle text-primary"></i>  <span class="nav-link-text"> Get help </span>  
                        </b-nav-item>
                        <b-nav-item class="mt-2" 
                            v-if="(getUserRole() === 'doctor' || getUserRole() === 'administrator') && userData.addOns.kiviPro != true"
                            :to="{ name: 'get_pro' }"
                            > <i class="fas fa-question-circle text-primary"></i>  <span class="nav-link-text"> Get Pro </span>  
                        </b-nav-item>
                        <b-nav-item  
                            v-if="getUserRole() === 'administrator'  && request_status == 'off'"
                            href="https://iqonic.design/feature-request/?for_product=kivicare"
                            target="_blank"
                            > <i class="fas fa-external-link-alt text-primary"></i>  
                                <span class="nav-link-text"> {{ $t('common.request_features') }} </span>
                        </b-nav-item>
                        <b-nav-item :to="{ name: 'patient-medical-report_id',params:{ patient_id: userData.ID } }"
                                    v-if="getUserRole() === 'patient' && kcCheckPermission('patient_report')">
                          <i class="fa fa-file text-primary"></i>
                          <span class="nav-link-text">{{$t('reports.reports')}}</span>
                          <span v-if="userData.addOns.kiviPro != true" v-html="kivicareProFeatureIcon('pro')">
                          </span>
                        </b-nav-item>
                         <b-nav-item :to="{ name: 'patient-clinic' }"
                                    v-if="getUserRole() === 'patient' && kcCheckPermission('patient_clinic')">
                            <i class="fas fa-hospital text-primary"></i>
                           <span class="nav-link-text">{{$t('clinic.clinic')}}</span>
                           <span v-if="userData.addOns.kiviPro != true" v-html="kivicareProFeatureIcon('pro')">
                          </span>
                        </b-nav-item>
                    </ul>
                </div>
            </div>
        </div>
        <slot />
    </nav>
</template>
<script>

import {post} from "../../config/request";

export default {
    props: ['mode'],
    data: () => {
        return {
            patient_id: '',
            logoURL: '',
            settingOption: ['custom-field', 'static-data', 'static-data.create', 'static-data.edit', 'custom-field.create', 'custom-field.edit', 'clinic-schedule', 'notification.create', 'terms-condition.create', 'clinic-schedule.index', 'common-settings.create', 'clinic-schedule.create', 'clinic-schedule.edit'],
            activeRouteClass: 'router-link-exact-active router-link-active',
            homeUrl:window.request_data.homePage,
            moduleList:[],
            request_status:'off',
            showSupportLink: 'off'
        }
    },
    mounted() {
        this.init();
        setTimeout(()=> { this.kiviPro }, 1000);
        this.logoURL = window.request_data.kiviCarePluginURL + 'assets/images/logo-banner.png';
        this.getModule();
        this.getRequestHelper();
    },
    methods: {
        getRequestHelper:function(){
            if(window.request_data.link_show_hide !== undefined && window.request_data.link_show_hide !== ''){
              this.showSupportLink = window.request_data.link_show_hide;
            }
        },
        init: function () {
            if (localStorage.getItem('sidebarToggle') === "false") {
                this.$store.commit('TOGGLE_SIDEBAR', false)
                this.handleMouseOut();
            } else {
                $('body').addClass('g-sidenav-show g-sidenav-pinned');
                this.$store.commit('TOGGLE_SIDEBAR', true)
            }

            new window.PerfectScrollbar('#sidenav-main', {
                wheelPropagation: false
            });
        },
        handleMouseOut: function () {
            if (!this.fullSideBar) {
                $('.sidenav .navbar-brand').hide();
                $('.sidenav-toggler').removeClass('active');
                $('body').removeClass('g-sidenav-show').addClass('g-sidenav-hidden');
                $('body').removeClass('g-sidenav-pinned');
            }
        },
        handleMouseIn: function () {
            if (!this.fullSideBar) {
                $('.sidenav .navbar-brand').show();
                $('body').addClass('g-sidenav-show').removeClass('g-sidenav-hidden');
            }
        },
        backToWordpress: function () {
            window.location = window.location.href.split("admin.php")[0];
        },
        handleSideBarToggle: function () {
            this.$store.commit('TOGGLE_SIDEBAR', !this.fullSideBar)
        },
        getModule:function (){
          if(window.request_data.link_show_hide !== undefined && window.request_data.link_show_hide !== ''){
            this.request_status = window.request_data.link_show_hide;
          }
        }
    },
    watch: {
        fullSideBar(newVal) {
            if (newVal) {
                $('.sidenav-toggler').addClass('active');
                $('.sidenav-toggler').data('action', 'sidenav-unpin');
                $('body').removeClass('g-sidenav-hidden').addClass('g-sidenav-show g-sidenav-pinned');
                localStorage.setItem('sidebarToggle', true)
            } else {
                $('.sidenav-toggler').removeClass('active');
                $('.sidenav-toggler').data('action', 'sidenav-pin');
                $('body').removeClass('g-sidenav-pinned').addClass('g-sidenav-hidden');
                $('body').find('.backdrop').remove();
                localStorage.setItem('sidebarToggle', false)
            }
        },
        logout: function () {
            this.$store.dispatch("logout", { self: this })
        }
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
        fullSideBar: function () {
            return this.$store.state.fullSideBar;
        },
        userData() {
            return this.$store.state.userDataModule.user;
        },
        teleMedEn () {
            return this.userData.addOns.telemed
        },
        kiviPro (){
            return this.userData.addOns.kiviPro
        },
        getSiteLogo(){
            return this.userData.site_logo
        }
    }
}
</script>
