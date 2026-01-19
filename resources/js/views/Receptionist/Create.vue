<template>
    <b-row>
      <div class="page-loader-section" v-if="formLoader">
        <loader-component-2></loader-component-2>
      </div>
        <b-col sm="12">
            <form id="receptionistDataForm" @submit.prevent="handleSubmit" :novalidate="true">
                <b-card class="p-0 shadow" header-tag="header" footer-tag="footer">
                    <template v-slot:header>
                        <b-row>
                            <b-col sm="12" md="8" lg="8">
                                <h3 class="mb-0">{{$t('clinic.add_receptionist')}}</h3>
                            </b-col>
                            <b-col sm="12" md="4" lg="4">
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-sm btn-primary" @click="$router.go(-1);">
                                        <i class="fa fa-angle-double-left"></i> {{$t('common.back')}}
                                    </button>
                                </div>
                            </b-col>
                        </b-row>
                    </template>

                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="text-primary mb-4">{{$t('common.basic_details')}}</h4>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="first_name" class="form-control-label">{{$t('common.fname')}} 
                                <span class="text-danger">*</span></label>
                                <input id="first_name"
                                       :class="{ ' is-invalid': submitted && $v.receptionistData.first_name.$error }"
                                       v-model="receptionistData.first_name" 
                                       :placeholder="formTranslation.receptionist.fname_plh"
                                       required="required" 
                                       name="first_name" 
                                       type="text" 
                                       class="form-control">
                                <div v-if="submitted && !$v.receptionistData.first_name.required"
                                     class="invalid-feedback">{{$t('common.fname_required')}}
                                </div>
                                <div
                                    v-else-if="submitted && (!$v.receptionistData.first_name.minLength || !$v.receptionistData.first_name.maxLength)"
                                    class="invalid-feedback"> {{$t('common.fname_validation_2')}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="last_name" class="form-control-label">
                                    {{$t('common.lname')}} <span class="text-danger">*</span>
                                </label>
                                <input id="last_name"
                                       :class="{ ' is-invalid': submitted && $v.receptionistData.last_name.$error }"
                                       v-model="receptionistData.last_name" 
                                       :placeholder="formTranslation.receptionist.lname_plh"
                                       required="required" name="first_name" type="text" class="form-control">
                                <div v-if="submitted && !$v.receptionistData.last_name.required"
                                     class="invalid-feedback">
                                    {{$t('common.lname_required')}}
                                </div>
                                <div
                                    v-else-if="submitted && (!$v.receptionistData.last_name.minLength || !$v.receptionistData.last_name.maxLength)"
                                    class="invalid-feedback"> {{$t('common.lname_validation_2')}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="email" class="form-control-label">{{$t('common.email')}} <span
                                    class="text-danger">*</span></label>
                                <input id="email"
                                       :class="{ ' is-invalid': submitted && $v.receptionistData.user_email.$error }"
                                       v-model="receptionistData.user_email" 
                                       :placeholder="formTranslation.receptionist.email_plh"
                                       required="required" name="user_email" type="text" class="form-control">
                                <div v-if="submitted && !$v.receptionistData.user_email.required"
                                     class="invalid-feedback">{{$t('common.email_required')}}
                                </div>
                                <div v-else-if="submitted && !$v.receptionistData.user_email.emailValidate"
                                     class="invalid-feedback">{{$t('common.invalid_email')}}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6" v-if="userData.addOns.kiviPro == true && getUserRole() == 'administrator'">
                                    <div class="form-group" >
                                        <label for="doctor_id" class="form-control-label">
                                            {{$t('receptionist.select_clinic')}}<span class="text-danger">*</span>
                                        </label>
                                        <multi-select
                                                deselect-label=""
                                                select-label=""
                                                v-model="receptionistData.clinic_id"
                                                :tag-placeholder="formTranslation.receptionist.select_clinic" id="clinic_id"
                                                :placeholder="formTranslation.receptionist.search_placeholder"
                                                label="label"
                                                :loading="clinicMultiselectLoader"
                                                track-by="id" :options="clinics"
                                        ></multi-select>
                                      <div v-if="submitted && !$v.receptionistData.clinic_id.required"
                                           class="invalid-feedback">{{$t('common.clinic_is_required')}}
                                      </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mobile_number" class="form-control-label">{{$t('common.contact_no')}}. <span
                                            class="text-danger">*</span>
                                        </label>
                                        <input id="mobile_number"
                                            :class="{ ' is-invalid': submitted && $v.receptionistData.mobile_number.$error }"
                                            v-model="receptionistData.mobile_number" 
                                            :placeholder="formTranslation.receptionist.contact_plh"
                                            required="required" name="mobile_number" type="text" class="form-control">
                                        <div v-if="submitted && !$v.receptionistData.mobile_number.required"
                                            class="invalid-feedback"> {{$t('common.contact_num_required')}}
                                        </div>
                                        <!-- <div
                                            v-else-if="submitted && (!$v.receptionistData.mobile_number.minLength || !$v.receptionistData.mobile_number.maxLength )"
                                            class="invalid-feedback">{{$t('common.contact_validation_1')}}
                                        </div> -->
                                        <div v-else-if="submitted && !$v.receptionistData.mobile_number.phoneNumber"
                                            class="invalid-feedback">{{$t('common.contact_validation_2')}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="receptionist_dob" class="form-control-label">{{$t('common.dob')}}. 
                                            <!-- <span class="text-danger">*</span> -->
                                        </label>
                                            <!-- :class="{ ' is-invalid': submitted && $v.receptionistData.dob.$error }" -->
                                            <input type="date" v-model="receptionistData.dob" name="doc_birthdate" id="doc_birthdate" :max="new Date().toISOString().slice(0, 10)">
                                        <!-- <vc-date-picker
                                            id="receptionist_dob"
                                            title-position="left"
                                            v-model="receptionistData.dob"
                                            :max-date="new Date().setFullYear(new Date().getFullYear() - 18)"
                                            :placeholder="formTranslation.receptionist.welcome_date_plh"
                                            :popover="{ placement: 'bottom', visibility: 'click' }"
                                        >
                                            <template v-slot="{ inputValue }">
                                                <input
                                                    class="form-control date-picker"
                                                    readonly
                                                    :value="inputValue"
                                                />
                                            </template>
                                        </vc-date-picker> -->
                                        <!-- <div v-if="submitted && !$v.receptionistData.dob.required" class="invalid-feedback">
                                            {{$t('common.dob_required')}}
                                        </div> -->
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status" class="form-control-label">
                                            {{$t('common.status')}} <span class="text-danger">*</span>
                                        </label>
                                        <b-select
                                            name="status"
                                            v-model="receptionistData.user_status"
                                            id="status"
                                            class="form-control"
                                            :class="{ ' is-invalid': submitted && $v.receptionistData.user_status.$error }"
                                        >
                                            <option value=""> {{$t('appointments.select_status')}}</option>
                                            <option value="0"> {{$t('common.active')}}</option>
                                            <option value="1"> {{$t('common.inactive')}}</option>
                                        </b-select>
                                        <div v-if="submitted && !$v.receptionistData.user_status.required"
                                            class="invalid-feedback">
                                            {{$t('appointments.status_required')}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gender" class="form-control-label">{{$t('common.gender')}} <span
                                            class="text-danger">*</span></label>
                                        <div class="col-md-12">
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="male" name="gender" v-model="receptionistData.gender"
                                                    value="male" class="custom-control-input">
                                                <label class="custom-control-label" for="male">{{$t('common.male')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="female" name="gender" v-model="receptionistData.gender"
                                                    value="female" class="custom-control-input">
                                                <label class="custom-control-label" for="female">{{$t('common.female')}}</label>
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="other" name="gender" v-model="receptionistData.gender"
                                                    value="other" class="custom-control-input">
                                                <label class="custom-control-label" for="other">{{$t('common.other')}}</label>
                                            </div>
                                        </div>
                                        <div v-if="submitted && !$v.receptionistData.gender.required" class="invalid-feedback">{{$t('common.gender_required')}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="kivicare-avatar-upload">
                                            <div class="kivicare-avatar-edit">
                                                <input  id="file" type="button" ref="file" class="p-2"
                                                           @click="uploadProfile()" style="color: transparent;">
                                                <label for="file" v-b-tooltip.hover :title="formTranslation.clinic.edit_profile_img">
                                                    <i class="fas fa-pencil-alt fa-2x"></i>
                                                    </label>
                                            </div>
                                            <div class="kivicare-avatar-preview">
                                                <div id="imagePreview" :style="'background-image: url('+ (imagePreview)+');'">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>

                    <hr/>

                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="text-primary mb-4">{{$t('doctor.other_details')}}</h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="address" class=" form-control-label">{{$t('common.address')}}</label>
                                <textarea name="address" id="address" class="form-control"
                                          v-model="receptionistData.address" 
                                          :placeholder="formTranslation.receptionist.address_plh"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="country" class="form-control-label">{{$t('common.country')}} </label>
                                <input id="country"
                                       v-model="receptionistData.country" 
                                       :placeholder="formTranslation.receptionist.country_plh"
                                       name="country"
                                       type="text" class="form-control">
                              
                                <!-- <div v-if="submitted && !$v.receptionistData.country.maxLength"
                                     class="invalid-feedback"> {{$t('common.country_validation_2')}}
                                </div> -->
                            </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
                            <label for="city" class="form-control-label">{{$t('common.city')}}</label>
                            <input id="city"
                                   v-model="receptionistData.city" 
                                   :placeholder="formTranslation.receptionist.city_plh" name="city" type="text"
                                   class="form-control">
                                   <!-- :class="{ ' is-invalid': submitted && $v.receptionistData.city.$error }" -->
                            
                            <!-- <div v-if="submitted && !$v.receptionistData.city.maxLength"
                                 class="invalid-feedback"> {{$t('common.city_validation_2')}}
                            </div> -->
                          </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="postal_code" class="form-control-label">{{$t('common.postal_code')}} </label>
                                <input
                                    id="postal_code"
                                    v-model="receptionistData.postal_code"
                                    :placeholder="formTranslation.receptionist.pcode_plh"
                                    name="postal_code"
                                    type="text"
                                    class="form-control">
                                    <!-- :class="{ ' is-invalid': submitted && $v.receptionistData.postal_code.$error }" -->
                                <!-- <div v-if="submitted && !$v.receptionistData.postal_code.maxLength"
                                     class="invalid-feedback"> {{$t('common.postal_code_validation_2')}}
                                </div> -->
                            </div>
                        </div>
                    </div>
                    <template v-slot:footer>
                        <div class="row p-0">
                            <div class="col-md-12 p-0">
                                <div class="d-flex justify-content-end">
                                    <button v-if="!loading" class="btn btn-primary" type="submit"
                                            > <i class="fa fa-save"></i> {{ formTranslation.receptionist.save_btn }} </button>
                                    <button v-else class="btn btn-primary" type="submit" disabled>
                                        <i class="fa fa-sync fa-spin"></i>&nbsp; {{$t('common.loading')}}
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" @click="$router.go(-1);"> {{$t('common.cancel')}}</button>
                                </div>
                            </div>
                        </div>
                    </template>
                </b-card>
            </form>
        </b-col>
    </b-row>
</template>

<script>
import {
    required,
    numeric,
    requiredIf,
    alpha,
    minLength,
    maxLength,
    minValue,
    maxValue
} from "vuelidate/lib/validators";
import {post,get} from "../../config/request";
import {validateForm, randomString, phoneNumber, alphaSpace, emailValidate} from "../../config/helper";

export default {
    data: () => {
        return {
            receptionistData: {},
            loading: false,
            submitted: false,
            qualificationSubmitted: false,
            cardTitle: 'Add receptionist',
            buttonText: '<i class="fa fa-save"></i> Save',
            price_type: ['range', 'fixed'],
            randomString: randomString(8),
          imagePreview: pluginBASEURL + 'assets/images/kc-demo-img.png',
          formLoader:false,
          clinicMultiselectLoader:true
        }
    },
    mounted() {
        this.receptionistData = this.defaultReceptionistData();
        this.init();
    },
    validations: {
        receptionistData: {
            first_name: {
                required,
                minLength: minLength(2),
                maxLength: maxLength(50)
            },
            last_name: {
                required,
                minLength: minLength(2),
                maxLength: maxLength(50)
            },
            user_email: {
                required,
                emailValidate
            },
            mobile_number: {
                required,
                phoneNumber,
                // minLength: minLength(6),
                // maxLength: maxLength(15)
            },
            // dob: {required},
            user_status: {required},
            gender:{
                required
            },
            clinic_id:{
              required: requiredIf(function () {
                return this.userData.addOns.kiviPro == true && this.getUserRole() == 'administrator'
              })
            }
            // city: {
            
            //     maxLength: maxLength(30)
            // },
            // country: {
              
            //     maxLength: maxLength(30)
            // },
            // postal_code: {
            //     maxLength: maxLength(12)
            // }
        }
    },
    methods: {
        init: function () {
            /// Code for the Edit functionality...
            if (this.$route.params.id !== undefined) {
                this.cardTitle = this.formTranslation.common.edit_receptionist;
                this.buttonText = '<i class="fa fa-save"></i>'+this.formTranslation.common.save;
                this.formLoader = true;
                get('receptionist_edit', {
                    id: this.$route.params.id
                })
                    .then((response) => {
                      this.formLoader = false;
                        if (response.data.status !== undefined && response.data.status === true) {
                            setTimeout(() => {
                                this.receptionistData = response.data.data;
                                // this.receptionistData.dob = new Date(this.receptionistData.dob + ' 00:00');
                                 if (this.receptionistData.user_profile) {
                                    this.imagePreview = this.receptionistData.user_profile
                                }
                            }, 200)
                        }
                    })
                    .catch((error) => {
                      this.formLoader = false;
                        console.log(error);
                        displayErrorMessage(this.formTranslation.widgets.record_not_found);
                    })
            }
        },
        uploadProfile() {
          let _this = this;

          var custom_uploader = kivicareCustomImageUploader(this.formTranslation)

          custom_uploader.on('select', function () {
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            _this.imagePreview = attachment.url;
            _this.receptionistData.profile_image = attachment.id;
          });

          //Open the uploader dialog
          custom_uploader.open();

        },
        defaultReceptionistData: function () {
            var oneYearFromNow = new Date();
            oneYearFromNow.setFullYear(oneYearFromNow.getFullYear() - 18);
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
                country: '',
                postal_code: '',
                specialties: [],
                price_type: this.formTranslation.doctor.range,
                price: 0,
                minPrice: 0,
                maxPrice: 0,
                qualifications: [],
                time_slot: 5,
                custom_fields: {},
                user_status: 0,
                clinic_id:'',
                profile_image: '',

            }
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
            if (this.$v.receptionistData.$invalid) {
                this.loading = false;
                return;
            }

            // this.receptionistData.dob = moment(this.receptionistData.dob).format("YYYY-MM-DD");

            if (validateForm("receptionistDataForm")) {
                post('receptionist_save', this.receptionistData)
                    .then((response) => {
                        this.loading = false;
                        if (response.data.status !== undefined && response.data.status === true) {
                            displayMessage(response.data.message);
                            this.$router.push({name: 'receptionist'});
                        } else {
                            // this.receptionistData.dob = new Date(this.receptionistData.dob + ' 00:00');
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
    },
    computed: {
        userData() {
            return this.$store.state.userDataModule.user;
        },
        clinics() {
            this.clinicMultiselectLoader = false;
            return this.$store.state.clinic
        },
        formTranslation: function () {
            return this.$store.state.staticDataModule.langTranslateData ;
        }
  }
}
</script>
<style scoped>
    [type="date"] {
    background:#fff url(https://cdn1.iconfinder.com/data/icons/cc_mono_icon_set/blacks/16x16/calendar_2.png)  97% 50% no-repeat ;
    }
    [type="date"]::-webkit-inner-spin-button {
    display: none;
    }
    [type="date"]::-webkit-calendar-picker-indicator {
    opacity: 0;
    }
    label {
    display: block;
    }
    #doc_birthdate {
        border: 1px solid #c4c4c4;
        border-radius: 5px;
        background-color: #fff;
        padding: 3px 5px;
        box-shadow: inset 0 3px 6px rgba(0,0,0,0.1);
        /* width: 190px; */
        width: 100%;
        height: 45px;
        color:#8c9cad
    }
    #doc_birthdate ::placeholder{
        color:#8c9cad
    }
</style>