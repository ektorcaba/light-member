<?php

require_once '../../../wp-load.php';



require_once 'includes/stripe-lib/vendor/autoload.php';


$stripeSecretKey = get_option('stripe_secret_key');
$endpoint_secret = get_option('stripe_endpoint_secret');
$live_mode = get_option('livemode');
$member_role = get_option('member_role');
$free_role = get_option('free_role');
$enable_log = get_option('enable_log');


\Stripe\Stripe::setApiKey($stripeSecretKey);


if(intval($live_mode) == 1){
	$livemode = true;
}else{
	$livemode = false;
}

$payload = @file_get_contents('php://input');
$event = null;

try {
  $event = \Stripe\Event::constructFrom(
    json_decode($payload, true)
  );
} catch(\UnexpectedValueException $e) {
  // Invalid payload
  echo '⚠️  Webhook error while parsing basic request.';
  http_response_code(400);
  exit();
}
if ($endpoint_secret) {
  // Only verify the event if there is an endpoint secret defined
  // Otherwise use the basic decoded event
  $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
  try {
    $event = \Stripe\Webhook::constructEvent(
      $payload, $sig_header, $endpoint_secret
    );
  } catch(\Stripe\Exception\SignatureVerificationException $e) {
    // Invalid signature
    echo '⚠️  Webhook error while validating signature.';
    http_response_code(400);
    exit();
  }
}






function lm_invoice_payment($event){
	global $livemode, $member_role, $enable_log, $stripeSecretKey;
	
	$stripe = new \Stripe\StripeClient($stripeSecretKey);
	
	$invoice = $event->data->object;

		if(($invoice->paid == 1) AND ($invoice->status == "paid") AND ($invoice->livemode == $livemode)){

			//TODO: Si no existe el pago en la DB -> Insertar pago en BD y Convertir usuario en miembro
			$data[$invoice->id]['event_id'] = $event->id;
			$data[$invoice->id]['invoice'] = $invoice->id;
			$data[$invoice->id]['number'] = $invoice->number;
			$data[$invoice->id]['subscription'] = $invoice->subscription;
			$data[$invoice->id]['customer'] = $invoice->customer;
			$data[$invoice->id]['created'] = $invoice->created;
			$data[$invoice->id]['period_start'] = $invoice->lines->data[0]->period->start;
			$data[$invoice->id]['period_end'] = $invoice->lines->data[0]->period->end;
			$data[$invoice->id]['hosted_invoice_url'] = $invoice->hosted_invoice_url;
			$data[$invoice->id]['invoice_pdf'] = $invoice->invoice_pdf;
			
			if ( !email_exists( $invoice->customer_email ) ) {
				$random_password = wp_generate_password( $length = 12, $include_standard_special_chars = false );
				$u = wp_create_user( lm_get_random_unique_username_wh((empty(get_option('lm_username_prefix')) ? 'u':get_option('lm_username_prefix'))), $random_password, $invoice->customer_email);
				
				if(!is_wp_error($u)){
					//Enviar email de password al usuario
					add_filter( 'wp_mail_from', function ( $original_email_address ) {
						return get_option('email_from');
					} );

					add_filter( 'wp_mail_from_name', function ( $original_email_from ) {
						return get_option('email_from_name');
					});

					add_filter( 'retrieve_password_message', 'lm_custom_retrieve_password_message_wh', 10, 4);
					add_filter('retrieve_password_title', 'lm_custom_retrieve_password_title_wh', 10 ,3);

					retrieve_password($invoice->customer_email);

					//fin email password
				}

			
				
				
			}else{
				$user = get_user_by( 'email', $invoice->customer_email);
				$u = $user->ID;
			}
			
			$meta = get_user_meta($u, 'pagos', false);
			
			
			if(!lm_findKey_wh($meta, $invoice->id)){
				if($u == (wp_update_user(array('ID' => $u, 'role' => $member_role)))){
					add_user_meta($u, 'pagos', $data);
					delete_user_meta($u, 'period_end');
					add_user_meta($u, 'period_end', $data[$invoice->id]['period_end']);
					add_user_meta($u, 'stripe_customer', $data[$invoice->id]['customer']);

					//enviar email
					add_filter( 'wp_mail_from', function ( $original_email_address ) {
						return get_option('email_from');
					} );

					add_filter( 'wp_mail_from_name', function ( $original_email_from ) {
						return get_option('email_from_name');
					});

					$subject_sub = get_option('lm_subscription_email_subject');
					
					$patrones_sub = array('{{sitename}}','{{siteurl}}','{{username}}');
					$user_sub = get_userdata($u);
					$valores_sub = array(get_bloginfo('name'), home_url(), rawurlencode($user_sub->user_login));
				
					$message_sub = str_replace($patrones_sub, $valores_sub, get_option('lm_subscription_email_message'));

					wp_mail( $invoice->customer_email, $subject_sub, $message_sub);
					//fin enviar mail
				
				}

			}
			
			$data[$invoice->id]['wpuserid'] = $u;
			$data[$invoice->id]['customer_email'] = $invoice->customer_email;
			$data[$invoice->id]['full_event_data'] = $event;


			
			if($enable_log){
				file_put_contents('logs/'.$event->type.'.log', print_r($data, true));
			}
		}		
}







