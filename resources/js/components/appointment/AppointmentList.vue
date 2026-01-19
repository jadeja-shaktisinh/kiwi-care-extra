<template>
    <div>
      <div class="page-loader-section" v-if="isAppointmentLoading">
        <loader-component-2></loader-component-2>
      </div>
      <div v-if="isAppointmentLoading == false">
        <div v-if="appointmentList.length > 0" class="kc-appointment-list">
            <div class="row" v-for="(appointment, index) in appointmentList" :key="index">
                <div class="col-md-12">
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <div class="card mt-0 mb-0 p-0 shadow-none shadow--hover animated fadeInUp kc-appointment-list">
                                <div class="card-body p-3">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <h3 class="text-primary mb-0">{{ appointment.patient_name }} <span class="small font-weight-bold text-muted">({{ appointmentEndTimeFormat(appointment.appointment_start_time) + ' - ' + appointmentEndTimeFormat(appointment.appointment_end_time) }})</span></h3>
                                            <h4 class=" mb-0"> {{$t('common.doctor')}} :  <span class="text-primary"> {{ appointment.doctor_name }}  </span></h4>
                                            <h4 class=" mb-0" v-if="getUserRole() === 'administrator'"> {{$t('clinic.clinic')}} :  <span class="text-primary"> {{ appointment.clinic_name }}  </span></h4>
                                            <p v-if="enableDisableAppointmentDescriptionStatus == 'on'" class="mb-0 small"><b>{{$t('appointments.description')}} :</b> {{ appointment.description || " not found" }}</p>
                                        </div>
                                        <div class="col-md-3 d-flex align-items-center">
                                            <h5 class="text-muted p-3"> {{ appointment.type_label }} </h5>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-center">
                                            <span class="badge badge-primary" v-if="appointment.status === '1'">{{$t('appointments.booked')}}</span>
                                            <span class="badge badge-danger" v-if="appointment.status === '0'">{{$t('appointments.cancelled')}}</span>
                                            <span class="badge badge-success" v-if="appointment.status === '2'">{{$t('appointments.arrived')}}</span>
                                            <span class="badge badge-success" v-if="appointment.status === '4'">{{$t('appointments.check_in')}}</span>
                                            <span class="badge badge-danger" v-if="appointment.status === '3'">{{$t('appointments.check_out')}}</span>
                                        </div>
                                        <div class="col-md-3 d-flex align-items-center justify-content-end">
                                            <div class="float-right">
                                                <b-button-group size="sm">
                                                    <b-button variant="outline-primary" v-b-tooltip.hover :title="formTranslation.patient_encounter.encounter_details" v-if="appointment.encounter_id !== null" :to="{ name: 'patient-encounter.dashboard', params: { encounter_id: appointment.encounter_id } }">
                                                        <i class="fa fa-tachometer-alt"></i>
                                                    </b-button>
                                                    <a :href="appointment.zoom_data !== null ? appointment.zoom_data.start_url : '#'" target="_blank"
                                                        v-b-tooltip.hover :title="formTranslation.appointments.start_video_call"
                                                        class="btn btn-sm btn-outline-primary" v-if="
                                                                        (getUserRole() === 'doctor' || getUserRole() === 'clinic_admin' || getUserRole() === 'administrator') &&
                                                                        appointment.video_consultation &&
                                                                        appointment.status === '4' &&
                                                                        currentDate === appointment.appointment_end_date">
                                                        <i class="fa fa-video"></i>
                                                    </a>
                                                    <a :href="appointment.zoom_data !== null ? appointment.zoom_data.join_url : '#'"
                                                        v-b-tooltip.hover :title="formTranslation.appointments.join_video_call"
                                                        target="_blank" class="btn btn-sm btn-outline-primary" v-if="
                                                                        getUserRole() === 'patient' &&
                                                                        appointment.video_consultation &&
                                                                        appointment.status === '4' &&
                                                                        currentDate === appointment.appointment_end_date">
                                                        <i class="fa fa-video"></i>
                                                    </a>
                                                    <b-button variant="outline-primary" v-b-tooltip.hover :title="formTranslation.appointments.check_in" v-if="kcCheckPermission('patient_appointment_status_change') && appointment.status !== '0' && appointment.status !== '4' && appointment.status !== '3' && currentDate === appointment.appointment_end_date" @click="handleAppointmentStatus(appointment, '4')">
                                                        <i class="fa fa-sign-in-alt"></i> {{$t('appointments.check_in')}}
                                                    </b-button>
                                                    <b-button variant="outline-danger" v-b-tooltip.hover :title="formTranslation.appointments.check_out" v-if="kcCheckPermission('patient_appointment_status_change') && appointment.status !== '0' && appointment.status === '4' && currentDate === appointment.appointment_end_date" @click="handleAppointmentStatus(appointment, '3')">
                                                        <i class="fa fa-sign-out-alt"></i> {{$t('appointments.check_out')}}
                                                    </b-button>
                                                    <b-button variant="outline-danger" v-b-tooltip.hover :title="formTranslation.clinic_schedule.dt_lbl_dlt" @click="handleAppointmentDelete(appointment)">
                                                        <i class="fa fa-trash"></i>
                                                    </b-button>
                                                </b-button-group>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else>
            <div class="row">
                <div class="col-md-12">
                    <div class="row mb-2">
                        <div class="col-md-12 text-center d-flex justify-content-center align-items-center " style="height:350px">
                            <h4> {{$t('common.no_appointments')}}. </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
