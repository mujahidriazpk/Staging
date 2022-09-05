<?php
/**
 * CSS for popup.
 *
 * @link       https://linksoftwarellc.com
 * @since      2.0.0
 *
 * @package    Wp_Terms_Popup
 * @subpackage Wp_Terms_Popup/public/partials
 */
?>

<?php
    $termsopt_opac = esc_attr(get_option('termsopt_opac'));
    $termsopacmoz = '0.8';
    $termsopac = '.80';
    $termsopacfilter = '80';

    if (!empty($termsopt_opac)) {
        if ($termsopt_opac != '10') {
            $termsopacmoz = '0.'.$termsopt_opac;
            $termsopac = '.'.$termsopt_opac.'0';
            $termsopacfilter = $termsopt_opac.'0';
        } elseif ($termsopt_opac == '10') {
            $termsopacmoz = '1.0';
            $termsopac = '1.0';
            $termsopacfilter = '100';
        }
    }
?>
<style type="text/css">
<?php if (!isset($isshortcode) || $isshortcode != 1) : ?>body { overflow: hidden; }<?php endif; ?>
.tdarkoverlay { background-color: #000000; -moz-opacity: <?php echo $termsopacmoz; ?>; opacity: <?php echo $termsopac; ?>; filter: alpha(opacity=<?php echo $termsopacfilter; ?>); }
<?php if (get_option('termsopt_javascript') == 1) : ?>.termspopupcontainer { height: 100%; background-position: center center; background-repeat: no-repeat; background-image: url(<?php echo plugins_url('wp-terms-popup/public/img/loading.gif'); ?>); }<?php endif; ?>
h3.termstitle { background: #c81f2c; color: #ffffff; }
</style>