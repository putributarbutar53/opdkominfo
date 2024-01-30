<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class SNAPSHOT extends Core
{
	var $snapDir;

	function __construct()
	{	
		//error_reporting(E_ALL);
		parent::__construct();
		$this->LoadModule("FileManager");
		$this->snapDir = $this->Config['main']['themes']."snapshot/";
	}
	
	function Load($Load1="", $Load2="", $Load3="", $Load4="", $Load5="", $Load6="")
	{
		//Load1 = Filename
		//Load2 = 
		//Load3 = 
		//Load4 = 
		//Load5 = 
		
		if ($Load1=="")
			echo "";
		else
		{
			$fileName = preg_replace("# #","_",strtolower($Load1)).".jpg";
			$fileCheck = $this->Module->FileManager->checkSnapshot($this->snapDir.$fileName);
			if ($fileCheck=="")
				echo "";
			else
			{
				$imgUrl = $this->Config['main']['url'].$this->snapDir.$fileName;
				$thumbnail = $this->Config['main']['url'].$this->snapDir."ask.png";
				echo "&nbsp;&nbsp;<a class=\"image-popup-no-margins\" href=\"".$imgUrl."\"><img src=\"".$thumbnail."\"></a>";
			}
		}
	}
		
}
?>