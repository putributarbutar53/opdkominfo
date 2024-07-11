<?php if (!defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class mygallery extends Core
{
	var $Submit, $Action, $Do, $Id, $Show, $photoDir, $idAlbum, $vCompare, $vTeks, $Page;
	public function __construct()
	{
		parent::__construct();

		//Load General Process
		include '../inc/general_admin.php';

		$this->LoadModule("Photo");

		$this->photoDir = $this->Config['upload']['photodir'];
		$this->Template->assign("photoDir", $this->photoDir);
		$this->Pile->fileDestination = $this->photoDir;

		$this->LoadModule("Paging");
		$this->Module->Paging->setPaging(20, 10, "&laquo; Prev", "Next &raquo;");

		$this->Template->assign("Signature", "photo");
		$this->idAlbum = ($_GET['idAlbum']) ? $_GET['idAlbum'] : "0";

		ob_clean();
	}

	function main()
	{
		echo $this->Template->ShowAdmin("gallery/gallery_album.html");
	}

	function getdetailcategory()
	{
		$listAlbum = $this->Module->Photo->listAlbum();
		$this->Template->assign("listAlbum", $listAlbum);
		$getAlbum = $this->Template->ShowAdmin("gallery/section_album_list.html");
		$json_data = array('category' => $getAlbum);
		echo json_encode($json_data);
	}

	function editcategory()
	{
		$this->Template->assign("Detail", $this->Module->Photo->detailAlbum($this->Id));
		echo $this->Template->ShowAdmin("gallery/section_album_edit.html");
	}

	function addcategory()
	{
		$vAlbum = $_POST['vAlbum'];
		$vPermalink = ($_POST['vPermalink'] == "") ? preg_replace("# #", "-", strtolower(preg_replace("/[^a-zA-Z0-9\-\s]/", "", $vAlbum))) : preg_replace("# #", "-", strtolower(preg_replace("/[^a-zA-Z0-9\-\s]/", "", $_POST['vPermalink'])));

		$Action = $_POST['action'];
		switch ($Action) {
			case "add":
				if ($vAlbum != "") {
					if ($this->Module->Photo->addAlbum(array(
						'vAlbum' => $vAlbum,
						'vPermalink' => $vPermalink
					))) {
						$Return = array(
							'status' => 'success',
							'message' => $this->Template->showMessage('success', 'Album photo telah di tambahkan'),
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

				if (($vAlbum != "") and ($idCategory != "")) {
					$UpdateField = array(
						'vAlbum' => $vAlbum,
						'vPermalink' => $vPermalink
					);

					if ($this->Module->Photo->updateAlbum($UpdateField, $idCategory)) {
						$Return = array(
							'status' => 'success',
							'message' => $this->Template->showMessage('success', 'Album photo telah di perbaharui'),
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
			$countPhoto = $this->Module->Photo->countPhoto($idCategory);
			if ($countPhoto['total'] <= 0) {
				if ($this->Module->Photo->deleteAlbum($idCategory)) {
					$Return = array(
						'status' => 'success',
						'message' => $this->Template->showMessage('success', 'Album photo telah di hapus'),
						'data' => ''
					);
				}
			} else {
				$Return = array(
					'status' => 'error',
					'message' => $this->Template->showMessage('error', 'Ops! Album photo ini mengandung data photo, harap hapus dulu data banner di dalam album'),
					'data' => ''
				);
			}
		} else {
			$Return = array(
				'status' => 'error',
				'message' => $this->Template->showMessage('error', 'Ops! ID album tidak valid'),
				'data' => ''
			);
		}

		echo json_encode($Return);
	}

	//Gallery
	function gallery()
	{
		$this->Template->assign("detailAlbum", $this->Module->Photo->detailAlbum($this->Id));
		echo $this->Template->ShowAdmin("gallery/gallery_index.html");
	}

	function loaddata()
	{
		$idAlbum = $_GET['idalbum'];

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
			$searchQuery = " AND ((vPhotoTitle like '%" . $searchValue . "%') OR (mtDesc like '%" . $searchValue . "%'))";
		}

		//Total Records without Filtering
		$records = $this->Db->sql_query_array("select count(*) as total from cpphoto WHERE idAlbum='" . $idAlbum . "'" . $searchQuery);
		$totalRecords = $records['total'];

		//Total Record with filtering
		$records = $this->Db->sql_query_array("select count(*) as total from cpphoto where id!='0'" . $searchQuery . " AND idAlbum='" . $idAlbum . "'");
		$totalRecordsWithFilter = $records['total'];

		//Fetch Records
		$orderBy = ($columnName == "") ? " order by id desc" : " order by " . $columnName . " " . $columnSortOrder;
		$limitBy = ($row == "") ? "" : " limit " . $row . "," . $rowperpage;

		$sqlQuery = "select * from cpphoto where id!='0' AND idAlbum='" . $idAlbum . "'" . $searchQuery . $orderBy . $limitBy;

		$sqlRecord = $this->Db->sql_query($sqlQuery);
		while ($row = $this->Db->sql_array($sqlRecord)) {
			$navButton = "<a href=\"javascript:editdata(" . $row['id'] . ")\"><i class='fas fa-pen-square'></i></a>&nbsp;&nbsp;
			<a href=\"javascript:deletedata(" . $row['id'] . ")\"><i class='fas fa-trash-alt'></i></a>";

			$dPublishDate = date("d F Y", strtotime($row['dPublishDate']));
			$Image_Pic = ($row['vPhotoName'] != "") ? "<img class=\"img-fluid\" src=\"" . $this->Config['base']['url'] . $this->photoDir . $row['vPhotoName'] . "\" width=\"200\" />" : "<img class=\"img-fluid\" src=\"" . $this->Config['base']['url'] . $this->Config['admin']['themes'] . "assets/img/no-picture.jpg\" width=\"200\" />";

			//$photo_Text = $Image_Pic;
			$photo_Text = "<a href=\"javascript:detaildata(" . $row['id'] . ")\"><span class=\"h4\">" . $row['vPhotoTitle'] . "</span></a>";
			$photo_Text .= "<br /><span class=\"fs--1\">" . $dPublishDate . "</span>";

			$data[] = array(
				"vPhotoTitle" => "<a href=\"javascript:detaildata(" . $row['id'] . ")\">" . $Image_Pic . "</a>",
				"vPhotoName" => $photo_Text,
				// "dCreated" => $dPublishDate,
				"navPage" => $navButton,
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
		$this->Template->assign("idAlbum", $_GET['idalbum']);
		echo $this->Template->ShowAdmin("gallery/gallery_add.html");
	}

	function edit()
	{
		$this->Template->assign("Detail", $this->Module->Photo->detailPhoto($this->Id));
		echo $this->Template->ShowAdmin("gallery/gallery_edit.html");
	}

	function detail()
	{
		$detailPhoto = $this->Module->Photo->detailPhoto($this->Id);
		$detailAlbum = $this->Module->Photo->detailAlbum($detailPhoto['idAlbum']);
		$this->Template->assign("Detail", $detailPhoto);
		$this->Template->assign("detailAlbum", $detailAlbum);

		echo $this->Template->ShowAdmin("gallery/gallery_detail.html");
	}

	function delete()
	{
		$detailPhoto = $this->Module->Photo->detailPhoto($this->Id);
		if ($detailPhoto['id']) {
			$this->Pile->deleteOldFile($detailPhoto['vPhotoName']);
			if ($this->Module->Photo->deletePhoto($this->Id)) {
				$Return = array(
					'status' => 'success',
					'message' => $this->Template->showMessage('success', 'Data photo telah di hapus'),
					'data' => ''
				);
			}
		} else {
			$Return = array(
				'status' => 'error',
				'message' => $this->Template->showMessage('error', 'Ops! ID photo tidak valid'),
				'data' => ''
			);
		}

		echo json_encode($Return);
	}

	function submitmultiple()
	{
		$idAlbum = $_GET['idalbum'];

		$vPhotoTitle = "No Caption";
		$vPermalink = "photo-" . date("YmdHis");
		$mtDesc = "No Description";
		$dPublishDate = date("Y-m-d");

		$Progress = false;

		if (!empty($_FILES)) {
			foreach ($_FILES['vPhotoName']['tmp_name'] as $key => $value) {

				$tempFile = $_FILES['vPhotoName']['tmp_name'][$key];

				$Ext = explode(".", strrev($_FILES['vPhotoName']['name'][$key]));
				$fileExt = strrev($Ext[0]);

				$newName = "photo_" . date("Yndhis") . rand(0, 9) . rand(0, 9) . rand(0, 9) . "." . $fileExt;
				$targetFile = $this->photoDir . $newName;

				move_uploaded_file($tempFile, $targetFile);

				// $vPhotoName = $this->Pile->simpanImage($_FILES['vPhotoName'][$key],"photo_".date("Yndhis").rand(0,9).rand(0,9).rand(0,9));		

				$this->Module->Photo->addPhoto(array(
					'idAlbum' => $idAlbum,
					'vPhotoTitle' => $vPhotoTitle,
					'vPermalink' => $vPermalink,
					'mtDesc' => $mtDesc,
					'vPhotoName' => $newName,
					'dPublishDate' => $dPublishDate
				));
			}

			$Progress = true;
		}

		if ($Progress) {
			$Return = array(
				'status' => 'success',
				'message' => 'Photo gallery telah di tambahkan',
				'data' => ''
			);
		} else {
			$Return = array(
				'status' => 'error',
				'message' => $this->Template->showMessage('error', 'Ops! Ada error pada database'),
				'data' => ''
			);
		}

		echo json_encode($Return);
	}

	function submit()
	{
		$idAlbum = $_POST['idalbum'];
		$vPhotoTitle = $_POST['vPhotoTitle'];
		$vPermalink = ($_POST['vPermalink'] == "") ? preg_replace("# #", "-", strtolower(preg_replace("/[^a-zA-Z0-9\-\s]/", "", $vPhotoTitle))) : preg_replace("# #", "-", strtolower(preg_replace("/[^a-zA-Z0-9\-\s]/", "", $_POST['vPermalink'])));
		$mtDesc = $_POST['mtDesc'];
		$vPhotoName = $this->Pile->simpanImage($_FILES['vPhotoName'], "photo_" . date("Yndhis") . rand(0, 9) . rand(0, 9) . rand(0, 9));
		$dPublishDate = date("Y-m-d");

		$Action = $_POST['action'];
		switch ($Action) {
			case "add":
				if ($vPhotoTitle != "") {
					if ($this->Module->Photo->addPhoto(array(
						'idAlbum' => $idAlbum,
						'vPhotoTitle' => (($vPhotoTitle) ? $vPhotoTitle : "No Title"),
						'vPermalink' => (($vPermalink) ? $vPermalink : "no-permalink" . rand(0, 9) . rand(0, 9)),
						'mtDesc' => (($mtDesc) ? $mtDesc : "No Description"),
						'vPhotoName' => $vPhotoName,
						'dPublishDate' => $dPublishDate
					))) {
						$Return = array(
							'status' => 'success',
							'message' => 'Photo gallery telah di tambahkan',
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
				if ($vPhotoTitle != "") {
					$detailPhoto = $this->Module->Photo->detailPhoto($this->Id);
					$UpdateField = array(
						'vPhotoTitle' => $vPhotoTitle,
						'vPermalink' => $vPermalink,
						'mtDesc' => $mtDesc,
						'dPublishDate' => $dPublishDate
					);

					if ($vPhotoName != "") {
						$this->Pile->deleteOldFile($detailPhoto['vPhotoName']);
						$UpdateField = array_merge($UpdateField, array('vPhotoName' => $vPhotoName));
					}

					if ($this->Module->Photo->updatePhoto($UpdateField, $this->Id)) {
						$Return = array(
							'status' => 'success',
							'message' => 'Data photo telah di perbaharui',
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
}
