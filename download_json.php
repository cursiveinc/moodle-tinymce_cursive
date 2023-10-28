<?php
/**
 * @package tiny_cursive
 * @category TinyMCE Editor
 * @copyright  CTI <info@cursivetechnology.com>
 * @author kuldeep singh <mca.kuldeep.sekhon@gmail.com>
 */

$resourceId=$_GET['resourceId'];
$user_id=$_GET['user_id'];
$cmid=$_GET['cmid'];
$fname=$_GET['fname'];
$filename = '';
        $dirname = __DIR__ .'/userdata/';    
        if($fname){
        $filename = $dirname.$fname;
        }else{
        $filename = $dirname. $user_id.'_'.$resourceId.'_'.$cmid.'_attempt'.'.json';
        }
        
        header("Content-Description: File Transfer"); 
        header("Content-Type: application/octet-stream"); 
        header("Content-Disposition: attachment; filename=\"". basename($filename) ."\""); 
        flush();
        $inp = file_get_contents($filename);
        echo $inp;
        die();

?>