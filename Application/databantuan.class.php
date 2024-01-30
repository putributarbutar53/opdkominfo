<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class databantuan extends Core
{
	var $Submit, $Action, $Do, $Id, $idStatus;
	public function __construct()
	{
		parent::__construct();
		
		$this->LoadModule("Bantuan");
		$this->LoadModule("Event");
		$this->LoadModule("Koperasi");
		$this->LoadModule("Paging");
		$this->Module->Paging->setPaging(10,10,"[prev]","[next]");

		$this->Pile->fileDestination = $this->Config['sertifikat']['image'];
		$this->Template->assign("dirSertifikat", $this->Config['sertifikat']['image']);

		$this->Template->assign("Signature", "bantuan");
		
		//Load General Process
		include '../inc/general.php';
		include '../inc/common.php';		
	}
		
	function main()
	{
		$this->Template->assign("listEvent", $this->Module->Event->listall());
		echo $this->Template->Show("bantuan/bantuan_index.html");
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
		if ($searchValue != '')
		{
			$searchQuery = " AND (tKet like '%".$searchValue."%')";
		}
		
		//Total Records without Filtering
		$records = $this->Db->sql_query_array("select count(*) as total from cpbantuan");
		$totalRecords = $records['total'];
		
		//Total Record with filtering
		$records = $this->Db->sql_query_array("select count(*) as total from cpbantuan where id!='0'".$searchQuery);
		$totalRecordsWithFilter = $records['total'];
		
		//Fetch Records
		$orderBy = ($columnName=="")?" order by id desc":" order by ".$columnName." ".$columnSortOrder;
		$limitBy = ($row=="")?"":" limit ".$row.",".$rowperpage;
		
		$sqlQuery = "select * from cpbantuan where id!='0'".$searchQuery.$orderBy.$limitBy;
			
		$sqlRecord = $this->Db->sql_query($sqlQuery);
		while ($row = $this->Db->sql_array($sqlRecord))
		{
			$checkStatus = $this->Module->Bantuan->getpeserta($row['id'], $this->idMember);
			if ($row['iStatus']=="1")
			{
				if ($checkStatus['id']=="")
				{
					$navButton = "-----";
				}
				else
				{
					if ($checkStatus['iStatus']=="1")
						$navButton = "<span class=\"text-success\">Peserta</span>";
					else
						$navButton = "<span class=\"text-secondary\">Terdaftar</span>";
				}
			}
			else
			{
				if ($checkStatus['id']=="")
				{
					$navButton = "<a class=\"btn btn-warning btn-sm\" href=\"javascript:daftarpeserta(".$row['id'].")\"><i class='fas fa-user-plus'></i> Daftar</a>";
				}
				else
				{
					if ($checkStatus['iStatus']=="1")
						$navButton = "<span class=\"badge badge rounded-capsule badge-soft-success\">Peserta</span>";
					else
					{
						$navButton = "<span class=\"badge badge rounded-capsule badge-soft-warning\">Terdaftar</span>";
						$navButton .= "&nbsp;&nbsp;<a href=\"javascript:batalpeserta(".$row['id'].")\"><i class='fas fa-trash fa-1x'></i></a>";
					}
				}
			}

			$dStart = date("d M y", strtotime($row['dTanggal']));
			$Seminar = $this->Module->Event->detail($row['idSeminar']);
			$_Status = ($row['iStatus']=="1")?"<span class=\"text-success\">Selesai</span>":"<span class=\"text-secondary\">Progress</span>";
			$Penerima = $this->Module->Bantuan->countpeserta($row['id']);

			$data[] = array(
				"iStatus" => $_Status,
				"dTanggal" => $dStart,
				"tKet" => $row['tKet'],
				"vSeminar" => $this->Template->no_value($Seminar['vSeminar']),
				"vSumber" => $this->Template->no_value($row['vSumber']),
				"iPenerima" => number_format($Penerima['total'])." Koperasi",
				'navButton' => $navButton,
			);
		}
		
		//Response
		$response = array(
			"draw" => intval($draw),
			"iTotalRecords" => $totalRecordsWithFilter,
			"iTotalDisplayRecords" => $totalRecords,
			"aaData" => (($data)?$data:array())
		);
		
		echo json_encode($response);
	}

	function daftarpeserta()
	{
		$idBantuan = filter_var($_GET['idbantuan'], FILTER_SANITIZE_STRING);
		if (($idBantuan!="") AND ($this->idMember!=""))
		{
			if ($this->Module->Bantuan->daftarpeserta(array(
				'idBantuan' => $idBantuan, 
				'idKoperasi' => $this->idMember,
				'iStatus' => '0'
			)))
			{
				$Return = array('status' => 'success',
				'message' => $this->Template->showMessage('success', 'Anda sudah di daftarkan dalam program bantuan ini'), 
				'data' => ''
				);
			}
		}
		else
		{
			$Return = array('status' => 'error',
			'message' => $this->Template->showMessage('error', 'Ops! ID peserta atau bantuan tidak valid'), 
			'data' => ''
			);			
		}

		echo json_encode($Return);
	}

	function batalpeserta()
	{
		$idBantuan = filter_var($_GET['idbantuan'],  FILTER_SANITIZE_STRING);
		if (($idBantuan!="") AND ($this->idMember!=""))
		{
			if ($this->Module->Bantuan->batalpeserta($idBantuan, $this->idMember))
			{
				$Return = array('status' => 'success',
				'message' => $this->Template->showMessage('success', 'Pendaftaran anda sudah di batalkan'), 
				'data' => ''
				);
			}
		}
		else
		{
			$Return = array('status' => 'error',
			'message' => $this->Template->showMessage('error', 'Ops! ID peserta atau bantuan tidak valid'), 
			'data' => ''
			);			
		}

		echo json_encode($Return);
	}

}

?>