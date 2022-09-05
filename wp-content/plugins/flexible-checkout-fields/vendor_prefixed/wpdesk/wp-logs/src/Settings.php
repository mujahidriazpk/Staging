<?php

namespace FcfVendor\WPDesk\Logger;

use Psr\Log\LogLevel;
final class Settings
{
    /** @var string */
    public $level = \Psr\Log\LogLevel::DEBUG;
    /** @var bool */
    public $use_wc_log = \true;
    /** @var bool */
    public $use_wp_log = \true;
}
