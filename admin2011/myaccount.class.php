<?php if (!defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class myaccount extends Core
{
	var $Submit, $Action, $Id, $DetailAdmin, $Username, $Do, $Show;

	public function __construct()
	{
		parent::__construct();

		//Load General Process
		include '../inc/general_admin.php';
		$this->LoadModule("Document");
		$this->LoadModule("Paging");
		$this->Module->Paging->setPaging(20, 10, "&laquo; Prev", "Next &raquo;");
	}

	//Search Website
	function main()
	{
		echo $this->Template->ShowAdmin("account/account_index.html");
	}

	function changepassword()
	{
		if ($this->Submit) {
			switch ($this->Action) {
				case "changepass":
					$vName = $_POST['vName'];
					$oPassword = $_POST['oPassword'];
					$nPassword = $_POST['nPassword'];
					$rPassword = $_POST['rPassword'];
					//$vName = $_POST['vName'];

					if (($oPassword != "") and ($nPassword != "") and ($rPassword != "")) {
						if ($nPassword == $rPassword) {
							if ($this->Module->Auth->checkPassword($this->Username, $oPassword)) {
								if ($this->Module->Auth->updatePassword($this->Username, $nPassword)) {
									$Return = array(
										'status' => 'success',
										'message' => $this->Template->showMessage('success', 'Password anda telah diperbaharui'),
										'data' => ''
									);
								}
							} else {
								$Return = array(
									'status' => 'error',
									'message' => $this->Template->showMessage('error', 'Ops! Password lama anda tidak benar'),
									'data' => ''
								);
							}
						} else {
							$Return = array(
								'status' => 'error',
								'message' => $this->Template->showMessage('error', 'Ops! Password tidak diulang dengan baik'),
								'data' => ''
							);
						}
					} else {
						$Return = array(
							'status' => 'error',
							'message' => $this->Template->showMessage('error', 'Ops! Data form isian password tidak boleh kosong'),
							'data' => ''
						);
					}

					// if (($vName!="") AND ($vName!=$this->DetailAdmin['vName']))
					// {
					// 	if ($this->Module->Auth->updateName($vName, $this->Username))
					// 	{
					// 		$DetailAdmin = $this->Module->Auth->detailAdmin(NULL,$this->Username);
					// 		$this->Template->assign("DetailAdmin", $DetailAdmin);
					// 		$this->Template->reportMessage("success", "Nama anda telah diperbaharui");
					// 	}
					// }
					//-------------------------------
					break;
			}
		}

		echo json_encode($Return);
	}

	function admin()
	{
		if ($this->DetailAdmin['vUsername'] == "admin")
			echo $this->Template->ShowAdmin("account/useradmin_index.html");
		else
			echo $this->Template->ShowAdmin("dashboard.html");
	}

	function loadadmin()
	{
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
			$searchQuery = " AND ((vUsername like '%" . $searchValue . "%') OR (vName like '%" . $searchValue . "%') OR (vEmail like '%" . $searchValue . "%'))";
		}

		//Total Records without Filtering
		$records = $this->Db->sql_query_array("select count(*) as total from cpadmin");
		$totalRecords = $records['total'];

		//Total Record with filtering
		$records = $this->Db->sql_query_array("select count(*) as total from cpadmin where id!='0'" . $searchQuery);
		$totalRecordsWithFilter = $records['total'];

		//Fetch Records
		$orderBy = ($columnName == "") ? " order by id desc" : " order by " . $columnName . " " . $columnSortOrder;
		$limitBy = ($row == "") ? "" : " limit " . $row . "," . $rowperpage;

		$sqlQuery = "select * from cpadmin where id!='0'" . $searchQuery . $orderBy . $limitBy;

		$sqlRecord = $this->Db->sql_query($sqlQuery);
		while ($row = $this->Db->sql_array($sqlRecord)) {
			$RoleButton = ($row['vUsername'] != "admin") ? "<a href=\"javascript:detaildata(" . $row['id'] . ")\"><i class='fas fa-bars'></i></a>&nbsp;" : "";
			$navButton = $RoleButton . "<a href=\"javascript:editdata(" . $row['id'] . ")\"><i class='fas fa-pen-square'></i></a>&nbsp;&nbsp;<a href=\"javascript:deletedata(" . $row['id'] . ")\"><i class='fas fa-trash-alt'></i></a>";

			$dLastlogin = date("d M y - H:i:s", strtotime($row['dLastlogin']));
			$data[] = array(
				"vUsername" => $row['vUsername'],
				"vEmail" => $row['vEmail'],
				"vName" => $row['vName'],
				"dLastLogin" => $dLastlogin,
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

	function submit()
	{
		$vUsername = $_POST['vUsername'];
		$cPassword = $_POST['cPassword'];
		$vEmail = $_POST['vEmail'];
		$vName = $_POST['vName'];
		$isLogin = ($_POST['isLogin'] == 'yes') ? 1 : 0;

		// Generate a random salt
		$salt = password_hash(random_bytes(16), PASSWORD_BCRYPT);
		// Hash the password with bcrypt
		$nPassword = password_hash($cPassword, PASSWORD_BCRYPT, ['salt' => $salt, 'cost' => 12]);

		$Action = $_POST['action'];
		switch ($Action) {
			case "add":
				if (($vUsername != "") and ($vName != "")) {
					if ($this->Module->Auth->adduser(array(
						'vUsername' => $vUsername,
						'cPassword' => $nPassword,
						'vEmail' => $vEmail,
						'dLastLogin' => date("Y-m-d H:i:s"),
						'vAuth' => '',
						'vDir' => '',
						'isLogin' => $isLogin,
						'vName' => $vName
					))) {
						$Return = array(
							'status' => 'success',
							'message' => $this->Template->showMessage('success', 'Data administrator telah di tambahkan'),
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
				if (($vUsername != "") and ($vName != "")) {
					$UpdateField = array(
						'vUsername' => $vUsername,
						'vEmail' => $vEmail,
						'vName' => $vName
					);

					if ($cPassword != '') $UpdateField['cPassword'] = $nPassword;
					if ($this->Module->Auth->updateUser($UpdateField, $this->Id)) {
						if ($cPassword != "") {
							$this->Db->sql_query("UPDATE cpadmin SET cPassword='" . password_hash($nPassword, PASSWORD_BCRYPT) . "' WHERE id='" . $this->Id . "'");
						}
						$this->Db->sql_query("UPDATE cpadmin SET isLogin='" . $isLogin . "' WHERE id='" . $this->Id . "'");
						$Return = array(
							'status' => 'success',
							'message' => $this->Template->showMessage('success', 'Data administrator telah di perbaharui'),
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

	function updaterole()
	{
		$vAuth = array();
		if ($_POST['page'] == "yes")
			$vAuth = array_merge($vAuth, array('page'));

		if ($_POST['content'] == "yes")
			$vAuth = array_merge($vAuth, array('content'));

		if ($_POST['banner'] == "yes")
			$vAuth = array_merge($vAuth, array('banner'));

		if ($_POST['album'] == "yes")
			$vAuth = array_merge($vAuth, array('album'));

		if ($_POST['filemanager'] == "yes")
			$vAuth = array_merge($vAuth, array('filemanager'));

		if ($_POST['document'] == "yes")
			$vAuth = array_merge($vAuth, array('document'));

		$vDir = array();
		$listPage = $this->Module->Page->listCategory();
		for ($i = 0; $i < count($listPage); $i++) {
			if ($_POST['page_' . $listPage[$i]['Item']['id']] == "yes")
				$vDir = array_merge($vDir, array('page_' . $listPage[$i]['Item']['id']));
		}

		$listContent = $this->Module->Content->listCategory();
		for ($i = 0; $i < count($listContent); $i++) {
			if ($_POST['content_' . $listContent[$i]['Item']['id']] == "yes")
				$vDir = array_merge($vDir, array('content_' . $listContent[$i]['Item']['id']));
		}

		$listBanner = $this->Module->Banner->listCategory();
		for ($i = 0; $i < count($listBanner); $i++) {
			if ($_POST['banner_' . $listBanner[$i]['Item']['id']] == "yes")
				$vDir = array_merge($vDir, array('banner_' . $listBanner[$i]['Item']['id']));
		}

		$listAlbum = $this->Module->Photo->listAlbum();
		for ($i = 0; $i < count($listAlbum); $i++) {
			if ($_POST['photo_' . $listAlbum[$i]['Item']['id']] == "yes")
				$vDir = array_merge($vDir, array('photo_' . $listAlbum[$i]['Item']['id']));
		}

		$listDocument = $this->Module->Document->listCategory();
		for ($i = 0; $i < count($listDocument); $i++) {
			if ($_POST['doc_' . $listDocument[$i]['Item']['id']] == "yes")
				$vDir = array_merge($vDir, array('doc_' . $listDocument[$i]['Item']['id']));
		}

		$UpdateField = array(
			'vAuth' => json_encode($vAuth),
			'vDir' => json_encode($vDir)
		);

		if ($this->Module->Auth->updateRole($UpdateField, $this->Id)) {
			$Return = array(
				'status' => 'success',
				'message' => $this->Template->showMessage('success', 'Data role telah di perbaharui'),
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

	function detail()
	{
		$this->Template->assign("listPage", $this->Module->Page->listCategory());
		$this->Template->assign("listContent", $this->Module->Content->listCategory());
		$this->Template->assign("listBanner", $this->Module->Banner->listCategory());
		$this->Template->assign("listAlbum", $this->Module->Photo->listAlbum());
		$this->Template->assign("listDocument", $this->Module->Document->listCategory());

		$Detail = $this->Module->Auth->detailAdmin($this->Id);
		$this->Template->assign("Detail", $Detail);

		$this->Template->assign("vAuth", json_decode($Detail['vAuth'], true));
		$this->Template->assign("vDir", json_decode($Detail['vDir'], true));

		echo $this->Template->ShowAdmin("account/useradmin_detail.html");
	}

	function edit()
	{
		$this->Template->assign("Detail", $this->Module->Auth->detailAdmin($this->Id));
		echo $this->Template->ShowAdmin("account/useradmin_edit.html");
	}

	function delete()
	{
		if ($this->Id != "") {
			if ($this->Module->Auth->deleteUser($this->Id)) {
				$Return = array(
					'status' => 'success',
					'message' => $this->Template->showMessage('success', 'Data admin telah di hapus'),
					'data' => ''
				);
			}
		} else {
			$Return = array(
				'status' => 'error',
				'message' => $this->Template->showMessage('error', 'Ops! ID admin tidak valid'),
				'data' => ''
			);
		}

		echo json_encode($Return);
	}
}
