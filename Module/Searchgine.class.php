<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class Searchgine extends Core
{
	public function __construct()
	{
		parent::__construct();
	}

	function listSearchPages($txtSearch)
	{
		$baca = $this->Db->sql_query("SELECT * FROM cppage WHERE ((vPageName LIKE '%".$txtSearch."%') OR (tContent LIKE '%".$txtSearch."%') OR (vPermalink LIKE '%".$txtSearch."%')) ORDER BY id DESC");
		$i=0;
		while ($Baca=$this->Db->sql_array($baca)){
			$Data[$i] = $Baca;
			$i++;
		}
		return $Data;
	}

	function listSearchProduct($txtSearch)
	{
		$baca = $this->Db->sql_query("SELECT * FROM cpproductitem WHERE ((vName LIKE '%".$txtSearch."%') OR (tDetail LIKE '%".$txtSearch."%') OR (vPermalink LIKE '%".$txtSearch."%')) AND iShow='1' ORDER BY id DESC");
		$i=0;
		while ($Baca=$this->Db->sql_array($baca)){
			$Data[$i] = $Baca;
			$i++;
		}
		return $Data;
	}

	function listSearch($txtSearch, $URL, $sType="all")
	{
		$j=0;
		
		if (($sType=="all") OR ($sType=="page"))
		{
			$Search = $this->listSearchPages($txtSearch);
			for ($i=$j;$i<count($Search);$i++)
			{
				$Data[$i] = array(	"id" => $Search[$i]['id'],
									"vTitle" => preg_replace("#_#"," ",$Search[$i]['vTitle']), 
									"mtDesc" => substr(strip_tags($Search[$i]['tDescription']),0,200).".....", 
									"page" => $URL."pages/".$Search[$i]['vPermalink'].".html", 
									"Item" => $Search[$i]
						);
				$j++;
			}
		}
		
		if (($sType=="all") OR ($sType=="product"))
		{
			$Search = $this->listSearchProduct($txtSearch);
			for ($i=$j;$i<count($Search);$i++)
			{
				$Data[$i] = array(	"id" => $Search[$i]['id'],
									"vTitle" => $Search[$i]['vName'], 
									"mtDesc" => substr(strip_tags($Search[$i]['tDetail']),0,200).".....", 
									"page" => $URL."product/".$Search[$i]['vPermalink'].".html", 
									"Item" => $Search[$i]
						);
				$j++;
			}
		}
		
		return $Data;
	}
	
}
?>