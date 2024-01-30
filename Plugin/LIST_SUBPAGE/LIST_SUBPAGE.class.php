<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class LIST_SUBPAGE extends Core
{
	function __construct()
	{	
		parent::__construct();
		$this->LoadModule("Page");
	}
	
	function Load($Load1="", $Load2="", $Load3="", $Load4="", $Load5="", $Load6="")
	{
		//Load1 = Sub Page
		//Load2 = Limit Menu
		//Load3 = Format Link / #URL / #TITLE / #SELECTED / #NO
		//Load4 = Selected
		
		$linkFormat = $Load3;
		$getID = $this->Module->Page->getIdPage($Load1);
		$theLoad = ($getID==0)?$Load1:$getID;
		$listPage = $this->Module->Page->list_SubPage($theLoad, $Load2);
		
		for ($i=0;$i<count($listPage);$i++)
		{
			$Selected = ($listPage[$i]['vPermalink']==$this->getPermalink())?$Load4:"";
			$URL = ($listPage[$i]['vURL']=="")?$this->getURL()."pages/".$listPage[$i]['vPermalink'].".html":$listPage[$i]['vURL'];
			$Change = array('#URL' => $URL, '#TITLE' => $listPage[$i]['vPageName'], '#SELECTED' => $Selected, '#NO' => ($i+1)	);
			$Temp .= strtr($linkFormat,$Change);
		}

		echo $Temp;
	}

	function getPermalink()
	{
		$Permalink_ = $_SERVER[REQUEST_URI];
		$Permalink1 = explode("/",$Permalink_);
		$Permalink2 = explode(".html",$Permalink1[(count($Permalink1)-1)]);	
		return $Permalink2[0];
	}

}
?>