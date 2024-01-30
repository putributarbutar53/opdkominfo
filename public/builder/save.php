<?php

error_reporting(0);
session_start();
if ($_SESSION['editor_status']!="live_editor")
{
    die('You are not authorize to access this page');
}

//error_reporting(0);
/**
 * This is a sample PHP script which demonstrates how you handle SAVE request from BuilderJS
 * The example is in PHP. However, you can use any server side programming you are familiar with (JAVA, .NET, Ruby, Perl, Python...)
 * The point is to capture the HTML content posted from BuilderJS through HTTP "content" parameter to the server.
 *
 * In this example, we write back the updated HTML content to the original template file
 *
 */

// Set up HTTP header for response
// BuilderJS expects JSON response
header('Content-Type: application/json');

// Get the Template ID posted to the server
// Template ID and type are configured in your BuilderJS initialization code
$templateID = $_POST['template_id'];
$type = $_POST['type'];

// Get the directory path of the specified template on the hosting server
// Path may look like this: /storage/templates/{type}/{ID}/
// In our sample templates, the HTML content is stored in the "index.html" file
//$path = dirname(__FILE__) . "/templates/" . $type . "/" . $templateID . "/index.html";

// Get the HTML content submitted from BuilderJS (when user clicks SAVE)
//$html = $_POST['content'];

// // Check if the file exists. Throw an error otherwise!
// if (!file_exists($path)) {
//     header("HTTP/1.1 404");
//     echo json_encode([ 'message' => "File not found: $path" ]);
//     return;
// }

// // Actually write the updated HTML content to the index.html file
// file_put_contents($path, $html);

// // Return HTTP 200, SUCCESS
// header("HTTP/1.1 200");
// echo json_encode([ 'success' => "Written to file {$path}" ]);
// return;


// -------------------------------------------------------------------------------------------------------------------
// THIS IS THE END OF THE REQUEST
// BELOW ARE ADDITIONAL GUIDELINES & EXAMPLES OF HOW YOU HANDLE "SAVE" REQUEST FROM BUILDERJS
// -------------------------------------------------------------------------------------------------------------------

/**
 * Of course you can also save the posted content to a MySQL database rather than an index.html file
 *
 * EXAMPLE 02: Below is an example of how you save it to a MySQL database table
 */

// Sample DB credentials

// $servername = "localhost";
// $username = "mysqlusername";
// $password = "SEcrEt!";
// $dbname = "db";

// Retrieve posted information
$html = $_POST['content'];
//$templateID = 1;

$dbHost="localhost";
$dbUser="root";
$dbPassword="";
$dbName="db_polbangtan";

preg_match("/<body[^>]*>(.*?)<\/body>/is", $html, $matches);

//Set Flag
$dbStatus=mysqli_connect($dbHost,$dbUser,$dbPassword,$dbName) or die("Could not connect: ". mysqli_error($dbStatus));
$Execute = mysqli_query($dbStatus,"UPDATE cppage SET tContent='".mysqli_real_escape_string($dbStatus, $matches[1])."' WHERE id='".$templateID."'"); // or die(mysqli_error($this->dbStatus));
mysqli_close($dbStatus);

header("HTTP/1.1 200");
echo json_encode([ 'success' => 'Halaman sudah perbaharui' ]);
return;

// Create connection
//$conn = new mysqli($servername, $username, $password, $dbname);

// Insert/update BuilderJS' posted content to the `emails` DB table
// Make sure you encode the HTML content correctly before injecting it into your SQL query
// Or you can use the parameter prepare/binding mechanism of PHP to handle it
// $stmt = $conn->prepare("UPDATE `emails` SET html_content = ? WHERE id = ?");
// $stmt->bind_param("ss", $html, $templateID);

// $stmt->execute();

// $stmt->close();
// $conn->close();

?>