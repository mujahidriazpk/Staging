<section>
  <select class="cs-select cs-skin-slide">
    <?php

    if ( ! empty( $post_analytics_settings_front ) ) {

      if ( is_array( $post_analytics_settings_front ) ) {

        if ( in_array( 'show-overall-front', $post_analytics_settings_front ) ) {
          ?>
          <option value="general" data-model="#general"><?php analytify_e( 'General Statistics', 'wp-analytify' ); ?></option>
          <?php
        }
      }

      if ( is_array( $post_analytics_settings_front ) ) {

        if ( in_array( 'show-country-front', $post_analytics_settings_front ) ) {
          ?>
          <option value="country" data-model="#country"><?php esc_html_e( 'Top Countries', 'wp-analytify-pro' ); ?></option>
          <?php
        }
      }

      if ( is_array( $post_analytics_settings_front ) ) {

        if ( in_array( 'show-city-front', $post_analytics_settings_front ) ) {
          ?>
          <option value="city" data-model="#city"><?php esc_html_e( 'Top Cities', 'wp-analytify-pro' ); ?></option>
          <?php
        }
      }

      if ( is_array( $post_analytics_settings_front ) ) {

        if ( in_array( 'show-os-front', $post_analytics_settings_front ) ) {
          ?>
          <option value="os" data-model="#os"><?php esc_html_e( 'Top Operating Systems', 'wp-analytify-pro' ); ?></option>
          <?php
        }
      }

      if ( is_array( $post_analytics_settings_front ) ) {

        if ( in_array( 'show-mobile-front', $post_analytics_settings_front ) ) {
          ?>
          <option value="mobile" data-model="#mobile"><?php analytify_e( 'Mobile device statistics', 'wp-analytify' ); ?></option>
          <?php
        }
      }

      if ( is_array( $post_analytics_settings_front ) ) {

        if ( in_array( 'show-keywords-front', $post_analytics_settings_front ) ) {
          ?>
          <option value="food"  data-model="#keywords"><?php esc_html_e( 'Top Keywords', 'wp-analytify-pro' ); ?></option>
          <?php
        }
      }
      if ( is_array( $post_analytics_settings_front ) ) {

        if ( in_array( 'show-social-front', $post_analytics_settings_front ) ) {

          ?>
          <option value="shopping" data-model="#social"><?php esc_html_e( 'SOCIAL MEDIA STATISTICS', 'wp-analytify-pro' ); ?></option>
          <?php
        }
      }
      if ( is_array( $post_analytics_settings_front ) ) {

        if ( in_array( 'show-browser-front', $post_analytics_settings_front ) ) {
          ?>
          <option value="dsf" data-model="#browser"><?php esc_html_e( 'TOP BROWSERS', 'wp-analytify-pro' ); ?></option>
          <?php
        }
      }


      if ( is_array( $post_analytics_settings_front ) ) {

        if ( in_array( 'show-referrer-front', $post_analytics_settings_front ) ) {
          ?>
          <option value="referrer" data-model="#referrer"><?php analytify_e( 'TOP REFERRERS', 'wp-analytify' ); ?></option>
          <?php
        }
      }
    }
    ?>
  </select>
</section>
