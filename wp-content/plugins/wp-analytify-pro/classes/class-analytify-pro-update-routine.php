<?php
class Analytify_Pro_Update_Routine {

	private $current_verison = '';

	/**
	 * Private constructor for singliton class.
	 * 
	 */
	function __construct( $current_verison ) {
		
		$this->current_verison = $current_verison;
		$this->run_routines();
	}

	/**
	 * Run update routines.
	 *
	 * @return void
	 */
	function run_routines() {

		if ( version_compare( $this->current_verison, '4.0.2', '<' ) ) {
			$this->update_routine_402();
		}

		// Update version to latest release.
		update_option( 'analytify_pro_current_version', ANALYTIFY_PRO_VERSION );
	}

	/**
	 * Update routine for version 4.0.2
	 * Active all pro modules by default except AMP.
	 *
	 * @return void
	 */
	function update_routine_402() {

		$wp_analytify_modules = get_option( 'wp_analytify_modules' );
		
		if ( $wp_analytify_modules ) {
			foreach ( $wp_analytify_modules as &$module ) {
				if ( 'amp' !== $module['slug'] ) {
					$module['status'] = 'active';
				}
			}

			update_option( 'wp_analytify_modules', $wp_analytify_modules );
		}
	}
}

// Get current plugin version.
$analytify_pro_current_version = get_option( 'analytify_pro_current_version', '4.0.1' );

// Call update routine if needed.
if ( version_compare( $analytify_pro_current_version, '4.0.2', '<' ) ) {
	new Analytify_Pro_Update_Routine( $analytify_pro_current_version );
}