<?php if (!defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class Page extends Core
{
	public function __construct()
	{
		parent::__construct();
	}

	//Page Status
	function addCategory($Data)
	{
		return $this->Db->add($Data, "cppagecategory");
	}

	function updateCategory($Data, $Id)
	{
		return $this->Db->update($Data, $Id, "cppagecategory");
	}

	function deleteCategory($id)
	{
		$Count = $this->Db->sql_query_row("SELECT COUNT(id) FROM cppage WHERE idCategory='" . $id . "'");
		if ($Count[0] <= 0)
			return $this->Db->sql_query("DELETE FROM cppagecategory WHERE id='" . $id . "'");
		else
			return false;
	}

	function getIDCategory($vCategory)
	{
		$Baca = $this->Db->sql_query_array("SELECT id FROM cppagecategory WHERE vCategory='" . $vCategory . "'");
		return $Baca['id'];
	}

	function listCategory($Max = "all")
	{
		if ($Max == "all")
			$baca = $this->Db->sql_query("SELECT * FROM cppagecategory WHERE id!='0'");
		else
			$baca = $this->Db->sql_query("SELECT * FROM cppagecategory WHERE id!='0' LIMIT 0," . $Max);

		$i = 0;
		while ($Baca = $this->Db->sql_array($baca)) {
			$Count = $this->Db->sql_query_array("SELECT COUNT(id) AS total FROM cppage WHERE idCategory='" . $Baca['id'] . "'");
			$Data[$i] = array(
				'id' => $Baca['id'],
				'Item' => $Baca,
				'Count' => $Count
			);
			$i++;
		}
		return $Data;
	}

	function detailCategory($Id)
	{
		return $this->Db->sql_query_array("SELECT * FROM cppagecategory WHERE id='" . $Id . "'");
	}

	function detailCategoryByPermalink($vPermalink)
	{
		return $this->Db->sql_query_array("SELECT * FROM cppagecategory WHERE vPermalink='" . $vPermalink . "'");
	}

	//Page
	function addPage($Data, $idCategory)
	{
		if ($this->Db->add($Data, "cppage")) {
			$detailCategory = $this->detailCategory($idCategory);
			$lastId = $this->Db->sql_query_row("SELECT id FROM cppage ORDER BY id DESC");
			return $this->addPageConf(array(
				'idPage' => $lastId[0],
				'iAddPage' => $detailCategory['iAddPage'],
				'iPictureIcon' => $detailCategory['iPictureIcon'],
				'iContent' => $detailCategory['iContent'],
				'iMenuURL' => $detailCategory['iMenuURL'],
				'iLinkTarget' => $detailCategory['iLinkTarget'],
				'iEditor' => $detailCategory['iEditor'],
				'iModule' => $detailCategory['iModule'],
				'iMeta' => $detailCategory['iMeta'],
				'iLiveEditor' => $detailCategory['iLiveEditor']
			));
		}
	}

	function addSubPage($Data, $iTopMenu)
	{
		if ($this->Db->add($Data, "cppage")) {
			$lastId = $this->Db->sql_query_row("SELECT id FROM cppage ORDER BY id DESC");
			$detailConf = $this->detailPageConf($iTopMenu);
			return $this->addPageConf(array(
				'idPage' => $lastId[0],
				'iAddPage' => $detailConf['iAddPage'],
				'iPictureIcon' => $detailConf['iPictureIcon'],
				'iContent' => $detailConf['iContent'],
				'iMenuURL' => $detailConf['iMenuURL'],
				'iLinkTarget' => $detailConf['iLinkTarget'],
				'iEditor' => $detailConf['iEditor'],
				'iModule' => $detailConf['iModule'],
				'iMeta' => $detailConf['iMeta'],
				'iLiveEditor' => $detailConf['iLiveEditor']
			));
		}
	}

	function updatePage($Data, $Id)
	{
		return $this->Db->update($Data, $Id, "cppage");
	}

	function deletePicture($Id)
	{
		return $this->Db->sql_query("UPDATE cppage SET lbPicture='' WHERE id='" . $Id . "'");
	}

	function setShow($iShow, $Id)
	{
		return $this->Db->sql_query("UPDATE cppage SET iShow='" . $iShow . "' WHERE id='" . $Id . "'");
	}

	function getStatusName($Id)
	{
		$Baca = $this->Db->sql_query_row("SELECT vPageStatus FROM cppagestatus WHERE id='" . $Id . "'");
		return $Baca[0];
	}

	function optionPageStatus($cS)
	{
		$Temp = "<select name=\"cStatus\">";

		$baca = $this->Db->sql_query("SELECT * FROM cppagestatus ORDER BY id ASC");
		while ($Baca = $this->Db->sql_array($baca)) {
			$Selected = ($cS == $Baca[id]) ? " selected" : "";

			$Temp .= "<option" . $Selected . " value=" . $Baca['id'] . ">" . $Baca['vPageStatus'] . "</option>";
		}
		$Temp .= "</select>";
		return $Temp;
	}

	function detailPage($Id)
	{
		return $this->Db->sql_query_array("SELECT * FROM cppage WHERE id='" . $Id . "'");
	}

	function detailPageByPermalink($vPermalink)
	{
		return $this->Db->sql_query_array("SELECT * FROM cppage WHERE vPermalink='" . $vPermalink . "'");
	}

	function setDefault($iDefault, $idPage)
	{
		if ($iDefault == "1") {
			$detailPage = $this->detailPage($idPage);
			//Set All Category as Non Default
			$this->Db->sql_query("UPDATE cppage SET iDefault='0' WHERE idCategory='" . $detailPage['idCategory'] . "'");
		}
		return $this->Db->sql_query("UPDATE cppage SET iDefault='" . $iDefault . "' WHERE id='" . $idPage . "'");
	}

	function pageDetail($Id)
	{
		return $this->Db->sql_query_array("SELECT * FROM cppage WHERE id='" . $Id . "'");
	}

	function getPageId($Status, $iTopMenu)
	{
		$Baca = $this->Db->sql_query_array("SELECT id FROM cppage WHERE iTopMenu='" . $iTopMenu . "' AND cStatus='" . $Status . "'");
		return $Baca['id'];
	}

	function deletePage($Id)
	{
		$this->Db->sql_query("DELETE FROM cppageconf WHERE idPage='" . $Id . "'");
		return $this->Db->sql_query("DELETE FROM cppage WHERE id='" . $Id . "'");
	}

	function checkTopDirectory($iTopMenu, $cStatus)
	{
		$myTopMenu = $iTopMenu;
		$j = 0;
		do {
			$Baca = $this->Db->sql_query_array("SELECT id,iTopMenu,vPageName FROM cppage WHERE id='" . $myTopMenu . "'");
			$myTopMenu = $Baca['iTopMenu'];
			$Link[$j] = $Baca['vPageName'];
			$iTop[$j] = $Baca['id'];
			$j++;
		} while ($myTopMenu != 0);

		for ($i = 0; $i <= $j; $i++) {
			if ($Link[$j - $i])
				$Temp .= " &raquo; <a href=\"" . $this->Config['admin']['url'] . "mypage/item?category=" . $vCategory . "&itopmenu=" . $iTop[$j - $i] . "\">" . $Link[$j - $i] . "</a>";
		}
		return $Temp;
	}

	function submenuDirectory($iTopMenu, $vCategory)
	{
		$myTopMenu = $iTopMenu;
		$j = 0;
		do {
			$Baca = $this->Db->sql_query_array("SELECT id,iTopMenu,vPageName FROM cppage WHERE id='" . $myTopMenu . "'");
			$myTopMenu = $Baca['iTopMenu'];
			$Link[$j] = $Baca['vPageName'];
			$iTop[$j] = $Baca['id'];
			$j++;
		} while ($myTopMenu != 0);

		for ($i = 0; $i <= $j; $i++) {
			if ($Link[$j - $i])
				$Temp .= "<li><span><a href=page.php?dir=item&category=" . $vCategory . "&itopmenu=" . $iTop[$j - $i] . ">" . $Link[$j - $i] . "</a></span></li>";
		}

		return $Temp;
	}

	function listSlidePage($vCategory, $iShow = "all")
	{
		$SHOW = ($iShow == "all") ? "" : " AND iShow='" . $iShow . "'";
		$baca = $this->Db->sql_query("SELECT * FROM cppage WHERE iTopMenu=0 AND idCategory='" . $this->getIDCategory($vCategory) . "'" . $SHOW . " ORDER BY iUrutan ASC, id ASC");
		$i = 0;
		while ($Baca = $this->Db->sql_array($baca)) {
			$Data[$i] = array(
				'Item' => $Baca,
				'totalTopMenu' => $this->countTopMenu($Baca['id'])
			);
			$i++;
		}

		return $Data;
	}

	function countPage($idCategory)
	{
		return $this->Db->sql_query_array("SELECT COUNT(*) AS total FROM cppage WHERE idCategory='" . $idCategory . "'");
	}

	function getIdPage($vPageName)
	{
		$Baca = $this->Db->sql_query_array("SELECT id FROM cppage WHERE vPageName='" . $vPageName . "'");
		return ($Baca['id']) ? $Baca['id'] : 0;
	}

	function getLastPage()
	{
		return $this->Db->sql_query_array("SELECT * FROM cppage ORDER BY id DESC");
	}

	function getDefaultPage($idCategory)
	{
		return $this->Db->sql_query_array("SELECT * FROM cppage WHERE idCategory='" . $idCategory . "' AND iDefault='1' ORDER BY id DESC");
	}

	function checkITopMenu($Id)
	{
		$Baca = $this->Db->sql_query_row("SELECT COUNT(id) FROM cppage WHERE iTopMenu='" . $Id . "'");
		return $Baca[0];
	}

	function listPage($Max = "", $iTMenu = "", $idCategory = "")
	{
		//list page, showing the list of pages
		//tampilkan halaman yang tidak merupakan cabang dari file lainnya :)
		$uLink = ($Max == "") ? "" : " LIMIT 0," . $Max;
		$iTopLink = ($iTMenu == "") ? "" : " AND iTopMenu='" . $iTMenu . "'";
		$Category_Link = ($idCategory == "") ? "" : " AND idCategory='" . $idCategory . "'";
		$baca = $this->Db->sql_query("SELECT * FROM cppage WHERE id!='0'" . $iTopLink . $Category_Link . " ORDER BY iUrutan ASC, id ASC" . $uLink);

		$i = 0;
		while ($Baca = $this->Db->sql_array($baca)) {
			$topMenu = $this->checkITopMenu($Baca['id']);
			$linkDeleted = ($topMenu > 0) ? "no" : "yes";
			$linkAddPage = ($topMenu > 0) ? "no" : "yes";

			$Data[$i] = array(
				'No' => ($i + 1),
				'Item' => $Baca,
				'linkDeleted' => $linkDeleted,
				'addPage' => $linkAddPage,
				'iUrutan' => $Baca['iUrutan'],
				'Sub' => number_format($topMenu)
			);
			$i++;
		}

		return $Data;
	}

	function listPages($idCategory, $MAX = "all")
	{
		if (($MAX == "all") or ($MAX == ""))
			$baca = $this->Db->sql_query("SELECT * FROM cppage WHERE idCategory='" . $idCategory . "' AND iTopMenu='0' AND iShow='1' ORDER BY iUrutan ASC, id ASC");
		else
			$baca = $this->Db->sql_query("SELECT * FROM cppage WHERE idCategory='" . $idCategory . "' AND iTopMenu='0' AND iShow='1' ORDER BY iUrutan ASC, id ASC LIMIT 0," . $MAX);

		$i = 0;
		while ($Baca = $this->Db->sql_array($baca)) {
			$Data[$i] = array('Item' => $Baca);
			$i++;
		}

		return $Data;
	}

	function listAllPages($idCategory)
	{
		$baca = $this->Db->sql_query("SELECT * FROM cppage WHERE idCategory='" . $idCategory . "' AND iTopMenu='0' ORDER BY iUrutan ASC, id ASC");
		$Data = "";
		while ($Baca = $this->Db->sql_array($baca)) {
			$_Urutan = ($Baca['iUrutan'] > 0) ? "(" . $Baca['iUrutan'] . ")&nbsp;" : "";
			$_Draft = ($Baca['iShow'] == "0") ? "&nbsp;&nbsp;<span class=\"text-danger h6\"><i>(draft)</i></span>" : "";

			$topMenu = $this->checkITopMenu($Baca['id']);
			if ($topMenu > '0') {
				$Data .= "<li>" . $_Urutan;
				$Data .= "<i class=\"far fa-folder-open ic-w mx-1\"></i>&nbsp;<a href=\"javascript:editdata(" . $Baca['id'] . ");\">" . $Baca['vPageName'] . "</a>" . $_Draft . "&nbsp;&nbsp;";

				if ($Baca['iDefault'] == "1") {
					$Data .= "<a href=\"javascript:setdefault('0','" . $Baca['id'] . "')\" class=\"text-danger\"><i class=\"fas fa-tag\"></i></a>";
				} else {
					$Data .= "<a href=\"javascript:setdefault('1','" . $Baca['id'] . "')\" class=\"text-400\"><i class=\"fas fa-tag\"></i></a>";
				}

				$Data .= $this->listAllSubPage($Baca['id']);
				$Data .= "</li>";
			} else {
				$Data .= "<li>" . $_Urutan . "<i class=\"far fa-file-alt ic-w mr-1\"></i>&nbsp;<a href=\"javascript:editdata(" . $Baca['id'] . ");\">" . $Baca['vPageName'] . "</a>";
				if ($Baca['iDefault'] == "1") {
					$Data .= "&nbsp;&nbsp;<a href=\"javascript:setdefault('0','" . $Baca['id'] . "')\" class=\"text-danger\"><i class=\"fas fa-tag\"></i></a>";
				} else {
					$Data .= "&nbsp;&nbsp;<a href=\"javascript:setdefault('1','" . $Baca['id'] . "')\" class=\"text-400\"><i class=\"fas fa-tag\"></i></a>";
				}
				$Data .= "&nbsp;&nbsp;<a href=\"javascript:deletedata(" . $Baca['id'] . ")\"><i class='fas fa-trash-alt h6'></i></a>" . $_Draft;
				$Data .= "</li>";
			}
		}

		return $Data;
	}

	function listAllSubPage($iTopMenu)
	{
		$baca = $this->Db->sql_query("SELECT * FROM cppage WHERE iTopMenu='" . $iTopMenu . "' ORDER BY iUrutan ASC, id ASC");

		$Data .= "<ul class=\"opened\">";
		while ($Baca = $this->Db->sql_array($baca)) {
			$_Urutan = ($Baca['iUrutan'] > 0) ? "(" . $Baca['iUrutan'] . ")&nbsp;" : "";
			$_Draft = ($Baca['iShow'] == "0") ? "&nbsp;&nbsp;<span class=\"text-danger h6\"><i>(draft)</i></span>" : "";

			$topMenu = $this->checkITopMenu($Baca['id']);
			if ($topMenu > '0') {
				$Data .= "<li>" . $_Urutan;
				$Data .= "<i class=\"far fa-folder-open ic-w mx-1\"></i>&nbsp;<a href=\"javascript:editdata(" . $Baca['id'] . ");\">" . $Baca['vPageName'] . "</a>" . $_Draft;
				$Data .= $this->listAllSubPage($Baca['id']);
				$Data .= "</li>";
			} else {
				$Data .= "<li>" . $_Urutan . "<i class=\"far fa-file-alt ic-w mr-1\"></i>&nbsp;<a href=\"javascript:editdata(" . $Baca['id'] . ");\">" . $Baca['vPageName'] . "</a>";
				$Data .= "&nbsp;&nbsp;<a href=\"javascript:deletedata(" . $Baca['id'] . ")\"><i class='fas fa-trash-alt h6'></i></a>" . $_Draft;
				$Data .= "</li>";
			}
		}
		$Data .= "</ul>";

		return $Data;
	}

	function listSubPage($iTopMenu, $Max = "")
	{
		$MAX_ = ($Max == "") ? "" : " LIMIT 0, " . $Max;
		$baca = $this->Db->sql_query("SELECT * FROM cppage WHERE iTopMenu='" . $iTopMenu . "' ORDER BY iUrutan ASC, id ASC" . $MAX_);
		$i = 0;
		while ($Baca = $this->Db->sql_array($baca)) {
			if ($this->checkITopMenu($Baca['id'])) {
				$Data[$i] = $Baca;
				$i++;
			}
		}
		return $Data;
	}

	function list_SubPage($iTopMenu, $Max = "")
	{
		$MAX_ = ($Max == "") ? "" : " LIMIT 0, " . $Max;
		$baca = $this->Db->sql_query("SELECT * FROM cppage WHERE iTopMenu='" . $iTopMenu . "' ORDER BY iUrutan ASC, id ASC" . $MAX_);
		$i = 0;
		while ($Baca = $this->Db->sql_array($baca)) {
			$Data[$i] = array(
				'Item' => $Baca,
				'totalTopMenu' => $this->countTopMenu($Baca['id'])
			);
			$i++;
		}
		return $Data;
	}

	function listIPage($iTopMenu)
	{
		$baca = $this->Db->sql_query("SELECT * FROM cppage WHERE iTopMenu='" . $iTopMenu . "' ORDER BY iUrutan ASC, id ASC");
		$i = 0;
		while ($Baca = $this->Db->sql_array($baca)) {
			if (!($this->checkITopMenu($Baca['id']))) {
				$Data[$i] = $Baca;
				$i++;
			}
		}
		return $Data;
	}

	function listITopMenu($Id, $noID = NULL)
	{
		if ($this->countTopMenu($Id) != 0) {
			$baca = $this->Db->sql_query("SELECT * FROM cppage WHERE iTopMenu='" . $Id . "' ORDER BY iUrutan ASC, id ASC");
			$j = 0;
			while ($Baca = $this->Db->sql_array($baca)) {
				if ($Baca['id'] != $noID) {
					$Data[$j] = array('Item' => $Baca);
					$j++;
				}
			}
			return $Data;
		} else return array();
	}

	function listSubGambar($Id)
	{
		if ($this->countTopMenu($Id) != 0) {
			$baca = $this->Db->sql_query("SELECT * FROM cppage WHERE iTopMenu='" . $Id . "' ORDER BY iUrutan ASC, id ASC");
			$i = 0;
			while ($Baca = $this->Db->sql_array($baca)) {
				$Data[$i] = array(
					'No' => ($i + 1),
					'Item' => $Baca
				);
				$i++;
			}
			return $Data;
		} else return "";
	}

	function resultSubInTable($listPage, $Colom)
	{
		$listPage_ = $listPage;
		$countPage = count($listPage_);
		$Temp = "<table cellpadding=6 cellspacing=6 border=0 with=100%>";
		$j = 0;
		for ($i = 0; $i < $countPage; $i++) {
			if ($j == 0) {
				$Temp .= "<tr>";
			}

			$Colspan = ($i == ($countPage - 1)) ? " colspan=\"" . ($Colom - $j) . "\"" : "";

			$URL = ($listPage_[$i]['Item'][vURL] == "") ? $this->Config['base']['url'] . $this->Config['index']['page'] . "index/page?id=" . $listPage_[$i]['Item'][id] . "&idSub=" . $idSub : $listPage_[$i]['Item'][vURL];

			$Temp .= "<td class=font_8 valign=top" . $Colspan . ">";

			$PICTURE = ($listPage_[$i]['Item'][lbPicture] == "") ? "images/no_picture.jpg" : $this->Db->App['pages']['dir'] . $listPage_[$i]['Item'][lbPicture];

			$Temp .= "<a href=" . $URL . "><img src=\"" . $PICTURE . "\" border=0 style=\"border:2px solid #FFFFFF\" align=left></a>";
			$Temp .= "<font size=2 color=#FF9900><b><a href=" . $URL . " class=main>" . $listPage_[$i]['Item'][vPageName] . "</a></b></font><br />" . substr($listPage_[$i]['Item'][tContent], 0, 200) . "...";
			$Temp .= "</td>";

			$j++;

			if (($j == $Colom) or ($i == ($countPage - 1))) {
				$Temp .= "</tr>";
				$j = 0;
			}
		}
		$Temp .= "</table>";
		return $Temp;
	}

	function listSubPage_($vName)
	{
		$baca = $this->Db->sql_query("SELECT * FROM cppage WHERE iTopMenu='" . $this->getIdPage($vName) . "' ORDER BY iUrutan ASC, id ASC");
		while ($Baca = $this->Db->sql_array($baca)) {
			$tempData = $Baca;
			$Data[$Baca['vPageName']] = array(
				'Item' => $tempData,
				'tContent' => $tempData['tContent']
			);
		}
		return $Data;
	}

	function countTopMenu($iTopMenu)
	{
		$Baca = $this->Db->sql_query_row("SELECT COUNT(id) FROM cppage WHERE iTopMenu='" . $iTopMenu . "'");
		return $Baca[0];
	}

	//Page Configuration
	function detailPageConf($idPage)
	{
		return $this->Db->sql_query_array("SELECT * FROM cppageconf WHERE idPage='" . $idPage . "'");
	}

	function countPageConf($idPage)
	{
		$Baca = $this->Db->sql_query_array("select COUNT(id) AS total from cppageconf WHERE idPage='" . $idPage . "'");
		return $Baca[total];
	}

	function addPageConf($Data)
	{
		return $this->Db->add($Data, "cppageconf");
	}

	function updatePageConf($Data, $idPage)
	{
		$tableName = "cppageconf";

		if (count($Data) <= 0)
			return false;
		else {
			$columns = array_keys($Data);
			$values = array_values($Data);
			for ($i = 0; $i < count($Data); $i++) {
				if ($j == 0)
					$data_query = " SET " . $columns[$i] . "='" . $values[$i] . "'";
				else
					$data_query .= ", " . $columns[$i] . "='" . $values[$i] . "'";

				$j++;
			}
			$query = "UPDATE " . $tableName . $data_query . " WHERE idPage='" . $idPage . "'";
			//return $query;
			return $this->Db->sql_query($query);
		}
	}

	function listPageByCategory($idCategory)
	{
		$baca = $this->Db->sql_query("SELECT * FROM cppage WHERE idCategory='" . $idCategory . "'");
		$i = 0;
		while ($Baca = $this->Db->sql_array($baca)) {
			$Data[$i] = array('Item' => $Baca);
			$i++;
		}
		return $Data;
	}

	//Parsing Content
	function Youtube($string)
	{
		//$string = 'aaaa [YOUTUBE=http://www.youtube.com/watch?v=OsV5RQtmBVw w=300 H=400] ffffsdsds dsdlwpq';
		//$pattern = '%\[YOUTUBE=.*/watch\?v=(.*)\s+w=(\d+)\s+h=(\d+)\]%is';
		//$replacement = '<iframe width=$2 height=$3 src=//www.youtube.com/embed/$1 frameborder="0" allowfullscreen></iframe>';
		//$Data = preg_replace($pattern, $replacement, $string);
		//echo $Data;
		//Filter Video without Width and Height
		//$string = 'aaaa <p>[YOUTUBE=http://www.youtube.com/watch?v=OsV5RQtmBVw]</p> ffffsdsds dsdlwpq';
		$pattern = '%\[YOUTUBE=.*/watch\?v=(.*?)\]%is';
		$replacement = '<iframe width="560" height="315" src="//www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe>';
		$Data = preg_replace($pattern, $replacement, $string);
		return $Data;
	}

	function folder_exist($folder)
	{
		// Get canonicalized absolute pathname
		$path = realpath($folder);

		// If it exist, check if it's a directory
		if ($path !== false and is_dir($path)) {
			// Return canonicalized absolute pathname
			return true;
		}

		// Path/folder does not exist
		return false;
	}

	function readFolder($Directory)
	{
		$dir = opendir($Directory);
		$i = 0;
		while ($file = readdir($dir)) {
			clearstatcache();
			if (!(($file == ".") or ($file == "..") or ($file == "index.php") or ($file == "core.php") or ($file == "index.html") or ($file == "index.htm"))) {
				$dataFile[$i] = array("fileName" => $file);
				$i++;
			}
		}
		closedir($dir);
		return $dataFile;
	}

	function Folder($string)
	{
		//Filter Video With Width and Height
		//$string = 'aaaa [FOLDER=data] ffffsdsds dsdlwpq';
		$pattern = '%\[FOLDER=(.*)\]%is';
		preg_match($pattern, $string, $match);
		$mainFolder = $match[1];

		if ($_GET['fldr'] != "")
			$folder = $this->Config['upload']['dir'] . $_GET['fldr'];
		else
			$folder = $this->Config['upload']['dir'] . $mainFolder;

		$actual_link = substr_replace($this->Config['base']['url'], "", -1) . $_SERVER['REQUEST_URI'];

		if ($this->folder_exist($folder)) {
			$getFolder = $this->readFolder($folder);
			$replacement = "";
			for ($i = 0; $i < count($getFolder); $i++) {
				$getFilename_ = explode(".", $getFolder[$i]['fileName']);
				$getFilename = ucwords(preg_replace("#_#", " ", preg_replace("#-#", " ", $getFilename_[0])));
				//$replacement .= "<li><a target=\"_blank\" href=\"".$this->Config['base']['url'].$folder."/".$getFolder[$i]['fileName']."\">".$getFilename."</a></li>";
				$replacement .= "<div class=\"panel panel-default\"><div class=\"panel-heading\" style=\"padding:5px;\">";

				if ($this->folder_exist($folder . "/" . $getFolder[$i]['fileName']))
					$replacement .= "<h4 class=\"panel-title\"><i class=\"fa fa-folder\" aria-hidden=\"true\"></i>&nbsp;&nbsp;<a href=\"" . $actual_link . ((preg_match("#?#", $actual_link)) ? "&" : "?") . "fldr=" . $mainFolder . "/" . $getFolder[$i]['fileName'] . "\">" . $getFilename . "</a></h4>";
				else {
					if (preg_match("#\.pdf#", $getFolder[$i]['fileName']))
						$replacement .= "<h4 class=\"panel-title\"><i class=\"fa fa-download\" aria-hidden=\"true\"></i>&nbsp;&nbsp;<a href=\"javascript:openpdf('" . $this->Config['base']['url'] . $folder . "/" . $getFolder[$i]['fileName'] . "');\">" . $getFilename . "</a></h4>";
					else
						$replacement .= "<h4 class=\"panel-title\"><i class=\"fa fa-download\" aria-hidden=\"true\"></i>&nbsp;&nbsp;<a target=\"_blank\" href=\"" . $this->Config['base']['url'] . $folder . "/" . $getFolder[$i]['fileName'] . "\">" . $getFilename . "</a></h4>";
				}

				$replacement .= "</div></div>";
			}
		} else
			$replacement = "";

		//$replacement = '<iframe width=$2 height=$3 src=//www.youtube.com/embed/$1 frameborder="0" allowfullscreen></iframe>';
		return preg_replace($pattern, $replacement, $string);
	}

	function FFile($string)
	{
		//Filter Video With Width and Height
		//$string = 'aaaa [FILE=data] ffffsdsds dsdlwpq';
		$pattern = '%\[FILE=(.*)\]%is';
		preg_match($pattern, $string, $match);
		$theFile = $this->Config['base']['url'] . $this->Config['upload']['dir'] . $match[1];

		$getFilename = ucwords(preg_replace("#_#", " ", preg_replace("#-#", " ", $match[1])));
		$replacement = "<div class=\"panel panel-default\"><div class=\"panel-heading\" style=\"padding:5px;\">";
		$replacement .= "<h4 class=\"panel-title\"><i class=\"fa fa-download\" aria-hidden=\"true\"></i>&nbsp;<a target=\"_blank\" href=\"" . $theFile . "\">" . $getFilename . "</a>";
		$replacement .= "</h4></div></div>";

		return preg_replace($pattern, $replacement, $string);
	}
}
