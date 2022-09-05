<?php
/**
 * Front-end Actions
 *
 * @package     responsive-thickbox
 * @subpackage  Functions
 * @copyright   Copyright (c) 2016, Lyquidity Solutions Limited
 * @license 	Lyquidity Commercial
 * @since       1.0.1
 */

namespace lyquidity\responsive_thickbox;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

add_shortcode( 'responsive_thickbox', '\lyquidity\responsive_thickbox\responsive_thickbox_shortcode' );
add_shortcode( 'responsive-thickbox', '\lyquidity\responsive_thickbox\responsive_thickbox_shortcode' );
add_shortcode( 'show_width', '\lyquidity\responsive_thickbox\show_width' );
add_shortcode( 'show_example_button', '\lyquidity\responsive_thickbox\show_example_button' );
add_shortcode( 'responsive_thickbox_url', '\lyquidity\responsive_thickbox\responsive_thickbox_url' );
add_action( 'wp_enqueue_scripts', '\lyquidity\responsive_thickbox\enqueue_scripts', 20 );
add_action( 'admin_enqueue_scripts', '\lyquidity\responsive_thickbox\enqueue_scripts', 20 );

function enqueue_scripts() 
{
	wp_register_script( 'rthickbox', RESPONSIVE_THICKBOX_PLUGIN_URL . '/assets/js/rthickbox.js', array('jquery','jquery-ui-draggable','jquery-ui-resizable') );
	wp_enqueue_script( 'rthickbox' );
	wp_localize_script( 'rthickbox', 'rthickboxL10n', array(
		'next' => __('Next &gt;'),
		'prev' => __('&lt; Prev'),
		'image' => __('Image'),
		'of' => __('of'),
		'close' => __('Close'),
		'noiframes' => __('This feature requires inline frames. You have iframes disabled or your browser does not support them.'),
		'loadingAnimation' => includes_url('js/thickbox/loadingAnimation.gif'),
	) );

	wp_register_style('rthickbox', RESPONSIVE_THICKBOX_PLUGIN_URL . '/assets/css/rthickbox.css', array( 'dashicons' )  );
	wp_enqueue_style('rthickbox');
}

/**
 * Hooks 'actions', when present in the $_GET superglobal. Every 'action'
 * present in $_GET is called using WordPress's do_action function. These
 * functions are called on init.
 *
 * @since 1.0
 * @return void
*/
function get_actions() {
	if ( isset( $_GET[ RESPONSIVE_THICKBOX_ACTION ] ) )
	{
		do_action( strtolower( $_GET[ RESPONSIVE_THICKBOX_ACTION ] ), $_GET );
		exit;
	}
}

/**
 * Hooks 'actions', when present in the $_POST superglobal. Every 'action'
 * present in $_POST is called using WordPress's do_action function. These
 * functions are called on init.
 *
 * @since 1.0
 * @return void
*/
function post_actions() {
	if ( isset( $_POST['responsive_thickbox_action'] ) )
	{
		do_action( strtolower( $_POST['responsive_thickbox_action'] ), $_POST );
		exit;
	}
}

/**
 * Return the plugin url
 */
function responsive_thickbox_url( $attr )
{
	return RESPONSIVE_THICKBOX_PLUGIN_URL;
}

function show_example_button( $atts )
{
		extract(
			shortcode_atts(
				array(
					'width' => 1030,
					'height' => 696,
				), $atts, 'show_example_button'
			)
		);

	ob_start();
?>
	<script>
		function show_example_window()
		{
			var width = <?php echo $width; ?>;
			var height = <?php echo $height; ?>;
			var left = (screen.width/2)-(width/2);
			var top = (screen.height/2)-(height/2) - 25;

			var example = window.open( '/responsive-thickbox-example', 'Responsive Thickbox Example', 'width=' + width + ',height=' + height + ',resizable,scrollbars,status=false' );
			example.moveTo( left, top );
			return true;
		}
	</script>
	<span	style="margin: 10px 0 10px 0;" 
			class="sexybutton sexysimple sexygreen" 
			onclick="return show_example_window();">
			Responsive Thickbox Example
	</span>	
<?php

	return ob_get_clean();
}

