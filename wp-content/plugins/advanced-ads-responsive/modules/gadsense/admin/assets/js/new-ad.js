/**
 * Advanced Ads.
 *
 * @author    Thomas Maier <thomas.maier@webgilde.com>
 * @license   GPL-2.0+
 * @link      http://webgilde.com
 * @copyright 2013-2015 Thomas Maier, webgilde GmbH
 */
;
(function ($) {
    "use strict";
	
	// On DOM loaded
	$(function(){
		
		function createNewRule(minw, w, h, hidden) {
			var rowHidden = (hidden)? '1' : '0';
			var newRuleCode = 
				'<tr data-minwidth="' + minw + '">' + 
				'<td><b><span class="row-minw">' + minw + '</span></b>&nbsp;px</td>';
			if (hidden) {
				newRuleCode += '<td colspan="2">' + respAdsAdsense.msg.notDisplayed +
				'<span class="row-w" style="display:none">' + w + '</span>' +
				'<span class="row-h" style="display:none">' + h + '</span>' +
				'<input type="hidden" class="row-hidden" value="1" />'
			} else {				
				newRuleCode += '<td><b><span class="row-w">' + w + '</span></b>&nbsp;px</td>' +
				'<td><b><span class="row-h">' + h + '</span></b>&nbsp;px</td>' + 				
				'<input type="hidden" class="row-hidden" value="0" />';
			}
			newRuleCode += '<td>' +
				'<button class="button button-secondary row-remove">' +
					'<i class="dashicons dashicons-dismiss" title="' + respAdsAdsense.msg.removeRule + '" style="vertical-align: middle;"></i> ' +
					'&nbsp;' + respAdsAdsense.msg.remove + 
				' </button>' +
				'</td></tr>';
			return $(newRuleCode);
		}
		
		$(document).on('gadsenseUnitChanged', function(){
			var type = $('#unit-type').val();
			if ('responsive' == type) {
				$('#resize-label').css('display', 'inline-block');
				$('#resize-label').next('div').css('display', 'inline-block');
			} else {
				$('#resize-label').css('display', 'none');
				$('#resize-label').next('div').css('display', 'none');
			}
			if ( 'matched-content' === type ) {
				$( '#advads-adsense-matched-content, #advads-adsense-matched-content-controls' ).css( 'display', 'block' );
			} else {
				$( '#advads-adsense-matched-content, #advads-adsense-matched-content-controls' ).css( 'display', 'none' );
			}
			manualCssCheckState();
		});
		
		$(document).on('change', '#ad-resize-type', function(ev){
			ev.preventDefault();
			manualCssCheckState();
            window.gadsenseFormatAdContent();
		});
		
		$(document).on('click', '#gadsense-css-div #new-ad-hidden', function(ev){
			var value = $(this).prop('checked');
			if (value) {
				$('#gadsense-css-div #new-ad-width, #gadsense-css-div #new-ad-height').attr('disabled', 'disabled');
			} else {
				$('#gadsense-css-div #new-ad-width, #gadsense-css-div #new-ad-height').removeAttr('disabled');
			}
		});
				
		$(document).on('click', '#gadsense-css-div #new-rule-btn', function(ev){
			ev.preventDefault();
			
			// min-width
			var minw = $('#new-ad-min-width').val();
			if ('' == minw) {
				$('#new-ad-min-width').css('background-color', 'rgba(255, 100, 60, 0.3)');
				return false;
			} else {
				$('#new-ad-min-width').css('background-color', '#fff');
			}
			
			var hidden = $('#new-ad-hidden').prop('checked');
			
			// ad width
			var w = (hidden)? '0' : $('#new-ad-width').val();
			if ('' == w) {
				$('#new-ad-width').css('background-color', 'rgba(255, 100, 60, 0.3)');
				return false;
			} else {
				$('#new-ad-width').css('background-color', '#fff');
			}
			
			// ad height
			var h = (hidden)? '0' : $('#new-ad-height').val();
			if ('' == h) {
				$('#new-ad-height').css('background-color', 'rgba(255, 100, 60, 0.3)');
				return false;
			} else {
				$('#new-ad-height').css('background-color', '#fff');
			}
			var hiddenValue = ($('#new-ad-hidden').prop('checked'))? true : false;
			var newRule = createNewRule(minw, w, h, hiddenValue);
			var ruleAdded = false;
			$('#gadsense-css-tbody tr').each(function(index, elem){
				var currentMinw = parseInt($(elem).attr('data-minwidth'));
				minw = parseInt(minw);
				if (currentMinw >= minw) {
					$(elem).before(newRule);
					ruleAdded = true;
					if (currentMinw == minw) {
						// replace row
						$(elem).remove();
					}
					return false;
				}
			});
			if (!ruleAdded) {
				$('#gadsense-css-tbody').append(newRule);
				ruleAdded = true;
			}
			if (ruleAdded) {
				$('#new-ad-min-width, #new-ad-width, #new-ad-height').val('');
				$('#new-ad-hidden').prop('checked', false);
				$('#gadsense-css-div #new-ad-width, #gadsense-css-div #new-ad-height').removeAttr('disabled');
			}
			
            window.gadsenseFormatAdContent();
			
		});
		
		$(document).on('change', 'input[name="default-width"]', function(){
			$('input[type="number"][name="advanced_ad[width]"]').val($(this).val());
		});
		
		$(document).on('change', 'input[name="default-height"]', function(){
			$('input[type="number"][name="advanced_ad[height]"]').val($(this).val());
		});
		
		$(document).on('click', '#advanced-ads-ad-parameters #default-hidden', function(){
			var hidden = $(this).prop('checked');
			if (hidden) {
				$('input[type="number"][name="default-width"], input[type="number"][name="default-height"]').attr('disabled', 'disabled');
			} else {
				$('input[type="number"][name="default-width"], input[type="number"][name="default-height"]').removeAttr('disabled');
				
			}
			window.gadsenseFormatAdContent();
		});
		
		$(document).on('click', '#gadsense-css-tbody .row-remove', function(ev){
			ev.preventDefault();
			$(this).parents('tr').hide(500, function(){
				$(this).remove();
				window.gadsenseFormatAdContent();
			});
		});
		
		/**
		 * Add media queries rules to post content
		 */
		$(document).on('gadsenseFormatAdResponsive', function(ev, adContent){
			if ('undefined' != typeof(adContent.resize) && 'manual' == adContent.resize) {
				var mediaRule = [];
				$('#gadsense-css-tbody tr').each(function(){
					var rule = 	$(this).find('.row-minw').text() + ':' + 
								$(this).find('.row-w').text() + ':' +
								$(this).find('.row-h').text();
					if (0 != $(this).find('.row-hidden').length) {
						rule += ':' + $(this).find('.row-hidden').val();
					}
					mediaRule.push(rule);
				});
				var defaultHiddenVal = ($('#default-hidden').prop('checked'))? true : false;
				adContent.defaultHidden = defaultHiddenVal;
				adContent.media = mediaRule;
				window.gadsenseAdContent = adContent;
			}
		});
		
		/**
		 * Display/Hide manual CSS related fields
		 */
		function manualCssCheckState() {
            var unitType = $('#unit-type').val();
			if ('responsive' == unitType) {
				var resizeMode = $('#ad-resize-type').val();
				if ('manual' == resizeMode) {
					$('#gadsense-css-div').css('display', 'block');
				} else {
					$('#gadsense-css-div').css('display', 'none');
				}
			} else {
				$('#gadsense-css-div').css('display', 'none');
			}
		}
		
		if ( respAdsAdsense.currentAd ) {
			$( '#advads-ad-content-adsense' ).val( JSON.stringify( respAdsAdsense.currentAd, 'false', false ) );
		}
		

		/**
		 * Get desktop and mobile version of attribute.
		 */
		function get_mobile_desktop_attr( attr, default_v ) {
			var attr = ( attr || '' ).split( ',' );
			if ( typeof default_v == 'number' ) {
				var mobile = parseInt( attr[0], 10 ) || default_v;
				var desktop = parseInt( attr[1], 10 ) || mobile;
			} else {
				var mobile = $.trim( attr[0] ) || default_v;
				var desktop = $.trim( attr[1] ) || mobile;
			}
			return [ mobile, desktop ];
		}

		/**
		 * Parse additional attributes of matched-content ad.
		 */
		$( document ).on( 'gadsenseParseAdContent', function( ev, theAd, adByGoogle ) {
			if ( 'autorelaxed' == theAd.format ) {
				theAd.matched_content_ui_type = adByGoogle.attr( 'data-matched-content-ui-type' );
				theAd.matched_content_rows_num = adByGoogle.attr( 'data-matched-content-rows-num' );
				theAd.matched_content_columns_num = adByGoogle.attr( 'data-matched-content-columns-num' );
			}
		});

		/**
		 * Set ad parameters fields from the result of parsing ad code
		 */
		$( document ).on( 'setDetailsFromAdCode', function( ev, theAd ) {
			if ( 'matched-content' == theAd.type ) {
				var ui_type = get_mobile_desktop_attr( theAd.matched_content_ui_type, 'image_sidebyside' );
				var rows_num = get_mobile_desktop_attr( theAd.matched_content_rows_num, 2 );
				var columns_num = get_mobile_desktop_attr( theAd.matched_content_columns_num, 2 );
				var $settings = $( '#advads-adsense-matched-content-controls' );

				// Enable 'Customize layout' checkbox and show inputs if 'data-matched-content-ui-type' exists
				$( '#matched-content-customize-switcher', $settings ).prop( 'checked', !! theAd.matched_content_ui_type ).change();
				$( '#matched-content-ui-type-m', $settings ).val( ui_type[0] );
				$( '#matched-content-ui-type', $settings ).val( ui_type[1] );
				$( '#matched-content-rows-num-m', $settings ).val( rows_num[0] );
				$( '#matched-content-rows-num', $settings ).val( rows_num[1]  );
				$( '#matched-content-columns-num-m', $settings ).val( columns_num[0] );
				$( '#matched-content-columns-num', $settings ).val( columns_num[1] );
			}
		});

		/**
		 * Format the post content field.
		 */
		$( document ).on( 'gadsenseFormatAdContent', function( ev, adContent ) {
			if ( 'matched-content' == adContent.unitType ) {
				var $settings = $( '#advads-adsense-matched-content-controls' );
				adContent.matched_content_customize_enabled = $( '#matched-content-customize-switcher:checked', $settings ).length;
				adContent.matched_content_ui_type_m = $( '#matched-content-ui-type-m', $settings ).val();
				adContent.matched_content_ui_type = $( '#matched-content-ui-type', $settings ).val();
				adContent.matched_content_rows_num_m = $( '#matched-content-rows-num-m', $settings ).val();
				adContent.matched_content_rows_num = $( '#matched-content-rows-num', $settings ).val();
				adContent.matched_content_columns_num_m = $( '#matched-content-columns-num-m', $settings ).val();
				adContent.matched_content_columns_num = $( '#matched-content-columns-num', $settings ).val();
			}
		});

		$( document ).on( 'change', '#advads-adsense-matched-content-controls', function( ev ) {
			window.gadsenseFormatAdContent();
		} );

	});
})(jQuery);
