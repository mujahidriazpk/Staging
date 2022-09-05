<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<?php wc_print_notices(); ?>
<?php do_action( 'woocommerce_before_customer_login_form' ); ?>
<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>
<?php if(isset($_GET['login'])&&$_GET['login']=='failed' && !isset($_GET['pr-errors'])){?>

<div class="woocommerce-notices-wrapper" >
  <div class="woocommerce-message woocommerce-error" style="margin:0;" role="alert"><?php echo __('Email Address / Password not recognized. Please check and try again.', 'wc_simple_auctions');?></div>
</div>
<?php }?>
<?php if(isset($_GET['login'])&&$_GET['login']=='failed' && isset($_GET['pr-errors'])){
	$error = json_decode(base64_decode($_GET['pr-errors']));
	?>
<div class="woocommerce-notices-wrapper" >
  <div class="woocommerce-message woocommerce-error" style="margin:0;" role="alert"><?php echo $error->loggedin_reached_limit;?></div>
</div>
<?php }?>
<?php if(isset($_GET['login'])&&$_GET['login']=='approve'){?>
<div class="woocommerce-notices-wrapper" >
  <div class="woocommerce-message woocommerce-error" style="margin:0;" role="alert">Your account has been disabled. Please <a href="/contact/" title="">contact</a> advertising support.</div>
</div>
<?php }?>
<?php if(isset($_GET['login'])&&$_GET['login']=='freeze'){?>
<div class="woocommerce-notices-wrapper" >
  <div class="woocommerce-message woocommerce-error" style="margin:0;" role="alert">Your account has been suspended please contact <a href="/contact/" title="">contact</a> admin@shopadoc.com</div>
</div>
<?php }?>
<div class="row" id="customer_login">
  <div class="col-md-12 login-form">
    <?php endif; ?>
    
    <!--<h2><?php esc_html_e( 'Sign-in', 'dokan-theme' ); ?></h2>-->
    
    <form method="post" class="login" style="margin:0;">
      <?php do_action( 'woocommerce_login_form_start' ); ?>
      <p class="form-row form-row-wide">
        <label for="username">
          <?php esc_html_e( 'Username or email address', 'dokan-theme' ); ?>
          <span class="required">*</span></label>
        <input type="text" class="input-text form-control" name="username" id="username" autocomplete="username"/>
      </p>
      <p class="form-row form-row-wide">
        <label for="password">
          <?php esc_html_e( 'Password', 'dokan-theme' ); ?>
          <span class="required">*</span></label>
        <input class="input-text form-control" type="password" name="password" id="password" autocomplete="current-password" />
        <span toggle="#password" class="fa fa-fw fa-eye field-icon toggle-password"></span> </p>
      <?php do_action( 'woocommerce_login_form' ); ?>
      <p class="form-row">
        <label for="rememberme" class="inline container_checkbox">
          <input name="rememberme" type="checkbox" id="rememberme" value="forever" />
          <span class="checkmark"></span> <span class="rememberme_text">
          <?php _e( 'Remember me', 'dokan-theme' ); ?>
          </span> </label>
      </p>
      <p class="form-row">
        <?php wp_nonce_field( 'woocommerce-login' ); ?>
        <button type="submit" class="dokan-btn dokan-btn-theme" name="login" value="<?php esc_attr_e( 'Login', 'dokan-theme' ); ?>">
        <?php _e( 'Login', 'dokan-theme' ); ?>
        </button>
      </p>
      <p class="lost_password"> <a href="<?php echo esc_url( wc_lostpassword_url() ); ?>">
        <?php esc_html_e( 'Lost your password?', 'dokan-theme' ); ?>
        </a> </p>
      <?php do_action( 'woocommerce_login_form_end' ); ?>
    </form>
    <?php if ( get_option('woocommerce_enable_myaccount_registration') === 'yes' && get_option( 'users_can_register' ) == '1' ) : ?>
  </div>
  <div class="col-md-6 reg-form hide" >
    <h2>
      <?php esc_html_e( 'Register', 'dokan-theme' ); ?>
    </h2>
    <form id="register" method="post" class="register">
      <?php do_action( 'woocommerce_register_form_start' ); ?>
      <?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
      <p class="form-row form-group form-row-wide">
        <label for="reg_username">
          <?php esc_html_e( 'Username', 'dokan-theme' ); ?>
          <span class="required">*</span></label>
        <input type="text" class="input-text form-control" name="username" id="reg_username" value="<?php if ( ! empty( $_POST['username'] ) ) esc_attr( $_POST['username'] ); ?>" required="required" />
      </p>
      <?php endif; ?>
      <p class="form-row form-group form-row-wide">
        <label for="reg_email">
          <?php esc_html_e( 'Email address', 'dokan-theme' ); ?>
          <span class="required">*</span></label>
        <input type="email" class="input-text form-control" name="email" id="reg_email" value="<?php if ( ! empty( $_POST['email'] ) ) esc_attr($_POST['email']); ?>" required="required" />
      </p>
      <?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
      <p class="form-row form-group form-row-wide">
        <label for="reg_password">
          <?php esc_html_e( 'Password', 'dokan-theme' ); ?>
          <span class="required">*</span></label>
        <input type="password" class="input-text form-control" name="password" id="reg_password" value="<?php if ( ! empty( $_POST['password'] ) ) esc_attr( $_POST['password'] ); ?>" required="required" minlength="6" />
      </p>
      <?php endif; ?>
      
      <!-- Spam Trap -->
      <div style="left:-999em; position:absolute;">
        <label for="trap">
          <?php esc_html_e( 'Anti-spam', 'dokan-theme' ); ?>
        </label>
        <input type="text" name="email_2" id="trap" tabindex="-1" />
      </div>
      <?php do_action( 'woocommerce_register_form' ); ?>
      <?php do_action( 'register_form' ); ?>
      <p class="form-row">
        <?php wp_nonce_field( 'woocommerce-register', '_wpnonce' ); ?>
        <button type="submit" class="dokan-btn dokan-btn-theme" name="register" value="<?php esc_attr_e( 'Register', 'dokan-theme' ); ?>">
        <?php _e( 'Register', 'dokan-theme' ); ?>
        </button>
      </p>
      <?php do_action( 'woocommerce_register_form_end' ); ?>
    </form>
  </div>
</div>
<?php endif; ?>
<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
<script type="text/javascript">
	//jQuery(".entry-header .entry-title").text("Sign-in");
</script>
<style type="text/css">
.responsive-menu-label{
	display:block !important;
}
</style>
