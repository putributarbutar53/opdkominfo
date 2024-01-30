<?php if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class sitemap extends Core
{
	function __construct()
	{
		parent::__construct();

		//Load General Process
		include '../inc/general.php';

		$this->LoadModule("Page");
		//$this->LoadModule("Content");
		$this->LoadModule("Bot");
	}

	//Page
	function main()
	{
		if (!$this->Module->Bot->isThatBot()) {
			$this->Error("404");
			exit;
		}
		ob_clean();
		$writer = new XMLWriter;
		$writer->openURI('php://output');
		$writer->startDocument('1.0', 'UTF-8');
		$writer->startElement('urlset');
		$writer->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
		
		//https://www.website.com/sitemap/page
		
		$page = $this->uri(2);
		if ($page=="page")
		{
			$baca = $this->Db->sql_query("SELECT * FROM cppage WHERE cStatus!='".$this->Module->Page->getIDStatus("OPTIONAL")."' AND iShow='0' ORDER BY id DESC");
			while ($Baca=$this->Db->sql_array($baca))
			{
				if ($Baca['vPermalink']!="")
				{
					$writer->startElement('url');
					$writer->writeElement('loc', $this->Config['base']['url'] . $this->Config['index']['page'] . 'pages/' . $Baca['vPermalink'] . '.html');
					$writer->writeElement('changefreq', 'monthly');
					$writer->writeElement('priority', 1);
					$writer->endElement();
				}
			}
		}
		
		$writer->endElement();
		$writer->endDocument();
		header('Content-type: text/xml');
		$writer->flush();
	}
}

?>