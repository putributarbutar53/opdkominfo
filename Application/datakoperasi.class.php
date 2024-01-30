<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class datakoperasi extends Core
{
	var $Submit, $Action, $Do, $Id, $idStatus;
	public function __construct()
	{
		parent::__construct();

		$this->LoadModule("Koperasi");
		$this->LoadModule("Paging");
		$this->Module->Paging->setPaging(10,10,"[prev]","[next]");

		$this->Template->assign("Signature", "koperasi");

		//Load General Process
		include '../inc/general.php';
		include '../inc/common.php';
	}
		
	function main()
	{
		$this->Template->assign("Detail", $this->detailMember);
		echo $this->Template->Show("koperasi/koperasi_index.html");
	}

	function getdetail()
	{
		$this->Template->assign("Detail", $this->detailMember);
		$getTitle = $this->Template->Show("koperasi/koperasi_title.html");
		$getDetail = $this->Template->Show("koperasi/koperasi_detail.html");
		$json_data = array('title' => $getTitle, 'detail' => $getDetail, 'test' => 'test');
		echo json_encode($json_data);
	}

	function loadpengurus()
	{
		$idKoperasi = $this->uri(3);

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
			$searchQuery = " AND ((vNama like '%".$searchValue."%') OR (vNik like '%".$searchValue."%') OR (vTelp like '%".$searchValue."%'))";
		}
		
		//Total Records without Filtering
		$records = $this->Db->sql_query_array("select count(*) as total from cpkoperasi_pengurus where idKoperasi='".$idKoperasi."'");
		$totalRecords = $records['total'];
		
		//Total Record with filtering
		$records = $this->Db->sql_query_array("select count(*) as total from cpkoperasi_pengurus where id!='0' AND idKoperasi='".$idKoperasi."'".$searchQuery);
		$totalRecordsWithFilter = $records['total'];
		
		//Fetch Records
		$orderBy = ($columnName=="")?" order by id desc":" order by ".$columnName." ".$columnSortOrder;
		$limitBy = ($row=="")?"":" limit ".$row.",".$rowperpage;
		
		$sqlQuery = "select * from cpkoperasi_pengurus where id!='0' AND idKoperasi='".$idKoperasi."'".$searchQuery.$orderBy.$limitBy;
			
		$sqlRecord = $this->Db->sql_query($sqlQuery);
		while ($row = $this->Db->sql_array($sqlRecord))
		{
			$navButton = "<a href=\"javascript:editpengurus(".$row['id'].")\"><i class='fas fa-pen-square'></i></a>&nbsp;&nbsp;
			<a href=\"javascript:deletepengurus(".$row['id'].")\"><i class='fas fa-trash-alt'></i></a>";

			$data[] = array(
				"vNik" => $row['vNik'],
				"vNama" => $row['vNama'],
				"vJabatan" => ucfirst($row['vJabatan']),
				"vTelp" => $row['vTelp'],
				"vPendidikan" => $row['vPendidikan'],
				"vNPWP" => $row['vNPWP'],
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
		$idDiskop = $_POST['idDiskop'];
		$vKoperasi = $_POST['vKoperasi'];
		$tAlamat = $_POST['tAlamat'];
		$vKelurahan = $_POST['vKelurahan'];
		$vKecamatan = $_POST['vKecamatan'];
		$vKabupaten = $_POST['vKabupaten'];
		$vProvinsi = $_POST['vProvinsi'];
		$vTelp = $_POST['vTelp'];
		$vEmail = $_POST['vEmail'];
		$vWebsite = $_POST['vWebsite'];
		$vFaks = $_POST['vFaks'];
		$dRAT = ($_POST['dRAT'])?$_POST['dRAT']:'0000-00-00';
		
		$Action = $_POST['action'];
		switch ($Action)
		{
			case "update":
				if ($this->Module->Koperasi->checkMobile($vTelp, $this->idMember))
				{
					if ($this->Module->Koperasi->checkEmail($vEmail, $this->idMember))
					{
						if ($this->Module->Koperasi->update(array(
							'idDiskop' => $idDiskop,
							'vKoperasi' => $vKoperasi,
							'tAlamat' => $tAlamat,
							'vKelurahan' => $vKelurahan,
							'vKecamatan' => $vKecamatan,
							'vKabupaten' => $vKabupaten,
							'vProvinsi' => $vProvinsi,
							'vTelp' => $vTelp,
							'vEmail' => $vEmail,
							'vWebsite' => $vWebsite,
							'vFaks' => $vFaks,
							'dRAT' => $dRAT),$this->idMember))
							{
								$Return = array('status' => 'success',
								'message' => $this->Template->showMessage('success', 'Data koperasi telah di perbaharui'), 
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
						'message' => $this->Template->showMessage('error', 'Ops! Email sudah di miliki oleh koperasi lain'), 
						'data' => ''
						);
					}
				}
				else
				{
					$Return = array('status' => 'error',
					'message' => $this->Template->showMessage('error', 'Ops! Nomor telepon sudah di miliki oleh koperasi lain'), 
					'data' => ''
					);
				}
			break;
		}

		echo json_encode($Return);
	}

	function submitpengurus()
	{
		$idKoperasi = ($_POST['idKoperasi'])?$_POST['idKoperasi']:0;

		$vJabatan = $_POST['vJabatan'];
		$vNama = $_POST['vNama'];
		$vTelp = $_POST['vTelp'];
		$vNik = $_POST['vNik'];
		$vPendidikan = $_POST['vPendidikan'];
		$vNPWP = $_POST['vNPWP'];

		$Action = $_POST['action'];
		$idPengurus = $_POST['idPengurus'];

		switch ($Action)
		{
			case "addpengurus":
				if (($vNama!="") AND ($vTelp!=""))
				{
					if ($idPengurus=="")
					{
						if ($this->Module->Koperasi->checkpengurus($idKoperasi, $vJabatan, $vNama, $vNik)==true)
						{
							if ($this->Module->Koperasi->addpengurus(array(
								'idKoperasi' => $idKoperasi,
								'vJabatan' => $vJabatan,
								'vNama' => $vNama,
								'vTelp' => $vTelp,
								'vNik' => $vNik,
								'vPendidikan' => $vPendidikan,
								'vNPWP' => $vNPWP
								)))
							{	
								$Return = array('status' => 'success',
								'message' => $this->Template->showMessage('success', 'Data pengurus telah di tambahkan'), 
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
							'message' => $this->Template->showMessage('error', 'Ops! Data pengurus sudah di input sebelumnya'), 
							'data' => ''
							);
						}
					}
					else
					{
						if ($this->Module->Koperasi->checkpengurus($idKoperasi, $vJabatan, $vNama, $vNik, $idPengurus)==true)
						{
							if ($this->Module->Koperasi->updatepengurus(array(
								'vJabatan' => $vJabatan,
								'vNama' => $vNama,
								'vTelp' => $vTelp,
								'vNik' => $vNik,
								'vPendidikan' => $vPendidikan,
								'vNPWP' => $vNPWP
								),$idPengurus))
								{
									$Return = array('status' => 'success',
									'message' => $this->Template->showMessage('success', 'Data pengurus telah di perbaharui'), 
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

	function edit()
	{
		$this->Template->assign("Detail", $this->detailMember);
		echo $this->Template->Show("koperasi/koperasi_edit.html");
	}

	function editpengurus()
	{
		$detailPengurus = $this->Module->Koperasi->detailpengurus($this->Id);
		$Result = array(
			'id' => $detailPengurus['id'],
			'vnama' => $detailPengurus['vNama'],
			'vjabatan' => $detailPengurus['vJabatan'],
			'vnik' => $detailPengurus['vNik'],
			'vtelp' => $detailPengurus['vTelp'],
			'vpendidikan' => $detailPengurus['vPendidikan'],
			'vnpwp' => $detailPengurus['vNPWP']
		);

		echo json_encode($Result);
	}

	function deletepengurus()
	{
		if ($this->Id!="")
		{
			if ($this->Module->Koperasi->deletepengurus($this->Id))
			{
				$Return = array('status' => 'success',
				'message' => $this->Template->showMessage('success', 'Data pengurus koperasi telah di hapus'), 
				'data' => ''
				);
			}
		}
		else
		{
			$Return = array('status' => 'error',
			'message' => $this->Template->showMessage('error', 'Ops! ID pengurus koperasi tidak valid'), 
			'data' => ''
			);			
		}

		echo json_encode($Return);

	}

}

?>