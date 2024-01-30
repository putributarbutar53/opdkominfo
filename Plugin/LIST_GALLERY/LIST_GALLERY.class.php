<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class LIST_GALLERY extends Core
{
	var $photoDir;

	function __construct()
	{	
		parent::__construct();
		$this->LoadModule("Photo");
		$this->photoDir = $this->Config['upload']['photodir'];
		$this->Template->assign("photoDir", $this->photoDir);
	}
	
	function Load($Load1="", $Load2="", $Load3="", $Load4="", $Load5="", $Load6="")
	{
		//Load1 = Album Permalink
		//Load2 = Max Photo
		//Load3 = File Format
		//Load4 = Ukuran File - width,height (75,75)

		$fileFormat = $Load3;
		$Ukuran = ($Load5=="")?array(0,0):explode(",",$Load4);
		$listPhoto = $this->Module->Photo->listByAlbum($Load1, $Load2);
		
		$Data = array();
		for ($i=0;$i<count($listPhoto);$i++)
		{
			$URL = $this->getURL()."gallery/".$listPhoto[$i]['Item']['vPermalink'].".html";
			$GAMBAR = "<img alt=\"".$listPhoto[$i]['Item']['vPhotoTitle']."\" src=\"".$this->getURL().$this->photoDir.$listPhoto[$i]['Item']['vPhotoName']."\" width=\"".$Ukuran[0]."\" height=\"".$Ukuran[1]."\" border=\"0\" />";
			
			$Data[$i] = array(	
				'No' => ($i+1), 
				'Item' => $listPhoto[$i]['Item'], 
				'no_html' => $this->textCut(strip_tags($listPhoto[$i]['Item']['mtDesc']), 150), 
				'URL' => $URL,
				'Gambar' => $GAMBAR, 
				'LinkGambar' => $this->getURL().$this->photoDir.$listPhoto[$i]['Item']['vPhotoName']
			);
		}

		$this->Template->assign("list", $Data);
		$fileFormat_ = ($fileFormat=="")?"read_format.html":$fileFormat;
		
		echo $this->Template->ShowTemplatePlugin($fileFormat_);

	}

	function textCut($text, $limit)
	{
		if (strlen($text)<=$limit)
			return $text;
		else
			return substr($text,0,$limit)."...";
	}
		
}
?>