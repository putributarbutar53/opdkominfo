<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class
//error_reporting(E_ALL);
class mycontent extends Core
{
	var $Submit, $Action, $Id, $dirContent, $idComment, $ContentModule, $Dir, $vCompare, $vTeks, $Do, $URL, $Category;

	public function __construct()
	{
		parent::__construct();
		
		//Load General Process
		include '../inc/general_admin.php';

		$this->LoadModule("Content");
		$this->LoadModule("Options");

		$this->LoadModule("FileManager");
		$this->LoadModule("Paging");
		$this->Module->Paging->setPaging(20,10,"&laquo; Prev","Next &raquo;");
		$this->Pile->fileDestination = $this->Config['content']['dir'];
		
		$this->ContentModule = "content";
		$this->URL = $this->Config['base']['url'].$this->Config['index']['page'].$this->Config['base']['admin']."/mycontent";

		$this->Dir=($_GET['dir'])?$_GET['dir']:$this->Dir;
		if ($this->Dir!="")
		{
			$this->Template->assign("Dir", $this->Dir);
			$this->Category = $this->Module->Content->detailDir($this->Dir);
			$this->Template->assign("Category", $this->Category);
		}

		$this->dirContent = $this->Config['content']['dir'];
		$this->Template->assign("dirContent", $this->dirContent);
		$this->Template->assign("Signature", "content");

		ob_clean();
	}
	
	function main()
	{
		echo $this->Template->ShowAdmin("content/content_category.html");
	}

	function getdetailcategory()
	{
		$listCategory = $this->Module->Content->listCategory();
		$this->Template->assign("listCategory", $listCategory);
		$getCategory = $this->Template->ShowAdmin("content/section_category_list.html");
		$json_data = array('category' => $getCategory);
		echo json_encode($json_data);
	}

	function editcategory()
	{
		$this->Template->assign("Detail", $this->Module->Content->detailDir($this->Id));
		echo $this->Template->ShowAdmin("content/section_category_edit.html");
	}

