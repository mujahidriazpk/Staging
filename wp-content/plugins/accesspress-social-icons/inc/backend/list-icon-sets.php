<?php defined('ABSPATH') or die("No script kiddies please!");
for ($i = 1; $i <= 1; $i++) {
    ?>
    <div class="aps-set-wrapper">
        <h3>Set <?php echo esc_attr($i); ?></h3>
        <?php
        $icon_set_path = '../../icon-sets/set1';
        $images = glob($icon_set_path . "*.*");
        $this->print_array($images);
        ?>
    </div><!--aps-set-wrapper-->
    <?php
}
die();


