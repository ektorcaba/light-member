<?php

defined('ABSPATH') or die( "Bye bye" );

//Comprueba que tienes permisos para acceder a esta pagina
if (! current_user_can ('manage_options')) wp_die (__ ('No tienes suficientes permisos para acceder a esta página.'));
?>

	<div class="wrap">
		<h2><?php _e( 'LightMember', 'lightmember' ) ?></h2>
		<?php _e( 'Welcome to LightMeber plugin settings', 'lightmember' ) ?>
		<form method="post" action="options.php"> 
		<?php
			settings_fields( 'lightmember-group' );
			do_settings_sections( 'lightmember-group' );
		?>
		<table class="form-table">
			<tr valign="top">
			<th scope="row"><?php _e( 'Stripe Endpoint Secret', 'lightmember' ) ?></th>
			<td><input type="text" class="regular-text" name="stripe_endpoint_secret" value="<?php echo esc_attr( get_option('stripe_endpoint_secret') ); ?>" /></td>
			</tr>
			
			<tr valign="top">
			<th scope="row"><?php _e( 'Stripe Secret Key', 'lightmember' ) ?></th>
			<td><input type="text" class="regular-text" name="stripe_secret_key" value="<?php echo esc_attr( get_option('stripe_secret_key') ); ?>" /></td>
			</tr>

			<tr valign="top">
			<th scope="row">&nbsp;</th>
			<td><a href="https://dashboard.stripe.com/apikeys" target="_blank"><?php _e( 'Stripe API Keys', 'lightmember' ) ?></a></td>
			</tr>	
			
			<tr valign="top">
			<th scope="row"><?php _e( 'Stripe Customer Portal URL', 'lightmember' ) ?></th>
			<td><input type="text" class="regular-text" name="stripe_customer_portal" value="<?php echo esc_attr( get_option('stripe_customer_portal') ); ?>" /></td>
			</tr>

			<tr valign="top">
			<th scope="row">&nbsp;</th>
			<td><a href="https://dashboard.stripe.com/settings/billing/portal" target="_blank"><?php _e( 'Enable Stripe Customer Portal', 'lightmember' ) ?></a></td>
			</tr>			

			<tr valign="top">
			<th scope="row" style="color:red;"><?php _e( 'Enable Stripe Production mode?', 'lightmember' ) ?></th>
			<td>
				<select id="livemode" name="livemode">
					<option value="1" <?php echo (esc_attr( get_option('livemode') ) == 1) ? 'selected="selected"' : ''; ?>><?php _e( 'True', 'lightmember' ) ?></option>
					<option value="0" <?php echo (esc_attr( get_option('livemode') ) == 0) ? 'selected="selected"' : ''; ?>><?php _e( 'False', 'lightmember' ) ?></option>
				</select>
			</td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php _e( 'Generated Username Prefix', 'lightmember' ) ?></th>
			<td><input type="text" class="regular-text" name="lm_username_prefix" value="<?php echo esc_attr( get_option('lm_username_prefix') ); ?>" /></td>
			</tr>
			


			<tr valign="top">
			<th scope="row"><?php _e( 'Free Member Role', 'lightmember' ) ?></th>
			<td><select class="regular-text" name="free_role"><?php wp_dropdown_roles(get_option('free_role')); ?></select> <span><?php _e('Default: Subscriber','lightmember') ?></span></td>			
			</tr>

			<tr valign="top">
			<th scope="row"><?php _e( 'Pay Member Role', 'lightmember' ) ?></th>
			<td><select class="regular-text" name="member_role"><?php wp_dropdown_roles(get_option('member_role')); ?></select></td>			
			</tr>

			<tr valign="top">
			<th scope="row"><?php _e( 'Enable Log files for endpoint requests?', 'lightmember' ) ?></th>
				<td>
					<select id="enable_log" name="enable_log">
						<option value="1" <?php echo (esc_attr( get_option('enable_log') ) == 1) ? 'selected="selected"' : ''; ?>><?php _e( 'True', 'lightmember' ) ?></option>
						<option value="0" <?php echo (esc_attr( get_option('enable_log') ) == 0) ? 'selected="selected"' : ''; ?>><?php _e( 'False', 'lightmember' ) ?></option>
					</select>
				</td>
			</tr>
			
			<tr valign="top">
			<th scope="row"><?php _e( 'Email From', 'lightmember' ) ?></th>
			<td><input type="text" class="regular-text" name="email_from" value="<?php echo esc_attr( get_option('email_from') ); ?>" /></td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php _e( 'Email From name', 'lightmember' ) ?></th>
			<td><input type="text" class="regular-text" name="email_from_name" value="<?php echo esc_attr( get_option('email_from_name') ); ?>" /></td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php _e( 'Email Subject', 'lightmember' ) ?></th>
			<td><input type="text" class="regular-text" name="email_pwd_link_subject" value="<?php echo esc_attr( get_option('email_pwd_link_subject') ); ?>" /></td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php _e( 'Email message', 'lightmember' ) ?></th>
			<td><textarea class="regular-text" name="email_pwd_link_message"><?php echo esc_attr( get_option('email_pwd_link_message') ); ?></textarea></td>
			</tr>
			<tr valign="top">
			<th scope="row"></th>
			<td>{{sitename}}, {{username}}, {{password_link}}</td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php _e( 'Message to display for partially hidden content to non-members', 'lightmember' ) ?></th>
			<td><textarea class="regular-text" name="lm_partial_hide_box_content"><?php echo esc_attr( get_option('lm_partial_hide_box_content') ); ?></textarea></td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php _e( 'Message to display for fullpage hidden content to non-members', 'lightmember' ) ?></th>
			<td><textarea class="regular-text" name="lm_fullpage_hide_box_content"><?php echo esc_attr( get_option('lm_fullpage_hide_box_content') ); ?></textarea></td>
			</tr>


			<tr valign="top">
			<th scope="row"><?php _e( 'Member Page', 'lightmember' ) ?></th>
			<td><p><?php wp_dropdown_pages(array('selected' => get_option('lm_member_page'),'name' => 'lm_member_page', 'id'=> 'lm_member_page','value_field'=>'ID')); ?></p><p><?php _e('Page for Login/Register/Forgot/Profile, use this shortcode: [lm_member_profile_page]','lightmember'); ?></p></td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php _e( 'Hide admin bar for non-administrators?', 'lightmember' ) ?></th>
			<td>
				<select id="lm_hide_adminbar" name="lm_hide_adminbar">
					<option value="1" <?php echo (esc_attr( get_option('lm_hide_adminbar') ) == 1) ? 'selected="selected"' : ''; ?>><?php _e( 'True', 'lightmember' ) ?></option>
					<option value="0" <?php echo (esc_attr( get_option('lm_hide_adminbar') ) == 0) ? 'selected="selected"' : ''; ?>><?php _e( 'False', 'lightmember' ) ?></option>
				</select>
			</td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php _e( 'Redirect "wp-admin/profile.php" for non-administrators?', 'lightmember' ) ?></th>
			<td>
				<select id="lm_hide_profile" name="lm_hide_profile">
					<option value="1" <?php echo (esc_attr( get_option('lm_hide_profile') ) == 1) ? 'selected="selected"' : ''; ?>><?php _e( 'True', 'lightmember' ) ?></option>
					<option value="0" <?php echo (esc_attr( get_option('lm_hide_profile') ) == 0) ? 'selected="selected"' : ''; ?>><?php _e( 'False', 'lightmember' ) ?></option>
				</select>
			</td>
			</tr>
			
			<tr valign="top">
			<th scope="row"><?php _e( 'Enable Register form for Free users?', 'lightmember' ) ?></th>
			<td>
				<select id="lm_register_free_member" name="lm_register_free_member">
					<option value="1" <?php echo (esc_attr( get_option('lm_register_free_member') ) == 1) ? 'selected="selected"' : ''; ?>><?php _e( 'True', 'lightmember' ) ?></option>
					<option value="0" <?php echo (esc_attr( get_option('lm_register_free_member') ) == 0) ? 'selected="selected"' : ''; ?>><?php _e( 'False', 'lightmember' ) ?></option>
				</select>
			</td>
			</tr>

			
			<tr valign="top">
				<th scope="row"><?php _e( 'Disable "wp-login.php" for non-logout & non-administrators?', 'lightmember' ) ?></th>
				<td>
					<p><select id="lm_prevent_wp_login" name="lm_prevent_wp_login">
						<option value="1" <?php echo (esc_attr( get_option('lm_prevent_wp_login') ) == 1) ? 'selected="selected"' : ''; ?>><?php _e( 'True', 'lightmember' ) ?></option>
						<option value="0" <?php echo (esc_attr( get_option('lm_prevent_wp_login') ) == 0) ? 'selected="selected"' : ''; ?>><?php _e( 'False', 'lightmember' ) ?></option>
					</select></p>
					<p><strong style="color:red;"><?php _e('WARNING! This deactivates the WordPress login form. You will need an alternative method to access your account with this option enabled','lightmember'); ?></strong></p>
				</td>
			</tr>


			<tr valign="top">
				<th scope="row"><?php _e( 'Enable reCAPTCHA on login/register/forgot forms?', 'lightmember' ) ?></th>
				<td>
					<p><select id="lm_google_recaptcha" name="lm_google_recaptcha">
						<option value="1" <?php echo (esc_attr( get_option('lm_google_recaptcha') ) == 1) ? 'selected="selected"' : ''; ?>><?php _e( 'True', 'lightmember' ) ?></option>
						<option value="0" <?php echo (esc_attr( get_option('lm_google_recaptcha') ) == 0) ? 'selected="selected"' : ''; ?>><?php _e( 'False', 'lightmember' ) ?></option>
					</select></p>
					<p><?php _e('You can obtain your reCAPTCHA keys from this link:','lightmember'); ?> <a href="https://www.google.com/recaptcha/" target="_blank">reCAPTCHA</a></p>
				</td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php _e( 'reCAPTCHA Site Key', 'lightmember' ) ?></th>
			<td><input type="text" class="regular-text" name="lm_r_site_key" value="<?php echo esc_attr( get_option('lm_r_site_key') ); ?>" /></td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php _e( 'reCAPTCHA Secret Key', 'lightmember' ) ?></th>
			<td><input type="text" class="regular-text" name="lm_r_secret_key" value="<?php echo esc_attr( get_option('lm_r_secret_key') ); ?>" /></td>
			</tr>


			<tr valign="top">
			<th scope="row"><?php _e( 'Subscription new/updated Email subject', 'lightmember' ) ?></th>
			<td><textarea class="regular-text" name="lm_subscription_email_subject"><?php echo esc_attr( get_option('lm_subscription_email_subject') ); ?></textarea></td>
			</tr>


			<tr valign="top">
			<th scope="row"><?php _e( 'Subscription new/updated Email message', 'lightmember' ) ?></th>
			<td><textarea class="regular-text" name="lm_subscription_email_message"><?php echo esc_attr( get_option('lm_subscription_email_message') ); ?></textarea></td>
			</tr>
			<tr valign="top">
			<th scope="row"></th>
			<td>{{sitename}}, {{username}}, {{siteurl}}</td>
			</tr>


		</table>
		<?php submit_button(); ?>
		</form>
		<h2><?php _e("Your Endpoint Webhook", 'lightmember') ?></h2>
		<p><?php _e('Add this endpoint url to your Stripe account to receive payment events.', 'lightmember') ?> <a href="https://dashboard.stripe.com/webhooks" target="_blank"><?php _e('Add endpoint', 'lightmember') ?></a></p>
		<table class="form-table">
		<tr valign="top">
			<td><textarea style="width:100%;" class="regular-text" name="endpoint_webhook" disabled><?php
			echo plugins_url().'/light-member/webhook.php';
			?></textarea></td>
			</tr>
		</table>

<p>LightMember Plugin by <a href="https://ektorcaba.com" target="_blank">ektorcaba</a>.</p>
</div>
