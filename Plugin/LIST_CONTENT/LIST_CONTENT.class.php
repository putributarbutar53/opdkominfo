<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class LIST_CONTENT extends Core
{	
	var $dirContent, $Status, $vModule;

	function __construct()
	{	
		parent::__construct();
		$this->LoadModule("Content");
		$this->LoadModule("Options");
		$this->LoadModule("Tanggal");

		$this->dirContent = $this->Config['content']['dir'];
		$this->Template->assign("dirContent", $this->dirContent);
		$this->Template->assign("relativePath", $this->Template->relativePath());
		$this->Template->assign("URL", $this->getURL());
		$this->Status = "";
		$this->vModule = "content";
	}
	
	function Load($Load1="", $Load2="", $Load3="", $Load4="", $Load5="", $Load6="")
	{
		#Load1 = Category Permalink / New Article
		#Load2 = Max Items
		#Load3 = Format File
		#Load4 = "From"
		
		if ($Load1=="new")
			$Temp = $this->contentArticle("", $Load2, $Load3, $Load4);
		else
			$Temp = $this->contentArticle($Load1, $Load2, $Load3, $Load4);
	}
	
	function contentArticle($catPermalink, $Max, $fileFormat, $From=0)
	{
		$Max_ = (($Max=="") OR ($Max=="0"))?20:$Max;
		$listContent = $this->Module->Content->listContentByPermalink($catPermalink,$From," ORDER BY dPublishDate DESC",$Max_);

		$Data = array();
		for ($i=0;$i<count($listContent);$i++)
		{
			$eye = $this->Module->Content->detailViewBYContent($listContent[$i]['Item']['id']);
			$URL = $this->getURL().$listContent[$i]['Category']['vPermalink']."/".$listContent[$i]['Item']['vPermalink'].".html";
			$Data[$i] = array(	
								'No' => ($i+1), 
								'Item' => $listContent[$i]['Item'], 
								'no_html' => $this->textCut(strip_tags($listContent[$i]['Item']['tDetail']), 150), 
								'URL' => $URL, 
								'Category' => $listContent[$i]['Category'], 
								'Detail_Sub' => $this->Module->Options->getContentSetting($listContent[$i]['Item']['id'], $this->vModule),
								'Eye' => $eye['iView']
						);				
		}

		$this->Template->assign("list", $Data);
		$fileFormat_ = ($fileFormat=="")?"read_format.html":$fileFormat;
		
		echo $this->Template->ShowTemplatePlugin($fileFormat_);
	}
	
	function textCut($text, $limit)
	{
		if (strlen($text)<=$limit)
			return $text;
		else
			return substr($text,0,$limit)."...";
	}
	
}
?>