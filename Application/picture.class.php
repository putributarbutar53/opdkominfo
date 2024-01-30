<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class picture extends Core
{
	function __construct()
	{
		parent::__construct();

		//Load General Process
		include '../inc/general.php';
		
		$this->LoadModule("Page");
		$this->LoadModule("Content");
		$this->Template->assign("dirNews", $this->Config['news']['dir']);

		$this->LoadModule("Paging");
		$this->Module->Paging->setPaging(20,10,"&laquo; Prev","Next &raquo;");

		$this->LoadModule("Photo");
	}

	//Page
	function main()
	{	
		$this->Template->assign("SIGNATURE", "picture");
		$Request_1 = $this->uri(3);
		$vPermalink_ = explode(".",$Request_1);
		$DetailPhoto = $this->Module->Photo->detailPhotoByPermalink($vPermalink_[0]);
		$this->Template->assign("DetailPhoto", $DetailPhoto);

		$idAlbum = $DetailPhoto['idAlbum'];
		$DetailAlbum = $this->Module->Photo->detailAlbum($idAlbum);
		$this->Template->assign("DetailAlbum", $DetailAlbum);
		
		$DetailCategory = $this->Module->Photo->detailCategory($DetailPhoto['idCategory']);
		$this->Template->assign("DetailCategory", $DetailCategory);
		
		$listPhoto = $this->Module->Photo->listPhoto($DetailPhoto['idCategory'],$DetailPhoto['idAlbum'],"","","all");
			
		for($i=0;$i<count($listPhoto);$i++)
		{
			if ($listPhoto[$i]['Item']['id']==$DetailPhoto['id'])
				$iSection = $i;
		}
		$TotalPhoto = count($listPhoto);
		
		if ($iSection > "0")
			$Back = $this->Config['base']['url'].$this->Config['index']['page']."picture/".$listPhoto[($iSection-1)]['Item']['vPermalink'].".html";
		else
			$Back = "";
			
		if (($iSection+1) < $TotalPhoto)
			$Next = $this->Config['base']['url'].$App['index']['page']."picture/".$listPhoto[($iSection+1)]['Item']['vPermalink'].".html";
		else
			$Next = "";
	
		$this->Template->assign("Back", $Back);
		$this->Template->assign("Next", $Next);
		$this->Template->assign("iSection", ($iSection+1));
		$this->Template->assign("totalPhoto", $TotalPhoto);

		$TITLE = $DetailPhoto['vPhotoTitle']." &raquo; ".$DetailAlbum['vAlbum']." &raquo; ".$DetailCategory['vCategory'];
		$this->Template->assign("TITLE", $TITLE);
		$this->Template->assign("METADESC", strip_tags($DetailPhoto['mtDesc']));
		$this->Template->assign("METAKEYWORD", "");
	
		echo $this->Template->Show("picture.html");
	}
	
	function album()
	{
		$this->Template->assign("SIGNATURE", "album");
		$Request_1 = $this->uri(3);

		$u=(($_GET['u']) AND (ereg("^[0-9]+$",$_GET['u'])))?$_GET['u']:"0";
		$this->Template->assign("u", $u);

		$vPermalink_ = explode(".",$Request_1);

		$DetailAlbum = $this->Module->Photo->detailAlbumByPermalink($vPermalink_[0]);
		$this->Template->assign("DetailAlbum", $DetailAlbum);
		
		$DetailCategory = $this->Module->Photo->detailCategory($DetailAlbum['idCategory']);
		$this->Template->assign("DetailCategory", $DetailCategory);
		
		$listThumbnail = $this->Module->Photo->galleryList($this->Module->Photo->listPhoto($DetailAlbum['idCategory'],$DetailAlbum['id'],$u,"",""),array('20%',4,4,0,'','font_8','main'),4,$DetailCategory['iView']);
		
		$this->Template->assign("listThumbnail", $listThumbnail);
		$this->Template->assign("photoIndex", $this->Db->dbBagi("cpphoto"," WHERE idCategory='".$DetailCategory['id']."' AND idAlbum='".$DetailAlbum['id']."'",$App['main']['url'].$App['index']['page']."album/".$DetailAlbum['vPermalink'].".html?","main"));

		$TITLE = $DetailAlbum['vAlbum']." &raquo; ".$DetailCategory['vCategory'];
		$this->Template->assign("TITLE", $TITLE);
		$this->Template->assign("METADESC", "");
		$this->Template->assign("METAKEYWORD", "");
		
		echo $this->Template->Show("picture_album.html");		
	}
	
	function category()
	{
		$this->Template->assign("SIGNATURE", "gallery");
		$listCategory = $this->Module->Photo->listCategory();
		$this->Template->assign("listCategory", $listCategory);
		
		$vPermalink_ = explode(".",$Request_1);
		$vPermalink = ($vPermalink_[0]=="")?$listCategory[0]['Item']['vPermalink']:$vPermalink_[0];

		$DetailCategory = $this->Module->Photo->detailCategoryByPermalink($vPermalink);
		$this->Template->assign("DetailCategory", $DetailCategory);

		$listAlbum = $this->Module->Photo->listAlbumByTable($DetailCategory['id'],array('20%',4,4,0,'','font_8','main'),4);
		$this->Template->assign("listAlbum", $listAlbum);

		$TITLE = ($DetailCategory['vCategory']=="")?"Photo Gallery":$DetailCategory['vCategory'];
		$this->Template->assign("TITLE", $TITLE);
		$this->Template->assign("METADESC", "");
		$this->Template->assign("METAKEYWORD", "");

		echo $this->Template->Show("picture_category.html");
	}

}

?>