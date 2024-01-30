<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class GET_BANNER extends Core
{
	function __construct()
	{	
		//error_reporting(E_ALL);
		parent::__construct();
		$this->LoadModule("Banner");
		$this->bannerFolder = $this->Config['upload']['bannerdir'];
	}
	
	function Load($Load1="", $Load2="", $Load3="", $Load4="", $Load5="", $Load6="")
	{
		//Load1 = Cat Banner
		//Load2 = Nama Banner
		//Load3 = Format List
		//Load4 = Banner Size
		//Load5 = Default Banner

		$linkFormat = $Load3;
		$PicSize = explode(",",$Load4);
		$defaultBanner = ($Load5=="")?"images/no_banner.gif":$Load5;

		$getIDBanner = $this->Module->Banner->getIdBanner($Load1);
		if ($getIDBanner=="")
		{
			$GAMBAR = "<img src=\"".$defaultBanner."\" width=\"".$PicSize[0]."\" height=\"".$PicSize[1]."\" border=\"0\" />";
			$Change = array(	'#NO' => '1', '#PICTURE' => $GAMBAR, '#URL' => $this->getURL()	);
			$Temp .= strtr($linkFormat,$Change);
		}
		else
		{
			$detailBanner = $this->Module->Banner->getBanner($Load2, $getIDBanner);
			if ($detailBanner['id']=="")
			{
				$GAMBAR = "<img src=\"".$defaultBanner."\" width=\"".$PicSize[0]."\" height=\"".$PicSize[1]."\" border=\"0\" />";
				$Change = array(	'#NO' => '1', '#PICTURE' => $GAMBAR, '#URL' => $this->getURL()	);
				$Temp .= strtr($linkFormat,$Change);				
			}
			else
			{
				if ($detailBanner['vBannerFile']=="")
					$GAMBAR = "<img alt=\"".$detailBanner['vBannerName']."\" title=\"".$detailBanner['vBannerName']."\" src=\"".$defaultBanner."\" width=\"".$PicSize[0]."\" height=\"".$PicSize[1]."\" border=\"0\" />";
				else
					$GAMBAR = "<img alt=\"".$detailBanner['vBannerName']."\" title=\"".$detailBanner['vBannerName']."\" src=\"images/thumb.php?src=".$this->Template->relativePath().$this->bannerFolder.$detailBanner['vBannerFile']."&w=".$PicSize[0]."&h=".$PicSize[1]."&zc=1\" width=\"".$PicSize[0]."\" height=\"".$PicSize[1]."\" border=\"0\" />";
	
				$Change = array(	'#NO' => '1', '#URL' => $detailBanner['vBannerURL'], 
									'#TITLE' => $detailBanner['vBannerName'], 
									'#FILENAME' => $detailBanner['vBannerFile'], 
									'#FILEURL' => $detailBanner['vFileURL'], 
									'#PICTURE' => $GAMBAR
								);
				$Temp .= strtr($linkFormat,$Change);
			}
		}
		
		echo $Temp;
	}
		
}
?>