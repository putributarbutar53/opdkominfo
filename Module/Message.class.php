<?php if (!defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class Message extends Core
{
	public function __construct()
	{
		parent::__construct();
	}

	function listall()
	{
		$baca = $this->Db->sql_query("SELECT * FROM cpmessage ORDER BY id ASC");
		$i = 0;
		while ($Baca = $this->Db->sql_array($baca)) {
			$Data[$i] = array(
				'Item' => $Baca
			);
			$i++;
		}

		return $Data;
	}
	function detail($Id)
	{
		return $this->Db->sql_query_array("SELECT * FROM cpmessage WHERE id='" . $Id . "'");
	}

	function delete($Id)
	{
		if ($Id == "")
			return FALSE;
		else
			return $this->Db->sql_query("DELETE FROM cpmessage WHERE id='" . $Id . "'");
	}

	function add($Data)
	{
		$Data['created_at'] = date('Y-m-d H:i:s');
		$Data['updated_at'] = date('Y-m-d H:i:s');
		return $this->Db->add($Data, "cpmessage");
	}

	function update($Data, $Id)
	{
		$Data['updated_at'] = date('Y-m-d H:i:s');
		return $this->Db->update($Data, $Id, "cpmessage");
	}

	function listPesan($Max = "3")
	{
		$_MAX = ($Max == "") ? "" : " LIMIT 0," . $Max;
		$baca = $this->Db->sql_query("SELECT * FROM cpmessage ORDER BY id DESC" . $_MAX);
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
