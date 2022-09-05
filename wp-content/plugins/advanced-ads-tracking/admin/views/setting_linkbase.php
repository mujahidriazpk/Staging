<?php echo home_url('/'); ?><input name="<?php echo $this->plugin->options_slug; ?>[linkbase]" type="text" value="<?php
    echo $linkbase; ?>"/>/(ad id)
<p class="description"><?php
    _e('Pattern of the click tracking link. Should not collide with any posts or pages. Use chars: a-z/-', 'advanced-ads-tracking'); ?></p>