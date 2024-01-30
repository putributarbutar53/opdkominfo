<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class MAINBANNER extends Core
{
	var $bannerFolder;

	function __construct()
	{	
		parent::__construct();
		$this->LoadModule("Banner");
		$this->LoadModule("Content");
		$this->LoadModule("Options");

		$this->bannerFolder = $this->Config['upload']['bannerdir'];
	}
	
	function Load($Load1="", $Load2="", $Load3="", $Load4="", $Load5="", $Load6="")
	{
		//Load1 = Cat Name
		//Load2 = Limit Banner
		//Load3 = Format List
		//Load4 = Banner Size
		//Load5 = Default Banner
		//Load6 = Active CSS
		
		$linkFormat = $Load3;
		$PicSize = explode(",",$Load4);
		$defaultBanner = $Load5; /* ($Load5=="")?"images/no_banner.gif":$Load5; */
		$Temp = "";
		$getIDBanner = $this->Module->Banner->getIdBanner($Load1);
		$listBanner = $this->Module->Banner->listBanner($getIDBanner, $Load2, " ORDER BY id ASC");
		
		$getLoad6 = explode("|", $Load6);
		$_Load6 = ($Load6=="")?"active":$getLoad6[0];
		
		if ($listBanner[0]['Item']['id']=="")
		{
			//for ($i=0;$i<$Load2;$i++)
			//{
			if ($defaultBanner!="")
			{
				$GAMBAR = "<img src=\"".$defaultBanner."\" width=\"".$PicSize[0]."\" height=\"".$PicSize[1]."\" border=\"0\" />";
				$PICTUREURL = $defaultBanner; //$this->Config['base']['url'].$this->Template->template_dir.$defaultBanner;
				$Change = array(	'#NO' => ($i+1), '#PICTURE' => $GAMBAR, '#URL' => $this->getURL(), '#PICTUREURL' => $PICTUREURL, '#TITLE' => ''	);
				$Temp .= strtr($linkFormat,$Change);
			}
			//}
		}
		else
		{
			$j=0;
			for ($i=0;$i<count($listBanner);$i++)
			{
				$PICTUREURL = $this->Config['base']['url'].$this->bannerFolder.$listBanner[$i]['Item']['vBannerFile'];
				
				$ModuleSetting = $this->Module->Options->getContentSetting($listBanner[$i]['Item']['id'], "banner");
				$PictureSetting = $this->Module->Options->getPictureSetting($listBanner[$i]['Item']['id'], "banner");
				
				if ($listBanner[$i]['Item']['vBannerFile']=="")
					$GAMBAR = "<img alt=\"".$listBanner[$i]['Item']['vBannerName']."\" alt=\"".$listBanner[$i]['Item']['vBannerName']."\" src=\"".$defaultBanner."\" width=\"".$PicSize[0]."\" height=\"".$PicSize[1]."\" border=\"0\" />";
				else
					$GAMBAR = "<img alt=\"".$listBanner[$i]['Item']['vBannerName']."\" alt=\"".$listBanner[$i]['Item']['vBannerName']."\" src=\"images/thumb.php?src=".$this->Template->relativePath().$this->bannerFolder.$listBanner[$i]['Item']['vBannerFile']."&w=".$PicSize[0]."&h=".$PicSize[1]."&zc=1\" width=\"".$PicSize[0]."\" height=\"".$PicSize[1]."\" border=\"0\" />";
	
				$Change = array(	'#NO' => $i, '#URL' => $listBanner[$i]['Item']['vBannerURL'], 
									'#TITLE' => $listBanner[$i]['Item']['vBannerName'], 
									'#FILENAME' => $listBanner[$i]['Item']['vBannerFile'], 
									'#FILEURL' => $listBanner[$i]['Item']['vFileURL'], 
									'#PICTURE' => $GAMBAR, 
									'#PICTUREURL' => $PICTUREURL, 
									'#DETAIL' => $listBanner[$i]['Item']['tDetail'], 
									'#ACTIVE' => (($i==0)?" ".$_Load6:$getLoad6[1]),
									'#MODULE' => $ModuleSetting,
									'#MODULEPIC' => (($PictureSetting=="")?"":$this->Config['base']['url'].$this->bannerFolder.$PictureSetting)
								);
				$Temp .= strtr($linkFormat,$Change);
				$j++;
			}
			
			if ($j<$Load2)
			{
				for ($k=$j;$k<($Load2-$j);$k++)
				{
					if ($defaultBanner!="")
					{
						$PICTUREURL = $defaultBanner; //$this->Config['base']['url'].$this->Template->template_dir.$defaultBanner;
						$GAMBAR = "<img src=\"".$defaultBanner."\" width=\"".$PicSize[0]."\" height=\"".$PicSize[1]."\" border=\"0\" />";
						$Change = array(	'#NO' => ($k), '#PICTURE' => $GAMBAR, '#URL' => $this->getURL(), '#PICTUREURL' => $PICTUREURL, '#TITLE' => ''	);
						$Temp .= strtr($linkFormat,$Change);
					}
				}
			}
		}
		
		echo $Temp;
	}
		
}
?>