<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class mypage extends Core
{
	var $Submit, $Do, $Id, $Action, $iTopMenu, $ContenModule, $pageDir;
	public function __construct()
	{
		parent::__construct();

		//Load General Process
		include '../inc/general_admin.php';
		
		$this->LoadModule("Options");

		$this->LoadModule("FileManager");
		$this->LoadModule("Paging");
		$this->Module->Paging->setPaging(20,10,"&laquo; Prev","Next &raquo;");
		
		$this->Template->assign("Signature", "page");

		$this->pageDir = $this->Config['page']['dir'];
		$this->Template->assign("pageDir", $this->pageDir);
		$this->Pile->fileDestination=$this->pageDir;

		$this->Submit = ($_POST['submit'])?$_POST['submit']:$_GET['submit'];
		$this->Action = ($_POST['action'])?$_POST['action']:$_GET['action'];
		$this->Do = ($_POST['do'])?$_POST['do']:$_GET['do'];
		$this->Template->assign("Do", $this->Do);
		
		$this->Id = ($_POST['id'])?$_POST['id']:$_GET['id'];
		$this->iTopMenu = ($_GET['itopmenu'])?$_GET['itopmenu']:"0";

		$this->ContentModule = "page";

		session_start();
		$_SESSION['editor_status'] = "live_editor";

		$this->Template->assign("Signature", "page");

		ob_clean();
	}

	//Page
	function main()
	{
		echo $this->Template->ShowAdmin("page/page_category.html");
	}

	function getdetailcategory()
	{
		$listCategory = $this->Module->Page->listCategory();
		$this->Template->assign("listCategory", $listCategory);
		$getCategory = $this->Template->ShowAdmin("page/section_category_list.html");
		$json_data = array('category' => $getCategory);
		echo json_encode($json_data);
	}

	function editcategory()
	{
		$this->Template->assign("Detail", $this->Module->Page->detailCategory($this->Id));
		echo $this->Template->ShowAdmin("page/section_category_edit.html");
	}

	function addcategory()
	{
		$vCategory = $_POST['vCategory'];

		$iAddPage = ($_POST['iAddPage']=="yes")?"1":"0";
		$iPictureIcon = ($_POST['iPictureIcon']=="yes")?"1":"0";
		$iContent = ($_POST['iContent']=="yes")?"1":"0";
		$iMenuURL = ($_POST['iMenuURL']=="yes")?"1":"0";
		$iLinkTarget = ($_POST['iLinkTarget']=="yes")?"1":"0";
		$iEditor  = ($_POST['iEditor']=="yes")?"1":"0";
		$iModule  = ($_POST['iModule']=="yes")?"1":"0";
		$iMeta  = ($_POST['iMeta']=="yes")?"1":"0";
		$iLiveEditor  = ($_POST['iLiveEditor']=="yes")?"1":"0";
		$iOpsi  = ($_POST['iOpsi']=="yes")?"1":"0";

		$vPermalink = ($_POST['vPermalink']=="")?preg_replace("# #","-",strtolower(preg_replace("/[^a-zA-Z0-9\-\s]/", "", $vCategory))):preg_replace("# #","-",strtolower(preg_replace("/[^a-zA-Z0-9\-\s]/", "", $_POST['vPermalink'])));

		$Action = $_POST['action'];
		switch ($Action)
		{
			case "add":
				if ($vCategory!="")
				{
					if ($this->Module->Page->addCategory(array(
						'vCategory' => $vCategory,
						'iAddPage' => $iAddPage,
						'iPictureIcon' => $iPictureIcon,
						'iContent' => $iContent,
						'iMenuURL' => $iMenuURL,
						'iLinkTarget' => $iLinkTarget,
						'iEditor' => $iEditor,
						'iModule' => $iModule,
						'iMeta' => $iMeta,
						'iLiveEditor' => $iLiveEditor,
						'iOpsi' => $iOpsi,
						'vPermalink' => $vPermalink)))
					{	
						$Return = array('status' => 'success',
						'message' => $this->Template->showMessage('success', 'Data category telah di tambahkan'), 
						'data' => ''
						);
					}
					else
					{
						$Return = array('status' => 'error',
						'message' => $this->Template->showMessage('error', 'Ops! Ada error pada database'), 
						'data' => ''
						);
					}
				}
				else
				{
					$Return = array(
						'status' => 'error',
						'message' => $this->Template->showMessage('error', 'Data form isian tidak lengkap'), 
						'data' => ''
					);
				}
			break;
			case "update":
				$idCategory = $_POST['idcategory'];

				if (($vCategory!="") AND ($idCategory!=""))
				{
					$UpdateField = array(
						'vCategory' => $vCategory,
						'iAddPage' => $iAddPage,
						'iPictureIcon' => $iPictureIcon,
						'iContent' => $iContent,
						'iMenuURL' => $iMenuURL,
						'iLinkTarget' => $iLinkTarget,
						'iEditor' => $iEditor,
						'iModule' => $iModule,
						'iMeta' => $iMeta,
						'iLiveEditor' => $iLiveEditor,
						'iOpsi' => $iOpsi,
						'vPermalink' => $vPermalink
					);

					if ($this->Module->Page->updateCategory($UpdateField,$idCategory))
						{
							$Return = array('status' => 'success',
							'message' => $this->Template->showMessage('success', 'Data category telah di perbaharui'), 
							'data' => ''
							);
						}
						else
						{
							$Return = array('status' => 'error',
							'message' => $this->Template->showMessage('error', 'Ops! Ada error pada database'), 
							'data' => ''
							);
						}
				}
				else
				{
					$Return = array('status' => 'error',
					'message' => $this->Template->showMessage('error', 'Ops! Data form isian tidak lengkap'), 
					'data' => ''
					);
				}
			break;
		}

		echo json_encode($Return);		
	}

	function deletecategory()
	{
		$idCategory = $_GET['idcategory'];
		if ($idCategory!="")
		{
			$countPage = $this->Module->Page->countPage($idCategory);
			if ($countPage['total']<=0)
			{
				if ($this->Module->Page->deleteCategory($idCategory))
				{
					$Return = array('status' => 'success',
					'message' => $this->Template->showMessage('success', 'Data category telah di hapus'), 
					'data' => ''
					);
				}
			}
			else
			{
				$Return = array('status' => 'error',
				'message' => $this->Template->showMessage('error', 'Ops! Category ini mengandung data banner, harap hapus dulu data banner di dalam category'), 
				'data' => ''
				);	
			}
		}
		else
		{
			$Return = array('status' => 'error',
			'message' => $this->Template->showMessage('error', 'Ops! ID category tidak valid'), 
			'data' => ''
			);			
		}

		echo json_encode($Return);
	}

	function page()
	{
		$this->Template->assign("detailCategory", $this->Module->Page->detailCategory($this->Id));
		echo $this->Template->ShowAdmin("page/page_index.html");
	}

	function getdatapage()
	{
		$idCategory = $_GET['idcategory'];
		$listAllPage = $this->Module->Page->listAllPages($idCategory);
		$this->Template->assign("listAllPage", $listAllPage);
		
		//$listCategory = $this->Module->Page->listCategory();
		//$this->Template->assign("listCategory", $listCategory);
		$getPage = $this->Template->ShowAdmin("page/page_tree.html");
		$json_data = array('page' => $getPage);
		echo json_encode($json_data);
	}

	function delete()
	{
		$detailPage = $this->Module->Page->detailPage($this->Id);
		if ($detailPage['id'])
		{			
			if ($detailPage['lbPicture']!="")
				$this->Pile->deleteOldFile($detailPage['lbPicture']);
			
			$listModule = $this->Module->Content->listModule($this->Id, "page");
			for ($i=0;$i<count($listModule);$i++)
			{
				if ($listModule[$i]['Item']['vPicture']!="")
					$this->Pile->deleteOldFile($listModule[$i]['Item']['vPicture']);

				$this->Module->Content->deleteModule($listModule[$i]['Item']['id']);
			}
				
			if ($this->Module->Page->deletePage($this->Id))
			{
				$Return = array('status' => 'success',
				'message' => $this->Template->showMessage('success', 'Data page telah di hapus'), 
				'data' => ''
				);
			}
		}
		else
		{
			$Return = array('status' => 'error',
			'message' => $this->Template->showMessage('error', 'Ops! ID page tidak valid'), 
			'data' => ''
			);			
		}

		echo json_encode($Return);
	}

	function add()
	{
		$idCategory = $_GET['idcategory'];
		$this->Template->assign("detailCategory", $this->Module->Page->detailCategory($idCategory));
		$this->Template->assign("idCategory", $idCategory);

		echo $this->Template->ShowAdmin("page/page_add.html");
	}

	function getcontent()
	{
		$idPage = $this->uri(4);
		$detailPage = $this->Module->Page->detailPage($idPage);
		$this->Template->assign("detailPage", $detailPage);
		echo $this->Template->ShowAdmin("page/page_default_editor.html");
	}

	function addsub()
	{
		$idPage = $_GET['idpage'];
		$this->Template->assign("idPage", $idPage);
		$this->Template->assign("detailPage", $this->Module->Page->detailPage($idPage));
		$this->Template->assign("detailPageConf", $this->Module->Page->detailPageConf($idPage));
		echo $this->Template->ShowAdmin("page/page_addsub.html");
	}

	function edit()
	{
		$detailPage = $this->Module->Page->detailPage($this->Id);
		$this->Template->assign("Detail", $detailPage);
		$this->Template->assign("detailConf", $this->Module->Page->detailPageConf($detailPage['id']));
		$getPage = $this->Template->ShowAdmin("page/page_edit.html");
		$json_data = array('page' => $getPage);
		echo json_encode($json_data);
	}

	function editconf()
	{
		$this->Template->assign("detailPage", $this->Module->Page->detailPage($this->Id));
		$this->Template->assign("detailConf", $this->Module->Page->detailPageConf($this->Id));
		echo $this->Template->ShowAdmin("page/pageconf_edit.html");
	}

	function deletephoto()
	{
		$idPage = $_GET['idpage'];
		$detailPage = $this->Module->Page->detailPage($idPage);
		if ($detailPage['id'])
		{			
			$this->Pile->deleteOldFile($detailPage['lbPicture']);
			if ($this->Module->Page->deletePicture($idPage))
			{
				$Return = array('status' => 'success',
				'message' => $this->Template->showMessage('success', 'Data gambar telah di hapus'), 
				'data' => ''
				);
			}
		}
		else
		{
			$Return = array('status' => 'error',
			'message' => $this->Template->showMessage('error', 'Ops! ID page tidak valid'), 
			'data' => ''
			);			
		}

		echo json_encode($Return);
	}

	function updateconf()
	{
		$idPage = $_POST['idPage'];

		$iAddPage = ($_POST['iAddPage']=="yes")?"1":"0";
		$iPictureIcon = ($_POST['iPictureIcon']=="yes")?"1":"0";
		$iContent = ($_POST['iContent']=="yes")?"1":"0";
		$iMenuURL = ($_POST['iMenuURL']=="yes")?"1":"0";
		$iLinkTarget = ($_POST['iLinkTarget']=="yes")?"1":"0";
		$iEditor  = ($_POST['iEditor']=="yes")?"1":"0";
		$iModule  = ($_POST['iModule']=="yes")?"1":"0";
		$iMeta  = ($_POST['iMeta']=="yes")?"1":"0";
		$iLiveEditor  = ($_POST['iLiveEditor']=="yes")?"1":"0";
		$iOpsi  = ($_POST['iOpsi']=="yes")?"1":"0";

		if ($idPage!="")
		{
			$UpdateField = array(
				'iAddPage' => $iAddPage,
				'iPictureIcon' => $iPictureIcon,
				'iContent' => $iContent,
				'iMenuURL' => $iMenuURL,
				'iLinkTarget' => $iLinkTarget,
				'iEditor' => $iEditor,
				'iModule' => $iModule,
				'iMeta' => $iMeta,
				'iLiveEditor' => $iLiveEditor,
				'iOpsi' => $iOpsi
			);

			if ($this->Module->Page->updatePageConf($UpdateField, $idPage))
				{
					$Return = array('status' => 'success',
					'message' => $this->Template->showMessage('success', 'Data page conf telah di perbaharui'), 
					'data' => $idPage
					);
				}
				else
				{
					$Return = array('status' => 'error',
					'message' => $this->Template->showMessage('error', 'Ops! Ada error pada database'), 
					'data' => ''
					);
				}
		}
		else
		{
			$Return = array('status' => 'error',
			'message' => $this->Template->showMessage('error', 'Ops! Data form isian tidak lengkap'), 
			'data' => ''
			);
		}

		echo json_encode($Return);
	}

	function submit()
	{
		$vPageName = $_POST['vPageName'];
		$iTopMenu = ($_POST['iTopMenu'])?$_POST['iTopMenu']:"0";
		$vPermalink = ($_POST['vPermalink']=="")?preg_replace("# #","-",strtolower(preg_replace("/[^a-zA-Z0-9\-\s]/", "", $vPageName))):preg_replace("# #","-",strtolower(preg_replace("/[^a-zA-Z0-9\-\s]/", "", $_POST['vPermalink'])));
		$vURL = $_POST['vURL'];
		$tContent = $this->Template->cleanURL($this->Db->real_escape_string($_POST['tContent']));
		$idCategory = $_POST['idCategory'];
		$dCreated = date("Y-m-d");
		$cURLTarget = $_POST['cURLTarget'];
		$lbPicture = $this->Pile->simpanImage($_FILES['lbPicture'],"page_".date("Yndhis").rand(0,9).rand(0,9).rand(0,9));
		$vMetaTitle = $_POST['vMetaTitle'];
		$vMetaDesc = $_POST['vMetaDesc'];
		$vMetaKeyword = $_POST['vMetaKeyword'];
		$iShow = ($_POST['iShow']=="")?"1":$_POST['iShow'];
		$iUrutan = ($_POST['iUrutan'])?$_POST['iUrutan']:"0";

		$Action = $_POST['action'];

		switch ($Action)
		{
			case "add":
				if ($vPageName!="")
				{
					if ($this->Module->Page->addPage(array(
						'vPageName' => $vPageName,
						'iTopMenu' => $iTopMenu,
						'vPermalink' => $vPermalink,
						'vURL' => $vURL,
						'tContent' => $tContent,
						'idCategory' => $idCategory,
						'dCreated' => $dCreated,
						'cURLTarget' => $cURLTarget,
						'lbPicture' => $lbPicture,
						'vMetaTitle' => $vMetaTitle, 
						'vMetaDesc' => $vMetaDesc, 
						'vMetaKeyword' => $vMetaKeyword,
						'iUrutan' => $iUrutan,
						'iShow' => $iShow
					), $idCategory))
					{
						$getLastID = $this->Module->Page->getLastPage();
						$Return = array('status' => 'success',
						'message' => 'Data page telah di tambahkan', 
						'data' => $getLastID['id']
						);
					}
					else
					{
						$Return = array('status' => 'error',
						'message' => $this->Template->showMessage('error', 'Ops! Ada error pada database'), 
						'data' => ''
						);
					}
				}
				else
				{
					$Return = array(
						'status' => 'error',
						'message' => $this->Template->showMessage('error', 'Data form isian tidak lengkap'), 
						'data' => ''
					);
				}
			break;
			case "update":
				if ($vPageName!="")
				{
					$idPage = $_POST['idPage'];
					$tContent_status = $_POST['tContent_status'];
					$detailPage = $this->Module->Page->detailPage($idPage);
					$UpdateField = array(
						'vPageName' => $vPageName,
						'vPermalink' => $vPermalink,
						'vURL' => $vURL,
						'dCreated' => $dCreated,
						'cURLTarget' => $cURLTarget,
						'vMetaTitle' => $vMetaTitle, 
						'vMetaDesc' => $vMetaDesc, 
						'vMetaKeyword' => $vMetaKeyword,
						'iUrutan' => $iUrutan,
						'iShow' => $iShow
					);

					if ($tContent_status=="1")
					{
						$UpdateField = array_merge($UpdateField,array('tContent' => $tContent));
					}

					if ($lbPicture!="")
					{
						$this->Pile->deleteOldFile($detailPage['lbPicture']);
						$UpdateField = array_merge($UpdateField,array('lbPicture' => $lbPicture));
					}

					if ($this->Module->Page->updatePage($UpdateField, $idPage))
					{
						$Return = array('status' => 'success',
						'message' => 'Data page telah di perbaharui', 
						'data' => $idPage
						);
					}
					else
					{
						$Return = array('status' => 'error',
						'message' => $this->Template->showMessage('error', 'Ops! Ada error pada database'), 
						'data' => ''
						);
					}
				}
				else
				{
					$Return = array('status' => 'error',
					'message' => $this->Template->showMessage('error', 'Ops! Data form isian tidak lengkap'), 
					'data' => ''
					);
				}
			break;
			case "addsub":
				if ($vPageName!="")
				{
					if ($this->Module->Page->addSubPage(array(
						'vPageName' => $vPageName,
						'iTopMenu' => $iTopMenu,
						'vPermalink' => $vPermalink,
						'vURL' => $vURL,
						'tContent' => $tContent,
						'idCategory' => $idCategory,
						'dCreated' => $dCreated,
						'cURLTarget' => $cURLTarget,
						'lbPicture' => $lbPicture,
						'vMetaTitle' => $vMetaTitle, 
						'vMetaDesc' => $vMetaDesc, 
						'vMetaKeyword' => $vMetaKeyword,
						'iUrutan' => $iUrutan,
						'iShow' => $iShow
					), $iTopMenu))
					{	
						$Return = array('status' => 'success',
						'message' => $this->Template->showMessage('success', 'Data page telah di tambahkan'), 
						'data' => ''
						);
					}
					else
					{
						$Return = array('status' => 'error',
						'message' => $this->Template->showMessage('error', 'Ops! Ada error pada database'), 
						'data' => ''
						);
					}
				}
				else
				{
					$Return = array(
						'status' => 'error',
						'message' => $this->Template->showMessage('error', 'Data form isian tidak lengkap'), 
						'data' => ''
					);
				}
			break;
		}

		echo json_encode($Return);
	}

	function showmodule()
	{
		$idPage = $_GET['idpage'];

		$draw = $_POST['draw'];
		$row = $_POST['start'];
		$rowperpage = $_POST['length'];
		
		$columnIndex = $_POST['order'][0]['column'];
		$columnName = $_POST['columns'][$columnIndex]['data'];
		
		$columnSortOrder = $_POST['order'][0]['dir'];
		$searchValue = $_POST['search']['value'];
		
		//Search
		$searchQuery = "";
		if ($searchValue != '')
		{
			$searchQuery = " AND ((vName like '%".$searchValue."%') OR (vData like '%".$searchValue."%'))";
		}
		
		//Total Records without Filtering
		$records = $this->Db->sql_query_array("select count(*) as total from cpcontentmodule WHERE idContent='".$idPage."' AND vModule='page'");
		$totalRecords = $records['total'];
		
		//Total Record with filtering
		$records = $this->Db->sql_query_array("select count(*) as total from cpcontentmodule where id!='0'".$searchQuery." AND idContent='".$idPage."' AND vModule='page'");
		$totalRecordsWithFilter = $records['total'];
		
		//Fetch Records
		$orderBy = ($columnName=="")?" order by id desc":" order by ".$columnName." ".$columnSortOrder;
		$limitBy = ($row=="")?"":" limit ".$row.",".$rowperpage;
		
		$sqlQuery = "select * from cpcontentmodule where id!='0' AND idContent='".$idPage."' AND vModule='page'".$searchQuery.$orderBy.$limitBy;
			
		$sqlRecord = $this->Db->sql_query($sqlQuery);
		while ($row = $this->Db->sql_array($sqlRecord))
		{
			$navButton = "<a href=\"javascript:editmodule(".$row['id'].")\"><i class='fas fa-pen-square'></i></a>&nbsp;<a href=\"javascript:deletemodule(".$row['id'].")\"><i class='fas fa-trash-alt'></i></a>";			
			$Image_Pic = ($row['vPicture']!="")?"<a href=\"".$this->Config['base']['url'].$this->pageDir.$row['vPicture']."\" data-fancybox=\"gallery\" data-caption=\"\"><img class=\"img-fluid\" src=\"".$this->Config['base']['url'].$this->pageDir.$row['vPicture']."\" width=\"100\" /></a>&nbsp;<a href=\"javascript:deletemodulepic(".$row['id'].")\"><i class='fas fa-trash-alt h6'></i></a>":"----";

			$data[] = array(
				"vName" => $row['vName'],
				"vData" => wordwrap(htmlspecialchars($row['vData']),"20","<br />\n"),
				"vPicture" => $Image_Pic,
				"navButton" => $navButton
			);
		}
		
		//Response
		$response = array(
			"draw" => intval($draw),
			"iTotalRecords" => $totalRecordsWithFilter,
			"iTotalDisplayRecords" => $totalRecords,
			"aaData" => (($data)?$data:array())
		);
		
		echo json_encode($response);
	}

	function submitmodule()
	{
		$idContent = $_POST['idContent'];
		$vName = $this->Db->real_escape_string($_POST['vName']);
		$vData = $this->Template->cleanURL($this->Db->real_escape_string($_POST['vData']));
		$vPicture = $this->Pile->simpanImage($_FILES['vPicture'],"pm_".date("Yndhis").rand(0,9).rand(0,9).rand(0,9));
		$vModule = "page";
		$idModule = $_POST['idModule'];

		$Action = $_POST['action'];

		switch ($Action)
		{
			case "add":
				if ($vName!="")
				{
					if ($idModule=="")
					{
						if ($this->Module->Content->addModule(array(
							'idContent' => $idContent,
							'vName' => $vName,
							'vData' => $vData,
							'vPicture' => $vPicture,
							'vModule' => $vModule)))
						{	
							$Return = array('status' => 'success',
							'message' => $this->Template->showMessage('success', 'Data module page telah di tambahkan'), 
							'data' => ''
							);
						}
						else
						{
							$Return = array('status' => 'error',
							'message' => $this->Template->showMessage('error', 'Ops! Ada error pada database'), 
							'data' => ''
							);
						}
					}
					else
					{
						$detailModule = $this->Module->Content->detailModule($idModule);
						if ($detailModule['id']!="")
						{
							$UpdateField = array(
								'vName' => $vName,
								'vData' => $vData
							);
		
							if ($vPicture!="")
							{
								$this->Pile->deleteOldFile($detailModule['vPicture']);
								$UpdateField = array_merge($UpdateField,array('vPicture' => $vPicture));
							}
		
							if ($this->Module->Content->updateModule($UpdateField, $idModule))
							{
								$Return = array('status' => 'success',
								'message' => $this->Template->showMessage('success', 'Data module telah di perbaharui'), 
								'data' => $idModule
								);
							}
							else
							{
								$Return = array('status' => 'error',
								'message' => $this->Template->showMessage('error', 'Ops! Ada error pada database'), 
								'data' => ''
								);
							}	
						}
					}
				}
				else
				{
					$Return = array(
						'status' => 'error',
						'message' => $this->Template->showMessage('error', 'Data form isian tidak lengkap'), 
						'data' => ''
					);
				}
			break;
		}

		echo json_encode($Return);
	}

	// function editmodule()
	// {
	// 	$detailModule = $this->Module->Content->detailModule($this->Id);
	// 	$Result = array(
	// 		'id' => $detailModule['id'],
	// 		'vname' => $detailModule['vName'],
	// 		'vdata' => $detailModule['vData'],
	// 		'vpicture' => $detailModule['vPicture']
	// 	);

	// 	echo json_encode($Result);
	// }

	function editmodule()
	{
		$idModule = $_GET['idmodule'];
		$this->Template->assign("detailModule", $this->Module->Content->detailModule($idModule));
		echo $this->Template->ShowAdmin("page/page_module_edit.html");
	}

	function deletemodulepic()
	{
		$idModule = $_GET['idmodule'];
		$detailModule = $this->Module->Content->detailModule($idModule);
		if ($detailModule['id'])
		{			
			if ($detailModule['vPicture']!="")
				$this->Pile->deleteOldFile($detailModule['vPicture']);
			
			if ($this->Module->Content->deleteModulePic($idModule))
			{
				$Return = array('status' => 'success',
				'message' => $this->Template->showMessage('success', 'Data gambar telah di hapus'), 
				'data' => ''
				);
			}
		}
		else
		{
			$Return = array('status' => 'error',
			'message' => $this->Template->showMessage('error', 'Ops! ID module tidak valid'), 
			'data' => ''
			);			
		}

		echo json_encode($Return);
	}

	function deletemodule()
	{
		$detailModule = $this->Module->Content->detailModule($this->Id);
		if ($detailModule['id'])
		{			
			if ($detailModule['vPicture']!="")
				$this->Pile->deleteOldFile($detailModule['vPicture']);
			
			if ($this->Module->Content->deleteModule($this->Id))
			{
				$Return = array('status' => 'success',
				'message' => $this->Template->showMessage('success', 'Data module telah di hapus'), 
				'data' => ''
				);
			}
		}
		else
		{
			$Return = array('status' => 'error',
			'message' => $this->Template->showMessage('error', 'Ops! ID module tidak valid'), 
			'data' => ''
			);			
		}

		echo json_encode($Return);
	}

	function setdefault()
	{
		if ($this->Id!="")
		{
			$iDefault = $_GET['idefault'];
			if ($this->Module->Page->setDefault($iDefault, $this->Id))
			{
				$Return = array('status' => 'success',
				'message' => $this->Template->showMessage('success', 'Status default page sudah di perbaharui'), 
				'data' => ''
				);
			}
		}
		else
		{
			$Return = array('status' => 'error',
			'message' => $this->Template->showMessage('error', 'Ops! ID Page tidak valid'), 
			'data' => ''
			);			
		}

		echo json_encode($Return);
	}

}
?>