<?php

namespace WeDevs\DokanPro\Modules\MangoPay\Support;

defined( 'ABSPATH' ) || exit; // Exit if called directly

use WeDevs\DokanPro\Modules\MangoPay\Processor\Kyc;
use WeDevs\DokanPro\Modules\MangoPay\Processor\User;

// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

/**
 * Shortcode handler class
 *
 * @since 3.5.0
 */
class Shortcode {

    /**
     * Constructor for the class
     *
     * @since 3.5.0
     */
    public function __construct() {
        // For KYC status and upload form
        add_shortcode( 'dokan_mangopay_kyc_upload_form', array( $this, 'kyc_doc_upload_form' ) );
        add_shortcode( 'dokan_mangopay_kyc_user_info', array( $this, 'kyc_doc_user_info' ) );
    }

    /**
     * Returns html for the KYC status for a user
     *
     * @since 3.5.0
     *
     * @return string
     */
    public function kyc_doc_user_info() {
        $wp_user_id      = get_current_user_id();
        $account_id      = Meta::get_mangopay_account_id( $wp_user_id );
        $mp_user         = User::get( $account_id );
        $refused_reasons = Kyc::get_refused_reasons();

        if ( empty( $mp_user ) ) {
            return;
        }

        $list_to_show = Kyc::get_doc_types( $mp_user );
        $all_docs     = Kyc::filter( $account_id );

        if ( $mp_user->KYCLevel === 'REGULAR' ) {
            $text_banner = __( 'You have successfully completed all the compliance checks.', 'dokan' );
        } else {
            $text_banner = __( 'You must upload the following documents to complete the compliance checks.', 'dokan' );
        }

        foreach ( $all_docs as &$doc ) {
            $doc->UserDocError = get_user_meta( $wp_user_id, "kyc_error_$doc->Id", true );
            $doc->Status       = strtolower( $doc->Status );
            $doc->StatusLabel  = ucfirst( str_replace( '_', ' ', $doc->Status ) );
            $doc->TypeLabel    = ucfirst( str_replace( '_', ' ', $doc->Type ) );
            $doc->CreationDate = dokan_format_date( $doc->CreationDate );

            unset( $list_to_show[ $doc->Type ] );
        }

        ob_start();

        Helper::get_template(
            'kyc-doc-user-info',
            array(
                'mp_user'         => $mp_user,
                'all_docs'        => $all_docs,
                'text_banner'     => $text_banner,
                'refused_reasons' => $refused_reasons,
                'list_to_show'    => $list_to_show,
            )
        );

        return ob_get_clean();
    }

    /**
     * Returns HTML for the KYC doc upload form
     *
     * @since 3.5.0
     *
     * @param string
     */
    public function kyc_doc_upload_form( $atts ) {
        // Get user Mangopay id
        $existing_account_id = Meta::get_mangopay_account_id( get_current_user_id() );

        // Get user Mangopay status
        $mp_user = User::get( $existing_account_id );

        if ( empty( $mp_user ) ) {
            return '';
        }

        // Mangopay KYC info
        $list_to_show = Kyc::get_doc_types( $mp_user );

        ob_start();

        Helper::get_template(
            'kyc-doc-upload-form',
            array(
                'existing_account_id' => $existing_account_id,
                'list_to_show'        => $list_to_show,
                'ubo_applicable'      => 'LEGAL' === $mp_user->PersonType && 'BUSINESS' === $mp_user->LegalPersonType,
            )
        );

        return ob_get_clean();
    }
}

// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
