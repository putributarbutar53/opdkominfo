<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class LIST_PAGEALL extends Core
{	
	var $bannerFolder, $ContentModule;

	function __construct()
	{	
		//error_reporting(E_ALL);
		parent::__construct();
		$this->LoadModule("Page");
		$this->LoadModule("Options");
		
		$this->Template->assign("pageDir", $this->Config['page']['dir']);
		$this->ContentModule = "page";
	}
	
	function Load($Load1="", $Load2="", $Load3="", $Load4="", $Load5="", $Load6="")
	{
		//#Load1 = Category Page
		//#Load2 = Limit Menu
		//#Load3 = Format File
		//#Load4 = Selected
		
		$listPage = $this->Module->Page->listSlidePage($Load1);
		for ($i=0;$i<count($listPage);$i++)
		{
			$Selected = ($listPage[$i]['Item']['vPermalink']==$this->getPermalink())?$Load4:"";
			$URL = ($listPage[$i]['Item']['vURL']=="")?$this->getURL()."pages/".$listPage[$i]['Item']['vPermalink'].".html":$listPage[$i]['Item']['vURL'];
			//$URL = ($listPage[$i]['Item']['vURL']=="")?$this->getURL()."?do=page&page=".$listPage[$i]['Item']['vPermalink'].".html":$listPage[$i]['Item']['vURL'];
			$Sub = "no";
			$checkTop = $listPage[$i]['totalTopMenu'];
			
			if ($checkTop>0)
			{
				$Sub = "yes";
				$listSub = $this->listSubPage($listPage[$i]['Item']['id']);
			}
			
			$Data[$i] = array(	
				'No' => ($i+1), 
				'Item' => $listPage[$i]['Item'], 
				'URL' => $URL, 
				'Sub' => $Sub, 
				'listSub' => $listSub,
				'Detail_Sub' => $this->Module->Options->getContentSetting($listPage[$i]['Item']['id'], $this->ContentModule)
			);
		}

		$this->Template->assign("list", $Data);
		$fileFormat = ($Load3=="")?"menu_format.html":$Load3;
		echo $this->Template->ShowTemplatePlugin($fileFormat);
	}
	
	function listSubPage($iTopMenu)
	{
		$listSub = $this->Module->Page->list_SubPage($iTopMenu);
		for ($i=0;$i<count($listSub);$i++)
		{
			$Selected = ($listSub[$i]['Item']['vPermalink']==$this->getPermalink())?$Load4:"";
			$URL = ($listSub[$i]['Item']['vURL']=="")?$this->getURL()."pages/".$listSub[$i]['Item']['vPermalink'].".html":$listSub[$i]['Item']['vURL'];
			$Sub = "no";
			$checkTopChild = $listSub[$i]['totalTopMenu'];
			if ($checkTopChild>0)
			{
				$Sub = "yes";
				$listSubChild = $this->listSubPage($listSub[$i]['Item']['id']);
			}

			$Data[$i] = array(	
				'No' => ($i+1), 
				'Item' => $listSub[$i]['Item'], 
				'URL' => $URL,
				'Sub' => $Sub, 
				'listSub' => $listSubChild,
				'Detail_Sub' => $this->Module->Options->getContentSetting($listSub[$i]['Item']['id'], $this->ContentModule)
			);
		}
		return $Data;
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