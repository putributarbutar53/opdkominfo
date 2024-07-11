<?php if (!defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class	

//ini_set('error_reporting', E_STRICT);
//Set TIMEZONE
//putenv("TZ=Asia/Jakarta");
//date_default_timezone_set('Asia/Jakarta');
//$config['db']['zone'] = "+7:00";
$config['db']['zone'] = "";

//Database Settings
$config['db']['host']    = "localhost";
$config['db']['user']    = "root";
$config['db']['pass']     = "";
$config['db']['name']    = "db_opdkominfo";

//Base URL
$config['main']['url'] = "http://" . $_SERVER['HTTP_HOST'] . "/opdkominfo/public/";
$config['base']['url'] = "http://" . $_SERVER['HTTP_HOST'] . "/opdkominfo/public/";
$config['file']['url']    = "http://" . $_SERVER['HTTP_HOST'] . "/opdkominfo/public/upload/";

//Index Page
$config['index']['page'] = "index.php/";

//Default Domain
$config['default']['domain'] = "localhost";

//Themes
$config['main']['themes'] = "themes/kominfo/";
$config['mobile']['themes'] = "themes/kominfo/";
$config['admin']['themes'] = "themes/admin/";

//Compile Dir
$config['compile']['topdir'] = "../cache/";
$config['compile']['dir'] = $config['compile']['topdir'] . "cached";
$config['compile']['mobile'] = $config['compile']['topdir'] . "mcached";
$config['compile']['admin'] = $config['compile']['topdir'] . "templates_c";

//Home
$config['home']['themes'] = "themes/home/";
$config['home']['compile'] = $config['compile']['topdir'] . "cached_1";

//Admin
$config['base']['admin'] = "admin2011";
$config['admin']['url'] = $config['main']['url'] . $config['index']['page'] . $config['base']['admin'] . "/";
//API
$config['base']['api'] = "api";

//Admin Username
$config['admin']['user'] = "admin";
//Admin Mode
$config['admin']['mode'] = "admin";

//Get Route
$config['route'] = "index";

//Send Email
$config['send']['email'] = "no";

//Max Member Log
$config['max']['log'] = 50;

//Default Session Time
$config['session']['time'] = 3600; //dalam Detik

//Home Path
$config['home']['path'] = "/home/profile1/";
