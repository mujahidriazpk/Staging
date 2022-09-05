jQuery(document).ready(function ($) {

	/**
	 * Configuration
	 */
	$.extend(true, window, {
		'ResponsiveThickboxAdmin': {
			'showError': function(message, button, reload)
			{
				if ( button ) button.removeAttr('disabled');

				var errorEl = $('#responsive-thickbox-errors');
				var loadingEl = $('#responsive-thickbox-loading');
				loadingEl.hide();

				errorEl.html(message);
				errorEl.show().css('display', 'inline-block');
				setTimeout( function()
				{
					if (reload)
						window.location.reload();
					else
						errorEl.hide();
				},10000 );
			},

			'showSuccess': function(message, button, reload)
			{
				if ( button ) button.removeAttr('disabled');

				var successEl = $('#responsive-thickbox-success');
				var loadingEl = $('#responsive-thickbox-loading');
				loadingEl.hide();

				successEl.html(message);
				successEl.show().css('display', 'inline-block');
				setTimeout( function()
				{
					if (reload)
						window.location.reload();
					else
						successEl.hide();
				},10000 );
			}
		}
	} );
	
	var ResponsiveThickboxAdminPrivate = 
	{
		fieldname: "",
		defaults: {},

		init: function() 
		{
			ResponsiveThickboxAdminPrivate.productList();
			ResponsiveThickboxAdminPrivate.selectImage();
			ResponsiveThickboxAdminPrivate.prepareControls();
			ResponsiveThickboxAdminPrivate.showControls();
		},

		productList: function() {
			$( '#product-list-close button' ).on( 'click', function(e) 
			{
				e.preventDefault();
				$( '#product-list-close').closest('td').hide();
			} );
		},

		selectImage: function()
		{
			// WP 3.5+ uploader
			var file_frame;

			$( document.body ).on('click', '.select_file_button', function(e) {

				e.preventDefault();

				var me = $(this);
				// Must use a 'global
				ResponsiveThickboxAdminPrivate.fieldname = me.attr('id').replace( /select_/, '' );

				// If the media frame already exists, reopen it.
				if ( file_frame ) {
					//file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
					file_frame.open();
					return;
				}

				// Create the media frame.
				file_frame = wp.media.frames.file_frame = wp.media( {
					title: me.data( 'uploader-title' ),
					button: {
						text: me.data( 'uploader-button-text' )
					},
					multiple: false
				} );

				// When an image is selected, run a callback.
				file_frame.on( 'select', function() {

					var selection = file_frame.state().get('selection');

					selection.each( function( attachment, index ) 
					{
						attachment = attachment.toJSON();
						// Only ever take the first
						if ( 0 !== index ) return;
						// place first attachment in field
						var control = $('#responsive_thickbox_settings_' + ResponsiveThickboxAdminPrivate.fieldname);
						control.val( attachment.url );
						control.change();
					});
				});

				// Finally, open the modal
				file_frame.open();
			});

			// WP 3.5+ uploader
			var file_frame;
			window.formfield = '';
			
		},

		hideControls: function( controls ) 
		{
			controls.each( function( index, control ) {
				// Lookup the default value
				control.value = ResponsiveThickboxAdminPrivate.defaults[ control.id ];
				$( control ).closest('tr').hide();
			} );
		},

		showControls: function()
		{
			var change = function( isDiscrete ) {
				
				var unchanging_ids = [
					'responsive_thickbox_settings\\[discrete\\]',
					'responsive_thickbox_settings_title',
					'responsive_thickbox_settings_content_url',
					'responsive_thickbox_settings_content_url_mp4',
					'responsive_thickbox_settings_content_url_ogg',
					'responsive_thickbox_settings_content_url_webm',
					'responsive_thickbox_settings_content_url_mobile',
					'responsive_thickbox_settings_thumbnail_url',
					'responsive_thickbox_settings_wide_thumbnail_width',
					'responsive_thickbox_settings\\[wide_tb_width\\]',
					'responsive_thickbox_settings\\[aspect_ratio\\]',
				];
				var discreteOnly_ids = [
					'responsive_thickbox_settings_small_thumbnail_width',
					'responsive_thickbox_settings\\[small_tb_width\\]',
					'responsive_thickbox_settings_narrow_thumbnail_width',
					'responsive_thickbox_settings\\[narrow_tb_width\\]',
					'responsive_thickbox_settings\\[small_sreen_width\\]',
					'responsive_thickbox_settings\\[narrow_screen_width\\]',
				];
				var continuousOnly_ids = [
					'responsive_thickbox_settings\\[border\\]',
				];

				if ( isDiscrete )
				{
					$( '#' + discreteOnly_ids.join( ', #' ) ).closest('tr').show();
					ResponsiveThickboxAdminPrivate.hideControls( $( '#' + continuousOnly_ids.join( ', #' ) ) );
				}
				else
				{
					$( '#' + continuousOnly_ids.join( ', #' ) ).closest('tr').show();
					ResponsiveThickboxAdminPrivate.hideControls( $( '#' + discreteOnly_ids.join( ', #' ) ) );
				}
			};

			var discrete = $( '#responsive_thickbox_settings\\[discrete\\]' );
			discrete.on( 'change', function( e ) 
			{
				e.preventDefault();
				change( this.checked );
				ResponsiveThickboxAdminPrivate.generateShortcode();
			} );

			change( false );
		},

		generateShortcode: function()
		{
			var shortcode = $( '#generated_shortcode' );

			var lines = [];
			ResponsiveThickboxAdminPrivate.controls.each( function( index, control ) 
			{
				if ( control.value == "" ) return;

				switch( control.name )
				{
					case 'title':
					case 'content_url':
					case 'content_url_mp4':
					case 'content_url_ogg':
					case 'content_url_webm':
					case 'content_url_mobile':
					case 'thumbnail_url':
						lines.push( control.name + '="' + control.value + '" ' );
						return;

					case 'wide_thumbnail_width':
					case 'small_thumbnail_width':
					case 'narrow_thumbnail_width':
						if ( control.value == '100%' ) return;
						break;

					case 'discrete':
						if ( ! control.checked ) return;
						break;

					case 'border':
						if ( control.value == '150' ) return;
						break;

					case 'wide_tb_width':
						if ( control.value == '1024' ) return;
						break;

					case 'small_tb_width':
						if ( control.value == '640' ) return;
						break;

					case 'narrow_tb_width':
						if ( control.value == '480' ) return;
						break;

					case 'small_sreen_width':
						if ( control.value == '1200' ) return;
						break;

					case 'narrow_screen_width':
						if ( control.value == '640' ) return;
						break;

					case 'aspect_ratio':
						if ( control.value == '1.6' ) return;
						break;

				}

				lines.push( control.name + '=' + control.value + ' ' );

			} );
			
			$result = "\n<p class=responsive-thickbox-shortcode start>[responsive-thickbox]</p>\n";
			if ( lines.length )
			{
				$result = "\n<p class=\"responsive-thickbox-shortcode start\">[responsive-thickbox </p>\n\t<p class=\"responsive-thickbox-shortcode line\">" + 
						( lines.length ? lines.join( "</p>\n\t<p class=\"responsive-thickbox-shortcode line\">" ) + "</p>\n" : "" ) +
					"<p class=\"responsive-thickbox-shortcode end\">]</p>\n";
			}

			shortcode.html( $result );
		
		},
		
		prepareControls: function()
		{
			var control_ids = [
				'responsive_thickbox_settings_title',
				'responsive_thickbox_settings_content_url',
				'responsive_thickbox_settings_content_url_mp4',
				'responsive_thickbox_settings_content_url_ogg',
				'responsive_thickbox_settings_content_url_webm',
				'responsive_thickbox_settings_content_url_mobile',
				'responsive_thickbox_settings_thumbnail_url',
				'responsive_thickbox_settings\\[discrete\\]',
				'responsive_thickbox_settings\\[border\\]',
				'responsive_thickbox_settings_wide_thumbnail_width',
				'responsive_thickbox_settings\\[wide_tb_width\\]',
				'responsive_thickbox_settings\\[aspect_ratio\\]',
				'responsive_thickbox_settings_small_thumbnail_width',
				'responsive_thickbox_settings\\[small_tb_width\\]',
				'responsive_thickbox_settings_narrow_thumbnail_width',
				'responsive_thickbox_settings\\[narrow_tb_width\\]',
				'responsive_thickbox_settings\\[small_sreen_width\\]',
				'responsive_thickbox_settings\\[narrow_screen_width\\]',
			];

			var controls = $( '#' + control_ids.join( ', #' ) );
			controls.each( function( index, control ) 
			{
				ResponsiveThickboxAdminPrivate.defaults[ control.id ] = control.value;

				var name = control.id.replace( /responsive_thickbox_settings_/, '' );
				name = name.replace( /responsive_thickbox_settings\[/, '' );
				name = name.replace( /\]/, '' );
				control.name = name;
			} );

			ResponsiveThickboxAdminPrivate.controls = controls;

			var change = function( e ) 
			{
				e.preventDefault();
				ResponsiveThickboxAdminPrivate.generateShortcode();
			};

			controls.on( 'change', change );
			controls.on( 'keyup', change );
		},
	}
	
	ResponsiveThickboxAdminPrivate.init();

} );
