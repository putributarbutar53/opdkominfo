<?php if (!defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class
//error_reporting(E_ALL);
class mydocument extends Core
{
	var $Submit, $Action, $Id, $idComment, $ContentModule, $docDir, $Dir, $vCompare, $vTeks, $Do, $URL, $Category;

	public function __construct()
	{
		parent::__construct();

		//Load General Process
		include '../inc/general_admin.php';

		$this->LoadModule("Document");
		$this->LoadModule("FileManager");
		$this->LoadModule("Paging");

		$this->Module->Paging->setPaging(20, 10, "&laquo; Prev", "Next &raquo;");
		$this->docDir = $this->Config['document']['dir'];
		$this->Pile->fileDestination = $this->docDir;
		$this->Template->assign("docDir", $this->docDir);

		$this->Template->assign("Signature", "document");

		ob_clean();
	}

	function main()
	{
		echo $this->Template->ShowAdmin("document/doc_category.html");
	}

	function getdetailcategory()
	{
		$listCategory = $this->Module->Document->listCategory();
		$this->Template->assign("listCategory", $listCategory);
		$getCategory = $this->Template->ShowAdmin("document/section_category_list.html");
		$json_data = array('category' => $getCategory);
		echo json_encode($json_data);
	}

	function editcategory()
	{
		$this->Template->assign("Detail", $this->Module->Document->detailCategory($this->Id));
		echo $this->Template->ShowAdmin("document/section_category_edit.html");
	}

	function addcategory()
	{
		$vCategory = $_POST['vCategory'];
		$vPermalink = ($_POST['vPermalink'] == "") ? preg_replace("# #", "-", strtolower(preg_replace("/[^a-zA-Z0-9\-\s]/", "", $vCategory))) : preg_replace("# #", "-", strtolower(preg_replace("/[^a-zA-Z0-9\-\s]/", "", $_POST['vPermalink'])));

		$Action = $_POST['action'];
		switch ($Action) {
			case "add":
				if ($vCategory != "") {
					if ($this->Module->Document->addCategory(array(
						'vCategory' => $vCategory,
						'vPermalink' => $vPermalink
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
			case "update":
				$idCategory = $_POST['idcategory'];

				if (($vCategory != "") and ($idCategory != "")) {
					$UpdateField = array(
						'vCategory' => $vCategory,
						'vPermalink' => $vPermalink
					);

					if ($this->Module->Document->updateCategory($UpdateField, $idCategory)) {
						$Return = array(
							'status' => 'success',
							'message' => $this->Template->showMessage('success', 'Data category telah di perbaharui'),
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
						'message' => $this->Template->showMessage('error', 'Ops! Data form isian tidak lengkap'),
						'data' => ''
					);
				}
				break;
		}

		echo json_encode($Return);
	}

	function deletecategory()
	{
		$idCategory = $_GET['idcategory'];
		if ($idCategory != "") {
			$countDocument = $this->Module->Document->countDoc($idCategory);
			if ($countDocument['total'] <= 0) {
				if ($this->Module->Document->deleteCategory($idCategory)) {
					$Return = array(
						'status' => 'success',
						'message' => $this->Template->showMessage('success', 'Data category telah di hapus'),
						'data' => ''
					);
				}
			} else {
				$Return = array(
					'status' => 'error',
					'message' => $this->Template->showMessage('error', 'Ops! Category ini mengandung document, harap hapus dulu document di dalam category'),
					'data' => ''
				);
			}
		} else {
			$Return = array(
				'status' => 'error',
				'message' => $this->Template->showMessage('error', 'Ops! ID category tidak valid'),
				'data' => ''
			);
		}

		echo json_encode($Return);
	}

	//Doc
	function doc()
	{
		$this->Template->assign("detailCategory", $this->Module->Document->detailCategory($this->Id));
		echo $this->Template->ShowAdmin("document/doc_index.html");
	}

	function loaddata()
	{
		$idCategory = $_GET['idcategory'];

		$draw = $_POST['draw'];
		$row = $_POST['start'];
		$rowperpage = $_POST['length'];

		$columnIndex = $_POST['order'][0]['column'];
		$columnName = $_POST['columns'][$columnIndex]['data'];

		$columnSortOrder = $_POST['order'][0]['dir'];
		$searchValue = $_POST['search']['value'];

		//Search
		$searchQuery = "";
		if ($searchValue != '') {
			$searchQuery = " AND (vDocument like '%" . $searchValue . "%')";
		}

		//Total Records without Filtering
		$records = $this->Db->sql_query_array("select count(*) as total from cpdoc WHERE idCategory='" . $idCategory . "'");
		$totalRecords = $records['total'];

		//Total Record with filtering
		$records = $this->Db->sql_query_array("select count(*) as total from cpdoc where id!='0'" . $searchQuery . " AND idCategory='" . $idCategory . "'");
		$totalRecordsWithFilter = $records['total'];

		//Fetch Records
		$orderBy = ($columnName == "") ? " order by id desc" : " order by " . $columnName . " " . $columnSortOrder;
		$limitBy = ($row == "") ? "" : " limit " . $row . "," . $rowperpage;

		$sqlQuery = "select * from cpdoc where id!='0' AND idCategory='" . $idCategory . "'" . $searchQuery . $orderBy . $limitBy;

		$sqlRecord = $this->Db->sql_query($sqlQuery);
		while ($row = $this->Db->sql_array($sqlRecord)) {
			$navButton = "<a href=\"javascript:editdata(" . $row['id'] . ")\"><i class='fas fa-pen-square'></i></a>&nbsp;&nbsp;
			<a href=\"javascript:deletedata(" . $row['id'] . ")\"><i class='fas fa-trash-alt'></i></a>";

			$dCreated = date("d F Y - H:i:s", strtotime($row['dCreated']));
			$vFile_URL = ($row['vFileURL'] != "") ? "<a href=\"" . $row['vFileURL'] . "\" target=\"_blank\"><span class=\"badge badge-warning fs-0\"><i class=\"fas fa-download\"></i> Download</span></a>" : "----";
			$fileURL = ($row['vFile'] != "") ? "<a href=\"" . $this->Config['base']['url'] . $this->docDir . $row['vFile'] . "\" target=\"_blank\"><span class=\"badge badge-warning fs-0\"><i class=\"fas fa-download\"></i> Download</span></a>" : $vFile_URL;

			$data[] = array(
				"vDocument" => "<span class=\"h4\">" . $row['vDocument'] . "</span><br /><span class=\"fs--2 badge badge-dark\">" . $dCreated . "</span>",
				"vFileURL" => $fileURL,
				"navButton" => $navButton,
			);
		}

		//Response
		$response = array(
			"draw" => intval($draw),
			"iTotalRecords" => $totalRecordsWithFilter,
			"iTotalDisplayRecords" => $totalRecords,
			"aaData" => (($data) ? $data : array())
		);

		echo json_encode($response);
	}

	function add()
	{
		$this->Template->assign("idCategory", $_GET['idcategory']);
		echo $this->Template->ShowAdmin("document/doc_add.html");
	}

	function edit()
	{
		$this->Template->assign("Detail", $this->Module->Document->detailDoc($this->Id));
		echo $this->Template->ShowAdmin("document/doc_edit.html");
	}

	function delete()
	{
		$detailDocument = $this->Module->Document->detailDoc($this->Id);
		if ($detailDocument['id']) {
			$this->Pile->deleteOldFile($detailDocument['vFile']);
			if ($this->Module->Document->deleteDoc($this->Id)) {
				$Return = array(
					'status' => 'success',
					'message' => $this->Template->showMessage('success', 'Data document telah di hapus'),
					'data' => ''
				);
			}
		} else {
			$Return = array(
				'status' => 'error',
				'message' => $this->Template->showMessage('error', 'Ops! ID document tidak valid'),
				'data' => ''
			);
		}

		echo json_encode($Return);
	}

	function submit()
	{
		$idCategory = $_POST['idcategory'];
		$vDocument = $_POST['vDocument'];
		$vFileURL = $_POST['vFileURL'];
		$vFile = $this->Pile->uploadDoc($_FILES['vFile'], "doc_" . date("Yndhis") . rand(0, 9) . rand(0, 9) . rand(0, 9));
		$dCreated = date("Y-m-d H:i:s");

		$Action = $_POST['action'];
		switch ($Action) {
			case "add":
				if ($vDocument != "") {
					if ($this->Module->Document->addDoc(array(
						'idCategory' => $idCategory,
						'vDocument' => $vDocument,
						'vFile' => $vFile,
						'dCreated' => $dCreated
					))) {
						$Return = array(
							'status' => 'success',
							'message' => 'Data document telah di tambahkan',
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
			case "update":
				if ($vDocument != "") {
					$detailDoc = $this->Module->Document->detailDoc($this->Id);
					$UpdateField = array(
						'vDocument' => $vDocument
					);

					if ($vFile != "") {
						$this->Pile->deleteOldFile($detailDoc['vFile']);
						$UpdateField = array_merge($UpdateField, array('vFile' => $vFile));
					}

					if ($this->Module->Document->updateDoc($UpdateField, $this->Id)) {
						$Return = array(
							'status' => 'success',
							'message' => 'Data document telah di perbaharui',
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
						'message' => $this->Template->showMessage('error', 'Ops! Data form isian tidak lengkap'),
						'data' => ''
					);
				}
				break;
		}

		echo json_encode($Return);
	}

	//-------------------

	function main_old()
	{
		if ($this->Submit) {
			switch ($this->Action) {
					//Category Content
				case "adddir":
				case "editdir":

					$vCategory = $_POST['vCategory'];
					$vPermalink = ($_POST['vPermalink'] == "") ? preg_replace("# #", "-", strtolower(preg_replace("/[^a-zA-Z0-9\-\s]/", "", $vCategory))) : preg_replace("# #", "-", strtolower(preg_replace("/[^a-zA-Z0-9\-\s]/", "", $_POST['vPermalink'])));
					$iSub = ($_POST['iSub']) ? $_POST['iSub'] : 0;
					$vModule = $this->ContentModule;
					$template_backend = $_POST['template_backend'];
					$template_frontend = $_POST['template_frontend'];

					if ($vCategory != "") {
						if ($this->Action == "adddir") {
							if ($this->Module->Content->addDir(array($vCategory, $vPermalink, $iSub, $vModule, $template_backend, $template_frontend)))
								$this->Template->reportMessage("success", "Content directory telah ditambahkan");
						}

						if ($this->Action == "editdir") {
							if ($this->Module->Content->updateDir(array($vCategory, $vPermalink, $iSub, $vModule, $template_backend, $template_frontend), $this->Id))
								$this->Template->reportMessage("success", "Content directory telah di perbaharui");
						}
					}

					break;
				case "deletedir":
					if ($this->Id) {
						if ($this->Module->Content->deleteDir($this->Id))
							$this->Template->reportMessage("success", "Content directory " . $this->Id . " telah dihapus");
					}
					break;
			}
		}

		$this->Template->assign("listTemplateAdmin", $this->Module->FileManager->readTemplate($this->Config['admin']['themes'], "content_"));
		$this->Template->assign("listTemplateFrontend", $this->Module->FileManager->readTemplate($this->Config['main']['themes'], "content_"));

		if ($this->Do == "editdir") {
			$this->Template->assign("detailCategory", $this->Module->Content->detailDir($this->Id));
			$this->Template->assign("hiddenOption", "<input type=hidden name=action value=editdir><input type=hidden name=id value=" . $this->Id . ">");
		} else
			$this->Template->assign("hiddenOption", "<input type=hidden name=action value=adddir>");

		$this->Template->assign("listCategory", $this->Module->Content->listDir());
		echo $this->Template->ShowAdmin("content_category.html");
	}

	function content()
	{
		if ($this->Submit) {
			switch ($this->Action) {
					//Content
				case "add":
				case "edit":

					$vTitle = $_POST['vTitle'];
					$vPermalink = ($_POST['vPermalink'] == "") ? preg_replace("# #", "-", strtolower(preg_replace("/[^a-zA-Z0-9\-\s]/", "", $vTitle))) : preg_replace("# #", "-", strtolower(preg_replace("/[^a-zA-Z0-9\-\s]/", "", $_POST['vPermalink'])));
					$tDetail = $_POST['tDetail'];
					$dPublishDate = (($_POST['dPublishDate'] == "") or ($_POST['dPublishDate'] == "0000-00-00")) ? date("Y-m-d") : $_POST['dPublishDate'];

					$vGambar = $this->Pile->simpanImage($_FILES['vGambar'], "news_" . date("Yndhis"));
					$vOutsideLink = $_POST['vOutsideLink'];

					$vMetaTitle = ($_POST['vMetaTitle'] == "") ? $vTitle : $_POST['vMetaTitle'];
					$vMetaDesc = ($_POST['vMetaDesc'] == "") ? substr(strip_tags(preg_replace("#\n#", " ", preg_replace("#\r#", "", $tDetail))), 0, 160) : $_POST['vMetaDesc'];
					$vMetaKeyword = ($_POST['vMetaKeyword'] == "") ? $vTitle : $_POST['vMetaKeyword'];

					$template_frontend = $_POST['template_frontend'];

					$vType = "";

					if (trim($_POST['vTitle'] == "")) {
						$this->Template->reportMessage("error", "Ops! Judul content tidak bisa kosong");
						$Do = "add";
					} else {
						$SubData = $_POST['sub_data'];
						$SubImage = $_POST['sub_image'];

						if ($SubData != "") {
							$SubData = explode(",", $SubData);
							for ($i = 0; $i < count($SubData); $i++)
								$Sub_Data[$SubData[$i]] = $_POST[$SubData[$i]];
						}

						if ($SubImage != "") {
							$SubImage = explode(",", $SubImage);
							for ($i = 0; $i < count($SubImage); $i++)
								$Sub_Image[$SubImage[$i]] = $this->Pile->simpanImage($_FILES[$SubImage[$i]], "sub_" . $i . "_" . date("Yndhis"));
						}

						if ($this->Action == "add") {
							$idCategory = $_POST['dir'];

							if ($this->Module->Content->addContent(array($idCategory, $vTitle, $vPermalink, $dPublishDate, $tDetail, $vGambar, $vOutsideLink, $vMetaTitle, $vMetaDesc, $vMetaKeyword, $template_frontend))) {
								if (is_array($Sub_Data)) {
									$lastContent = $this->Module->Content->getLastContent();
									foreach ($Sub_Data as $Key => $Value)
										$this->Module->Options->addContentSetting(array($lastContent['id'], $Key, $Value, $this->ContentModule, $vType));
								}

								if (is_array($Sub_Image)) {
									if ($lastContent['id'] == "")
										$lastContent = $this->Module->Content->getLastContent();

									foreach ($Sub_Image as $Key => $Value)
										$this->Module->Options->addContentSetting(array($lastContent['id'], $Key, $Value, $this->ContentModule, $vType));
								}

								$this->Template->reportMessage("success", "Content telah di tambahkan");
							} else
								$this->Template->reportMessage("error", "Penambahan content gagal");
						}

						if ($this->Action == "edit") {
							$detailContent = $this->Module->Content->detailContent($this->Id);
							//Jika gambar di perbaharui, hapus yang lama
							if ($vGambar != "")
								$this->Pile->deleteOldFile($detailContent['vGambar']);

							if ($this->Module->Content->updateContent(array($vTitle, $vPermalink, $dPublishDate, $tDetail, $vOutsideLink, $vMetaTitle, $vMetaDesc, $vMetaKeyword, $template_frontend), $vGambar, $this->Id)) {
								if (is_array($Sub_Data)) {
									foreach ($Sub_Data as $Key => $Value) {
										$CheckContent = $this->Module->Options->checkContentSetting($this->Id, $Key, $this->ContentModule);
										if ($CheckContent['vName'] == "")
											$this->Module->Options->addContentSetting(array($this->Id, $Key, $Value, $this->ContentModule, $vType));
										else
											$this->Module->Options->updateContentSetting(array($Value), $this->Id, $Key, $this->ContentModule);
									}
								}

								if (is_array($Sub_Image)) {
									foreach ($Sub_Image as $Key => $Value) {
										$CheckContent = $this->Module->Options->checkContentSetting($this->Id, $Key, $this->ContentModule);
										if ($CheckContent['vName'] == "")
											$this->Module->Options->addContentSetting(array($this->Id, $Key, $Value, $this->ContentModule, $vType));
										else {
											if ($Value != "") {
												$this->Pile->deleteOldFile($CheckContent['vData']);
												$this->Module->Options->updateContentSetting(array($Value), $this->Id, $Key, $this->ContentModule);
											}
										}
									}
								}
								$this->Template->reportMessage("success", "Content " . $this->Id . " telah diperbaharui");
							}
						}
					}
					break;
				case "deletepic":
					$detailContent = $this->Module->Content->detailContent($this->Id);
					if ($detailContent['id'] != "") {
						$this->Pile->deleteOldFile($detailContent['vGambar']);

						if ($this->Module->Content->deleteIcon($this->Id))
							$this->Template->reportMessage("success", "Gambar untuk content " . $this->Id . " telah di hapus");
					}
					break;
				case "delete":
					$detailContent = $this->Module->Content->detailContent($this->Id);
					if ($detailContent['id']) {
						$listContentSetting = $this->Module->Options->listContentSetting($detailContent['id'], $this->ContentModule);
						for ($j = 0; $j < count($listContentSetting); $j++) {
							$this->Pile->deleteOldFile($listContentSetting[$j]['Item']['vData']);
							$this->Module->Options->deleteContentSettingByID($listContentSetting[$j]['Item']['id'], $this->ContentModule);
						}

						$this->Pile->deleteOldFile($detailContent['vGambar']);

						if ($this->Module->Content->deleteContent($this->Id))
							$this->Template->reportMessage("success", "Content " . $this->Id . " telah di hapus");
					}
					break;
			}
		}

		if (($this->vCompare == "") or ($this->vTeks == ""))
			$this->Module->Paging->query("SELECT * FROM cpcontent WHERE idCategory='" . $this->Dir . "' ORDER BY id DESC");
		else
			$this->Module->Paging->query("SELECT * FROM cpcontent WHERE idCategory='" . $this->Dir . "' AND (" . $this->vCompare . " LIKE '%" . $this->vTeks . "%') ORDER BY id DESC");

		$this->Module->Paging->dataLink = "dir=" . $this->Dir . "&vCompare=" . $this->vCompare . "&vTeks=" . $this->vTeks;
		$page = $this->Module->Paging->print_info();
		$this->Template->assign("status", "Category " . $page[start] . " - " . $page[end] . " of " . $page[total] . " [Total " . $page[total_pages] . " Groups]");
		$i = 0;
		while ($Baca = $this->Module->Paging->result_assoc()) {
			$Data[$i] = array(
				'No' => ($i + 1),
				'Item' => $Baca,
				'Comment' => $this->Module->Content->countComment($Baca['id']),
				'Setting' => $this->Module->Options->getContentSetting($Baca['id'], $this->ContentModule)
			);
			$i++;
		}
		$this->Template->assign("list", $Data);
		$this->Template->assign("link", $this->Module->Paging->print_link());
		echo $this->Template->ShowAdmin("content_index.html", "content_index_" . strtolower($this->Category['vPermalink']) . ".html");
	}

	function add_old()
	{
		$this->Template->assign("Do", "add");

		$hiddenOption = "<input type=hidden name=submit value=yes>";
		$hiddenOption .= "<input type=hidden name=action value=add>";
		$hiddenOption .= "<input type=hidden name=dir value=" . $this->Dir . ">";

		$this->Template->assign("listTemplateFrontend", $this->Module->FileManager->readTemplate($this->Config['main']['themes'], "content_"));

		$this->Template->assign("maxFileSize", $this->Config['file']['max_icon_size']);
		$this->Template->assign("hiddenOption", $hiddenOption);

		echo $this->Template->ShowAdmin("content_add_edit.html", "content_add_edit_" . strtolower($this->Category['vPermalink']) . ".html");
	}

	function edit_old()
	{
		$this->Template->assign("Do", "edit");

		if ($this->Submit) {
			switch ($this->Action) {
				case "deletepic":
					$detailContent = $this->Module->Content->detailContent($this->Id);
					if ($detailContent['id'] != "") {
						$this->Pile->deleteOldFile($detailContent['vGambar']);

						if ($this->Module->Content->deletePicture($this->Id))
							$this->Template->reportMessage("success", "Gambar untuk content " . $this->Id . " telah di hapus");
					}
					break;
			}
		}

		$this->Template->assign("listTemplateFrontend", $this->Module->FileManager->readTemplate($this->Config['main']['themes'], "content_"));

		$listSetting = $this->Module->Options->getContentSetting($this->Id, $this->ContentModule);
		$this->Template->assign("listSetting", $listSetting);

		$Baca = $this->Module->Content->detailContent($this->Id);
		$this->Template->assign("dPublishDate", $Baca['dPublishDate']);
		$this->Template->assign("Detail", $Baca);

		$hiddenOption = "<input type=hidden name=submit value=yes>";
		$hiddenOption .= "<input type=hidden name=action value=edit>";
		$hiddenOption .= "<input type=hidden name=id value=" . $this->Id . ">";
		$hiddenOption .= "<input type=hidden name=dir value=" . $this->Dir . ">";

		$this->Template->assign("maxFileSize", $this->Config['file']['max_icon_size']);
		$this->Template->assign("hiddenOption", $hiddenOption);

		echo $this->Template->ShowAdmin("content_add_edit.html", "content_add_edit_" . strtolower($this->Category['vPermalink']) . ".html");
	}

	function d_commentedit()
	{
		$this->Template->assign("postURL", $this->URL . "?content/dir=" . $Dir . "&id=" . $this->Id);
		$this->Template->assign("Comment", $this->Module->Content->detailComment($this->idComment));
		echo $this->Template->ShowAdmin("contentcomment_edit.html", "contentcomment_edit_" . strtolower($this->Category['vPermalink']) . ".html");
	}

	function commentedit()
	{
		$this->Template->assign("postURL", $this->URL . "commentedit");
		$this->Template->assign("Comment", $this->Module->Content->detailComment($this->idComment));
		echo $this->Template->ShowAdmin("contentcomment_edit.html");
	}

	function comment()
	{
		if ($this->Submit) {
			switch ($this->Action) {
				case "enablecomment":
					if ($this->Module->Content->setComment("1", $this->idComment))
						$this->Template->reportMessage("success", "Comment " . $this->idComment . " telah di setujui");
					break;
				case "disablecomment":
					if ($this->Module->Content->setComment("0", $this->idComment))
						$this->Template->reportMessage("success", "Comment " . $this->idComment . " telah di sembunyikan");
					break;
				case "updatecomment":
					$vName = $_POST['vName'];
					$vEmail = $_POST['vEmail'];
					$vURL = $_POST['vURL'];
					$tComment = $_POST['tComment'];
					$iStatus = $_POST['iStatus'];

					if (($vName != "") and ($vEmail != "") and ($vURL != "")) {
						if ($this->Module->Content->updateComment(array($vName, $vEmail, $vURL, $tComment, $iStatus), $this->idComment))
							$this->Template->reportMessage("success", "Comment " . $this->idComment . " telah di perbaharui");
					}

					break;
				case "deletecomment":
					if ($this->Module->Content->deleteComment($this->idComment))
						$this->Template->reportMessage("success", "Comment " . $this->idComment . " telah di hapus");
					break;
			}
		}
		$this->Template->assign("listComment", $this->Module->Content->listComment());
		echo $this->Template->ShowAdmin("contentcomment_index.html");
	}

	function detail_old()
	{
		//Get the content detail for editing---------------------
		$listSetting = $this->Module->Options->getContentSetting($this->Id, $this->ContentModule);
		$this->Template->assign("listSetting", $listSetting);

		$Baca = $this->Module->Content->detailContent($this->Id);
		$this->Template->assign("dPublishDate", $Baca['dPublishDate']);

		$this->Template->assign("Detail", $Baca);

		$this->Template->assign("listComment", $this->Module->Content->listCommentByContent($this->Id));
		echo $this->Template->ShowAdmin("content_detail.html", "content_detail_" . strtolower($this->Category['vPermalink']) . ".html");
	}
}
