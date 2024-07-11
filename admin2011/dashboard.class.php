<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class dashboard extends Core
{
	var $Submit, $Action, $Id;
	public function __construct()
	{
		parent::__construct();
		ob_clean();
		
		$this->LoadModule("Date");
		$this->LoadModule("Paging");

		//Load General Process
		include '../inc/general_admin.php';
		$this->Module->Paging->setPaging(21,5);
		
		$this->Template->assign("dirProducts", $this->Config['upload']['products']);
		$this->Template->assign("Signature", "dashboard");
	}
	
	function main()
	{ 
		echo $this->Template->ShowAdmin("dashboard.html");
	}

	private function isJSON($string){
	   return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
	}
	
	function pageconf()
	{
		$this->Module->Auth->verifyAdmin(0);	
		
		if ($this->Submit)
		{
			switch($this->Action)
			{
				case "pageconf":
					$status=false;
					$idPage = $_POST['idPage'];
					if ($this->Module->Page->countPageConf($idPage)==0)
					{
						if ($this->Module->Page->addPageConf($idPage,array($_POST['iAddPage'],$_POST['iPictureIcon'],$_POST['iContent'],$_POST['iMenuURL'],$_POST['iLinkTarget'],$_POST['iEditor'])))
							$status=true;
					}
					else
					{
						if ($this->Module->Page->updatePageConf($idPage,array($_POST['iAddPage'],$_POST['iPictureIcon'],$_POST['iContent'],$_POST['iMenuURL'],$_POST['iLinkTarget'],$_POST['iEditor'])))
							$status=true;
					}
					
					if ($status)
						$this->Template->reportMessage("success", "Page Configuration has been set");
					else
						$this->Template->reportMessage("error", "Page Configuration has not been set");
				break;
			}
		}
			
		$this->Template->assign("Signature", "pageconf");
		$listPage = $this->Module->Page->listPage(NULL,"","");
		$this->Template->assign("listPage", $listPage);
		$idPage = ($_GET['idPage'])?$_GET['idPage']:$listPage[0]['id'];
		$this->Template->assign("idPage", $idPage);
		$DetailConf = $this->Module->Page->detailPageConf($idPage);
		$this->Template->assign("DetailConf", $DetailConf);
		echo $this->Template->ShowAdmin("main_pageconf.html");
	}
	
	function pagestatusconf()
	{
		$this->Module->Auth->verifyAdmin(0);	
		
		if ($this->Submit)
		{
			switch($this->Action)
			{
				case "pageconf":
					$status=false;
					$idPageStatus = $_POST['idPageStatus'];
					if ($this->Module->Page->countStatusConf($idPageStatus)==0)
					{
						if ($this->Module->Page->addStatusConf($idPageStatus,array($_POST['iAdd'])))
							$status=true;
					}
					else
					{
						if ($this->Module->Page->updateStatusConf($idPageStatus,array($_POST['iAdd'])))
							$status=true;
					}
					
					if ($status)
						$this->Template->reportMessage("success", "Page directory configuration has been set");
					else
						$this->Template->reportMessage("error", "Page directory configuration has not been set");		
				break;
			}
		}
		
		$this->Template->assign("Signature", "pageconf");
		$listPageStatus = $this->Module->Page->listPageStatus();
		$this->Template->assign("listPageStatus", $listPageStatus);
		$idPageStatus = ($_GET['idPageStatus'])?$_GET['idPageStatus']:$listPageStatus[0]['id'];
		$this->Template->assign("idPageStatus", $idPageStatus);
		$DetailConf = $this->Module->Page->detailStatusConf($idPageStatus);
		$this->Template->assign("DetailConf", $DetailConf);
		echo $this->Template->ShowAdmin("main_statusconf.html");
	}
	
	function loadinbox()
	{
		echo $this->Template->ShowAdmin("loadinbox.html");
	}

}
