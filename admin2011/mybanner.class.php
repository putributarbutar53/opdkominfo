<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class mybanner extends Core
{
	var $Submit, $Action, $Do, $Id, $idStatus, $dirBanner;
	public function __construct()
	{
		parent::__construct();
		
		//Load General Process
		include '../inc/general_admin.php';

		$this->LoadModule("Paging");
		$this->Module->Paging->setPaging(10,10,"[prev]","[next]");

		$this->dirBanner = $this->Config['upload']['bannerdir'];
		$this->Pile->fileDestination = $this->dirBanner;

		$this->Template->assign("dirBanner", $this->dirBanner);
		$this->Template->assign("Signature", "banner");
		ob_clean();
	}
		
	function main()
	{
		echo $this->Template->ShowAdmin("banner/banner_category.html");
	}

	function getdetailcategory()
	{
		$listCategory = $this->Module->Banner->listCategory();
		$this->Template->assign("listCategory", $listCategory);
		$getCategory = $this->Template->ShowAdmin("banner/section_category_list.html");
		$json_data = array('category' => $getCategory);
		echo json_encode($json_data);
	}

	function editcategory()
	{
		$this->Template->assign("Detail", $this->Module->Banner->detailCategory($this->Id));
		echo $this->Template->ShowAdmin("banner/section_category_edit.html");
	}

	function addcategory()
	{
		$vCategory = $_POST['vCategory'];
		$vPermalink = ($_POST['vPermalink']=="")?preg_replace("# #","-",strtolower(preg_replace("/[^a-zA-Z0-9\-\s]/", "", $vCategory))):preg_replace("# #","-",strtolower(preg_replace("/[^a-zA-Z0-9\-\s]/", "", $_POST['vPermalink'])));
		$iModule = ($_POST['iModule']=="yes")?"1":"0";

		$Action = $_POST['action'];
		switch ($Action)
		{
			case "add":
				if ($vCategory!="")
				{
					if ($this->Module->Banner->addCategory(array(
						'vCategory' => $vCategory,
						'vPermalink' => $vPermalink,
						'iModule' => $iModule)))
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
						'vPermalink' => $vPermalink,
						'iModule' => $iModule
					);

					if ($this->Module->Banner->updateCategory($UpdateField,$idCategory))
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
			$countBanner = $this->Module->Banner->countBanner($idCategory);
			if ($countBanner['total']<=0)
			{
				if ($this->Module->Banner->deleteCategory($idCategory))
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

	function banner()
	{
		$this->Template->assign("detailCategory", $this->Module->Banner->detailCategory($this->Id));
		echo $this->Template->ShowAdmin("banner/banner_index.html");
	}

	function loaddata()
	{
		$idCategory = $_GET['idcategory'];

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
			$searchQuery = " AND ((vBannerName like '%".$searchValue."%') OR (vBannerURL like '%".$searchValue."%'))";
		}
		
		//Total Records without Filtering
		$records = $this->Db->sql_query_array("select count(*) as total from cpbanner WHERE idCategory='".$idCategory."'".$searchQuery);
		$totalRecords = $records['total'];
		
		//Total Record with filtering
		$records = $this->Db->sql_query_array("select count(*) as total from cpbanner where id!='0'".$searchQuery." AND idCategory='".$idCategory."'");
		$totalRecordsWithFilter = $records['total'];
		
		//Fetch Records
		$orderBy = ($columnName=="")?" order by id desc":" order by ".$columnName." ".$columnSortOrder;
		$limitBy = ($row=="")?"":" limit ".$row.",".$rowperpage;
		
		$sqlQuery = "select * from cpbanner where id!='0' AND idCategory='".$idCategory."'".$searchQuery.$orderBy.$limitBy;
			
		$sqlRecord = $this->Db->sql_query($sqlQuery);
		while ($row = $this->Db->sql_array($sqlRecord))
		{
			$navButton = "<a href=\"javascript:editdata(".$row['id'].")\"><i class='fas fa-pen-square'></i></a>&nbsp;&nbsp;
			<a href=\"javascript:deletedata(".$row['id'].")\"><i class='fas fa-trash-alt'></i></a>";

			$dCreated = date("d F Y", strtotime($row['dCreated']));
			$_Status = ($row['iStatus']=="1")?"<span class=\"text-success\"><a href=\"javascript:setstatus('0',".$row['id'].")\" class=\"text-success\"><i class=\"fas fa-toggle-on\"></i></a> Published</span>":"<span class=\"text-secondary\"><a href=\"javascript:setstatus('1',".$row['id'].")\" class=\"text-secondary\"><i class=\"fas fa-toggle-off\"></i></a> Draft</span>";
			
			// $ImageByURL = ($row['vFileURL']!="")?"<a href=\"".$row['vFileURL']."\" data-fancybox=\"gallery\" data-caption=\"".$row['vBannerName']."\"><img class=\"img-fluid\" src=\"".$row['vFileURL']."\" width=\"100\" /></a>":"<img class=\"img-fluid\" src=\"".$this->Config['base']['url'].$this->Config['admin']['themes']."assets/img/no-picture.jpg\" width=\"200\" />";
			// $Image_Pic = ($row['vBannerFile']!="")?"<a href=\"".$this->Config['base']['url'].$this->dirBanner.$row['vBannerFile']."\" data-fancybox=\"gallery\" data-caption=\"".$row['vBannerName']."\"><img class=\"img-fluid\" src=\"".$this->Config['base']['url'].$this->dirBanner.$row['vBannerFile']."\" width=\"200\" /></a>&nbsp;<span class=\"h6\"><a href=\"javascript:deletephoto(".$row['id'].")\"><i class='fas fa-trash-alt fa-1x'></i></a></span>":$ImageByURL;

			$ImageByURL = ($row['vFileURL']!="")?"<a href=\"".$this->Config['admin']['url']."mybanner/detail?id=".$row['id']."\" data-caption=\"".$row['vBannerName']."\"><img class=\"img-fluid\" src=\"".$row['vFileURL']."\" width=\"100\" /></a>":"<img class=\"img-fluid\" src=\"".$this->Config['base']['url'].$this->Config['admin']['themes']."assets/img/no-picture.jpg\" width=\"200\" />";
			$Image_Pic = ($row['vBannerFile']!="")?"<a href=\"".$this->Config['admin']['url']."mybanner/detail?id=".$row['id']."\" data-caption=\"".$row['vBannerName']."\"><img class=\"img-fluid\" src=\"".$this->Config['base']['url'].$this->dirBanner.$row['vBannerFile']."\" width=\"200\" /></a>&nbsp;<span class=\"h6\"><a href=\"javascript:deletephoto(".$row['id'].")\"><i class='fas fa-trash-alt fa-1x'></i></a></span>":$ImageByURL;

			$Banner_Link = "<a class=\"h4\" href=\"".$this->Config['admin']['url']."mybanner/detail?id=".$row['id']."\">".$row['vBannerName']."</a>";

			$Banner_Text = $Image_Pic;
			$Banner_Text .= "<br />".$Banner_Link;
			$Banner_Text .= "<br /><span class=\"fs--1\">".$dCreated."</span>";

			$data[] = array(
				"iStatus" => $_Status."<br /><br />".$navButton,
				"dCreated" => $Banner_Text,
				//"vBannerName" => "<a href=\"javascript:detaildata(".$row['id'].")\">".$row['vBannerName']."</a>",
				// "vBannerName" => "<a href=\"".$this->Config['admin']['url']."mybanner/detail?id=".$row['id']."\">".$row['vBannerName']."</a>",
				// "vBannerFile" => $Image_Pic
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
	
	function add()
	{
		$this->Template->assign("idCategory", $_GET['idcategory']);
		echo $this->Template->ShowAdmin("banner/banner_add.html");
	}

	function edit()
	{
		$this->Template->assign("Detail", $this->Module->Banner->detailBanner($this->Id));
		echo $this->Template->ShowAdmin("banner/banner_edit.html");
	}

	function status()
	{
		if ($this->Id!="")
		{
			$iStatus = $_GET['status'];
			if ($this->Module->Banner->status($iStatus, $this->Id))
			{
				$Return = array('status' => 'success',
				'message' => $this->Template->showMessage('success', 'Status banner sudah di perbaharui'), 
				'data' => ''
				);
			}
		}
		else
		{
			$Return = array('status' => 'error',
			'message' => $this->Template->showMessage('error', 'Ops! ID Banner tidak valid'), 
			'data' => ''
			);			
		}

		echo json_encode($Return);
	}

	function detail()
	{
		$detailBanner = $this->Module->Banner->detailBanner($this->Id);
		$detailCategory = $this->Module->Banner->detailCategory($detailBanner['idCategory']);
		$this->Template->assign("Detail", $detailBanner);
		$this->Template->assign("detailCategory", $detailCategory);

		echo $this->Template->ShowAdmin("banner/banner_detail.html");
	}

	function deletephoto()
	{
		$idBanner = $_GET['idbanner'];
		$detailBanner = $this->Module->Banner->detailBanner($idBanner);
		if ($detailBanner['id'])
		{			
			$this->Pile->deleteOldFile($detailBanner['vBannerFile']);
			if ($this->Module->Banner->deletePicture($idBanner))
			{
				$Return = array('status' => 'success',
				'message' => $this->Template->showMessage('success', 'Data banner telah di hapus'), 
				'data' => ''
				);
			}
		}
		else
		{
			$Return = array('status' => 'error',
			'message' => $this->Template->showMessage('error', 'Ops! ID banner tidak valid'), 
			'data' => ''
			);			
		}

		echo json_encode($Return);
	}

	function delete()
	{
		$detailBanner = $this->Module->Banner->detailBanner($this->Id);
		if ($detailBanner['id'])
		{			
			$listModule = $this->Module->Content->listModule($this->Id, "banner");
			for ($i=0;$i<count($listModule);$i++)
			{
				if ($listModule[$i]['Item']['vPicture']!="")
					$this->Pile->deleteOldFile($listModule[$i]['Item']['vPicture']);

				$this->Module->Content->deleteModule($listModule[$i]['Item']['id']);
			}

			if ($detailBanner['vBannerFile']!="")
				$this->Pile->deleteOldFile($detailBanner['vBannerFile']);
			
			if ($this->Module->Banner->deleteBanner($this->Id))
			{
				$Return = array('status' => 'success',
				'message' => $this->Template->showMessage('success', 'Data banner telah di hapus'), 
				'data' => ''
				);
			}
		}
		else
		{
			$Return = array('status' => 'error',
			'message' => $this->Template->showMessage('error', 'Ops! ID banner tidak valid'), 
			'data' => ''
			);			
		}

		echo json_encode($Return);
	}

	function submit()
	{
		$idCategory = $_POST['idcategory'];
		$vBannerName = $_POST['vBannerName'];
		$vBannerURL = $_POST['vBannerURL'];
		$dCreated = date("Y-m-d");
		$iStatus = $_POST['iStatus'];
		$vFileURL = $_POST['vFileURL'];
		$vBannerFile = $this->Pile->simpanImage($_FILES['vBannerFile'],"banner_".date("Yndhis").rand(0,9).rand(0,9).rand(0,9));
		$tDetail = $_POST['tDetail'];

		$Action = $_POST['action'];
		switch ($Action)
		{
			case "add":
				if ($vBannerName!="")
				{
					if ($this->Module->Banner->addBanner(array(
						'idCategory' => $idCategory,
						'vBannerName' => $vBannerName,
						'vBannerURL' => $vBannerURL,
						'dCreated' => $dCreated,
						'iStatus' => $iStatus,
						'vFileURL' => $vFileURL,
						'vBannerFile' => $vBannerFile,
						'tDetail' => $tDetail)))
					{
						$Return = array('status' => 'success',
						'message' => 'Data banner telah di tambahkan', 
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
				if ($vBannerName!="")
				{
					$detailBanner = $this->Module->Banner->detailBanner($this->Id);
					$UpdateField = array(
						'vBannerName' => $vBannerName,
						'vBannerURL' => $vBannerURL,
						'dCreated' => $dCreated,
						'iStatus' => $iStatus,
						'vFileURL' => $vFileURL,
						'tDetail' => $tDetail
					);

					if ($vBannerFile!="")
					{
						$this->Pile->deleteOldFile($detailBanner['vBannerFile']);
						$UpdateField = array_merge($UpdateField,array('vBannerFile' => $vBannerFile));
					}

					if ($this->Module->Banner->updateBanner($UpdateField,$this->Id))
						{
							$Return = array('status' => 'success',
							'message' => 'Data banner telah di perbaharui', 
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

	//Module
	function showmodule()
	{
		$idContent = $this->uri(4);

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
		$records = $this->Db->sql_query_array("select count(*) as total from cpcontentmodule WHERE idContent='".$idContent."' AND vModule='banner'");
		$totalRecords = $records['total'];
		
		//Total Record with filtering
		$records = $this->Db->sql_query_array("select count(*) as total from cpcontentmodule where id!='0'".$searchQuery." AND idContent='".$idContent."' AND vModule='banner'");
		$totalRecordsWithFilter = $records['total'];
		
		//Fetch Records
		$orderBy = ($columnName=="")?" order by id desc":" order by ".$columnName." ".$columnSortOrder;
		$limitBy = ($row=="")?"":" limit ".$row.",".$rowperpage;
		
		$sqlQuery = "select * from cpcontentmodule where id!='0' AND idContent='".$idContent."' AND vModule='banner'".$searchQuery.$orderBy.$limitBy;
			
		$sqlRecord = $this->Db->sql_query($sqlQuery);
		while ($row = $this->Db->sql_array($sqlRecord))
		{
			$navButton = "<a href=\"javascript:editmodule(".$row['id'].")\"><i class='fas fa-pen-square'></i></a>&nbsp;<a href=\"javascript:deletemodule(".$row['id'].")\"><i class='fas fa-trash-alt'></i></a>";			
			$Image_Pic = ($row['vPicture']!="")?"<a href=\"".$this->Config['base']['url'].$this->dirBanner.$row['vPicture']."\" data-fancybox=\"gallery\" data-caption=\"\"><img class=\"img-fluid\" src=\"".$this->Config['base']['url'].$this->dirBanner.$row['vPicture']."\" width=\"100\" /></a>&nbsp;<a href=\"javascript:deletemodulepic(".$row['id'].")\"><i class='fas fa-trash-alt h6'></i></a>":"----";

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
		$vName = $_POST['vName'];
		$vData = $_POST['vData'];
		$vPicture = $this->Pile->simpanImage($_FILES['vPicture'],"bn_".date("Yndhis").rand(0,9).rand(0,9).rand(0,9));
		$vModule = "banner";

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
							'message' => $this->Template->showMessage('success', 'Data module content telah di tambahkan'), 
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

	function editmodule()
	{
		$idModule = $_GET['idmodule'];
		$this->Template->assign("detailModule", $this->Module->Content->detailModule($idModule));
		echo $this->Template->ShowAdmin("banner/banner_module_edit.html");
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

}

?>