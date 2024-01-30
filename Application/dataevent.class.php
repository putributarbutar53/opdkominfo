<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

// Include the main TCPDF library (search for installation path).
require_once('tcpdf/examples/tcpdf_include.php');

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {
	//Page header
	public function Header() {
		// get the current page break margin
		$bMargin = $this->getBreakMargin();
		// get current auto-page-break mode
		$auto_page_break = $this->AutoPageBreak;
		// disable auto-page-break
		$this->SetAutoPageBreak(false, 0);
		// set bacground image
		//$img_file = K_PATH_IMAGES.'image_demo.jpg';
		$img_file = $_GET['image'];

		//$this->Image($img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
		//$this->Image($img_file, 0, 0, 0, 0, '', '', '', true, 600, 'L', true, true, true);
		$this->Image($img_file, 0, 0, 0, 0, '', '', 'T', true, 600, 'L', false, false, 0, true, false, true);
		// restore auto-page-break status
		$this->SetAutoPageBreak($auto_page_break, $bMargin);
		// set the starting point for the page content
		$this->setPageMark();
	}
}

class dataevent extends Core
{
	var $Submit, $Action, $Do, $Id, $idStatus, $dirSertifikat;
	public function __construct()
	{
		parent::__construct();
		
		$this->LoadModule("Event");
		$this->LoadModule("Koperasi");
		$this->LoadModule("Paging");
		$this->Module->Paging->setPaging(10,10,"[prev]","[next]");

		//Load General Process
		include '../inc/general.php';
		include '../inc/common.php';	
		
		$this->dirSertifikat = $this->Config['sertifikat']['image'];
		$this->Pile->fileDestination = $this->dirSertifikat;
		$this->Template->assign("dirSertifikat", $this->dirSertifikat);

		$this->Template->assign("Signature", "event");

		ob_end_clean();

	}
		
	function main()
	{
		echo $this->Template->Show("event/event_index.html");
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
			$searchQuery = " AND (vSeminar like '%".$searchValue."%')";
		}
		
		//Total Records without Filtering
		$records = $this->Db->sql_query_array("select count(*) as total from cpevent");
		$totalRecords = $records['total'];
		
		//Total Record with filtering
		$records = $this->Db->sql_query_array("select count(*) as total from cpevent where id!='0'".$searchQuery);
		$totalRecordsWithFilter = $records['total'];
		
		//Fetch Records
		$orderBy = ($columnName=="")?" order by id desc":" order by ".$columnName." ".$columnSortOrder;
		$limitBy = ($row=="")?"":" limit ".$row.",".$rowperpage;
		
		$sqlQuery = "select * from cpevent where id!='0'".$searchQuery.$orderBy.$limitBy;
			
