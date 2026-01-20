
** This extra plugin adds more to  functionlity **


functionality added

adding clinic to all doctor when new clinic is added
adding clinic  for all doctor in session 
and deleting session when clinic is deleted
GUI changes with Vue.js

hub admin functionality

fixes after kiwicare and kiwicare-pro  plugin are updated

every get request gets error
clinic doctors etc are not added



Not all functionlities are  in this plugin but some are added from  snippets by previous developer 


how each functionality is added



adding clinic to all doctor when new clinic is added 

before 
    it was added with add_to_all_doctors action 

    this action is added with snippet and was used in core files

after 

    we used this same action but removed it from core file 

    and added in wp-content/plugins/kiwi-care-extra/kiwi-care-extra.php

    in kcpro_save_clinic filter we used this same action with do_actions

    kcpro_save_clinic filter is in wp-content/plugins/kivicare-clinic-management-system/app/controllers/KCClinicController.php
    line 320

adding clinic  for all doctor in session and delete session

        this functionality is added with addsessions and for delete delsessions

        both added in snippet 

        and used in core files

        we were not able to find any way to implement this without changin core files

        this both action are used in 

        wp-content/plugins/kivicare-clinic-management-system/app/controllers/KCClinicSessionController.php
        save() and delete() function of the class KCClinicSessionController
        line 227 and 256


GUI changes with Vue.js

before

    before vue.js code and its build files were in core plugin 

after 

    we removed it from core plugin added in extra plugin

    in resoure/js and css in resource/scss

    build files are in assests/js

    wp-content/plugins/kiwi-care-extra/kiwi-care-extra.php 

    add_action(
			'admin_enqueue_scripts',
			array( $this, 'dequeue_original_scripts' ),
			999
		);
	add_action(
			'wp_print_scripts',
			array( $this, 'enqueue_custom_scripts' ),
			10
		);
	add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front_scripts' ) );

    are used to overide orignal js files with our build files 


hub admin functionality

before 

    wp-content/plugins/kivicare-clinic-management-system/app/database/kc-clinic-db.php 
    new field for hub hub_admin_email was added 

    and most of code was added via core files

