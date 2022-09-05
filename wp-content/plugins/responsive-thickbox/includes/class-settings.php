<?php

/**
 * Responsive Thickbox Settings class
 *
 * @package     responsive-thickbox
 * @subpackage  Includes
 * @copyright   Copyright (c) 2016, Lyquidity Solutions Limited
 * @License:	Lyquidity Commercial
 * @since       1.0
 */

namespace lyquidity\responsive_thickbox;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Responsive_Thickbox_Settings {

	private $options;

	/**
	 * Get things started
	 *
	 * @since 1.0
	 * @return void
	*/
	public function __construct() {

		$this->options = get_option( RESPONSIVE_THICKBOX_SETTINGS, array() );

		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Delete the options record
	 */
	public function delete_all()
	{
		delete_option( RESPONSIVE_THICKBOX_SETTINGS );
	}

	/**
	 * Set all the options
	 * @param mixed $options
	 */
	public function set_all( $options )
	{
		$this->options = $options;
	}

	/**
	 * Set a specific value
	 * @param string $key
	 * @param mixed $values
	 */
	public function set( $key, $values )
	{
		$this->options[ $key ] = $values;
	}

	/**
	 * Save the settings
	 */
	public function save()
	{
		\update_option( 'responsive_thickbox_settings', $this->options );
	}

	/**
	 * Get the value of a specific setting
	 *
	 * @since 1.0
	 * @return mixed
	*/
	public function get( $key, $default = false ) {
		$value = isset( $this->options[ $key ] ) ? $this->options[ $key ] : $default;
		return $value;
	}

	/**
	 * Get all settings
	 *
	 * @since 1.0
	 * @return array
	*/
	public function get_all() {
		return $this->options;
	}

	/**
	 * Add all settings sections and fields
	 *
	 * @since 1.0
	 * @return void
	*/
	function register_settings() {

		if ( false == get_option( 'responsive_thickbox_settings' ) ) {
			add_option( 'responsive_thickbox_settings' );
		}

		foreach( $this->get_registered_settings() as $tab => $settings ) {

			add_settings_section(
				'responsive_thickbox_settings_' . $tab,
				__return_null(),
				'__return_false',
				'responsive_thickbox_settings_' . $tab
			);

			foreach ( $settings as $key => $option ) {

				$name = isset( $option['name'] ) ? $option['name'] : '';

				add_settings_field(
					'responsive_thickbox_settings[' . $key . ']',
					$name,
					is_callable( array( $this, $option[ 'type' ] . '_callback' ) ) 
						? array( $this, $option[ 'type' ] . '_callback' ) 
						: ( 
							is_callable( __NAMESPACE__ . '\\' . $option[ 'type' ] . '_callback' )
								? __NAMESPACE__ . '\\' . $option[ 'type' ] . '_callback'
								: array( $this, 'missing_callback' ) ),
					'responsive_thickbox_settings_' . $tab,
					'responsive_thickbox_settings_' . $tab,
					array(
						'id'      => isset( $option['id'] ) ? $option['id'] : $key,
						'desc'    => ! empty( $option['desc'] ) ? $option['desc'] : '',
						'name'    => isset( $option['name'] ) ? $option['name'] : null,
						'section' => $tab,
						'size'    => isset( $option['size'] ) ? $option['size'] : null,
						'max'     => isset( $option['max'] ) ? $option['max'] : null,
						'min'     => isset( $option['min'] ) ? $option['min'] : null,
						'step'    => isset( $option['step'] ) ? $option['step'] : null,
						'options' => isset( $option['options'] ) ? $option['options'] : '',
						'std'     => isset( $option['std'] ) ? $option['std'] : ''
					)
				);
			}

		}

		// Creates our settings in the options table
		register_setting( 'responsive_thickbox_settings', 'responsive_thickbox_settings', array( $this, 'sanitize_settings' ) );

	}

	/**
	 * Retrieve the array of plugin settings
	 *
	 * @since 1.0
	 * @return array
	*/
	function sanitize_settings( $input = array() ) {

		// Don't save anything
		global $validationError, $saved, $group;
		$validationError = false;
		return array();
	}

	/**
	 * Retrieve the array of plugin settings
	 *
	 * @since 1.0
	 * @return array
	*/
	function get_registered_settings() {

		$video = "<a href=\"https://www.youtube.com/embed/oUJ_hN0uIv8?TB_iframe=true&width=1024&height=800\" class=\"rthickbox\"  >video</a>";

		$settings = array(
			'responsivethickbox' => apply_filters( 'responsive_thickbox_settings_blockcountries', 
				array(
					'getting-started' => array(
						'id' => 'getting-started',
						'name' => __( "Responsive Thickbox", "responsive-thickbox" ),
						'desc' => '<strong>' . __( "Getting Started", "responsive-thickbox" ) . '</strong>',
						'options' => array(
							__( "To use the responsive thickbox insert shortcodes into your posts and pages. Use the options below and press the " .
								"'Generate' button to create a shortcode you can use.", "responsive-thickbox" ),
							__( sprintf( "This %s reviews the options on this page provides and example of using them.", $video ), "responsive-thickbox" ),
							__( "The plugin supports three trigger widths by default. These are known as 'wide', 'small' and 'narrow'. " .
								"The exact widths these represent is up to you and is controlled by the options below.  ", "responsive-thickbox" ),
							__( "If you want more trigger widths then implement a filter called 'responsive_thickbox_sizes'.  " . 
								"Using the filter you can create any number of widths.  However, this page can only support the default trigger widths.", "responsive-thickbox" ),
						),
						'type' => 'description',
					),
					'header' => array(
						'id' => 'header',
						'type' => 'header',
					),
					'generated_shortcode' => array(
						'name' => 'Generated shortcode',
						'desc' => '[responsive-thickbox]',
						'type' => 'description',
						
					),
					'title' => array(
						'id' => 'title',
						'name' => __( "Title", "responsive-thickbox" ),
						'desc' => __( "A title to be shown above or below the popup content", "responsive-thickbox" ),
						'size' => 'large',
						'type' => 'text',
					),
					'thumbnail_url' => array(
						'id' => 'thumbnail_url',
						'name' => __( "Thumbnail URL", "responsive-thickbox" ),
						'desc' => __( "Enter a URL to the thumbnail image to be displayed and which a user should click to see the popup", "responsive-thickbox" ),
						'size' => 'large',
						'options' => array(
							'select_text' => "Select the content to display",
						),
						'type' => 'media_select',
					),
					'content_url' => array(
						'id' => 'content_url',
						'name' => __( "Content URL", "responsive-thickbox" ),
						'desc' => __( "Enter a URL to the content to be displayed in the Thickbox popup.  If the content is video and you know the encoding used you can " .
									  "use one of the other content url options if appropriate. This option is only required if the content is an image or HTML such as a YouTube video", "responsive-thickbox" ),
						'size' => 'large',
						'options' => array(
							'select_text' => "Select the content to display",
						),
						'type' => 'media_select',
					),
					'media_description' => array(
						'id' => 'media_description',
						'name' => __( "", "responsive-thickbox" ),
						'desc' => '<strong>' . __( "About alternative video URLs", "responsive-thickbox" ) . '</strong>',
						'options' => array(
							__( "The responsive thickbox will use the HTML5 &lt;video> tag to display videos.  Because not all browsers support all video encoding formats " .
								"the tag allows a site owner to specify multiple sources with different encodings so the browser can choose to use the video with the " .
								"most appropriate encoding.", "responsive-thickbox" ),
							__( "You can use just the content url above to specify the video to display.  However, you can use the options below to specify video streams " .
								"with alternative encodings if they are available.", "responsive-thickbox" ),
							"<strong><i>" . __( "These are not required options.", "responsive-thickbox" ) . "</i></strong> ",
						),
						'type' => 'description',
					),
					'content_url_mp4' => array(
						'id' => 'content_url_mp4',
						'name' => __( "Content URL (MP4)", "responsive-thickbox" ),
						'desc' => __( "If the media is video use this option to specify a url to a file encoded as MP4", "responsive-thickbox" ),
						'size' => 'large',
						'options' => array(
							'select_text' => "Select the content to display",
						),
						'type' => 'media_select',
					),
					'content_url_ogg' => array(
						'id' => 'content_url_ogg',
						'name' => __( "Content URL (OGG)", "responsive-thickbox" ),
						'desc' => __( "If the media is video use this option to specify a url to a file encoded as OGG", "responsive-thickbox" ),
						'size' => 'large',
						'options' => array(
							'select_text' => "Select the content to display",
						),
						'type' => 'media_select',
					),
					'content_url_webm' => array(
						'id' => 'content_url_webm',
						'name' => __( "Content URL (WebM)", "responsive-thickbox" ),
						'desc' => __( "If the media is video use this option to specify a url to a file encoded as WebM", "responsive-thickbox" ),
						'size' => 'large',
						'options' => array(
							'select_text' => "Select the content to display",
						),
						'type' => 'media_select',
					),
					'content_url_mobile' => array(
						'id' => 'content_url_mobile',
						'name' => __( "Content URL (Mobile)", "responsive-thickbox" ),
						'desc' => __( "If the media is video use this option to specify a url to a file encoded as 3GP", "responsive-thickbox" ),
						'size' => 'large',
						'options' => array(
							'select_text' => "Select the content to display",
						),
						'type' => 'media_select',
					),
					'discrete' => array(
						'id' => 'discrete',
						'name' => __( "Defined trigger widths", "responsive-thickbox" ),
						'desc' => __( "By default the thickbox content will be displayed to fit in the available browser window width up to the " .
									  "size of the content (Wide Thickbox width) and allowing for a border.  But if content is best shown at " .
									  "specific resolutions then check this box to be able to set specific wide, small and narrow widths to show content.", "responsive-thickbox" ),
						'type' => 'checkbox',
					),					

					'border' => array(
						'id' => 'border',
						'name' => __( "Content border", "responsive-thickbox" ),
						'desc' => __( "When 'Defined trigger widths' are <b><i>not</i></b> being used and the browser window width is less than the width " .
									  "of the content then the content will be shown sized to accommodate a visual border.  The default is 10px.", "responsive-thickbox" ),
						'type' => 'number',
						'std'  => 150,
					),

					'wide_thumbnail_width' => array(
						'id' => 'wide_thumbnail_width',
						'name' => __( "Wide Thumbnail width", "responsive-thickbox" ),
						'desc' => __( "Enter the width of the thumbnail image when the browser is 'wide'.  The default is 100% of its container so the display will be responsive.", "responsive-thickbox" ),
						'type' => 'text',
						'options' => array(
							'placeholder' => "100%",
						),
					),					
					'wide_tb_width' => array(
						'id' => 'wide_tb_width',
						'name' => __( "Wide Thickbox width", "responsive-thickbox" ),
						'desc' => __( "Enter the width of the thickbox to be shown when the browser is 'wide'.  The default is 1024px.", "responsive-thickbox" ),
						'type' => 'number',
						'std'  => 1024,
					),

					'aspect_ratio' => array(
						'id' => 'aspect_ratio',
						'name' => __( "Thickbox popup aspect ratio", "responsive-thickbox" ),
						'desc' => __( "Enter the aspect ratio of the video or image being displayed.  The aspect ratio is used to compute the heights from the widths." .
									  "The default is 1.6 which the same as 1024x640.", "responsive-thickbox" ),
						'type' => 'number',
						'std'  => 1.6,
					),

					'small_thumbnail_width' => array(
						'id' => 'small_thumbnail_width',
						'name' => __( "Small Thumbnail width", "responsive-thickbox" ),
						'desc' => __( "Enter the width of the thumbnail image when the browser is 'small'.  The default is 100% of its container so the display will be responsive.", "responsive-thickbox" ),
						'type' => 'text',
						'options' => array(
							'placeholder' => "100%",
						),
					),					
					'small_tb_width' => array(
						'id' => 'small_tb_width',
						'name' => __( "Small Thickbox width", "responsive-thickbox" ),
						'desc' => __( "Enter the width of the thickbox to be shown when the browser is 'narrow'.  The default is 640px.", "responsive-thickbox" ),
						'type' => 'number',
						'std'  => 640,
					),

					'narrow_thumbnail_width' => array(
						'id' => 'narrow_thumbnail_width',
						'name' => __( "Narrow Thumbnail width", "responsive-thickbox" ),
						'desc' => __( "Enter the width of the thumbnail image when the browser is 'narrow'.  The default is 100% of its container so the display will be responsive.", "responsive-thickbox" ),
						'type' => 'text',
						'options' => array(
							'placeholder' => "100%",
						),
					),
					'narrow_tb_width' => array(
						'id' => 'narrow_tb_width',
						'name' => __( "Narrow Thickbox width", "responsive-thickbox" ),
						'desc' => __( "Enter the width of the thickbox to be shown when the browser is 'narrow'.  The default is 480px.", "responsive-thickbox" ),
						'type' => 'number',
						'std'  => 480,
					),

					'small_sreen_width' => array(
						'id' => 'small_sreen_width',
						'name' => __( "Small screen width", "responsive-thickbox" ),
						'desc' => __( "Enter the maximum width of the 'small' display.  A browser width above this value will be considered 'wide'. The default is 1200px.", "responsive-thickbox" ),
						'type' => 'number',
						'std'  => 1200,
					),
					'narrow_screen_width' => array(
						'id' => 'narrow_screen_width',
						'name' => __( "Narrow Thickbox width", "responsive-thickbox" ),
						'desc' => __( "Enter the maximum width of the 'narrow' display.  A browser width above this value will be considered 'small'. The default is 640px.", "responsive-thickbox" ),
						'type' => 'number',
						'std'  => 640,
					),
				) 
			),
		);

		return apply_filters( 'responsive_thickbox_settings', $settings );
	}

	/**
	 * Presents a text box and a link to show the media selector
	 */
	function media_select_callback( $args )
	{
		$defaults = array(
			'id' => 'media_select',
			'options' => array( 'select_text' => "Select the image to display" ),
		);
		$args = wp_parse_args( $args, $defaults );

		$this->text_callback( $args );
?>
		<p><a href="#" id="select_<?php echo $args['id']; ?>" onclick="return false;" class="select_file_button" data-uploader-button-text="Select" data-uploader-title="<?php echo $args['options']['select_text']; ?>"><?php echo $args['options']['select_text']; ?></a></p>
<?php

	}

	/**
	 * Header Callback
	 *
	 * Renders the header.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @return void
	 */
	function header_callback( $args ) {

		$defaults = array(
			'id' => 'header',
		);
		$args = wp_parse_args( $args, $defaults );

		echo "<hr id='{$args['id']}' />";
	}

	/**
	 * Description Callback
	 *
	 * Renders a control that only has a description.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @return void
	 */
	function description_callback( $args ) {

		$defaults = array(
			'id' => 'description',
			'options' => array(),
		);
		$args = wp_parse_args( $args, $defaults );

		echo "<div id=\"{$args['id']}\">\n";

		if ( isset( $args['desc'] ) )
			echo "<p>" . $args['desc'] . "</p>\n";

		if ( isset( $args['options'] ) && is_array( $args['options'] ) )
		{
			echo "<p>" . implode( "</p>\n<p>", $args['options'] ) . "</p>\n";
		}
		
		echo "</div>\n";
	}

	/**
	 * Registers the license field callback for Software Licensing
	 *
	 * @since 1.5
	 * @param array $args Arguments passed by the setting
	 * @return void
	 */
	function license_key_callback( $args ) {

		$messages = array();
		$license  = get_option( $args['options']['is_valid_license_option'] );

		$value = $this->get( $args['id'], '' );

		if( ! empty( $license ) && is_object( $license ) ) {

			// activate_license 'invalid' on anything other than valid, so if there was an error capture it
			if ( false === $license->success ) {

				switch( $license->error ) {

					case 'expired' :

						$class = 'error';
						$messages[] = sprintf(
							__( 'Your license key expired on %s. Please <a href="%s" target="_blank" title="Renew your license key">renew your license key</a>.', 'responsive-thickbox' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license->expires, current_time( 'timestamp' ) ) ),
							'https://www.wproute.com/checkout/?edd_license_key=' . $value . '&utm_campaign=admin&utm_source=licenses&utm_medium=expired'
						);

						$license_status = 'license-' . $class . '-notice';

						break;

					case 'missing' :

						$class = 'error';
						$messages[] = sprintf(
							__( 'Invalid license. Please <a href="%s" target="_blank" title="Visit account page">visit your account page</a> and verify it.', 'responsive-thickbox' ),
							'https://www.wproute.com/your-account?utm_campaign=admin&utm_source=licenses&utm_medium=missing'
						);

						$license_status = 'license-' . $class . '-notice';

						break;

					case 'invalid' :
					case 'site_inactive' :

						$class = 'error';
						$messages[] = sprintf(
							__( 'Your %s is not active for this URL. Please <a href="%s" target="_blank" title="Visit account page">visit your account page</a> to manage your license key URLs.', 'responsive-thickbox' ),
							$args['name'],
							'https://www.wproute.com/your-account?utm_campaign=admin&utm_source=licenses&utm_medium=invalid'
						);

						$license_status = 'license-' . $class . '-notice';

						break;

					case 'item_name_mismatch' :

						$class = 'error';
						$messages[] = sprintf( __( 'This is not a %s.', 'responsive-thickbox' ), $args['name'] );

						$license_status = 'license-' . $class . '-notice';

						break;

					case 'no_activations_left':

						$class = 'error';
						$messages[] = sprintf( __( 'Your license key has reached its activation limit. <a href="%s">View possible upgrades</a> now.', 'responsive-thickbox' ), 'https://www.wproute.com/your-account/' );

						$license_status = 'license-' . $class . '-notice';

						break;

				}

			} else {

				switch( $license->license ) {

					case 'valid' :
					default:

						$class = 'valid';

						$now        = current_time( 'timestamp' );
						$expiration = strtotime( $license->expires, current_time( 'timestamp' ) );

						if( 'lifetime' === $license->expires ) {

							$messages[] = __( 'License key never expires.', 'responsive-thickbox' );

							$license_status = 'license-lifetime-notice';

						} elseif( $expiration > $now && $expiration - $now < ( DAY_IN_SECONDS * 30 ) ) {

							$messages[] = sprintf(
								__( 'Your license key expires soon! It expires on %s. <a href="%s" target="_blank" title="Renew license">Renew your license key</a>.', 'responsive-thickbox' ),
								date_i18n( get_option( 'date_format' ), strtotime( $license->expires, current_time( 'timestamp' ) ) ),
								'https://www.wproute.com/checkout/?edd_license_key=' . $value . '&utm_campaign=admin&utm_source=licenses&utm_medium=renew'
							);

							$license_status = 'license-expires-soon-notice';

						} else {

							$messages[] = sprintf(
								__( 'Your license key expires on %s.', 'responsive-thickbox' ),
								date_i18n( get_option( 'date_format' ), strtotime( $license->expires, current_time( 'timestamp' ) ) )
							);

							$license_status = 'license-expiration-date-notice';
						}

						break;
				}
			}

		} else {
			$license_status = null;
		}

		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html = '<input type="text" class="' . sanitize_html_class( $size ) . '-text" id="responsive_thickbox_settings[' . $this->sanitize_key( $args['id'] ) . ']" name="responsive_thickbox_settings[' . $this->sanitize_key( $args['id'] ) . ']" value="' . esc_attr( $value ) . '"/>';

		if ( ( is_object( $license ) && 'valid' == $license->license ) || 'valid' == $license ) {
			$html .= '<input type="submit" class="button-secondary" name="' . $args['id'] . '_deactivate" value="' . __( 'Deactivate License',  'responsive-thickbox' ) . '"/>';
		}

		$html .= '<label for="responsive_thickbox_settings[' . $this->sanitize_key( $args['id'] ) . ']"> '  . wp_kses_post( $args['desc'] ) . '</label>';

		if ( ! empty( $messages ) ) {
			foreach( $messages as $message ) {

				$html .= '<div class="responsive-thickbox-license-data responsive-thickbox-license-' . $class . '">';
					$html .= '<p>' . $message . '</p>';
				$html .= '</div>';

			}
		}

		wp_nonce_field( $this->sanitize_key( $args['id'] ) . '-nonce', $this->sanitize_key( $args['id'] ) . '-nonce' );

		if ( isset( $license_status ) ) {
			echo '<div class="' . $license_status . '">' . $html . '</div>';
		} else {
			echo '<div class="license-null">' . $html . '</div>';
		}
	}

