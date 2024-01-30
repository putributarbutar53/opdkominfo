<?php  if ( ! defined('ONPATH')) exit('No direct script access allowed'); //Mencegah akses langsung ke class

class Readoutside extends Core
{
    var $tempFolder;
    var $tempFiles = array();

    function __destruct () {
        foreach ($this->tempFiles as $file) {
            unlink($file['temp']);
        }
    }
    
	public function __construct()
	{
		parent::__construct();
		$this->tempFolder = $this->Config['temp']['dir'];
	}
	    
    function get($url) {
        array_unshift($this->tempFiles, array(
            'extension'=> array_pop(explode('.', $url)),
            'original'=> basename($url),
            'temp'=> $this->tempFolder . md5(microtime()),
        ));
        $ch = curl_init($url);
        $fp = fopen($this->tempFiles[0]['temp'], 'w');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        return $this->tempFiles[0]['temp'];
    }
    
    function read ($index = 0) {
        return file_get_contents($this->tempFiles[$index]['temp']);
    }
    
    function readArray ($index = 0)
    {
        return file($this->tempFiles[$index]['temp']);
    }
    
    function listFiles () {
        return $this->tempFiles;
    }
    
    function save($path, $fileName=NULL, $index = 0) {
		$fName = ($fileName==NULL)?$this->tempFiles[$index]['original']:$fileName;
        copy($this->tempFiles[$index]['temp'], (is_dir($path) ? $path.$fName : $path));
    }

}

?>

