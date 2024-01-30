<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class MOBILECHECK extends Core
{	
	var $gobalPermalink, $vUsername, $vURL;
	
	function __construct()
	{	
		parent::__construct();
		$this->vUsername = _USERNAME;
		$this->vURL = _URL;
	}
	
	function Load($Load1="", $Load2="", $Load3="", $Load4="", $Load5="", $Load6="")
	{
		//Load1 = nama browser,nama browser
		//Load2 = file execute
		//Load3 = file default
		//Load4 = ''

		$Temp = $Load3;
		$Browser = explode(",",strtolower($Load1));
		for ($i=0;$i<count($Browser);$i++)
		{
			if (eregi($Browser[$i],$_SERVER['HTTP_USER_AGENT']))
			{
				$Temp = $Load2;
				break;
			}
		}

		echo ($Temp=="")?"":$this->Template->ShowTemplatePlugin($Temp);
	}	
}
?>