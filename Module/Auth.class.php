<?php if (!defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class Auth extends Core
{
	var $Db, $userAuth, $DetailAdmin;

	public function __construct()
	{
		parent::__construct();
	}

	function addAuthAdmin($userAuth, $DetailAdmin)
	{
		$this->userAuth = $userAuth;
		$this->DetailAdmin = $DetailAdmin;
	}

	function checkUsername($Username)
	{
		$Baca = $this->Db->sql_query_row("SELECT id FROM cpadmin WHERE vUsername='" . $Username . "' AND isLogin='1'");
		if ($Baca[0] == "") return false;
		else return true;
	}

	function detailUsername($username) {
		return  $this->Db->sql_query_array("SELECT * FROM cpadmin WHERE vUsername='" . $username . "'");
	}

	function getEmail($vUsername)
	{
		$Baca = $this->Db->sql_query_row("SELECT vEmail FROM cpadmin WHERE vUsername='" . $vUsername . "'");
		return $Baca[0];
	}

	function checkPassword($Username, $Password)
	{
		$Baca = $this->Db->sql_query_array("SELECT cPassword FROM cpadmin WHERE vUsername='" . $Username . "'");
		if (password_verify($Password, $Baca[0]))
			return true;
		else
			return false;
	}

	//liat sessionnya apakah betul nih session
	function checkSession($Username, $thepage = NULL)
	{
		$Username = trim($Username);
		$me = true;
		//jika spesifikasi pages tidak ada
		if ($thepage == NULL) {
			if ($Username == "")
				$me = false;
			else {
				if ($this->checkUsername($Username) == false)
					$me = false;
			}
		} else {
			//jika membutuhkan spesifikasi pages
			$me = false;
			if ($Username != "admin") {
				$Baca = $this->detailAdmin(NULL, $Username);
				$Compar = explode(",", $Baca['vRestriction']);
				for ($i = 0; $i < count($Compar); $i++) {
					if ($Compar[$i] == $thepage) {
						$me = true;
						break;
					}
				}
			} else
				$me = true;
		}

		if ($me == false)
			die("<font face=verdana size=2 color=red>Ops! Your session has been expired or you are not authenticate to access this page<br />Please <b><a href=\"" . $this->Config['base']['url'] . $this->Config['index']['page'] . $this->Config['base']['admin'] . "\">login</a></b> first</font>");

		return $me;
	}

	//kudu update last login nya dia
	function updateLastLogin($Username)
	{
		return $this->Db->sql_query("UPDATE cpadmin SET dLastLogin='" . date("Y-n-d h:i:s") . "' WHERE vUsername='" . $Username . "'");
	}

	//kudu dapetin last login nya si username
	function getLastLogin($Username)
	{
		$Baca = $this->Db->sql_query_array("SELECT DATE_FORMAT(dLastLogin, '%W, %d %M %Y - %h:%i:%s') AS dateLogin FROM cpadmin WHERE vUsername='" . $Username . "'");
		return $Baca['dateLogin'];
	}

	function updatePassword($Username, $Password)
	{
		// Generate a random salt
		$salt = password_hash(random_bytes(16), PASSWORD_BCRYPT);
		// Hash the password with bcrypt
		$nPassword = password_hash($Password, PASSWORD_BCRYPT, ['salt' => $salt, 'cost' => 12]);
		return $this->Db->sql_query("UPDATE cpadmin SET cPassword='" .$nPassword. "' WHERE vUsername='" . $Username . "'");
	}

	function updateName($vName, $Username)
	{
		return $this->Db->sql_query("UPDATE cpadmin SET vName='" . $vName . "' WHERE vUsername='" . $Username . "'");
	}

	function updateEmail($Username, $vEmail)
	{
		return $this->Db->sql_query("UPDATE cpadmin SET vEmail='" . $vEmail . "' WHERE vUsername='" . $Username . "'");
	}

	function detailAdmin($id, $vUsername = NULL)
	{
		if ($vUsername == NULL)
			return $this->Db->sql_query_array("SELECT * FROM cpadmin WHERE id='" . $id . "'");
		else
			return $this->Db->sql_query_array("SELECT * FROM cpadmin WHERE vUsername='" . $vUsername . "'");
	}

	function detailAdminByEmail($vEmail)
	{
		return $this->Db->sql_query_array("SELECT * FROM cpadmin WHERE vEmail='" . $vEmail . "'");
	}

	function deleteUser($id)
	{
		$Baca = $this->detailAdmin($id);
		if ($Baca['vUsername'] != "admin") {
			$this->Db->sql_query("DELETE FROM cpadmin WHERE id='" . $id . "'");
			return true;
		} else
			return false;
	}

	function adduser($Data)
	{
		return $this->Db->add($Data, "cpadmin");
	}

	function updateUser($Data, $Id)
	{
		return $this->Db->update($Data, $Id, "cpadmin");
	}

	function updateRole($Data, $Id)
	{
		return $this->Db->update($Data, $Id, "cpadmin");
	}

	function listAdmin()
	{
		$baca = $this->Db->sql_query("SELECT * FROM cpadmin ORDER BY id ASC");
		$i = 0;
		while ($Baca = $this->Db->sql_array($baca)) {
			$Data[$i] = array(
				'No' => ($i + 1),
				'Item' => $Baca
			);
			$i++;
		}
		return $Data;
	}

	//Request Testimony
	function countTestimony()
	{
		return $this->Db->sql_query_array("SELECT COUNT(id) AS total FROM cptestimony WHERE iStatus='0'");
	}

	//Verify Admin
	private function dashboard()
	{
		echo "<script>location.href='" . $this->Config['base']['url'] . $this->Config['index']['page'] . $this->Config['base']['admin'] . "/dashboard';</script>";
		die();
	}

	function verifyAdmin($slug, $username = "", $DetailAdmin)
	{
		if (!(empty($slug))) {
			$progress = false;
			for ($i = 0; $i < count($slug); $i++) {
				if (in_array($slug[$i], $DetailAdmin['auth'])) {
					$progress = true;
					break;
				}
			}

			if ($progress == false) {
				$this->dashboard();
			}
		} else {
			if ($username == "") {
				$this->dashboard();
			} else {
				if ($DetailAdmin['vUsername'] != $username) {
					$this->dashboard();
				}
			}
		}
	}
}
