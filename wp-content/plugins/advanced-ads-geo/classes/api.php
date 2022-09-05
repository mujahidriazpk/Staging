<?php

/**
 * API for geo location based functions
 *
 * @package     Advanced Ads Geo
 * @subpackage  Functions
 * @copyright   Copyright (c) 2015, Thomas Maier, webgilde GmbH
 * @since       1.0
 *
 * NOT USED
 */
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;
    
    class Advanced_Ads_Geo_Api {
        
        /**
         *
         * @var Advanced_Ads_Geo_Api
         */
        protected static $instance;
        
        /**
         * save if the city reader was used already
         */
        public $used_city_reader = false;
        
        /**
         * save ip of current visitor
         */
        protected $current_ip;
        
        /**
         * current visitor continent
         */
        public $current_continent;
        
        /**
         * current visitor country
         */
        public $current_country;
        
        /**
         * current visitor state/region
         */
        public $current_region;
        
        /**
         * current visitor city
         */
        public $current_city;
        
        /**
         * current visitor latitude
         */
        public $current_lat;
        
        /**
         * current visitor longitude
         */
        public $current_lon;
        
        
        
        /**
         * @var Array of GST states
         * @since 1.0.0
         */
        public static $gst_countries = array("AU", "NZ", "CA", "CN");
        
        /**
         * @var Array of languages used in the MaxMind database
         * @since 1.1
         */
        public static $locales = array( 'en' => 'English', 'de' => 'Deutsch', 'fr' => 'Français', 'es' => 'Español', 'ja' => '日本語', 'pr-BR' => 'Português', 'ru' => 'Русский', 'zh-CN' => '华语' );
        
        /**
         * @var array of EU states
         * @since 1.0
         */
        public static $eu_states = array( "AT", "BE", "BG", "HR", "CY", "CZ", "DK", "EE", "FI", "FR", "DE", "GB", "GR", "HU", "IE", "IT", "LV", "LT", "LU", "MT", "NL", "PL", "PT", "RO", "SK", "SI", "ES", "SE" );
        
        /**
         *
         * @return Advanced_Ads_Geo_Api
         */
        public static function get_instance() {
            // If the single instance hasn't been set, set it now.
            if (null === self::$instance) {
                self::$instance = new self;
            }
            
            return self::$instance;
        }
        
        /**
         get default country
         *
         * @since 1.0.0
         * @return string $country two letter country code for default country
         */
        static function default_country() {
            return $country = null;
        }
        
        /**
         * get country list
         *
         * @since 1.0.0
         * @return array $countries list of the available countries
         */
        public static function get_countries() {
            $countries = array(
                '' => '',
                'US' => __('United States', 'advanced-ads-geo' ),
                'GB' => __('United Kingdom', 'advanced-ads-geo' ),
                'EU' => __('European Union', 'advanced-ads-geo' ),
                'DE' => __('Germany', 'advanced-ads-geo' ),
                '-' => '---',
                'CONT_NA' => __('North America', 'advanced-ads-geo' ),
                'CONT_SA' => __('South America', 'advanced-ads-geo' ),
                'CONT_EU' => __('Europe', 'advanced-ads-geo' ),
                'CONT_AF' => __('Africa', 'advanced-ads-geo' ),
                'CONT_AS' => __('Asia', 'advanced-ads-geo' ),
                'CONT_OC' => __('Oceania', 'advanced-ads-geo' ),
                '--' => '---',
                'AF' => __('Afghanistan', 'advanced-ads-geo' ),
                'AX' => __('&#197;land Islands', 'advanced-ads-geo' ),
                'AL' => __('Albania', 'advanced-ads-geo' ),
                'DZ' => __('Algeria', 'advanced-ads-geo' ),
                'AS' => __('American Samoa', 'advanced-ads-geo' ),
                'AD' => __('Andorra', 'advanced-ads-geo' ),
                'AO' => __('Angola', 'advanced-ads-geo' ),
                'AI' => __('Anguilla', 'advanced-ads-geo' ),
                'AQ' => __('Antarctica', 'advanced-ads-geo' ),
                'AG' => __('Antigua and Barbuda', 'advanced-ads-geo' ),
                'AR' => __('Argentina', 'advanced-ads-geo' ),
                'AM' => __('Armenia', 'advanced-ads-geo' ),
                'AW' => __('Aruba', 'advanced-ads-geo' ),
                'AU' => __('Australia', 'advanced-ads-geo' ),
                'AT' => __('Austria', 'advanced-ads-geo' ),
                'AZ' => __('Azerbaijan', 'advanced-ads-geo' ),
                'BS' => __('Bahamas', 'advanced-ads-geo' ),
                'BH' => __('Bahrain', 'advanced-ads-geo' ),
                'BD' => __('Bangladesh', 'advanced-ads-geo' ),
                'BB' => __('Barbados', 'advanced-ads-geo' ),
                'BY' => __('Belarus', 'advanced-ads-geo' ),
                'BE' => __('Belgium', 'advanced-ads-geo' ),
                'BZ' => __('Belize', 'advanced-ads-geo' ),
                'BJ' => __('Benin', 'advanced-ads-geo' ),
                'BM' => __('Bermuda', 'advanced-ads-geo' ),
                'BT' => __('Bhutan', 'advanced-ads-geo' ),
                'BO' => __('Bolivia', 'advanced-ads-geo' ),
                'BQ' => __('Bonaire, Saint Eustatius and Saba', 'advanced-ads-geo' ),
                'BA' => __('Bosnia and Herzegovina', 'advanced-ads-geo' ),
                'BW' => __('Botswana', 'advanced-ads-geo' ),
                'BV' => __('Bouvet Island', 'advanced-ads-geo' ),
                'BR' => __('Brazil', 'advanced-ads-geo' ),
                'IO' => __('British Indian Ocean Territory', 'advanced-ads-geo' ),
                'BN' => __('Brunei Darrussalam', 'advanced-ads-geo' ),
                'BG' => __('Bulgaria', 'advanced-ads-geo' ),
                'BF' => __('Burkina Faso', 'advanced-ads-geo' ),
                'BI' => __('Burundi', 'advanced-ads-geo' ),
                'KH' => __('Cambodia', 'advanced-ads-geo' ),
                'CM' => __('Cameroon', 'advanced-ads-geo' ),
                'CA' => __('Canada', 'advanced-ads-geo' ),
                'CV' => __('Cape Verde', 'advanced-ads-geo' ),
                'KY' => __('Cayman Islands', 'advanced-ads-geo' ),
                'CF' => __('Central African Republic', 'advanced-ads-geo' ),
                'TD' => __('Chad', 'advanced-ads-geo' ),
                'CL' => __('Chile', 'advanced-ads-geo' ),
                'CN' => __('China', 'advanced-ads-geo' ),
                'CX' => __('Christmas Island', 'advanced-ads-geo' ),
                'CC' => __('Cocos Islands', 'advanced-ads-geo' ),
                'CO' => __('Colombia', 'advanced-ads-geo' ),
                'KM' => __('Comoros', 'advanced-ads-geo' ),
                'CD' => __('Congo, Democratic People\'s Republic', 'advanced-ads-geo' ),
                'CG' => __('Congo, Republic of', 'advanced-ads-geo' ),
                'CK' => __('Cook Islands', 'advanced-ads-geo' ),
                'CR' => __('Costa Rica', 'advanced-ads-geo' ),
                'CI' => __('Cote d\'Ivoire', 'advanced-ads-geo' ),
                'HR' => __('Croatia/Hrvatska', 'advanced-ads-geo' ),
                'CU' => __('Cuba', 'advanced-ads-geo' ),
                'CW' => __('Cura&Ccedil;ao', 'advanced-ads-geo' ),
                'CY' => __('Cyprus', 'advanced-ads-geo' ),
                'CZ' => __('Czech Republic', 'advanced-ads-geo' ),
                'DK' => __('Denmark', 'advanced-ads-geo' ),
                'DJ' => __('Djibouti', 'advanced-ads-geo' ),
                'DM' => __('Dominica', 'advanced-ads-geo' ),
                'DO' => __('Dominican Republic', 'advanced-ads-geo' ),
                'TP' => __('East Timor', 'advanced-ads-geo' ),
                'EC' => __('Ecuador', 'advanced-ads-geo' ),
                'EG' => __('Egypt', 'advanced-ads-geo' ),
                'GQ' => __('Equatorial Guinea', 'advanced-ads-geo' ),
                'SV' => __('El Salvador', 'advanced-ads-geo' ),
                'ER' => __('Eritrea', 'advanced-ads-geo' ),
                'EE' => __('Estonia', 'advanced-ads-geo' ),
                'ET' => __('Ethiopia', 'advanced-ads-geo' ),
                'FK' => __('Falkland Islands', 'advanced-ads-geo' ),
                'FO' => __('Faroe Islands', 'advanced-ads-geo' ),
                'FJ' => __('Fiji', 'advanced-ads-geo' ),
                'FI' => __('Finland', 'advanced-ads-geo' ),
                'FR' => __('France', 'advanced-ads-geo' ),
                'GF' => __('French Guiana', 'advanced-ads-geo' ),
                'PF' => __('French Polynesia', 'advanced-ads-geo' ),
                'TF' => __('French Southern Territories', 'advanced-ads-geo' ),
                'GA' => __('Gabon', 'advanced-ads-geo' ),
                'GM' => __('Gambia', 'advanced-ads-geo' ),
                'GE' => __('Georgia', 'advanced-ads-geo' ),
                'DE' => __('Germany', 'advanced-ads-geo' ),
                'GR' => __('Greece', 'advanced-ads-geo' ),
                'GH' => __('Ghana', 'advanced-ads-geo' ),
                'GI' => __('Gibraltar', 'advanced-ads-geo' ),
                'GL' => __('Greenland', 'advanced-ads-geo' ),
                'GD' => __('Grenada', 'advanced-ads-geo' ),
                'GP' => __('Guadeloupe', 'advanced-ads-geo' ),
                'GU' => __('Guam', 'advanced-ads-geo' ),
                'GT' => __('Guatemala', 'advanced-ads-geo' ),
                'GG' => __('Guernsey', 'advanced-ads-geo' ),
                'GN' => __('Guinea', 'advanced-ads-geo' ),
                'GW' => __('Guinea-Bissau', 'advanced-ads-geo' ),
                'GY' => __('Guyana', 'advanced-ads-geo' ),
                'HT' => __('Haiti', 'advanced-ads-geo' ),
                'HM' => __('Heard and McDonald Islands', 'advanced-ads-geo' ),
                'VA' => __('Holy See (City Vatican State)', 'advanced-ads-geo' ),
                'HN' => __('Honduras', 'advanced-ads-geo' ),
                'HK' => __('Hong Kong', 'advanced-ads-geo' ),
                'HU' => __('Hungary', 'advanced-ads-geo' ),
                'IS' => __('Iceland', 'advanced-ads-geo' ),
                'IN' => __('India', 'advanced-ads-geo' ),
                'ID' => __('Indonesia', 'advanced-ads-geo' ),
                'IR' => __('Iran', 'advanced-ads-geo' ),
                'IQ' => __('Iraq', 'advanced-ads-geo' ),
                'IE' => __('Ireland', 'advanced-ads-geo' ),
                'IM' => __('Isle of Man', 'advanced-ads-geo' ),
                'IL' => __('Israel', 'advanced-ads-geo' ),
                'IT' => __('Italy', 'advanced-ads-geo' ),
                'JM' => __('Jamaica', 'advanced-ads-geo' ),
                'JP' => __('Japan', 'advanced-ads-geo' ),
                'JE' => __('Jersey', 'advanced-ads-geo' ),
                'JO' => __('Jordan', 'advanced-ads-geo' ),
                'KZ' => __('Kazakhstan', 'advanced-ads-geo' ),
                'KE' => __('Kenya', 'advanced-ads-geo' ),
                'KI' => __('Kiribati', 'advanced-ads-geo' ),
                'KW' => __('Kuwait', 'advanced-ads-geo' ),
                'KG' => __('Kyrgyzstan', 'advanced-ads-geo' ),
                'LA' => __('Lao People\'s Democratic Republic', 'advanced-ads-geo' ),
                'LV' => __('Latvia', 'advanced-ads-geo' ),
                'LB' => __('Lebanon', 'advanced-ads-geo' ),
                'LS' => __('Lesotho', 'advanced-ads-geo' ),
                'LR' => __('Liberia', 'advanced-ads-geo' ),
                'LY' => __('Libyan Arab Jamahiriya', 'advanced-ads-geo' ),
                'LI' => __('Liechtenstein', 'advanced-ads-geo' ),
                'LT' => __('Lithuania', 'advanced-ads-geo' ),
                'LU' => __('Luxembourg', 'advanced-ads-geo' ),
                'MO' => __('Macau', 'advanced-ads-geo' ),
                'MK' => __('Macedonia', 'advanced-ads-geo' ),
                'MG' => __('Madagascar', 'advanced-ads-geo' ),
                'MW' => __('Malawi', 'advanced-ads-geo' ),
                'MY' => __('Malaysia', 'advanced-ads-geo' ),
                'MV' => __('Maldives', 'advanced-ads-geo' ),
                'ML' => __('Mali', 'advanced-ads-geo' ),
                'MT' => __('Malta', 'advanced-ads-geo' ),
                'MH' => __('Marshall Islands', 'advanced-ads-geo' ),
                'MQ' => __('Martinique', 'advanced-ads-geo' ),
                'MR' => __('Mauritania', 'advanced-ads-geo' ),
                'MU' => __('Mauritius', 'advanced-ads-geo' ),
                'YT' => __('Mayotte', 'advanced-ads-geo' ),
                'MX' => __('Mexico', 'advanced-ads-geo' ),
                'FM' => __('Micronesia', 'advanced-ads-geo' ),
                'MD' => __('Moldova, Republic of', 'advanced-ads-geo' ),
                'MC' => __('Monaco', 'advanced-ads-geo' ),
                'MN' => __('Mongolia', 'advanced-ads-geo' ),
                'ME' => __('Montenegro', 'advanced-ads-geo' ),
                'MS' => __('Montserrat', 'advanced-ads-geo' ),
                'MA' => __('Morocco', 'advanced-ads-geo' ),
                'MZ' => __('Mozambique', 'advanced-ads-geo' ),
                'MM' => __('Myanmar', 'advanced-ads-geo' ),
                'NA' => __('Namibia', 'advanced-ads-geo' ),
                'NR' => __('Nauru', 'advanced-ads-geo' ),
                'NP' => __('Nepal', 'advanced-ads-geo' ),
                'NL' => __('Netherlands', 'advanced-ads-geo' ),
                'AN' => __('Netherlands Antilles', 'advanced-ads-geo' ),
                'NC' => __('New Caledonia', 'advanced-ads-geo' ),
                'NZ' => __('New Zealand', 'advanced-ads-geo' ),
                'NI' => __('Nicaragua', 'advanced-ads-geo' ),
                'NE' => __('Niger', 'advanced-ads-geo' ),
                'NG' => __('Nigeria', 'advanced-ads-geo' ),
                'NU' => __('Niue', 'advanced-ads-geo' ),
                'NF' => __('Norfolk Island', 'advanced-ads-geo' ),
                'KR' => __('North Korea', 'advanced-ads-geo' ),
                'MP' => __('Northern Mariana Islands', 'advanced-ads-geo' ),
                'NO' => __('Norway', 'advanced-ads-geo' ),
                'OM' => __('Oman', 'advanced-ads-geo' ),
                'PK' => __('Pakistan', 'advanced-ads-geo' ),
                'PW' => __('Palau', 'advanced-ads-geo' ),
                'PS' => __('Palestinian Territories', 'advanced-ads-geo' ),
                'PA' => __('Panama', 'advanced-ads-geo' ),
                'PG' => __('Papua New Guinea', 'advanced-ads-geo' ),
                'PY' => __('Paraguay', 'advanced-ads-geo' ),
                'PE' => __('Peru', 'advanced-ads-geo' ),
                'PH' => __('Phillipines', 'advanced-ads-geo' ),
                'PN' => __('Pitcairn Island', 'advanced-ads-geo' ),
                'PL' => __('Poland', 'advanced-ads-geo' ),
                'PT' => __('Portugal', 'advanced-ads-geo' ),
                'PR' => __('Puerto Rico', 'advanced-ads-geo' ),
                'QA' => __('Qatar', 'advanced-ads-geo' ),
                'XK' => __('Republic of Kosovo', 'advanced-ads-geo' ),
                'RE' => __('Reunion Island', 'advanced-ads-geo' ),
                'RO' => __('Romania', 'advanced-ads-geo' ),
                'RU' => __('Russian Federation', 'advanced-ads-geo' ),
                'RW' => __('Rwanda', 'advanced-ads-geo' ),
                'BL' => __('Saint Barth&eacute;lemy', 'advanced-ads-geo' ),
                'SH' => __('Saint Helena', 'advanced-ads-geo' ),
                'KN' => __('Saint Kitts and Nevis', 'advanced-ads-geo' ),
                'LC' => __('Saint Lucia', 'advanced-ads-geo' ),
                'MF' => __('Saint Martin (French)', 'advanced-ads-geo' ),
                'SX' => __('Saint Martin (Dutch)', 'advanced-ads-geo' ),
                'PM' => __('Saint Pierre and Miquelon', 'advanced-ads-geo' ),
                'VC' => __('Saint Vincent and the Grenadines', 'advanced-ads-geo' ),
                'SM' => __('San Marino', 'advanced-ads-geo' ),
                'ST' => __('S&atilde;o Tom&eacute; and Pr&iacute;ncipe', 'advanced-ads-geo' ),
                'SA' => __('Saudi Arabia', 'advanced-ads-geo' ),
                'SN' => __('Senegal', 'advanced-ads-geo' ),
                'RS' => __('Serbia', 'advanced-ads-geo' ),
                'SC' => __('Seychelles', 'advanced-ads-geo' ),
                'SL' => __('Sierra Leone', 'advanced-ads-geo' ),
                'SG' => __('Singapore', 'advanced-ads-geo' ),
                'SK' => __('Slovak Republic', 'advanced-ads-geo' ),
                'SI' => __('Slovenia', 'advanced-ads-geo' ),
                'SB' => __('Solomon Islands', 'advanced-ads-geo' ),
                'SO' => __('Somalia', 'advanced-ads-geo' ),
                'ZA' => __('South Africa', 'advanced-ads-geo' ),
                'GS' => __('South Georgia', 'advanced-ads-geo' ),
                'KP' => __('South Korea', 'advanced-ads-geo' ),
                'SS' => __('South Sudan', 'advanced-ads-geo' ),
                'ES' => __('Spain', 'advanced-ads-geo' ),
                'LK' => __('Sri Lanka', 'advanced-ads-geo' ),
                'SD' => __('Sudan', 'advanced-ads-geo' ),
                'SR' => __('Suriname', 'advanced-ads-geo' ),
                'SJ' => __('Svalbard and Jan Mayen Islands', 'advanced-ads-geo' ),
                'SZ' => __('Swaziland', 'advanced-ads-geo' ),
                'SE' => __('Sweden', 'advanced-ads-geo' ),
                'CH' => __('Switzerland', 'advanced-ads-geo' ),
                'SY' => __('Syrian Arab Republic', 'advanced-ads-geo' ),
                'TW' => __('Taiwan', 'advanced-ads-geo' ),
                'TJ' => __('Tajikistan', 'advanced-ads-geo' ),
                'TZ' => __('Tanzania', 'advanced-ads-geo' ),
                'TH' => __('Thailand', 'advanced-ads-geo' ),
                'TL' => __('Timor-Leste', 'advanced-ads-geo' ),
                'TG' => __('Togo', 'advanced-ads-geo' ),
                'TK' => __('Tokelau', 'advanced-ads-geo' ),
                'TO' => __('Tonga', 'advanced-ads-geo' ),
                'TT' => __('Trinidad and Tobago', 'advanced-ads-geo' ),
                'TN' => __('Tunisia', 'advanced-ads-geo' ),
                'TR' => __('Turkey', 'advanced-ads-geo' ),
                'TM' => __('Turkmenistan', 'advanced-ads-geo' ),
                'TC' => __('Turks and Caicos Islands', 'advanced-ads-geo' ),
                'TV' => __('Tuvalu', 'advanced-ads-geo' ),
                'UG' => __('Uganda', 'advanced-ads-geo' ),
                'UA' => __('Ukraine', 'advanced-ads-geo' ),
                'AE' => __('United Arab Emirates', 'advanced-ads-geo' ),
                'UY' => __('Uruguay', 'advanced-ads-geo' ),
                'UM' => __('US Minor Outlying Islands', 'advanced-ads-geo' ),
                'UZ' => __('Uzbekistan', 'advanced-ads-geo' ),
                'VU' => __('Vanuatu', 'advanced-ads-geo' ),
                'VE' => __('Venezuela', 'advanced-ads-geo' ),
                'VN' => __('Vietnam', 'advanced-ads-geo' ),
                'VG' => __('Virgin Islands (British)', 'advanced-ads-geo' ),
                'VI' => __('Virgin Islands (USA)', 'advanced-ads-geo' ),
                'WF' => __('Wallis and Futuna Islands', 'advanced-ads-geo' ),
                'EH' => __('Western Sahara', 'advanced-ads-geo' ),
                'WS' => __('Western Samoa', 'advanced-ads-geo' ),
                'YE' => __('Yemen', 'advanced-ads-geo' ),
                'ZM' => __('Zambia', 'advanced-ads-geo' ),
                'ZW' => __('Zimbabwe', 'advanced-ads-geo' ),
            );
            
            // remove continents, if Sucuri method is used
            // todo: needs more dynamic approach
            if( 'sucuri' === Advanced_Ads_Geo_Plugin::get_current_targeting_method() ){
                unset( $countries[ 'CONT_NA' ], $countries[ 'CONT_SA' ], $countries[ 'CONT_EU' ], $countries[ 'CONT_AF' ], $countries[ 'CONT_AS' ], $countries[ 'CONT_AU' ] );
            }
            
            return apply_filters('advanced-ads-geo-countries', $countries);
        }
        
        /*
         * To change this license header, choose License Headers in Project Properties.
         * To change this template file, choose Tools | Templates
         * and open the template in the editor.
         */
        
        public function get_GeoLite_country_filename() {
			if ( ! $upload_full = Advanced_Ads_Geo_Plugin::get_instance()->get_upload_full() ) {
				return false;
			}

			// Check if prefixed file exists.
			$prefix = Advanced_Ads_Geo_Plugin::get_maxmind_file_prefix();
			$filename = $upload_full . $prefix . '-GeoLite2-Country.mmdb';
			if ( file_exists( $filename ) ) {
				return $filename;
			}

			// Check if unprefixed file exists.
			$filename = $upload_full . 'GeoLite2-Country.mmdb';
			if ( file_exists( $filename ) ) {
				return $filename;
			}

			return false;
        }
        
        public function get_GeoLite_city_filename() {
			if ( ! $upload_full = Advanced_Ads_Geo_Plugin::get_instance()->get_upload_full() ) {
				return false;
			}

			// Check if prefixed file exists.
			$prefix = Advanced_Ads_Geo_Plugin::get_maxmind_file_prefix();
			$filename = $upload_full . $prefix . '-GeoLite2-City.mmdb';
			if ( file_exists( $filename ) ) {
				return $filename;
			}

			// Check if unprefixed file exists.
			$filename = $upload_full . 'GeoLite2-City.mmdb';
			if ( file_exists( $filename ) ) {
				return $filename;
			}
                
			return false;
        }
        
        public function get_GeoIP2Country_code($ip_address) {
            // Now get the location information from the MaxMind database.
            try {
                $reader = $this->get_GeoIP2Country_reader();
                
                // todo: return default country
                
                // Look up the IP address
                $record = $reader->country($ip_address);
                
                // Get the location.
                $location = $record->country->isoCode;
                
                // MaxMind returns a blank for location if it can't find it, but we want to use get shop country to replace it.
                // todo: return default country
                return $location;
            } catch (\InvalidArgumentException $e) {
                error_log("InvalidArgumentException: " . $e->getMessage());
                return false;
            } catch (\GeoIp2\Exception\AddressNotFoundException $e) {
                error_log("AddressNotFoundException: " . $e->getMessage());
                return false;
            } catch (Exception $e) {
                return false;
            }
        }
        
        public function require_GeoIP2() {
            // May already be loaded
            if ( defined('requireGeoIP2') ){
                return;
            }
            
            /**
             * WooCommerce introduced this part
             */
            if( ! class_exists( 'MaxMind\Db\Reader', false ) ){
                require_once( AAGT_BASE_PATH . "/includes/MaxMind/Db/Reader/Decoder.php");
                require_once( AAGT_BASE_PATH . "/includes/MaxMind/Db/Reader/InvalidDatabaseException.php");
                require_once( AAGT_BASE_PATH . "/includes/MaxMind/Db/Reader/Metadata.php");
                require_once( AAGT_BASE_PATH . "/includes/MaxMind/Db/Reader/Util.php");
                require_once( AAGT_BASE_PATH . "/includes/MaxMind/Db/Reader.php");
            }
            
            /**
             * WooCommerce DIDN’T introduce this part, so we check it separately
             */
            if ( ! interface_exists( 'GeoIp2\ProviderInterface', false )  // needed to prevent conflicts with GeoIP Detect plugin, because class_exists() doesn’t seem to work here
                && ! class_exists( 'GeoIp2\ProviderInterface', false ) ){
                    
                    require_once( AAGT_BASE_PATH . '/includes/GeoIp2/Compat/JsonSerializable.php' );
                    require_once( AAGT_BASE_PATH . "/includes/GeoIp2/ProviderInterface.php");
                    require_once( AAGT_BASE_PATH . "/includes/GeoIp2/Exception/GeoIp2Exception.php");
                    require_once( AAGT_BASE_PATH . "/includes/GeoIp2/Exception/AddressNotFoundException.php");
                    require_once( AAGT_BASE_PATH . "/includes/GeoIp2/Exception/AuthenticationException.php");
                    require_once( AAGT_BASE_PATH . "/includes/GeoIp2/Exception/HttpException.php");
                    require_once( AAGT_BASE_PATH . "/includes/GeoIp2/Exception/InvalidRequestException.php");
                    require_once( AAGT_BASE_PATH . "/includes/GeoIp2/Exception/OutOfQueriesException.php");
                    require_once( AAGT_BASE_PATH . "/includes/GeoIp2/Model/AbstractModel.php");
                    require_once( AAGT_BASE_PATH . "/includes/GeoIp2/Model/Country.php");
                    require_once( AAGT_BASE_PATH . "/includes/GeoIp2/Model/City.php");
                    require_once( AAGT_BASE_PATH . "/includes/GeoIp2/Model/ConnectionType.php");
                    require_once( AAGT_BASE_PATH . "/includes/GeoIp2/Model/Domain.php");
                    require_once( AAGT_BASE_PATH . "/includes/GeoIp2/Model/Insights.php");
                    require_once( AAGT_BASE_PATH . "/includes/GeoIp2/Model/Isp.php");
                    require_once( AAGT_BASE_PATH . "/includes/GeoIp2/Record/AbstractRecord.php");
                    require_once( AAGT_BASE_PATH . "/includes/GeoIp2/Record/AbstractPlaceRecord.php");
                    require_once( AAGT_BASE_PATH . "/includes/GeoIp2/Record/City.php");
                    require_once( AAGT_BASE_PATH . "/includes/GeoIp2/Record/Continent.php");
                    require_once( AAGT_BASE_PATH . "/includes/GeoIp2/Record/Country.php");
                    require_once( AAGT_BASE_PATH . "/includes/GeoIp2/Record/Location.php");
                    require_once( AAGT_BASE_PATH . "/includes/GeoIp2/Record/MaxMind.php");
                    require_once( AAGT_BASE_PATH . "/includes/GeoIp2/Record/Postal.php");
                    require_once( AAGT_BASE_PATH . "/includes/GeoIp2/Record/RepresentedCountry.php");
                    require_once( AAGT_BASE_PATH . "/includes/GeoIp2/Record/Subdivision.php");
                    require_once( AAGT_BASE_PATH . "/includes/GeoIp2/Record/Traits.php");
                    require_once( AAGT_BASE_PATH . "/includes/GeoIp2/WebService/Client.php");
                    
                    require_once( AAGT_BASE_PATH . "/includes/GeoIp2/Database/Reader.php");
            }
            
            define('requireGeoIP2', true);
        }
        
        public function get_GeoIP2_city_reader() {
            // Now get the location information from the MaxMind database.
            try {
                $this->require_GeoIP2();
                
                $filename = $this->get_GeoLite_city_filename();
                if ($filename === FALSE){
                    return false;
                }
                
                // Create a new Reader and point it to the database.
                return new \GeoIp2\Database\Reader($filename);
            } catch (Exception $e) {
                return false;
            }
        }
        
        public function get_GeoIP2_country_reader() {
            // get the location information from the MaxMind database.
            try {
                $this->require_GeoIP2();
                
                $filename = $this->get_GeoLite_country_filename();
                if ($filename === FALSE)
                    return false;
                    
                    // Create a new Reader and point it to the database.
                    return new \GeoIp2\Database\Reader($filename);
            } catch (Exception $e) {
                return false;
            }
        }
        
        public function get_real_IP_address() {
            
            if ( isset($this->current_ip) ) {
                return $this->current_ip;
            }
            
            if( defined('ADVANCED_ADS_GEO_TEST_IP')){
		        return ADVANCED_ADS_GEO_TEST_IP;
            }
            
            $ip = $this->get_raw_IP_address();
            
            $this->current_ip = filter_var( $ip, FILTER_VALIDATE_IP );
            
            return apply_filters('get-ip-address', $this->current_ip);
        }
        
	/**
	 * Get IP address from various sources
	 */
        public function get_raw_IP_address( ){

        	if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {  // cloudflare proxy, see https://support.cloudflare.com/hc/en-us/articles/200170876-I-can-t-install-mod-cloudflare-and-there-s-no-plugin-to-restore-original-visitor-IP-What-should-I-do-
                $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
            } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {   // IP from shared internet
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  // IP from proxy
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }

	    /**
	     * $_SERVER['REMOTE_ADDR'] or $_SERVER['HTTP_CF_CONNECTING_IP'] or $_SERVER['HTTP_X_FORWARDED_FOR'] and maybe others could return
	     * an IP address like `79.68.108.126, 79.68.108.126` which we just strip to the first value
	     * since MaxMind wouldn’t work with a double IP address
	     * 
	     * http://www.howgeek.com/2011/06/04/_serverhttp_x_forwarded_for-returns-multiple-ips-what-to-do/
	     */
	    if (strpos( $ip, ',') ) {
		    $ip_exploded = explode( ',', $ip );

		    $ip = trim( reset( $ip_exploded ) );
	    }

            return $ip;
        }
        
        /**
         * check if the given country code belongs to an EU state
         *
         * @param str $country two letter country code
         * @return bool true if is EU member state
         */
        public function is_eu_state( $country = '' ){
            
            return in_array( $country, self::$eu_states );
            
        }
        
    }

