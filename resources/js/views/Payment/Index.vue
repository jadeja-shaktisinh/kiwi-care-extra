<template>
  <b-row>
    <div class="page-loader-section" v-if="formLoader">
      <loader-component-2></loader-component-2>
    </div>
      <div v-else>
          <b-card class="shadow" body-class="" header-tag="header" footer-tag="footer">
              <template v-slot:header>
                <b-row>
                  <b-col sm="12" md="8" lg="8">
                      <h2 class="mb-0"> {{$t('patient_bill.payment_setting')}} <a v-if="request_status == 'off'" href="https://wordpress.iqonic.design/docs/product/kivicare/clinic-admin/settings/#payment-setting" target="_blank"><i class="fa fa-question-circle"></i></a> </h2>
                  </b-col>
                </b-row>
                <hr/>
                <b-row>
                  <b-col sm="12" md="8" lg="8">
                      <h2 class="mb-0"> {{$t('patient_bill.woocommerce_payment_gateway')}} </h2>
                  </b-col>
                </b-row>
              </template>
            <div class="row">
              <div class="col-md-12 ml-4">
                <div>
                  <b-row >
                    <b-col>
                      <div class="form-group ml-2">
                        <b-form-checkbox
                            size="md"
                            id="checkbox-1"
                            v-model="status"
                            name="checkbox-1"
                            value="on"
                            unchecked-value="off"
                            @change="onPaymentStatusChange"
                            switch
                            :disabled="userData.addOns.kiviPro != true && userData.addOns.telemed != true && userData.addOns.googlemeet != true"
                        >
                          <b>{{$t('widget_setting.enable_woocommerce')}}</b><span v-if="userData.addOns.kiviPro != true && userData.addOns.telemed != true && userData.addOns.googlemeet != true" v-html="kivicareProAndAddonIcon"></span>
                        </b-form-checkbox>
                      </div>
                    </b-col>
                  </b-row>
                </div>
              </div>
            </div>
              <b-row sm="12" md="12" lg="12" class="m-2">
               {{$t('patient_bill.woocommerce_payment_notice')}}
              </b-row>
          </b-card>
          <b-card class="p-0 shadow" header-tag="header" footer-tag="footer">
          <template v-slot:header>
            <b-row>
              <b-col sm="8" md="8" lg="8">
                <h2 class="mb-0">{{ $t('paypal.paypal_setting') }}</h2>
              </b-col>
            </b-row>
          </template>
          <form
              id="paypalDataForm"
              name="paypalDataForm"
              @submit.prevent="handleSubmit"
              :novalidate="true"
              enctype="multipart/form-data"
          >
            <div class="row">
              <div class="col-md-12 ml-4">
                <div>
                  <b-row >
                    <b-col>
                      <div class="form-group ml-2">
                        <b-form-checkbox
                            size="md"
                            id="paymentPaypalSwitch"
                            v-model="paypalConfigData.enablePaypal"
                            name="checkbox-1"
                            @change="handleSubmit"
                            switch
                        >
                          <b>{{$t('paypal.paypal_status')}}</b>
                        </b-form-checkbox>
                      </div>
                    </b-col>
                  </b-row>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12" v-if="paypalConfigData.enablePaypal">
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="mode" class="form-control-label">{{$t('paypal.mode')}} <span class="text-danger"> *</span></label>
                      <div class="kivi-pr">
                        <multi-select
                            id="mode"
                            v-model="paypalConfigData.mode"
                            label="label"
                            track-by="id"
                            :options="mode_options"
                        ></multi-select>
                        <div v-if="submitted && !$v.paypalConfigData.mode.required" class="invalid-feedback">{{$t('paypal.paypal_mode_required')}}</div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3" v-if="paypalConfigData.enablePaypal">
                    <div class="form-group">
                      <label for="client_id" class="form-control-label"
                      >{{$t('paypal.client_id')}}
                      </label>
                      <input
                          type="text"
                          id="client_id"
                          class="form-control"
                          name="postfix_value"
                          v-model="paypalConfigData.client_id"
                      />
                      <div v-if="submitted && !$v.paypalConfigData.client_id.required" class="invalid-feedback">{{$t('paypal.paypal_client_id_required')}}</div>
                    </div>
                  </div>
                  <div class="col-md-3" v-if="paypalConfigData.enablePaypal">
                    <div class="form-group">
                      <label for="client_secret" class="form-control-label"
                      >{{$t('paypal.client_secret')}}
                      </label>
                      <input
                          type="text"
                          id="postfix_value"
                          class="form-control"
                          name="client_secret"
                          v-model="paypalConfigData.client_secret"
                      />
                      <div v-if="submitted && !$v.paypalConfigData.client_secret.required" class="invalid-feedback">{{$t('paypal.paypal_client_secret_required')}}</div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="currency" class="form-control-label">{{$t('paypal.currency')}} <span class="text-danger"> *</span></label>
                      <div class="kivi-pr">
                        <multi-select
                            id="currency"
                            v-model="paypalConfigData.currency"
                            label="label"
                            track-by="id"
                            :options="currency_list"
                        ></multi-select>
                        <div v-if="submitted && !$v.paypalConfigData.currency.required" class="invalid-feedback">{{$t('paypal.paypal_currency_required')}}</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row m-2">
              <b-row sm="12" md="12" lg="12" v-if="paypalConfigData.enablePaypal">
                {{$t('paypal.paypal_currency_notice')}}
              </b-row>
            </div>
            <div class="row" v-if="paypalConfigData.enablePaypal">
              <div class="col-md-12">
                <div class="d-flex justify-content-end">
                  <button type="submit" :disabled="loading" id="btn-paypal-submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> {{ $t("common.save") }}
                  </button>
                </div>
              </div>
            </div>
          </form>
        </b-card>
          <b-card class="shadow" body-class="" header-tag="header" footer-tag="footer">
          <template v-slot:header>
            <b-row>
              <b-col sm="12" md="8" lg="8">
                <h2 class="mb-0"> {{$t('patient_bill.local_payment_gateway')}}  </h2>
              </b-col>
            </b-row>
          </template>
          <div class="row">
            <div class="col-md-12 ml-4">
              <div>
                <b-row >
                  <b-col>
                    <div class="form-group ml-2">
                      <b-form-checkbox
                          size="md"
                          id="localpaymentstatus"
                          v-model="local_payment_status"
                          name="checkbox-1"
                          value="on"
                          unchecked-value="off"
                          @change="localPaymentStatusChange"
                          switch
                      >
                        <b>{{$t('widget_setting.enable_local_payment')}}</b>
                      </b-form-checkbox>
                    </div>
                  </b-col>
                </b-row>
              </div>
            </div>
          </div>
        </b-card>
        <b-card class="p-0 shadow" header-tag="header" footer-tag="footer">
          <template v-slot:header>
            <b-row>
              <b-col sm="8" md="8" lg="8">
                <h2 class="mb-0">{{ $t('common.razorpay') + ' ' +$t('common.settings') }}
                  <a href="#" target="_blank" v-if="!(userData.addOns.razorpay)">
                    <span  v-html="kivicareProFeatureIcon('Razorpay','Available In Razorpay Addon')"></span>
                  </a>
                </h2>
              </b-col>
            </b-row>
          </template>
          <form
              id="razorpayDataForm"
              name="razorpayDataForm"
              @submit.prevent="handleRazorpaySubmit"
              :novalidate="true"
              enctype="multipart/form-data"
              v-if="userData.addOns.razorpay"
          >
            <div class="row">
              <div class="col-md-12 ml-4">
                <div>
                  <b-row >
                    <b-col>
                      <div class="form-group ml-2">
                        <b-form-checkbox
                            size="md"
                            id="paymentRazorpaySwitch"
                            v-model="razorPayConfigData.enable"
                            name="paymentRazorpaySwitch"
                            @change="handleRazorpaySubmit"
                            switch
                        >
                          <b>{{$t('common.razorpay') + ' ' +$t('receptionist.dt_lbl_status')}}</b>
                        </b-form-checkbox>
                      </div>
                    </b-col>
                  </b-row>
                </div>
              </div>
            </div>
            <div class="row" v-if="razorPayConfigData.enable">
              <div class="col-md-4" >
                    <div class="form-group">
                      <label for="paymentRazorpayApiKey" class="form-control-label"
                      >{{$t('doctor.api_key')}}
                      </label>
                      <input
                          type="text"
                          id="paymentRazorpayApiKey"
                          class="form-control"
                          name="paymentRazorpayApiKey"
                          :placeholder="formTranslation.doctor.api_key"
                          v-model="razorPayConfigData.api_key"
                      />
                      <div v-if="razorpaySubmitted && !$v.razorPayConfigData.api_key.required" class="invalid-feedback">{{ $t('doctor.api_key_required')}}</div>
                    </div>
                  </div>
                  <div class="col-md-4" >
                    <div class="form-group">
                      <label for="paymentRazorpaySecretKey" class="form-control-label"
                      >{{$t('doctor.api_secret')}}
                      </label>
                      <input
                          type="text"
                          id="paymentRazorpaySecretKey"
                          class="form-control"
                          name="paymentRazorpaySecretKey"
                          :placeholder="formTranslation.doctor.api_secret"
                          v-model="razorPayConfigData.secret_key"
                      />
                      <div v-if="razorpaySubmitted && !$v.razorPayConfigData.secret_key.required" class="invalid-feedback">{{$t('doctor.api_secret_required')}}</div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="paymentRazorpayCurrency" class="form-control-label">{{$t('paypal.currency')}} <span class="text-danger"> *</span></label>
                      <div class="kivi-pr">
                        <multi-select
                            id="paymentRazorpayCurrency"
                            v-model="razorPayConfigData.currency"
                            label="label"
                            track-by="id"
                            :disabled="true"
                            :options="currency_list"
                        ></multi-select>
                        <div v-if="razorpaySubmitted && !$v.razorPayConfigData.currency.required" class="invalid-feedback">{{$t('paypal.paypal_currency_required')}}</div>
                      </div>
                    </div>
                  </div>
            </div>
            <div class="row m-2">
              <b-row sm="12" md="12" lg="12" v-if="razorPayConfigData.enable">
                {{$t('common.razorpay_currency_notice')}}
              </b-row>
            </div>
            <div class="row" v-if="razorPayConfigData.enable">
              <div class="col-md-12">
                <div class="d-flex justify-content-end">
                  <button type="submit" :disabled="razorpayLoading" id="btn-razorpay-submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> {{ $t("common.save") }}
                  </button>
                </div>
              </div>
            </div>
          </form>
        </b-card>
      </div>
  </b-row>
