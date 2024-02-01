<?php if (!defined('ONPATH'))
	exit('No direct script access allowed'); //Mencegah akses langsung ke class

class index extends Core
{
	function __construct()
	{
		parent::__construct();

		$this->LoadModule("Page");
		$this->LoadModule("Options");
		$this->LoadModule("Banner");
		$this->LoadModule("Content");
		$this->LoadModule("Polling");
		$this->Template->assign("dirNews", $this->Config['news']['dir']);
		$this->Template->assign("dirPage", $this->Config['page']['dir']);
		$this->Template->assign("dirContent", $this->Config['content']['dir']);
		$this->Template->assign("dirBanner", $this->Config['upload']['bannerdir']);

		$this->LoadModule("Paging");
		$this->Module->Paging->setPaging(20, 10, "&laquo; Prev", "Next &raquo;");

		//Load General Process
		include '../inc/general.php';

		//Load Plugin
		$this->LoadPlugin("CAPTCHA");
		$this->Template->assign("urlWEB", $this->Config['sub']['domain']);
	}

	function main()
	{
		$category = $this->Module->Page->detailCategoryByPermalink('home');
		$this->Template->assign('listBanner', $this->Module->Banner->listBanner(1));
		$this->Template->assign('listMenu', $this->Module->Page->listPages($category['id']));
		$this->Template->assign('listPopuler', $this->Module->Content->listView(12));
		$this->Template->assign('listTerkini', $this->Module->Content->listNewContent(10));
		$this->Template->assign('listComment', $this->Module->Content->listComment(5));
		$this->Template->assign('listVideo', $this->Module->Content->listContentVideo(2));
		echo $this->Template->Show("index.html");
	}
}
