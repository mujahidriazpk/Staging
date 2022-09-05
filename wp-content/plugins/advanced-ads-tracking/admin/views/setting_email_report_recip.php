<input type="text" name="<?php echo $this->plugin->options_slug; 
	?>[email-addresses]" style="width:85%;" value="<?php 
	echo esc_attr( $recipients ); 
	?>" autocomplete="email"/>
<p class="description"><?php _e( 'Separate multiple emails with commas', 'advanced-ads-tracking' ); ?></p>