</template>
<script>
    import {post, get} from "../../config/request";
    export default {
        props: {
            isLoading: {
                type: [Boolean]
            },
        },
        name: 'AppointmentList',
        data: () => {
            return {
                appointmentRequest: {},
                appointmentData: [],
                isAppointmentLoading: true,
            }
        },
        mounted() {
            this.init();
        },
        methods: {
            init:  function () {
                this.todayAppointmentList();
                this.appointmentRequest = this.defaultAppointmentRequest();
            },
            defaultAppointmentRequest: function () {
                return {
                    date: new Date()
                }
            },
            todayAppointmentList: function () {
                this.$store.dispatch("userDataModule/fetchUserForDropdown", { userRoleName: this.patientRoleName })
                let filterData = Object.assign({}, this.appointmentRequest);
                filterData.date = moment(this.appointmentRequest.date).format('YYYY-MM-DD') ;
                this.$store.dispatch("appointmentModule/fetchAppointmentData", { filterData: filterData }).then(() => {
                        this.appointmentData = this.$store.state.appointmentModule.appointmentList ;
                })
                setTimeout(() => {
                    this.isAppointmentLoading = false
                }, 500);
            },
            handleAppointmentEdit: function (appointment, collapseID) {
                this.editAppointment = true;
                this.$root.$emit('bv::toggle::collapse', collapseID);
                this.$store.commit('TOGGLE_APPOINTMENT_FORM', false)
                this.editId = appointment.id
                setTimeout(() => {
                    this.appointmentFormObj = Object.assign({},appointment);
                }, 200)
            },
            handleAppointmentDelete: function (appointment) {
                if (appointment.id !== undefined) {
                    $.confirm({
                        title: this.formTranslation.clinic_schedule.dt_are_you_sure,
                        content: this.formTranslation.common.press_yes_delete_billitems,
                        type: 'red',
                        buttons: {
                            ok: {
                                text: this.formTranslation.common.yes,
                                btnClass: 'btn-danger',
                                keys: ['enter'],
                                action: () => {
                                    get('appointment_delete', {
                                        id: appointment.id
                                    })
                                        .then((data) => {
                                            if (data.data.status !== undefined && data.data.status === true) {
                                                displayMessage(data.data.message);
                                                this.reloadAppointment();
                                            }
                                        })
                                        .catch((error) => {
                                            console.log(error);
                                            displayErrorMessage(this.formTranslation.common.internal_server_error);
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
            handleAppointmentStatus: function (appointment, status) {
                if(status === '3' ){
                    if(appointment.encounter_id !== null && appointment.encounter_detail !== undefined
                        && [1,'1'].includes(appointment.encounter_detail.status)){
                    displayErrorMessage(this.formTranslation.common.encounter_not_close)
                        return ;
                    }
                }

                $.confirm({
                    title: this.formTranslation.clinic_schedule.dt_are_you_sure,
                    content: this.formTranslation.common.update_appointment_status,
                    type: 'green',
                    buttons: {
                        ok: {
                            text: this.formTranslation.common.yes,
                            btnClass: 'btn-primary',
                            keys: ['enter'],
                            action: () => {

                                get('appointment_update_status', {
                                    appointment_id: appointment.id,
                                    appointment_status: status
                                })
                                    .then((response) => {
                                        if (response.data.status !== undefined && response.data.status === true) {
                                            this.reloadAppointment();
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
                        cancel: {
                            text:this.formTranslation.common.cancel
                        }
                    }
                });
            },
            reloadAppointment: function () {
                // let filterData = Object.assign({}, this.appointmentRequest);
                // filterData.date = moment(this.appointmentRequest.date).format('YYYY-MM-DD');
                setTimeout(() => {
                    this.$store.dispatch("appointmentModule/fetchAppointmentData", { filterData:{date:moment(new Date()).format('YYYY-MM-DD')} })
                },300)
            },
            refreshAppointment: function () {
                this.isAppointmentLoading = true;
                // let filterData = Object.assign({},  { date: moment(new Date()).format('YYYY-MM-DD') });
                // filterData.date = moment(new Date()).format('YYYY-MM-DD');
                // console.log(filterData);
              get('get_appointment_queue', {filterData:{date:moment(new Date()).format('YYYY-MM-DD')}})
                    .then((response) => {
                        if (response.data.status !== undefined && response.data.status === true) {
                            this.$store.commit('appointmentModule/FETCH_APPOINTMENT_DATA', response.data.data)
                            this.$emit('isReloadTrue', false);
                        }
                    })
                    .catch((error) => {
                      this.isAppointmentLoading = false;
                      console.log(error);
                    })
            }
        },
        computed: {
            appointmentList: function () {
                return this.$store.state.appointmentModule.appointmentList ;
            },
            currentDate() {
                return moment().format('YYYY-MM-DD');
            },
          enableDisableAppointmentDescriptionStatus(){
            return this.$store.state.appointmentModule.description_status
          }
        },
        watch:{
          isLoading: function (value) {
            if(value) {
              this.isAppointmentLoading = true;
              this.refreshAppointment();
            } else {
              this.isAppointmentLoading = false;
            }
          },
          appointmentList:function(){
            this.$emit('csvData',this.appointmentList)
          }
        }
    }
</script>
