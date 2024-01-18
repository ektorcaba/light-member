<?php

defined('ABSPATH') or die( "Bye bye" );




function lm_hide_adminbar() {
	if (!current_user_can('administrator') && !is_admin()) {
    if(get_option('lm_hide_adminbar')==1){
	    add_filter( 'show_admin_bar', '__return_false' );
    }
	}
}


function lm_hide_profile(){
    if(get_option('lm_hide_profile')==1){
        //admin won't be affected
        if (current_user_can('manage_options')) return '';

        //if we're at admin profile.php page
        if (strpos ($_SERVER ['REQUEST_URI'] , 'wp-admin/profile.php' )) {
            wp_redirect (home_url()); // to page like: example.com/my-profile/
            exit();
        }
    }
}


add_action('init', 'lm_prevent_wp_login');

function lm_prevent_wp_login() {
    global $pagenow;
    $action = (isset($_GET['action'])) ? $_GET['action'] : '';
    if (!current_user_can('administrator') && !is_admin()) {
      if(get_option('lm_prevent_wp_login')==1){
        if( $pagenow == 'wp-login.php' && ( ! $action || ( $action && ! in_array($action, array('logout'))))) {
          wp_redirect(home_url());
          exit();
        }
      }
    }
}



add_action( 'init', 'lm_hide_profile', 20 );
add_action('after_setup_theme', 'lm_hide_adminbar');


// El hook admin_menu ejecuta la funcion lm_lightmember_admin_menu
add_action( 'admin_menu', 'lm_lightmember_admin_menu' );
add_action( 'admin_init', 'lm_register_lightmember_settings' );
 
// Top level menu del plugin
function lm_lightmember_admin_menu()
{
	add_menu_page("LightMember","LightMember",'manage_options',LIGHTM_PATH . '/admin/settings.php','','dashicons-groups'); //Crea el menu
}



function lm_add_lightmember_stylesheet() 
{
    wp_enqueue_style( 'lightmember_css', plugins_url( '/css/style.css', __FILE__ ) );
}

add_action('wp_enqueue_scripts', 'lm_add_lightmember_stylesheet');




