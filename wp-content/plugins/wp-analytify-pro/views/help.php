<div class="support">

	<h3><?php _e( 'Email Support', 'wp-analytify-pro' ) ?></h3>

	<div class="support-content check" style="display: block;">
		<?php $_analytify_license_key = get_option( 'analytify_license_key' ); ?>
		<?php if ( ! empty( $_analytify_license_key ) and  get_option( 'analytify_license_status' ) ) : ?>
			<p><?php _e( 'Fetching license details, please wait...', 'wp-analytify-pro' ); ?></p>
		<?php else : ?>
			<p><?php _e( 'We couldn\'t find your license information. Please switch to the License tab and enter your license.', 'wp-analytify-pro' ); ?></p>
			<p><?php _e( 'Once completed, you may visit this tab to view your support details.', 'wp-analytify-pro' ); ?></p>
		<?php endif; ?>
	</div>

	<div class="support-content full" style="display: none;">

		<style type="text/css" media="screen" scoped>

			body .wpanalytify .support .support-content {
			  overflow: hidden;
			  width: 727px;
			}

			body .wpanalytify .support .support-content .intro {
			  margin-bottom: 20px;
			}

			body .wpanalytify .support .support-content .submission-success p {
			  padding: 2px;
			  margin: 0.5em 0;
			  font-size: 13px;
			  line-height: 1.5;
			}

			body .wpanalytify .support .support-content .submission-error{
				margin: 5px 0 15px;
				border-left-color: #dc3232;
				background: #fff;
			    border-left: 4px solid #fff;
			    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
			    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
			    margin: 5px 15px 2px;
			    padding: 1px 12px;
			}

			body .wpanalytify .support .support-content .analytify-support-form {
			  width: 475px;
			  float: left;
			}

			body .wpanalytify .support .support-content .analytify-support-form p {
			  width: auto;
			}

			body .wpanalytify .support .support-content .analytify-support-form .field {
			  margin-bottom: 5px;
			}

			body .wpanalytify .support .support-content .analytify-support-form input[type=text],body .wpanalytify .support .support-content .analytify-support-form textarea {
			  width: 100%;
			}

			body .wpanalytify .support .support-content .analytify-support-form .field.from label {
			  float: left;
			  line-height: 28px;
			  display: block;
			  font-weight: bold;
			}

			body .wpanalytify .support .support-content .analytify-support-form .field.from select {
			  float: right;
			  width: 400px;
			}

			body .wpanalytify .support .support-content .analytify-support-form .field.from .note {
			  clear: both;
			  padding-top: 5px;
			}

			body .wpanalytify .support .support-content .analytify-support-form .field.email-message textarea {
			  height: 170px;
			}

			body .wpanalytify .support .support-content .analytify-support-form .note {
			  font-size: 12px;
			  color: #666;
			}

			body .wpanalytify .support .support-content .analytify-support-form .submit-form {
			  overflow: hidden;
			  padding: 10px 0;
			}

			body .wpanalytify .support .support-content .analytify-support-form .button {
			  float: left;
			}

			body .wpanalytify .support .support-content .submission-error, body .wpanalytify .support .support-content .submission-success{
				background: #fff;
				border-left: 4px solid #dc3232;
				-webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
				box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
				margin: 11px 0 11px;
				padding: 1px 12px;
			}
			body .wpanalytify .support .support-content .submission-success{
				border-left-color: #46b450;
			}

		</style>

		<section class="analytify-support-form">

			<p class="intro">
				<?php printf( __( 'You have an %1$s active %2$s license. You will get front-of-the-line email support when submitting a support request below.', 'wp-analytify-pro' ), '<strong>', '</strong>' ) ?>
			</p>

			<div class="submission-success" style="display: none;">
				<p><strong><?php _e( 'Success!', 'wp-analytify-pro' ) ?></strong> — <?php _e( 'Thanks for submitting your support request. We\'ll be in touch soon.', 'wp-analytify-pro' ) ?></p>
			</div>

			<div class="submission-error api-error" style="display: none;">
				<p><strong><?php _e( 'Error!', 'wp-analytify-pro' ) ?></strong> — </p>
			</div>

			<div class="submission-error xhr-error" style="display: none;">
				<p><strong><?php _e( 'Error!', 'wp-analytify-pro' ) ?></strong> — <?php _e( 'There was a problem submitting your request:', 'wp-analytify-pro' ) ?></p>
			</div>

			<div class="submission-error email-error" style="display: none;">
				<p><strong><?php _e( 'Error!', 'wp-analytify-pro' ) ?></strong> — <?php _e( 'Please select your email address.', 'wp-analytify-pro' ) ?></p>
			</div>

			<div class="submission-error subject-error" style="display: none;">
				<p><strong><?php _e( 'Error!', 'wp-analytify-pro' ) ?></strong> — <?php _e( 'Please enter a subject', 'wp-analytify-pro' ) ?>.</p>
			</div>

			<div class="submission-error message-error" style="display: none;">
				<p><strong><?php _e( 'Error!', 'wp-analytify-pro' ) ?></strong> — <?php _e( 'Please enter a message.', 'wp-analytify-pro' ) ?></p>
			</div>

			<form target="_blank" method="post" action="https://analytify.io/plugin-support-form/?action=priority-support&product=wp-analytify-pro&key=<?php echo $this->get_license_key(); ?>" id="support-email-form">

				<div class="field from">
					<label><?php _e( 'From:', 'wp-analytify-pro' ) ?></label>
					<select name="email" id="analytify-support-email" required="required">
						<option value="">— <?php _e( 'Select your email address', 'wp-analytify-pro' ) ?> —</option>
					</select>

					<p class="note">
						<?php printf( __( 'Replies will be sent to this email address. Update your name &amp; email in %1$s Your Account%2$s.' , 'wp-analytify-pro' ), '<a href="https://analytify.io/your-account/">', '</a>' ) ?>
					</p>

				</div>

				<div class="field subject">
					<input type="text" name="subject" placeholder="Subject" id="analytify-support-subject"  required="required">
					<input type="hidden" name="site_url" value="<?php echo home_url(); ?>">
				</div>

				<div class="field email-message">
					<textarea name="message" placeholder="Message" id="analytify-support-message"  required="required"></textarea>
				</div>

				<div class="field checkbox diagnostic-check">
					<label>
						<input type="checkbox" name="diagnostic-check" value="1" checked="checked" id="analytify-support-include-report">
						<?php _e( 'Include your diagnostic information and error log (shown below)', 'wp-analytify-pro' ) ?>
					</label>
				</div>

				<div class="submit-form">
					<button type="submit" class="button" id="btn-analytify-send-email">Send Email</button>
					<span style="display:none"><strong><?php _e( 'Success!', 'wp-analytify-pro' ) ?></strong> — <?php _e( 'Thanks for submitting your support request. We\'ll be in touch soon.', 'wp-analytify-pro' ) ?></span>
				</div>

				<p class="note trouble">
					<?php printf( __( 'Having trouble submitting the form? Email your support request to %1$s support@analytify.io%2$s instead.' , 'wp-analytify-pro' ), ' <a href="mailto:support@analytify.io">', '</a>' ) ?>
				</p>

			</form>

		</section>

		<script>! function(a) {
		    var b = a(".analytify-support-form form"),
		        c = a(".submit-form", b);
		    is_submitting = !1;
		    // var d = a(".remote-diagnostic input", b),
		    //     e = a(".remote-diagnostic-content", b);
		    // d.on("click", function() {
		    //     d.prop("checked") ? e.show() : e.hide()
		    // });
		    var f = ajaxurl.replace("/admin-ajax.php", "/images/wpspin_light");
		    window.devicePixelRatio >= 2 && (f += "-2x"), f += ".gif", b.submit(function(d) {
		        if (d.preventDefault(), !is_submitting) {
		            is_submitting = !0, a(".button", b).blur();
		            var e = a(".ajax-spinner", c);
		            e[0] ? e.show() : (e = a('<img src="' + f + '" alt="" class="ajax-spinner general-spinner" />'), c.append(e)), a(".submission-error").hide();
		            var g = ["email", "subject", "message"],
		                h = {},
		                i = !1;
		            a.each(b.serializeArray(), function(b, c) {
		                h[c.name] = c.value, a.inArray(c.name, g) > -1 && "" === c.value && (a("." + c.name + "-error").fadeIn(), i = !0)
		            });
		            // var j = a("input[name=remote-diagnostic]", b).is(":checked");
		            // if (j && "" === h["remote-diagnostic-content"] && (a(".remote-diagnostic-content-error").fadeIn(), i = !0), i)
		            // 	return e.hide(), void(is_submitting = !1);

		            if( a("input[name=diagnostic-check]", b).is(":checked") )
		            	h["diagnostic-content"] = a(".debug-log-textarea").val();

		         	a.ajax({
		                url: b.prop("action"),
		                type: "POST",
		                dataType: "JSON",
		                cache: !1,
		                data: h,
		                error: function(b, c, d) {
		                    var f = a(".xhr-error");
		                    a("p", f).append(" " + d + " (" + c + ")"), f.show(), e.hide(), is_submitting = !1
		                },
		                success: function(c) {
		                	//console.log(typeof c.errors); return;
		                    if ("undefined" != typeof c.errors) {
		                        var d = a(".api-error");
		                        return a.each(c.errors, function(b, c) {
		                            return a("p", d).append(c), !1
		                        }), d.show(), e.hide(), void(is_submitting = !1)
		                    }
		                    a(".submission-success").show(), b.hide(), e.hide(), is_submitting = !1
		                }
		            })
		        }
		    })
		}(jQuery);</script>

	</div>
</div>
