<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class UserOnline extends Core
{

    /* public: connection parameters */
    var $page     = "";
	var $ip       = "";
    var $timeoutSeconds = 600;
	var $idMember = "";
    
    public function __construct($visitor=true) 
	{	
		parent::__construct();
	  	//choose how you want this done depending on your version and preference
	  	//$this->ip = $REMOTE_ADDR;
	  	$this->ip = $_SERVER['REMOTE_ADDR'];
	  	//$this->page = $PHP_SELF;
	  	//$this->page = $_SERVER['PHP_SELF'];
	  	$this->page = $_SERVER['REQUEST_URI'];
	  	$this->idMember = ($_SESSION['uID']=="")?0:$_SESSION['uID'];
			
     	 if ($visitor)
	    	$this->addVisitor();
    }//constructor
 
    function getNumber($siteOrFile="site") 
	{
		  $timeout = $this->getTimeOut();
		  if ($siteOrFile == "site")
			$sql = "SELECT DISTINCT ip FROM cpusersonline WHERE timestamp >= ".$timeout;
		  else
			$sql = "SELECT DISTINCT ip FROM cpusersonline WHERE file='".$this->page."' and  timestamp >= ".$timeout;
	
		  $result = $this->Db->sql_query($sql);
			
		  return $this->Db->sql_row($result); 
        
    }//getNumber
	
    function getMemberNumber($siteOrFile="site") 
	{
		  $timeout = $this->getTimeOut();
		  if ($siteOrFile == "site")
			$sql = "SELECT DISTINCT idMember FROM cpusersonline WHERE timestamp >= ".$timeout;
		  else
			$sql = "SELECT DISTINCT idMember FROM cpusersonline WHERE file='".$this->page."' AND  timestamp >= ".$timeout;
	
		  $result = $this->Db->sql_query($sql);
			
		  return $this->Db->sql_row($result); 
        
    }//getNumber

	function listMemberOnline($siteOrFile="site")
	{
		  $timeout = $this->getTimeOut();
		  if ($siteOrFile == "site")
			$sql = "SELECT DISTINCT idMember FROM cpusersonline WHERE timestamp >= ".$timeout;
		  else
			$sql = "SELECT DISTINCT idMember FROM cpusersonline WHERE file='".$this->page."' AND  timestamp >= ".$timeout;
	
		  $baca = $this->Db->sql_query($sql);
			$i=0;
			while ($Baca = $this->Db->sql_array($baca))
			{
				$Data[$i] = $Baca;
				$i++;
			}
			
		  return $Data; 
	}	
    
    function printNumber($siteOrFile="site") 
	{
	  	echo $this->getNumberInfo($siteOrFile);
    }//printNumber
	
    function getNumberInfo($siteOrFile="site") 
	{
		  // I use templates so I just want to get the string and pass it to
		  // my template object
		  $cnt = $this->getNumber($siteOrFile);
		  if( $cnt == 1) 
			 $output = "1 visitor online";
		  else 
			 $output = $cnt." visitors online";
		  return $output;
    }//getNumberInfo
	
    
    function refresh() {
        global $REMOTE_ADDR, $PHP_SELF;
        
		$timeout = $this->getTimeOut();
        $sql = "DELETE FROM cpusersonline WHERE timestamp < ".$timeout;
        $this->Db->sql_query($sql);
    }//refresh
	
	function getTimeOut()
	{
        $currentTime = time();
        return $currentTime - $this->timeoutSeconds;	  
	}//getTimeOut
    
	function addVisitor() 
	{
        global $REMOTE_ADDR, $PHP_SELF;
        
        $currentTime = time();

        $sql = "INSERT INTO cpusersonline VALUES ('".$currentTime."','".$this->ip."','".$this->page."','".$this->idMember."')";
        $this->Db->sql_query($sql);
     }//addVisitor
	 
	 function refreshMember($idMember)
	 {
	 	return $this->Db->sql_query("DELETE FROM cpusersonline WHERE idMember=".$idMember);
	 }
	 
}//class UsersOnline

?>