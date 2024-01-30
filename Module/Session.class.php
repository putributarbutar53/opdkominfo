<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class Session extends Core
{
	var $timeoutSeconds;

	public function __construct()
	{
		parent::__construct();
		$this->timeoutSeconds = $this->Config['session']['time'];
	}
		
	//Lang History
    function refresh()
	{        
		$timeout = $this->getTimeOut();
        $sql = "DELETE FROM cpsession WHERE timestamp < ".$timeout." AND iRemember='0'";
        return $this->Db->sql_query($sql);
    }
	
    function logout($idMember)
	{        
        $sql = "DELETE FROM cpsession WHERE idMember='".$idMember."'";
        return $this->Db->sql_query($sql);
    }
		
	function updateSession($vSignature,$idMember,$iRemember=0)
	{
		$iRemember_ = ($iRemember=="")?0:$iRemember;
		$currentTime = time();
		$sessionHistory = $this->getSessionHistory($vSignature);
		if ($sessionHistory['idMember']=="")
		{
			$this->logout($idMember);
			return $this->Db->sql_query("INSERT INTO cpsession (timestamp,vSignature,idMember,iRemember) VALUES ('".$currentTime."','".session_id()."','".$idMember."','".$iRemember_."')");
		}
		else
		{
			return $this->Db->sql_query("UPDATE cpsession SET timestamp='".$currentTime."' WHERE vSignature='".$vSignature."' AND idMember='".$idMember."'");
		}
	}
	
	function getSessionHistory($vSignature)
	{
		$Baca = $this->Db->sql_query_array("SELECT * FROM cpsession WHERE vSignature = '".$vSignature."'");
		return $Baca;
	}

	function getTimeOut()
	{
        $currentTime = time();
        return $currentTime - $this->timeoutSeconds;	  
	}
	
}
?>