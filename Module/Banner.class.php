<?php if (!defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class Banner extends Core
{
	public function __construct()
	{
		parent::__construct();
	}

	function listCategory($Max = "")
	{
		$MAX = ($Max == "") ? "" : " LIMIT 0," . $Max;
		$baca = $this->Db->sql_query("SELECT * FROM cpbannercategory ORDER BY id" . $MAX);
		$i = 0;
		while ($Baca = $this->Db->sql_array($baca)) {
			$Count = $this->Db->sql_query_array("SELECT COUNT(id) AS total FROM cpbanner WHERE idCategory='" . $Baca['id'] . "'");
			$Data[$i] = array(
				'No' => ($i + 1),
				'Item' => $Baca,
				'Count' => $Count
			);
			$i++;
		}
		return $Data;
	}

	function addCategory($Data)
	{
		return $this->Db->add($Data, "cpbannercategory");
	}

	function updateCategory($Data, $Id)
	{
		return $this->Db->update($Data, $Id, "cpbannercategory");
	}

	function countBanner($idCategory)
	{
		return $this->Db->sql_query_row("SELECT COUNT(*) AS total FROM cpbanner WHERE idCategory='" . $idCategory . "'");
	}

	function getIdBanner($vCategory)
	{
		$Baca = $this->Db->sql_query_row("SELECT id FROM cpbannercategory WHERE vCategory='" . $vCategory . "'");
		return $Baca[0];
	}

	function detailCategory($Id)
	{
		return $this->Db->sql_query_array("SELECT * FROM cpbannercategory WHERE id='" . $Id . "'");
	}

	function getBannerCategory($Id)
	{
		$Baca = $this->Db->sql_query_row("SELECT vCategory FROM cpbannercategory WHERE id='" . $Id . "'");
		return $Baca[0];
	}

	function deleteCategory($Id)
	{
		return $this->Db->sql_query("DELETE FROM cpbannercategory WHERE id='" . $Id . "'");
	}

	//Banner
	function detailBanner($Id)
	{
		return $this->Db->sql_query_array("SELECT * FROM cpbanner WHERE id='" . $Id . "'");
	}

	function deletePicture($Id)
	{
		return $this->Db->sql_query("UPDATE cpbanner SET vBannerFile='' WHERE id='" . $Id . "'");
	}

	function status($iShow, $idBanner)
	{
		return $this->Db->sql_query("UPDATE cpbanner SET iStatus='" . $iShow . "' WHERE id='" . $idBanner . "'");
	}

	function showBanner($Id)
	{
		return $this->Db->sql_query("UPDATE cpbanner SET iShow='1' WHERE id='" . $Id . "'");
	}

	function hideBanner($Id)
	{
		return $this->Db->sql_query("UPDATE cpbanner SET iShow=0 WHERE id='" . $Id . "'");
	}

	function listBannerStatus()
	{
		$baca = $this->Db->sql_query("SELECT * FROM cpbannerstatus ORDER BY id ASC");
		$i = 0;
		while ($Baca = $this->Db->sql_array($baca)) {
			$Data[$i] = array('Item' => $Baca, 'totalBanner' => $this->countBanner($Baca[id]));
			$i++;
		}

		return $Data;
	}

	function addBanner($Data)
	{
		return $this->Db->add($Data, "cpbanner");
	}

	function deleteBanner($Id)
	{
		return $this->Db->sql_query("DELETE FROM cpbanner WHERE id='" . $Id . "'");
	}

	function updateBanner($Data, $Id)
	{
		return $this->Db->update($Data, $Id, "cpbanner");
	}

	function listBanner($idCategory, $Max = "10")
	{
		$_MAX = ($Max == "") ? "" : " LIMIT 0," . $Max;
		$baca = $this->Db->sql_query("SELECT * FROM cpbanner WHERE idCategory='" . $idCategory . "' ORDER BY id DESC" . $_MAX);
		$i = 0;
		while ($Baca = $this->Db->sql_array($baca)) {
			$data[$i] = array(
				'No' => ($i + 1),
				'Item' => $Baca
			);
			$i++;
		}
		return $data;
	}
}
