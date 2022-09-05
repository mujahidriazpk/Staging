<div class="advads-condition-line-wrap">
    <input type="hidden" name="<?php echo $name; ?>[type]" value="<?php echo $options['type']; ?>"/>
    <?php if( 0 <= version_compare( ADVADS_VERSION, '1.9.1' ) ) {
	    include( ADVADS_BASE_PATH . 'admin/views/ad-conditions-string-operators.php' ); 
    } ?>
    <input type="text" name="<?php echo $name; ?>[value]" value="<?php echo $value; ?>"/>
</div>
	<p class="description"><?php echo $type_options[ $options['type'] ]['description']; ?></p>
