<template>
  <div class="text-center">
    <a :href="href" target="_blank">
      <i class="fas fa-lock text-primary" style="font-size: 30px;"></i>
      <p id="cancel-label" v-html="kivicareProFeatureIcon(addon_type,message)"> </p>
    </a>
  </div>
</template>

<script>
export default {
  props:{
    addon_type:{
      type:[String],
      default(){
        return 'pro';
      }
    }
  },
  name: "overlay",
  data: () => {
    return {
      href:"https://codecanyon.net/item/kivicare-pro-clinic-patient-management-system-ehr-addon/30690654?s_rank=3",
      message:"Available In Pro"
    }
  },
  mounted() {
    if(this.addon_type == 'telemed'){
      this.href = "https://codecanyon.net/item/kivicare-telemed-addon/29363726?s_rank=9"
      this.message = "Available In Telemed Addon"
    }else if(this.addon_type == 'googlemeet'){
      this.href = "https://codecanyon.net/item/kivicare-google-meet-telemed-addon/35301137?s_rank=21"
      this.message = "Available In Googlemeet Addon"
    }
  },
  methods:{

  }
}
</script>

<style scoped>

</style>