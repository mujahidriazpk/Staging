(function(){
	var wcmp_players = [],
		wcmp_player_counter = 0;

	window['generate_the_wcmp'] = function(isOnLoadEvent)
	{
		if(
			typeof isOnLoadEvent !== 'boolean' &&
			typeof wcmp_global_settings != 'undefined' &&
			wcmp_global_settings['onload']*1
		) return;

		if('undefined' !== typeof generated_the_wcmp) return;
		generated_the_wcmp = true;

		var $ = jQuery;
        $('.wcmp-player-container').on('click', '*', function(evt){evt.preventDefault();evt.stopPropagation();return false;}).parent().removeAttr('title');

		/**
		 * Play next player
		 */
		function _playNext( playernumber, loop )
		{
			if( playernumber+1 < wcmp_player_counter || loop)
			{

				var toPlay = playernumber+1;
                if(
                    loop &&
                    (
                        toPlay == wcmp_player_counter ||
                        $('[playernumber="'+toPlay+'"]').closest('[data-loop]').length == 0 ||
                        $('[playernumber="'+toPlay+'"]').closest('[data-loop]')[0] != $('[playernumber="'+playernumber+'"]').closest('[data-loop]')[0]
                    )
                )
                {
                    toPlay = $('[playernumber="'+playernumber+'"]').closest('[data-loop]').find('[playernumber]:first').attr('playernumber');
                }

				if( wcmp_players[ toPlay ] instanceof $ && wcmp_players[ toPlay ].is( 'a' ) ){
					if(wcmp_players[ toPlay ].is(':visible')) wcmp_players[ toPlay ].click();
					else _playNext(playernumber+1, loop);
				}
				else
				{
					if($(wcmp_players[ toPlay ].container).is(':visible')) wcmp_players[ toPlay ].play();
					else _playNext(playernumber+1, loop);
				}
			}
		};

		function _setOverImage(p)
		{
			var i = p.data('product'),
				t = $('img.product-'+i);

			if(t.length && $('[data-product="'+i+'"]').length == 1)
			{
				var o = t.offset(),
					c = p.closest('div.wcmp-player');

				c.css({'position': 'absolute', 'z-index': 999999})
				 .offset({'left': o.left+(t.width()-c.width())/2, 'top': o.top+(t.height()-c.height())/2});
			}
		};

		$.expr[':'].regex = function(elem, index, match) {
			var matchParams = match[3].split(','),
				validLabels = /^(data|css):/,
				attr = {
					method: matchParams[0].match(validLabels) ?
								matchParams[0].split(':')[0] : 'attr',
					property: matchParams.shift().replace(validLabels,'')
				},
				regexFlags = 'ig',
				regex = new RegExp(matchParams.join('').replace(/^\s+|\s+$/g,''), regexFlags);
			return regex.test($(elem)[attr.method](attr.property));
		}

		//------------------------ MAIN CODE ------------------------
		var play_all = (typeof wcmp_global_settings != 'undefined') ? wcmp_global_settings[ 'play_all' ] : true, // Play all songs
			pause_others = (typeof wcmp_global_settings != 'undefined') ? !(wcmp_global_settings['play_simultaneously']*1) : true,
			fade_out = (typeof wcmp_global_settings != 'undefined') ? wcmp_global_settings['fade_out']*1 : true,
			ios_controls = (
				typeof wcmp_global_settings != 'undefined' &&
				('ios_controls' in wcmp_global_settings) &&
				wcmp_global_settings['ios_controls']*1
			) ? true : false,
			s = $('audio.wcmp-player:not(.track):not([playernumber])'),
			m = $('audio.wcmp-player.track:not([playernumber])'),
			c = {
					pauseOtherPlayers: pause_others,
					iPadUseNativeControls: ios_controls,
					iPhoneUseNativeControls: ios_controls,
					success: function( media, dom ){
                        var duration = $(dom).data('duration'),
                            estimated_duration = $(dom).data('estimated_duration'),
                            player_index = $(dom).attr('playernumber');

                        if(typeof estimated_duration != 'undefined')
                        {
                            media.getDuration = function(){
                                return estimated_duration;
                            };
                        }

						if(typeof duration != 'undefined')
                        {
                            setTimeout((function(player_index, duration){
                                return function(){
                                    wcmp_players[ player_index ].updateDuration = function(){
                                        $(this.media).closest('.wcmp-player')
                                         .find('.mejs-duration')
                                         .html(duration);
                                    };
                                    wcmp_players[ player_index ].updateDuration();
                                };
                            })(player_index, duration), 50);
                        }

						if($(dom).attr('volume'))
                        {
                            media.setVolume(parseFloat($(dom).attr('volume')));
                            if(media.volume == 0) media.setMuted(true);
                        }

						media.addEventListener( 'timeupdate', function( evt ){
							var e = media, duration = e.getDuration();
							if(!isNaN( e.currentTime ) && !isNaN( duration ))
							{
								if( fade_out && duration - e.currentTime < 4 )
								{
									e.setVolume( e.volume - e.volume / 3 );
								}
								else
								{
									if(e.currentTime)
                                    {
                                        if(typeof e[ 'bkVolume' ] == 'undefined' )
                                            e[ 'bkVolume' ] = parseFloat( $(e).find('audio,video').attr('volume') || e.volume);
                                        e.setVolume( e.bkVolume );
                                        if(e.bkVolume == 0) e.setMuted(true);
                                    }
								}

							}
						});

						media.addEventListener( 'volumechange', function( evt ){
							var e = media, duration = e.getDuration();
							if(!isNaN( e.currentTime ) && !isNaN(duration ))
							{
								if( ( duration - e.currentTime > 4 || !fade_out) && e.currentTime )  e[ 'bkVolume' ] = e.volume;
							}
						});

						media.addEventListener( 'ended', function( evt ){
							var e = media,
                                c = $(e).closest('[data-loop="1"]');
                             e.currentTime = 0;

							if( play_all*1 || c.length)
							{
								var playernumber = $(e).attr('playernumber')*1;
                                if(isNaN(playernumber))
                                    playernumber = $(e).find('[playernumber]').attr('playernumber')*1;
                                _playNext( playernumber, c.length);
							}
						});
					}
				},
			selector = '.product-type-grouped :regex(name,quantity\\[\\d+\\])';
		s.each(function(){
			var e 	= $(this),
				src = e.find( 'source' ).attr( 'src' );

			e.attr('playernumber', wcmp_player_counter);

			c['audioVolume'] = 'vertical';
			try{
				wcmp_players[ wcmp_player_counter ] = new MediaElementPlayer(e[0], c);
			}
			catch(err)
			{
				if('console' in window) console.log(err);
			}

			wcmp_player_counter++;
			/* _setOverImage(e); */
		});


		m.each(function(){
			var e = $(this),
				src = e.find( 'source' ).attr( 'src' );

			e.attr('playernumber', wcmp_player_counter);

			c['features'] = ['playpause'];
			try{
				wcmp_players[ wcmp_player_counter ] = new MediaElementPlayer(e[0], c);
			}
			catch(err)
			{
				if('console' in window) console.log(err);
			}

			wcmp_player_counter++;
			_setOverImage(e);
			$(window).resize(function(){_setOverImage(e);});
		});

		if(!$(selector).length) selector = '.product-type-grouped [data-product_id]';
		if(!$(selector).length) selector = '.woocommerce-grouped-product-list [data-product_id]';
		if(!$(selector).length) selector = '.woocommerce-grouped-product-list [id*="product-"]';

		$(selector).each(function(){
			try
			{
				var e = $(this),
					i = (e.data( 'product_id' )||e.attr('name')||e.attr('id')).replace(/[^\d]/g,''),
					c = $( '.wcmp-player-list.merge_in_grouped_products .product-'+i+':first .wcmp-player-title' ), /* Replaced :last with :first 2018.06.12 */
					t = $('<table></table>');

				if(c.length && !c.closest('.wcmp-first-in-product').length)
				{
					c.closest('tr').addClass('wcmp-first-in-product'); /* To identify the firs element in the product */
					if(c.closest('form').length == 0)
					{
						c.closest('.wcmp-player-list').prependTo(e.closest('form'));
					}
					t.append(e.closest('tr').prepend('<td>'+c.html()+'</td>'));
					c.html('').append(t);
				}
			}
			catch(err){}
		});
	}

	window['wcmp_force_init'] = function()
	{
		delete window.generated_the_wcmp;
		generate_the_wcmp(true);
	}

	jQuery(generate_the_wcmp);
	jQuery(window).on('load', function(){
		generate_the_wcmp(true);
		var $ = jQuery,
			ua = window.navigator.userAgent;

		$('[data-lazyloading]').each(function(){ var e = $(this); e.attr('preload', e.data('lazyloading'));});
		if(ua.match(/iPad/i) || ua.match(/iPhone/i))
		{
			var p = (typeof wcmp_global_settings != 'undefined') ? wcmp_global_settings[ 'play_all' ] : true;
			if(p) // Solution to the play all in Safari iOS
			{
				$('.wcmp-player .mejs-play button').one('click', function(){

					if('undefined' != typeof wcmp_preprocessed_players) return;
					wcmp_preprocessed_players = true;

					var e = $(this);
					$('.wcmp-player audio').each(function(){
						this.play();
						this.pause();
					});
					setTimeout(function(){e.click();}, 500);
				});
			}
		}
	}).on('popstate', function(){
		if(jQuery('audio[data-product]:not([playernumber])').length) wcmp_force_init();
	});

	jQuery(document).on('scroll wpfAjaxSuccess woof_ajax_done yith-wcan-ajax-filtered wpf_ajax_success berocket_ajax_products_loaded berocket_ajax_products_infinite_loaded', wcmp_force_init);
})()