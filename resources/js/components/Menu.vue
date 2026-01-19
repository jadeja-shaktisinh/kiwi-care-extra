<template>

    <div class="card shadow p-0 mb-3">
        <b-navbar toggleable="lg">
            <b-navbar-brand :to="{ name: 'dashboard' }">{{$t('dashboard.medical_dashboard')}}</b-navbar-brand>
            <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>
            <b-collapse id="nav-collapse" is-nav>
                <!-- Right aligned nav items -->
                <b-navbar-nav class="ml-auto">
                    <b-nav-item :to="{ name: 'dashboard' }"> <i class="fa fa-tachometer-alt"></i> {{$t('dashboard.dashboard')}}</b-nav-item>
                    <b-nav-item :to="{ name: 'doctor' }" v-if="kcCheckPermission('doctor_list')"> <i class="fa fa-user-md"></i> {{$t('common.doctors')}}</b-nav-item>
                    <b-nav-item :to="{ name: 'patient' }" v-if="kcCheckPermission('patient_list')"> <i class="fas fa-hospital-user"></i> {{$t('dashboard.patients')}}</b-nav-item>
                    <b-nav-item :to="{ name: 'patient-encounter', params: { patient_id: patient_id }}" v-if="kcCheckPermission('patient_encounters')" > <i class="fas fa-calendar-week"></i> {{$t('patient_encounter.encounters')}} </b-nav-item>
                    <b-nav-item :to="{ name: 'appointment' }" v-if="kcCheckPermission('appointment_list')"> <i class="fas fa-calendar-week"></i> {{$t('dashboard.appointments')}}</b-nav-item>
                    <b-nav-item :to="{ name: 'service' }" v-if="kcCheckPermission('service_list')"><i class="fa fa-server"></i> {{$t('common.services')}}</b-nav-item>
                    <b-nav-item-dropdown text="Settings" right>
                        <b-dropdown-item :to="{ name: 'clinic.profile' }" v-if="kcCheckPermission('clinic_profile')" > <i class="fa fa-server"></i> {{$t('clinic.clinic_profile')}}</b-dropdown-item>
                        <b-dropdown-item :to="{ name: 'receptionist.profile'}" v-if="kcCheckPermission('receptionist_profile')"><i class="fa fa-server"></i> Profile </b-dropdown-item>
                        <b-dropdown-item :to="{ name: 'doctor.profile'}" v-if="kcCheckPermission('doctor_profile')"><i class="fa fa-server"></i> {{$t('common.profile')}} </b-dropdown-item>
                        <b-dropdown-item :to="{ name: 'patient.profile'}" v-if="kcCheckPermission('patient_profile')"><i class="fa fa-server"></i> {{$t('common.profile')}} </b-dropdown-item>
                        <b-dropdown-item :to="{ name: 'account-setting.password' }" v-if="kcCheckPermission('change_password')"><i class="fa fa-server"></i>  {{$t('common.change_password')}} </b-dropdown-item>
                        <b-dropdown-item :to="{ name: 'clinic-schedule' }" v-if="kcCheckPermission('clinic_schedule')"><i class="fa fa-server"></i> {{$t('clinic_schedule.schedule')}} </b-dropdown-item>
                        <b-dropdown-item :to="{ name: 'common-settings.create' }" v-if="kcCheckPermission('common_settings')"><i class="fa fa-server"></i> {{$t('settings.general')}} </b-dropdown-item>
                        <b-dropdown-item :to="{ name: 'notification.create' }" v-if="kcCheckPermission('notification_setting')"><i class="fa fa-server"></i> {{$t('notification.notification')}} </b-dropdown-item>
                        <b-dropdown-item :to="{ name: 'terms-condition.create' }" v-if="kcCheckPermission('terms_condition')"><i class="fa fa-server"></i> {{$t('static_data.terms_n_condition')}} </b-dropdown-item>
                        <b-dropdown-item :to="{ name: 'static-data' }" v-if="kcCheckPermission('static_data_list')"><i class="fa fa-server"></i> {{$t('common.static_data')}}</b-dropdown-item>
                        <b-dropdown-item :to="{ name: 'custom-field' }" v-if="kcCheckPermission('custom_field_list')"><i class="fa fa-server"></i> {{$t('settings.custom_field')}}</b-dropdown-item>
                        <b-dropdown-item  @click="logout" ><i class="fa fa-server"></i> {{$t('common.logout')}} </b-dropdown-item>
                    </b-nav-item-dropdown>
                </b-navbar-nav>
            </b-collapse>
        </b-navbar>
    </div>

</template>

<script>
    export default {
        data: () => {
            return {
                patient_id: 0,
            }
        },
        mounted() {
            this.init();
        },
        methods: {
            init: function () {
                this.patientEncounter();
            },
            logout: function () {
                this.$store.dispatch("logout", { self: this })
            },
            patientEncounter: function () {
                if (this.$store.state.userDataModule.user.ID !== undefined) {
                    this.patient_id  =  this.$store.state.userDataModule.user.ID ;
                } else {
                    this.$store.dispatch("userDataModule/fetchUserData", {});
                    setTimeout(() => {
                        this.patient_id  =  this.$store.state.userDataModule.user.ID ;
                    }, 1000);
                }
            }
        }
    }
</script>
