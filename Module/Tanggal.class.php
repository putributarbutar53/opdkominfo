<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class Tanggal extends Core
{
	public function __construct()
	{
		parent::__construct();
	}
	
	//Format Date = yyyy-mm-dd h:i:s
	function datediff($interval, $datefrom, $dateto, $using_timestamps = false) {
		# /*
		# $interval can be:
		# yyyy - Number of full years
		# q - Number of full quarters
		# m - Number of full months
		# y - Difference between day numbers
		# (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
		# d - Number of full days
		# w - Number of full weekdays
		# ww - Number of full weeks
		# h - Number of full hours
		# n - Number of full minutes
		# s - Number of full seconds (default)
		# */
	
		if (!$using_timestamps) {
			$datefrom = strtotime($datefrom, 0);
			$dateto = strtotime($dateto, 0);
		}

		$difference = $dateto - $datefrom; // Difference in seconds

		switch($interval) {
		
		case 'yyyy': // Number of full years
			$years_difference = floor($difference / 31536000);
	
			if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom)+$years_difference) > $dateto) {
				$years_difference--;
			}
		
			if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto)-($years_difference+1)) > $datefrom) {
				$years_difference++;
			}
		
			$datediff = $years_difference;
		break;		 
		case "q": // Number of full quarters
		 
			$quarters_difference = floor($difference / 8035200);
			while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($quarters_difference*3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
				$months_difference++;
			}
		
			$quarters_difference--;
			$datediff = $quarters_difference;
		break;		  
		case "m": // Number of full months		  
			$months_difference = floor($difference / 2678400);
			while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
				$months_difference++;
			}
			$months_difference--;
			$datediff = $months_difference;
		break;
		case 'y': // Difference between day numbers
			$datediff = date("z", $dateto) - date("z", $datefrom);
		break;	 
		case "d": // Number of full days
			$datediff = floor($difference / 86400);
		break;		  
		case "w": // Number of full weekdays 
			$days_difference = floor($difference / 86400);
			$weeks_difference = floor($days_difference / 7); // Complete weeks
			$first_day = date("w", $datefrom);
			$days_remainder = floor($days_difference % 7);
			$odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?
			
			if ($odd_days > 7) { // Sunday
				$days_remainder--;
			}
			
			if ($odd_days > 6) { // Saturday
				$days_remainder--;
			}
			$datediff = ($weeks_difference * 5) + $days_remainder;
		break;		  
		case "ww": // Number of full weeks
			$datediff = floor($difference / 604800);
		break;	  
		case "h": // Number of full hours
			$datediff = floor($difference / 3600);
		break;		  
		case "n": // Number of full minutes
			$datediff = floor($difference / 60);
		break;	  
		default: // Number of full seconds (default)
			$datediff = $difference;
		break;
		}
		  
		return $datediff;
 
	}
	
	/**
	 * Shifts from and to dates by $shift_amount of $shift_units.
	 * are the unit names.
	 * Modified in my version: I am more familiar with the standard
	 * units used in the date function provided by PHP so I changed
	 * the units to them. Also the default in mine is to add dates and 
	 * time radther than subtracting. Cleaned up a bit of the code and
	 * added checking if a datetime variable is set and if not it will
	 * use the todays but you are able to input your own to modify.
	 *
	 *	shift_units:
	 *		months = m;
	 *		days = d;
	 *		years = Y;
	 *		hours = H;
	 *		minutes = i;
	 *		seconds = s;
	 *
	 * @param	string	$datetime	TimeStamp of the time you woud like to edit "YYYY-MM-DD HH:MM:SS". 
	 * @param	integer	$shit_amount The amount you want to shit
	 * @param	string	$shift_unit	Unit you would like to shift
	 * @return timestamp
	 */
    function shift_dates($datetime="", $shift_amount, $shift_unit) {

		/* Check for $datetime */
		if(!$datetime){
			$datetime=date("Y-m-d H:i:s");
		}

		/* Split into separate sections: date and time */
		list($date, $time)=split(" ", $datetime);
		
		/* Break down the date */
		list($year, $month, $day)=split("-", $date, 3);
		
		/* Break down the time */
		list($hour, $min, $sec)=split(":", $time, 3);
		

		/* This is the date shifting area */
		if($shift_unit=="m") {
			$newdate = mktime ($hour,$min,$sec,$month+$shift_amount,$day, $year);
			$newdate = date("Y-m-d H:i:s", $newdate);
		} elseif( $shift_unit=="d") {
			$newdate = mktime ($hour,$min,$sec,$month,$day+$shift_amount, $year);
			$newdate = date("Y-m-d H:i:s", $newdate);
		} elseif ($shift_unit=="Y") {
			$newdate = mktime ($hour,$min,$sec,$month,$day, $year+$shift_amount);
			$newdate = date("Y-m-d H:i:s", $newdate);
		} elseif( $shift_unit=="H") {
			$newdate = mktime ($hour+$shift_amount,$min,$sec,$month,$day, $year);
			$newdate = date("Y-m-d H:i:s", $newdate);
		} elseif ($shift_unit=="i") {
			$newdate = mktime ($hour,$min+$shift_amount,$sec,$month,$day, $year);
			$newdate = date("Y-m-d H:i:s", $newdate);
		} elseif( $shift_unit=="s") {
			$newdate = mktime ($hour,$min,$sec+$shift_amount,$month,$day, $year);
			$newdate = date("Y-m-d H:i:s", $newdate);
		}


		return $newdate;

    }
	
	//Outputnya adalah string time, sehingga dapat di set sesuai dengan format tanggal yg di inginkan
    function shift_tanggal($datetime="", $shift_amount, $shift_unit) {

		/* Check for $datetime */
		if(!$datetime){
			$datetime=date("Y-m-d H:i:s");
		}

		/* Split into separate sections: date and time */
		list($date, $time)=split(" ", $datetime);
		
		/* Break down the date */
		list($year, $month, $day)=split("-", $date, 3);
		
		/* Break down the time */
		list($hour, $min, $sec)=split(":", $time, 3);
		

		/* This is the date shifting area */
		if($shift_unit=="m") {
			$newdate = mktime ($hour,$min,$sec,$month+$shift_amount,$day, $year);
		} elseif( $shift_unit=="d") {
			$newdate = mktime ($hour,$min,$sec,$month,$day+$shift_amount, $year);
		} elseif ($shift_unit=="Y") {
			$newdate = mktime ($hour,$min,$sec,$month,$day, $year+$shift_amount);
		} elseif( $shift_unit=="H") {
			$newdate = mktime ($hour+$shift_amount,$min,$sec,$month,$day, $year);
		} elseif ($shift_unit=="i") {
			$newdate = mktime ($hour,$min+$shift_amount,$sec,$month,$day, $year);
		} elseif( $shift_unit=="s") {
			$newdate = mktime ($hour,$min,$sec+$shift_amount,$month,$day, $year);
		}
		return $newdate;
    }

	function format_date($dateFormat, $datesource)
	{
		list($date, $time) = split(" ", $datesource);
		list($year, $month, $day) = split("-",$date);
		list($hour, $minute, $second) = split(":",$time);
		//echo $hour."-".$minute."-".$second."-".$month."-".$day."-".$year;
		return date($dateFormat, mktime($hour,$minute,$second,$month,$day,$year));
	}

	/**
	 * This converts MM-DD-YYYY to YYYY-MM-DD
	 *
	 * @param  date  $date  MM-DD-YYYY
	 * @return string
	 */
	function machine_date($date){
		
		list($month, $date, $year)=split("-", $date);
		
		return $year . "-" . $month . "-" . $date;

	}
	
	/**
	 * This converts YYYY-MM-DD to MM-DD-YYYY
	 *
	 * @param  date  $date  YYYY-MM-DD
	 * @return string
	 */
	 function human_date($date){
	 
	 	list($year, $month, $date)=split("-", $date);
		
		return $month . "-" . $date . "-" . $year;
	 
	 }
	
	/**
	 * This converts stuff like 3:00am to 03:00 and 4:00pm to like 16:00
	 *
	 * @param  string  $time   Time in the form of 1:00 or whatever
	 * @param  string  $ampm   Values are am or pm
	 * @return string
	 */
	function convert_to_24_hr($time, $ampm){
	
		/* Make sure it's lower case */
		$ampm=strtolower($ampm);
		
		
		/* Split the time */
		list($hour, $min)=split(":", $time);
		
		switch($ampm){
			
			case "am":
				$hour=$this->fill_time($hour);
			break;
			
			case "pm":
				$hour=$hour + 12;
			break;
			
		}
		
		$min=$this->fill_time($min);
		
		return $hour . ":" . $min;
	
	}
	
	/**
	 * This converts stuff like 16:00 to 04:00pm
	 *
	 * @param  string  $time   Time in the form of HH:MI
	 * @return array
	 */
	function convert_from_24_hr($time){
	
		/* Split up the time */
		list($hour, $min)=split(":", $time);
		
		if($hour > 12){
			$hour=$hour-12;
			$ampm="pm";
		}else{
			$ampm="am";
		}
		
		if($hour=="00"){
			$hour="24";
		}
		
		
		$result=array(
					"time" => $hour . ":" . $min,
					"ampm" => $ampm
		);
		
		return $result;
	
	}	
	
	/**
	 * Add a 0 in front of times with 1 character
	 *
	 * @param  string  $string
	 * @return string
	 */
	function fill_time($string){
	
		if(strlen($string)==1){
		
			$string="0" . $string;
		
		}
		return $string;
	
	}
	
	function add_Date($dateFormat, $dateNow, $intervalDay)
	{
		return date($dateFormat,strtotime($dateNow) + (24*3600*$intervalDay));	
	}
	
}

?>