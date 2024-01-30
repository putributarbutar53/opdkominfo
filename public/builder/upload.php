<?php

error_reporting(0);
session_start();
if ($_SESSION['editor_status']!="live_editor")
{
    die('You are not authorize to access this page');
}

// upload template
function uploadTemplate()
{
    if (isset($_POST) && empty($_FILES['file'])) {
        return json(['error' =>  [ 'file' => 'No file upload' ] ], 404);
    }
    
    $assetName = uniqid();
    $filename = $_FILES['file']['name'];
    $targetPath = __DIR__ . DIRECTORY_SEPARATOR . "tmp/original";
    $uploadPath = __DIR__ . DIRECTORY_SEPARATOR . "templates/custom/" . $assetName ."/";
    $movethumbnail = __DIR__ . DIRECTORY_SEPARATOR . "templates/custom/";
    $thumbnail = __DIR__ . DIRECTORY_SEPARATOR . "assets/image/thumb.png";

    if (isset($_POST) && !empty($_FILES['file'])) {
        $file = $_FILES['file']['tmp_name'];

        move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);
        
        $zip = new ZipArchive();

        $zip->open($targetPath);
        $zip->extractTo($uploadPath);
        $zip->close();
        
        $files = glob($uploadPath. "/index.html"); /*search index.html in folder*/
        $content = file_get_contents($files[0]);
        
        copy($thumbnail, $uploadPath. 'thumb.png');
    }
    return $assetName;
}

// UPLOAD
$tid = uploadTemplate();

// response json
header("HTTP/1.1 200");
header('Content-Type: application/json');
echo json_encode([ 'url' => "design.php?id={$tid}&type=custom" ]);
