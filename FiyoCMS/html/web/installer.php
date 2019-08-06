<?php
/**
* @name			Updater
* @version		2.0.5
* @package		Fiyo CMS
* @copyright	Copyright (C) 2015 Fiyo CMS.
* @license		GNU/GPL, see LICENSE.txt
*/

defined("_FINDEX_") or die("Access Denied");


$version	 	= "2.0.6.1";
$addons["name"] = "Patch 2.0.6.1";
$addons["type"] = "updater";
$addons["info"] = "<h1>Update Successfully</h1><p>Version $version successfully installed.</p>";


$db = new FQuery();
$db->update(FDBPrefix."setting",array("value"=>"$version"),"name='version'");
if(!oneQuery("setting","name","https"))
$db->insert(FDBPrefix."setting",array("","https","0"));

