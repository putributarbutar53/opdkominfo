<?php if (!defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class contentpage extends Core
{
	var $ContentModule, $Optional;
	function __construct()
	{
		parent::__construct();

		$this->LoadModule("Page");
		$this->LoadModule("Options");
		$this->LoadModule("Paging");
		$this->LoadModule("Content");
		$this->Module->Paging->setPaging(20, 10, "&laquo; Prev", "Next &raquo;");

		$this->Template->assign("SIGNATURE", "pages");
		$this->ContentModule = "page";

		$this->Template->assign("contentDir", $this->Config['content']['dir']);
		$this->Template->assign("pageDir", $this->Config['page']['dir']);

		//Load General Process
		include '../inc/general.php';

		$this->Optional = $OPTIONAL;
	}

	//Page
	function main()
	{
		$vPermalink = $this->uri(1);
		$detailCategory = $this->Module->Page->detailCategoryByPermalink($vPermalink);
		$this->Template->assign('listCategory', $this->Module->Content->listCategory());
		if ($_POST['submit']) {
			$Action = ($_POST['action']) ? $_POST['action'] : $_GET['action'];

			switch ($Action) {
					//Save the comments
				case "savecomment":
					$AddingComment = FALSE;

					$vName = htmlentities($_POST['vName']);
					$vURL = htmlentities($_POST['vURL']);
					$vEmail = htmlentities($_POST['vEmail']);
					$vComment = htmlentities($_POST['vComment']);

					$idNews = $_POST['idNews'];
					$vKode = $_POST['vKode'];
					if (($vName != "") and ($vComment != "")) {
						if ($this->Module->Content->addComment(array(
							'vName' => $vName,
							'vURL' => $vURL,
							'vEmail' => $vEmail,
							'tComment' => $vComment,
							'iStatus' => 1,
							'idContent' => $idNews,
							'vIP' => $_SERVER['REMOTE_ADDR'],
							'dPublishDate' => date('Y-m-d'),
						))) {
							$AddingComment = TRUE;
							$this->Template->reportMessage("success", "Komentar telah di kirimkan untuk di moderasi");
						}
					} else
						$this->Template->reportMessage("error", "Ops! Harap isi setidaknya nama dan komentar anda");
					if ($AddingComment == FALSE)
						$this->Template->assign("DetailComment", array('vName' => $vName, 'vURL' => $vURL, 'vEmail' => $vEmail, 'vComment' => $vComment));
					break;
			}
		}


		if ($detailCategory['id'] == "") {
			$detailCategoryContent = $this->Module->Content->detailCategoryByPermalink($vPermalink);
			if ($detailCategoryContent['id'] == "") {
				ob_end_clean();
				$this->Error("404");
				die();
			} else {
				$this->Template->assign("detailCategory", $detailCategoryContent);
				$contentPemalink = preg_replace("#.html#", "", $this->uri(2));
				if ($contentPemalink != "") {
					$contentDetail = $this->Module->Content->detailContentByPermalink($contentPemalink);
					$this->Template->assign("Detail", $contentDetail);
					$this->Template->assign("countComment", $this->Module->Content->countComment($contentDetail['id']));
					$this->Template->assign("nextContent", $this->Module->Content->nextContent($contentDetail['id']));
					$this->Template->assign("beforeContent", $this->Module->Content->beforeContent($contentDetail['id']));
					$this->Module->Content->readView($contentDetail['id']);
					$this->Template->assign("maps", $this->Module->Content->listModule($contentDetail['id'], 'maps'));
					$this->Template->assign("video", $this->Module->Content->listModule($contentDetail['id'], 'video'));
					$this->Template->assign("alert", $this->Module->Content->listModule($contentDetail['id'], 'alert'));
					$this->Template->assign("listComment", $this->Module->Content->listComment($contentDetail['id']));
				}

				$this->Template->assign("listContent", $this->Module->Content->listContent($detailCategoryContent['id'], 5));
				$this->Template->assign("listModule", $this->Module->Content->listModuleByName($contentDetail['id']));
				$this->Template->assign('listEvent', $this->Module->Content->listContentByCategory('event'));

				$this->Template->assign('listKliping', $this->Module->Content->listContentByCategory('kliping'));
				$this->Template->assign('listFile', json_decode($contentDetail['attacment'], true));
				$this->Template->assign('listLink', json_decode($contentDetail['File'], true));
				// print_r($this->Module->Content->listModuleByName($contentDetail['id']));die();

				if (($contentDetail['id'] != "") and ($contentDetail['iStatus'] == "1")) {
					$METATITLE = ($contentDetail['vMetaTitle']) ? $contentDetail['vMetaTitle'] : $contentDetail['vTitle'];
					$METADESC = ($contentDetail['vMetaDesc']) ? $contentDetail['vMetaDesc'] : substr(preg_replace('#\n#', ' ', preg_replace('#\"#', '', strip_tags($contentDetail['tDetail']))), 0, strpos($contentDetail['tDetail'], ' ', 200));
					$METAKEYWORD = ($contentDetail['vMetaKeyword']) ? $contentDetail['vMetaKeyword'] : $METATITLE;

					$this->Template->assign("METATITLE", $METATITLE . " - " . $detailCategoryContent['vCategory']);
					$this->Template->assign("METADESC", $METADESC);
					$this->Template->assign("METAKEYWORD", $METAKEYWORD);
					if ($detailCategoryContent['vPermalink'] == 'kliping' or $detailCategoryContent['vPermalink'] == 'event')
						echo $this->Template->Show("content-page.html", $themePermalink . "-detail.html");
					else
						echo $this->Template->Show("content-detail.html", $themePermalink . "-detail.html");
				} else {
					$this->Template->assign("METATITLE", $detailCategoryContent['vCategory'] . " - " . $this->Optional['TITLE']['tContent']);
					$this->Template->assign("METADESC", "Berikut informasi perihal " . $detailCategoryContent['vCategory']);
					$this->Template->assign("METAKEYWORD", $detailCategoryContent['vCategory']);

					echo $this->Template->Show("content.html", $themePermalink . ".html");
				}
			}
		} else {
			$this->Template->assign("detailCategory", $detailCategory);
			$listPage = $this->Module->Page->listPageByCategory($detailCategory['id']);
			$this->Template->assign("listPage", $listPage);

			$vPermalinkPage = preg_replace("#.html#", "", $this->uri(2));
			$Baca = $this->Module->Page->detailPageByPermalink($vPermalinkPage);

			if ($Baca['id'] == "")
				$Baca = $this->Module->Page->getDefaultPage($detailCategory['id']);

			$this->Template->assign("Detail", $Baca);
			$this->Template->assign("Detail_Sub", $this->Module->Options->getContentSetting($Baca['id'], $this->ContentModule));

			$METATITLE = ($Baca['vMetaTitle']) ? $Baca['vMetaTitle'] : $Baca['vPageName'];
			$METADESC = ($Baca['vMetaDesc']) ? $Baca['vMetaDesc'] : substr(preg_replace('#\n#', ' ', preg_replace('#\"#', '', strip_tags($Baca['tContent']))), 0, strpos($Baca['tContent'], ' ', 200));
			// $METADESC = ($Baca['vMetaDesc'])?$Baca['vMetaDesc']:substr(strip_tags($contentDetail['tContent']),0,200);
			$METAKEYWORD = ($Baca['vMetaKeyword']) ? $Baca['vMetaKeyword'] : $METATITLE;

			$this->Template->assign("METATITLE", $METATITLE);
			$this->Template->assign("METADESC", $METADESC);
			$this->Template->assign("METAKEYWORD", $METAKEYWORD);

			echo $this->Template->Show("contentpage.html", $Baca['vPermalink'] . ".html");
		}
	}
}
