/*
 * Based on Thickbox 3.1.
 * By Cody Lindley (http://www.codylindley.com)
 * 
 */

if ( typeof rtb_pathToImage != 'string' ) {
	var rtb_pathToImage = rthickboxL10n.loadingAnimation;
}

/*!!!!!!!!!!!!!!!!! edit below this line at your own risk !!!!!!!!!!!!!!!!!!!!!!!*/

//on page load call tb_init
jQuery(document).ready(function()
{
	responsiveThickbox = {
		
		/*
		 * Add thickbox to href & area elements that have a class of .rthickbox.
		 * Remove the loading indicator when content in an iframe has loaded.
		 */
		init: function(domChunk)
		{
			jQuery( 'body' )
				.on( 'click', domChunk, responsiveThickbox.click )
				.on( 'rthickbox:iframe:loaded', function() {
					jQuery( '#rTB_window' ).removeClass( 'rthickbox-loading' );
				});
		},

		click: function()
		{
			var title = this.title || this.name || null;
			var url = this.href || this.alt;
			var group = this.rel || false;
			responsiveThickbox.show(title,{ url: url.replace(/\?.*/,'')},url.replace(/^[^\?]+\??/,''),group);
			this.blur();
			return false;
		},

		/**
		 * Function called when the user clicks on a thickbox link
		 */
		show: function( caption, urls, queryString, imageGroup )
		{
			var $closeBtn;

			try 
			{
				// if IE 6
				if ( typeof document.body.style.maxHeight === "undefined" ) 
				{
					jQuery("body","html").css({height: "100%", width: "100%"});
					jQuery("html").css("overflow","hidden");
					if (document.getElementById("rTB_HideSelect") === null) //iframe to hide select elements in ie6
					{
						jQuery("body").append(
							"<iframe id='rTB_HideSelect'>"+rthickboxL10n.noiframes+"</iframe>" +
							"<div id='rTB_overlay'></div>" +
							"<div id='rTB_window' class='rthickbox-loading'></div>"
						);
						jQuery("#rTB_overlay").click(responsiveThickbox.remove);
					}
				} else //all others
				{
					if(document.getElementById("rTB_overlay") === null)
					{
						jQuery("body").append(
							"<div id='rTB_overlay'></div>" +
							"<div id='rTB_window' class='rthickbox-loading'></div>");
						jQuery("#rTB_overlay").click(responsiveThickbox.remove);
						jQuery( 'body' ).addClass( 'modal-open' );
					}
				}

				if ( responsiveThickbox.detectMacXFF() )
				{
					jQuery("#rTB_overlay").addClass("rTB_overlayMacFFBGHack"); //use png overlay so hide flash
				} else
				{
					jQuery("#rTB_overlay").addClass("rTB_overlayBG"); //use background and opacity
				}

				if(caption===null) {caption="";}
				jQuery("body").append( //add loader to the page
					"<div id='rTB_load'>" +
					"	<img src='"+responsiveThickbox.imgLoader.src+"' width='208' />" +
					"</div>"
				); 
				jQuery('#rTB_load').show(); //show loader

				var baseURL;
				if ( urls.url.indexOf("?") !== -1 ) // ff there is a query string involved
				{
					baseURL = urls.url.substr(0, url.indexOf("?"));
				} else 
				{
					baseURL = urls.url;
				}

				var urlImages = /\.jpg$|\.jpeg$|\.png$|\.gif$|\.bmp$/;
				var urlVideos = /\.mp4$|\.avi$|\.mov$|\.ogg$|\.ogv$|\.webm$/;
				var is_Image = baseURL.toLowerCase().match(urlImages);
				var is_video = baseURL.toLowerCase().match(urlVideos);
				var params = responsiveThickbox.parseQuery( queryString );
				var is_discrete = params['TB_discrete'] || false;
				var is_inline = params['TB_inline'] || false;
				var useFrame = params['TB_iframe'] || false;

				// var useFrame = url.indexOf('TB_iframe') != -1;

				if( is_Image )
				{
					responsiveThickbox.PrevCaption = "";
					responsiveThickbox.PrevURL = "";
					responsiveThickbox.PrevHTML = "";
					responsiveThickbox.NextCaption = "";
					responsiveThickbox.NextURL = "";
					responsiveThickbox.NextHTML = "";
					responsiveThickbox.imageCount = "";
					responsiveThickbox.FoundURL = false;

					if ( imageGroup )
					{
						responsiveThickbox.TempArray = jQuery("a[rel="+imageGroup+"]").get();
						for (responsiveThickbox.Counter = 0; ((responsiveThickbox.Counter < responsiveThickbox.TempArray.length) && (responsiveThickbox.NextHTML === "")); responsiveThickbox.Counter++)
						{
							var urlTypeTemp = responsiveThickbox.TempArray[responsiveThickbox.Counter].href.toLowerCase().match(urlString);
							if (!(responsiveThickbox.TempArray[responsiveThickbox.Counter].href == url))
							{
								if (responsiveThickbox.FoundURL)
								{
									responsiveThickbox.NextCaption = responsiveThickbox.TempArray[responsiveThickbox.Counter].title;
									responsiveThickbox.NextURL = responsiveThickbox.TempArray[responsiveThickbox.Counter].href;
									responsiveThickbox.NextHTML = "<span id='rTB_next'>&nbsp;&nbsp;<a href='#'>"+rthickboxL10n.next+"</a></span>";
								} else 
								{
									responsiveThickbox.PrevCaption = responsiveThickbox.TempArray[responsiveThickbox.Counter].title;
									responsiveThickbox.PrevURL = responsiveThickbox.TempArray[responsiveThickbox.Counter].href;
									responsiveThickbox.PrevHTML = "<span id='rTB_prev'>&nbsp;&nbsp;<a href='#'>"+rthickboxL10n.prev+"</a></span>";
								}
							} else 
							{
								responsiveThickbox.FoundURL = true;
								responsiveThickbox.imageCount = rthickboxL10n.image + ' ' + (responsiveThickbox.Counter + 1) + ' ' + rthickboxL10n.of + ' ' + (responsiveThickbox.TempArray.length);
							}
						}
					}

					imgPreloader = new Image();
					imgPreloader.onload = function(){
						imgPreloader.onload = null;

						// Resizing large images - original by Christian Montoya edited by me.
						var imageSize = is_discrete
							? { width: params['width'] || 640, height: params['height'] || 480 }
							: responsiveThickbox.sizing( 
								  imgPreloader.width, //defaults to 630 if no parameters were added to URL
								  imgPreloader.height, //defaults to 440 if no parameters were added to URL
								  (params['TB_border']*1) || 150
							  );

						responsiveThickbox.WIDTH = imageSize.width * 1 + 30;
						responsiveThickbox.HEIGHT = imageSize.height * 1 + 60;

						jQuery("#rTB_window").append(
							"<a href='' id='rTB_ImageOff'>" +
							"	<span class='screen-reader-text'>"+rthickboxL10n.close+"</span>" +
							"	<img id='rTB_Image' src='" + baseURL + "?width=" + imageSize.width + "&height=" + imageSize.height + "' width='" + imageSize.width + "' height='" + imageSize.height + "' alt='" + caption + "'/>" +
							"</a>" + 
							"<div id='rTB_caption'>" + caption +
							"	<div id='rTB_secondLine'>" + responsiveThickbox.imageCount + responsiveThickbox.PrevHTML + responsiveThickbox.NextHTML + "</div>" +
							"</div>" +
							"<div id='rTB_closeWindow'><button type='button' id='rTB_closeWindowButton'>" +
							"	<span class='screen-reader-text'>" + rthickboxL10n.close + "</span>" +
							"	<span class='rtb-close-icon'></span></button>" +
							"</div>"
						);

						jQuery("#rTB_closeWindowButton").click(responsiveThickbox.remove);

						if (!(responsiveThickbox.PrevHTML === ""))
						{
							function goPrev()
							{
								if(jQuery(document).unbind("click",goPrev)){jQuery(document).unbind("click",goPrev);}
								jQuery("#rTB_window").remove();
								jQuery("body").append("<div id='rTB_window'></div>");
								responsiveThickbox.show(responsiveThickbox.PrevCaption, responsiveThickbox.PrevURL, imageGroup);
								return false;
							}
							jQuery("#rTB_prev").click(goPrev);
						}

						if (!(responsiveThickbox.NextHTML === "")) {
							function goNext(){
								jQuery("#rTB_window").remove();
								jQuery("body").append("<div id='rTB_window'></div>");
								responsiveThickbox.show(responsiveThickbox.NextCaption, responsiveThickbox.NextURL, imageGroup);
								return false;
							}
							jQuery("#rTB_next").click(goNext);
						}

						jQuery(document).bind('keydown.rthickbox', function(e)
						{
							if ( e.which == 27 ) // close
							{
								responsiveThickbox.remove();

							} else if ( e.which == 190 ) // display previous image
							{
								if(!(responsiveThickbox.NextHTML == "")){
									jQuery(document).unbind('rthickbox');
									goNext();
								}
							} else if ( e.which == 188 ) // display next image
							{
								if(!(responsiveThickbox.PrevHTML == ""))
								{
									jQuery(document).unbind('rthickbox');
									goPrev();
								}
							}
							return false;
						});

						responsiveThickbox.position();
						jQuery("#rTB_load").remove();
						jQuery("#rTB_ImageOff").click(responsiveThickbox.remove);
						jQuery("#rTB_window").css({'visibility':'visible'}); //for safari using css instead of show
					};

					imgPreloader.src = urls.url;

				} else if ( is_video)
				{
					var imageSize = is_discrete
						? { width: params['width'] || 630, height: params['height'] || 440 }
						: responsiveThickbox.sizing( 
							  params['width'] || 630, //defaults to 630 if no parameters were added to URL
							  params['height'] || 440, //defaults to 440 if no parameters were added to URL
							  (params['TB_border']*1) || 150
						  );
					responsiveThickbox.WIDTH = imageSize.width * 1 + 30;
					responsiveThickbox.HEIGHT = imageSize.height * 1 + 60;

					jQuery("#rTB_window").append(
						"<a href='' id='rTB_ImageOff'>" +
						"	<span class='screen-reader-text'>" + rthickboxL10n.close + "</span>" +
						"	<video id='rTB_Video' controls='' autoplay='' width='" + imageSize.width + "' height='" + imageSize.height + "' name='media'>" +
						"		<source src=" + baseURL + "?width=" + imageSize.width + "&height=" + imageSize.height + " type='video/mp4'>" +
						"		<object type=\"application/x-shockwave-flash\" " +
						"			data=\"http://releases.flowplayer.org/swf/flowplayer-3.2.1.swf\" " +
						"			width=\"" + imageSize.width + "\" height=\"" + imageSize.height + "\"> " +
						"			<param name=\"movie\" value=\"http://releases.flowplayer.org/swf/flowplayer-3.2.1.swf\" /> " +
						"			<param name=\"allowFullScreen\" value=\"true\" /> " +
						"			<param name=\"wmode\" value=\"transparent\" /> " +
						"			<param name=\"flashVars\" " +
						"				value=\"config={'playlist':[{'url':" + encodeURI( baseURL +  + "?width=" + imageSize.width + "&height=" + imageSize.height ) + "','autoPlay':false}]}\" /> " +
						"				No video playback capabilities, please download the video below" +
						"		</object> " +
						"	</video>" +
						"</a>" + 
						"<div id='rTB_caption'>" + caption + "</div>" +
						"<div id='rTB_closeWindow'>" +
						"	<button type='button' id='rTB_closeWindowButton'>" +
						"		<span class='screen-reader-text'>"+rthickboxL10n.close+"</span>" +
						"		<span class='rtb-close-icon'></span>" +
						"	</button>" +
						"</div>");

					jQuery("#rTB_closeWindowButton").click(responsiveThickbox.remove);

					responsiveThickbox.handleEscape( params );

					responsiveThickbox.position();

					jQuery("#rTB_load").remove();
					jQuery("#rTB_ImageOff").click(responsiveThickbox.remove);
					var window = jQuery("#rTB_window");
					window.css({'visibility':'visible'}); //for safari using css instead of show
					if ( is_discrete ) window.css({'paddingBottom':'50px'});

				} else //code to show html
				{
					// Resizing large images - original by Christian Montoya edited by me.
					var imageSize = is_discrete
						? { width: params['width'] * 1 + 30 || 630, height: params['height'] * 1 + 40 || 440 }
						: responsiveThickbox.sizing( 
							  params['width'] || 630, //defaults to 630 if no parameters were added to URL
							  params['height'] || 440, //defaults to 440 if no parameters were added to URL
							  (params['TB_border']*1) || 150
						  );

					responsiveThickbox.WIDTH = imageSize.width * 1;
					responsiveThickbox.HEIGHT = imageSize.height * 1;
					imageSize.width = responsiveThickbox.WIDTH - 30;
					imageSize.height = responsiveThickbox.HEIGHT - 45;

					// responsiveThickbox.WIDTH = (params['width']*1) + 30 || 630; //defaults to 630 if no parameters were added to URL
					// responsiveThickbox.HEIGHT = (params['height']*1) + 40 || 440; //defaults to 440 if no parameters were added to URL
					// imageSize.width = responsiveThickbox.WIDTH - 30;
					// imageSize.height = responsiveThickbox.HEIGHT - 45;

					if ( useFrame ) // either iframe or ajax window
					{
						// urlNoQuery = urls.url.split('TB_');
						jQuery("#rTB_iframeContent").remove();
						if(params['modal'] != "true") //iframe no modal
						{
							jQuery("#rTB_window").append(
								"<div id='rTB_title'>" +
								"	<div id='rTB_ajaxWindowTitle'>"+caption+"</div>" +
								"	<div id='rTB_closeAjaxWindow'>" +
								"		<button type='button' id='rTB_closeWindowButton'>" +
								"			<span class='screen-reader-text'>" + rthickboxL10n.close + "</span>" +
								"			<span class='rtb-close-icon'></span>" +
								"		</button>" +
								"	</div>" +
								"</div>" +
								"<iframe frameborder='0' hspace='0' " +
								"	allowtransparency='true' " +
								"	src='" + baseURL + "?width=" + (imageSize.width + 29) + "&height=" + (imageSize.height + 17) + "' " +
								"	id='rTB_iframeContent' name='rTB_iframeContent" + Math.round(Math.random()*1000)+"' " +
								"	onload='responsiveThickbox.showIframe()' " +
								"	style='width:"+(imageSize.width + 29)+"px;height:"+(imageSize.height + 17)+"px;' >" + rthickboxL10n.noiframes + 
								"</iframe>"
							);
						} else //iframe modal
						{
							jQuery("#rTB_overlay").unbind();
							jQuery("#rTB_window").append(
								"<iframe frameborder='0' " +
								"	hspace='0' allowtransparency='true' " +
								"	src='" + baseURL + "?width=" + (imageSize.width + 29) + "&height=" + (imageSize.height + 17) + "' " +
								"	id='rTB_iframeContent' name='rTB_iframeContent"+Math.round(Math.random()*1000)+"' " +
								"	onload='responsiveThickbox.showIframe()' " +
								"	style='width:"+(imageSize.width + 29)+"px;height:"+(imageSize.height + 17)+"px;'>" + rthickboxL10n.noiframes +
								"</iframe>"
							);
						}
					} else // not an iframe, ajax
					{
						if ( jQuery("#rTB_window").css("visibility") != "visible" )
						{
							if ( params['modal'] != "true" ) //ajax no modal
							{
								jQuery("#rTB_window").append(
									"<div id='rTB_title'>" +
									"	<div id='rTB_ajaxWindowTitle'>" + caption + "</div>" +
									"	<div id='rTB_closeAjaxWindow'>" +
									"		<a href='#' id='rTB_closeWindowButton'>" +
									"			<div class='rtb-close-icon'></div>" +
									"		</a>" +
									"	</div>" +
									"</div>" +
									"<div id='rTB_ajaxContent' style='width:" + imageSize.width + "px;height:" + imageSize.height + "px'></div>");
							} else //ajax modal
							{
								jQuery("#rTB_overlay").unbind();
								jQuery("#rTB_window").append(
									"<div id='rTB_ajaxContent' class='rTB_modal' style='width:" + imageSize.width + "px;height:" + imageSize.height + "px;'></div>"
								);
							}
						} else //this means the window is already up, we are just loading new content via ajax
						{
							jQuery("#rTB_ajaxContent")[0].style.width = imageSize.width + "px";
							jQuery("#rTB_ajaxContent")[0].style.height = imageSize.height + "px";
							jQuery("#rTB_ajaxContent")[0].scrollTop = 0;
							jQuery("#rTB_ajaxWindowTitle").html(caption);
						}
					}

					jQuery("#rTB_closeWindowButton").click(responsiveThickbox.remove);

					if( is_inline )
					{
						jQuery("#rTB_ajaxContent").append(jQuery('#' + params['inlineId']).children());
						jQuery("#rTB_window").bind('rtb_unload', function () 
						{
							jQuery('#' + params['inlineId']).append( jQuery("#rTB_ajaxContent").children() ); // move elements back when you're finished
						});
						responsiveThickbox.position();
						jQuery("#rTB_load").remove();
						var window = jQuery("#rTB_window");
						window.css({'visibility':'visible'}); //for safari using css instead of show
						// if ( is_discrete ) window.css({'paddingBottom':'50px'});
					}
					else if( useFrame )
					{
						responsiveThickbox.position();
						jQuery("#rTB_load").remove();
						var window = jQuery("#rTB_window");
						window.css({'visibility':'visible'}); //for safari using css instead of show
						// if ( is_discrete ) window.css({'paddingBottom':'50px'});
					} else 
					{
						var load_url = urls.url;
						load_url += -1 === load_url.indexOf('?') ? '?' : '&';
						jQuery("#rTB_ajaxContent").load(load_url += "random=" + (new Date().getTime()),function() //to do a post change this load method
						{
							responsiveThickbox.position();
							jQuery("#rTB_load").remove();
							tb_init("#rTB_ajaxContent a.rthickbox");
							var window = jQuery("#rTB_window");
							window.css({'visibility':'visible'}); //for safari using css instead of show
							// if ( is_discrete ) window.css({'paddingBottom':'50px'});
						});
					}

					if ( ! params['modal'] ) responsiveThickbox.handleEscape( params );
				}

				$closeBtn = jQuery( '#rTB_closeWindowButton' );
				/*
				 * If the native Close button icon is visible, move focus on the button
				 * (e.g. in the Network Admin Themes screen).
				 * In other admin screens is hidden and replaced by a different icon.
				 */
				if ( $closeBtn.find( '.rtb-close-icon' ).is( ':visible' ) )
				{
					$closeBtn.focus();
				}

				var window = jQuery('#rTB_window');
				window.draggable();
				window.css( 'cursor', 'move' );

			} catch(e) 
			{
				//nothing here
			}
		},

		// helper functions below
		handleEscape: function( params ) 
		{
			jQuery(document).bind('keydown.rthickbox', function(e){
				if ( e.which == 27 ) // close
				{
					responsiveThickbox.remove();
					return false;
				}
			});
		},

		showIframe: function()
		{
			jQuery("#rTB_load").remove();
			jQuery("#rTB_window").css({'visibility':'visible'}).trigger( 'rthickbox:iframe:loaded' );
		},

		remove: function()
		{
			jQuery("#rTB_imageOff").unbind("click");
			jQuery("#rTB_closeWindowButton").unbind("click");
			jQuery( '#rTB_window' ).fadeOut( 'fast', function()
			{
				jQuery( '#rTB_window, #rTB_overlay, #rTB_HideSelect' ).trigger( 'rtb_unload' ).unbind().remove();
				jQuery( 'body' ).trigger( 'rthickbox:removed' );
			});

			jQuery( 'body' ).removeClass( 'modal-open' );
			jQuery("#rTB_load").remove();
			if (typeof document.body.style.maxHeight == "undefined") //if IE 6
			{
				jQuery("body","html").css({height: "auto", width: "auto"});
				jQuery("html").css("overflow","");
			}

			jQuery(document).unbind('.rthickbox');
			return false;
		},

		sizing: function( imageWidth, imageHeight, border )
		{
			// Resizing large images
			var pagesize = responsiveThickbox.getPageSize();
			var x = pagesize[0] - border;
			var y = pagesize[1] - border;

			if ( imageWidth > x ) 
			{
				imageHeight = imageHeight * (x / imageWidth);
				imageWidth = x;
				if ( imageHeight > y ) 
				{
					imageWidth = imageWidth * (y / imageHeight);
					imageHeight = y;
				}
			} else if ( imageHeight > y ) 
			{
				imageWidth = imageWidth * (y / imageHeight);
				imageHeight = y;
				if ( imageWidth > x ) 
				{
					imageHeight = imageHeight * (x / imageWidth);
					imageWidth = x;
				}
			}
			// End Resizing
			
			return { width: imageWidth, height: imageHeight };
		},

		position: function()
		{
			var isIE6 = typeof document.body.style.maxHeight === "undefined";
			jQuery("#rTB_window").css({marginLeft: '-' + parseInt((responsiveThickbox.WIDTH / 2),10) + 'px', width: responsiveThickbox.WIDTH + 'px'});
			if ( ! isIE6 ) { // take away IE6
				jQuery("#rTB_window").css({marginTop: '-' + parseInt((responsiveThickbox.HEIGHT / 2),10) + 'px'});
			}
		},

		parseQuery: function ( query ) 
		{
			var params = {};
			if ( ! query ) { return params; } // return empty object
			var pairs = query.split( /[;&]/ );
			for ( var i = 0; i < pairs.length; i++ )
			{
				var keyVal = pairs[i].split('=');
				if ( ! keyVal || keyVal.length != 2 ) { continue; }
				var key = unescape( keyVal[0] );
				var val = unescape( keyVal[1] );
				val = val.replace( /\+/g, ' ' );
				params[key] = val;
			}
			return params;
		},

		getPageSize: function()
		{
			var de = document.documentElement;
			var w = window.innerWidth || self.innerWidth || (de&&de.clientWidth) || document.body.clientWidth;
			var h = window.innerHeight || self.innerHeight || (de&&de.clientHeight) || document.body.clientHeight;
			arrayPageSize = [w,h];
			return arrayPageSize;
		},

		detectMacXFF: function()
		{
			var userAgent = navigator.userAgent.toLowerCase();
			if (userAgent.indexOf('mac') != -1 && userAgent.indexOf('firefox')!=-1)
			{
				return true;
			}
		}

	};

	responsiveThickbox.init('a.rthickbox, area.rthickbox, input.rthickbox'); //pass where to apply rthickbox
	responsiveThickbox.imgLoader = new Image();// preload image
	responsiveThickbox.imgLoader.src = rtb_pathToImage;
});
