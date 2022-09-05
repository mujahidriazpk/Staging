<?php

namespace WPDesk\FCF\Free\Notice;

/**
 * Interface for class that supports notice displayed in admin panel.
 */
interface Notice {

	/**
	 * Returns unique key of notice.
	 *
	 * @return string
	 */
	public function get_notice_name(): string;

	/**
	 * Returns status if notice is active.
	 *
	 * @return bool Do show notice?
	 */
	public function is_active(): bool;

	/**
	 * Returns server path for view template.
	 *
	 * @return string Server path relative to plugin /templates directory.
	 */
	public function get_template_path(): string;

	/**
	 * Returns variables with values using in view template.
	 *
	 * @return string[] Args extract in view template.
	 */
	public function get_vars_for_view(): array;

	/**
	 * Disables visible notice.
	 *
	 * @param bool $is_permanently .
	 *
	 * @return void
	 * @internal
	 */
	public function set_notice_as_hidden( bool $is_permanently );
}
