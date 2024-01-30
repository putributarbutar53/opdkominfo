<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class MAINMENU extends Core
{	
	var $gobalPermalink;
	
	function __construct()
	{	
		parent::__construct();
		$this->LoadModule("Page");
	}
	
	function Load($Load1="", $Load2="", $Load3="", $Load4="", $Load5="", $Load6="")
	{
		//Load1 = Category Page (Permalink)
		//Load2 = Limit Menu
		//Load3 = Format List / #URL / #TITLE / #TARGET
		//Load4 = Selected
		
		$linkFormat = $Load3;
		$listAwal = $this->Module->Page->listSlidePage($Load1, "0");		
		$Temp = "";
		$countAwal = count($listAwal);
		
		$actual_link = $_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI]; //"http://".
		//echo $actual_link;
		$link_ = explode("/",$actual_link);
		$thePermalink_ = explode(".",$link_[(count($link_)-1)]);
		$thePermalink = $thePermalink_[0];
		$this->gobalPermalink = $thePermalink;
		
		for ($i=0;$i<$countAwal;$i++)
		{
			$URL = ($listAwal[$i]['Item']['vURL']!="")?$listAwal[$i]['Item']['vURL']:$this->Config['base']['url'].$this->Config['index']['page']."pages/".$listAwal[$i]['Item']['vPermalink'].".html";
			$linkAktif = ($this->checkActiveLink($thePermalink, $listAwal[$i]['Item']['vPermalink']))?$Load4:"";
			$Change = array('#URL' => $URL, '#TITLE' => $listAwal[$i]['Item']['vPageName'], '#SELECTED' => $linkAktif, '#NO' => ($i+1)	);
			$Temp .= strtr($linkFormat,$Change);
		}
		
		echo $Temp;
	}
		
	function checkActiveLink($permalink_1, $permalink_2)
	{
		if ($permalink_1==$permalink_2)
			return TRUE;
		else
		{
			$detailPage = $this->Module->Page->detailPageByPermalink($permalink_1);
			if ($detailPage['iTopMenu']!=0)
			{
				$detailPage_ = $this->Module->Page->detailPage($detailPage['iTopMenu']);
				if (($permalink_2==$detailPage_['vPermalink']) OR ($permalink_2==$detailPage_['vURL']))
					return TRUE;
				else
					return FALSE;
			}
			else
				return FALSE;
		}
	}	
}
?>