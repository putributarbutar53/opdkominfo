<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class BOLD extends Core
{
	function __construct()
	{	
		parent::__construct();
		$this->LoadModule("Page");
		$this->LoadModule("Options");
	}
	
	function Load($Load1="", $Load2="", $Load3="", $Load4="", $Load5="", $Load6="")
	{
		//Load1 = Load Data
		//Load2 = 
		//Load3 = 
		//Load4 = 
		
		echo "<strong>".$Load1."</strong>";
	}
	
}
?>