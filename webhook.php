<?php
/**
 * @package tiny_cursive
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

require(__DIR__ . '/../../../../../config.php');

global $DB, $CFG, $SESSION,$PAGE;

$payload = @file_get_contents('php://input');
$event = null;
$payload =json_decode($payload, true);

try {
    $dataObj = (object) $payload;
    $table = 'tiny_cursive_user_writing';
    $DB->insert_record($table, $dataObj);
} catch(Exception $e) {
    http_response_code(500);
    exit();
}

$responseData = array(
    'status' => 'success',
    'message'=>"Data saved successfully",
);

$responseJson = json_encode($responseData);

http_response_code(200);
header('Content-Type: application/json');
echo $responseJson;