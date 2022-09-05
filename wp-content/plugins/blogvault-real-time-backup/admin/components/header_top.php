<?php
	if ($this->bvinfo->isMalcare()) {
		$plugin_slug = "malcare-security";
		$brand_name = "MalCare";
		$plugin_logo = plugins_url("/../../img/mclogo.svg", __FILE__);
		$title = "Secure your website with MalCare's 360 degree protection";
		$intro_video_url = "https://youtu.be/rBuYh2dIadk";
		$header_logo_link = "https://malcare.com/?utm_source=mc_plugin_lp_logo&utm_medium=logo_link&utm_campaign=mc_plugin_lp_header&utm_term=header_logo&utm_content=image_link";
	} else {
		$plugin_slug = "blogvault-real-time-backup";
		$brand_name = "BlogVault";
		$plugin_logo = plugins_url("/../../img/bvlogo.svg", __FILE__);
		$title = "Create Smart Incremental Backups On Cloud";
		$intro_video_url = "https://youtu.be/Y4teDRL08mY";
		$header_logo_link = "https://blogvault.net/?utm_source=bv_plugin_lp_logo&utm_medium=logo_link&utm_campaign=bv_plugin_lp_header&utm_term=header_logo&utm_content=image_link";
	}
?>
<div class="header-top">
	<div class="top-links">
		<span>
			<a href="https://wordpress.org/support/plugin/<?php echo $plugin_slug; ?>/reviews/#new-post" target="_blank" rel="noopener noreferrer">
				Leave a Review
			</a>
		</span>
		&nbsp;
		<span>
			<a href="https://wordpress.org/support/plugin/<?php echo $plugin_slug; ?>/" target="_blank" rel="noopener noreferrer">
				Need Help?
			</a>
		</span>
	</div>
	<div class="logo-img">
		<a href="<?php echo $header_logo_link; ?>" target="_blank" rel="noopener noreferrer">
			<img height="65" src="<?php echo $plugin_logo; ?>" alt="Logo">
		</a>
	</div>
	<h2 class="text-center heading"><?php echo $title; ?></h2>
	<div class="text-center intro-video">
		<a href="<?php echo $intro_video_url; ?>" target="_blank" rel="noopener noreferrer">
			<img src="<?php echo plugins_url("/../../img/play-video.png", __FILE__); ?>"/>
			Watch the <?php echo $brand_name; ?> Video
		</a>
	</div>
</div>