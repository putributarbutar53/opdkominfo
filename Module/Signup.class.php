<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class Signup extends Core
{
	public function __construct()
	{
		parent::__construct();
	}
	
	function checkLogin($vEmail="", $cPassword="")
	{
		$vEmail = trim($this->Db->real_escape_string($vEmail));
		$cPassword = $this->Db->real_escape_string($cPassword);

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

	function sendPassword($Username,$Email)
	{
		if (($Username!="") AND ($Email!=""))
			return $this->Db->sql_query_array("SELECT id FROM cpmembers WHERE vUsername='".$Username."' AND vEmail='".$Email."'");
		else
			return "";
	}

	function checkUsername($Username="")
	{
		if ($Username!="")
		{
			$Baca = $this->Db->sql_query_array("SELECT id FROM cpmembers WHERE vUsername='".$vUsername."'");
			if ($Baca['id']!="")
				return true;
			else
				return false;
		}
		else
			return false;
	}

	function checkEmail($vEmail="")
	{
		if ($vEmail!="")
		{
			$Baca = $this->Db->sql_query_array("SELECT id FROM cpmembers WHERE vEmail='".$vEmail."'");
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

	function checkMobile($vMobile="")
	{
		if ($vEmail!="")
		{
			$Baca = $this->Db->sql_query_array("SELECT id FROM cpmembers WHERE vMobile='".$vMobile."'");
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

	function detailMember($Id=NULL,$vUsername=NULL)
	{
		if (($Id!=NULL) AND ($Id!=""))		
			return $this->Db->sql_query_array("SELECT * FROM cpmembers WHERE id='".$Id."'");
		else
			return $this->Db->sql_query_array("SELECT * FROM cpmembers WHERE vUsername='".$vUsername."'");
	}

	function detailEmail($vEmail)
	{
		return $this->Db->sql_query_array("SELECT * FROM cpmembers WHERE vEmail='".$vEmail."'");
	}

	function submitLogin($vEmail, $Password)
	{
		if ($this->checkEmpty(array($vEmail,$Password)))
		{
			$detailCustomer = $this->detailEmail($vEmail);
			if ($detailCustomer['id']=="")
				$detailCustomer = $this->detailMemberByPhone($vEmail);
			
			$Username = $detailCustomer['vUsername'];
			if ($this->checkBanned($Username)==false)
			{
				if ($this->checkActiveUsername($Username, $detailCustomer['iType'])==true)
				{
					if ($detailCustomer['cPassword']==md5($Password))
					{
						$checkVerify = $this->detailVerify($Username);
						if ($checkVerify['id']=="")
						{
							//Log Login time
							$this->addLog($Username);
							return "success";
						}
						else
						{
							return "verify";
						}
					}
					else
						return "wrongpassword";
				}
				else
				{
					if ($detailCustomer['id']=="")
						return "notregistered";
					else
					{
						return "notactivated";
					}
				}
			}
			else
				return "banneduser";
		}
		else
			return "allform";		
	}
	
	function registerMember($Detail)
	{
		$vUsername = $Detail['vUsername'];
		$cPassword = $Detail['cPassword'];
		$RPassword = $Detail['RPassword']; 
		$vName = $Detail['vName'];
		$vEmail = $Detail['vEmail'];
		$vAddress = $Detail['vAddress'];
		$vZIP = $Detail['vZIP'];
		$vCity = $Detail['vCity'];
		$vPhone = $Detail['vPhone'];
		$vMobile = $Detail['vMobile'];
		$iStatus = $Detail['iStatus'];
		$vTwitterID = $Detail['vTwitterID'];
		$vGoogleID = $Detail['vGoogleID'];
		$vFacebookID = $Detail['vFacebookID'];
		
		if ($this->checkEmpty(array($vUsername,$cPassword,$RPassword,$vName,$vEmail)))
		{
			if ($cPassword == $RPassword)
			{
				if (ctype_alnum($vUsername))
				{					
					if ($this->checkBanned($vUsername)==false)
					{
						if ($this->checkUsername($vUsername)==false)
						{					
							if (($this->checkEmailAddress($vEmail)==true) AND ($this->checkEmail($vEmail)==false))
							{
								if ($this->addMember(array($vUsername,$cPassword,$vName,$vEmail,$vAddress,$vZIP,$vCity,$vPhone,$vMobile,$iStatus,$vTwitterID,$vGoogleID,$vFacebookID)))
									return "success";
								else
									return "error";
							}
							else
								return "duplicate_email";
						}
						else
							return "duplicate_username";
					}
					else
						return "banned_username";
				}
				else
					return "numeric_username";
			}
			else
				return "retype_password";
		}
		else
			return "allform";
	}

	function listAllMembers($iStatus="all")
	{
		$_STATUS = (($iStatus=="all") OR ($iStatus==""))?"":" WHERE iStatus='".$iStatus."'";
		$baca=$this->Db->sql_query("SELECT * FROM cpmembers".$_STATUS." ORDER BY id DESC");	
		$i=0;
		
		while ($Baca=$this->Db->sql_array($baca))
		{
			$Data[$i] = array('No' => ($i+1), 'Item' => $Baca	);
			$i++;
		}
		
		return $Data;
	}
	
	//LOG MEMBER
	function countLog($vUsername)
	{
		if ($vUsername=="")
			return NULL;
		else
		{
			$Baca = $this->Db->sql_query_array("SELECT COUNT(*) AS total FROM cpmembers WHERE vUsername='".$vUsername."'");
			return $Baca['total'];
		}
	}		
	
	function lastLogin($vUsername)
	{
		return $this->Db->sql_query_array("SELECT * FROM cplog WHERE vUsername='".$vUsername."' ORDER BY dLogin DESC");
	}
	
	function addLog($vUsername)
	{
		if ($vUsername!="")
		{
			if ($this->countLog($vUsername) >= $this->Config['max']['log'])
			{
				$baca=$this->Db->sql_query("SELECT * FROM cplog WHERE vUsername='".$vUsername."' ORDER BY dLogin ASC");	
				while ($Baca=$this->Db->sql_array($baca))
				{
					$this->deleteLog($Baca['dLogin'],$Baca['vUsername']);
				}
			}

			return $this->Db->sql_query("INSERT INTO cplog VALUES (NULL,'".$vUsername."','".date("Y-m-d H:i:s")."','".$_SERVER['REMOTE_ADDR']."')");
		}
		else
			return false;
	}
	
	function deleteLog($dLogin,$vUsername)
	{
		if (($vUsername!="") AND ($dLogin!=""))
			return $this->Db->sql_query("DELETE FROM cplog WHERE vUsername='".$vUsername."' AND dLogin='".$dLogin."'");
		else
			return false;
	}
	
	function listLog($vUsername="")
	{
		$USERNAME = ($vUsername=="")?"":" WHERE vUsername='".$vUsername."'";
		$baca=$this->Db->sql_query("SELECT * FROM cplog".$USERNAME." ORDER BY dLogin DESC");	
		$i=0;
		while ($Baca=$this->Db->sql_array($baca))
		{
			$Data[$i] = array('No' => ($i+1), 'Item' => $Baca	);
			$i++;
		}
		return $Data;
	}

	function deleteAllLog($vUsername)
	{
		if (trim($vUsername)=="")
			return FALSE;
		else
			return $this->Db->sql_query("DELETE FROM cplog WHERE vUsername='".$vUsername."'");
	}
	
	function lastLog($vUsername)
	{
		return $this->Db->sql_query_array("SELECT * FROM cplog WHERE vUsername='".$vUsername."' ORDER BY dLogin DESC");
	}
	
}
?>