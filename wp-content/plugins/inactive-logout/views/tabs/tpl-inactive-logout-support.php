<?php
/**
 * Template for Basic settings page.
 *
 * @package inactive-logout
 */

use Codemanas\InactiveLogout\Helpers;

?>

<div class="ina-settings-admin-wrap ina-settings-admin-support">

	<?php if ( ! Helpers::is_pro_version_active() ) { ?>
        <div class="ina-settings-admin-support-bg red">
            <h3>Need more features ?</h3>
            <p>Among many other features/enhancements, inactive logout pro comes with a few additional features if you feel like you need it. <a href="https://www.inactive-logout.com/">Check out the pro version here</a> to download.</p>
            <ol>
                <li>Auto browser close logout.</li>
                <li>Multiple tab sync.</li>
                <li>Individual role browser close logout enable/disable option.</li>
                <li>Override Multiple Login priority</li>
                <li>Disable inactive logout for specified pages according to your need. Check this Documentation for additional post type support.</li>
                <li>Multi-User configurations ( Coming Soon )</li>
                <li>And more..</li>
            </ol>
        </div>

        <div class="ina-settings-admin-support-bg">
            <p>Support for this plugin is free! If you encounter any issues or have any queries please use the <a href="https://wordpress.org/support/plugin/inactive-logout" target="_blank">support forums</a> or <a href="https://www.imdpen.com/contact" target="_blank">send a support mail</a>. I will reply to you at the earliest possible.</p>
            <p>If you are planning to do something creative with inactive logout, you might want to <a href="https://www.imdpen.com/contact" target="_blank">hire a freelance developer</a> to assist you.</p>
        </div>
	<?php } else { ?>
        <div class="ina-settings-admin-support-bg">
            <h3>Premium Support Ticket</h3>
            <p>Create a ticket from <a href="https://inactive-logout.com/account/">Support forum</a>. Check <a href="https://inactive-logout.com/changelogs/">site</a> for recent change logs and updates.</p>
        </div>
	<?php } ?>

	<?php if ( ! Helpers::is_pro_version_active() ) { ?>
        <div class="ina-settings-admin-support-bg">
            <h3>Rate This Plugin</h3>
            <p>We really appreciate if you can spare a minute to <a href="https://wordpress.org/plugins/inactive-logout/#reviews" target="_blank">rate the plugin.</a></p>
        </div>

        <div class="ina-settings-admin-support-bg">
            <h3>Support This Plugin</h3>
            <p>If you are using and benefiting from this plugin and wish to show your support, you can buy me a <a href="https://www.paypal.com/donate?hosted_button_id=2UCQKR868M9WE" target="_blank">coffee</a> you should know that I greatly appreciate this gesture. Every little bit helps!</p>
        </div>
	<?php } ?>

    <div class="ina-settings-admin-support-bg">
        <h3>Developer</h3>
        <p>Feel free to reach me from <a href="https://www.imdpen.com/contact" target="_blank">Here</a>, if you have any questions or queries.</p>
    </div>

</div>
