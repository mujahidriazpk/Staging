<?php

/**
 * Metabox for Subscription Activity Content
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! empty( $activities ) ): ?>
	<ul class="order_notes">
		<?php
		foreach ( $activities as $activity ) : ?>
			<li rel="<?php echo $activity->id ?>" class="note <?php echo $activity->status ?>">
				<div class="note_content">
					<p><?php echo $activity->description ?></p>
				</div>
				<p class="meta">
					<abbr class="exact-date" title="<?php echo $activity->timestamp_date ?>"><?php printf( __( 'added on %1$s at %2$s', 'yith-woocommerce-subscription' ), date_i18n( wc_date_format(), strtotime( $activity->timestamp_date ) ), date_i18n( wc_time_format(), strtotime( $activity->timestamp_date ) ) ); ?></abbr>
				</p>
			</li>
		<?php endforeach ?>
	</ul>
<?php endif ?>