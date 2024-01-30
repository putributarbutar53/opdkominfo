<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class Event extends Core
{
	public function __construct()
	{
		parent::__construct();
	}

	function listall($iStatus="")
	{
		$_STATUS = ($iStatus=="")?"":" AND iStatus='".$iStatus."'";
		$baca = $this->Db->sql_query("SELECT * FROM cpevent WHERE id!='0'".$_STATUS." ORDER BY id ASC");
		$i=0;		
		while ($Baca=$this->Db->sql_array($baca))
		{
			$Data[$i] = array(	
				'Item' => $Baca
			);
			$i++;
		}
		
		return $Data;
	}

	function listnew($Max="")
	{
		$_MAX = ($Max=="")?"":" LIMIT 0,".$Max;
		$baca = $this->Db->sql_query("SELECT * FROM cpevent WHERE id!='0'".$_STATUS." ORDER BY id DESC".$_MAX);
		$i=0;		
		while ($Baca=$this->Db->sql_array($baca))
		{
			$Data[$i] = array(	
				'Item' => $Baca
			);
			$i++;
		}
		
		return $Data;
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

	function detail($Id)
	{
		return $this->Db->sql_query_array("SELECT * FROM cpevent WHERE id='".$Id."'");
	}

	function status($iStatus, $idEvent)
	{
		return $this->Db->sql_query("UPDATE cpevent SET iStatus='".$iStatus."' WHERE id='".$idEvent."'");
	}

	function delete($Id)
	{
		if ($Id=="")
			return FALSE;
		else
			return $this->Db->sql_query("DELETE FROM cpevent WHERE id='".$Id."'");
	}
	
	function add($Data)
	{
		return $this->Db->add($Data, "cpevent");
	}

	function update($Data, $Id)
	{
		return $this->Db->update($Data, $Id, "cpevent");
	}

	function checkpeserta($idSeminar, $idMember)
	{
		$Baca = $this->Db->sql_query_array("SELECT id FROM cpevent_peserta WHERE idSeminar='".$idSeminar."' AND idMember='".$idMember."'");
		if ($Baca['id']=="")
			return true;
		else
			return false;
	}

	function getpeserta($idSeminar, $idMember)
	{
		return $this->Db->sql_query_array("SELECT id FROM cpevent_peserta WHERE idSeminar='".$idSeminar."' AND idMember='".$idMember."'");
	}

	function deletepeserta($Id)
	{
		if ($Id=="")
			return FALSE;
		else
			return $this->Db->sql_query("DELETE FROM cpevent_peserta WHERE id='".$Id."'");
	}

	function addpeserta($Data)
	{
		return $this->Db->add($Data, "cpevent_peserta");
	}

	function statuspeserta($iStatus, $Id)
	{
		return $this->Db->sql_query("UPDATE cpevent_peserta SET iStatus='".$iStatus."' WHERE id='".$Id."'");
	}

	function absenpeserta($iHadir, $Id)
	{
		return $this->Db->sql_query("UPDATE cpevent_peserta SET iHadir='".$iHadir."' WHERE id='".$Id."'");
	}

	function countpeserta($idSeminar)
	{
		return $this->Db->sql_query_array("SELECT COUNT(*) AS total FROM cpevent_peserta WHERE idSeminar='".$idSeminar."'");
	}

	function deleteallpeserta($idSeminar)
	{
		return $this->Db->sql_query("DELETE FROM cpevent_peserta WHERE idSeminar='".$idSeminar."'");
	}
	
}
?>