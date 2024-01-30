<?php
$this->LoadModule("Website");
$this->LoadModule("Auth");
//check session and register the page application
$Username = $_SESSION['zxcvbnm'];
$this->Module->Auth->checkSession($Username);

$DomainName = explode("/",preg_replace("#https://#","",$App['main']['url']));
$this->Template->assign("Username", $Username." (".$DomainName[0].")");
$this->Template->assign("LastLogin", $this->Module->Auth->getLastLogin($Username)." WIB");
$this->Template->assign("AdminMode", $this->Config['admin']['mode']);

$DetailAdmin = $this->Module->Auth->detailAdmin(NULL,$Username);
$DetailAdminAuth = json_decode($DetailAdmin['vAuth'], true);
$DetailAdminDir = json_decode($DetailAdmin['vDir'], true);

$DetailAdmin = array_merge($DetailAdmin, 
				array('auth' => $DetailAdminAuth), 
				array('dir' => $DetailAdminDir)
			);
			
$this->Template->assign("DetailAdmin", $DetailAdmin);
$this->Template->assign("DetailAdminAuth", $DetailAdminAuth);
$this->Template->assign("DetailAdminDir", $DetailAdminDir);

$this->Username = $Username;
$this->DetailAdmin = $DetailAdmin;

$this->LoadModule("Banner");
$this->LoadModule("Content");
$this->LoadModule("Page");
$this->LoadModule("Photo");

$this->Template->assign("relativePath", $this->Template->relativePath());
//------End Not editable session--------
//User Auth
$userAuth_ = $DetailAdmin['vAuth'];
for ($r=0;$r<strlen($userAuth_);$r++)
{
	$userAuth[$r] = substr($userAuth_,$r,1);
}

$this->Template->assign("userAuth", $userAuth);
$this->Module->Auth->addAuthAdmin($userAuth, $DetailAdmin);

function PLUGIN($param)
{
	global $Core;
	extract($param);
	if ($NAME!="")
	{
		$Core->LoadPlugin($NAME);
		$Core->Plugin->$NAME->Load($VAR1, $VAR2, $VAR3, $VAR4, $VAR5, $VAR6);
	}
}
$this->Template->register_function("PLUGIN", "PLUGIN");

//Action
$this->Submit=($_POST['submit'])?$_POST['submit']:$_GET['submit'];
$this->Action=($_POST['action'])?$_POST['action']:$_GET['action'];

$this->Id=($_POST['id'])?$_POST['id']:$_GET['id'];
$this->Template->assign("Id", $this->Id);

$this->Do=($_POST['do'])?$_POST['do']:$_GET['do'];
$this->Template->assign("Do", $this->Do);

$this->Show=($_POST['show'])?$_POST['show']:$_GET['show'];
$this->Template->assign("Show", $this->Show);

$this->vCompare=($_POST['vCompare'])?$_POST['vCompare']:$_GET['vCompare'];
$this->Template->assign("vCompare", $this->vCompare);

$this->vTeks=($_POST['vTeks'])?$_POST['vTeks']:$_GET['vTeks'];
$this->Template->assign("vTeks", $this->vTeks);

$this->Template->assign("Page", $_GET['page']);

?>