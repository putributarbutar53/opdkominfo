<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class WHATSAPP extends Core
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
		
		$detailPage = $this->Module->Page->detailPageByPermalink(strtolower(trim($Load1)));
		$ModuleSetting = $this->Module->Options->getContentSetting($detailPage['id'], "page");

		$mobilephone = ($ModuleSetting['mobile'])?$ModuleSetting['mobile']:'62811111111111';
		$title = ($ModuleSetting['title'])?$ModuleSetting['title']:'web.id';
		$position = ($ModuleSetting['position'])?$ModuleSetting['position']:'left';
		$message = ($detailPage['tContent'])?$detailPage['tContent']:'Halo, saya ingin bertanya mengenai produk anda';
		$headercolor = ($ModuleSetting['headercolor'])?$ModuleSetting['headercolor']:'green';
		$headertitle = ($ModuleSetting['headertitle'])?$ModuleSetting['headertitle']:'CS Perusahaan';

		$PageDetail = "	phone: '".$mobilephone."',\n
							popupMessage: '".$title."',\n
							position: '".$position."',\n
							message: '".$message."',\n
							headerColor: '".$headercolor."',\n
							headerTitle: '".$headertitle."',\n
							";

		//if ($detailPage['id']=="")
		//{
		//	$PageDetail = "	phone: '62811111111111',\n
		//					popupMessage: 'web.id',\n
		//					position: 'left',\n
		//					message: 'Halo, saya ingin bertanya mengenai produk anda',\n
		//					headerColor: 'green',\n
		//					headerTitle: 'CS Perusahaan',\n
		//					";
		//}
		//else
		//	$PageDetail = preg_replace("#`#","'",$detailPage['tContent']);
		
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