<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class DUPLICATE_WEB extends Core
{
	function __construct()
	{	
		//error_reporting(E_ALL);
		parent::__construct();
		$this->LoadModule("Duplicateweb");
		$this->contentDir = $this->Config['content']['dir'];
	}
	
	function Load($Load1="", $Load2="", $Load3="", $Load4="", $Load5="", $Load6="")
	{
		//Load1 = type data
		//Load2 = 
		//Load3 = 
		//Load4 = 
		//Load5 = 
		
		$readOpt = $this->Db->sql_query_array("SELECT id FROM cppagestatus WHERE vPageStatus='OPTIONAL'");
		$baca = $this->Db->sql_query("SELECT * FROM cppage WHERE cStatus='".$readOpt['id']."'");
		while ($Baca = $this->Db->sql_array($baca))
		{
			$OPTIONAL[$Baca['vPageName']] = $Baca;
		}
		$this->Template->assign("OPTIONAL", $OPTIONAL);

		$getUser = ($_COOKIE['duplic_user'])?$_COOKIE['duplic_user']:$_SESSION['duplic_user'];
		$detailDuplicate = $this->Module->Duplicateweb->detailDuplicateByLink($getUser);

		switch($Load1)
		{
			case "name":
				echo ($detailDuplicate['vName']=="")?$OPTIONAL['DEFAULT_NAME']['tContent']:$detailDuplicate['vName'];
			break;
			case "mobile":
				echo ($detailDuplicate['vMobile']=="")?$OPTIONAL['DEFAULT_MOBILE']['tContent']:$detailDuplicate['vMobile'];
			break;
			case "email":
				echo ($detailDuplicate['vEmail']=="")?$OPTIONAL['WEBEMAIL']['tContent']:$detailDuplicate['vEmail'];
			break;
			case "url":
				echo ($detailDuplicate['vURL']=="")?$OPTIONAL['DEFAULT_DOMAIN']['tContent']:$detailDuplicate['vURL'];
			break;
			case "title":
				echo ($detailDuplicate['vTitle']=="")?$OPTIONAL['TITLE']['tContent']:$detailDuplicate['vTitle'];
			break;
			case "desc":
				echo ($detailDuplicate['tDesc']=="")?$OPTIONAL['METADESC']['tContent']:$detailDuplicate['tDesc'];
			break;
			case "gambar":
				echo ($detailDuplicate['vGambar']=="")?$OPTIONAL['LOGO']['lbPicture']:$detailDuplicate['vGambar'];
			break;
			default:
				echo "";
			break;
		}

	}
		
}
?>