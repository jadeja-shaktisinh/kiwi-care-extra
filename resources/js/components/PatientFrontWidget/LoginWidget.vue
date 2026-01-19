<template>
    <section>
        <form action="#" method="post">

            <div class="container">

                <label for="uname"><b>{{$t('patient_front_widget.username_email')}}</b></label>
                <input type="text" :placeholder="formTranslation.patient.plh_username" v-model="loginRequest.username" name="uname" required>

                <div v-if="submitted && !$v.loginRequest.username" class="invalid-feedback">
                    {{$t('common.repeat_password_required')}}
                </div>

                <label for="psw"><b>{{$t('common.password')}}</b></label>
                <input type="password" 
                :placeholder="formTranslation.patient.plh_pwd" v-model="loginRequest.password" name="psw" required>

                <div v-if="submitted && !$v.loginRequest.password" class="invalid-feedback">
                    {{$t('common.repeat_password_required')}}
                </div>

                <button type="button" class="btn btn-primary btn-lg" @click="onSubmit">{{$t('common.login_btn')}}</button>

            </div>
        </form>
    </section>
</template>

<script>

import {post, _patientLogin, _axios_post} from "../../config/request";
import {validateForm} from "../../config/helper";
import {required} from "vuelidate/lib/validators";

export default {
    name: 'LoginWidget',
    data: () => {
        return {
            submitted: false,
            loginRequest: {
                username: '',
                password: ''
            }
        }
    },
    validations: {
        loginRequest: {
            username: {required},
            password: {required}
        }
    },
    mounted() {
        this.init();
    },
    methods: {
        init: function () {
        },
        onSubmit: function () {

            this.submitted = true;

            this.$v.$touch();

            if (this.$v.loginRequest.$invalid) {
                return;
            }

            _axios_post(_patientLogin, this.loginRequest).then((response) => {
                this.submitted = false;
                location.reload()
            }).catch((err) => {
                this.submitted = false;
                console.log(err)
            })
        }
    }
}
</script>