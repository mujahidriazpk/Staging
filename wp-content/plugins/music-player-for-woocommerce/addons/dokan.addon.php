<?php
if(!class_exists('WCMP_DOKAN_ADDON'))
{
	class WCMP_DOKAN_ADDON
	{
		private $_wcmp;

		function __construct($wcmp)
		{
			$this->_wcmp = $wcmp;

			if(get_option('wcmp_dokan_enabled', 1) && !get_option('wcmp_dokan_hide_settings', 0))
			{
				add_action('dokan_product_edit_after_main',array($this, 'product_settings'));
				add_action('dokan_process_product_meta',array($this, 'save_product_settings'));
			}
			add_action( 'dokan_product_deleted', array($this, 'delete_product'));
			add_action('wcmp_addon_general_settings', array($this, 'general_settings'));
			add_action('wcmp_save_setting', array($this, 'save_general_settings'));
		} // End __construct

		public function general_settings()
		{
			$wcmp_dokan_enabled = get_option('wcmp_dokan_enabled', 1);
			$wcmp_dokan_hide_settings = get_option('wcmp_dokan_hide_settings', 0);
			print '<tr><td><input aria-label="'.esc_attr(__('Activate the Dokan add-on','music-player-for-woocommerce')).'" type="checkbox" name="wcmp_dokan_enabled" '.($wcmp_dokan_enabled ? 'CHECKED'  : '').'></td><td width="100%"><b>'.__('Activate the Dokan add-on', 'music-player-for-woocommerce').'</b><br><i>'.__('If the "Dokan Multivendor" plugin is installed on the website, check the checkbox to allow vendors to configure their music players.', 'music-player-for-woocommerce').'</i><br><br>
            <input type="checkbox" aria-label="'.esc_attr(__('Hide settings', 'music-player-for-woocommerce')).'" name="wcmp_dokan_hide_settings" '.($wcmp_dokan_hide_settings ? 'CHECKED' : '').'> '.__('Hides the players settings from vendors interface.', 'music-player-for-woocommerce').'</td></tr>';
		} // End general_setting

		public function save_general_settings()
		{
			update_option('wcmp_dokan_enabled', (!empty($_POST['wcmp_dokan_enabled'])) ? 1 : 0);
			update_option('wcmp_dokan_hide_settings', (!empty($_POST['wcmp_dokan_hide_settings'])) ? 1 : 0);
		} // End save_general_settings

		public function product_settings()
		{
            global $wcmp_dokan_flag;
            $wcmp_dokan_flag = true;

			wp_enqueue_style('wcmp-dokan', plugin_dir_url(__FILE__).'dokan/style.css');
			include dirname(__FILE__).'/dokan/player_options.php';
		} // End product_settings

		public function save_product_settings($post_id)
		{
            global $wcmp_dokan_flag;
            $wcmp_dokan_flag = true;

			$post = get_post($post_id);
			$this->_wcmp->save_post($post_id, $post, true);
		} // End save_product_settings

		public function delete_product($post_id)
		{
			$this->_wcmp->delete_post($post_id);
		} // End delete_product

		//******************** PRIVATE METHODS ************************


	} // End WCMP_CLOUD_DRIVE_ADDON
}

new WCMP_DOKAN_ADDON($wcmp);