after 

    we were not able to implement this functionlity through our code as kiwicare don't provide many filter or action to customize it without changing core files
    //added custom code for hub admin.
    we have put above comment at every channge we did for hub admin
    changes we made 

    wp-content/plugins/kivicare-clinic-management-system/app/database/kc-clinic-db.php 
    line 39 'hub_admin_email' => 'varchar(255) NULL', added


    wp-content/plugins/kivicare-clinic-management-system/app/controllers/KCAppointmentController.php
    line 113 

    global $current_user;
    get_currentuserinfo();
    $hubadminemail = (string) $current_user->user_email; 
    added 

    line 166
    $user = wp_get_current_user();
    if ( !(in_array( 'kiviCare_doctor', (array) $user->roles ) ) && !(in_array( 'kiviCare_clinic_admin', (array) $user->roles ) ) && !(in_array( 'kiviCare_patient', (array) $user->roles ) ) && ($current_user->user_email == 'alex@drapersolutions.com' || $current_user->user_email == 'info@paradiseproductdevelopment.com')) {
        $query .= " AND {$clinics_table}.hub_admin_email={$hubadminemail} ";
    }
    added

    line 936

    $current_user = wp_get_current_user();
    $hubadminemail = $current_user->user_email;
    if (in_array( 'hub', (array) $current_user->roles )){
    $data_filter.=" AND $clinics_table.hub_admin_email='{$hubadminemail}' ";
    } 

    added


    wp-content/plugins/kivicare-clinic-management-system/app/controllers/KCClinicController.php

    line 48 
     $current_user = wp_get_current_user();
	
	$hubadminemail = (string) $current_user->user_email;
	
	if ($current_user->user_email == 'alex@drapersolutions.com' || $current_user->user_email == 'info@paradiseproductdevelopment.com'){
		$condition = " WHERE 0=0";
	}
	else{
	$condition = " WHERE clinic.hub_admin_email='".$hubadminemail."'" ;
	}

    added 

    wp-content/plugins/kivicare-clinic-management-system/app/controllers/KCHomeController.php
    line 471 
    global $current_user;
	get_currentuserinfo();
    $hubadminemail = (string) $current_user->user_email;
    if($current_user_login == 'administrator' && ($current_user->user_email !== 'alex@drapersolutions.com' || $current_user->user_email !== 'info@paradiseproductdevelopment.com')){
        $clinic_condition = " AND hub_admin_email =".$hubadminemail;
    }

    added 

    wp-content/plugins/kivicare-clinic-management-system/app/controllers/KCPatientEncounterController.php
    line 176 
    $current_user = wp_get_current_user();

		$hubadminemail = (string) $current_user->user_email;

		if ( ( in_array( 'administrator', (array) $current_user->roles ) ) && ( $current_user->user_email != 'alex@drapersolutions.com' || $current_user->user_email != 'info@paradiseproductdevelopment.com' ) ) {

			$common_query = " FROM  {$patient_encounter_table}
		       LEFT JOIN {$users_table} doctors
		              ON {$patient_encounter_table}.doctor_id = doctors.id
		       LEFT JOIN {$users_table} patients
		              ON {$patient_encounter_table}.patient_id = patients.id
		       LEFT JOIN {$clinics_table}
		              ON {$patient_encounter_table}.clinic_id = {$clinics_table}.id 
            WHERE 0 = 0  {$patient_user_condition}  {$doctor_user_condition}  {$clinic_condition} {$search_condition} AND {$clinics_table}.hub_admin_email = '{$hubadminemail}'";
		} elseif ( $this->getLoginUserRole() == $this->getClinicAdminRole() ) {
			$common_query = " FROM  {$patient_encounter_table}
			LEFT JOIN {$users_table} doctors
				   ON {$patient_encounter_table}.doctor_id = doctors.id
			LEFT JOIN {$users_table} patients
				   ON {$patient_encounter_table}.patient_id = patients.id
			LEFT JOIN {$clinics_table}
				   ON {$patient_encounter_table}.clinic_id = {$clinics_table}.id 
		 WHERE 0 = 0  {$patient_user_condition}  {$doctor_user_condition}  {$clinic_condition} {$search_condition}";
		} else {
			$common_query = " FROM  {$patient_encounter_table}
		LEFT JOIN {$users_table} doctors
			   ON {$patient_encounter_table}.doctor_id = doctors.id
		LEFT JOIN {$users_table} patients
			   ON {$patient_encounter_table}.patient_id = patients.id
		LEFT JOIN {$clinics_table}
			   ON {$patient_encounter_table}.clinic_id = {$clinics_table}.id 
	 WHERE 0 = 0  {$patient_user_condition}  {$doctor_user_condition}  {$clinic_condition} {$search_condition}";
		}
		if ( $current_user->user_email == 'alex@drapersolutions.com' || $current_user->user_email == 'info@paradiseproductdevelopment.com' ) {
			$common_query = " FROM  {$patient_encounter_table}
		LEFT JOIN {$users_table} doctors
			   ON {$patient_encounter_table}.doctor_id = doctors.id
		LEFT JOIN {$users_table} patients
			   ON {$patient_encounter_table}.patient_id = patients.id
		LEFT JOIN {$clinics_table}
			   ON {$patient_encounter_table}.clinic_id = {$clinics_table}.id 
	 WHERE 0 = 0  {$patient_user_condition}  {$doctor_user_condition}  {$clinic_condition} {$search_condition}";

		}


fix after plugin are updated


get ajax geting errors


    because old plugin was not nonce cheking 

    but updated plugin does check nonce in get request 

    wp-content/plugins/kivicare-clinic-management-system/app/baseClasses/KCRoutesHandler.php

    line 117 we commented this condtion check



clinic doctors etc are not added

    this is beacause original plugin have reuired fields that we don't have in our forms for adding clininc doctors etc

    beacause of this we had to reomove those rules from it

    wp-content/plugins/kivicare-clinic-management-system/app/controllers/KCDoctorController.php
    line 284 and 285 commented 

    wp-content/plugins/kivicare-clinic-management-system/app/controllers/KCPatientController.php
    line 306 306 

    wp-content/plugins/kivicare-clinic-management-system/app/controllers/KCReceptionistController.php
    217 219 220 


    wp-content/plugins/kivicare-clinic-management-system/app/controllers/KCSetupController.php
    115 115


    this changes are need or when saving we will get error of required fields


