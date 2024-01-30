<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class BUTTON extends Core
{
	function __construct()
	{	
		parent::__construct();
		$this->LoadModule("Page");
		$this->LoadModule("Options");
	}
	
	function Load($Load1="", $Load2="", $Load3="", $Load4="", $Load5="", $Load6="")
	{
		//Load1 = Link
		//Load2 = Text
		//Load3 = 
		//Load4 = 
		
		echo "<button class=\"active\" data-filter=\"*\" onclick=\"location.href='".$Load1."';\">".$Load2."</button>";
	}
	
}
?>