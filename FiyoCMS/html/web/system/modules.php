<?php
/**
* @version		2.0
* @package		Fiyo CMS
* @copyright	Copyright (C) 2014 Fiyo CMS.
* @license		GNU/GPL, see LICENSE.
**/

defined('_FINDEX_') or die('Access Denied');

function loadModule($position, $echo = false) {	
	if(isset($_GET['theme']) AND $_GET['theme'] =='module' AND $_SESSION['USER_LEVEL'] < 3) {
		return "<div class='theme-module'>$position</div>";
	} 
	else {
		ob_start();
		$db = new FQuery();  
		$db ->connect();	
		$qrs = $db->select(FDBPrefix.'module','*',"status=1 AND position='$position'" .Level_Access, 'short ASC');
		if(is_array($qrs))
		foreach($qrs as $qr){
			if(!empty($qr['page'])) {
				$page = explode(",",$qr['page']);
				foreach($page as $val)
				{
					if(Page_ID == $val)
					{ 	
						$qr['show_title']== 1 ? $title="<h3>$qr[name]</h3>" : $title = "";						
						echo "<div class=\"modules $qr[class]\">$title<div class=\"mod-inner\" style=\"$qr[style]\">";
						$modId = $qr['id'];
						$modParam = $qr['parameter'];
						if(checkLocalhost()) {
							$modParam = str_replace(FLocal."media/","media/",$modParam);
							$modParam = str_replace("/media/",FUrl."media/",$modParam);			
						}
						$modFolder = $qr['folder'];
						$theme = siteConfig('site_theme');
						$tfile = "themes/$theme/modules/$qr[folder]/$qr[folder].php";	
						$file = "modules/$qr[folder]/$qr[folder].php";	
						if(file_exists($tfile))
							include($tfile);
						else if(file_exists($file))
							include($file);
						else
							echo "<i>Module Error</i> : <b>$qr[folder]</b> is not installed!";
						echo"</div></div>";
					}
				}
			}
			
			else if($qr['page']==Page_ID AND FUrl==$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']){
				if($qr['show_title']==1){$title="<h3>$qr[name]</h3>";}
				else {$title="";}
				echo "<div class=\"modules $qr[class]\">$title<div class=\"mod-inner\" style=\"$qr[style]\">";
				$tfile 	= "themes/$theme/modules/$qr[folder]/$qr[folder].php";	
				$file	="modules/$qr[folder]/$qr[folder].php";	
				$modId 	= $qr['id'];
				$modFolder 	= $qr['folder'];
				$modParam 	= $qr['parameter'];
				if(checkLocalhost()) {
					$modParam = str_replace(FLocal."media/","media/",$modParam);
					$modParam = str_replace("/media/",FUrl."media/",$modParam);			
				}
				if(file_exists($tfile))
					include($tfile);
				else if(file_exists($file))
					include($file);
				else
					echo "Module Error : <b>$qr[folder]</b> is not installed!";
				echo"</div></div>";
			}
		}
		$mod = ob_get_contents();
		ob_end_clean();
		if($echo == true)
		return $mod;
		else
		echo $mod;
	}
}

function checkModule($position) {
	if(isset($_GET['theme']) AND $_GET['theme'] =='module' AND $_SESSION['USER_LEVEL'] < 3) {
		return true;
	}
	else {
		$db = new FQuery();  
		$db ->connect();	
		if(!defined('Page_ID') AND $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']==FUrl){
			$sql=$db->select(FDBPrefix.'menu','*','home=1'); 
			$qr = mysql_fetch_array($sql);
			$pid= $qr['id'];
		}
		else{	
			$pid = Page_ID;
			if(empty($pid)) $pid = 0;
		}
		$val = false;
		if(!is_array($position)) $where = " position = '$position'";
		else $where = " position IN('".implode("','",$position)."')";
		$sq = $db->select(FDBPrefix.'module','*',"status=1 AND $where" .Level_Access, 'short ASC');
		foreach($sq as $qr){
			if(!empty($qr['page'])) {
				$pid = explode(",",$qr['page']);
				foreach($pid as $a) {
					if($a == Page_ID )
					$val = true;
				}
			}		
		}
		return $val;
	}
}


function loadModuleCss() {
	if(isset($_GET['theme']) AND $_GET['theme'] =='module' AND $_SESSION['USER_LEVEL'] < 3) {
	echo "<style>.theme-module {
		border: 2px solid #e3e3e3; 
		background: rgba(250,250,250,0.8);
		color : #666; 
		padding: 10px;
		margin: 5px 3px;
		font-weight: bold;
		cursor: pointer;
		transition: all .2s ease;
		}
		.theme-module:hover {
		border-color: #ff9000; 
		background: rgba(255, 205, 130,0.15);
		color : #ff6100;
		box-shadow: 0 0 10px #ffcd82;} </style>";
	}
	else {
		$db = new FQuery();  
		$db ->connect();	
		if(!defined('Page_ID') AND $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']==FUrl){
			$qr = $db->select(FDBPrefix.'menu','*','home=1'); 
			$pid= $qr['id'];
		}
		else{	
			$pid = Page_ID;
			if(empty($pid)) $pid = 0;
		}
		$val = false;
		$no = 1;
		$qr = $db->select(FDBPrefix.'module','*',"status=1 " .Level_Access, 'short ASC');
		foreach($qr as $qr){
			if(!empty($qr['page'])) {
				$pid = explode(",",$qr['page']);
				foreach($pid as $a) { 
					if($a == Page_ID ) {
						$file	= "modules/$qr[folder]/mod_style.php";
						if(file_exists($file)) {
							require_once ($file);
							$no++;
						}	
					}
				
				}
			}		
		}	
	}
}