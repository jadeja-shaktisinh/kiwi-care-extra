<?php
/**
 * Plugin Name: KiviCareExtra - Extra Features for KiviCare EHR
 * Description: KiviCareExtra is an extra features plugin for KiviCare EHR.
 * Version:3.0.8
 * Author: yourname
 * Text Domain: kiwi-care-extra
 * Domain Path: /languages
 **/


if ( ! defined( 'KIVI_CARE_PREFIX' ) ) {
	define( 'KIVI_CARE_PREFIX', 'kiviCare_' );
}
use App\baseClasses\KCBase;
class KiwiCareExtra extends KCBase {
	public $plugin_url;

	public function __construct() {
		$this->plugin_url = plugin_dir_url( __FILE__ );
		$this->include();
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
		
	}


	public function include() {
		// require __DIR__ . '/vendor/autoload.php';

		require_once WP_PLUGIN_DIR . '/kivicare-clinic-management-system/utils/kc_helpers.php';
	}


	public function dequeue_original_scripts() {
		// Only override on dashboard page
		if ( ! isset( $_REQUEST['page'] ) || $_REQUEST['page'] !== 'dashboard' ) {
			return;
		}

		// Dequeue and deregister dashboard scripts (enqueued)
		wp_dequeue_script( 'kc_js_bundle' );
		wp_deregister_script( 'kc_js_bundle' );
		
		// Dequeue and deregister dashboard styles (enqueued)
		wp_dequeue_style( 'kc_app_min_style' );
		wp_deregister_style( 'kc_app_min_style' );
	}

	public function enqueue_custom_scripts() {
		// Only override on dashboard page
		if ( ! isset( $_REQUEST['page'] ) || $_REQUEST['page'] !== 'dashboard' ) {
			return;
		}

		$js_url = plugins_url( 'assets/js/', __FILE__ );
		$css_url = plugins_url( 'assets/css/', __FILE__ );

		// Force register and enqueue right before printing
		wp_register_script(
			'kc_js_bundle',
			$js_url . 'app.min.js',
			array( 'jquery' ),
			'3.0.8',
			true
		);
		wp_enqueue_script( 'kc_js_bundle' );

		wp_register_style(
			'kc_app_min_style',
			$css_url . 'app.min.css',
			array(),
			'3.0.8'
		);
		wp_enqueue_style( 'kc_app_min_style' );

		wp_localize_script( 'kc_js_bundle', 'request_data', $this->getLocalizeScriptData( 'dashboard' ) );
	}

	public function enqueue_front_scripts() {
		
		$localizeArray = $this->getLocalizeScriptData( 'frontend' );
		
		// Deregister original frontend assets (these are only registered, not enqueued)
		wp_deregister_style( 'kc_front_app_min_style' );
		wp_deregister_script('kc_front_js_bundle');

		$js_url = plugins_url( 'assets/js/', __FILE__ );
		$css_url = plugins_url( 'assets/css/', __FILE__ );

		// Register custom frontend assets with original handles
		wp_register_script(
			'kc_front_js_bundle',
			$js_url . 'frontend.min.js',
			array( 'jquery' ),
			'3.0.8',
			true
		);

		wp_register_style( 
			'kc_front_app_min_style', 
			$css_url . 'front-app.min.css', 
			array(), 
			'3.0.8',
			false 
		);

		// Localize the frontend script
		wp_localize_script( 'kc_front_js_bundle', 'ajaxData', $localizeArray );
		wp_localize_script( 'kc_front_js_bundle', 'request_data', $localizeArray );
	}


