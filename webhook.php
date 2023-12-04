<?php
/**
 * @package tiny_cursive
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

ini_set('log_errors', 1); // enable error logging
ini_set('error_log', __DIR__ . '/lib/editor/tiny/plugins/cursive/my-errors.log'); // specify error log file path
require(__DIR__ . '/../../../../../config.php');
$err_file= __DIR__ . '/my-errors.log';


global $DB, $CFG, $SESSION,$PAGE;

$payload = @file_get_contents('php://input');
$event = null;
$payload =json_decode($payload, true);

try {
    $dataObj = (object) $payload;
    error_log($payload, 3,$err_file );
    $table = 'tiny_cursive_user_writing';
    $DB->insert_record($table, $dataObj);
} catch(Exception $e) {
    error_log($e, 3,$err_file );
   // http_response_code(500);
    exit();
}

$responseData = array(
    'status' => 'success',
    'message'=>"Data saved successfully",
);

// $responseJson = json_encode($responseData);

http_response_code(200);
header('Content-Type: application/json');
echo $responseJson;