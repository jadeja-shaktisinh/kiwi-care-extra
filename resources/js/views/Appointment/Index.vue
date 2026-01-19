<template>
    <appointment-calender @reloadAppointment="init" v-if="reloadCalender"></appointment-calender>
</template>

<script>


export default {
    data: () => {
        return {
            reloadCalender: true
        }
    },
    mounted() {
    },
    methods: {
        init: function () {
            this.reloadCalender = false;
            this.$nextTick(() => {
                // Add the component back in
                this.reloadCalender = true;
            });
        },
    },
}

</script>

<style lang='scss'>

@import '~@fullcalendar/core/main.css';
@import '~@fullcalendar/daygrid/main.css';

.fc-button {
    text-transform: capitalize;
}

</style>