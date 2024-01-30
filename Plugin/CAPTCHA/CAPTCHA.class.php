<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class CAPTCHA extends Core
{
	var $publickey, $privatekey, $resp, $error;
	function __construct()
	{	
		parent::__construct();
		require_once('recaptchalib.php');
		// Get a key from https://www.google.com/recaptcha/admin/create
		$this->publickey = "6LeHF-sSAAAAADapNkTFpvHbyViXhsU7m6LQ8tBo";
		$this->privatekey = "6LeHF-sSAAAAALbSaN4TLIyS-n8N33KuqK1Me2l9";
		# the response from reCAPTCHA
		$this->resp = null;
		# the error code from reCAPTCHA, if any
		$this->error = null;
		//-----------------------
	}
		
	function Load($Load1="", $Load2="", $Load3="", $Load4="", $Load5="", $Load6="")
	{
	}
	
	function getHTML()
	{
		return recaptcha_get_html($this->publickey, $this->error);
	}
	
	function getResponse()
	{
		//$_POST["recaptcha_response_field"]
		if ($_POST["recaptcha_response_field"]) {
			$resp = recaptcha_check_answer ($this->privatekey,
						$_SERVER["REMOTE_ADDR"],
						$_POST["recaptcha_challenge_field"],
						$_POST["recaptcha_response_field"]
				);
		}
		return $resp->is_valid;
	}
	
}
?>