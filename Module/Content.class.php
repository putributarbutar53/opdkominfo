<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class Content extends Core
{
	public function __construct()
	{
		parent::__construct();
	}
	
	//Content Directory
	function addDir($Data)
	{
		return $this->Db->add($Data, "cpcontentcategory");
	}
	
	function updateDir($Data, $Id)
	{
		return $this->Db->update($Data, $Id, "cpcontentcategory");
	}

	function deleteDir($Id)
	{
		$Count=$this->Db->sql_query_row("SELECT COUNT(id) FROM cpcontent WHERE idCategory='".$Id."'");
		if ($Count[0]==0)
			return $this->Db->sql_query("DELETE FROM cpcontentcategory WHERE id='".$Id."'");
	}
	
	function detailDir($Id)
	{	
		return $this->Db->sql_query_array("SELECT * FROM cpcontentcategory WHERE id='".$Id."'");
	}

	function detailCategoryByPermalink($vPermalink)
	{
		return $this->Db->sql_query_array("SELECT * FROM cpcontentcategory WHERE vPermalink='".$vPermalink."'");
	}
	
	function listCategory($Max="")
	{
		$MAX = ($Max=="")?"":" LIMIT 0,".$Max;
		$baca=$this->Db->sql_query("SELECT * FROM cpcontentcategory ORDER BY id".$MAX);							
		$i=0;
		while ($Baca=$this->Db->sql_array($baca))
		{
			$Count=$this->Db->sql_query_array("SELECT COUNT(id) AS total FROM cpcontent WHERE idCategory='".$Baca['id']."'");
			$Data[$i] = array(	
				'No' => ($i+1), 
				'Item' => $Baca, 
				'Count' => $Count
			);
			$i++;
		}
		return $Data;
	}
	
	function countPagination($active, $perpage, $category=null) {
		$query = $this->Db->sql_query_array("SELECT COUNT(id) AS total FROM cpcontent");
		$total_records = $query['total'];
		$total_pages = ceil($total_records / $perpage);
		
		$before = $active - 1;
		$after = $active + 1;
		if ($total_pages < 6 ) {
			$previus = ($active != 1) ? $this->Config['base']['url'].$this->Config['index']['page']."pages/berita?page=$before":"#";
			$string .= "<li class=\"page-item  me-auto\"><a class=\"page-link\" href=\"".$previus."\">Previous</a></li>";
			for ($i=1; $i <= $total_pages; $i++) { 
				$activ_ = ($active == $i) ? " active":'';
				$activ_c = ($active == $i) ? " rounded":'';
				$activ_d = ($active == $i) ? ' <span class="sr-only">(current)</span>':'';
				$string .= "<li class=\"page-item$activ_\"><a class=\"page-link border-0 rounded text-dark$activ_c\" href=\"".$this->Config['base']['url'].$this->Config['index']['page']."pages/berita?page=$i\">$i $activ_d</a></li>";
			}
			
			$next = ($active != $total_pages)?$this->Config['base']['url'].$this->Config['index']['page']."pages/berita?page=$after":"#";
			$string .= "<li class=\"page-item ms-auto\"><a class=\"page-link\" href=\"".$next."\">Next</a></li>";
		} else {
			$previus = ($active != 1) ? $this->Config['base']['url'].$this->Config['index']['page']."pages/berita?page=$before":"#";
			$string .= "<li class=\"page-item  me-auto\"><a class=\"page-link\" href=\"".$previus."\">Previous</a></li>";
			if ($active !=1)
			$string .= "<li class=\"page-item\"><a class=\"page-link border-0 rounded text-dark\" href=\"".$this->Config['base']['url'].$this->Config['index']['page']."pages/berita?page=1\">1</a></li>";
			if ($active > 3) {
				$string .= "<li class=\"page-item\"><a class=\"page-link border-0 rounded text-dark\" href=\"#\">...</a></li>";
			} 
			if ($active == $total_pages)
			$string .= "<li class=\"page-item\"><a class=\"page-link border-0 rounded text-dark\" href=\"".$this->Config['base']['url'].$this->Config['index']['page']."pages/berita?page=".($total_pages-2)."\">".($total_pages-2)."</a></li>";
	
			if ($active != 1) 
			$string .= "<li class=\"page-item\"><a class=\"page-link border-0 rounded text-dark\" href=\"".$this->Config['base']['url'].$this->Config['index']['page']."pages/berita?page=$before\">$before</a></li>";
			$string .= "<li class=\"page-item active\"><a class=\"page-link border-0 rounded\" href=\"".$this->Config['base']['url'].$this->Config['index']['page']."pages/berita?page=$active\">$active</a></li>";
			if ($active != $total_pages)
			$string .= "<li class=\"page-item\"><a class=\"page-link border-0 rounded text-dark\" href=\"".$this->Config['base']['url'].$this->Config['index']['page']."pages/berita?page=$after\">$after</a></li>";
			if ($active == 1)
			$string .= "<li class=\"page-item\"><a class=\"page-link border-0 rounded text-dark\" href=\"".$this->Config['base']['url'].$this->Config['index']['page']."pages/berita?page=3\">3</a></li>";
			if ($active < ($total_pages - 2)) {
				$string .= "<li class=\"page-item\"><a class=\"page-link border-0 rounded text-dark\" href=\"#\">...</a></li>";
			}
			if ($active != $total_pages)
			$string .= "<li class=\"page-item\"><a class=\"page-link border-0 rounded text-dark\" href=\"".$this->Config['base']['url'].$this->Config['index']['page']."pages/berita?page=$total_pages\">$total_pages</a></li>";
		
			$next = ($active != $total_pages)?$this->Config['base']['url'].$this->Config['index']['page']."pages/berita?page=$after":"#";
			$string .= "<li class=\"page-item ms-auto\"><a class=\"page-link\" href=\"".$next."\">Next</a></li>";
		}
		return $string;

	}
	function listAllPagination($start, $end, $category=null)
	{
		$baca=$this->Db->sql_query("SELECT * FROM cpcontent ORDER BY dPublishDate DESC, id DESC LIMIT $start, $end");							
		$i=0;
		while ($Baca=$this->Db->sql_array($baca))
		{
			$eye = $this->detailViewBYContent($Baca['id']);
			$detailCategory = $this->detailDir($Baca['idCategory']);
			$countComment = $this->countComment($Baca['id']);
			$Data[$i] = array(	
				'Item' => $Baca, 
				'URL' => $this->Config['base']['url'].$this->Config['index']['page'].$detailCategory['vPermalink']."/".$Baca['vPermalink'].'.html',
				'Category' => $detailCategory,
				'Eye' => $eye['iView'],
				'totalComment'=>$countComment['total']
			);
			$i++;
		}
		return $Data;
	}
	function listAllPaginationContent()
	{
		$baca=$this->Db->sql_query("SELECT * FROM cpcontent ORDER BY dPublishDate DESC, id DESC");							
		$i=0;
		while ($Baca=$this->Db->sql_array($baca))
		{
			$eye = $this->detailViewBYContent($Baca['id']);
			$detailCategory = $this->detailDir($Baca['idCategory']);
			$Data[$i] = array(	
				'Item' => $Baca, 
				'URL' => $this->Config['base']['url'].$this->Config['index']['page'].$detailCategory['vPermalink']."/".$Baca['vPermalink'].'.html',
				'Category' => $detailCategory,
				'Eye' => $eye['iView']
			);
			$i++;
		}
		return $Data;
	}
	
	//Content
	function status($iStatus, $idContent)
	{
		return $this->Db->sql_query("UPDATE cpcontent SET iStatus='".$iStatus."' WHERE id='".$idContent."'");
	}

	function countContent($idCategory)
	{
		return $this->Db->sql_query_row("SELECT COUNT(id) AS total FROM cpcontent WHERE idCategory='".$idCategory."'");
	}

	function detailContent($Id)
	{
		return $this->Db->sql_query_array("SELECT * FROM cpcontent WHERE id='".$Id."'");
	}
	function beforeContent($Id)
	{
		$data = $this->Db->sql_query_array("SELECT * FROM cpcontent WHERE id < '".$Id."' ORDER BY id DESC LIMIT 1");
		$data['category'] = $this->detailDir($data['idCategory']);
		$data['title'] = substr($data['vTitle'], 0, 30);
		$data['title'] .= '...';
		return $data;
	}
	function nextContent($Id)
	{
		$data = $this->Db->sql_query_array("SELECT * FROM cpcontent WHERE id > '".$Id."' ORDER BY id ASC LIMIT 1");
		
		$data['category'] = $this->detailDir($data['idCategory']);
		$data['title'] = substr($data['vTitle'], 0, 30);
		$data['title'] .= '...';
		return $data;
	}

	function detailContentByPermalink($vPermalink)
	{
		return $this->Db->sql_query_array("SELECT * FROM cpcontent WHERE vPermalink='".$vPermalink."'");
	}
	
	function getLastContent()
	{
		return $this->Db->sql_query_array("SELECT * FROM cpcontent ORDER BY id DESC");
	}
	
	function addContent($Data)
	{
		return $this->Db->add($Data, "cpcontent");
	}
	
	function deleteContent($Id)
	{
		$this->Db->sql_query("DELETE FROM cpcontentcomment WHERE idContent='".$Id."'");
		return $this->Db->sql_query("DELETE FROM cpcontent WHERE id='".$Id."'");
	}
	
	function deletePicture($Id)
	{
		return $this->Db->sql_query("UPDATE cpcontent SET vGambar='' WHERE id='".$Id."'");
	}
	
	function updateContent($Data, $Id)
	{
		return $this->Db->update($Data, $Id, "cpcontent");
	}
	
	function listAllContent()
	{
		$baca=$this->Db->sql_query("SELECT * FROM cpcontent ORDER BY id ASC");
							
		$i=0;
		while ($Baca=$this->Db->sql_array($baca))
		{

			$data[$i] = array(		'No' => ($i+1),
									'Item' => $Baca
								);
			$i++;
		}
		return $data;
	}
	
	function listContent($idCategory, $Max="10")
	{
		$_MAX = ($Max=="")?"":" LIMIT 0,".$Max;
		$baca=$this->Db->sql_query("SELECT * FROM cpcontent WHERE idCategory='".$idCategory."' AND iStatus='1' ORDER BY id DESC".$_MAX);
		$i=0;
		while ($Baca=$this->Db->sql_array($baca))
		{
			$data[$i] = array(		'No' => ($i+1),
									'Item' => $Baca,
									'Comment' => $this->countComment($Baca[id])
								);
			$i++;
		}
		return $data;
	
	}
	function listContentByCategory($category, $Max="10")
	{
		$detailCategory = $this->detailCategoryByPermalink($category);
		$idCategory = $detailCategory['id'];
		$_MAX = ($Max=="")?"":" LIMIT 0,".$Max;
		$baca=$this->Db->sql_query("SELECT * FROM cpcontent WHERE idCategory='".$idCategory."' AND iStatus='1' ORDER BY id DESC".$_MAX);
		$i=0;
		while ($Baca=$this->Db->sql_array($baca))
		{
			if (strlen($Baca['tDetail']) >= 200) 
				$desc = substr($Baca['tDetail'], 0, 200) . "...";
			else $desc =$Baca['tDetail'];
			$data[$i] = array(		'No' => ($i+1),
									'Item' => $Baca,
									'Comment' => $this->countComment($Baca['id']),
									'category' => $detailCategory,
									'Module' => $this->listModuleByName($Baca['id']),
									'description' => $desc
								);
			$i++;
		}
		return $data;
	
	}
	function listNewContent($Max="10")
	{
		$_MAX = ($Max=="")?"":" LIMIT 0,".$Max;
		$baca=$this->Db->sql_query("SELECT * FROM cpcontent WHERE iStatus='1' ORDER BY dPublishDate DESC".$_MAX);
		$i=0;
		while ($Baca=$this->Db->sql_array($baca))
		{
			
			// $content = $this->detailContent($Baca['idContent']);
			$eye = $this->detailViewBYContent($Baca['id']);
			if (strlen($Baca['vTitle']) >= 50) 
				$title = substr($Baca['vTitle'], 0, 50) . "...";
			else $title =$Baca['vTitle'];
			$category = $this->detailDir($Baca['idCategory']);
			if ($category['vPermalink'] != 'kliping') {
			$data[$i] = array(		'No' => ($i+1),
									'Item' => $Baca,
									'category' => $this->detailDir($Baca['idCategory']),
									'title' => $title,
									'Eye' => $eye['iView']
								);
								$i++;
				}
		}
		return $data;
	
	}

	function listContentByPermalink($CategoryPermalink="",$u=0,$Sort=NULL,$max="all")
	{
		$u = ($u=="")?0:$u;

		if ($CategoryPermalink!="")
		{
			$getID = $this->detailCategoryByPermalink($CategoryPermalink);
			$idCat = " AND idCategory='".$getID['id']."'";
		}
		
		if ($max=="all")
			$baca = $this->Db->sql_query("SELECT * FROM cpcontent WHERE id!='0' AND iStatus='1'".$idCat.$Sort);
		else
			$baca = $this->Db->sql_query("SELECT * FROM cpcontent WHERE id!='0' AND iStatus='1'".$idCat.$Sort." LIMIT ".$u.", ".$max);

		$i=0;
		while ($Baca=$this->Db->sql_array($baca))
		{
			$category = $this->detailDir($Baca['idCategory']);
		if ($category['vPermalink'] !='kliping'){
			$Data[$i] = array(
				'Item' => $Baca, 
				'Category' => $this->detailDir($Baca['idCategory'])
			);					
			$i++;
		}
		}
		return $Data;
	}

	//Module
	function listModule($idContent, $vModule='')
	{
		if ($vModule) $query = " AND vName='".$vModule."'";
		$baca=$this->Db->sql_query("SELECT * FROM cpcontentmodule WHERE idContent='".$idContent."'".$query." ORDER BY id DESC");
		$i=0;
		while ($Baca=$this->Db->sql_array($baca))
		{
			$data[$i] = array(
				'No' => ($i+1),
				'Item' => $Baca
			);
			$i++;
		}
		return $data;
	}
	function listModuleByName($idContent)
	{
		$baca=$this->Db->sql_query("SELECT * FROM cpcontentmodule WHERE idContent='".$idContent."' ORDER BY id DESC");
		$i=0;
		while ($Baca=$this->Db->sql_array($baca))
		{
			$data[$Baca['vName']] = $Baca;
			$i++;
		}
		return $data;
	}
	function listContentVideo($Max='2')
	{
		// if ($vModule) $query = " AND vName='".$vModule."'";
		$category  = $this->detailCategoryByPermalink('video');
		// $MAX = ($Max=="")?"":" LIMIT 0,".$Max;
		$MAX = ($Max=="") ? "" : " LIMIT 0," . (int)$Max;
		$baca=$this->Db->sql_query("SELECT * FROM cpcontent WHERE idCategory='".$category['id']."' ORDER BY dPublishDate DESC" . $MAX);
		$i=0;
		while ($Baca=$this->Db->sql_array($baca))
		{
			if (strlen($Baca['vTitle']) >= 30) 
				$title = substr($Baca['vTitle'], 0, 30) . "...";
			else $title =$Baca['vTitle'];
			if ($Baca['iStatus'] =='1') {
				$data[$i] = array(
					'No' => ($i+1),
					'Item' => $Baca,
					'category' => $category,
					'view' => $this->detailViewBYContent($Baca['id']),
					'title' => $title,
					'module' => $this->listModuleByName($Baca['id'])
					
				);
				$i++;
			}
		}
		// print_r($data); die();
		return $data;
	}
	
	function addModule($Data)
	{
		return $this->Db->add($Data, "cpcontentmodule");
	}

	function updateModule($Data, $Id)
	{
		return $this->Db->update($Data, $Id, "cpcontentmodule");
	}

	function deleteModule($Id)
	{
		return $this->Db->sql_query("DELETE FROM cpcontentmodule WHERE id='".$Id."'");
	}

	function deleteModulePic($Id)
	{
		return $this->Db->sql_query("UPDATE cpcontentmodule SET vPicture='' WHERE id='".$Id."'");
	}

	function detailModule($Id)
	{
		return $this->Db->sql_query_array("SELECT * FROM cpcontentmodule WHERE id='".$Id."'");
	}
	//View
	function listView($Max="10")
	{
		$MAX = ($Max=="")?"":" LIMIT 0,".$Max;
		$baca=$this->Db->sql_query("SELECT * FROM cpcontentview ORDER BY iView DESC".$MAX);
		$i=0;
		while ($Baca=$this->Db->sql_array($baca))
		{
			$content = $this->detailContent($Baca['idContent']);
			if (strlen($content['vTitle']) >= 30) 
				$title = substr($content['vTitle'], 0, 30) . "...";
			else $title =$content['vTitle'];
			$category = $this->detailDir($content['idCategory']);
			if ($category['vPermalink'] != 'kliping') {
				if($content['iStatus'] == '1') {
					$data[$i] = array(
						'No' => ($i+1),
						'Item' => $Baca,
						'content' => $content,
						'category' => $category,
						'title' => $title
					);
					$i++;
				}
			}
		}
		return $data;
	}
	
	function addView($Data)
	{
		return $this->Db->add($Data, "cpcontentview");
	}

	function updateView($Data, $Id)
	{
		return $this->Db->update($Data, $Id, "cpcontentview");
	}

	function readView($idContent) {
		if ($idContent != "") {
			$data = $this->detailViewBYContent($idContent);
			if ($data) {
				$view = intval($data['iView']) + 1;
				$this->updateView(array('iView'=> $view), $data['id']);
			} else {
				$this->addView(array('iView'=> 1, 'idContent'=> $idContent));
			}
			return true;
		}
	}

	function deleteView($Id)
	{
		return $this->Db->sql_query("DELETE FROM cpcontentview WHERE id='".$Id."'");
	}

	function detailView($Id)
	{
		return $this->Db->sql_query_array("SELECT * FROM cpcontentview WHERE id='".$Id."'");
	}
	function detailViewBYContent($Id)
	{
		return $this->Db->sql_query_array("SELECT * FROM cpcontentview WHERE idContent='".$Id."'");
	}

	//Content Comments
	function addComment($Data)
	{
		return $this->Db->add($Data, "cpcontentcomment");
	}
	
	function countComment($idContent)
	{
		return $this->Db->sql_query_array("SELECT COUNT(*) AS total FROM cpcontentcomment WHERE idContent='".$idContent."'");
	}
	
	function detailComment($Id)
	{
		return $this->Db->sql_query_array("SELECT * FROM cpcontentcomment WHERE id='".$Id."'");
	}
	
	function deleteComment($Id)
	{
		return $this->Db->sql_query("DELETE FROM cpcontentcomment WHERE id='".$Id."'");
	}
	
	function setComment($iStatus, $Id)
	{
		return $this->Db->sql_query("UPDATE cpcontentcomment SET iStatus='".$iStatus."' WHERE id='".$Id."'");
	}

	function listComment($idContent, $iStatus="all")
	{
		if ($iStatus=="all")
			$baca=$this->Db->sql_query("SELECT * FROM cpcontentcomment WHERE idContent='".$idContent."' ORDER BY id DESC");
		else
			$baca=$this->Db->sql_query("SELECT * FROM cpcontentcomment WHERE idContent='".$idContent."' AND iStatus='".$iStatus."' ORDER BY id DESC");
							
		$i=0;
		while ($Baca=$this->Db->sql_array($baca))
		{
			$data[$i] = array(		'No' => ($i+1),
									'Item' => $Baca,
									'Content' => $this->detailContent($Baca['idContent']),
									'date' => date('d F Y', strtotime($Baca['dPublishDate']))
								);
			$i++;
		}
		return $data;
	}
	
	function time_elapsed_string($datetime, $full = false) {
		$now = new DateTime;
		$ago = new DateTime($datetime);
		$diff = $now->diff($ago);
	
		$diff->w = floor($diff->d / 7);
		$diff->d -= $diff->w * 7;
	
		$string = array(
			'y' => 'year',
			'm' => 'month',
			'w' => 'week',
			'd' => 'day',
			'h' => 'hour',
			'i' => 'minute',
			's' => 'second',
		);
		foreach ($string as $k => &$v) {
			if ($diff->$k) {
				$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
			} else {
				unset($string[$k]);
			}
		}
	
		if (!$full) $string = array_slice($string, 0, 1);
		return $string ? implode(', ', $string) . ' ago' : 'just now';
	}

	function listCommentByContent($idContent, $iStatus="all")
	{
		if ($iStatus=="all")
			$baca=$this->Db->sql_query("SELECT * FROM cpcontentcomment WHERE idNews='".$idContent."' ORDER BY id DESC");
		else
			$baca=$this->Db->sql_query("SELECT * FROM cpcontentcomment WHERE idNews='".$idContent."' AND iStatus='".$iStatus."' ORDER BY id DESC");
							
		$i=0;
		while ($Baca=$this->Db->sql_array($baca))
		{

			$data[$i] = array(		'No' => ($i+1),
									'Item' => $Baca,
									'News' => $this->detailNews($Baca['idNews'])
								);
			$i++;
		}
		return $data;
	}

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
	
}
?>