	/**
	 * Checkbox Callback
	 *
	 * Renders checkboxes.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $this->options Array of all the plugin Options
	 * @return void
	 */
	function checkbox_callback( $args ) {

		$defaults = array(
			'id' => 'checkbox',
			'options' => array( 'disabled' => false ),
		);
		$args = wp_parse_args( $args, $defaults );
		
		$value = $this->get( $args['id'], false );

		// $checked = checked(1, isset( $this->options[ $args['id'] ] ) ? $this->options[ $args['id'] ] : ( isset( $args['std'] ) ? $args['std'] : 0 ), false);
		$checked = checked(1, $value, false);
		$disabled = isset( $args['options']['disabled'] ) && $args['options']['disabled'] ? ' disabled="disabled" ' : '';

		$html  = "<input type=\"checkbox\" id=\"responsive_thickbox_settings[{$args['id']}]\" name=\"responsive_thickbox_settings[{$args['id']}]\" value=\"1\" $checked $disabled />";
		$html .= '<label for="responsive_thickbox_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

		echo $html;
	}

	/**
	 * Multicheck Callback
	 *
	 * Renders multiple checkboxes.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $this->options Array of all the plugin Options
	 * @return void
	 */
	function multicheck_callback( $args ) {

		$defaults = array(
			'id' => 'multicheck'
		);
		$args = wp_parse_args( $args, $defaults );

		if ( ! empty( $args['options'] ) ) {
			foreach( $args['options'] as $key => $option ) {
				if( isset( $this->options[$args['id']][$key] ) ) { $enabled = $option; } else { $enabled = NULL; }
				echo '<input name="responsive_thickbox_settings[' . $args['id'] . '][' . $key . ']" id="responsive_thickbox_settings[' . $args['id'] . '][' . $key . ']" type="checkbox" value="' . $option . '" ' . checked($option, $enabled, false) . '/>&nbsp;';
				echo '<label for="responsive_thickbox_settings[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br/>';
			}
			echo '<p class="description">' . $args['desc'] . '</p>';
		}
	}

