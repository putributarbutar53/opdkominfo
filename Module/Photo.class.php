<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class Photo extends Core
{
	public function __construct()
	{
		parent::__construct();
	}
	
	//Album
	function addAlbum($Data)
	{
		return $this->Db->add($Data, "cpphotoalbum");
	}

	function updateAlbum($Data, $Id)
	{
		return $this->Db->update($Data, $Id, "cpphotoalbum");
	}

	function detailAlbum($Id)
	{
		return $this->Db->sql_query_array("SELECT * FROM cpphotoalbum WHERE id='".$Id."'");
	}

	function deleteAlbum($Id)
	{
		$Count=$this->Db->sql_query_row("SELECT COUNT(*) FROM cpphoto WHERE idAlbum='".$Id."'");
		if ($Count[0]==0)
			return $this->Db->sql_query("DELETE FROM cpphotoalbum WHERE id='".$Id."'");
	}

	function detailAlbumByPermalink($vPermalink)
	{
		return $this->Db->sql_query_array("SELECT * FROM cpphotoalbum WHERE vPermalink='".$vPermalink."'");
	}

	function listAlbum()
	{
		$baca=$this->Db->sql_query("SELECT * FROM cpphotoalbum ORDER BY id ASC");

		$i=0;
		while ($Baca=$this->Db->sql_array($baca))
		{
			$data[$i] = array(		'Item' => $Baca,
									'Count' => $this->countPhoto($Baca[id])
								);
			$i++;
		}
		return $data;
	}

	function getFirstAlbum($idCategory="")
	{
		if ($idCategory=="")
			return $this->Db->sql_query_array("SELECT * FROM cpphotoalbum ORDER BY id ASC");
		else
			return $this->Db->sql_query_array("SELECT * FROM cpphotoalbum WHERE idCategory='".$idCategory."' ORDER BY id ASC");
	}
	
	function getPrimary($idAlbum)
	{
		return $this->Db->sql_query_array("SELECT * FROM cpphoto WHERE idAlbum='".$idAlbum."' AND iPrimary='1'");
	}

	//Photo	
	function addPhoto($Data)
	{
		return $this->Db->add($Data, "cpphoto");
	}
		
	function updatePhoto($Data, $Id)
	{
		return $this->Db->update($Data, $Id, "cpphoto");
	}

	function deletePhoto($Id)
	{
		return $this->Db->sql_query("DELETE FROM cpphoto WHERE id='".$Id."'");
	}
	
	function detailPhoto($Id)
	{
		return $this->Db->sql_query_array("SELECT * FROM cpphoto WHERE id='".$Id."'");
	}

	function detailPhotoByPermalink($vPermalink)
	{
		return $this->Db->sql_query_array("SELECT * FROM cpphoto WHERE vPermalink='".$vPermalink."'");
	}

	function listPhoto($idAlbum, $Max="10")
	{
		$_MAX = ($Max=="")?"":" LIMIT 0,".$Max;
		$baca=$this->Db->sql_query("SELECT * FROM cpphoto WHERE idAlbum='".$idAlbum."' ORDER BY id DESC".$_MAX);
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

	function listLastPhoto($Sort="", $Max="")
	{
		$MAX = ($Max=="")?"":" LIMIT 0, ".$Max;
		if ($Sort=="")
			$baca = $this->Db->sql_query("SELECT * FROM cpphoto ORDER BY id DESC".$MAX);
		else
			$baca = $this->Db->sql_query("SELECT * FROM cpphoto".$Sort.$MAX);
		
		$i=0;
		while ($Baca=$this->Db->sql_array($baca))
		{
			$Data[$i] = array(
				'No' => ($i+1), 
				'Item' => $Baca
			);
			$i++;
		}
		return $Data;
	}
	
	function list_LastPhoto($idCategory="", $idAlbum="", $Sort="", $Max="")
	{
		$MAX = ($Max=="")?"":" LIMIT 0, ".$Max;
		$SORT = ($Sort=="")?" ORDER BY id DESC":$Sort;
		$CATEGORY = ($idCategory=="")?"":" AND idCategory='".$idCategory."'";
		$ALBUM = ($idAlbum=="")?"":" AND idAlbum='".$idAlbum."'";
		
		$baca = $this->Db->sql_query("SELECT * FROM cpphoto WHERE id!='0'".$CATEGORY.$ALBUM.$Sort.$MAX);
		$i=0;
		while ($Baca=$this->Db->sql_array($baca))
		{
			$Data[$i] = array(
				'No' => ($i+1), 
				'Item' => $Baca
			);
			$i++;
		}
		return $Data;
	}

	function listByAlbum($vPermalink, $Max="", $Sort="")
	{
		$detailAlbum = $this->detailAlbumByPermalink($vPermalink);
		$MAX = ($Max=="")?"":" LIMIT 0, ".$Max;
		$SORT = ($Sort=="")?" ORDER BY id DESC":$Sort;
		
		$baca = $this->Db->sql_query("SELECT * FROM cpphoto WHERE id!='0' AND idAlbum='".$detailAlbum['id']."'".$CATEGORY.$ALBUM.$Sort.$MAX);
		$i=0;
		while ($Baca=$this->Db->sql_array($baca))
		{
			$Data[$i] = array(
				'No' => ($i+1), 
				'Item' => $Baca
			);
			$i++;
		}
		return $Data;
	}

	function getFirstPhoto($idCategory="",$idAlbum="")
	{
		if (($idCategory=="") OR ($idAlbum==""))
			return $this->Db->sql_query_array("SELECT * FROM cpphoto ORDER BY id ASC");
		else
			return $this->Db->sql_query_array("SELECT * FROM cpphoto WHERE idCategory='".$idCategory."' AND idAlbum='".$idAlbum."' ORDER BY id ASC");
	}

	function updateHits($Id)
	{
		$Detail = $this->detailPhoto($Id);
		return $this->Db->sql_query("UPDATE cpphoto SET iHits='".($Detail[iHits]+1)."' WHERE id='".$Id."'");
	}

	function countPhoto($idAlbum="")
	{
		if ($idAlbum=="")
			return $this->Db->sql_query_array("SELECT COUNT(*) AS total FROM cpphoto");
		else
			return $this->Db->sql_query_array("SELECT COUNT(*) AS total FROM cpphoto WHERE idAlbum='".$idAlbum."'");
	}

	function listPhotoByTable($listData,$tblConf,$Cols)
	{
		/*
			Width 		= $tblConf[0]
			Cellpadding = $tblConf[1]
			Cellspacing = $tblConf[2]
			Border 		= $tblConf[3]
			Table Class	= $tblConf[4]
			TD Class	= $tblConf[5]
			URL Class	= $tblConf[6]
		*/
		//check if there is no coloums configuration
		$Cols = ($Cols=="")?2:$Cols;
		$Data = $listData;
		$totalRows = count($Data);
		$Rows = ($totalRows > ($Cols-1))?ceil($totalRows / $Cols):1;
		$DataPages = "<table cellpadding=\"".$tblConf[1]."\" cellspacing=\"".$tblConf[2]."\" width=\"".$tblConf[0]."\" border=\"".$tblConf[3]."\">";
		$r=0;
		for ($i=0;$i<$Rows;$i++)
		{
			$DataPages .= "<tr>";
			for ($j=0;$j<$Cols;$j++)
			{	
				if ($Data[$r]['Item']['id']=="")
					$dataTemp = "&nbsp;";
				else
					$dataTemp = ($Data[$r]['Item']['vThumbName']=="")?"&nbsp;":"<a href=\"".$this->Db->App['main']['url'].$this->Db->App['index']['page']."picture/".$Data[$r]['Item']['vPermalink'].".html\"><img style='border-color:#CCCCCC' border=1 src=\"".$this->Db->App['main']['url'].$this->Db->App['photo']['dir'].$Data[$r]['Item']['vThumbName']."\" border=0></a><br />".substr($Data[$r]['Item']['vPhotoTitle'],0,120);
				
				$DataPages .= "<td class=\"".$tblConf[5]."\">".$dataTemp."</td>";
				$r++;
			}
			$DataPages .= "</tr>";
		}
	
		$DataPages .= "</table>";
		return $DataPages;
	}

	function galleryList($listData,$tblConf,$Cols,$Show=0)
	{
		/*
			Width 		= $tblConf[0]
			Cellpadding = $tblConf[1]
			Cellspacing = $tblConf[2]
			Border 		= $tblConf[3]
			Table Class	= $tblConf[4]
			TD Class	= $tblConf[5]
			URL Class	= $tblConf[6]
		*/
		//check if there is no coloums configuration
		$Cols = ($Cols=="")?2:$Cols;
		$Data = $listData;
		$totalRows = count($Data);
		$Rows = ($totalRows > ($Cols-1))?ceil($totalRows / $Cols):1;
		$DataPages = "<table cellpadding=\"".$tblConf[1]."\" cellspacing=\"".$tblConf[2]."\" width=\"".$tblConf[0]."\" border=\"".$tblConf[3]."\">";
		$r=0;
		for ($i=0;$i<$Rows;$i++)
		{
			$DataPages .= "<tr>";
			for ($j=0;$j<$Cols;$j++)
			{	
				if ($Data[$r]['Item']['id']=="")
					$dataTemp = "&nbsp;";
				else
				{
					$PhotoList = ($Data[$r]['Item']['vThumbName']=="")?"<img style='border-color:#CCCCCC' border=1 src=\"images/photo_album.png\" border=0>":"<img style='border-color:#CCCCCC' border=1 src=\"".$this->Db->App['main']['url'].$this->Db->App['photo']['dir'].$Data[$r]['Item']['vThumbName']."\" border=0>";

					if ($Show=="1")
						$dataTemp = ($Data[$r]['Item']['id']=="")?"&nbsp;":"<a href=\"".$this->Db->App['main']['url'].$this->Db->App['photo']['dir'].$Data[$r]['Item']['vPhotoName']."\" rel=\"lightbox[".$Data[$r]['Item']['idAlbum']."]\" title=\"".$Data[$r]['Item']['mtDesc']."\" rev=\"\">".$PhotoList."</a><br />".substr($Data[$r]['Item']['vPhotoTitle'],0,120);
					else
						$dataTemp = ($Data[$r]['Item']['id']=="")?"&nbsp;":"<a href=\"".$this->Db->App['main']['url'].$this->Db->App['index']['page']."picture/".$Data[$r]['Item']['vPermalink'].".html\">".$PhotoList."</a><br />".substr($Data[$r]['Item']['vPhotoTitle'],0,120);
				}
				
				$DataPages .= "<td class=\"".$tblConf[5]."\">".$dataTemp."</td>";
				$r++;
			}
			$DataPages .= "</tr>";
		}
	
		$DataPages .= "</table>";
		return $DataPages;
	}

}
?>