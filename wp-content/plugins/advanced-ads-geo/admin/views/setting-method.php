<?php foreach( $methods as $_key => $_method ) : ?>
<label>
    <input type="radio" name="<?php echo ADVADS_SLUG . '[' . AAGT_SLUG . '][method]' ?>" value="<?php echo $_key; ?>" <?php checked( $_key, $method ); ?>/>
    <?php echo $_method['description']; ?></label><br/>
<?php endforeach; ?>