function lm_register_lightmember_settings() { // whitelist options
    register_setting( 'lightmember-group', 'stripe_endpoint_secret' );
    register_setting( 'lightmember-group', 'stripe_secret_key' );
    register_setting( 'lightmember-group', 'stripe_customer_portal' );
    register_setting( 'lightmember-group', 'livemode' );
    register_setting( 'lightmember-group', 'member_role' );
    register_setting( 'lightmember-group', 'free_role' );
    register_setting( 'lightmember-group', 'enable_log' );
    register_setting( 'lightmember-group', 'email_from' );
    register_setting( 'lightmember-group', 'email_from_name' );
    register_setting( 'lightmember-group', 'email_pwd_link_subject');
    register_setting( 'lightmember-group', 'email_pwd_link_message');
    register_setting( 'lightmember-group', 'lm_member_login');
    register_setting( 'lightmember-group', 'lm_member_edit');
    register_setting( 'lightmember-group', 'lm_member_profile');
    register_setting( 'lightmember-group', 'lm_member_page');
    register_setting( 'lightmember-group', 'lm_username_prefix', array('default' => 'u'));
    register_setting( 'lightmember-group', 'lm_hide_adminbar', array('default' => 0));
    register_setting( 'lightmember-group', 'lm_hide_profile',array('default' => 0));
    register_setting( 'lightmember-group', 'lm_prevent_wp_login',array('default' => 0));
    register_setting( 'lightmember-group', 'lm_register_free_member',array('default' => 1));
    register_setting( 'lightmember-group', 'lm_google_recaptcha',array('default' => 0));
    register_setting( 'lightmember-group', 'lm_r_site_key' );
    register_setting( 'lightmember-group', 'lm_r_secret_key' );
    register_setting( 'lightmember-group', 'lm_partial_hide_box_content', array('default' => 'Unlock the full content by becoming a member today!'));
    register_setting( 'lightmember-group', 'lm_fullpage_hide_box_content', array('default' => 'Unlock the full content by becoming a member today!.'));
    register_setting( 'lightmember-group', 'lm_subscription_email_message',array('default' => __('Thank you for your subscription to {{sitename}}!.\n You can now enjoy all its benefits by accessing your account.\n\n {{siteurl}}','lightmember')));
    register_setting( 'lightmember-group', 'lm_subscription_email_subject',array('default' => __('Your subscription has been updated!','lightmember')));


    add_role( 'member', 'Member', array( 'read' => true, 'level_0' => true ) );  
    

    
  }



  add_filter( 'plugin_row_meta', 'lm_donation_plugin_row_meta', 10, 2 );


  function lm_donation_plugin_row_meta( $links, $file ) {
  
    if ( strpos( $file, 'light-member.php' ) !== false ) {
      $new_links = array(
        '<a href="'.LM_DONATION_URL.'" target="_blank">'.__('Donate','lightmember').'</a>'
        );
      
      $links = array_merge( $links, $new_links );
    }
    
    return $links;
  }




  function lm_lightmember_member_profile($atts = [], $content = null, $tag = ''){
      
      if(is_user_logged_in()){
        $user = wp_get_current_user();

        $content = '<div class="lm_profile_box"><p>'.__('Welcome to your member area.','lightmember').'</p><h2>'.__('Welcome','lightmember').', '.$user->user_login.'</h2>';
        
        if (( in_array( get_option('member_role'), (array) $user->roles ) ) OR (current_user_can('administrator') OR is_admin())) {
          if(!empty(get_user_meta($user->ID, 'period_end',true))){
            $content .= '<div class="lm_flash_message_i">'.__('You are part of the members\' community. Your membership is valid until','lightmember').'&nbsp;'.date(__('Y-m-d','lightmember'),get_user_meta($user->ID, 'period_end',true)).'</div>';
          }else{
            $content .= '<div class="lm_flash_message_i">'.__('You are part of the members\' community.','lightmember').'</div>';
          }
            
        }else{
            $content .= '<div class="lm_flash_message_e">'.__('You are currently not a member of the site','lightmember').'</div>';
        }

        $content .= '<p>';
        if (( in_array( get_option('member_role'), (array) $user->roles ) ) OR (current_user_can('administrator') OR is_admin())) {
            if(!empty(get_option('stripe_customer_portal'))){
                $content .= '<a href="'.get_option('stripe_customer_portal').'" target="_blank">'.__('Subscriptions','lightmember').'</a>&nbsp;<span>|</span>&nbsp;';
            }
            
        }
        
        $content .= '<a href="'.wp_logout_url().'">'.__('Log out','lightmember').'</a></p></div>';


      }

      return $content;
  }
  
  add_shortcode('lm_member_profile', 'lm_lightmember_member_profile');






  function lm_stripe_invoices($atts = [], $content = null, $tag = '')
  {
      
      if(is_user_logged_in()){
          $user_id = get_current_user_id();
          
          $facturas = get_user_meta($user_id, 'pagos');

          if(!empty($facturas)){
            $content = '<br /><h3>'.__('Invoices','lightmember').'</h3><div class="invoices_table">';
            $content .= '<div class="invoice_entry invoice_entry_header"><div>'.__('No.','lightmember').'</div><div>'.__('Date','lightmember').'</div><div>&nbsp;</div></div>';
            foreach($facturas as $key => $entry){
                foreach($entry as $key => $factura){
                    $content .= '<div class="invoice_entry"><div>'.$factura['number'].'</div>'
                        .'<div>'.gmdate("d/m/Y", $factura['created']).'</div>'
                        .'<div><a href="'.$factura['hosted_invoice_url'].'" target="_blank">'.__('View invoice','lightmember').'</a></div></div>';
                }
            }
            $content .= '</div>';
        }
      }
      return $content;
  }
  
  add_shortcode('lm_stripe_invoices', 'lm_stripe_invoices');



add_action( 'show_user_profile', 'lm_lightmember_period_extra_field' );
add_action( 'edit_user_profile', 'lm_lightmember_period_extra_field' );