	/**
	 * Radio Callback
	 *
	 * Renders radio boxes.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $this->options Array of all the plugin Options
	 * @return void
	 */
	function radio_callback( $args ) {

		$defaults = array(
			'id' => 'radio'
		);
		$args = wp_parse_args( $args, $defaults );

		foreach ( $args['options'] as $key => $option ) :
			$checked = false;

			if ( isset( $this->options[ $args['id'] ] ) && $this->options[ $args['id'] ] == $key )
				$checked = true;
			elseif( isset( $args['std'] ) && $args['std'] == $key && ! isset( $this->options[ $args['id'] ] ) )
				$checked = true;

			echo '<input name="responsive_thickbox_settings[' . $args['id'] . ']"" id="responsive_thickbox_settings[' . $args['id'] . '][' . $key . ']" type="radio" value="' . $key . '" ' . checked(true, $checked, false) . '/>&nbsp;';
			echo '<label for="responsive_thickbox_settings[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br/>';
		endforeach;

		echo '<p class="description">' . $args['desc'] . '</p>';
	}

	/**
	 * Text Callback
	 *
	 * Renders text fields.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $this->options Array of all the plugin Options
	 * @return void
	 */
	function text_callback( $args ) 
	{
		$defaults = array(
			'id' => 'text',
		);
		$args = wp_parse_args( $args, $defaults );
		$options = array(
			'disabled' => false,
			'placeholder' => '',
		);
		$options = wp_parse_args( $args['options'], $options );
		$args['options'] = $options;

		$value = $this->get( $args['id'], "" );

		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$disabled = isset( $args['options']['disabled'] ) && $args['options']['disabled'] ? ' disabled="disabled" ' : '';

		$html  = "<input type=\"text\" class=\"{$size}-text\" id=\"responsive_thickbox_settings_{$args['id']}\" name=\"responsive_thickbox_settings[{$args['id']}]\" " .
				 "placeholder=\"{$args['options']['placeholder']}\" value=\"" . esc_attr( $value ) . "\" $disabled />";
		$html .= "<p><label for=\"responsive_thickbox_settings_{$args['id']}\">{$args['desc']}</label><p>";

		echo $html;
	}

