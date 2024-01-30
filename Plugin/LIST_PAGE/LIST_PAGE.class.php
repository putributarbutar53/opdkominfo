<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class LIST_PAGE extends Core
{
	function __construct()
	{	
		parent::__construct();
		$this->LoadModule("Page");
	}
	
	function Load($Load1="", $Load2="", $Load3="", $Load4="", $Load5="", $Load6="")
	{
		//Load1 = Category Page
		//Load2 = Limit Menu
		//Load3 = Format Link / #URL / #TITLE / #SELECTED / #NO
		//Load4 = Selected
		
		$linkFormat = $Load3;
		$listPage = $this->Module->Page->listPages($Load1, $Load2);
		
		for ($i=0;$i<count($listPage);$i++)
		{
			$Picture = "";
			if ($listPage[$i]['lbPicture']!="")
				$Picture = $this->Config['base']['url'].$this->Config['page']['dir'].$listPage[$i]['lbPicture'];
			
			$Selected = ($listPage[$i]['vPermalink']==$this->getPermalink())?$Load4:"";
			$URL = ($listPage[$i]['vURL']=="")?$this->getURL()."pages/".$listPage[$i]['vPermalink'].".html":$listPage[$i]['vURL'];
			//$URL = ($listPage[$i]['vURL']=="")?$this->getURL()."?do=page&page=".$listPage[$i]['vPermalink'].".html":$listPage[$i]['vURL'];
			$Change = array(	'#URL' => $URL, 
								'#TITLE' => $listPage[$i]['vPageName'], 
								'#SELECTED' => $Selected, 
								'#NO' => ($i+1), 
								'#PICTURE' => $Picture, 
								'#TARGET' => $listPage[$i]['cURLTarget']
							);
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