	public function getLocalizeScriptData( $type ) {
		$prefix         = KIVI_CARE_PREFIX;
		$config_options = kc_get_multiple_option(
			"
            '{$prefix}appointment_description_config_data',
            '{$prefix}appointment_patient_info_config_data',
            '{$prefix}copyrightText',
            '{$prefix}site_logo',
            '{$prefix}request_helper_status',
            '{$prefix}theme_color',
            'mp_demo_sandbox_id'
        "
		);
		// localize data
		$user_locale                  = get_user_locale();
		$lang                         = explode( '_', $user_locale );
		$lang                         = ! empty( $lang[0] ) ? $lang[0] : 'en';
		$enableAppointmentDescription = ! empty( $config_options[ KIVI_CARE_PREFIX . 'appointment_description_config_data' ] ) ? $config_options[ KIVI_CARE_PREFIX . 'appointment_description_config_data' ] : 'off';
		$enablePatientInfo            = ! empty( $config_options[ KIVI_CARE_PREFIX . 'appointment_patient_info_config_data' ] ) ? $config_options[ KIVI_CARE_PREFIX . 'appointment_patient_info_config_data' ] : 'off';
		$time_format                  = get_option( 'time_format' );
		$data                         = array(
			'ajaxurl'                    => admin_url( 'admin-ajax.php' ),
			'nonce'                      => wp_create_nonce( 'ajax_post' ),
			'get_nonce'                  => wp_create_nonce( 'ajax_get' ),
			'kiviCarePluginURL'          => $this->plugin_url,
			'loaderImage'                => kcPluginLoader(),
			'homePage'                   => get_home_url(),
			'appointment_time_format'    => $time_format,
			'current_user_role'          => $this->getLoginUserRole(),
			'current_wordpress_lang'     => $lang,
			'proActive'                  => isKiviCareProActive(),
			'appointment_restrict'       => kcAppointmentRestrictionData(),
			'date_format'                => kcGetDateFormat(),
			'logout_redirect_url'        => kcGetLogoutRedirectSetting( 'all' ),
			'copyrightText'              => ! empty( $config_options[ KIVI_CARE_PREFIX . 'copyrightText' ] ) ? $config_options[ KIVI_CARE_PREFIX . 'copyrightText' ] : '',
			'file_upload_status'         => kcAppointmentMultiFileUploadEnable() ? 'on' : 'off',
			'description_status'         => $enableAppointmentDescription,
			'patient_detail_info_status' => $enablePatientInfo,
			'menu_url'                   => admin_url( 'admin.php?page=dashboard' ),
			'wp_timezone'                => wp_timezone_string(),
		);

		if ( $type === 'frontend' ) {
			$site_logo = ! empty( $config_options[ KIVI_CARE_PREFIX . 'site_logo' ] )
				? wp_get_attachment_url( $config_options[ KIVI_CARE_PREFIX . 'site_logo' ] ) : $this->plugin_url . 'assets/images/logo-banner.png';
			$temp      = array(
				'site_logo'            => $site_logo,
				'forget_password_page' => apply_filters( 'kivicare_custom_lost_password_url', wp_lostpassword_url() ),
				'default_clinic_id'    => kcGetDefaultClinicId(),
			);
			$data      = array_merge( $data, $temp );
		}

		if ( $type === 'dashboard' ) {
			$temp = array(
				'new_user'                  => kcToCheckUserIsNew() ? 'true' : 'false',
				'adminUrl'                  => admin_url(),
				'color'                     => ! empty( $config_options[ KIVI_CARE_PREFIX . 'theme_color' ] ) ? $config_options[ KIVI_CARE_PREFIX . 'theme_color' ] : '#4874dc',
				'link_show_hide'            => ! empty( $config_options[ KIVI_CARE_PREFIX . 'request_helper_status' ] ) && $config_options[ KIVI_CARE_PREFIX . 'request_helper_status' ] == 'on' ? 'on' : 'off',
				'understand_loco_translate' => kcGetiUnderstand(),
				'time_zone_data'            => kcGetTimeZoneOption(),
				'allClinicHaveSession'      => kcGetAllClinicHaveSession(),
				'wordpress_logo'            => kcWordpressLogostatusAndImage( 'image' ),
				'demo_plugin_active'        => ! empty( $config_options['mp_demo_sandbox_id'] ) && is_multisite(),
				'wordpress_logo_status'     => kcWordpressLogostatusAndImage( 'status' ),
			);
			$data = array_merge( $data, $temp );
		}
		return apply_filters( 'kivicare_localize_request_data', $data, $type );
	}


	public function kcGetAppointmentTimeFormatOption() {
		$data = get_option( KIVI_CARE_PREFIX . 'appointment_time_format', true );
		return gettype( $data ) != 'boolean' ? $data : 'off';
	}
}

add_action(
	'plugins_loaded',
	function () {
		new KiwiCareExtra();
	}
);