function lm_customer_subscription_updated($event){
	global $livemode, $enable_log, $stripeSecretKey, $free_role;
	
	$stripe = new \Stripe\StripeClient($stripeSecretKey);

    $subscription = $event->data->object;
	$user = get_user_by('email', $stripe->customers->retrieve($subscription->customer, [])->email);
	
	$data['subscription'] = $subscription->id;
	$data['customer'] = $subscription->customer;
	$data['event_id'] = $event->id;
	$data['full_event_data'] = $event;
	
		if(($subscription->status != "active") AND ($invoice->livemode == $livemode)){		
			
			if( $user->ID == (wp_update_user(array('ID' => $user->ID, 'role' => $free_role))) ){
				delete_user_meta($user->ID, 'period_end');
				add_user_meta($user->ID, 'period_end', time());

				if($enable_log){
					file_put_contents('logs/'.$event->type.'.log', print_r($data, true));
				}
			}
			
		}elseif(($subscription->status == "active") AND ($invoice->livemode == $livemode)){
			delete_user_meta($user->ID, 'period_end');
			add_user_meta($user->ID, 'period_end', $subscription->current_period_end);
			
			if($enable_log){
				file_put_contents('logs/'.$event->type.'_active.log', print_r($data, true));
			}
			
		}
}



function lm_customer_subscription_deleted($event){
	global $livemode, $enable_log, $stripeSecretKey, $free_role;
	
	$stripe = new \Stripe\StripeClient($stripeSecretKey);

    $subscription = $event->data->object;
		if(($subscription->status == "canceled") AND ($invoice->livemode == $livemode)){
			
			//TODO: poner cuenta de usuario en usuario normal
			$data['subscription'] = $subscription->id;
			$data['customer'] = $subscription->customer;
			$data['event_id'] = $event->id;
			$data['full_event_data'] = $event;

			$user = get_user_by('email', $stripe->customers->retrieve($subscription->customer, [])->email);
			
			if( $user->ID == (wp_update_user(array('ID' => $user->ID, 'role' => $free_role))) ){
				delete_user_meta($user->ID, 'period_end');
				add_user_meta($user->ID, 'period_end', time());
				if($enable_log){
					file_put_contents('logs/'.$event->type.'.log', print_r($data, true));
				}
			}
		}	
}



function lm_custom_retrieve_password_title_wh($message, $user_login, $user_data ) {

	$patrones = array('{{sitename}}','{{username}}');
	$valores = array(get_bloginfo('name'), rawurlencode($user_login));

	$subject = str_replace($patrones, $valores, get_option('email_pwd_link_subject'));

    return $subject;
}



function lm_custom_retrieve_password_message_wh($message, $key, $user_login, $user_data ){
    // Setting up message for retrieve password

	$patrones = array('{{sitename}}','{{username}}', '{{password_link}}');
	$valores = array(get_bloginfo('name'), rawurlencode($user_login), get_permalink(get_option('lm_member_page'))."?recover&key=$key&login=" . rawurlencode($user_login));

	$message = str_replace($patrones, $valores, get_option('email_pwd_link_message'));

    return $message;

}







function lm_get_random_unique_username_wh( $prefix = '' ){
    $user_exists = 1;
    do {
       $rnd_str = sprintf("%06d", mt_rand(1, 999999));
       $user_exists = username_exists( $prefix . $rnd_str );
   } while( $user_exists > 0 );
   return $prefix . $rnd_str;
}


function lm_findKey_wh($array, $keySearch)
{
    foreach ($array as $key => $item) {
        if ($key == $keySearch) {
            //echo 'yes, it exists';
            return true;
        } elseif (is_array($item) && lm_findKey_wh($item, $keySearch)) {
            return true;
        }
    }
    return false;
}




// Handle the event
switch ($event->type) {	
  case 'customer.subscription.deleted':
	lm_customer_subscription_deleted($event);
  case 'customer.subscription.updated':
	lm_customer_subscription_updated($event);
  case 'invoice.payment_succeeded':
	lm_invoice_payment($event);
  default:
    echo '';
}



http_response_code(200);