	function addcategory()
	{
		$vCategory = $_POST['vCategory'];
		$vPermalink = ($_POST['vPermalink']=="")?preg_replace("# #","-",strtolower(preg_replace("/[^a-zA-Z0-9\-\s]/", "", $vCategory))):preg_replace("# #","-",strtolower(preg_replace("/[^a-zA-Z0-9\-\s]/", "", $_POST['vPermalink'])));
		$iComment = ($_POST['iComment']=="yes")?"1":"0";
		$iModule = ($_POST['iModule']=="yes")?"1":"0";

		$Action = $_POST['action'];
		switch ($Action)
		{
			case "add":
				if ($vCategory!="")
				{
					if ($this->Module->Content->addDir(array(
						'vCategory' => $vCategory,
						'vPermalink' => $vPermalink,
						'iComment' => $iComment,
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
					$UpdateField = array('vCategory' => $vCategory,
					'vPermalink' => $vPermalink, 
					'iComment' => $iComment,
					'iModule' => $iModule
					);

					if ($this->Module->Content->updateDir($UpdateField,$idCategory))
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
			$countContent = $this->Module->Content->countContent($idCategory);
			if ($countContent['total']<=0)
			{
				if ($this->Module->Content->deleteDir($idCategory))
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
				'message' => $this->Template->showMessage('error', 'Ops! Category ini mengandung content, harap hapus dulu content di dalam category'), 
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

	function content()
	{
		$this->Template->assign("detailCategory", $this->Module->Content->detailDir($this->Id));
		echo $this->Template->ShowAdmin("content/content_index.html");
	}

	function loaddata()
	{
		$idCategory = $_GET['idcategory'];

		$draw = $_POST['draw'];
		$row = $_POST['start'];
		$rowperpage = $_POST['length'];
		
		$columnIndex = $_POST['order'][0]['column'];
		$columnName = $_POST['columns'][$columnIndex]['data'];
		
		$columnSortOrder = ($_POST['order'][0]['dir']=='asc')?'desc':'asc';
		$searchValue = $_POST['search']['value'];
		
		//Search
		$searchQuery = "";
		if ($searchValue != '')
		{
			$searchQuery = " AND ((vTitle like '%".$searchValue."%'))";
		}
		
		//Total Records without Filtering
		$records = $this->Db->sql_query_array("select count(*) as total from cpcontent WHERE idCategory='".$idCategory."'".$searchQuery);
		$totalRecords = $records['total'];
		
		//Total Record with filtering
		$records = $this->Db->sql_query_array("select count(*) as total from cpcontent where id!='0'".$searchQuery." AND idCategory='".$idCategory."'");
		$totalRecordsWithFilter = $records['total'];
		
		//Fetch Records
		$orderBy = ($columnName=="")?" order by dPublishDate desc":" order by ".$columnName." ".$columnSortOrder;
		$limitBy = ($row=="")?"":" limit ".$row.",".$rowperpage;
		
		$sqlQuery = "select * from cpcontent where id!='0' AND idCategory='".$idCategory."'".$searchQuery.$orderBy.$limitBy;
			
		$sqlRecord = $this->Db->sql_query($sqlQuery);
		while ($row = $this->Db->sql_array($sqlRecord))
		{
			$navButton = "<a href=\"javascript:editdata(".$row['id'].")\"><i class='fas fa-pen-square'></i></a>&nbsp;&nbsp;
			<a href=\"javascript:deletedata(".$row['id'].")\"><i class='fas fa-trash-alt'></i></a>";

			$dPublishDate = date("d F Y", strtotime($row['dPublishDate']));
			$_Status = ($row['iStatus']=="1")?"<span class=\"text-success\"><a href=\"javascript:setstatus('0',".$row['id'].")\" class=\"text-success\"><i class=\"fas fa-toggle-on\"></i></a> Published</span>":"<span class=\"text-danger\"><a href=\"javascript:setstatus('1',".$row['id'].")\" class=\"text-danger\"><i class=\"fas fa-toggle-off\"></i></a> Draft</span>";
			$Image_Pic = ($row['vGambar']!="")?"<a href=\"".$this->Config['base']['url'].$this->dirContent.$row['vGambar']."\" data-fancybox=\"gallery\" data-caption=\"".$row['vTitle']."\"><img class=\"img-fluid\" src=\"".$this->Config['base']['url'].$this->dirContent.$row['vGambar']."\" width=\"200\" /></a>&nbsp;<span class=\"h6\"><a href=\"javascript:deletephoto(".$row['id'].")\"><i class='fas fa-trash-alt fa-1x'></i></a></span>":"<img class=\"img-fluid\" src=\"".$this->Config['base']['url'].$this->Config['admin']['themes']."assets/img/no-picture.jpg\" width=\"200\" />";

			$theContent = $Image_Pic."<br />";
			$theContent .= "<a href=\"".$this->Config['admin']['url']."mycontent/detail?id=".$row['id']."\" class=\"h4\">".$row['vTitle']."</a>";
			$theContent .= "<br /><span class=\"fs--0\">".$dPublishDate."</span>";

			$data[] = array(
				"dPublishDate" => $_Status."<br />".$navButton,
				"iStatus" => $theContent,
				// "vTitle" => "<a href=\"javascript:detaildata(".$row['id'].")\">".$row['vTitle']."</a>",
				// "vTitle" => "<a href=\"".$this->Config['admin']['url']."mycontent/detail?id=".$row['id']."\">".$row['vTitle']."</a>",
				// "vPicture" => $Image_Pic
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
		echo $this->Template->ShowAdmin("content/content_add.html");
	}

	function edit()
	{
		$this->Template->assign("Detail", $this->Module->Content->detailContent($this->Id));
		echo $this->Template->ShowAdmin("content/content_edit.html");
	}

	function status()
	{
		if ($this->Id!="")
		{
			$iStatus = $_GET['status'];
			if ($this->Module->Content->status($iStatus, $this->Id))
			{
				$Return = array('status' => 'success',
				'message' => $this->Template->showMessage('success', 'Status content sudah di perbaharui'), 
				'data' => ''
				);
			}
		}
		else
		{
			$Return = array('status' => 'error',
			'message' => $this->Template->showMessage('error', 'Ops! ID Content tidak valid'), 
			'data' => ''
			);			
		}

		echo json_encode($Return);
	}

	function detail()
	{
		$detailContent = $this->Module->Content->detailContent($this->Id);
		$detailCategory = $this->Module->Content->detailDir($detailContent['idCategory']);	
		
		if ($detailCategory['iComment']=="1")
		{
			$this->Template->assign("totalComment", $this->Module->Content->countComment($detailContent['id']));
			$this->Template->assign("listComment", $this->Module->Content->listComment($detailContent['id']));
		}

		$this->Template->assign("Detail", $detailContent);
		$this->Template->assign("detailCategory", $detailCategory);

		echo $this->Template->ShowAdmin("content/content_detail.html");
	}

	function deletephoto()
	{
		$idContent = $_GET['idcontent'];
		$detailContent = $this->Module->Content->detailContent($idContent);
		if ($detailContent['id'])
		{			
			if ($detailContent['vGambar']!="")
				$this->Pile->deleteOldFile($detailContent['vGambar']);
			
			if ($this->Module->Content->deletePicture($idContent))
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
			'message' => $this->Template->showMessage('error', 'Ops! ID content tidak valid'), 
			'data' => ''
			);			
		}

		echo json_encode($Return);
	}

	function delete()
	{
		$detailContent = $this->Module->Content->detailContent($this->Id);
		if ($detailContent['id'])
		{
			// $listContentSetting = $this->Module->Options->listContentSetting($detailContent['id'], $this->ContentModule);
			// for ($j=0;$j<count($listContentSetting);$j++)
			// {
			// 	$this->Pile->deleteOldFile($listContentSetting[$j]['Item']['vData']);
			// 	$this->Module->Options->deleteContentSettingByID($listContentSetting[$j]['Item']['id'], $this->ContentModule);
			// }

			$listModule = $this->Module->Content->listModule($this->Id, "content");
			for ($i=0;$i<count($listModule);$i++)
			{
				if ($listModule[$i]['Item']['vPicture']!="")
					$this->Pile->deleteOldFile($listModule[$i]['Item']['vPicture']);

				$this->Module->Content->deleteModule($listModule[$i]['Item']['id']);
			}
			
			if ($detailContent['vGambar']!="")
				$this->Pile->deleteOldFile($detailContent['vGambar']);
			
			if ($this->Module->Content->deleteContent($this->Id))
			{
				$Return = array('status' => 'success',
				'message' => $this->Template->showMessage('success', 'Data content telah di hapus'), 
				'data' => ''
				);
			}
		}
		else
		{
			$Return = array('status' => 'error',
			'message' => $this->Template->showMessage('error', 'Ops! ID content tidak valid'), 
			'data' => ''
			);			
		}

		echo json_encode($Return);
	}

	function loadkomentar()
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
			$searchQuery = " AND ((vName like '%".$searchValue."%') OR (vEmail like '%".$searchValue."%') OR (tComment like '%".$searchValue."%'))";
		}
		
		//Total Records without Filtering
		$records = $this->Db->sql_query_array("select count(*) as total from cpcontentcomment where idContent='".$idContent."'");
		$totalRecords = $records['total'];
		
		//Total Record with filtering
		$records = $this->Db->sql_query_array("select count(*) as total from cpcontentcomment where id!='0' AND idContent='".$idContent."'".$searchQuery);
		$totalRecordsWithFilter = $records['total'];
		
		//Fetch Records
		$orderBy = ($columnName=="")?" order by id desc":" order by ".$columnName." ".$columnSortOrder;
		$limitBy = ($row=="")?"":" limit ".$row.",".$rowperpage;
		
		$sqlQuery = "select * from cpcontentcomment where id!='0' AND idContent='".$idContent."'".$searchQuery.$orderBy.$limitBy;
			
		$sqlRecord = $this->Db->sql_query($sqlQuery);
		while ($row = $this->Db->sql_array($sqlRecord))
		{
			$navButton = "<a href=\"javascript:deletecomment(".$row['id'].")\"><i class='fas fa-trash-alt'></i></a>";
			$_Status = ($row['iStatus']=="1")?"<span class=\"text-success\"><a href=\"javascript:statuscomment('0',".$row['id'].")\" class=\"text-success\"><i class=\"fas fa-toggle-on\"></i></a></span>":"<span class=\"text-secondary\"><a href=\"javascript:statuscomment('1',".$row['id'].")\" class=\"text-secondary\"><i class=\"fas fa-toggle-off\"></i></a></span>";
			
			$data[] = array(
				"iStatus" => $_Status,
				"vName" => $row['vName'],
				"vEmail" => $row['vEmail'],
				"tComment" => $row['tComment'],
				"dPublishDate" => $row['dPublishDate'],
				"vIP" => $row['vIP'],
				'navButton' => $navButton,
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
		$records = $this->Db->sql_query_array("select count(*) as total from cpcontentmodule WHERE idContent='".$idContent."' AND vModule='content'");
		$totalRecords = $records['total'];
		
		//Total Record with filtering
		$records = $this->Db->sql_query_array("select count(*) as total from cpcontentmodule where id!='0'".$searchQuery." AND idContent='".$idContent."' AND vModule='content'");
		$totalRecordsWithFilter = $records['total'];
		
		//Fetch Records
		$orderBy = ($columnName=="")?" order by id desc":" order by ".$columnName." ".$columnSortOrder;
		$limitBy = ($row=="")?"":" limit ".$row.",".$rowperpage;
		
		$sqlQuery = "select * from cpcontentmodule where id!='0' AND idContent='".$idContent."' AND vModule='content'".$searchQuery.$orderBy.$limitBy;
			
		$sqlRecord = $this->Db->sql_query($sqlQuery);
		while ($row = $this->Db->sql_array($sqlRecord))
		{
			$navButton = "<a href=\"javascript:editmodule(".$row['id'].")\"><i class='fas fa-pen-square'></i></a>&nbsp;<a href=\"javascript:deletemodule(".$row['id'].")\"><i class='fas fa-trash-alt'></i></a>";			
			$Image_Pic = ($row['vPicture']!="")?"<a href=\"".$this->Config['base']['url'].$this->dirContent.$row['vPicture']."\" data-fancybox=\"gallery\" data-caption=\"\"><img class=\"img-fluid\" src=\"".$this->Config['base']['url'].$this->dirContent.$row['vPicture']."\" width=\"100\" /></a>&nbsp;<a href=\"javascript:deletemodulepic(".$row['id'].")\"><i class='fas fa-trash-alt h6'></i></a>":"----";

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

	function submit()
	{
		$idCategory = $_POST['idcategory'];
		$vTitle = $this->Db->real_escape_string($_POST['vTitle']);
		$dPublishDate = ($_POST['dPublishDate'])?$_POST['dPublishDate']:date("Y-m-d");
		$vPermalink = ($_POST['vPermalink']=="")?preg_replace("# #","-",strtolower(preg_replace("/[^a-zA-Z0-9\-\s]/", "", $vTitle))):preg_replace("# #","-",strtolower(preg_replace("/[^a-zA-Z0-9\-\s]/", "", $_POST['vPermalink'])));
		$tDetail = $this->Db->real_escape_string($_POST['tDetail']);

		$vGambar = $this->Pile->simpanImage($_FILES['vGambar'],"content_".date("Yndhis").rand(0,9).rand(0,9).rand(0,9));

		$vLinkOutside = $_POST['vLinkOutside'];
		$vMetaTitle = $_POST['vMetaTitle'];
		$vMetaDesc = $_POST['vMetaDesc'];
		$vMetaKeyword = $_POST['vMetaKeyword'];
		$iStatus = $_POST['iStatus'];

		$Action = $_POST['action'];

		switch ($Action)
		{
			case "add":
				if (($vTitle!="") AND ($tDetail!=""))
				{
					if ($this->Module->Content->addContent(array(
						'idCategory' => $idCategory,
						'vTitle' => $vTitle,
						'vPermalink' => $vPermalink,
						'dPublishDate' => $dPublishDate,
						'tDetail' => $tDetail,
						'vGambar' => $vGambar,
						'vLinkOutside' => $vLinkOutside,
						'vMetaTitle' => $vMetaTitle,
						'vMetaDesc' => $vMetaDesc,
						'vMetaKeyword' => $vMetaKeyword,
						'iStatus' => $iStatus)))
					{	
						$Return = array('status' => 'success',
						'message' => 'Data content telah di tambahkan', 
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
				if (($vTitle!="") AND ($tDetail!=""))
				{
					$detailContent = $this->Module->Content->detailContent($this->Id);
					$UpdateField = array(
						'vTitle' => $vTitle,
						'vPermalink' => $vPermalink,
						'dPublishDate' => $dPublishDate,
						'tDetail' => $tDetail,
						'vLinkOutside' => $vLinkOutside,
						'vMetaTitle' => $vMetaTitle,
						'vMetaDesc' => $vMetaDesc,
						'vMetaKeyword' => $vMetaKeyword,
						'iStatus' => $iStatus
					);

					if ($vGambar!="")
					{
						$this->Pile->deleteOldFile($detailContent['vGambar']);
						$UpdateField = array_merge($UpdateField,array('vGambar' => $vGambar));
					}

					if ($this->Module->Content->updateContent($UpdateField,$this->Id))
						{
							$Return = array('status' => 'success',
							'message' => 'Data content telah di perbaharui', 
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

	function statuscomment()
	{
		if ($this->Id!="")
		{
			$iStatus = $_GET['status'];
			if ($this->Module->Content->setComment($iStatus, $this->Id))
			{
				$Return = array('status' => 'success',
				'message' => $this->Template->showMessage('success', 'Status komentar sudah di perbaharui'), 
				'data' => ''
				);
			}
		}
		else
		{
			$Return = array('status' => 'error',
			'message' => $this->Template->showMessage('error', 'Ops! ID komentar tidak valid'), 
			'data' => ''
			);			
		}

		echo json_encode($Return);
	}

	function deletecomment()
	{
		if ($this->Id!="")
		{
			if ($this->Module->Content->deleteComment($this->Id))
			{
				$Return = array('status' => 'success',
				'message' => $this->Template->showMessage('success', 'Komentar telah di hapus'), 
				'data' => ''
				);
			}
		}
		else
		{
			$Return = array('status' => 'error',
			'message' => $this->Template->showMessage('error', 'Ops! ID komentar tidak valid'), 
			'data' => ''
			);			
		}

		echo json_encode($Return);
	}

	function submitmodule()
	{
		$idContent = $_POST['idContent'];
		$vName = $_POST['vName'];
		$vData = $_POST['vData'];
		$vPicture = $this->Pile->simpanImage($_FILES['vPicture'],"cm_".date("Yndhis").rand(0,9).rand(0,9).rand(0,9));
		$vModule = "content";

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
		echo $this->Template->ShowAdmin("content/content_module_edit.html");
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