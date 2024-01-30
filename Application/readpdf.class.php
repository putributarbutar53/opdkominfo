<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class readpdf extends Core
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
		echo $this->Template->Show("readpdf.html");
	}

	function profile()
	{
		$filedata = "dhani.pdf";
		//$filedata = $_GET['file'];

		$this->Template->assign("filePdf", $this->Config['base']['url'].$this->dirUpload."profile/".$filedata);
		echo $this->Template->Show("readpdf.html");
	}

	function link()
	{
		$link = $_GET['link'];

		$this->Template->assign("filePdf", $link);
		echo $this->Template->Show("readpdf.html");
	}

}

?>