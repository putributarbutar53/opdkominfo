<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class Document extends Core
{
	public function __construct()
	{
		parent::__construct();
	}
	
	function addCategory($Data)
	{
		return $this->Db->add($Data, "cpdoccategory");
	}
	
	function updateCategory($Data, $Id)
	{
		return $this->Db->update($Data, $Id, "cpdoccategory");
	}

	function deleteCategory($Id)
	{
		$Count=$this->Db->sql_query_row("SELECT COUNT(id) FROM cpdoc WHERE idCategory='".$Id."'");
		if ($Count[0]==0)
			return $this->Db->sql_query("DELETE FROM cpdoccategory WHERE id='".$Id."'");
	}
	
	function detailCategory($Id)
	{	
		return $this->Db->sql_query_array("SELECT * FROM cpdoccategory WHERE id='".$Id."'");
	}

	function detailCategoryByPermalink($vPermalink)
	{	
		return $this->Db->sql_query_array("SELECT * FROM cpdoccategory WHERE vPermalink='".$vPermalink."'");
	}
	
	function listCategory($Max="")
	{
		$MAX = ($Max=="")?"":" LIMIT 0,".$Max;
		$baca=$this->Db->sql_query("SELECT * FROM cpdoccategory ORDER BY id".$MAX);							
		$i=0;
		while ($Baca=$this->Db->sql_array($baca))
		{
			$Count=$this->countDoc($Baca['id']);
			$Data[$i] = array(	
				'No' => ($i+1), 
				'Item' => $Baca, 
				'Count' => $Count
			);
			$i++;
		}
		return $Data;
	}
	
	//Document
	function countDoc($Id)
	{
		return $this->Db->sql_query_array("SELECT COUNT(id) AS total FROM cpdoc WHERE idCategory='".$Id."'");
	}

	function detailDoc($Id)
	{
		return $this->Db->sql_query_array("SELECT * FROM cpdoc WHERE id='".$Id."'");
	}
	
	function addDoc($Data)
	{
		return $this->Db->add($Data, "cpdoc");
	}
	
	function deleteDoc($Id)
	{
		return $this->Db->sql_query("DELETE FROM cpdoc WHERE id='".$Id."'");
	}
		
	function updateDoc($Data, $Id)
	{
		return $this->Db->update($Data, $Id, "cpdoc");
	}
	
	function listDoc($idCategory, $Max="10")
	{
		$_MAX = ($Max=="")?"":" LIMIT 0,".$Max;
		$baca=$this->Db->sql_query("SELECT * FROM cpdoc WHERE idCategory='".$idCategory."' ORDER BY id DESC".$_MAX);
		$i=0;
		while ($Baca=$this->Db->sql_array($baca))
		{
			$data[$i] = array(		'No' => ($i+1),
									'Item' => $Baca
								);
			$i++;
		}
		return $data;
	
	}	

	function listAllDoc($idCategory)
	{
		$baca=$this->Db->sql_query("SELECT * FROM cpdoc WHERE idCategory='".$idCategory."' ORDER BY id DESC");
		$i=0;
		while ($Baca=$this->Db->sql_array($baca))
		{
			$data[$i] = array(		'No' => ($i+1),
									'Item' => $Baca
								);
			$i++;
		}
		return $data;
	
	}	

}
?>