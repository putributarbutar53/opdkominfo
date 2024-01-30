<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class PARSER extends Core
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
	
	function youtube($Text)
	{
		/* $string = '[YOUTUBE=http://www.youtube.com/watch?v=OsV5RQtmBVw w=300 H=400]'; */
		$pattern = '%\[YOUTUBE=.*/watch\?v=(.*)\s+w=(\d+)\s+h=(\d+)\]%is';
		$replacement = '<iframe width=$2 height=$3 src=//www.youtube.com/embed/$1 frameborder="0" allowfullscreen></iframe>';
		return preg_replace($pattern, $replacement, $Text);
	}
	
}
?>