function lm_lightmember_period_extra_field( $user ) {
  if (current_user_can('administrator') && is_admin()) {

    if(in_array( get_option('member_role'), (array) $user->roles )){
  ?>
    <h3><?php _e("Member data", "lightmember"); ?></h3>
    <table class="form-table">
      <?php
        if(!empty(get_user_meta( $user->ID, 'stripe_customer',true))){
          echo '<tr>'
            .'<th><label for="stripe_customer">'.__("Stripe Customer ID",'lightmember').'</label></th>'
            .'<td>'.get_user_meta( $user->ID, 'stripe_customer',true).'</td>'
            .'</tr>';
        }
      ?>
      <tr>
        <th><label for="period_end"><?php _e("Member Period end"); ?></label></th>
        <td>
          <?php
          if(!empty(get_user_meta( $user->ID, 'period_end', true))){
            echo '<input name="period" type="datetime-local" value="'.date("Y-m-d\TH:i", get_user_meta( $user->ID, 'period_end', true)).'" /></td>';
          }else{
            echo '<input name="period" type="datetime-local" value="'.date("Y-m-d\TH:i").'" /></td>';
          }
          ?>
      </tr>
    </table>
  <?php
    }
  }
}



function lm_update_lightmember_period_extra_field($user_id) {
  if ( current_user_can('edit_user',$user_id) )
    if(!empty($_POST['period'])){
      update_user_meta($user_id, 'period_end', strtotime($_POST['period']));
    }
}
add_action('edit_user_profile_update', 'lm_update_lightmember_period_extra_field');








