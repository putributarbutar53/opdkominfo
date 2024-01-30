<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class Signin extends Core
{
	public function __construct()
	{
		parent::__construct();
	}
	
	function checkLogin($vEmail="", $cPassword="")
	{
		if (($vEmail!="") AND ($cPassword!=""))
		{
			$Baca = $this->Db->sql_query_array("SELECT id FROM cpkoperasi WHERE vEmail='".$vEmail."'");
			if ($Baca['id']!="")
			{
				$checkPassword = $this->Db->sql_query_array("SELECT id FROM cpkoperasi WHERE cPassword='".$cPassword."' AND id='".$Baca['id']."'");
				if ($checkPassword['id']!="")
					return true;
				else
					return false;
			}
			else
				return false;
		}
		else
			return false;
	}
		
	function updatePassword($vEmail, $oPassword, $nPassword)
	{
		if ($this->checkLogin($vEmail,$oPassword))
			return $this->Db->sql_query("UPDATE cpkoperasi SET cPassword='".md5($nPassword)."' WHERE vEmail='".$vEmail."'");
		else
			return false;
	}

	function sendPassword($vTelp, $Email)
	{
		if (($vTelp!="") AND ($Email!=""))
			return $this->Db->sql_query_array("SELECT id FROM cpkoperasi WHERE vTelp='".$vTelp."' AND vEmail='".$Email."'");
		else
			return "";
	}

	function checkEmail($vEmail="")
	{
		if ($vEmail!="")
		{
			$Baca = $this->Db->sql_query_array("SELECT id FROM cpkoperasi WHERE vEmail='".$vEmail."'");
			if ($Baca['id']!="")
				return true;
			else
				return false;
		}
		else
			return false;
	}
	
	function checkEmailAddress($vEmail)
	{
		if (filter_var($vEmail, FILTER_VALIDATE_EMAIL))
			return true;
		else
			return false;
	}

	function checkMobile($vTelp="")
	{
		if ($vTelp!="")
		{
			$Baca = $this->Db->sql_query_array("SELECT id FROM cpkoperasi WHERE vTelp='".$vTelp."'");
			if ($Baca['id']!="")
				return true;
			else
				return false;
		}
		else
			return false;
	}

	function checkEmpty($Data)
	{
		for ($i=0;$i<count($Data);$i++)
		{
			if ($Data[$i]=="")
			{
				return false;
				break;
			}
		}
		return true;
	}

	function detailMember($Id=NULL,$vEmail=NULL)
	{
		if (($Id!=NULL) AND ($Id!=""))		
			return $this->Db->sql_query_array("SELECT * FROM cpkoperasi WHERE id='".$Id."'");
		else
			return $this->Db->sql_query_array("SELECT * FROM cpkoperasi WHERE vEmail='".$vEmail."'");
	}

	function detailEmail($vEmail)
	{
		return $this->Db->sql_query_array("SELECT * FROM cpkoperasi WHERE vEmail='".$vEmail."'");
	}

	function detailPhone($vTelp)
	{
		return $this->Db->sql_query_array("SELECT * FROM cpkoperasi WHERE vTelp='".$vTelp."'");
	}

	function submitLogin($vEmail, $Password)
	{
		if ($this->checkEmpty(array($vEmail,$Password)))
		{
			$detailCustomer = $this->detailEmail($vEmail);
			if ($detailCustomer['id']=="")
				$detailCustomer = $this->detailPhone($vEmail);
			
			$idMember = $detailCustomer['id'];
			if ($idMember!="")
			{
				if ($detailCustomer['cPassword']==md5($Password))
				{
					//Log Login time
					$this->addLog($idMember);
					return "success";
				}
				else
					return "wrongpassword";
			}
			else
				return "noaccount";
		}
		else
			return "allform";		
	}
	
	//LOG MEMBER
	function countLog($idMember)
	{
		if ($idMember=="")
			return NULL;
		else
		{
			$Baca = $this->Db->sql_query_array("SELECT COUNT(*) AS total FROM cplog WHERE idMember='".$idMember."'");
			return $Baca['total'];
		}
	}		
	
	function lastLogin($idMember)
	{
		return $this->Db->sql_query_array("SELECT * FROM cplog WHERE idMember='".$idMember."' ORDER BY dLogin DESC");
	}
	
	function addLog($idMember)
	{
		if ($idMember!="")
		{
			if ($this->countLog($idMember) >= $this->Config['max']['log'])
			{
				$baca=$this->Db->sql_query("SELECT * FROM cplog WHERE idMember='".$idMember."' ORDER BY dLogin ASC");	
				while ($Baca=$this->Db->sql_array($baca))
				{
					$this->deleteLog($Baca['dLogin'],$Baca['idMember']);
				}
			}

			return $this->Db->sql_query("INSERT INTO cplog VALUES (NULL,'".$idMember."','".date("Y-m-d H:i:s")."','".$_SERVER['REMOTE_ADDR']."')");
		}
		else
			return false;
	}
	
	function deleteLog($dLogin,$idMember)
	{
		if (($idMember!="") AND ($dLogin!=""))
			return $this->Db->sql_query("DELETE FROM cplog WHERE idMember='".$idMember."' AND dLogin='".$dLogin."'");
		else
			return false;
	}
	
	function listLog($idMember="")
	{
		$MEMBER = ($idMember=="")?"":" WHERE idMember='".$idMember."'";
		$baca=$this->Db->sql_query("SELECT * FROM cplog".$MEMBER." ORDER BY dLogin DESC");	
		$i=0;
		while ($Baca=$this->Db->sql_array($baca))
		{
			$Data[$i] = array('No' => ($i+1), 'Item' => $Baca	);
			$i++;
		}
		return $Data;
	}

	function deleteAllLog($idMember)
	{
		if (trim($idMember)=="")
			return FALSE;
		else
			return $this->Db->sql_query("DELETE FROM cplog WHERE idMember='".$idMember."'");
	}
	
	function lastLog($idMember)
	{
		return $this->Db->sql_query_array("SELECT * FROM cplog WHERE idMember='".$idMember."' ORDER BY dLogin DESC");
	}
	
}
?>