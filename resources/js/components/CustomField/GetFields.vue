<template>
  <div class="row" v-if="customFields.length > 0">
    <div v-for="(field, index) in customFields" class="col-md-5 mb-3" :key="index" v-if="field.status !== undefined && field.status == '1'">
      <label :for="field.name + '_' + index" class="form-control-label text-capitalize">
          {{ field.label }} <span class="text-danger" v-if="parseInt(field.isRequired) === 1">*</span>
      </label>
      <input
          v-if="field.type === 'text' || field.type === 'number'"
          :id="field.name + '_' + index"
          v-model="fieldsObj['custom_field_'+field.id]"
          :placeholder="field.placeholder"
          :required="parseInt(field.isRequired) === 1"
          :name="'custom_field_'+field.id"
          @change="handleValueChange"
          :type="field.type"
          class="form-control"
      />
      <b-select
          v-if="field.type === 'select'"
          :id="field.name + '_' + index"
          v-model="fieldsObj['custom_field_'+field.id]"
          class="form-control text-capitalize"
          :name="field.name"
          @change="handleValueChange"
          :required="parseInt(field.isRequired) === 1"
      >
          <option value=""> {{$t('common.select_option')}} </option>
          <option v-for="(option, index) in field.options" :value="option.id" :key="index" >{{ option.text }}</option>
      </b-select>
      <div v-if="field.type === 'radio'">
          <div v-for="(option, key) in field.options" class="custom-control custom-radio custom-control-inline" :key="key">
              <input
                  type="radio"
                  :id="field.name + '_' +key"
                  :name="'custom_field_'+field.id"
                  :required="parseInt(field.isRequired) === 1"
                  v-model="fieldsObj['custom_field_'+field.id]"
                  :value="option.id"
                  class="custom-control-input"
                  @change="handleValueChange"
              >
              <label class="custom-control-label" :for="field.name + '_' +key">{{ option.text }}</label>
          </div>
      </div>
      <div v-if="field.type === 'checkbox'">
          <div v-for="(option, key) in field.options" class="custom-control custom-checkbox custom-control-inline" :key="key">
              <input
                  type="checkbox"
                  :id="field.name + '_' +key"
                  :name="'custom_field_'+field.id"
                  :required="parseInt(field.isRequired) === 1"
                  v-model="fieldsObj['custom_field_'+field.id]"
                  :value="option.id"
                  class="custom-control-input"
                  @change="handleValueChange"
              >
              <label class="custom-control-label" :for="field.name + '_' +key">{{ option.text }}</label>
          </div>
      </div>
      <div v-if="field.type === 'calendar'">
          <vc-date-picker
              id="doc_birthdate"
              title-position="left"
              v-model="fieldsObj['custom_field_'+field.id]"
                @change="handleValueChange"
              :popover="{ placement: 'bottom', visibility: 'click' }"
          >
              <template v-slot="{ inputValue }">
                  <input
                      class="form-control date-picker"
                      readonly
                      :value="inputValue"
                      :placeholder="field.placeholder"
                  />
              </template>
          </vc-date-picker>
      </div>
      <textarea
          v-if="field.type === 'textarea'"
          :id="field.name + '_' + index"
          v-model="fieldsObj['custom_field_'+field.id]"
          :placeholder="field.placeholder"
          @change="handleValueChange"
          :name="'custom_field_'+field.id"
          class="form-control"
          :required="parseInt(field.isRequired) === 1"
      ></textarea>
    </div>
  </div>
</template>
<script>
import {post, get} from "../../config/request";
export default {
    props: {
        module_type: {
            type: [String],
            default() {
                return ""
            }
        },
        module_id: {
            type: [String,Number],
            default() {
                return ""
            }
        },
        doctor_id: {
          type: [String],
          default() {
            return ""
          }
        },
        fieldsValue: {
            type: [Array, Object],
            default() {
                return {};
            }
        },
        customFieldsObj: {
            type: [Array, Object],
            default() {
                return {};
            }
        }
    },
    data: () => {
        return {
            fieldsObj: {},
            validateRequired: [],
            customFields: []
        }
    },
    mounted() {
        this.init();
    },
    methods: {
        init: function () {
            if (this.module_type !== "") {
                get('get_custom_fields', {
                    module_type: this.module_type,
                    module_id: this.module_id,
                    doctor_id: this.doctor_id
                })
                    .then((response) => {
                        if (response.data.status !== undefined && response.data.status === true) {
                            if (response.data.data !== null && response.data.data !== undefined && response.data.data.length > 0) {
                                this.customFields = response.data.data;
                                this.$emit('customFieldAvailable');
                            }
                            this.handleFormObject()
                            // this.handleValueChange();
                        } else {
                            displayErrorMessage(response.data.message)
                        }

                    })
                    .catch((error) => {
                        console.log(error);
                        displayErrorMessage(this.formTranslation.common.internal_server_error);
                    })
            } else {
                this.customFields = this.customFieldsObj;
                this.handleFormObject()
                // this.handleValueChange();
            }
        },
        handleFormObject: function () {
            if (this.customFields.length > 0) {
                this.customFields.map((field) => {
                    if (field.type === "checkbox") {
                        this.fieldsObj['custom_field_'+field.id] = [];
                    } else if (field.type === "calendar") {
                      var date =  moment(field.field_data).format("YYYY-MM-DD");
                      this.fieldsObj['custom_field_'+field.id] =  new Date(date + ' 00:00') ?  new Date(date + ' 00:00') :date;
                    } else {
                         this.fieldsObj['custom_field_'+field.id] = "";
                    }
                })
            }

        },
        handleValueChange: function () {
            this.validateRequired = []

            if (this.customFields.length > 0) {
                this.customFields.map((field) => {
                    var add = false;
                    if (parseInt(field.isRequired) === 1 && parseInt(field.status) === 1) {
                        if (Array.isArray(this.fieldsObj['custom_field_'+field.id])) {
                          if (this.fieldsObj['custom_field_'+field.id] === []) {
                                add = true;
                            }
                        } else {
                            if (!this.fieldsObj['custom_field_'+field.id]) {
                                add = true;
                            }
                        }

                        if (add === true) {
                            if (!this.validateRequired.includes('custom_field_'+field.id)) {
                                this.validateRequired.push('custom_field_'+field.id);
                            }
                        }
                    }
                })
            }
            this.$emit('bindCustomField', this.fieldsObj);
            this.$emit('requiredCustomField', this.validateRequired);
        }
    },
    computed: {
    },
    watch: {
        fieldsValue: function (newObj) {
            this.handleValueChange();
        },
        customFieldsObj: function (newObj) {
            this.customFields = newObj;
        }
    }
}
</script>