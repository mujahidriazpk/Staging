<?php
/**
 * Basic tab
 */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

set_as_network_screen();

$icon = BREEZE_PLUGIN_URL . 'assets/images/database-active.png';

$post_revisions = 0;
$drafted        = 0;
$trashed        = 0;
$comments_trash = 0;
$comments_spam  = 0;
$trackbacks     = 0;
$transients     = 0;

$total_no = 0;

if ( is_multisite() && is_network_admin() ) {
	// Count items from all network sites.
	$sites = get_sites(
		array(
			'fields' => 'ids',
		)
	);

	foreach ( $sites as $blog_id ) {
		switch_to_blog( $blog_id );
		$post_revisions += Breeze_Configuration::get_element_to_clean( 'revisions' );
		$drafted        += Breeze_Configuration::get_element_to_clean( 'drafted' );
		$trashed        += Breeze_Configuration::get_element_to_clean( 'trash' );
		$comments_trash += Breeze_Configuration::get_element_to_clean( 'comments_trash' );
		$comments_spam  += Breeze_Configuration::get_element_to_clean( 'comments_spam' );
		$trackbacks     += Breeze_Configuration::get_element_to_clean( 'trackbacks' );
		$transients     += Breeze_Configuration::get_element_to_clean( 'transient' );
		restore_current_blog();
	}
} else {
	// Count items from the current site.
	$post_revisions = Breeze_Configuration::get_element_to_clean( 'revisions' );
	$drafted        = Breeze_Configuration::get_element_to_clean( 'drafted' );
	$trashed        = Breeze_Configuration::get_element_to_clean( 'trash' );
	$comments_trash = Breeze_Configuration::get_element_to_clean( 'comments_trash' );
	$comments_spam  = Breeze_Configuration::get_element_to_clean( 'comments_spam' );
	$trackbacks     = Breeze_Configuration::get_element_to_clean( 'trackbacks' );
	$transients     = Breeze_Configuration::get_element_to_clean( 'transient' );
}

$total_no = $post_revisions + $drafted + $trashed + $comments_trash + $comments_spam + $trackbacks + $transients;

$is_optimize_disabled = is_multisite() && ! is_network_admin() && '0' !== get_option( 'breeze_inherit_settings' );

$sections_actions = array(
	'post_revisions'       => array(
		'title'    => __( 'Post Revisions', 'breeze' ),
		'describe' => __( 'Remove all post/pages revisions from DB', 'breeze' ),
		'no'       => $post_revisions,
	),
	'auto_drafts'          => array(
		'title'    => __( 'Auto Drafts', 'breeze' ),
		'describe' => __( 'Remove all post/pages auto drafts from DB', 'breeze' ),
		'no'       => $drafted,
	),
	'trashed_comments'     => array(
		'title'    => __( 'Trashed Comments', 'breeze' ),
		'describe' => __( 'Remove all trashed comments from DB', 'breeze' ),
		'no'       => $comments_trash,
	),
	'trashed_posts'        => array(
		'title'    => __( 'Trashed Posts', 'breeze' ),
		'describe' => __( 'Remove all trashed posts from DB', 'breeze' ),
		'no'       => $trashed,
	),
	'spam_comments'        => array(
		'title'    => __( 'Spam Comments', 'breeze' ),
		'describe' => __( 'Remove all the comments that are considered spam from DB', 'breeze' ),
		'no'       => $comments_spam,
	),
	'all_transients'       => array(
		'title'    => __( 'All Transients', 'breeze' ),
		'describe' => __( 'Delete expired and active transients from the WordPress database.', 'breeze' ),
		'no'       => $transients,
	),
	'trackbacks_pingbacks' => array(
		'title'    => __( 'Trackbacks/Pingbacks', 'breeze' ),
		'describe' => __( 'Remove all trackbacks/pingbakcs data from DB', 'breeze' ),
		'no'       => $trackbacks,
	),
//	'expired_transients'   => array(
//		'title'    => __( 'Expired Transients', 'breeze' ),
//		'describe' => __( 'Remove all expired transients data from DB', 'breeze' ),
//		'no'       => $transients,
//	),
//	'clean_optimizer'      => array(
//		'title'    => __( 'Clean CSS/JS Optimizer (0)', 'breeze' ),
//		'describe' => __( 'Optimise CSS/JS', 'breeze' ),
//		'no'       => '',
//	),
//	'optimize_tables'      => array(
//		'title'    => __( 'Optimize Tables', 'breeze' ),
//		'describe' => __( 'Try to optimize all the DB tables', 'breeze' ),
//		'no'       => '',
//	),
);

?>
<section>
	<div class="br-section-title">
		<img src="<?php echo $icon; ?>"/>
		<?php _e( 'DATABASE OPTIONS', 'breeze' ); ?>
	</div>
	<br/>
	<div class="cta-cleanall">

		<div class="on-off-checkbox brilbr">
			<label class="br-switcher">
				<input type="checkbox" name="br-clean-all" id="br-clean-all"/>
				<div class="br-see-state">
				</div>
			</label><br>
		</div>
	<label for="br-clean-all" class="br-clean-label"><?php _e( 'Clean All', 'breeze' ); ?> <span class="br-has">( <?php echo esc_html( $total_no ); ?> )</span></label>
	<p>
		<?php _e( 'Cleall the trashed posts and pages.', 'breeze' ); ?>
	</p>
		<p class="br-important">
			<?php
			echo '<strong>';
			_e( 'Important: ', 'breeze' );
			echo '</strong>';
			_e( 'Backup your database before using the following options!', 'breeze' );
			?>
		</p>

	<input type="button" class="simple-btn" value="<?php _e( 'Clean Now', 'breeze' ); ?>" disabled id="br-clean-all-cta">
	</div>
	<div class="br-db-boxes">
		<?php
		if ( ! empty( $sections_actions ) ) {
			foreach ( $sections_actions as $section_slug => $section_data ) {

				$no_data     = '';
				$css_opacity = '';
				if ( '' !== $section_data['no'] ) {
					if ( 0 === $section_data['no'] ) {
						$no_data     = ' (0)';
						$css_opacity = 'opac';
					} else {
						$no_data = ' (<span class="br-has">' . $section_data['no'] . '</span>)';
					}
				}
				?>
				<div class="br-db-item" data-section-title="<?php echo esc_attr( $section_data['title'] ); ?>" data-section="<?php echo esc_attr( $section_slug ); ?>">
					<img src="<?php echo BREEZE_PLUGIN_URL . 'assets/images/' . esc_attr( $section_slug ) . '.png'; ?>">
					<h3>
						<?php
						echo $section_data['title'];
						echo $no_data;
						?>
					</h3>
					<p>
						<?php echo $section_data['describe']; ?>
					</p>

					<!--<a href="#<?php echo esc_attr( $section_slug ); ?>" data-section="<?php echo esc_attr( $section_slug ); ?>" class="do_clean_action <?php echo $css_opacity; ?>"><?php echo _e( 'Clean now', 'breeze' ); ?></a>-->
				</div>
				<?php
			}
		}
		?>
	</div>
	<div class="cta-cleanall">
	<input type="button" class="simple-btn" id="optimize-selected-services" value="<?php _e( 'Optimize', 'breeze' ); ?>">
	<br/><br/>
	</div>
</section>
