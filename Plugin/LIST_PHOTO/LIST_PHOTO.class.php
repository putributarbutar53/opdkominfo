<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class LIST_PHOTO extends Core
{	
	var $photoDir;

	function __construct()
	{	
		//error_reporting(E_ALL);
		parent::__construct();
		$this->LoadModule("Photo");
		$this->photoDir = $this->Config['upload']['photodir'];
		$this->Template->assign("photoDir", $this->photoDir);
	}
	
	function Load($Load1="", $Load2="", $Load3="", $Load4="", $Load5="", $Load6="")
	{
		//#Load1 = Category Photo
		//#Load2 = Album Photo
		//#Load3 = Format HTML
		//#Load4 = Max Photo
		//#Load5 = Ukuran File - width,height (75,75)

		$linkFormat = $Load3;
		$Ukuran = ($Load5=="")?array(0,0):explode(",",$Load5);
		$listPhoto = $this->Module->Photo->list_LastPhoto($Load1, $Load2, "", $Load4);
		
		$Temp = "";
		for ($i=0;$i<count($listPhoto);$i++)
		{
			$URL = $this->getURL()."gallery/".$listPhoto[$i]['Item']['vPermalink'].".html";
			$GAMBAR = "<img alt=\"".$listPhoto[$i]['Item']['vPhotoTitle']."\" src=\"".$this->getURL().$this->photoDir.$listPhoto[$i]['Item']['vPhotoName']."\" width=\"".$Ukuran[0]."\" height=\"".$Ukuran[1]."\" border=\"0\" />";
			$Change = array(	'#URL' => $URL, 
								'#TITLE' => $listPhoto[$i]['Item']['vPhotoTitle'], 
								'#GAMBAR' => $GAMBAR,
								'#PICTUREURL' => $this->getURL().$this->photoDir.$listPhoto[$i]['Item']['vPhotoName'], 
								'#PERMALINK' => $listPhoto[$i]['Item']['vPermalink'], 
								'#ID' => $listPhoto[$i]['Item']['id']
							);
			$Temp .= strtr($linkFormat,$Change);
		}
		
		echo $Temp;
	}

}
?>