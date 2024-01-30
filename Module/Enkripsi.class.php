<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class Enkripsi extends Core
{
	var $Key;
	public function __construct()
	{	
		parent::__construct();
		$this->Key = "YjNgB4nN3rKL1K";
	}
	
	function encrypt($string, $key_=NULL) { 
		$key = ($key_==NULL)?$this->Key:$key_;
		$result = ''; 
		for($i=0; $i<strlen($string); $i++) { 
			$char = substr($string, $i, 1); 
			$keychar = substr($key, ($i % strlen($key))-1, 1); 
			$char = chr(ord($char)+ord($keychar)); 
			$result.=$char; 
		}
		return rtrim(strtr(base64_encode($result), '+/', '-_'), '=');
	} 
	
	function decrypt($string, $key_=NULL) { 
		$key = ($key_==NULL)?$this->Key:$key_;
		$result = ''; 
		$string = base64_decode(str_pad(strtr($string, '-_', '+/'), strlen($string) % 4, '=', STR_PAD_RIGHT));
		
		for($i=0; $i<strlen($string); $i++) { 
			$char = substr($string, $i, 1); 
			$keychar = substr($key, ($i % strlen($key))-1, 1); 
			$char = chr(ord($char)-ord($keychar)); 
			$result.=$char; 
		}
		
		return $result; 
	} 
}
?>