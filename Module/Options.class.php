<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class Options extends Core
{
	public function __construct()
	{
		parent::__construct();
	}
	
	//Content Setting
	function getContentSetting($idContent, $vModule)
	{
		$baca = $this->Db->sql_query("SELECT * FROM cpcontentmodule WHERE idContent='".$idContent."' AND vModule='".$vModule."'");
		$i=0;
		while ($Baca = $this->Db->sql_array($baca))
		{
			if ($i==0)
				$Data = array(	$Baca['vName'] => $Baca['vData']	);
			else
			{
				$Data_ = array(	$Baca['vName'] => $Baca['vData']	);
				$Data = array_merge($Data,$Data_);
			}
			$i++;
		}
		return $Data;
	}

	function getPictureSetting($idContent, $vModule)
	{
		$baca = $this->Db->sql_query("SELECT * FROM cpcontentmodule WHERE idContent='".$idContent."' AND vModule='".$vModule."'");
		$i=0;
		while ($Baca = $this->Db->sql_array($baca))
		{
			if ($i==0)
				$Data = array(	$Baca['vName'] => $Baca['vPicture']	);
			else
			{
				$Data_ = array(	$Baca['vName'] => $Baca['vPicture']	);
				$Data = array_merge($Data,$Data_);
			}
			$i++;
		}
		return $Data;
	}
	
	function listContentSetting($idContent, $vModule)
	{
		$baca = $this->Db->sql_query("SELECT * FROM cpcontentmodule WHERE idContent='".$idContent."' AND vModule='".$vModule."' ORDER BY vName ASC, id ASC");
		$i=0;
		while ($Baca = $this->Db->sql_array($baca))
		{
			$Data[$i] = array(	'Item' => $Baca	);
			$i++;
		}
		return $Data;		
	}
	
	function updateContentSetting($Data, $idContent, $vName, $vModule)
	{
		return $this->Db->sql_query("UPDATE cpcontentmodule SET vData='".$Data[0]."' WHERE idContent='".$idContent."' AND vName='".$vName."' AND vModule='".$vModule."'");
	}

	function addContentSetting($Data)
	{
		return $this->Db->sql_query("INSERT INTO cpcontentmodule VALUES (NULL,'".$Data[0]."','".$Data[1]."','".$Data[2]."','".$Data[3]."','".$Data[4]."')");
	}

	function deleteContentSetting($idContent, $vName, $vModule)
	{
		return $this->Db->sql_query("DELETE FROM cpcontentmodule WHERE idContent='".$idContent."' AND vName='".$vName."' AND vModule='".$vModule."'");
	}
	
	function deleteContentSettingByID($Id, $vModule)
	{
		return $this->Db->sql_query("DELETE FROM cpcontentmodule WHERE id='".$Id."' AND vModule='".$vModule."'");
	}
	
	function checkContentSetting($idContent,$vName,$vModule)
	{
		return $this->Db->sql_query_array("SELECT * FROM cpcontentmodule WHERE idContent='".$idContent."' AND vName='".$vName."' AND vModule='".$vModule."'");
	}
	
}
?>