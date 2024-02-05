<?php if (!defined('ONPATH'))
	exit('No direct script access allowed'); //Mencegah akses langsung ke class

class mymessage extends Core
{
	var $Submit, $Action, $Id;
	public function __construct()
	{
		parent::__construct();
		ob_clean();

		$this->LoadModule("Date");
		$this->LoadModule("Paging");
		$this->LoadModule("Message");
		//Load General Process
		include '../inc/general_admin.php';
		$this->Module->Paging->setPaging(21, 5);

		$this->Template->assign("dirProducts", $this->Config['upload']['products']);
		$this->Template->assign("Signature", "message");
	}

	function main()
	{
		echo $this->Template->ShowAdmin("message/index.html");
	}
	function loaddata()
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
			$searchQuery = " AND ((name like '%" . $searchValue . "%') OR (email like '%" . $searchValue . "%') OR (phone like '%" . $searchValue . "%') OR (subject like '%" . $searchValue . "%') OR (pesan like '%" . $searchValue . "%') OR (created_at like '%" . $searchValue . "%'))";
		}

		//Total Records without Filtering
		$records = $this->Db->sql_query_array("select count(*) as total from cpmessage WHERE id!='0'");
		$totalRecords = $records['total'];

		//Total Record with filtering
		$records = $this->Db->sql_query_array("select count(*) as total from cpmessage where id!='0'" . $searchQuery . "");
		$totalRecordsWithFilter = $records['total'];

		//Fetch Records
		$orderBy = ($columnName == "") ? " order by id desc" : " order by " . $columnName . " " . $columnSortOrder;
		$limitBy = ($row == "") ? "" : " limit " . $row . "," . $rowperpage;

		$sqlQuery = "select * from cpmessage where id!='0'" . $searchQuery . $orderBy . $limitBy;

		$sqlRecord = $this->Db->sql_query($sqlQuery);
		while ($row = $this->Db->sql_array($sqlRecord)) {
			$navButton = "<a href=\"javascript:deletedata(" . $row['id'] . ")\"><i class='fas fa-trash-alt'></i></a>";

			$data[] = array(
				"created_at" => date('d/m/Y H:i', strtotime($row['created_at'])),
				"name" => $row['name'],
				"email" => $row['email'],
				"phone" => $row['phone'],
				"subject" => $row['subject'],
				"message" => $row['message'],
				"navButton" => $navButton,
			);
		}

		//Response
		$response = array(
			"draw" => intval($draw),
			"iTotalRecords" => $totalRecords,
			"iTotalDisplayRecords" => $totalRecordsWithFilter,
			"aaData" => (($data) ? $data : array())
		);

		echo json_encode($response);
	}
	function add()
	{
	}
	function edit()
	{
	}
	function delete()
	{
		if ($this->Id != "") {
			if ($this->Module->Message->delete($this->Id)) {
				$Return = array(
					'status' => 'success',
					'message' => $this->Template->showMessage('success', 'Data pesan telah di hapus'),
					'data' => ''
				);
			}
		} else {
			$Return = array(
				'status' => 'error',
				'message' => $this->Template->showMessage('error', 'Ops! ID pesan tidak valid'),
				'data' => ''
			);
		}

		echo json_encode($Return);
	}
	function submit()
	{
	}
}