function lm_lightmember_edit_member_profile($atts = [], $content = null, $tag = ''){
      global $wp;
  if(is_user_logged_in()){
      $user = wp_get_current_user();

  if($_POST['lm_action_user']=='lm_edit_user'){


    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $nickname = $_POST['nickname'];
    $email = $_POST['email'];
    $url = $_POST['url'];
    $description = $_POST['description'];
    $userdata = array(
    'ID'            =>  $user->ID,
    'first_name'    =>  $first_name,
    'last_name'     =>  $last_name,
    'nickname'      =>  $nickname,
    'display_name'  =>  $nickname,
    'description'   =>  $description
    );
 
    
    if(!empty($_POST['pwd1']) AND !empty($_POST['pwd2'])){
      if($_POST['pwd1'] == $_POST['pwd2']){
        wp_set_password($_POST['pwd1'],$user->ID);
      }

    }

    $userp = wp_update_user($userdata);

    do_action('lm_save_custom_fields', $user->ID, $_POST);

   

    if($userp){
      $flash_message = '<div class="lm_flash_message">'.__('Changes saved!','lightmember').'</div>';
    }else{
      $flash_message = '<div class="lm_flash_message_e">'.__('ERROR!, The data has not been saved','lightmember').'</div>';  
    }

}



$content .= '<br /><div class="lm_edit_form"><h3>'.__('Edit profile','lightmember').'</h3>'.$flash_message.'<form class="lm_edit_member" method="post">'
.'<p class="lm_entry_form"><label for="u">'.__('User','lightmember').'</label>'
.'<input type="text" name="u" value="'.$user->user_login.'" disabled></p>'
.'<p class="lm_entry_form"><label for="e">'.__('Email','lightmember').'</label>'
.'<input type="text" name="e" value="'.$user->user_email.'" placeholder="'.__('Email','lightmember').'" disabled/></p>'
.'<p class="lm_entry_form"><label for="first_name">'.__('First name','lightmember').'</label>'
.'<input type="text" name="first_name" value="'.get_user_meta( $user->ID, 'first_name',true).'" placeholder="'.__('First Name','lightmember').'" /></p>'
.'<p class="lm_entry_form"><label for="last_name">'.__('Last name','lightmember').'</label>'
.'<input type="text" name="last_name" value="'.get_user_meta( $user->ID, 'last_name',true).'" placeholder="'.__('Last Name','lightmember').'" /></p>'
.'<p class="lm_entry_form"><label for="nickname">'.__('Display name','lightmember').'</label>'
.'<input type="text" name="nickname" value="'.get_user_meta( $user->ID, 'nickname',true).'" placeholder="'.__('Nickname','lightmember').'" /></p>'
.'<p class="lm_entry_form"><label for="description">'.__('Description','lightmember').'</label>'
.'<textarea name="description" placeholder="'.__('Biographical Info','lightmember').'">'.get_user_meta( $user->ID, 'description',true).'</textarea></p>'
.'<p class="lm_entry_form"><label for="pwd1">'.__('New password','lightmember').'</label>'
.'<input type="password" id="pwd1" name="pwd1" placeholder="'.__('Leave empty to avoid updating','lightmember').'" /></p>'
.'<p class="lm_entry_form"><label for="pwd2">'.__('Repeat new password','lightmember').'</label>'
.'<input type="password" id="pwd2" name="pwd2" placeholder="'.__('Leave empty to avoid updating','lightmember').'" /></p>'
.'<input type="hidden" name="lm_action_user" value="lm_edit_user" />';

$content.= apply_filters( 'lm_add_custom_fields', "" );

$content .='<p class="lm_entry_form"><input id="submit" type="submit" name="post_submit" value="'.__('Submit','lightmember').'" /></p>'
.'</form></div>';

$content .= '<script>'
.'function lm_checkpwd(){
  if (document.getElementById("pwd1").value == document.getElementById("pwd2").value) {
    document.getElementById("pwd1").style.border = "1px solid green";
    document.getElementById("pwd2").style.border = "1px solid green";
  } else {
    document.getElementById("pwd1").style.border = "1px solid red";
    document.getElementById("pwd2").style.border = "1px solid red";
  }

}

document.getElementById("pwd1").onkeyup = function() {lm_checkpwd()};
document.getElementById("pwd2").onkeyup = function() {lm_checkpwd()};'
.'</script>';



}else{

  if(isset($_GET['reset'])){

    if($_POST['lm_action_user']=='lm_forgot_password'){
      $captcha_response = lm_check_recaptcha($_POST['g-recaptcha-response']);
        
      if($captcha_response){
        add_filter( 'retrieve_password_message', 'lm_custom_retrieve_password_message', 10, 4);
        add_filter('retrieve_password_title', 'lm_custom_retrieve_password_title', 10 ,3);

        if(retrieve_password($_POST['email'])){
          $flash_message = '<div class="lm_flash_message">'.__('Reset link sent to your mail!','lightmember').'</div>';
        }else{
          $flash_message = '<div class="lm_flash_message_e">'.__('ERROR!, Incorrect mail address','lightmember').'</div>';
        }
      }else{
        $flash_message = '<div class="lm_flash_message_e">'.__('ERROR!, invalid captcha','lightmember').'</div>';
      }
    }

    $content = '<div class="lm_login_form"><h3>'.__('Forgot your password?','lightmember').'</h3>'.$flash_message.'<form class="lm_edit_member" method="post">'
    .'<p class="lm_entry_form"><label for="email">'.__('Email','lightmember').'</label>'
    .'<input type="text" name="email" value="" placeholder="'.__('Email','lightmember').'" autocomplete="off" /></p>'
    .'<input type="hidden" name="lm_action_user" value="lm_forgot_password" />';
    
    $content.= apply_filters( 'lm_add_custom_fields_forgot_form', "" );
    
    if(get_option('lm_google_recaptcha')){
      if(get_option('lm_r_site_key')){
        $content .= '<script>function lm_enableBtn(){document.getElementById(\'submit\').disabled = false;}</script>';
        $content.='<p><div class="g-recaptcha" data-sitekey="'.get_option('lm_r_site_key').'" data-callback="lm_enableBtn"></div></p>';
      }
    }

    $content .='<p class="lm_entry_form"><input id="submit" type="submit" name="post_submit" value="'.__('Reset password','lightmember').'" disabled="disabled" /></p>'
    .'</form></div>';

    $register = '<a href="'.home_url( add_query_arg( array(), $wp->request ) ).'?register">'.__('Register','lightmember').'</a> | ';
    $content .= '<p>'.((get_option('lm_register_free_member')==1)?$register:'').'<a href="'.home_url( add_query_arg( array(), $wp->request ) ).'">'.__('Log in','lightmember').'</a></p>';



  }elseif(isset($_GET['register'])){
    if(get_option('lm_register_free_member')==1){

      if($_POST['lm_action_user']=='lm_register_user'){
        
        $captcha_response = lm_check_recaptcha($_POST['g-recaptcha-response']);
        
        if($captcha_response){
          //registrar usuario
          if(lm_register_logic($_POST)){
            $flash_message = '<div class="lm_flash_message">'.__('Success!, You will receive an email with instructions to set up your password','lightmember').'</div>';
          }else{
            $flash_message = '<div class="lm_flash_message_e">'.__('ERROR!, This email is already registered','lightmember').'</div>';
          }
        }else{
          $flash_message = '<div class="lm_flash_message_e">'.__('ERROR!, invalid captcha','lightmember').'</div>';
        }
      }

      $content = '<div class="lm_login_form"><h3>'.__('Register','lightmember').'</h3>'.$flash_message.'<form class="lm_edit_member" method="post">'

      .'<p class="lm_entry_form"><label for="email">'.__('Email','lightmember').'</label>'
      .'<input type="text" name="email" value="" placeholder="'.__('Email','lightmember').'" autocomplete="off" required /></p>'
      .'<input type="hidden" name="lm_action_user" value="lm_register_user" />';
      
      $content.= apply_filters( 'lm_add_custom_fields_register_form', "" );

      if(get_option('lm_google_recaptcha')){
        if(get_option('lm_r_site_key')){
          $content .= '<script>function lm_enableBtn(){document.getElementById(\'submit\').disabled = false;}</script>';
          $content.='<p><div class="g-recaptcha" data-sitekey="'.get_option('lm_r_site_key').'" data-callback="lm_enableBtn"></div></p>';
        }
      }

      $content .='<p class="lm_entry_form"><input id="submit" type="submit" name="post_submit" value="'.__('Register','lightmember').'" disabled="disabled" /></p>'
      .'</form></div>';

      
      $content .= '<p><a href="'.home_url( add_query_arg( array(), $wp->request ) ).'">'.__('Log in','lightmember').'</a> | <a href="'.home_url( add_query_arg( array(), $wp->request ) ).'?reset">'.__('Forgot your password?','lightmember').'</a></p>';


    }else{
      wp_redirect(home_url());
    }

  }elseif(isset($_GET['recover'])){
    $ok = 0;

    if($_POST['lm_action_user']=='lm_setup_password'){
        

      $captcha_response = lm_check_recaptcha($_POST['g-recaptcha-response']);
        
      if($captcha_response){
        //establecer contraseña usuario
        if(lm_setup_password_logic($_POST)){
          $flash_message = '<div class="lm_flash_message">'.__('Success!, Your password has been set','lightmember').'</div>';
          $ok = 1;
        }else{
          $flash_message = '<div class="lm_flash_message_e">'.__('ERROR!, Your password could not be set, please try again','lightmember').'</div>';
          $ok = 0;
        }
      }else{
        $flash_message = '<div class="lm_flash_message_e">'.__('ERROR!, invalid captcha','lightmember').'</div>';
      }
      

    }

if($ok==0){
    $content = '<div class="lm_login_form"><h3>'.__('Set password','lightmember').'</h3>'.$flash_message.'<form class="lm_edit_member" method="post">'
    .'<p class="lm_entry_form"><label for="pwd1">'.__('Password','lightmember').'</label>'
    .'<input type="password" id="pwd1" name="pwd1" autocomplete="off" required /></p>'
    .'<p class="lm_entry_form"><label for="pwd2">'.__('Confirm password','lightmember').'</label>'
    .'<input type="password" id="pwd2" name="pwd2" autocomplete="off" required /></p>'
    .'<input type="hidden" name="rp_key" value="'.$_GET['key'].'" />'
    .'<input type="hidden" name="user_login" value="'.$_GET['login'].'" />'
    .'<input type="hidden" name="lm_action_user" value="lm_setup_password" />';


    $content.= apply_filters( 'lm_add_custom_fields_login_form', "" );
    

    
    if(get_option('lm_google_recaptcha')){
      if(get_option('lm_r_site_key')){
        $content .= '<script>function lm_enableBtn(){document.getElementById(\'submit\').disabled = false;}</script>';
        $content.='<p><div class="g-recaptcha" data-sitekey="'.get_option('lm_r_site_key').'" data-callback="lm_enableBtn"></div></p>';
      }
    }

    $content .='<p class="lm_entry_form"><input id="submit" type="submit" name="post_submit" value="'.__('Set you password','lightmember').'" disabled="disabled"/></p>'
    .'</form></div>';


    $content .= '<script>'
    .'function lm_checkpwd(){
      if (document.getElementById("pwd1").value == document.getElementById("pwd2").value) {
        document.getElementById("pwd1").style.border = "1px solid green";
        document.getElementById("pwd2").style.border = "1px solid green";
      } else {
        document.getElementById("pwd1").style.border = "1px solid red";
        document.getElementById("pwd2").style.border = "1px solid red";
      }
    
    }
    
    document.getElementById("pwd1").onkeyup = function() {lm_checkpwd()};
    document.getElementById("pwd2").onkeyup = function() {lm_checkpwd()};'
    .'</script>';
    
  }else{

    $content = '<div class="lm_login_form"><h3>'.__('Set password','lightmember').'</h3>'.$flash_message;

    $content .= '<p><a href="'.home_url( add_query_arg( array(), $wp->request ) ).'">'.__('Log in','lightmember').'</a> | <a href="'.home_url( add_query_arg( array(), $wp->request ) ).'?reset">'.__('Forgot your password?','lightmember').'</a></p>';
  }



  }else{

      if($_POST['lm_action_user']=='lm_login_user'){
        
        $captcha_response = lm_check_recaptcha($_POST['g-recaptcha-response']);
        
        if($captcha_response){
          if(!lm_login_logic($_POST)){
            $flash_message = '<div class="lm_flash_message_e">'.__('ERROR!, check your email or password','lightmember').'</div>';
          }else{
            $flash_message = '<div class="lm_flash_message">'.__('Success!','lightmember').'</div>';      
          }
        }else{
          $flash_message = '<div class="lm_flash_message_e">'.__('ERROR!, invalid captcha','lightmember').'</div>';
        }

      }


    $content = '<div class="lm_login_form"><h3>'.__('Log in','lightmember').'</h3>'.$flash_message.'<form class="lm_edit_member" method="post">'

    .'<p class="lm_entry_form"><label for="email">'.__('Email','lightmember').'</label>'
    .'<input type="text" name="email" value="" placeholder="'.__('Email','lightmember').'" autocomplete="off" required /></p>'
    .'<p class="lm_entry_form"><label for="pwd">'.__('Password','lightmember').'</label>'
    .'<input type="password" id="pwd" name="pwd" autocomplete="off" required /></p>'
    .'<input type="hidden" name="lm_action_user" value="lm_login_user" />';

    if(get_option('lm_google_recaptcha')){
      if(get_option('lm_r_site_key')){
        $content .= '<script>function lm_enableBtn(){document.getElementById(\'submit\').disabled = false;}</script>';
        $content.='<p><div class="g-recaptcha" data-sitekey="'.get_option('lm_r_site_key').'" data-callback="lm_enableBtn"></div></p>';
      }
    }
    
    $content.= apply_filters( 'lm_add_custom_fields_login_form', "" );
    
    $content .='<p class="lm_entry_form"><input id="submit" type="submit" name="post_submit" value="'.__('Sign in','lightmember').'" disabled="disabled" /></p>'
    .'</form></div>';

    $register = '<a href="'.home_url( add_query_arg( array(), $wp->request ) ).'?register">'.__('Register','lightmember').'</a> | ';
    $content .= '<p>'.((get_option('lm_register_free_member')==1)?$register:'').'<a href="'.home_url( add_query_arg( array(), $wp->request ) ).'?reset">'.__('Forgot your password?','lightmember').'</a></p>';


  }

}