	/**
	 * Number Callback
	 *
	 * Renders number fields.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $this->options Array of all the plugin Options
	 * @return void
	 */
	function number_callback( $args ) {

		$defaults = array(
			'id' => 'number'
		);

		$args = wp_parse_args( $args, $defaults );

		if ( isset( $this->options[ $args['id'] ] ) )
			$value = $this->options[ $args['id'] ];
		else
			$value = isset( $args['std'] ) ? $args['std'] : '';

		$max  = isset( $args['max'] ) ? $args['max'] : 999999;
		$min  = isset( $args['min'] ) ? $args['min'] : 0;
		$step = isset( $args['step'] ) ? $args['step'] : 1;

		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html = '<input type="number" step="' . esc_attr( $step ) . '" max="' . esc_attr( $max ) . '" min="' . esc_attr( $min ) . '" class="' . $size . '-text" id="responsive_thickbox_settings[' . $args['id'] . ']" name="responsive_thickbox_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
		$html .= '<p><label for="responsive_thickbox_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label></p>';

		echo $html;
	}

	/**
	 * Textarea Callback
	 *
	 * Renders textarea fields.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $this->options Array of all the plugin Options
	 * @return void
	 */
	function textarea_callback( $args ) {

		$defaults = array(
			'id' => 'textarea'
		);
		$args = wp_parse_args( $args, $defaults );

		if ( isset( $this->options[ $args['id'] ] ) )
			$value = $this->options[ $args['id'] ];
		else
			$value = isset( $args['std'] ) ? $args['std'] : '';

		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html = '<textarea class="large-text" cols="50" rows="5" id="responsive_thickbox_settings[' . $args['id'] . ']" name="responsive_thickbox_settings[' . $args['id'] . ']">' . esc_textarea( $value ) . '</textarea>';
		$html .= '<label for="responsive_thickbox_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

		echo $html;
	}

