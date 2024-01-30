<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class Excel extends Core
{
	public function __construct()
	{
		parent::__construct();
	}
	
	function exportWithPage($php_page,$excel_file_name)
	{
		$this->setHeader($excel_file_name);
		require_once "$php_page";
	
	}
	function setHeader($excel_file_name)//this function used to set the header variable
	{
		
		header("Content-type: application/octet-stream");//A MIME attachment with the content type "application/octet-stream" is a binary file.
		//Typically, it will be an application or a document that must be opened in an application, such as a spreadsheet or word processor. 
		header("Content-Disposition: attachment; filename=".$excel_file_name);//with this extension of file name you tell what kind of file it is.
		header("Pragma: no-cache");//Prevent Caching
		header("Expires: 0");//Expires and 0 mean that the browser will not cache the page on your hard drive
	}
	
	function exportToExcel($listData,$Title,$Header_,$excel_file_name)//to export with query
	{
		$header=$Title."<table border=1px>";
			$body.="<tr>";
			for ($k=0;$k<count($Header_);$k++)
			{
				$body.="<td valign=center>".$Header_[$k]."</td>";
			}
			$body.="</tr>";
		
		for($i=0;$i<count($listData);$i++)
		{
			$body.="<tr>";
			for($j=0;$j<count($listData[$i]);$j++)
			{
				$body.="<td valign=top>".$listData[$i][$j]."</td>";
			}
			$body.="</tr>";	
		}
		
		$this->setHeader($excel_file_name);
		echo $header.$body."</table>";
	}

}
?>