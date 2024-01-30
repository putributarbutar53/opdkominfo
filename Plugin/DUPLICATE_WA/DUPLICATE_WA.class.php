<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class DUPLICATE_WA extends Core
{
	function __construct()
	{	
		parent::__construct();
		$this->LoadModule("Page");
		$this->LoadModule("Duplicateweb");
		$this->contentDir = $this->Config['content']['dir'];
	}
	
	function Load($Load1="", $Load2="", $Load3="", $Load4="", $Load5="", $Load6="")
	{
		//Load1 = Load Data
		//Load2 = 
		//Load3 = 
		//Load4 = 
		
		$readOpt = $this->Db->sql_query_array("SELECT id FROM cppagestatus WHERE vPageStatus='OPTIONAL'");
		$baca = $this->Db->sql_query("SELECT * FROM cppage WHERE cStatus='".$readOpt['id']."'");
		while ($Baca = $this->Db->sql_array($baca))
		{
			$OPTIONAL[$Baca['vPageName']] = $Baca;
		}
		$this->Template->assign("OPTIONAL", $OPTIONAL);

		$getUser = ($_COOKIE['duplic_user'])?$_COOKIE['duplic_user']:$_SESSION['duplic_user'];
		$detailDuplicate = $this->Module->Duplicateweb->detailDuplicateByLink($getUser);

		$name = ($detailDuplicate['vName']=="")?$OPTIONAL['DEFAULT_NAME']['tContent']:$detailDuplicate['vName'];
		$mobile = ($detailDuplicate['vMobile']=="")?$OPTIONAL['DEFAULT_MOBILE']['tContent']:$detailDuplicate['vMobile'];
		$mobile = preg_replace('/0/', '62', $mobile, 1);

		$PageDetail = "	phone: '".$mobile."',\n
		popupMessage: '".$OPTIONAL['DEFAULT_DOMAIN']['tContent']."',\n
		position: 'left',\n
		message: 'Halo, saya ingin bertanya mengenai ".$OPTIONAL['DEFAULT_DOMAIN']['tContent']."',\n
		headerColor: 'green',\n
		headerTitle: 'Chat Dengan ".$name."',\n
		";
		
		$Temp = "<link rel=\"stylesheet\" href=\"".$this->Config['base']['url']."plugin/WHATSAPP/floating-wpp.min.css\" type=\"text/css\" media=\"all\" />\n";
		$Temp .= "<script src=\"".$this->Config['base']['url']."plugin/WHATSAPP/floating-wpp.min.js\"></script>\n";
		$Temp .= "<script type=\"text/javascript\">\n";
		$Temp .= "$(function () {\n";
		$Temp .= "$('.floating-wpp').floatingWhatsApp({\n";
		$Temp .= "showPopup: true,\n";
		$Temp .= "autoOpen: false,\n";
		$Temp .= "//autoOpenTimer: 4000,\n";
		$Temp .= $PageDetail."\n";
		$Temp .= "});\n";
		$Temp .= "});\n";
		$Temp .= "</script>\n";
		$Temp .= "<div class=\"floating-wpp\" style=\"z-index:10000;\"></div>\n";
		echo $Temp;
	}
	
}
?>