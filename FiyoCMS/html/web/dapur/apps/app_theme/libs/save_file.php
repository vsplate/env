<?php 
/**
* @version		2.0
* @package		Fiyo CMS
* @copyright	Copyright (C) 2014 Fiyo CMS.
* @license		GNU/GPL, see LICENSE.
**/

define('_FINDEX_','BACK');

if (get_magic_quotes_gpc()) {
    function stripslashes_gpc(&$value)
    {
        $value = stripslashes($value);
    }
    array_walk_recursive($_GET, 'stripslashes_gpc');
    array_walk_recursive($_POST, 'stripslashes_gpc');
    array_walk_recursive($_COOKIE, 'stripslashes_gpc');
    array_walk_recursive($_REQUEST, 'stripslashes_gpc');
}

require_once ('../../../system/jscore.php');
$c = $_POST["content"];
$f = $_POST["src"]; 


$w = file_put_contents($f,$c);

if($w){
    alert('info',File_Saved);
} else {
   alert('error',File_Error);
}

?>