return $content;
}

add_shortcode('lm_member_edit_profile', 'lm_lightmember_edit_member_profile');





function lm_check_recaptcha($post){
  $response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".get_option('lm_r_secret_key')."&response=".$post), true);
  return $response['success'];
}



function lm_setup_password_logic($post){
  $user = check_password_reset_key(esc_attr($post['rp_key']), esc_attr($post['user_login']));

  if(!is_wp_error($user)){

    if($post['pwd1']==$post['pwd2']){

      wp_set_password( $post['pwd1'], $user->ID );
      return true;
    }else{
      return false;
    }
  }else{
    return false;
  }
}


function lm_external_enqueue_scripts() {
  wp_enqueue_script('google-recaptcha', 'https://www.google.com/recaptcha/api.js');
}
add_action( 'wp_enqueue_scripts', 'lm_external_enqueue_scripts' );



function lm_login_logic($post){
  global $wp;
  $user = get_user_by( 'email', esc_attr($post['email']));



  if( $user ) {
    if(wp_check_password($post['pwd'], $user->user_pass, $user->ID)){

      wp_set_current_user( $user->ID, $user->user_login );
      wp_set_auth_cookie( $user->ID );
      wp_redirect(home_url( add_query_arg( array(), $wp->request ) ));
    }else{
      return false;
    }
  }else{
    return false;
  }
}

