# Kiwi Care Extra Plugin – Functionality Overview

> **This extra plugin adds additional functionality on top of the core KiwiCare and KiwiCare Pro plugins.**

---

## Summary of Added / Modified Functionality

- Adding a clinic to all doctors when a new clinic is created  
- Adding clinics for all doctors in sessions and deleting sessions when a clinic is deleted  
- GUI changes using Vue.js  
- Hub admin functionality  
- Fixes required after KiwiCare and KiwiCare Pro plugin updates  
  - GET requests returning errors  
  - Clinic doctors, patients, etc. not being added  

> **Note:**  
> Not all functionality exists entirely inside this plugin. Some logic is still implemented using snippets created by a previous developer.

---

## How Each Functionality Is Implemented

---

## 1. Adding Clinic to All Doctors When a New Clinic Is Added

### Before
- Clinic was added to all doctors using the `add_to_all_doctors` action.
- This action was defined via a snippet.
- The action was triggered directly inside core plugin files.

### After
- The same `add_to_all_doctors` action is reused.
- It has been **removed from core plugin files**.
- The action is now implemented inside the extra plugin:

wp-content/plugins/kiwi-care-extra/kiwi-care-extra.php

markdown
Copy code

- The action is triggered using `do_action()` inside the `kcpro_save_clinic` filter.
- The `kcpro_save_clinic` filter exists in:

wp-content/plugins/kivicare-clinic-management-system/app/controllers/KCClinicController.php
(Line 320)

yaml
Copy code

---

## 2. Adding Clinic for All Doctors in Session & Deleting Session

### Implementation Details
- This functionality is handled using two actions:
  - `addsessions`
  - `delsessions`
- Both actions were originally added via snippets.
- These actions are used directly inside core plugin files.

### Limitation
- There was **no available filter or action** provided by KiwiCare to implement this externally.
- Because of this, modifying core files was unavoidable.

### Core File Changes
Used inside:

wp-content/plugins/kivicare-clinic-management-system/app/controllers/KCClinicSessionController.php

yaml
Copy code

- `save()` method → Line 227  
- `delete()` method → Line 256  

---

## 3. GUI Changes with Vue.js

### Before
- Vue.js source code and build files were part of the core plugin.

### After
- Vue.js code has been **removed from the core plugin**.
- It is now fully managed inside the extra plugin.

### File Structure

resources/js
resources/scss
assets/js (build files)

makefile
Copy code

### Script Override Logic

In:

wp-content/plugins/kiwi-care-extra/kiwi-care-extra.php

kotlin
Copy code

The following hooks are used to override original scripts:

```php
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

add_action(
    'wp_enqueue_scripts',
    array( $this, 'enqueue_front_scripts' )
);
These hooks dequeue the original plugin scripts and enqueue the custom Vue.js build files instead.

4. Hub Admin Functionality
Before
A new database field hub_admin_email was added.

Most of the hub admin logic was implemented directly inside core plugin files.

After
KiwiCare does not provide enough hooks or filters to fully implement this feature externally.

Core file modifications were unavoidable.

All hub-admin-related changes are marked with comments:

php
Copy code
// added custom code for hub admin.
Core File Changes for Hub Admin
1. Database Schema
pgsql
Copy code
wp-content/plugins/kivicare-clinic-management-system/app/database/kc-clinic-db.php
(Line 39)
php
Copy code
'hub_admin_email' => 'varchar(255) NULL'
2. Appointment Controller
swift
Copy code
wp-content/plugins/kivicare-clinic-management-system/app/controllers/KCAppointmentController.php
Line 113

php
Copy code
global $current_user;
get_currentuserinfo();
$hubadminemail = (string) $current_user->user_email;
Line 166

php
Copy code
$user = wp_get_current_user();
if (
    !(in_array('kiviCare_doctor', (array) $user->roles)) &&
    !(in_array('kiviCare_clinic_admin', (array) $user->roles)) &&
    !(in_array('kiviCare_patient', (array) $user->roles)) &&
    ($current_user->user_email == 'alex@drapersolutions.com' ||
     $current_user->user_email == 'info@paradiseproductdevelopment.com')
) {
    $query .= " AND {$clinics_table}.hub_admin_email={$hubadminemail} ";
}
Line 936

php
Copy code
$current_user = wp_get_current_user();
$hubadminemail = $current_user->user_email;

if (in_array('hub', (array) $current_user->roles)) {
    $data_filter .= " AND $clinics_table.hub_admin_email='{$hubadminemail}' ";
}
3. Clinic Controller
css
Copy code
wp-content/plugins/kivicare-clinic-management-system/app/controllers/KCClinicController.php
(Line 48)
php
Copy code
$current_user = wp_get_current_user();
$hubadminemail = (string) $current_user->user_email;

if (
    $current_user->user_email == 'alex@drapersolutions.com' ||
    $current_user->user_email == 'info@paradiseproductdevelopment.com'
) {
    $condition = " WHERE 0=0";
} else {
    $condition = " WHERE clinic.hub_admin_email='".$hubadminemail."'";
}
4. Home Controller
css
Copy code
wp-content/plugins/kivicare-clinic-management-system/app/controllers/KCHomeController.php
(Line 471)
php
Copy code
global $current_user;
get_currentuserinfo();
$hubadminemail = (string) $current_user->user_email;

if (
    $current_user_login == 'administrator' &&
    ($current_user->user_email !== 'alex@drapersolutions.com' ||
     $current_user->user_email !== 'info@paradiseproductdevelopment.com')
) {
    $clinic_condition = " AND hub_admin_email =".$hubadminemail;
}
5. Patient Encounter Controller
css
Copy code
wp-content/plugins/kivicare-clinic-management-system/app/controllers/KCPatientEncounterController.php
(Line 176)
Custom SQL conditions added to filter data by hub_admin_email

Logic varies based on role:

Administrator

Clinic Admin

Hub Admin

Special exceptions for specific admin email IDs

5. Fixes After Plugin Updates
5.1 GET / AJAX Requests Returning Errors
Cause
Older versions of KiwiCare did not validate nonces.

Updated versions now enforce nonce validation for GET requests.

Temporary Fix
Nonce validation was commented out in:

css
Copy code
wp-content/plugins/kivicare-clinic-management-system/app/baseClasses/KCRoutesHandler.php
(Line 117)
5.2 Clinic Doctors / Patients Not Being Added
Cause
Updated plugin introduced required fields.

These required fields are not present in custom forms.

This caused validation errors when saving data.

Fix
Required field validation rules were commented out in core controllers.

Modified Files
Doctor Controller
css
Copy code
wp-content/plugins/kivicare-clinic-management-system/app/controllers/KCDoctorController.php
(Line 284, 285)
Patient Controller
css
Copy code
wp-content/plugins/kivicare-clinic-management-system/app/controllers/KCPatientController.php
(Line 306)
Receptionist Controller
css
Copy code
wp-content/plugins/kivicare-clinic-management-system/app/controllers/KCReceptionistController.php
(Line 217, 219, 220)
Setup Controller
css
Copy code
wp-content/plugins/kivicare-clinic-management-system/app/controllers/KCSetupController.php
(Line 115)
These changes are required; otherwise, saving data results in validation errors due to missing required fields.

End of Document