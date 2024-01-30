<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class memberaccount extends Core
{
	var $idMember, $detailMember;
	
	public function __construct()
	{
		parent::__construct();

		$this->LoadModule("Signin");
		$this->LoadModule("Koperasi");
		$this->LoadModule("Event");
		$this->LoadModule("Bantuan");

		$this->LoadModule("Paging");
		$this->Module->Paging->setPaging(21,5);

		$this->LoadModule("Tanggal");
		//Load General Process
		include '../inc/general.php';
		include '../inc/common.php';	
		$this->Template->assign("Signature_Sub", $this->uri(2));
		$this->Template->assign("dirPicture", $this->Config['member']['image']);
	}
	
	//Main
	function main()
	{
		$this->Template->assign("listEvent", $this->Module->Event->listnew("5"));
		$this->Template->assign("listBantuan", $this->Module->Bantuan->listnew("5"));
		echo $this->Template->Show("dashboard.html");
	}

	function countdata()
	{
		$countEvent = $this->Db->sql_query_array("SELECT COUNT(a.id) AS total FROM cpevent_peserta a JOIN cpkoperasi_pengurus b ON b.id = a.idMember WHERE a.idKoperas='".$this->detailMember['id']."'");
		$countBantuan = $this->Db->sql_query_array("SELECT COUNT(id) AS total FROM cpbantuan_peserta WHERE idKoperasi='".$this->detailMember['id']."'");
		$countAnggota = $this->Db->sql_query_array("SELECT COUNT(id) AS total FROM cpkoperasi_pengurus WHERE idKoperasi='".$this->detailMember['id']."'");

		$Result = array(
			'totalevent' => number_format($countEvent['total'])." Events",
			'totalbantuan' => number_format($countBantuan['total'])." Bantuan",
			'totalanggota' => number_format($countAnggota['total'])." Anggota"
		);
		echo json_encode($Result);
	}
	
	//Profile
	function profile()
	{
		$this->Template->assign("Signature_Sub", "profile");
		$fileDestination = $this->Config['member']['image'];
		$this->Template->assign("fileDestination", $fileDestination);
		$this->Pile->fileDestination=$fileDestination;
		
		if ($_POST['submit'])
		{
			$Action = ($_POST['action'])?$_POST['action']:$_GET['action'];
			
			switch($Action)
			{
				case "simpanprofile":
					$vName = strip_tags($_POST['vName']);
					$vEmail = strip_tags($_POST['vEmail']);
					$vAddress = strip_tags($_POST['vAddress']);
					$vZIP = strip_tags($_POST['vZIP']);
					$idCity = strip_tags($_POST['idCity']);
					$vPhone = strip_tags($_POST['vPhone']);
					$vMobile = strip_tags($_POST['vMobile']);
					
					$vKTP = $_POST['vKTP'];
					$vNamaUsaha = $_POST['vNamaUsaha'];
					$vNPWP = $_POST['vNPWP'];
					
					$vSex = $_POST['vSex'];
					$dTglLahir = $_POST['dTglLahir'];
					
					if ($this->Module->Member->checkEmpty(array($vName,$vEmail,$vAddress,$vZIP,$idCity)))
					{
						//$vAvatar = $this->Pile->simpanImage($_FILES['vAvatar'],"av_".strtolower(trim($this->mUsername))."_".date("Yndhis").rand(0,9).rand(0,9).rand(0,9));
						//if ($vAvatar!="") 
						//	$this->Pile->deleteOldFile($this->detailMember['vAvatar']);
						$vAvatar = "";
						
						if ($this->Module->Member->updateProfile(array($vName,$vEmail,$vAddress,$vZIP,$idCity,$vPhone,$vMobile,$vKTP,$vNamaUsaha,$vNPWP,$vSex,$dTglLahir),$this->mUsername,$vAvatar))
							$this->Template->reportMessage("success", "Update data member berhasil");
						else
							$this->Template->reportMessage("error", "Ops! Ada error dalam database");
					}
					else
						$this->Template->reportMessage("error", "Ops! Semua form pengisian harus anda isi");
				break;
			}
		}
		
		$Detail = $this->Module->Member->detailMember(NULL,$this->mUsername);
		$this->Template->assign("listCity", $this->Module->Products->listCity());
		$this->Template->assign("Detail", $Detail);
		echo $this->Template->Show("main_profile.html");
	}
	
	//Close Account
	function closeaccount()
	{
		$this->LoadModule("Tools");
		
		$Detail = $this->Module->Member->detailMember(NULL,$this->mUsername);
		$this->Template->assign("Detail", $Detail);

		if ($_POST['submit'])
		{
			$Action = ($_POST['action'])?$_POST['action']:$_GET['action'];
			
			switch($Action)
			{
				case "tutupaccount":
									
					$Alasan = $_POST['vAlasan'];
					
					$Message = "Request untuk penutupan account. Data member berikut:\n\n";
					$Message .= "Username: ".$Detail['vUsername']."\nMember Name: ".$Detail['vName']."\nEmail: ".$Detail['vEmail']."\n\nAlasan Penutupan:\n".$Alasan."\n\n";
					$Message .= "\nPenutupan akan di proses oleh administrator maksimal 2x24 jam. Mohon Tunggu.\n\nTerima kasih\n\n";
					
					$this->Module->Tools->sendEmail($Detail['vEmail'],"Permintaan Penutupan ACCOUNT (".$Detail['vUsername'].")",$Message,$this->Config['default']['email']);
					$this->Module->Tools->sendEmail($this->Config['confirmation']['email'],"Permintaan Penutupan ACCOUNT (".$Detail['vUsername'].")",$Message,$this->Config['noreply']['email']);
					
					$this->Template->reportMessage("success", "Sukses! Permintaan penutupan account anda telah kami simpan dan akan di PROSES maksimal 2x24 jam. Mohon Tunggu!!");
				break;
			}
		}
		
		echo $this->Template->Show("main_hapus.html");
	}

	//Membership
	function membership()
	{		
		$this->checkAuthMember(array("no","all"), $this->detailMember);//Check Auth
		$Submit = ($_POST['submit'])?$_POST['submit']:$_GET['submit'];
		$Id = ($_POST['id'])?$_POST['id']:$_GET['id'];
		
		if ($Submit)
		{
			$Action = ($_POST['action'])?$_POST['action']:$_GET['action'];
			switch($Action)
			{
				case "upgrademember":
					$idMembership = $_POST['idMembership'];
					$detailMembership = $this->Module->Memberoptions->detailMembership($idMembership);
					$checkMembershipOrder = $this->Module->Memberoptions->detailMembershipOrderByUsername($this->mUsername);
					
					if ($checkMembershipOrder['id']=="")
						$dateRegister = date("Y-m-d");
					else
						$dateRegister = $checkMembershipOrder['dExpired'];
					
					$dateExpired = $this->Module->Tanggal->add_Date("Y-m-d", $dateRegister, $detailMembership['iDays']);
					$Price = $detailMembership['iPrice'];
					$vKet = $detailMembership['vMembership'];
					$iStatus = "0";
					$Process = FALSE;
					
					if ($this->Module->Memberoptions->addMembershipOrder(array($this->mUsername,$vKet,$dateRegister,$dateExpired,$Price,$iStatus)))
					{
						//$this->Module->Member->potongDeposit($this->mUsername, $Price);
						$this->detailMember = $this->Module->Member->detailMember(NULL,$this->mUsername);
						$this->Template->assign("detailMember", $this->detailMember);
						//$newRef = $this->Module->Options->getNewRef();
						//$this->Module->Options->addTransaksi(array($newRef,$this->mUsername,date("Y-m-d H:i:s"),"UPGRADE MEMBERSHIP: ".$vKet,$Price,"1"));
						//$this->Module->Member->updateMembership("1", $this->mUsername);
						//Save To Billing
						$this->Module->Options->saveBilling(array($this->mUsername,date("Y-m-d"),"UPGRADE MEMBERSHIP",($Price-$selisihHarga),'0000-00-00 00:00:00','0','membership'));
						
						$this->Template->reportMessage("success", "Upgrade membership sukses");
					}
				break;
			}
		}
		
		$this->Template->assign("listMemb", $this->Module->Memberoptions->listMembership());
			
		$Do=$_GET['do'];
		$this->Template->assign("Do", $Do);
		
		$Show=$_GET['show'];
		$this->Template->assign("Show", $Show);
		
		$this->Template->assign("Membership", $this->Module->Memberoptions->detailMembershipOrderByUsername($this->mUsername));

		switch ($Show)
		{
			case "confirmmembership":
				$idMembership = $_POST['idMembership'];
				$detailMembership = $this->Module->Memberoptions->detailMembership($idMembership);	
				$this->Template->assign("detailMembership", $detailMembership);
				echo $this->Template->Show("main_membership_confirm.html");
			break;
			default:
			
				$this->Module->Paging->query("SELECT * FROM cpmembership WHERE vUsername='".$this->mUsername."' ORDER BY id DESC");
				$this->Module->Paging->URL = $this->getURL()."memberaccount/membership";
				$this->Module->Paging->dataLink = "";
				$page=$this->Module->Paging->print_info();
				$this->Template->assign("status", "Membership ".$page[start]." - ".$page[end]." of ".$page[total]." [Total ".$page[total_pages]." Post]");
				$i=0;
				while ($Baca=$this->Module->Paging->result_assoc()) {
					$Data[$i] = array(	'No' => ($i+1),	
										'Item' => $Baca
									);
					$i++;
				}
				$this->Template->assign("list", $Data);
				$this->Template->assign("link", $this->Module->Paging->print_link());
				echo $this->Template->Show("main_membership.html");
			break;
		}		
	}
		
	//User Bank Account
	function userbankaccount()
	{
		$Submit = ($_POST['submit'])?$_POST['submit']:$_GET['submit'];
		$Id = ($_POST['id'])?$_POST['id']:$_GET['id'];
		
		if ($Submit)
		{
			$Action = ($_POST['action'])?$_POST['action']:$_GET['action'];
				
			switch ($Action)
			{
				//User Bank
				case "adduserbank":
				case "updateuserbank":
				
					$vNoRekening = $_POST['vNoRekening'];
					$vCabang = $_POST['vCabang'];
					$vBank = $_POST['vBank'];
					$vName = $_POST['vName'];
					
					if (($vNoRekening!="") OR ($vCabang!="") OR ($vBank!="") OR ($vName!=""))
					{
						if ($Action=="adduserbank")
						{
							if ($this->Module->Options->addUserBank(array($this->mUsername,$vName,$vNoRekening,$vCabang,$vBank)))
								$this->Template->reportMessage("success", "Data bank account telah ditambahkan");
						}
						
						if ($Action=="updateuserbank")
						{
							if ($this->Module->Options->updateUserBank(array($vName,$vNoRekening,$vCabang,$vBank),$Id,$this->mUsername))
								$this->Template->reportMessage("success", "Data bank account telah diperbaharui");
						}
					}					
				break;
				case "deleteuserbank":
					if ($this->Module->Options->deleteUserBank($this->mUsername, $Id))
						$this->Template->reportMessage("success", "Data bank account telah dihapus");
				break;
			}
		}
		
		$Do=$_GET['do'];
		$this->Template->assign("Do", $Do);
		
		$Show=$_GET['show'];
		$this->Template->assign("Show", $Show);
		
		switch($Show)
		{
			case "edit":
				$detailBank = $this->Module->Options->detailUserBank($Id, $this->mUsername);
				$this->Template->assign("Detail", $detailBank);
				$hiddenOption = "<input type=hidden name=action value=updateuserbank />";
				$hiddenOption .= "<input type=hidden name=id value=".$detailBank['id']." />";
			break;
			default:
				$hiddenOption = "<input type=hidden name=action value=adduserbank />";
			break;
		}
		
		$this->Template->assign("hiddenOption", $hiddenOption);
		$listBank = $this->Module->Options->listUserBank($this->mUsername);
		$this->Template->assign("listBank", $listBank);
		echo $this->Template->Show("main_user_bank.html");
	}		

	//Testimony
	function testimony()
	{
		$Submit = ($_POST['submit'])?$_POST['submit']:$_GET['submit'];
		$Id = ($_POST['id'])?$_POST['id']:$_GET['id'];
		
		if ($Submit)
		{
			$Action = ($_POST['action'])?$_POST['action']:$_GET['action'];
				
			switch ($Action)
			{
				//Add Testimony
				case "addtestimony":
				
					$tTestimony = $_POST['tTestimony'];
					$iRank = $_POST['iRank'];
					$dTanggal = date("Y-m-d H:i:s");
					$iStatus = 0;
										
					if ($tTestimony!="")
					{
						if ($this->Module->Memberoptions->addTestimony(array($this->mUsername,$dTanggal,$tTestimony,$iRank,$iStatus)))
							$this->Template->reportMessage("success", "Terima Kasih, testimony anda telah dikirimkan");
					}
							
				break;
			}
		}
		
		$Do=$_GET['do'];
		$this->Template->assign("Do", $Do);
		
		$Show=$_GET['show'];
		$this->Template->assign("Show", $Show);
		
		switch($Show)
		{
			case "edit":
				$Detail = $this->Module->Memberoptions->detailTestimony($Id, $this->mUsername);
				$this->Template->assign("Detail", $Detail);
				$hiddenOption = "<input type=hidden name=action value=updatetestimony />";
				$hiddenOption .= "<input type=hidden name=id value=".$Detail['id']." />";
			break;
			default:
				$hiddenOption = "<input type=hidden name=action value=addtestimony />";
			break;
		}
		
		$this->Template->assign("hiddenOption", $hiddenOption);
		$list = $this->Module->Memberoptions->listTestimony($this->mUsername);
		$this->Template->assign("list", $list);
		echo $this->Template->Show("main_testimony.html");
	}		

	//Payment Info
	function paymentinfo()
	{
		$id_billing = $_GET['id_billing'];
		$this->Template->assign("detailBilling", $this->Module->Options->detailBilling($this->mUsername, $id_billing));
		$this->Template->assign("listAdminBank", $this->Module->Options->listAdminBank());
		echo $this->Template->Show("main_paymentinfo.html");
	}

	//My Password	
	function mypassword()
	{
		if ($_POST['submit'])
		{
			$Action = ($_POST['action'])?$_POST['action']:$_GET['action'];
			switch($Action)
			{
				case "changepass":

					$oPassword = $_POST['oPassword'];
					$nPassword = $_POST['nPassword'];
					$rPassword = $_POST['rPassword'];
					
					if ($this->Module->Signin->checkEmpty(array($oPassword,$nPassword,$rPassword)))
					{
						if ($nPassword==$rPassword)
						{
							if ($this->Module->Signin->checkLogin($this->detailMember['vEmail'], md5($oPassword)))
							{
								if ($this->Module->Signin->updatePassword($this->detailMember['vEmail'], md5($oPassword), $nPassword))
								{
									$Return = array('status' => 'success',
										'message' => $this->Template->showMessage('success', 'Password anda telah diperbaharui'), 
										'data' => ''
									);
								}
								else
								{
									$Return = array('status' => 'error',
										'message' => $this->Template->showMessage('error', 'Ops! Ada error dalam database'), 
										'data' => ''
									);
								}
							}
							else
							{
								$Return = array('status' => 'error',
									'message' => $this->Template->showMessage('error', 'Ops! Password lama anda tidak benar'), 
									'data' => ''
								);
							}
						}
						else
						{
							$Return = array('status' => 'error',
								'message' => $this->Template->showMessage('error', 'Ops! Password baru tidak di ulang dengan baik'), 
								'data' => ''
							);
						}
					}
					else
					{
						$Return = array('status' => 'error',
							'message' => $this->Template->showMessage('error', 'Ops! Semua field yang di tandai merah harus anda isi'), 
							'data' => ''
						);
					}
				break;
			}

			echo json_encode($Return);
			die();
		}

		echo $this->Template->Show("account/account_index.html");
	}
	
	//Logout
	function logout()
	{
		$this->Module->Session->logout($this->idMember);
		$this->Template->assign("detailMember", array());
		
		//Hapus All Session
		$_SESSION['sourceURL'] = "";
		unset($_SESSION['sourceURL']);

		$this->Template->reportMessage("success", "Anda telah keluar dari member area, IP Anda: ".$_SERVER['REMOTE_ADDR']);
		echo $this->Template->Show("index.html");
	}
	
}

?>