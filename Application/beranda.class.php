<?php if (!defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class beranda extends Core
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

		$this->Template->assign('style_2', 1);
		$this->Template->assign('listBanner', $this->Module->Banner->listBanner(1));
		$this->Template->assign('listPopuler', $this->Module->Content->listView(12));
		$this->Template->assign('listVideo', $this->Module->Content->listContentVideo(2));
		$this->Template->assign('listTerkini', $this->Module->Content->listNewContent(10));
		$this->Template->assign('listKliping', $this->Module->Content->listContentByCategory('kliping'));
		$this->Template->assign('listEvent', $this->Module->Content->listContentByCategory('event'));
		$this->Template->assign('listPemerintah', $this->Module->Content->listContentByCategory('pemerintah'));
		$this->Template->assign('Detail', $this->Module->Polling->detail($this->Id));
		echo $this->Template->Show("404.html");
	}
}