function lm_register_logic($post){


  if ( !email_exists( $post['email'] ) ) {
    $random_password = wp_generate_password( $length = 12, $include_standard_special_chars = false );
    $u = wp_create_user( lm_get_random_unique_username((empty(get_option('lm_username_prefix')) ? 'u':get_option('lm_username_prefix'))), $random_password, $post['email']);
    
    //Enviar email de password al usuario
    add_filter( 'wp_mail_from', function ( $original_email_address ) {
      return get_option('email_from');
    } );

    add_filter( 'wp_mail_from_name', function ( $original_email_from ) {
      return get_option('email_from_name');
    });

    add_filter( 'retrieve_password_message', 'lm_custom_retrieve_password_message', 10, 4);
    add_filter('retrieve_password_title', 'lm_custom_retrieve_password_title', 10 ,3);

    retrieve_password($post['email']);
    return true;
    
  }else{
    return false;
  }

}


function lm_custom_retrieve_password_title($message, $user_login, $user_data ) {

	$patrones = array('{{sitename}}','{{username}}');
	$valores = array(get_bloginfo('name'), rawurlencode($user_login));

	$subject = str_replace($patrones, $valores, get_option('email_pwd_link_subject'));

    return $subject;
}




function lm_custom_retrieve_password_message($message, $key, $user_login, $user_data ){
    // Setting up message for retrieve password

	$patrones = array('{{sitename}}','{{username}}', '{{password_link}}');
	$valores = array(get_bloginfo('name'), rawurlencode($user_login), get_permalink(get_option('lm_member_page'))."?recover&key=$key&login=" . rawurlencode($user_login));

	$message = str_replace($patrones, $valores, get_option('email_pwd_link_message'));

    return $message;

}



