<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class
class STAR extends Core
{	
	function __construct()
	{	
		parent::__construct();
	}
	
	function Load($Load1="", $Load2="", $Load3="", $Load4="", $Load5="", $Load6="")
	{
		//Load1 = Nilai Rating
		//Load2 = Full Star
		//Load3 = Half Star
		//Load4 = ""

		$Temp = $this->Show($Load1);	
		echo $Temp;
	}
	
	function Show($rating=5)
	{
		//Half: <i class="fa fa-star-half-o"></i>
		$HTML = "";
		if ($rating>0)
		{
			for ($i=0;$i<$rating;$i++)
			{
				$HTML .= "<i class=\"fa fa-star\"></i>";
			}
		}
		
		if ($HTML=="")
			return "<i class=\"fa fa-star\" style=\"color:#CCCCCC;\"></i><i class=\"fa fa-star\" style=\"color:#CCCCCC;\"></i><i class=\"fa fa-star\" style=\"color:#CCCCCC;\"></i><i class=\"fa fa-star\" style=\"color:#CCCCCC;\"></i><i class=\"fa fa-star\" style=\"color:#CCCCCC;\"></i>";
		else
			return $HTML;
	}
		
}
?>