	/**
	 * Password Callback
	 *
	 * Renders password fields.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $this->options Array of all the plugin Options
	 * @return void
	 */
	function password_callback( $args ) {

		if ( isset( $this->options[ $args['id'] ] ) )
			$value = $this->options[ $args['id'] ];
		else
			$value = isset( $args['std'] ) ? $args['std'] : '';

		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html = '<input type="password" class="' . $size . '-text" id="responsive_thickbox_settings[' . $args['id'] . ']" name="responsive_thickbox_settings[' . $args['id'] . ']" value="' . esc_attr( $value ) . '"/>';
		$html .= '<label for="responsive_thickbox_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';
		$html .= "<input type=\"hidden\" name=\"_wp_nonce\" value=\"" . wp_create_nonce( 'responsive_thickbox_settings' ) . "\" >";

		echo $html;
	}

	/**
	 * Missing Callback
	 *
	 * If a function is missing for settings callbacks alert the user.
	 *
	 * @since 1.3.1
	 * @param array $args Arguments passed by the setting
	 * @return void
	 */
	function missing_callback($args) {
		printf( __( 'The callback function used for the <strong>%s</strong> setting is missing.', 'responsive-thickbox' ), $args['id'] );
	}

	/**
	 * Select Callback
	 *
	 * Renders select fields.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $this->options Array of all the plugin Options
	 * @return void
	 */
	function select_callback($args) {

		$defaults = array(
			'id' => 'select'
		);
		$args = wp_parse_args( $args, $defaults );

		if ( isset( $this->options[ $args['id'] ] ) )
			$value = $this->options[ $args['id'] ];
		else
			$value = isset( $args['std'] ) ? $args['std'] : '';

		$html = '<select id="responsive_thickbox_settings[' . $args['id'] . ']" name="responsive_thickbox_settings[' . $args['id'] . ']"/>';

		foreach ( $args['options'] as $option => $name ) :
			$selected = selected( $option, $value, false );
			$html .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
		endforeach;

		$html .= '</select>';
		$html .= '<label for="responsive_thickbox_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

		echo $html;
	}