function lm_meta_box_protect() {
global $post;

echo '<p><label for="lm_members_only">'.__('Member\'s Only', 'lightmember').'</label> <select id="lm_members_only" name="lm_members_only">'
.'<option value="1" '.(get_post_meta($post->ID,'lm_members_only',true) ? 'selected="selected"' : '').'>'.__( 'True', 'lightmember' ).'</option>'
.'<option value="0" '.(!get_post_meta($post->ID,'lm_members_only',true) ? 'selected="selected"' : '').'>'.__( 'False', 'lightmember' ).'</option>'
.'</select></p>';

  echo '<p>'.__('Hide partial content with <b>[lm_members_only]</b>Hidden content<b>[/lm_members_only]</b>','lightmember').'</p>';


  echo '<p><label for="lm_comments_status">'.__('Hide comments to non-members?', 'lightmember').'</label> <select id="lm_comments_status" name="lm_comments_status">'
  .'<option value="1" '.(get_post_meta($post->ID,'lm_comments_status',true) ? 'selected="selected"' : '').'>'.__( 'True', 'lightmember' ).'</option>'
  .'<option value="0" '.(!get_post_meta($post->ID,'lm_comments_status',true) ? 'selected="selected"' : '').'>'.__( 'False', 'lightmember' ).'</option>'
  .'</select></p>';


}


add_action( 'add_meta_boxes', 'lm_private_content_meta_box_add' );