/**
 * Shortcut to show the current width of the browser window
 */
function show_width( $atts )
{
	ob_start();
?>

<div id="resize-information" style="margin-left: 10px; font-weight: bold;"></div>
<script>

	var setWidth = function( width )
	{
		// The width to use is the nominal width provided multiplied by a scaling factor
		factoredWidth = ((window.outerWidth - 10) / window.innerWidth) * width;
		window.resizeTo(factoredWidth, window.outerHeight);

		return true;
	}

	var resize = function( e ) {
		$size = window.innerWidth > 1200 ? "wide - video 1028x660" : ( window.innerWidth > 640 ? "small - video 768x640" : "narrow - video 480x300");
		var msg = window.innerWidth + " ( " + $size + " )";
		if ( window.opener != null )
		{
			msg += ' <button onclick="setWidth(1024 + 6);">1024px</button>';
			msg += ' <button onclick="setWidth(800 + 10);">800px</button>';
			msg += ' <button onclick="setWidth(640 + 10);">640px</button>';
		}

		jQuery('#resize-information').html( msg );
	};

	jQuery(document).ready(function($) {
		resize();
	} );

	jQuery(window).resize( resize );

</script>

<?php
	return ob_get_clean();
}

function responsive_thickbox_shortcode( $atts )
{
	extract(
		shortcode_atts( 
			array(
				'title'					 => 'A popup title',
				'discrete'			 	 => false,
				'border'				 => 150,
				'thumbnail_url'			 => '',
				'content_url'			 => '',
				'content_url_mp4'		 => '',
				'content_url_ogg'		 => '',
				'content_url_webm'		 => '',
				'content_url_mobile'	 => '',
				'wide_thumbnail_width'	 => '100%',
				'small_thumbnail_width'	 => '100%',
				'narrow_thumbnail_width' => '100%',
				'wide_tb_width'			 => 1024,
				'small_tb_width'		 =>  640,
				'narrow_tb_width'		 =>  480,
				'small_sreen_width'		 => 1200,
				'narrow_screen_width'	 =>  640,
				'ratio'					 => 1.6,
				'default'				 => 'wide',
			),
			$atts, 'responsive_thickbox' 
		)
	);

	error_log("$thumbnail_url\n$content_url");

	if ( empty( $thumbnail_url ) || ( empty( $content_url ) && empty( $content_url_mp4 ) && empty( $content_url_ogg ) && empty( $content_url_webm ) && empty( $content_url_mobile ) ) ) return "<div>Content and/or Thumbnail url missing</div>";

	// Create a short unique name comprising just a-z and 0-9 so there is no issue using it as a css class name or as part of JavaScript function
	global $responsive_thickbox_names;
	if ( ! isset( $responsive_thickbox_names ) )
	{
		$responsive_thickbox_names = array();
	}

	$name = "";
	while ( in_array( ( $name = base_convert( rand( getrandmax() - 2000, getrandmax() ) * rand( getrandmax() - 2000, getrandmax() ), 10, 36 ) ), $responsive_thickbox_names ) );
	$responsive_thickbox_names[] = $name;

	$matches = array();
	if ( preg_match( "/\((?<shortcode>.*)\)/U", $thumbnail_url, $matches ) )
	{
		// If there's at least one
		$thumbnail_url = do_shortcode( str_replace( array( "(", ")" ), array( "[", "]" ), $thumbnail_url ) );
	}

	$matches = array();
	if ( ! empty( $content_url ) && preg_match( "/\((?<shortcode>.*)\)/U", $content_url, $matches ) )
	{
		// If there's at least one
		$content_url = do_shortcode( str_replace( array( "(", ")" ), array( "[", "]" ), $content_url ) );
	}

	if ( ! empty( $content_url_mp4 ) && preg_match( "/\((?<shortcode>.*)\)/U", $content_url_mp4, $matches ) )
	{
		// If there's at least one
		$content_url_mp4 = do_shortcode( str_replace( array( "(", ")" ), array( "[", "]" ), $content_url_mp4 ) );
	}

	if ( ! empty( $content_url_ogg ) && preg_match( "/\((?<shortcode>.*)\)/U", $content_url_ogg, $matches ) )
	{
		// If there's at least one
		$content_url_ogg = do_shortcode( str_replace( array( "(", ")" ), array( "[", "]" ), $content_url_ogg ) );
	}

	if ( ! empty( $content_url_webm ) && preg_match( "/\((?<shortcode>.*)\)/U", $content_url_webm, $matches ) )
	{
		// If there's at least one
		$content_url_webm = do_shortcode( str_replace( array( "(", ")" ), array( "[", "]" ), $content_url_webm ) );
	}

	$discrete = filter_var( $discrete, FILTER_VALIDATE_BOOLEAN );
	if ( ! $discrete )
	{
		$small_tb_width = $wide_tb_width;
		$narrow_tb_width = $wide_tb_width;
	}

	ob_start();

	// Create an array from which the JavaScript code can be built
	$sizes = array(
		'wide'		=> array( 
			'tb_width'			=> $wide_tb_width,
			'tb_height'			=> endsWith( $wide_tb_width, '%' ) ? $wide_tb_width : round( $wide_tb_width/$ratio ),
			'thumb_width'		=> $wide_thumbnail_width == "100%" ? "auto" : $wide_thumbnail_width,
			'thumb_height'		=> $wide_thumbnail_width == "100%" ? "auto" : ( endsWith( $wide_thumbnail_width, '%' ) ? $wide_thumbnail_width : round( $wide_thumbnail_width/$ratio ) ),
			'min_screen_width'	=> $small_sreen_width
		),
		'small'		=> array( 
			'tb_width'			=> $small_tb_width,
			'tb_height'			=> endsWith( $small_tb_width, '%' ) ? $small_tb_width : round( $small_tb_width/$ratio ),
			'thumb_width'		=> $small_thumbnail_width == "100%" ? "auto" : $small_thumbnail_width,
			'thumb_height'		=> $small_thumbnail_width == "100%" ? "auto" : ( endsWith( $small_thumbnail_width, '%' ) ? $small_thumbnail_width : round( $small_thumbnail_width/$ratio ) ),
			'min_screen_width'	=> $narrow_screen_width
		),
		'narrow'	=> array(
			'tb_width'			=> $narrow_tb_width,
			'tb_height'			=> endsWith( $narrow_tb_width, '%' ) ? $narrow_tb_width : round( $narrow_tb_width/$ratio ),
			'thumb_width'		=> $narrow_thumbnail_width == "100%" ? "auto" : $narrow_thumbnail_width,
			'thumb_height'		=> $narrow_thumbnail_width == "100%" ? "auto" : ( endsWith( $narrow_thumbnail_width, '%' ) ? $narrow_thumbnail_width : round( $narrow_thumbnail_width/$ratio ) ),
			'min_screen_width'	=> 0
		),
	);

	// Allow a user to extend the number of discrete widths supported
	$sizes = apply_filters( 'responsive_thickbox_sizes', $sizes );

	// Sort the sizes into descending order so the 'if' conditions work
	uasort( $sizes, function( $a, $b ) {
		return $b['tb_width'] - $a['tb_width'];
	} );

	$content_path = parse_url( $content_url, PHP_URL_PATH );
	$is_image = preg_match( "/\.jpg$|\.jpeg$|\.png$|\.gif$|\.bmp$/", $content_path );
	$is_video = preg_match( "/\.mp4$|\.avi$|\.mov$|\.ogg$|\.ogv$|\.3gp$/", $content_path );
?>
	<script>
		function doThickbox<?php echo $name; ?>(title, url, name) {
			
			var urls = {
				url: '<?php echo $content_url; ?>',
				mp4: '<?php echo $content_url_mp4; ?>',
				ogg: '<?php echo $content_url_ogg; ?>',
				webm: '<?php echo $content_url_webm; ?>',
				mobile: '<?php echo $content_url_mobile; ?>'
			};

			// Remove empty ones
			urls = Object.keys(urls).reduce( function(carry, url) { if ( urls[url] != "" ) carry[url] = urls[url]; return carry; }, {} );
			// Remove duplicates
			if ( Object.keys(urls).length > 1 )
			{
				var seen = {};
				urls = Object.keys(urls).reduce( function(carry,url) 
				{
					if ( seen.hasOwnProperty(urls[url]) ) return carry;
					carry[url] = urls[url];
					seen[urls[url]] = true;
					return carry;
				}, {} );
			}

			var sizes = {
<?php
			$if_clause  = array();

			foreach( $sizes as $key => $size )
			{
				$width  = $size['tb_width']  - ( $is_image || $is_video ? 0 : 29 );
				$height = $size['tb_height'] - ( $is_image || $is_video ? 0 : 12 );

				$thumb_width	= $size['thumb_width'];
				$thumb_height	= $size['thumb_height'];

				echo "\t\t\t\t'$key': { width: '$width', height: '$height', screen_width: '{$size['min_screen_width']}' },\n";

				$if_clause[] = "\t\t\t" . ( empty( $if_clause ) ? "" : "else " ) . "if ( window.innerWidth > sizes.$key.screen_width ) ";
				$if_clause[] = "\t\t\t\tsize = sizes.$key";
			}
?>
			};
<?php
			echo "\t\t\tvar size = sizes.$default == undefined ? reset( sizes ) : sizes.$default;\n";
			echo implode( "\n", $if_clause ) . ";\n\n";
?>
			responsiveThickbox.show( 
				title, urls, 
				'TB_iframe=<?php echo $is_image || $is_video ? "false" : "true"; ?>&width=' + size.width + 
				'&height=' + size.height + '&TB_border=<?php echo $border; ?>&TB_discrete=<?php echo $discrete; ?>', '' );
			jQuery('div#rTB_window').addClass( name );
			return false;
		}
	</script>

	<style>
<?php
		if ( /* $is_video && */ ! $discrete)
		{
			global $videoStyle;
			if ( ! isset( $videoStyle ) )
			{
?>
			#rTB_window video {
				display: block;
				margin: 15px 0 0 15px;
				border-bottom: 1px solid #ccc;
			}

			a video {
				border: none;
			}

			video {
				max-width: 100%;
				height: auto;
				border: none;
			}
<?php
				$videoStyle = true;
			}
		}
		else /* if ( ! $is_image ) */
		{
			// sizes are sorted in ascending order so this information can be used to set min/max widths as appropriate
			$previous = null;
			foreach( $sizes as $key => $size )
			{
				$directives = array();

				$min_width = $size['min_screen_width'] == 0 ? 0 : $size['min_screen_width'] + 1;
				$directives[] = "(min-width: {$min_width}px)";

				if ( $previous !== null )
					$directives[] = "(max-width: {$previous['min_screen_width']}px)";
?>
				/* <?php echo $key; ?> */
				@media <?php echo implode( " and ", $directives ); ?> {
					div#rTB_window.<?php echo $name; ?> {
						width: <?php echo $size['tb_width']; ?>px !important;
						height: <?php echo $size['tb_height']; ?>px;
					}
					
					a.responsive-thickbox.<?php echo $name; ?> img {
						width:  <?php echo "{$size['thumb_width']}"  . ( $size['thumb_width']  == 'auto' || endsWith( $size['thumb_width'],  '%' ) ? "" : "px" ); ?>;
						height: <?php echo "{$size['thumb_height']}" . ( $size['thumb_height'] == 'auto' || endsWith( $size['thumb_height'], '%' ) ? "" : "px" ); ?>;
					}
				}
<?php
				$previous = $size;
			}
		}
?>
	</style>

	<a	onclick="return doThickbox<?php echo $name; ?>( '<?php echo $title; ?>', '<?php echo $content_url; ?>', <?php echo "'$name'"; ?> );"
		class='responsive-thickbox <?php echo $name; ?>' title='<?php echo $title; ?>' rel="nofollow" ><img src='<?php echo $thumbnail_url; ?>' ></a>
<?php

	return ob_get_clean();
}
