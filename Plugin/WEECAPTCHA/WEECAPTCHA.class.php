<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class WEECAPTCHA extends Core
{
	function __construct()
	{	
		parent::__construct();
	}
	
	function Load($Load1="", $Load2="", $Load3="", $Load4="", $Load5="", $Load6="")
	{
	} 

	function Show()
	{
		return "<img src=\"".preg_replace("#index.php/#","",$this->getURL())."Plugin/WEECAPTCHA/securimage_show.php?sid=".md5(uniqid(time()))."\">";
	}
	
	function authCapt($vCode)
	{
	  include("securimage.php");
	  $img = new Securimage();
	  return $img->check($vCode);
	}
	
}
?>