function lm_private_content_meta_box_add()
{
  add_meta_box( 'lm_meta_box_private_content', __('Hide entire content to non-members','lightmember'), 'lm_meta_box_protect' );
}


function lm_save_metabox_data( $post_id ) {
  if (current_user_can('administrator') && is_admin()) {
    delete_post_meta($post_id, 'lm_members_only');
    update_post_meta( $post_id, 'lm_members_only', $_POST['lm_members_only'] );
    delete_post_meta($post_id, 'lm_comments_status');
    update_post_meta( $post_id, 'lm_comments_status', $_POST['lm_comments_status'] );
    
  }
}

add_action( 'save_post', 'lm_save_metabox_data' );




function lm_get_random_unique_username( $prefix = '' ){
  $user_exists = 1;
  do {
     $rnd_str = sprintf("%06d", mt_rand(1, 999999));
     $user_exists = username_exists( $prefix . $rnd_str );
 } while( $user_exists > 0 );
 return $prefix . $rnd_str;
}



function lm_member_profile_page(){

  echo lm_lightmember_member_profile();
  echo lm_lightmember_edit_member_profile();
  echo lm_stripe_invoices();


}

add_shortcode('lm_member_profile_page', 'lm_member_profile_page');







function lm_hide_partial_content($atts = [], $content = null, $tag = '')
{
    $atts = array_change_key_case((array) $atts, CASE_LOWER);

    $user = wp_get_current_user();


    if (( in_array( get_option('member_role'), (array) $user->roles ) ) OR (current_user_can('administrator') OR is_admin())) {
      if ((current_user_can('administrator') OR is_admin())) {
        $o .= apply_filters('the_content', '<div class="lm_admin_box"><div class="lm_hide_title_box">'.__('Hidden content','lightmember').'</div>'.$content.'</div>');
      }else{
        $o .= apply_filters('the_content', $content);
      }

    }else{
      if(!empty($atts['message'])){
        $o .= '<div class="lm_hide_box">'.$atts['message'].'</div>';
      }else{
        $o .= '<div class="lm_hide_box">'.get_option('lm_partial_hide_box_content',__('Unlock the full content by becoming a member today!','lightmember')).'</div>';       
      }
		}

    return $o;
}

add_shortcode('lm_members_only', 'lm_hide_partial_content');





add_filter( 'the_content', 'lm_hide_full_content' );
function lm_hide_full_content( $content ) {
  global $post;

  $user = wp_get_current_user();

  if(get_post_meta($post->ID,'lm_members_only',true) == 1){
    if (( in_array( get_option('member_role'), (array) $user->roles ) ) OR (current_user_can('administrator') OR is_admin())) {
      return $content;
    }else{
      return '<div class="lm_hide_box_fullpage">'.get_option('lm_fullpage_hide_box_content',__('Unlock the full content by becoming a member today!','lightmember')).'</div>';


    }
  }else{
    return $content;
  }
}




add_action( 'wp_head', 'lm_change_role_of_expired_member' );

function lm_change_role_of_expired_member(){

  $user = wp_get_current_user();

  if(get_user_meta($user->ID, 'period_end', true) < time()){
    if (!current_user_can('administrator')) {
      wp_update_user(array('ID' => $user->ID, 'role' => get_option('free_role')));
    }
  }

}


function lm_disable_comments_selection(){
  global $post;

  $user = wp_get_current_user();

  if(get_post_meta($post->ID,'lm_comments_status',true) == 1){
    if (!current_user_can('administrator')) {
        if(!in_array( get_option('member_role'), (array) $user->roles )){
          add_filter('comments_open', '__return_false', 20, 2);
        }
        
    }
  }

}

add_action( 'wp_head', 'lm_disable_comments_selection' );



add_filter( 'login_url', 'lm_add_custom_login_url', PHP_INT_MAX );

function lm_add_custom_login_url( $login_url ) {
  
  if(get_option('lm_prevent_wp_login')==1){
    if(get_option('lm_member_page')){
      $post_slug = get_post_field( 'post_name', get_post(get_option('lm_member_page')) ); 
      $login_url = site_url( $post_slug, 'login' );	
    }
  }
	
    return $login_url;
}





?>