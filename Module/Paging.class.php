<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class Paging extends Core
{
	var $koneksi;
	var $p;
	var $page;
	var $q;
	var $query;
	var $next;
	var $prev;
	var $number;
	var $dataLink;
	var $URL="";
	var $formatLink="";
	var $front="";
	var $last="";
	var $selected="";
	var $dbStatus;
	
	public function __construct()
	{
		parent::__construct();
		$this->dbStatus = $this->Db->dbStatus;
	}
	
	function setPaging($baris=5, $langkah=5, $prev="Prev", $next="Next", $number="%%number%%")
	{		
		$this->next=$next;
		$this->prev=$prev;
		$this->number=$number;
		$this->p["baris"]=$baris;
		$this->p["langkah"]=$langkah;
		$_SERVER["QUERY_STRING"]=preg_replace("/&page=[0-9]*/","",$_SERVER["QUERY_STRING"]);
		if (empty($_GET["page"])) {
			$this->page=1;
		} else {
			$this->page=$_GET["page"];
		}
	}

	function query($query)
	{
		$kondisi=false;
		// only select
		if (!preg_match("/^[\s]*select*/i",$query)) {
			$query="select ".$query;
		}

		$querytemp = mysqli_query($this->dbStatus,$query);
		$this->p["count"]= mysqli_num_rows($querytemp);

		// total page
		$this->p["total_page"]=ceil($this->p["count"]/$this->p["baris"]);

		// filter page
		if  ($this->page<=1)
			$this->page=1;
		elseif ($this->page>$this->p["total_page"])
			$this->page=$this->p["total_page"];

		// awal data yang diambil
		$this->p["mulai"]=$this->page*$this->p["baris"]-$this->p["baris"];

		$query=$query." limit ".$this->p["mulai"].",".$this->p["baris"];

		$query=mysqli_query($this->dbStatus, $query) or die("Could not connect: ". mysqli_error($this->dbStatus));
		$this->query=$query;
	}
	
	function result()
	{
		return $result=mysqli_fetch_object($this->query);
	}

	function result_assoc()
	{
		return mysqli_fetch_assoc($this->query);
	}

	function print_no()
	{
		$number=$this->p["mulai"]+=1;
		return $number;
	}
	
	function print_color($color1,$color2)
	{
		if (empty($this->p["count_color"]))
			$this->p["count_color"] = 0;
		if ( $this->p["count_color"]++ % 2 == 0 ) {
			return $color=$color1;
		} else {
			return $color=$color2;
		}
	}

	function print_info()
	{
		$page=array();
		$page["start"]=$this->p["mulai"]+1;
		$page["end"]=$this->p["mulai"]+$this->p["baris"];
		$page["total"]=$this->p["count"];
		$page["total_pages"]=$this->p["total_page"];
			if ($page["end"] > $page["total"]) {
				$page["end"]=$page["total"];
			}
			if (empty($this->p["count"])) {
				$page["start"]=0;
			}

		return $page;
	}

	function number_($i,$number)
	{
		return $i;
		//return preg_replace("^(.*)%%number%%(.*)$","\\1$i\\2",$number);
	}

	function print_link()
	{		
		//generate template
		$print_link = false;
	
		$print_link = $this->front;
		if ($this->formatLink!="")
		{
			if ($this->p["count"]>$this->p["baris"]) {
				// print prev
				if ($this->page>1)
				{
					$Change = array(	'#URL' => (($this->URL=="")?$_SERVER["PHP_SELF"]:$this->URL)."?".$this->dataLink."&page=".($this->page-1), 
										'#TITLE' => $this->prev, 
										'#SELECTED' => ""
							);
					$print_link .= strtr($this->formatLink,$Change);
				}
				// set number
				$this->p["bawah"]=$this->page-$this->p["langkah"];
					if ($this->p["bawah"]<1) $this->p["bawah"]=1;

				$this->p["atas"]=$this->page+$this->p["langkah"];
					if ($this->p["atas"]>$this->p["total_page"]) $this->p["atas"]=$this->p["total_page"];

				// print start
				if ($this->page<>1)
				{
					for ($i=$this->p["bawah"];$i<=$this->page-1;$i++)
					{
						$Change = array(	'#URL' => (($this->URL=="")?$_SERVER["PHP_SELF"]:$this->URL)."?".$this->dataLink."&page=".$i, 
														'#TITLE' => $this->number_($i,$this->number), 
														'#SELECTED' => ""
												);
						$print_link .= strtr($this->formatLink,$Change);
					}
				}
				// print active
				if ($this->p["total_page"]>1)
				{
					$Change = array(	'#URL' => "", 
													'#TITLE' => $this->number_($this->page,$this->number), 
													'#SELECTED' => $this->selected
											);
					$print_link .= strtr($this->formatLink,$Change);
				}
				
				// print end
				for ($i=$this->page+1;$i<=$this->p["atas"];$i++)
				{
					$Change = array(	'#URL' => (($this->URL=="")?$_SERVER["PHP_SELF"]:$this->URL)."?".$this->dataLink."&page=".$i, 
													'#TITLE' => $this->number_($i,$this->number), 
													'#SELECTED' => ""
											);
					$print_link .= strtr($this->formatLink,$Change);
				}
				
				// print next
				if ($this->page<$this->p["total_page"])
				{
					$Change = array(	'#URL' => (($this->URL=="")?$_SERVER["PHP_SELF"]:$this->URL)."?".$this->dataLink."&page=".($this->page+1), 
										'#TITLE' => $this->next, 
										'#SELECTED' => ""
									);
					$print_link .= strtr($this->formatLink,$Change);
				}
			}
		}
		else
		{
			if ($this->p["count"]>$this->p["baris"]) {
				// print prev
				if ($this->page>1)
				$print_link .= "<a href=\"".(($this->URL=="")?$_SERVER["PHP_SELF"]:$this->URL)."?".$this->dataLink."&page=".($this->page-1)."\" class=\"pagingLink\" id=\"pagingLink\">".$this->prev."</a>\n";
				// set number
				$this->p["bawah"]=$this->page-$this->p["langkah"];
					if ($this->p["bawah"]<1) $this->p["bawah"]=1;

				$this->p["atas"]=$this->page+$this->p["langkah"];
					if ($this->p["atas"]>$this->p["total_page"]) $this->p["atas"]=$this->p["total_page"];

				// print start
				if ($this->page<>1)
				{
					for ($i=$this->p["bawah"];$i<=$this->page-1;$i++)
						$print_link .="<a href=\"".(($this->URL=="")?$_SERVER["PHP_SELF"]:$this->URL)."?".$this->dataLink."&page=$i\" class=\"pagingLink\" id=\"pagingLink\">".$this->number_($i,$this->number)."</a>\n";
				}
				// print active
				if ($this->p["total_page"]>1)
					$print_link .= "<span style=\"pagingDefault\" id=\"pagingDefault\">".$this->number_($this->page,$this->number)."</span>\n";

				// print end
				for ($i=$this->page+1;$i<=$this->p["atas"];$i++)
				$print_link .= "<a href=\"".(($this->URL=="")?$_SERVER["PHP_SELF"]:$this->URL)."?".$this->dataLink."&page=$i\" class=\"pagingLink\" id=\"pagingLink\">".$this->number_($i,$this->number)."</a>\n";
				// print next
				if ($this->page<$this->p["total_page"])
				$print_link .= "<a href=\"".(($this->URL=="")?$_SERVER["PHP_SELF"]:$this->URL)."?".$this->dataLink."&page=".($this->page+1)."\" class=\"pagingLink\" id=\"pagingLink\">".$this->next."</a>\n";
			}
		}
		$print_link .=$this->last;
		
		return $print_link;
	}
	
	/*
	function print_link()
	{		
		//generate template
		$print_link = false;
		$print_link = $this->front;
		if ($this->p["count"]>$this->p["baris"]) {
			// print prev
			if ($this->page>1)
				$print_link .= "<a href=\"".(($this->URL=="")?$_SERVER["PHP_SELF"]:$this->URL)."?".$this->dataLink."&page=".($this->page-1)."\" class=\"prev\"><span class=\"fa fa-chevron-left\"></span>".$this->prev."</a>\n";
			
			$print_link .= "<ul>";
			
			// set number
			$this->p["bawah"]=$this->page-$this->p["langkah"];
				if ($this->p["bawah"]<1) $this->p["bawah"]=1;

			$this->p["atas"]=$this->page+$this->p["langkah"];
				if ($this->p["atas"]>$this->p["total_page"]) $this->p["atas"]=$this->p["total_page"];

			// print start
			if ($this->page<>1)
			{
				for ($i=$this->p["bawah"];$i<=$this->page-1;$i++)
					$print_link .="<li><a href=\"".(($this->URL=="")?$_SERVER["PHP_SELF"]:$this->URL)."?".$this->dataLink."&page=$i\">".$this->number_($i,$this->number)."</a></li>\n";
			}
			// print active
			if ($this->p["total_page"]>1)
				$print_link .= "<li class=\"active\"><a href=\"\">".$this->number_($this->page,$this->number)."</a></li>\n";

			// print end
			for ($i=$this->page+1;$i<=$this->p["atas"];$i++)
				$print_link .= "<li><a href=\"".(($this->URL=="")?$_SERVER["PHP_SELF"]:$this->URL)."?".$this->dataLink."&page=$i\">".$this->number_($i,$this->number)."</a></li>\n";
			
			$print_link .= "</ul>";
			
			// print next
			if ($this->page<$this->p["total_page"])
				$print_link .= "<a href=\"".(($this->URL=="")?$_SERVER["PHP_SELF"]:$this->URL)."?".$this->dataLink."&page=".($this->page+1)."\" class=\"next\">".$this->next."<span class=\"fa fa-chevron-right\"></span></a>\n";
		}
		$print_link .=$this->last;
		return $print_link;
	}
	*/

}
?>