	/**
	 * Rich Editor Callback
	 *
	 * Renders rich editor fields.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $this->options Array of all the plugin Options
	 * @global $wp_version WordPress Version
	 */
	function rich_editor_callback( $args ) {

		if ( isset( $this->options[ $args['id'] ] ) )
			$value = $this->options[ $args['id'] ];
		else
			$value = isset( $args['std'] ) ? $args['std'] : '';

		ob_start();
		wp_editor( stripslashes( $value ), 'responsive_thickbox_settings[' . $args['id'] . ']', array( 'textarea_name' => 'responsive_thickbox_settings[' . $args['id'] . ']' ) );
		$html = ob_get_clean();

		$html .= '<br/><label for="responsive_thickbox_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

		echo $html;
	}

	/**
	 * Button Callback
	 *
	 * Renders button fields.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $this->options Array of all the plugin Options
	 * @global $wp_version WordPress Version
	 */
	function button_callback( $args ) {
		
		$defaults = array(
			'id' => 'button',
			'class' => 'button-secondary',
			'options' => array( 'button_text' => 'click', 'action' => 'click' ),
		);
		$args = wp_parse_args( $args, $defaults );
		ob_start()

?>
		<button id="responsive_thickbox_settings_<?php echo $args['id']; ?>" class="button <?php echo $args['class']; ?>" action="<?php echo $args['options']['action']; ?>"><?php echo $args['options']['button_text']; ?></button>
		<label for="responsive_thickbox_settings_<?php echo $args['id']; ?>"><?php echo wp_kses_post( $args['desc'] ); ?></label>
		<img src="<?php echo RESPONSIVE_THICKBOX_PLUGIN_URL; ?>assets/images/loading.gif" id="responsive-thickbox-loading" style="display:none; margin-left: 10px;"/>
		<p/>
		<span id="responsive-thickbox-errors" class="responsive_thickbox_errors" style="display:none; padding: 4px;"></span>
		<span id="responsive-thickbox-success" class="responsive_thickbox_success" style="display:none; padding: 4px;"></span>
<?php
		echo ob_get_clean();
	}