</template>
<script>
import {post,get} from "../../config/request";
import { required } from "vuelidate/lib/validators";
import {alphaSpace, validateForm} from "../../config/helper";
export default {
    data: () => {
        return {
          status: 'off',
          formLoader:true,
          request_status:'off',
          paypalConfigData: {
            enablePaypal: false,
            mode: 0,
            client_id: '',
            client_secret: '',
            currency:[{ id: 'USD', label: 'USD' }]
          },
          razorPayConfigData:{},
          local_payment_status:'on',
          submitted: false,
          loading: false,
          razorpaySubmitted:false,  
          razorpayLoading:false,
          mode_options: [{ id: 1, label: 'Live' },
                        { id: 0, label: 'Sandbox' }],
          currency_list: [
            { id: 'AUD', label: 'AUD' },
            { id: 'BRL', label: 'BRL' },
            { id: 'CAD', label: 'CAD' },
            { id: 'CNY', label: 'CNY' },
            { id: 'CZK', label: 'CZK' },
            { id: 'DKK', label: 'DKK' },
            { id: 'EUR', label: 'EUR' },
            { id: 'HKD', label: 'HKD' },
            { id: 'HUF', label: 'HUF' },
            { id: 'ILS', label: 'ILS' },
            { id: 'JPY', label: 'JPY' },
            { id: 'MYR', label: 'MYR' },
            { id: 'MXN', label: 'MXN' },
            { id: 'TWD', label: 'TWD' },
            { id: 'NZD', label: 'NZD' },
            { id: 'NOK', label: 'NOK' },
            { id: 'PHP', label: 'PHP' },
            { id: 'PLN', label: 'PLN' },
            { id: 'GBP', label: 'GBP' },
            { id: 'RUB', label: 'RUB' },
            { id: 'SGD', label: 'SGD' },
            { id: 'SEK', label: 'SEK' },
            { id: 'CHF', label: 'CHF' },
            { id: 'THB', label: 'THB' },
            { id: 'USD', label: 'USD' }]
        }
    },
    mounted () {
      if(!['administrator'].includes(this.getUserRole())) {
        this.$router.push({ name: "403"})
      }

        this.paypalConfigData = this.defaultPaypalData();
        this.razorPayConfigData = this.defaultRazorpayData();
        this.getPaymentStatusAll();
        this.kivicareProAndAddonIcon = this.kivicareProFeatureIcon('pro');
        this.getModule();
    },
    validations: {
      paypalConfigData: {
          mode: { required },
          client_id: { required },
          client_secret: { required },
          currency:{required}
      },
      razorPayConfigData: {
          api_key: { required },
          secret_key: { required },
          currency:{required}
      }
    },
    methods: {
        getPaymentStatusAll: function (value) {
          this.formLoader = true;
          get('get_payment_status_all', { status: '' } )
            .then((response) => {
              //woocommerce setting get
              this.status = response.data.data ;
              //paypal setting get
              if(response.data.paypal == 'off'){
                this.paypalConfigData.enablePaypal = false;
              }else{
                this.paypalConfigData = response.data.paypal;
              }
              //local payment setting get
              this.local_payment_status = response.data.local_payment
              //razorpay setting
              if(response.data.razorpay !== undefined && response.data.razorpay.enable !== undefined){
                this.razorPayConfigData = response.data.razorpay;
                this.razorPayConfigData.enable = ['on',true,'true'].includes(this.razorPayConfigData.enable); 
              }
              this.formLoader = false;
            })
            .catch((error) => {
              this.formLoader = false;
              console.log('error', error);
            })
        },
        onPaymentStatusChange: function () {
            get('change_woocommerce_payment_status', { status: this.status } )
                .then((response) => {
                  if( response.data.status !== undefined &&
                      response.data.status === true){
                    displayMessage(response.data.message);
                    setTimeout(()=>{
                      window.location.reload()
                    },1000)
                  }else{
                    this.status = this.status === 'on' ? 'off' : 'on' ;
                    displayErrorMessage(response.data.message)
                  }
                })
                .catch((error) => {
                  this.status = this.status === 'on' ? 'off' : 'on';
                  console.log('error', error);
                })
        },
        localPaymentStatusChange: function () {
            get('change_local_payment_status', { status: this.local_payment_status } )
                .then((response) => {
                  if( response.data.status !== undefined &&
                      response.data.status === true){
                    displayMessage(response.data.message);
                    setTimeout(()=>{
                      window.location.reload()
                    },1000)
                  }else{
                    this.local_payment_status = this.local_payment_status === 'on' ? 'off' : 'on';
                    displayErrorMessage(response.data.message)
                  }
                })
                .catch((error) => {
                  this.status = this.local_payment_status === 'on' ? 'off' : 'on';
                  console.log('error', error);
                })
        },
        defaultPaypalData() {
          return {
            mode: "",
            client_id: "",
            client_secret: "",
            enablePaypal: false
          };
        },
        defaultRazorpayData() {
          return {
            api_key: "",
            secret_key: "",
            enable: false,
            currency:{
              id:'INR',
              value:'Indian Rupees'
            }
          };
        },
        handleRazorpaySubmit(){
          this.razorpayLoading = true;
          this.razorpaySubmitted = true;
          // stop here if form is invalid
          this.$v.$touch();
          if (this.$v.razorPayConfigData.$invalid) {
            this.razorpayLoading = false;
            return;
          }
          var element =$('#btn-razorpay-submit').find("i");
          element.removeClass('fa fa-save ')
          element.addClass("fa fa-spinner fa-spin");
          this.razorpaySubmitted = true;

          if (validateForm("razorpayDataForm")) {
            post("razorpay_config_save", {data:this.razorPayConfigData})
              .then((response) => {
                this.razorpayLoading = false;
                element.removeClass("fa fa-spinner fa-spin");
                element.addClass("fa fa-save");
                if (
                  response.data.status !== undefined &&
                  response.data.status === true
                ) {
                  displayMessage(response.data.message);
                  setTimeout(()=>{
                    window.location.reload()
                  },1000)
                } else {
                  this.razorPayConfigData.enable = this.razorPayConfigData.enable === true ? false : true;
                  displayErrorMessage(response.data.message);
                }
              })
              .catch((error) => {
                console.log(error);
                this.razorpayLoading = false;
                displayErrorMessage(this.formTranslation.common.internal_server_error);
              });
          }
        },
        handleSubmit() {
          this.loading = true;
          this.submitted = true;
          // stop here if form is invalid
          this.$v.$touch();
          if (this.$v.paypalConfigData.$invalid) {
            this.loading = false;
            return;
          }
          var element =$('#btn-paypal-submit').find("i");
          element.removeClass('fa fa-save ')
          element.addClass("fa fa-spinner fa-spin");
          this.isSubmitedPaypal = true;

          if (validateForm("paypalDataForm")) {
            get("paypal_config_save", {data:this.paypalConfigData})
              .then((response) => {
                this.isSubmitedPaypal = false;
                this.submitted = false;
                this.loading = false;
                element.removeClass("fa fa-spinner fa-spin");
                element.addClass("fa fa-save");
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
                console.log(error);
                this.loading = false;
                this.submitted = false;
                displayErrorMessage(this.formTranslation.common.internal_server_error);
              });
          }
        },
        getModule:function (){
            if(window.request_data.link_show_hide !== undefined && window.request_data.link_show_hide !== ''){
            this.request_status = window.request_data.link_show_hide;
            }
        }
    },
    computed: {
      userData() {
        if(this.$store.state.userDataModule !== undefined && this.$store.state.userDataModule.user !== undefined) {
          return this.$store.state.userDataModule.user;
        } else {
          return [];
        }
      },
      teleMedEn() {
        return this.userData.addOns.telemed
      },
    }
}
</script>
<style scoped>
header.card-header{
  min-height: unset;
}
</style>