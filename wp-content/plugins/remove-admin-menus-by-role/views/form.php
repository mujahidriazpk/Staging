<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<script>

	jQuery(document).ready(function(){

		jQuery('#hide_menu_admin_form li span.show_submenu').click(function(){

			jQuery(this).parent('li').find('.submenu').toggle('fast');

		});

		jQuery('#hide_menu_admin_form .select_all_menu').click(function(){

			jQuery('#hide_menu_admin_form .menu input').attr('checked', '');

		});

		jQuery('#hide_menu_admin_form .deselect_all_menus').click(function(){

			jQuery('#hide_menu_admin_form .menu input').removeAttr('checked');

		});

	});

</script>
<h2>Remove admin menus by role</h2>
<form action="" method="post" id="hide_menu_admin_form">
	<?php wp_nonce_field( 'rambr' ) ?>
	<div class="column">
		<strong><?php _e('For these roles:', 'remove-admin-menus-by-role'); ?></strong>
		<ul>
		<?php
			foreach (get_editable_roles() as $role_name => $role_infos)
			{
				if($role_name != 'administrator')
					echo '<li><input type="checkbox" name="roles[]" value="'.$role_name.'" '.(in_array($role_name, $roles_selected) ? 'checked="checked"' : '').' />'.$role_name.'</li>';
			}
		?>
		</ul>
	</div>
	<div class="column">
		<strong><?php _e('Remove these menus:', 'remove-admin-menus-by-role'); ?></strong><br />
		<a href="#" class="select_all_menu"><?php _e('Select all', 'remove-admin-menus-by-role'); ?></a> <a href="#" class="deselect_all_menus"><?php _e('Deselect all', 'remove-admin-menus-by-role');?></a>
		<ul class="menus">
<?php

	foreach($menu as $k => $m)
	{
		if($m[0])
		{
			echo '<li class="menu">
			  <input type="checkbox" name="menus_hidden[]" value="'.$m[2].'" '.(in_array($m[2], $menus_hidden) ? 'checked="checked"' : '').' />'.$m[0];

			//submenus ?
			if(@is_countable($submenu[$m[2]]) && @sizeof($submenu[$m[2]]) > 0)
			{
				echo '<span class="show_submenu"></span><div class="submenu">';
				foreach($submenu[$m[2]] as $sm)
				{
					$value = htmlspecialchars_decode($m[2].'|'.$sm[2]);
					echo '<input type="checkbox" name="submenus_hidden[]" value="'.$value.'" '.(in_array($value, $submenus_hidden) ? 'checked="checked"' : '').' />'.$sm[0].'<br />';
				}
				echo '</div>';
			}

			echo '</li>';
		}
		
	}
?>

</ul></div>
<input type="submit" value="<?php _e('Save profile', 'remove-admin-menus-by-role'); ?>" class="button button-primary" />
</form>

<p>
	<h3><?php _e('You need multiple profiles? Look at', 'remove-admin-menus-by-role'); ?> <a href="https://www.info-d-74.com/en/produit/remove-admin-menus-by-role-pro-plugin-wordpress-2/" target="_blank"><?php _e('Pro version', 'remove-admin-menus-by-role'); ?> <a href="https://www.facebook.com/infod74/" target="_blank"><img src="<?php echo plugins_url( 'images/fb.png', dirname(__FILE__)) ?>" alt="" /></a></h3><br />
	<a href="https://www.info-d-74.com/en/produit/remove-admin-menus-by-role-pro-plugin-wordpress-2/" target="_blank">
		<img src="<?= plugins_url( 'images/pro_version.png', dirname(__FILE__) ); ?>" />
	</a>
</p>