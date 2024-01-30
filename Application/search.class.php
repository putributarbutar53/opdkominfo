<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class search extends Core
{
	var $mUsername, $detailMember;
	function __construct()
	{
		parent::__construct();

		$this->LoadModule("Content");
		$this->LoadModule("Page");
		$this->LoadModule("Options");
		$this->LoadModule("Paging");
		$this->Module->Paging->setPaging(20,10,"&laquo; Prev","Next &raquo;");

		$this->Template->assign("dirContent", $this->Config['content']['dir']);

		//Load General Process
		include '../inc/general.php';
	}
	
	//Search Website
	function main()
	{		
		$this->Template->assign("SIGNATURE", "search");
		$this->LoadModule("Searchgine");
		$Submit = ($_POST['submit'])?$_POST['submit']:$_GET['submit'];
		$Submit = filter_var($Submit, FILTER_SANITIZE_STRING);
		
		$this->Module->Paging->URL = $this->Config['base']['url'].$this->Config['index']['page']."search";
		
		$txtSearch = ($_POST['txtSearch']=="")?$_GET['search']:$_POST['txtSearch'];
		$txtSearch = filter_var($txtSearch, FILTER_SANITIZE_STRING);
		$this->Template->assign("txtSearch", $txtSearch);
		
		$this->Template->assign("METATITLE", (($txtSearch=="")?"Cari Di Website":"Hasil pencarian di website: ".$txtSearch));
		$this->Template->assign("METADESC", (($txtSearch=="")?"":"Hasil pencarian: ".$txtSearch.". ")."Anda dapat mencari dengan mudah informasi yang tersedia di website ini");
		$this->Template->assign("METAKEYWORD", "pencarian");
		
		if (($Submit) AND ($txtSearch!=""))
		{
			$baca = $this->Db->sql_query("SELECT * FROM cpcontent WHERE id!='0' AND (vTitle LIKE '%".$txtSearch."%' OR tDetail LIKE '%".$txtSearch."%') ORDER BY id DESC");
			$i=0;
			while ($Baca=$this->Db->sql_array($baca)) {
				$Data[$i] = array(	
					'No' => ($i+1),	
					'Item' => $Baca, 
					'Category' => $this->Module->Content->detailDir($Baca['idCategory']),
					'type' => 'content'
				);
				$i++;
			}

			$baca = $this->Db->sql_query("SELECT * FROM cppage WHERE id!='0' AND (vPageName LIKE '%".$txtSearch."%' OR tContent LIKE '%".$txtSearch."%') AND iTopMenu!='0' ORDER BY id DESC");
			while ($Baca=$this->Db->sql_array($baca)) {
				$Data[$i] = array(	
					'No' => ($i+1),	
					'Item' => $Baca, 
					'Category' => '',
					'type' => 'page'
				);
				$i++;
			}

			$this->Template->assign("list", $Data);
		}
		
		echo $this->Template->Show("search.html");
	}

}

?>