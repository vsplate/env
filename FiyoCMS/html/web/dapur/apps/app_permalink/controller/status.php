<?php
/**
* @version		2.0
* @package		Fiyo CMS
* @copyright	Copyright (C) 2014 Fiyo CMS.
* @license		GNU/GPL, see LICENSE.
**/

session_start();
if(!isset($_SESSION['USER_LEVEL']) AND $_SESSION['USER_LEVEL'] > 2) die ();
define('_FINDEX_','BACK');

require_once ('../../../system/jscore.php');
$db = new FQuery();  

/****************************************/
/*	    Enable and Disbale SEF			*/
/****************************************/
if(isset($_GET['stat'])) {
	if($_GET['stat']=='1'){
		$db->update(FDBPrefix.'permalink',array("locker"=>"1"),'id='.$_GET['id']);
		alert('success',Status_Applied,1);
	}
	if($_GET['stat']=='0'){
		$db->update(FDBPrefix.'permalink',array("locker"=>"0"),'id='.$_GET['id']);
		alert('success',Status_Applied,1);
	}
}