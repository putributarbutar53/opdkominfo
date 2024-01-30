<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class download extends Core
{
	var $photoDir, $Optional;
	function __construct()
	{
		parent::__construct();

		$this->LoadModule("Options");
		//Load General Process
		include '../inc/general.php';
		$this->Optional = $OPTIONAL;

		$this->LoadModule("Document");

		$this->docDir = $this->Config['document']['dir'];
		$this->Pile->fileDestination = $this->docDir;
		$this->Template->assign("docDir", $this->docDir);

		$this->LoadModule("Paging");
		$this->Module->Paging->setPaging(20,10,"&laquo; Prev","Next &raquo;");
		
		$this->Template->assign("SIGNATURE", "download");
	}

	//Photo
	function main()
	{
		$vPermalink = $this->uri(2);
		if ($vPermalink!="")
		{
			$detailCategory = $this->Module->Document->detailCategoryByPermalink($vPermalink);
			$this->Template->assign("detailCategory", $detailCategory);
			$listDocument = $this->Module->Document->listAllDoc($detailCategory['id']);
			$this->Template->assign("listDoc", $listDocument);

			$METATITLE = "Download data / document / file ".$detailCategory['vCategory'];
			$METADESC = "Download data / document / file ".$detailCategory['vCategory']." - ".$this->Optional['TITLE']['tContent'];
			$METAKEYWORD = $METATITLE;

			$this->Template->assign("METATITLE", $METATITLE." - ".$detailCategoryContent['vCategory']);
			$this->Template->assign("METADESC", $METADESC);
			$this->Template->assign("METAKEYWORD", $METAKEYWORD);

			echo $this->Template->Show("download.html");
		}
		else
		{
			$this->Template->assign("METATITLE", "List Category Download - ".$this->Optional['TITLE']['tContent']);
			$this->Template->assign("METADESC", "Berikut informasi category download ".$this->Optional['TITLE']['tContent']);
			$this->Template->assign("METAKEYWORD", $this->Optional['TITLE']['tContent']);

			$this->Template->assign("listCategory", $this->Module->Document->listCategory());
			echo $this->Template->Show("download-category.html");
		}
	}
	
}

?>