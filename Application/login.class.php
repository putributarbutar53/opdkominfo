<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class login extends Core
{
	public function __construct()
	{
		parent::__construct();
		$this->LoadModule("Koperasi");
		$this->LoadModule("Signin");
		
		//Load Session
			$this->LoadModule("Session");
			$this->Module->Session->refresh();
			$getSession = $this->Module->Session->getSessionHistory(session_id());
			$this->Template->assign("theUsername", $getSession['vUsername']);
			if ($getSession['vUsername']!="")
			{
				echo "<script>location.href='".$this->getURL()."memberaccount'</script>";
				die();
			}
	
		//Load General Process
		include '../inc/general.php';
	}
	
	//Login
	function main($Report=NULL)
	{	
		echo $this->Template->Show("index.html");
	}
	
	//Forgot Password
	function forgotpassword()
	{
		echo $this->Template->Show("forgotpassword.html");
	}
	
	//Send Password
	function sendpassword()
	{
		$Username = $_POST['Username'];
		$Email = $_POST['Email'];
		
		if ($this->Module->Member->checkEmpty(array($Username,$Email)))
		{
			//Sending Them via Email
			$Account = $this->Module->Member->sendPassword($Username,$Email);
			if ($Account['id']!="")
			{
				//Kirim Email Notifikasi
				$EmailDetail = array(
					'link' => $this->getURL()."login/resetpassword/".$Username."/".$Account['cPassword']
				);
				$this->Module->Tools->kirimEmail($Account['vEmail'], $EmailDetail, "sendreset");
				//-----------------------
				$this->Template->reportMessage("success", "Email dengan link untuk mereset password anda telah di kirim");
			}
			else
				$this->Template->reportMessage("error", "Ops! Username tidak sesuai dengan data email anda");
		}
		else
			$this->Template->reportMessage("error", "Ops! Semua form harus diisi");
		
		echo $this->Template->Show("forgotpassword.html");
	}

	//Login
	function submitlogin()
	{
		//$Username = strtolower($_POST['Username']);
		$Password = $_POST['Password'];
		$iRemember = $_POST['iRemember'];
		$vEmail = $_POST['vEmail'];
		
		$Report= NULL;
		
		if ($_POST['submit'])
		{
			switch ($this->Module->Signin->submitLogin($vEmail,$Password))
			{
				case "success":
					$detail_User = $this->Module->Signin->detailEmail($vEmail);
					//Session Added
					$this->Module->Session->refresh();
					//$session_Signature = md5($_SERVER['HTTP_USER_AGENT']."-".$_SERVER['REMOTE_ADDR']);
					$session_Signature = session_id();
					$this->Module->Session->updateSession($session_Signature,$detail_User['id'],$iRemember);
					
					//$_SESSION['mUsername'] = $Username;
					echo "<script>location.href='".$this->getURL()."memberaccount/main'</script>";
					die();
				break;
				case "wrongpassword":
					$this->Template->reportMessage("error", "Ops! Password anda tidak sesuai dengan account anda");
				break;
				case "noaccount":
					$this->Template->reportMessage("error", "Ops! Maaf, account anda belum terdaftar");
				break;
				case "allform":
				default:
					$this->Template->reportMessage("error", "Ops! Semua form harus di isi");
				break;
			}
		}
		
		$this->main($Report);				
	}
	
	//Reset Password
	function resetpassword()
	{
		$Username = $this->uri(3);
		$Password = $this->uri(4);
		
		if ($_SESSION['_resetpass']!="")
			$this->Template->reportMessage("success", "Sukses! Password baru anda telah kami reset menjadi: ".$_SESSION['_resetpass']);
		else
		{
			$Account = $this->Module->Member->detailMember(NULL,$Username);
			if ($this->Module->Member->checkLogin($Username,$Password))
			{
			
				$this->LoadModule("Pwgen");
				$this->Module->Pwgen->CreateRandom();
				$NewPassword = $this->Module->Pwgen->passwd;
				$this->Module->Member->updatePassword($Username, $Password, $NewPassword);
				
				//Kirim Email Notifikasi
				$EmailDetail = array(
					'username' => $Username, 
					'password' => $NewPassword, 
					'name' => $Account['vName']
				);
				$this->Module->Tools->kirimEmail($Account['vEmail'], $EmailDetail, "resetpassword");
				//-----------------------
				$_SESSION['_resetpass'] = $NewPassword;
			
				$this->Template->reportMessage("success", "Sukses! Password baru anda telah kami reset menjadi: ".$NewPassword);
			}
			else
				$this->Template->reportMessage("error", "Ops! Ada kesalahan dalam mereset password");
	
		}

		echo $this->Template->Show("login.html");
	}

}

?>