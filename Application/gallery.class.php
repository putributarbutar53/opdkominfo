<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class gallery extends Core
{
	var $photoDir;
	function __construct()
	{
		parent::__construct();

		$this->LoadModule("Options");
		//Load General Process
		include '../inc/general.php';

		$this->LoadModule("Photo");
		$this->photoDir = $this->Config['upload']['photodir'];
		$this->Template->assign("photoDir", $this->photoDir);

		$this->LoadModule("Paging");
		$this->Module->Paging->setPaging(20,10,"&laquo; Prev","Next &raquo;");
		
		$this->Template->assign("SIGNATURE", "gallery");
	}

	//Photo
	function main()
	{
		$vPermalink = $this->uri(2);

		if ($vPermalink!="")
		{
			$this->Template->assign("detailAlbum", $this->Module->Photo->detailAlbumByPermalink($vPermalink));
			$listByAlbum = $this->Module->Photo->listByAlbum($vPermalink);
			$this->Template->assign("listPhoto", $listByAlbum);

			echo $this->Template->Show("gallery.html");
		}
		else
		{
			$this->Template->assign("listAlbum", $this->Module->Photo->listAlbum());
			echo $this->Template->Show("gallery-album.html");
		}
	}
	
}

?>