	/**
	 * File Callback
	 *
	 * Renders a file field.
	 *
	 * @since 1.0
	 * @param array $args Arguments passed by the setting
	 * @global $this->options Array of all the plugin Options
	 * @global $wp_version WordPress Version
	 */
	function file_callback( $args ) {
		
		$defaults = array(
			'id' => 'button',
			'class' => 'button-secondary',
			'options' => array( 'button_text' => 'click', 'action' => 'click' ),
		);
		$args = wp_parse_args( $args, $defaults );
		wp_nonce_field( 'responsive-thickbox-restore', 'responsive-thickbox-restore-nonce' );
		ob_start()
?>
		<input id="responsive_thickbox_settings_<?php echo $args['id']; ?>_file" type="file">
		<button id="responsive_thickbox_settings_<?php echo $args['id']; ?>" disabled 
			<?php echo "class=\"button {$args['class']}\" "; ?>
			<?php echo isset( $args['options']['action'] ) ? "action=\"{$args['options']['action']}\"" : ""; ?> >
			<?php echo isset( $args['options']['button_text'] ) ? $args['options']['button_text'] : ""; ?>
		</button>
		<img src="<?php echo RESPONSIVE_THICKBOX_PLUGIN_URL; ?>assets/images/loading.gif" id="responsive-thickbox-restore-loading" style="display:none; margin-left: 10px;"/>
		<p>
			<span id="responsive-thickbox-restore-errors" class="responsive_thickbox_errors" style="display:none; padding: 4px;"></span>
			<span id="responsive-thickbox-restore-success" class="responsive_thickbox_success" style="display:none; padding: 4px;"></span>
		<p/>
		<p><label for="responsive_thickbox_settings_<?php echo $args['id']; ?>"><?php echo wp_kses_post( $args['desc'] ); ?></label></p>
<?php
		echo ob_get_clean();
	}

