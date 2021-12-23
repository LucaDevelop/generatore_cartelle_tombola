<?php
include 'config.inc.php';
include 'makecart.php';

$captcha_response = (isset($_POST['captcha_response'])?$_POST['captcha_response']:'');
$cart_number = (isset($_POST['cart_number'])?$_POST['cart_number']:0);

if($captcha_response != '')
{
	$url = "https://www.google.com/recaptcha/api/siteverify";
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_URL,            $url );
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt($ch, CURLOPT_POST,           1 );
	curl_setopt($ch, CURLOPT_POSTFIELDS,     http_build_query(['secret' => RECAPTCHA_CLIENT_SECRET, 'response' => $captcha_response])); 
	$result=curl_exec ($ch);
	
	$resobj = json_decode($result, true);
	if($resobj != null){
		if($resobj['success']){
			if($cart_number >= 1 && $cart_number <= 10){
				for($i=0; $i<$cart_number; $i++){
					
					echo '<img src="data:image/png;base64,'.MakeCart().'" />';
				}
			}
		}
	}
	else{
	}
}
?>