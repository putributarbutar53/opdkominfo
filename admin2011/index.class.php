<?php if (!defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class index extends Core
{
	public function __construct()
	{
		parent::__construct();
		$this->LoadModule("Auth");
		ob_clean();

		//Action
		$this->Submit = ($_POST['submit']) ? $_POST['submit'] : $_GET['submit'];
		$this->Action = ($_POST['action']) ? $_POST['action'] : $_GET['action'];

		$this->Id = ($_POST['id']) ? $_POST['id'] : $_GET['id'];
		$this->Template->assign("Id", $this->Id);
	}

	function main()
	{
		if ($this->Submit) {
			switch ($this->Action) {
				case "login":

					$Username = $_POST['username'];
					$Password = $_POST['password'];

					if (($Username != "") and ($Password != "")) {
						if ($this->Module->Auth->checkUsername($Username)) {
							if ($this->Module->Auth->checkPassword($Username, $Password)) {
								$_SESSION['zxcvbnm'] = $Username;
								//header("Location: ".$this->Config['base']['url'].$this->Config['index']['page'].$this->Config['base']['admin']."/dashboard");
								//echo $this->Config['base']['url'].$this->Config['index']['page'].$this->Config['base']['admin']."/dashboard";
								echo "<script>location.href='" . $this->Config['base']['url'] . $this->Config['index']['page'] . $this->Config['base']['admin'] . "/dashboard';</script>";
								die();
							} else {
								// Jika salah, tambahkan 1 ke counter percobaan gagal
								if (!isset($_SESSION['login_attempts'])) {
									$_SESSION['login_attempts'] = 0;
								}
								$_SESSION['login_attempts']++;
								// Jika telah melebihi batas percobaan, blokir pengguna
								if ($_SESSION['login_attempts'] >= 5) {
									$username_ = $this->Module->Auth->detailUsername($Username);
									if ($this->Module->Auth->updateUser(array('isLogin' => 0), $username_['id']))
										$this->Template->reportMessage("error", "Ops! Anda telah mencoba login 5 kali, akun Anda telah diblokir!!");
								} else {
									$this->Template->reportMessage("error", "Ops! Password anda tidak benar!!");
								}
							}
						} else
							$this->Template->reportMessage("error", "Ops! Username anda tidak benar");
					} else
						$this->Template->reportMessage("error", "Ops! Mohon diisi username dan password");

					break;
			}
		}
		echo $this->Template->ShowAdmin("index.html");
	}
	function logout()
	{
		$this->Module->Auth->updateLastLogin($_SESSION['zxcvbnm']);
		$_SESSION['zxcvbnm'] = "";
		unset($_SESSION['zxcvbnm']);
		$this->Template->reportMessage("success", "Anda sudah logout dari IP: " . $_SERVER['REMOTE_ADDR']);
		echo $this->Template->ShowAdmin("index.html");
	}
}
