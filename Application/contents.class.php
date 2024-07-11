<?php if (!defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class
//error_reporting(E_ALL);
class contents extends Core
{
	var $ContentModule, $Optional;

	function __construct()
	{
		parent::__construct();

		$this->LoadModule("Content");
		$this->LoadModule("Options");
		$this->LoadModule("Paging");
		$this->LoadModule("Duplicateweb");

		$this->Module->Paging->setPaging(20, 10, "&laquo; Prev", "Next &raquo;");

		$this->Template->assign("dirContent", $this->Config['content']['dir']);
		$this->Template->assign("SIGNATURE", "content");

		$this->ContentModule = "content";

		//Load General Process
		include '../inc/general.php';

		$this->Optional = $OPTIONAL;
		ob_end_clean();
	}

	//Category Products
	function main()
	{
		$Request_1 = $this->uri(1);
		$Request_2 = $this->uri(2);
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

					// if ($vKode == $_SESSION['captcha'])
					// {
					if (($vName != "") and ($vComment != "")) {
						if ($this->Module->Content->addComment(array($vName, $vEmail, $vURL, $vComment, $_SERVER['REMOTE_ADDR']), $idNews)) {
							$AddingComment = TRUE;
							$this->Template->reportMessage("success", "Komentar telah di kirimkan untuk di moderasi");
						}
					} else
						$this->Template->reportMessage("error", "Ops! Harap isi setidaknya nama dan komentar anda");
					// }
					// else
					// 	$this->Template->reportMessage("error", "Ops! Kode captcha salah"); 

					if ($AddingComment == FALSE)
						$this->Template->assign("DetailComment", array('vName' => $vName, 'vURL' => $vURL, 'vEmail' => $vEmail, 'vComment' => $vComment));
					break;
			}
		}

		$this->Template->assign("Page", $_GET['page']);
		$detailCategory = $this->Module->Content->detailCategoryByPermalink($Request_1);

		if ($detailCategory['id'] != "")
			$idCategory = $detailCategory['id'];
		else {
			$detailDuplicate = $this->Module->Duplicateweb->detailDuplicateByLink($Request_1);
			if ($detailDuplicate['id'] == "") {
				$this->Error("404");
				die();
				//$fC=$this->Module->Content->getFirstCategory();
				//$idCategory=$fC[id];
			} else {
				setcookie("duplic_user", $detailDuplicate['vLink'], time() + 31556926, '/');
				$_SESSION['duplic_user'] = $detailDuplicate['vLink'];

				$this->Template->assign("Detail", $detailDuplicate);
				echo $this->Template->Show("duplicateweb.html");
				die();
			}
		}
		$detailCategory = $this->Module->Content->detailDir($idCategory);

		$NewsDetail = FALSE;
		$newsPermalink = preg_replace("#.html#", "", $Request_2);

		if ($newsPermalink != "") {
			$newsDetail = $this->Module->Content->detailContentByPermalink($newsPermalink);
			if (($newsDetail['id'] != "") and ($newsDetail['iStatus'] == "1"))
				$NewsDetail = TRUE;
		}

		if ($NewsDetail) {
			$Detail = $this->Module->Content->Youtube($newsDetail['tDetail']);
			$this->Template->assign("Detail", array_merge($newsDetail, array('tDetail' => $Detail))); //News Detail
			$Do = "detail";
			$idCategory = $newsDetail['idCategory'];
			$this->Template->assign("Detail_Sub", $this->Module->Options->getContentSetting($newsDetail['id'], $this->ContentModule));

			$detailCategory = $this->Module->Content->detailDir($idCategory);
			if ($detailCategory['iComment'] == "1") {
				$this->Template->assign("listComment", $this->Module->Content->listComment($newsDetail['id']));
			}
		}
		//Category--------------
		$this->Template->assign("detailCategory", $detailCategory);
		$categoryText = preg_replace("#_#", " ", $detailCategory['vCategory']);
		$this->Template->assign("categoryText", $categoryText);
		//----------------------

		$this->Module->Paging->query("SELECT * FROM cpcontent WHERE idCategory='" . $idCategory . "' AND iStatus='1' ORDER BY dPublishDate DESC");
		$this->Module->Paging->dataLink = "show=list";
		$page = $this->Module->Paging->print_info();
		$this->Template->assign("status", "News " . $page['start'] . " - " . $page['end'] . " of " . $page['total'] . " [Total " . $page['total_pages'] . " Groups]");
		$i = 0;
		while ($Baca = $this->Module->Paging->result_assoc()) {
			$Data[$i] = array(
				'No' => ($i + 1),
				'Item' => $Baca,
				'Category' => $this->Module->Content->detailDir($Baca['idCategory']),
				'Detail_Sub' => $this->Module->Options->getContentSetting($Baca['id'], "content")
			);
			$i++;
		}

		$this->Template->assign("listAnotherNews", $Data);
		$this->Template->assign("link", $this->Module->Paging->print_link());

		$this->Template->assign("AnotherNews", $this->Module->Content->listContent($idCategory));
		$themePermalink = ($newsDetail['vPermalink'] != "") ? $newsDetail['vPermalink'] : $detailCategory['vPermalink'];

		if ($NewsDetail) {
			$METATITLE = ($newsDetail['vMetaTitle']) ? $newsDetail['vMetaTitle'] : $newsDetail['vTitle'];
			$METADESC = ($newsDetail['vMetaDesc']) ? $newsDetail['vMetaDesc'] : substr(preg_replace('#\n#', ' ', preg_replace('#\"#', '', strip_tags($newsDetail['tDetail']))), 0, strpos($newsDetail['tDetail'], ' ', 200));
			$METAKEYWORD = ($newsDetail['vMetaKeyword']) ? $newsDetail['vMetaKeyword'] : $METATITLE;

			$this->Template->assign("METATITLE", $METATITLE . " - " . $detailCategory['vCategory']);
			$this->Template->assign("METADESC", $METADESC);
			$this->Template->assign("METAKEYWORD", $METAKEYWORD);

			echo $this->Template->Show("content-detaila.html", $themePermalink . "-detail.html");
		} else {
			$this->Template->assign("METATITLE", $detailCategory['vCategory'] . " - " . $this->Optional['TITLE']['tContent']);
			$this->Template->assign("METADESC", "Berikut informasi perihal " . $detailCategory['vCategory']);
			$this->Template->assign("METAKEYWORD", $detailCategory['vCategory']);

			echo $this->Template->Show("content.html", $themePermalink . ".html");
		}
	}

	function getmessage()
	{
		$this->Template->assign('listComment', $this->Module->Content->listComment($_GET['idContent']));
		$getData = $this->Template->Show('list_comment.html');
		$data_array = array(
			'data' => $getData
		);
		echo json_encode($data_array);
	}

	function submit_message()
	{
		$vName = $_POST['vName'];
		$tComment = $_POST['tComment'];
		$vSubject = $_POST['vSubject'];
		$vEmail = $_POST['vEmail'];
		$idContent = $_POST['idContent'];
		$vIP = $_SERVER['REMOTE_ADDR'];
		$dPublishDate = date('Y-m-d');
		$iStatus = 1;

		$Action = $_POST['action'];
		switch ($Action) {
			case "add":
				if ($vName != "" and $tComment != "") {
					if ($this->Module->Content->addComment(array(
						'vName' => $vName,
						'tComment' => $tComment,
						// 'vSubject' => $vSubject,
						'idContent' => $idContent,
						'vIP' => $vIP,
						'dPublishDate' => $dPublishDate,
						'iStatus' => $iStatus,
						'vEmail' => $vEmail
					))) {
						$Return = array(
							'status' => 'success',
							'message' => $this->Template->showMessage('success', 'Data category telah di tambahkan'),
							'data' => ''
						);
					} else {
						$Return = array(
							'status' => 'error',
							'message' => $this->Template->showMessage('error', 'Ops! Ada error pada database'),
							'data' => ''
						);
					}
				} else {
					$Return = array(
						'status' => 'error',
						'message' => $this->Template->showMessage('error', 'Data form isian tidak lengkap'),
						'data' => ''
					);
				}
				break;
		}

		echo json_encode($Return);
	}
}
