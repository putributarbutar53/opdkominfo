<?php if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class myfile extends Core
{
	var $Submit, $Action, $Id, $Dir, $uploadDir, $MainURL;
	public function __construct()
	{
		parent::__construct();
		
		//Load General Process
		include '../inc/general_admin.php';
		$this->LoadModule("FileManager");

		$this->uploadDir = $this->Config['upload']['dir']."/";
		$this->MainURL = $this->Config['base']['url'].$this->Config['index']['page'].$this->Config['base']['admin']."/myfile";
		$Dir_ = ($_POST['dir'])?$_POST['dir']:"";		
		$this->Dir = ($Dir_)?$this->uploadDir.$Dir_."/":$this->uploadDir;

		$this->Template->assign("Signature", "file");

		ob_clean();
	}

	function main()
	{
		echo $this->Template->ShowAdmin("myfile/myfile_index.html");
	}

	function getfilemanager()
	{
		$Dir_ = ($_POST['dir'])?$_POST['dir']:"";
		$DirTemp = explode("/",$_POST['dir']);

		$DirectoryList = "<li class=\"breadcrumb-item\"><a href=\"".$this->MainURL."\">home</a></li>";

		if ($_POST['dir']!="")
		{
			for ($i=0;$i<count($DirTemp);$i++)
			{
				 if ($i=="0") { $dTemp.= $DirTemp[$i]; }
				 else { $dTemp .= "/".$DirTemp[$i]; }

				$DirectoryList .= "<li class=\"breadcrumb-item\" aria-current=\"page\"><a href=\"javascript:getfilemanager('".$dTemp."');\">".$DirTemp[$i]."</a></li>";
				// $DirectoryList .= " <a href=\"".$MainURL."dir=".$dTemp."\">".$DirTemp[$i]."</a> / ";
			}
		}
		//$DirectoryList;

		$this->Template->assign("maxFileSize", $this->Config['file']['max_file_size']);
		$this->Template->assign("dirName", (($Dir_=="")?$Dir_:$Dir_."/"));
		$this->Template->assign("dirReal", $Dir_);
		$this->Template->assign("DirectoryList", $DirectoryList);

		$this->Template->assign("uploadURL", $this->Config['base']['url'].$this->uploadDir);

		$this->Pile->fileDestination = $this->Dir;
		$this->Template->assign("Signature", "filemanager");
		$this->Template->assign("listPile", $this->Module->FileManager->readDir($this->Dir));

		$getContent = $this->Template->ShowAdmin("myfile/myfile_list.html");

		$json_data = array('content' => $getContent, 'status' => 'success', 'directory' => $Dir_);
		echo json_encode($json_data);
	}

	function deletefile()
	{
		$this->Pile->fileDestination = $this->Dir;
		$fName = $_GET['fname'];
		$Dir_ = ($_POST['dir'])?$_POST['dir']:"";

		if ($fName!="")
		{
			$DirResult = ($Dir_=="")?$Dir_:$Dir_."/";
			//echo $uploadDir.$DirResult.$fName;
			if (file_exists($this->uploadDir.$DirResult.$fName))
			{
				if ($this->Pile->deleteOldFile($fName))
				{
					$Return = array('status' => 'success',
					'message' => $this->Template->showMessage('success', 'File '.$fName.' telah di hapus'), 
					'data' => ''
					);	
					//$this->Template->reportMessage("success", "File ".$fName." telah di hapus");
				}
			}
		}
		else
		{
			$Return = array('status' => 'error',
			'message' => $this->Template->showMessage('error', 'Ops! Nama file kosong'), 
			'data' => ''
			);
		}
		echo json_encode($Return);
	}

	function deletefolder()
	{
		$this->Pile->fileDestination = $this->Dir;
		$fName = $_GET['fname'];
		$Dir_ = ($_POST['dir'])?$_POST['dir']:"";

		if ($fName!="")
		{
			if ((is_dir($this->Dir.$fName)) AND ($this->Module->FileManager->countDir($this->Dir.$fName)==0))
			{
				if ($this->Module->FileManager->deleteDir($this->Dir.$fName))
				{
					$Return = array('status' => 'success',
					'message' => $this->Template->showMessage('success', 'Directory '.$fName.' telah di hapus'), 
					'data' => ''
					);	
				}
			}
		}
		else
		{
			$Return = array('status' => 'error',
			'message' => $this->Template->showMessage('error', 'Ops! Nama file kosong'), 
			'data' => ''
			);
		}
		echo json_encode($Return);
	}

	function uploadfile()
	{
		$this->Pile->fileDestination = $this->Dir;
		$this->Pile->fileSource = $_FILES['uFile'];
		$this->Pile->PileRecord();
		if ($this->Pile->validateFileType($this->Config['doc']['filetype']))
		{
			if ($this->Pile->validateFileSize($this->Config['file']['max_file_size']))
			{
				if ($this->Pile->copyNewFile())
				{
					$Return = array('status' => 'success',
					'message' => $this->Template->showMessage('success', 'File sudah di Upload ke dalam server'), 
					'data' => ''
					);
				}
			}
			else
			{
				$Return = array('status' => 'error',
				'message' => $this->Template->showMessage('error', 'Maaf! Ukuran file terlalu besar'), 
				'data' => ''
				);	
			}
		}
		else
		{
			$Return = array('status' => 'error',
			'message' => $this->Template->showMessage('error', 'Maaf! Tipe file tidak di izinkan atau file tidak valid'), 
			'data' => ''
			);
		}

		echo json_encode($Return);
	}

	function createdir()
	{
		$uDir = $_POST['uDir'];
		if ($uDir!="")
		{
			if ($this->Module->FileManager->createDir($this->Dir.$uDir, 0777))
			{
				if ($this->Pile->writeFile($this->Dir.$uDir."/index.html","Ops! cannot access directory"))
				{
					$Return = array('status' => 'success',
					'message' => $this->Template->showMessage('success', 'Directory '.$uDir.' sudah di buat'), 
					'data' => ''
					);
				}
			}
		}
		else
		{
			$Return = array('status' => 'error',
			'message' => $this->Template->showMessage('error', 'Maaf! Nama directory masih kosong'), 
			'data' => ''
			);
		}

		echo json_encode($Return);
	}

	//File Upload
	function main_old()
	{
		$uploadDir = $this->Config['upload']['dir']."/";
		$MainURL = $this->Config['base']['url'].$this->Config['index']['page'].$this->Config['base']['admin']."/myfile";
		$Dir_ = ($_POST['dir'])?$_POST['dir']:"";
		
		$this->Dir = ($Dir_)?$uploadDir.$Dir_."/":$uploadDir;
		$DirTemp = explode("/",$_POST['dir']);
		$Show = $_GET['show'];

		$optionShow = ($Show=="open")?"?show=open&":"?";
		$DirectoryList = "<li class=\"breadcrumb-item\"><a href=\"".$MainURL.$optionShow."\">home</a></li>";

		if ($_POST['dir']!="")
		{
			for ($i=0;$i<count($DirTemp);$i++)
			{
				 if ($i=="0") { $dTemp.= $DirTemp[$i]; }
				 else { $dTemp .= "/".$DirTemp[$i]; }

				$DirectoryList .= "<li class=\"breadcrumb-item\" aria-current=\"page\"><a href=\"".$MainURL.$optionShow."dir=".$dTemp."\">".$DirTemp[$i]."</a></li>";
				// $DirectoryList .= " <a href=\"".$MainURL.$optionShow."dir=".$dTemp."\">".$DirTemp[$i]."</a> / ";
			}
		}
		//$DirectoryList;

		$this->Template->assign("maxFileSize", $this->Config['file']['max_file_size']);
		$this->Template->assign("dirName", (($Dir_=="")?$Dir_:$Dir_."/"));
		$this->Template->assign("dirReal", $Dir_);
		$this->Template->assign("DirectoryList", $DirectoryList);

		$this->Template->assign("uploadURL", $this->Config['base']['url'].$uploadDir);

		$this->Pile->fileDestination = $this->Dir;
		$this->Template->assign("Signature", "filemanager");
		//----------------------------------------
		if ($this->Submit)
		{
			if ($_POST['nFile'])
			{
				$this->Pile->fileSource = $_FILES['uFile'];
				$this->Pile->PileRecord();
				if ($this->Pile->validateFileType($this->Config['doc']['filetype']))
				{
					if ($this->Pile->validateFileSize($this->Config['file']['max_file_size']))
					{
						if ($this->Pile->copyNewFile())
							$this->Template->reportMessage("success", "File sudah di Upload ke dalam server");
					}
					else
						$this->Template->reportMessage("error", "Maaf! Ukuran file terlalu besar");
				}
				else
					$this->Template->reportMessage("error", "Maaf! Tipe file tidak di izinkan");
			}

			if ($_POST['nDir']!="")
			{
				if ($this->Module->FileManager->createDir($this->Dir.$_POST['uDir'], 0777))
				{
					if ($this->Pile->writeFile($this->Dir.$_POST['uDir']."/index.html","Ops! cannot access directory"))
						$this->Template->reportMessage("success", "Directory ".$_POST['uDir']." sudah di buat");
				}
			}
			
			$fName = $_GET['fname'];
			switch($this->Action)
			{
				case "deldir":
					if ((is_dir($this->Dir.$fName)) AND ($this->Module->FileManager->countDir($this->Dir.$fName)==0))
					{
						if ($this->Module->FileManager->deleteDir($this->Dir.$fName))
							$this->Template->reportMessage("success", "Directory ".$fName." sudah di hapus");
					}
				break;
				case "delfile":
					$DirResult = ($Dir_=="")?$Dir_:$Dir_."/";
					//echo $uploadDir.$DirResult.$fName;
					if (file_exists($uploadDir.$DirResult.$fName))
					{
						if ($this->Pile->deleteOldFile($fName))
							$this->Template->reportMessage("success", "File ".$fName." telah di hapus");
					}
				break;
			}
			
		}

		$this->Template->assign("listPile", $this->Module->FileManager->readDir($this->Dir));
		echo $this->Template->ShowAdmin("myfile/myfile_index.html");
	}

}
?>