		$sqlRecord = $this->Db->sql_query($sqlQuery);
		while ($row = $this->Db->sql_array($sqlRecord))
		{			
			$getPeserta = $this->Module->Event->getpeserta($row['id'], $this->idMember);
			if ($row['iStatus']=="1")
			{
				if ($getPeserta['id']=="")
					$navButton = "----";
				else
				{
					if ($getPeserta['iStatus']=="1")
					{
						$navButton = "<span class=\"badge badge rounded-capsule badge-soft-success\">Peserta</span>";
						if ($getPeserta['iHadir']=="1")
						{
							$navButton .= "<span class=\"badge badge rounded-capsule badge-soft-success\">Hadir</span>";
						}
						else
						{
							$navButton .= "<span class=\"badge badge rounded-capsule badge-soft-danger\">Tidak Hadir</span>";								
						}
					}
					else
					{
						$navButton = "<span class=\"badge badge rounded-capsule badge-soft-warning\">Terdaftar</span>";
					}
				}
			}
			else
			{
				if ($getPeserta['id']=="")
					$navButton = "<a href=\"javascript:editdata(".$row['id'].")\" class=\"btn btn-info btn-sm\"><i class='fas fa-user-plus'></i> Daftar</a>";
				else
				{
					if ($getPeserta['iStatus']=="1")
					{
						$navButton = "<span class=\"badge badge rounded-capsule badge-soft-success\">Peserta</span>";
						if ($getPeserta['iHadir']=="1")
						{
							$navButton .= "<span class=\"badge badge rounded-capsule badge-soft-success\">Hadir</span>";
						}
						else
						{
							$navButton .= "<span class=\"badge badge rounded-capsule badge-soft-danger\">Tidak Hadir</span>";								
						}
					}
					else
					{
						$navButton = "<span class=\"badge badge rounded-capsule badge-soft-warning\">Terdaftar</span>";
						$navButton .= "&nbsp;&nbsp;<a href=\"javascript:batalpeserta(".$row['id'].")\"><i class='fas fa-trash fa-1x'></i></a>";
					}
				}
			}

			$dStart = date("d M y - H:i", strtotime($row['dTanggal']));
			$dEnd = date("d M y - H:i", strtotime($row['dEnd']));
			
			$_Status = ($row['iStatus']=="1")?"<span class=\"text-success\"> Selesai</span>":"<span class=\"text-secondary\"> Sedang Berlangsung</span>";
			
			$countPeserta = $this->Module->Event->countpeserta($row['id']);

			$data[] = array(
				"iStatus" => $_Status,
				"vSeminar" => "<a href=\"javascript:detaildata(".$row['id'].")\">".$row['vSeminar']."</a><br />".$row['tDesc'],
				"dTanggal" => $dStart." s/d<br />".$dEnd,
				"vLokasi" => $row['vLokasi'],
				"iPeserta" => number_format($countPeserta['total'])."/".number_format($row['iPeserta'])." orang",
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

	function submit()
	{
		$vSeminar = $_POST['vSeminar'];
		$tDesc = $_POST['tDesc'];
		$vPermalink = ($_POST['vPermalink']=="")?preg_replace("# #","-",strtolower(preg_replace("/[^a-zA-Z0-9\-\s]/", "", $vSeminar))):preg_replace("# #","-",strtolower(preg_replace("/[^a-zA-Z0-9\-\s]/", "", $_POST['vPermalink'])));

		$dTanggal = ($_POST['dTanggal'])?$_POST['dTanggal']:date("Y-m-d H:i:s");
		$dEnd = ($_POST['dEnd'])?$_POST['dEnd']:date("Y-m-d H:i:s");
		$vLokasi = $_POST['vLokasi'];
		$iStatus = $_POST['iStatus'];
		$iPeserta = $_POST['iPeserta'];
		//$tInclude = $_POST['tInclude'];
		
		$vDoc = $this->Pile->uploadDoc($_FILES['vDoc'],"doc_".date("Yndhis").rand(0,9).rand(0,9).rand(0,9));
		$Action = $_POST['action'];

		switch ($Action)
		{
			case "add":
				if (($vSeminar!="") AND ($tDesc!=""))
				{
					if ($this->Module->Event->add(array(
						'vSeminar' => $vSeminar,
						'tDesc' => $tDesc,
						'vPermalink' => $vPermalink,
						'dTanggal' => $dTanggal,
						'dEnd' => $dEnd,
						'vLokasi' => $vLokasi,
						'iStatus' => $iStatus,
						'iPeserta' => $iPeserta,
						'tInclude' => $tInclude,
						'vSertifikat' => $vSertifikat,
						'tPengaturan' => $tPengaturan,
						'vJenisPage' => $vJenisPage,
						'vDoc' => $vDoc)))
					{	
						$Return = array('status' => 'success',
						'message' => $this->Template->showMessage('success', 'Data event telah di tambahkan'), 
						'data' => $vDoc
						);
					}
					else
					{
						$Return = array('status' => 'error',
						'message' => $this->Template->showMessage('error', 'Ops! Ada error pada database'), 
						'data' => ''
						);
					}
				}
				else
				{
					$Return = array(
						'status' => 'error',
						'message' => $this->Template->showMessage('error', 'Data form isian tidak lengkap'), 
						'data' => ''
					);
				}
			break;
			case "update":
				if (($vSeminar!="") AND ($tDesc!=""))
				{
					$detailEvent = $this->Module->Event->detail($this->Id);
					$UpdateField = array('vSeminar' => $vSeminar,
					'tDesc' => $tDesc,
					'vPermalink' => $vPermalink,
					'dTanggal' => $dTanggal,
					'dEnd' => $dEnd,
					'vLokasi' => $vLokasi,
					'iStatus' => $iStatus,
					'iPeserta' => $iPeserta
					);

					if ($vDoc!="")
					{
						$this->Pile->deleteOldFile($detailEvent['vDoc']);
						$UpdateField = array_merge($UpdateField,array('vDoc' => $vDoc));
					}

					// $_Sertifikat = array();
					// if ($vSertifikat!="")
					// {
					// 	$this->Pile->deleteOldFile($detailEvent['vSertifikat']);
					// 	$_Sertifikat = array('vSertifikat' => $vSertifikat);
					// }

					if ($this->Module->Event->update($UpdateField,$this->Id))
						{
							$Return = array('status' => 'success',
							'message' => $this->Template->showMessage('success', 'Data event telah di perbaharui'), 
							'data' => $vDoc
							);
						}
						else
						{
							$Return = array('status' => 'error',
							'message' => $this->Template->showMessage('error', 'Ops! Ada error pada database'), 
							'data' => ''
							);
						}
				}
				else
				{
					$Return = array('status' => 'error',
					'message' => $this->Template->showMessage('error', 'Ops! Data form isian tidak lengkap'), 
					'data' => ''
					);
				}
			break;
			case "updatesertifikat":

				$idEvent = $_POST['idEvent'];
				$vSertifikat = $this->Pile->simpanImage($_FILES['vSertifikat'],"sertifikat_".date("Yndhis").rand(0,9).rand(0,9).rand(0,9));
				$tPengaturan = $_POST['tPengaturan'];
				$vJenisPage = $_POST['vJenisPage'];

				if (($tPengaturan!="") AND ($vJenisPage!="") AND ($idEvent!=""))
				{
					$detailEvent = $this->Module->Event->detail($idEvent);
					$UpdateField = array('tPengaturan' => $tPengaturan,
					'vJenisPage' => $vJenisPage);

					if ($vSertifikat!="")
					{
						$this->Pile->deleteOldFile($detailEvent['vSertifikat']);
						$UpdateField = array_merge($UpdateField,array('vSertifikat' => $vSertifikat));
					}

					if ($this->Module->Event->update($UpdateField,$idEvent))
						{
							$Return = array('status' => 'success',
							'message' => $this->Template->showMessage('success', 'Data sertifikat telah di perbaharui'), 
							'data' => (($vSertifikat!="")?$this->Config['base']['url'].$this->dirSertifikat.$vSertifikat:"")
							);
						}
						else
						{
							$Return = array('status' => 'error',
							'message' => $this->Template->showMessage('error', 'Ops! Ada error pada database'), 
							'data' => ''
							);
						}
				}
				else
				{
					$Return = array('status' => 'error',
					'message' => $this->Template->showMessage('error', 'Ops! Data form isian tidak lengkap'), 
					'data' => ''
					);
				}
			break;
		}

		echo json_encode($Return);
	}

	function detail()
	{
		$this->Template->assign("Detail", $this->Module->Event->detail($this->Id));
		echo $this->Template->ShowAdmin("event/event_detail.html");
	}

	function peserta()
	{
		$this->Template->assign("listPeserta", $this->Module->Koperasi->listpengurus());
		$this->Template->assign("Detail", $this->Module->Event->detail($this->Id));
		echo $this->Template->ShowAdmin("event/event_peserta.html");
	}

	function edit()
	{
		$this->Template->assign("Do", "edit");
		$this->Template->assign("Detail", $this->Module->Event->detail($this->Id));
		echo $this->Template->ShowAdmin("event/event_edit.html");
	}

	function delete()
	{
		if ($this->Id!="")
		{
			$detailEvent = $this->Module->Event->detail($this->Id);

			if ($detailEvent['vSertifikat']!="") 
				$this->Pile->deleteOldFile($detailEvent['vSertifikat']);

			if ($detailEvent['vDoc']!="") 
				$this->Pile->deleteOldFile($detailEvent['vDoc']);

			$this->Module->Event->deleteallpeserta($this->Id);

			if ($this->Module->Event->delete($this->Id))
			{
				$Return = array('status' => 'success',
				'message' => $this->Template->showMessage('success', 'Data event telah di hapus'), 
				'data' => ''
				);
			}
		}
		else
		{
			$Return = array('status' => 'error',
			'message' => $this->Template->showMessage('error', 'Ops! ID event tidak valid'), 
			'data' => ''
			);			
		}

		echo json_encode($Return);

	}

	function status()
	{
		if ($this->Id!="")
		{
			$iStatus = $_GET['status'];
			if ($this->Module->Event->status($iStatus, $this->Id))
			{
				$Return = array('status' => 'success',
				'message' => $this->Template->showMessage('success', 'Status data event sudah di perbaharui'), 
				'data' => ''
				);
			}
		}
		else
		{
			$Return = array('status' => 'error',
			'message' => $this->Template->showMessage('error', 'Ops! ID event tidak valid'), 
			'data' => ''
			);			
		}

		echo json_encode($Return);
	}

	function printsertifikat()
	{
		$Detail = $this->Module->Event->detail($this->Id);
		$jenisPage = ($Detail['vJenisPage']=="")?"L":$Detail['vJenisPage'];

		$idMember = $_GET['idmember'];
		$detailMember = $this->Module->Koperasi->detailpengurus($idMember);

		$defaultName = ($detailMember['vNama']=="")?"Testing Name":$detailMember['vNama'];
		$Change = array('#name#' => $defaultName);
		$tPengaturan = strtr($Detail['tPengaturan'], $Change);

		// create new PDF document
		$pdf = new MYPDF($jenisPage, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Dinas Koperasi dan UMKN Deliserdang');
		$pdf->SetTitle('E-Sertifikat');
		$pdf->SetSubject('E-Sertifikat');
		$pdf->SetKeywords('umkm, deliserdang');

		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(0, 0, 0, true);
		$pdf->SetHeaderMargin(0);
		$pdf->SetFooterMargin(0);

		// remove default footer
		$pdf->setPrintFooter(false);

		// set auto page breaks
		$pdf->SetAutoPageBreak(false);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set font
		$pdf->SetFont('times', '', 48);
		$pdf->SetXY(400, 200);

		// add a page
		$pdf->AddPage();

		// Print a text
		//$html = '<br /><br /><br /><br /><br /><div style="font-family:helvetica;font-weight:bold;font-size:40pt;width:500px;text-align:center;">'.$detailMember['vNama'].'</div>';
		$pdf->writeHTML($tPengaturan, true, false, true, false, '');

		//Close and output PDF document
		$pdf->Output('sertifikat_print.pdf', 'I');
	}

	function loadpeserta()
	{
		$idSeminar = $this->uri(4);

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
			$searchQuery = " AND (b.vNama like '%".$searchValue."%')";
		}
		
		//Total Records without Filtering
		$records = $this->Db->sql_query_array("select count(*) as total from cpevent_peserta WHERE idSeminar='".$idSeminar."'");
		$totalRecords = $records['total'];
		
		//Total Record with filtering
		$records = $this->Db->sql_query_array("select count(*) as total from cpevent_peserta a LEFT JOIN cpkoperasi_pengurus b ON a.idMember = b.id where a.id!='0' AND a.idSeminar='".$idSeminar."'".$searchQuery);
		$totalRecordsWithFilter = $records['total'];
		
		//Fetch Records
		$orderBy = ($columnName=="")?" order by a.id desc":" order by ".$columnName." ".$columnSortOrder;
		$limitBy = ($row=="")?"":" limit ".$row.",".$rowperpage;
		
		$sqlQuery = "select a.id AS id, a.idSeminar AS idSeminar, b.id AS idPeserta, b.idKoperasi AS idKoperasi, b.vNama AS vNama, a.iStatus AS iStatus, a.iHadir AS iHadir from cpevent_peserta a JOIN cpkoperasi_pengurus b ON a.idMember = b.id where b.id!='0' AND a.idSeminar='".$idSeminar."'".$searchQuery.$orderBy.$limitBy;
			
		$sqlRecord = $this->Db->sql_query($sqlQuery);
		while ($row = $this->Db->sql_array($sqlRecord))
		{
			$detailKoperasi = $this->Module->Koperasi->detail($row['idKoperasi']);
			$navButton = "<a href=\"javascript:deletepeserta(".$row['id'].")\"><i class='fas fa-trash-alt'></i></a>";
			$_Status = ($row['iStatus']=="1")?"<span class=\"text-success\"><a href=\"javascript:statuspeserta('0',".$row['id'].")\" class=\"text-success\"><i class=\"fas fa-toggle-on\"></i></a> Peserta":"<span class=\"text-secondary\"><a href=\"javascript:statuspeserta('1',".$row['id'].")\" class=\"text-secondary\"><i class=\"fas fa-toggle-off\"></i></a> Terdaftar</span>";
			$_Hadir = ($row['iHadir']=="1")?"<span class=\"text-success\"><a href=\"javascript:absenpeserta('0',".$row['id'].")\" class=\"text-success\"><i class=\"fas fa-toggle-on\"></i></a> Hadir":"<span class=\"text-secondary\"><a href=\"javascript:absenpeserta('1',".$row['id'].")\" class=\"text-secondary\"><i class=\"fas fa-toggle-off\"></i></a> Tidak Hadir</span>";

			$data[] = array(
				'vNama' => $row['vNama']." / ".$detailKoperasi['vKoperasi'],
				'iStatus' => $_Status,
				'iHadir' => (($row['iStatus']=="1")?$_Hadir:"----"),
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

	function submitpeserta()
	{
		$idEvent = ($_POST['idEvent'])?$_POST['idEvent']:0;

		$idMember = ($_POST['idMember'])?$_POST['idMember']:0;
		$iStatus = ($_POST['iStatus'])?$_POST['iStatus']:0;
		$iHadir = "0";

		$Action = $_POST['action'];

		switch ($Action)
		{
			case "addpeserta":

				if (($idMember!="") AND ($idEvent!=""))
				{
					if ($this->Module->Event->checkpeserta($idEvent, $idMember)==true)
					{
						if ($this->Module->Event->addpeserta(array(
							'idSeminar' => $idEvent,
							'idMember' => $idMember,
							'iStatus' => $iStatus,
							'iHadir' => $iHadir)))
						{	
							$Return = array('status' => 'success',
							'message' => $this->Template->showMessage('success', 'Data peserta telah di tambahkan'), 
							'data' => ''
							);
						}
						else
						{
							$Return = array('status' => 'error',
							'message' => $this->Template->showMessage('error', 'Ops! Ada error pada database'), 
							'data' => ''
							);
						}
					}
					else
					{
						$Return = array('status' => 'error',
						'message' => $this->Template->showMessage('error', 'Ops! Data penerima sudah di input sebelumnya'), 
						'data' => ''
						);
					}
				}
				else
				{
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

	function deletepeserta()
	{
		if ($this->Id!="")
		{
			if ($this->Module->Event->deletepeserta($this->Id))
			{
				$Return = array('status' => 'success',
				'message' => $this->Template->showMessage('success', 'Data peserta telah di hapus'), 
				'data' => ''
				);
			}
		}
		else
		{
			$Return = array('status' => 'error',
			'message' => $this->Template->showMessage('error', 'Ops! ID peserta tidak valid'), 
			'data' => ''
			);			
		}

		echo json_encode($Return);
	}

	function statuspeserta()
	{
		if ($this->Id!="")
		{
			$iStatus = $_GET['status'];
			if ($this->Module->Event->statuspeserta($iStatus, $this->Id))
			{
				$Return = array('status' => 'success',
				'message' => $this->Template->showMessage('success', 'Status peserta sudah di perbaharui'), 
				'data' => ''
				);
			}
		}
		else
		{
			$Return = array('status' => 'error',
			'message' => $this->Template->showMessage('error', 'Ops! ID peserta tidak valid'), 
			'data' => ''
			);			
		}

		echo json_encode($Return);
	}

	function absenpeserta()
	{
		if ($this->Id!="")
		{
			$iStatus = $_GET['status'];
			if ($this->Module->Event->absenpeserta($iStatus, $this->Id))
			{
				$Return = array('status' => 'success',
				'message' => $this->Template->showMessage('success', 'Status kehadiran peserta sudah di perbaharui'), 
				'data' => ''
				);
			}
		}
		else
		{
			$Return = array('status' => 'error',
			'message' => $this->Template->showMessage('error', 'Ops! ID peserta tidak valid'), 
			'data' => ''
			);			
		}

		echo json_encode($Return);
	}

}

?>