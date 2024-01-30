<?php
// 2ONC Frameworks ver. 0.1, BETA RELEASE
// Copyright @2008 - 2ON COMPANY, Inc
// http://labs.2onc.com
// Email: support@2onc.com
ob_start();

//Load Main Object
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
error_reporting(0);
/*
if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == ""){
    $redirect = "https://www.".preg_replace("#www.#","",$_SERVER['HTTP_HOST']).$_SERVER['REQUEST_URI'];
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: $redirect");
}

if (!(eregi("www.",$_SERVER['HTTP_HOST'])))
{
    $redirect = "https://www.".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: $redirect");
}
*/

session_start();
define('ONPATH', ''); //Protect Our Frameworks
include '../config/core.php';
$ConfigTemp = readConfig("../config");
for ($i = 0; $i < count($ConfigTemp); $i++) {
	if (file_exists("../config/" . $ConfigTemp[$i]['fileName']))
		include '../config/' . $ConfigTemp[$i]['fileName'];
}
include_once("../Core/Core.class.php");

$Core = new Core;
//Get URL Data
//echo $_SERVER['SCRIPT_NAME']." | ".$_SERVER['REQUEST_URI'];

$Separate = explode("index.php", $_SERVER['SCRIPT_NAME']);
if (($Separate[0] == "") or ($Separate[0] == "/")) {
	$_2ONCTEMP = explode("?", $_SERVER['REQUEST_URI']);
	$Parsing = ($config['index']['page'] == "") ? $_2ONCTEMP[0] . "/" : $_2ONCTEMP[0];
} else {
	$_2ONC_ = explode($Separate[0], $_SERVER['REQUEST_URI']);
	$_2ONCTEMP = explode("?", $_2ONC_[1]);
	$Parsing = ($config['index']['page'] == "") ? "/" . $_2ONCTEMP[0] . "/" : $_2ONCTEMP[0];
}
$_2ONC = explode("/", $Parsing);
//Get Default Route
if ($_2ONC[1] == "")
	$_dClass = $config['route'];
else
	$_dClass = $_2ONC[1];

//Check if file exists

$DefaultIndex = false;
if ($_dClass == $config['base']['admin']) {
	if ($_2ONC[2] == "")
		$dClass_ = $config['route'];
	else
		$dClass_ = $_2ONC[2];

	$DefaultIndex = true;
	if ((file_exists("../" . $config['base']['admin'] . "/" . strtolower($dClass_) . ".class.php"))) {
		$Temp = $Core->load($config['base']['admin'] . "." . $dClass_);
		$Method = ($_2ONC[3] == "") ? "main" : $_2ONC[3];
		$Main = ($_2ONC[3] == "") ? "\$Temp->main()" : "\$Temp->" . $_2ONC[3] . "()";
		//Check if function on object is exists
		if (in_array($Method, get_class_methods($Temp)))
			eval($Main . ";");
		else {
			eval("\$Temp->main();");
			//$Core->Error("404"); //Throw on error when can't load
		}
	} else
		$Core->Error("404");
}

//Api
if ($_dClass == $config['base']['api']) {
	if ($_2ONC[2] == "")
		$dClass_ = $config['route'];
	else
		$dClass_ = $_2ONC[2];

	$DefaultIndex = true;

	if ((file_exists("../" . $config['base']['api'] . "/" . strtolower($dClass_) . ".class.php"))) {
		$Temp = $Core->load($config['base']['api'] . "." . $dClass_);
		$Method = ($_2ONC[3] == "") ? "main" : $_2ONC[3];
		$Main = ($_2ONC[3] == "") ? "\$Temp->main()" : "\$Temp->" . $_2ONC[3] . "()";
		//Check if function on object is exists
		if (in_array($Method, get_class_methods($Temp)))
			eval($Main . ";");
		else {
			eval("\$Temp->main();");
			//$Core->Error("404"); //Throw on error when can't load
		}
	} else
		$Core->Error("404");
}

if ($DefaultIndex == false) {
	if ((file_exists("../Application/" . strtolower($_dClass) . ".class.php"))) {
		$Temp = $Core->load("Application." . $_dClass);
		$Method = ($_2ONC[2] == "") ? "main" : $_2ONC[2];
		$Main = ($_2ONC[2] == "") ? "\$Temp->main()" : "\$Temp->" . $_2ONC[2] . "()";
		//Check if function on object is exists

		if (in_array($Method, get_class_methods($Temp)))
			eval($Main . ";");
		else {
			eval("\$Temp->main();");
			//$Core->Error("404"); //Throw on error when can't load
		}
	} else {
		if ((file_exists("../Application/contentpage.class.php"))) {
			$Temp = $Core->load("Application.contentpage");
			$Temp->main();
		} else
			$Core->Error("404");
	}
}
