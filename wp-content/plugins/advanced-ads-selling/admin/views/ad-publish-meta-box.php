<div class="pre" style="margin: 5px 0; padding: 5px; background-color: #ffdede;"><?php

if ( $warning ) : echo $warning; endif;

if ( $do_import ) : ?><input type="hidden" name="advads-selling-add-to-placement" value="<?php echo $p_slug; ?>"><?php endif; ?>
</div>