<div class="advads-group-slider-options"
    <div>
        <label>
            <strong>
                <?php _e('Slide delay', 'slider-ads' ); ?>
            </strong>
            <input type="number" name="advads-groups[<?php echo $group->id; ?>][options][slider][delay]" value="<?php echo $delay; ?>"/>
        </label>
        <p class="description"><?php _e('Pause for each ad slide in milliseconds', 'slider-ads' ); ?></p>
        <br>
        <label>
            <strong>
                <?php _e('Random order', 'slider-ads' ); ?>
            </strong>
            <input type="checkbox" name="advads-groups[<?php echo $group->id; ?>][options][slider][random]"
            <?php if ($random) : ?>
                checked = "checked"
            <?php endif; ?>
            />
        </label>
        <p class="description"><?php _e('Display ads in the slider in a random order', 'slider-ads' ); ?></p>
    </div>
</div>