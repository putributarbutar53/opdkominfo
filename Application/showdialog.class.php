<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class showdialog extends Core
{
	var $ContentModule, $dirUpload;
	function __construct()
	{
		parent::__construct();

		$this->LoadModule("Page");
		$this->LoadModule("Options");
		$this->LoadModule("Paging");
		$this->Module->Paging->setPaging(20,10,"&laquo; Prev","Next &raquo;");

		$this->dirUpload = $this->Config['upload']['dir'];

		$this->Template->assign("SIGNATURE", "readpdf");
		//Load General Process
		include '../inc/general.php';
	}

	//Page
	function main()
	{	
		echo $this->Template->Show("showdialog.html");
	}

	function page()
	{
		$vPermalink = $_GET['page'];
		$detailPage = $this->Module->Page->detailPageByPermalink($vPermalink);
		$this->Template->assign("Detail", $detailPage);
		echo $this->Template->Show("showdialog.html");
	}
	
}

?>