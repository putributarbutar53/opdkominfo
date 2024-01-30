<?php if (!defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class pages extends Core
{
	var $ContentModule;
	function __construct()
	{
		parent::__construct();

		$this->LoadModule("Page");
		$this->LoadModule("Options");
		$this->LoadModule("Content");
		$this->LoadModule("Paging");
		$this->LoadModule("Message");
		$this->Module->Paging->setPaging(20, 10, "&laquo; Prev", "Next &raquo;");

		$this->Template->assign("SIGNATURE", "pages");
		$this->Template->assign("pageDir", $this->Config['page']['dir']);
		$this->Template->assign("dirContent", $this->Config['content']['dir']);
		$this->ContentModule = "page";

		//Load General Process
		include '../inc/general.php';
	}

	//Page
	function main()
	{
		if ($_POST['submit']) {
			$Action = ($_POST['action']) ? $_POST['action'] : $_GET['action'];

			switch ($Action) {
					//Save the comments
				case "hubungi-kami":
					$name = htmlentities($_POST['name']);
					$subject = htmlentities($_POST['subject']);
					$email = htmlentities($_POST['email']);
					$phone = htmlentities($_POST['phone']);
					$message = htmlentities($_POST['message']);

					if (($name != "") and ($message != "")) {
						if ($this->Module->Message->add(array(
							'name' => $name,
							'subject' => $subject,
							'email' => $email,
							'phone' => $phone,
							'message' => $message,
						))) {
							$this->Template->reportMessage("success", "Pesan berhasil di kirim");
						}
					} else
						$this->Template->reportMessage("error", "Ops! form isian tidak lengkap");
					break;
			}
		}

		if ($_GET['id'] == "") {
			$vPermalink = preg_replace("#.html#", "", $this->uri(2));
			$Baca = $this->Module->Page->detailPageByPermalink($vPermalink);
		} else {
			$Id = $_GET['id'];
			$Baca = $this->Module->Page->detailPage($Id);
		}

		if (($Baca['iShow'] == "0") or ($Baca['id'] == "")) {
			ob_end_clean();
			$this->Error("404");
			die();
		} else {
			$this->Template->assign("PAGENAME", $Baca['vPermalink']);
			//List Sub Pages

			$this->Template->assign("listITopMenu", $this->Module->Page->listITopMenu($Baca['id']));
			$checkSub = 0;
			if ($Baca['iTopMenu'] != "0")
				$checkSub = $this->Module->Page->checkITopMenu($Baca['iTopMenu']);

			if ($checkSub > 0) {
				$detailTop = $this->Module->Page->detailPage($Baca['iTopMenu']);
				$this->Template->assign("Sidelink", array('Sub' => 'yes', 'vPageStatus' => $detailTop['vPageName']));
			} else
				$this->Template->assign("Sidelink", array_merge(array('Sub' => ''), $this->Module->Page->detailCategory($Baca['idCategory'])));
			//Page Detail

			$tContent = $this->Module->Page->Youtube($this->Module->Page->FFile($this->Module->Page->Folder($Baca['tContent'])));
			$this->Template->assign("tContent", $tContent);

			$this->Template->assign("Detail", $Baca);
			$this->Template->assign("Detail_Sub", $this->Module->Options->getContentSetting($Baca['id'], $this->ContentModule));
			$METATITLE = ($Baca['vMetaTitle']) ? $Baca['vMetaTitle'] : $Baca['vPageName'];
			$METADESC = ($Baca['vMetaDesc']) ? $Baca['vMetaDesc'] : substr(preg_replace('#\n#', ' ', preg_replace('#\"#', '', strip_tags($Baca['tContent']))), 0, strpos($Baca['tContent'], ' ', 200));
			$METAKEYWORD = ($Baca['vMetaKeyword']) ? $Baca['vMetaKeyword'] : $METATITLE;

			$this->Template->assign("METATITLE", $METATITLE);
			$this->Template->assign("METADESC", $METADESC);
			$this->Template->assign("METAKEYWORD", $METAKEYWORD);
			if ($Baca['vPermalink'] == 'berita') {
				$per_page = 20;

				// menentukan halaman saat ini
				if (isset($_GET["page"])) {
					$page = $_GET["page"];
				} else {
					$page = 1;
				}

				// menentukan posisi data pada halaman saat ini
				$start_from = ($page - 1) * $per_page;

				$this->Template->assign('listBerita', $this->Module->Content->listAllPagination($start_from, $per_page));
				$this->Template->assign('countPagination', $this->Module->Content->countPagination($page, $per_page));
			}

			$this->Template->assign('listPesan', $this->Module->Message->listPesan('3'));
			echo $this->Template->Show("page.html", $Baca['vPermalink'] . ".html");
		}
	}
}
