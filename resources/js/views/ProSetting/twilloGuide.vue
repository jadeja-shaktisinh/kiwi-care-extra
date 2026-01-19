<template>
  <div>
    <div class="row">
      <div class="col-md-12">
        <h4 class="mb-3">
          {{ twilloType === 'sms' ? $t("pro_setting.twilo_sms_guide") : $t("pro_setting.twilo_whatsapp_guide") }}
        </h4>
      </div>
    </div>
    <b-list-group>
      <b-list-group-item
      >{{ $t("pro_setting.twilio_step_1") }}:
        <a href="https://www.twilio.com/try-twilio" target="_blank">
          {{ $t("pro_setting.twilio_step_1") }}</a
        ></b-list-group-item
      >
      <b-list-group-item
      >{{ $t("pro_setting.twilio_step_2") }} ,
        <a href="https://www.twilio.com/console" target="_blank">
          {{ $t("pro_setting.twilo_sms_portal") }}
        </a>
        {{ $t("pro_setting.unique_sid") }}
      </b-list-group-item>
      <b-list-group-item
      >{{ $t("pro_setting.twilio_step_3") }}
      </b-list-group-item>
      <b-list-group-item
      >{{ $t("pro_setting.twilio_step_4") }} ,
        <a
            href="https://www.twilio.com/console/phone-numbers/search"
            target="_blank"
        >
          {{ $t("pro_setting.head_on_console") }}
        </a>
        {{ $t("pro_setting.phone_msg_sid") }}.
      </b-list-group-item>
      <b-list-group-item
      >{{ $t("pro_setting.twillo_imp_note") }} ,
        <a
            href="https://www.twilio.com/docs/api/errors/21211"
            target="_blank"
        >
          {{ $t("pro_setting.twillo_help_note") }}
        </a>
      </b-list-group-item>
    </b-list-group>
  </div>
</template>

<script>
export default {
  name: "twilloGuide",
  props:{
    twilloType:[String]
  },
  data: () => {
    return {
    }
  }
}
</script>


<style scoped>

</style>