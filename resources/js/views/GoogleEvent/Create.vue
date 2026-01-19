<template>
  <div>
    <div class="page-loader-section" v-if="formLoader">
      <loader-component-2></loader-component-2>
    </div>
    <b-row v-else>
      <b-col sm="12" md="12" lg="12">
        <b-overlay :show="userData.addOns.kiviPro != true" variant="white"
                   :opacity="overlayOpacity">
          <template #overlay>
            <overlay-message addon_type="pro"></overlay-message>
          </template>
        <form id="doctorDataForm" :novalidate="true">
          <b-card class="p-0 shadow" header-tag="header" footer-tag="footer">
            <template v-slot:header>
              <b-row>
                <b-col sm="8" md="8" lg="8">
                  <h2 class="mb-0">{{ $t('common.google_event_template') }} <a v-if="request_status == 'off'" href="https://wordpress.iqonic.design/docs/product/kivicare/google-calendar/google-event-template/" target="_blank"><i class="fa fa-question-circle"></i></a> </h2>
                </b-col>
              </b-row>
            </template>
            <div v-for="(item, index) in googleEventList" :key="index">
              <b-collapse :id="'accordion'" :visible="true" accordion="my-accordion" role="tabpanel">
                <b-card-body>
                  <label class="form-control-label">{{$t('google_event.google_event_title')}}</label>
                  <input
                      class="form-control"
                      type="text"
                      id="example-color-input"
                      v-model="item.post_title"
                  />
                  <label class="form-control-label">{{$t('google_event.google_event_desc')}} </label>
                  <vue-editor :editorToolbar="customToolbar" v-model="item.post_content"></vue-editor>
                </b-card-body>
              </b-collapse>
            </div>
            <b-row class="mt-2">
              <b-col sm="12" md="12" lg="12">
                <div class="d-flex justify-content-end">
                  <button
                      class="btn btn-primary " type="button" @click="saveGoogleEventTemplate"><i
                      class="fa fa-save mr-1"></i> {{$t('common.save')}}
                  </button>
                </div>
              </b-col>
            </b-row>
          </b-card>
        </form>
        </b-overlay>
      </b-col>
    </b-row>
  </div>
</template>
<script>
import {post,get} from "../../config/request";
export default {
  data: () => {
    return {
      googleEventList:[],
      eventTitle: '',
      request_status:'off',
      templateSaveRequest: {
        ID: 0,
        post_content: '',
      },
      formLoader:true,
      customToolbar: [[{
        header: [false, 1, 2, 3, 4, 5, 6]
      }], ["bold", "italic", "underline", "strike"], // toggled buttons
        [{
          align: ""
        }, {
          align: "center"
        }, {
          align: "right"
        }, {
          align: "justify"
        }], ["blockquote", "code-block"], [{
          list: "ordered"
        }, {
          list: "bullet"
        }, {
          list: "check"
        }], [{
          indent: "-1"
        }, {
          indent: "+1"
        }], // outdent/indent
        [{
          color: []
        }, {
          background: []
        }], // dropdown with defaults from theme
      ]
    }
  },
  
  mounted() {
    this.init();
    this.getModule();
  },
  methods: {
    init: function () {
        this.getGoogleEventTemplate();
    },
    getGoogleEventTemplate: function () {
      this.formLoader =true;
      get('get_google_event_template', {})
        .then((response) => {
          if (response.data.status !== undefined && response.data.status === true) {
            this.googleEventList = response.data.data
          }
          this.formLoader =false;
        })
        .catch((error) => {
          this.formLoader =false;
          console.log(error);
        })
    },
    saveGoogleEventTemplate: function () {
      if(this.userData.addOns.kiviPro != true){
        return;
      }
      post('save_google_event_template', { data : this.googleEventList } )
        .then((response) => {
          if (response.data.status !== undefined && response.data.status === true) {
            this.getGoogleEventTemplate();
            displayMessage(response.data.message);
          } else {
            displayErrorMessage(response.data.message);
          }
          this.loading = false;
        })
        .catch((error) => {
          this.loading = false;
          displayErrorMessage(this.formTranslation.common.server_error);
        })
    },
    getModule:function (){
        if(window.request_data.link_show_hide !== undefined && window.request_data.link_show_hide !== ''){
        this.request_status = window.request_data.link_show_hide;
        }
    }
  },
  watch: {},
  computed:{
    userData () {
      return this.$store.state.userDataModule.user;
    }
  }
}

</script>