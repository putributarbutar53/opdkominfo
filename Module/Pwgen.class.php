<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class Pwgen extends Core
{
	 var $passwdchars;
	 var $passwd = NULL;
	 var $length;

	public function __construct()
	{
		parent::__construct();
	}
	
	function CreateRandom($min=6, $max=8, $special=NULL, $chararray=NULL) {
		if ($chararray == NULL) {
		   $this->passwdstr = "abcdefghijklmnopqrstuvwxyz";
		   $this->passwdstr .= strtoupper($this->passwdstr);
		   $this->passwdstr .= "1234567890";
	
		   // add special chars to start
		   if ($special) {
			  $this->passwdstr .= "!@#$%";
		   }
		} else {
			$this->passwdstr = $chararray;
		}
	
		for($i=0; $i<strlen($this->passwdstr); $i++) {
			$this->passwdchars[$i]=$this->passwdstr[$i];
		}
				 
		// randomize the chars
		srand ((float)microtime()*1000000);
		shuffle($this->passwdchars);
	
		$this->length = rand($min, $max);
	
		for($i=0; $i<$this->length; $i++) {
		   $charnum = rand(1, count($this->passwdchars));
		   $this->passwd .= $this->passwdchars[$charnum-1];
		}    
	}

	function getPasswd() {
		return $this->passwd;
	}
 
	function getPasswdImg() {
	// create the image
		$png = ImageCreate(200,80);
		$bg = ImageColorAllocate($png,192,192,192);
		$tx = ImageColorAllocate($png,128,128,128);
		ImageFilledRectangle($png,0,0,200,80,$bg);
		srand ((float)microtime()*1000000);
		ImageString($png,5,rand(0,90),rand(0,50),$this->passwd,$tx);
	
		// send the image
		header("content-type: image/png");
		ImagePng($png);
		Imagedestroy($png);
	}

	function getPasswdHtml() {
		return htmlentities($this->passwd);
	}
}
?>
