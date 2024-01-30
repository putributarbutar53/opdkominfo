<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class FileManager extends Core
{
	public function __construct()
	{
		parent::__construct();
		global $sUsername;
		$this->sUsername = $sUsername;
	}
	
	function countDir($Directory)
	{
			$dir=opendir($Directory);
			$dataFile=0; 
			while ($file=readdir($dir)){
			
			clearstatcache();
			
			if (!(($file==".") OR ($file=="..") OR ($file=="index.php") OR ($file=="index.html") OR ($file=="index.htm"))){
					if (is_dir($Directory."/".$file))
						$dataFile++;
				}
			}
			closedir($dir);
			return $dataFile;
	}

	function countFile($Directory)
	{
			$dir=opendir($Directory);
			$dataFile=0; 
			while ($file=readdir($dir)){
			
			clearstatcache();
			
			if (!(($file==".") OR ($file=="..") OR ($file=="index.php") OR ($file=="index.html") OR ($file=="index.htm"))){
					if (!is_dir($Directory."/".$file))
						$dataFile++;
				}
			}
			closedir($dir);
			return $dataFile;
	}

	function readDir($Directory)
	{
		$dir=opendir($Directory);
			$i=0;
			$j=0;
			$k=0;

			$dataFile = array();
			$dataFolder = array();

			while ($file=readdir($dir)){	
				clearstatcache();		
				if (!(($file==".") OR ($file=="..") OR ($file=="index.php") OR ($file=="index.html") OR ($file=="index.htm"))){
					$fileDesc = explode(".",$file);
					if (is_dir($Directory.$file))
					{
						$dataFolder[$k] = array (	"fileName" => $file,
												"fileType" => $fileDesc[1],
												"icon" => (is_dir($Directory.$file)?"dir_icon.gif":"file_icon.gif"),
												"type" => (is_dir($Directory.$file)?"dir":"file"),
												"totalDir" => (is_dir($Directory.$file)?$this->countDir($Directory.$file):0),
												"totalFile" => (is_dir($Directory.$file)?$this->countFile($Directory.$file):0)
									);
						$k++;
					}
					else
					{
						$dataFile[$j] = array (	"fileName" => $file,
												"fileType" => $fileDesc[1],
												"icon" => (is_dir($Directory.$file)?"dir_icon.gif":"file_icon.gif"),
												"type" => (is_dir($Directory.$file)?"dir":"file"),
												"totalDir" => (is_dir($Directory.$file)?$this->countDir($Directory.$file):0),
												"totalFile" => (is_dir($Directory.$file)?$this->countFile($Directory.$file):0)
											);
						//echo $dataFile[$i]['fileName'];
						$j++;
					}
					$i++;
				}
			}
			closedir($dir);
			
			if (is_array($dataFolder))
				return array_merge($dataFolder,$dataFile);
			else
				return $dataFile;
	}
	
	function readFolder($Directory)
	{
		$dir=opendir($Directory);
			$i=0;
			$k=0;
			while ($file=readdir($dir)){	
				clearstatcache();		
				if (!(($file==".") OR ($file=="..") OR ($file=="index.php") OR ($file=="index.html") OR ($file=="index.htm"))){
					$fileDesc = explode(".",$file);
					if (is_dir($Directory.$file))
					{
						$dataFolder[$k] = array (	"fileName" => $file,
												"fileType" => $fileDesc[1],
												"icon" => (is_dir($Directory.$file)?"dir_icon.gif":"file_icon.gif"),
												"type" => (is_dir($Directory.$file)?"dir":"file"),
												"totalDir" => (is_dir($Directory.$file)?$this->countDir($Directory.$file):0) );
						$k++;
					}
					$i++;
				}
			}
			closedir($dir);
			return $dataFolder;
	}

	function deleteDir($dir){
	 	$current_dir = opendir($dir);
		while($entryname = readdir($current_dir)){
			if (is_dir("$dir/$entryname") and ($entryname != "." and $entryname!="..")){
		   		deleteDir("${dir}/${entryname}");
			}	
			elseif ($entryname != "." and $entryname!=".."){
		   		unlink("${dir}/${entryname}");
			}
	 	}
	 		closedir($current_dir);
	 		return rmdir(${dir});
	}

	function checkDir($strPath)
	{
		if (is_dir($strPath)) return true;
		else return false;
	}

	function createDir($strPath, $mode){
 		if (is_dir($strPath)) return true;
 		return mkdir($strPath, $mode);
	}

	function checkSnapshot($fileSnapshot)
	{
		if (file_exists($fileSnapshot))
			return $fileSnapshot;
		else
			return "";
	}

	function readTemplate($Directory, $vPageName)
	{
		$dir=opendir($Directory);
		$i=0;
		$k=0;
		while ($file=readdir($dir)){
			clearstatcache();		
			if (!(($file==".") OR ($file=="..") OR ($file=="index.php") OR ($file=="index.html") OR ($file=="index.htm"))){

				if (preg_match("#".$vPageName."#",$file))
				{
					$dataFolder[$k] = array (
						"fileName" => preg_replace("#.html#","",$file)
					);
					$k++;
				}

				$i++;
			}
		}
		closedir($dir);
		return $dataFolder;
	}
	
}
?>