	function validate_callback( $args ) {
?>
		<button id="validate_credentials" password_id="responsive_thickbox_settings\[password\]" sender_id="responsive_thickbox_settings\[sender_id\]" value="Validate Credentials" class="button button-primary" >Validate Credentials</button>
		<img src="<?php echo RESPONSIVE_THICKBOX_PLUGIN_URL . "images/loading.gif" ?>" id="responsive-thickbox-loading" style="display:none; margin-left: 10px; margin-top: 8px;" />
		<input type="hidden" name="_validate_credentials_nonce" value="<?php echo wp_create_nonce( 'validate_credentials' ); ?>" >
<?php
	}

	/**
	 * Sanitizes a string key for BlockCountry Settings
	 *
	 * Keys are used as internal identifiers. Alphanumeric characters, dashes, underscores, stops, colons and slashes are allowed
	 *
	 * @since  1.0.0
	 * @param  string $key String key
	 * @return string Sanitized key
	 */
	function sanitize_key( $key ) {
		$raw_key = $key;
		$key = preg_replace( '/[^a-zA-Z0-9_\-\.\:\/]/', '', $key );

		/**
		 * Filter a sanitized key string.
		 *
		 * @since 1.0.0
		 * @param string $key     Sanitized key.
		 * @param string $raw_key The key prior to sanitization.
		 */
		return apply_filters( 'responsive_thickbox_sanitize_key', $key, $raw_key );
	}

}
