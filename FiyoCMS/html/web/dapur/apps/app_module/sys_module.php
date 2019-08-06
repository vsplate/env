<?php
/**
* @version		2.0
* @package		Fiyo CMS
* @copyright	Copyright (C) 2014 Fiyo CMS.
* @license		GNU/GPL, see LICENSE.
**/

defined('_FINDEX_') or die('Access Denied');

// Access only for Administrator
if($_SESSION['USER_LEVEL'] > 2)
	redirect('index.php');
	
$db = new FQuery();
$db->connect(); 

/****************************************/
/*			 Add Module				*/
/****************************************/
if(isset($_POST['save_add']) or isset($_POST['apply_add'])){	
	if(!empty($_POST['title']) AND !empty($_POST['folder']) AND !empty($_POST['position'])) {	
		$param=''; // first value from $param
		if((@$_POST['totalParam']) >= 1){			
			for($p=1;$p<=$_POST['totalParam'];$p++)
			{	
				@$pars = $_POST["param$p"];
				if(@multipleSelect($pars))
					@$pars = multipleSelect($pars);
				else
					@$pars = $pars;
				@$param .=$_POST["nameParam$p"]."=".$pars.';\n';
			}
		}
		
		$page = @$_POST['page'];
		$page = @multipleSelect($page);
		
		@$parameter=str_replace('"',"'","$_POST[editor]");
		@$parameter=$parameter.$param;
		
		if(checkLocalhost()) {
			$parameter = str_replace(FLocal."media/","media/",$parameter);	
			$parameter = str_replace("http://localhost","",$parameter);			
		}
		
		$qr=$db->insert(FDBPrefix.'module',array("","$_POST[title]","$_POST[folder]",stripTags("$_POST[position]"),"$_POST[short]","$_POST[level]","$_POST[status]","$page","$parameter","$_POST[class]","$_POST[style]","$_POST[show_title]"));
		if($qr AND isset($_POST['apply_add'])){
                print_r($_POST);
			$db = new FQuery();
			$db->connect(); 				
			$sql = $db->select(FDBPrefix.'module','id','','id DESC' ); 
			$row = $sql[0];				
			notice('success',New_Module_Saved);	
			redirect('?app=module&act=edit&id='.$row['id']);
		}
		elseif($qr AND isset($_POST['save_add'])) {
			notice('success',New_Module_Saved);
			if($qr)
			redirect('?app=module');
		}
		else {
			notice('error',Status_Invalid,2);
		}					
	}
	else 
	{			
		notice('error',Status_Invalid,2);
	}	
}	

/****************************************/
/*              Edit Module		*/
/****************************************/	
if(isset($_POST['save_edit']) or isset($_POST['apply_edit'])){	
	if(!empty($_POST['title']) AND !empty($_POST['folder']) AND !empty($_POST['position'])) {
		$param = ''; // first value from $param
		if((@$_POST['totalParam']) >= 1){			
			for($p=1;$p<=$_POST['totalParam'];$p++)
			{	
				@$pars = $_POST["param$p"];
				if(@multipleSelect($pars))
					$pars = multipleSelect($pars);
				else
					$pars = $pars;
				@$param .=$_POST["nameParam$p"]."=".$pars.';\n';
			}
		}		
		
		@$page = $_POST['page'];
		@$page = multipleSelect($page);
		
		@$parameter = str_replace('"',"'","$_POST[editor]");
		@$parameter = $parameter.$param;
		
		if(checkLocalhost()) {
			$parameter = str_replace(FLocal."media/","media/",$parameter);	
			$parameter = str_replace("http://localhost","",$parameter);			
		}
		
		$qr= $db-> update(FDBPrefix.'module',array("name"=>"$_POST[title]",
		"position"=>"$_POST[position]",
		"short"=>"$_POST[short]",
		"level"=>"$_POST[level]",
		"status"=>"$_POST[status]",
		"page"=>"$page",
		"class"=>"$_POST[class]",
		"style"=>"$_POST[style]",
		"parameter"=>"$parameter",
		"show_title"=>"$_POST[show_title]"),
		"id=$_REQUEST[id]");
			
		if($qr AND isset($_POST['apply_edit'])){				
			notice('success',Module_Saved);
			redirect(getUrl());
		}
		else if($qr AND isset($_POST['save_edit'])) {
			notice('success',Module_Saved);
			redirect('?app=module');
		}
		else {
			notice('error',Status_Invalid);
		}					
	}
	else {			
		notice('error',Status_Invalid);
	}	
}

/****************************************/
/*		Delete Module		*/
/****************************************/
if(isset($_POST['delete']) or isset($_POST['check'])){
	$source = @$_POST['check'];
	$source = multipleSelect($source);
	$delete = multipleDelete('module',$source);
	if(isset($delete))
            notice('info',Module_Deleted);
	else
            notice('error',Module_Not_Selected);
	refresh();		
}

/****************************************/
/*  Redirect when Module-Id not found   */
/****************************************/
if(!isset($_POST['save_edit']) AND !isset($_POST['apply_edit'])) {
	if(isset($_REQUEST['act']))
		if($_REQUEST['act']=='edit'){
		$id = $_REQUEST['id'];
		$react = oneQuery('module','id',$id,'id');
		if(!isset($react)) header('location:?app=module');
	}
}

function option_sub_menu($parent_id,$sub = null, $pre = null, $page) {
	$db = new FQuery();
	$sql = $db->select(FDBPrefix."menu","*","parent_id=$parent_id");
	foreach($sql as $qr){	
		$sel = multipleSelected($page,$qr['id']);
		if($sel =='selected' or !$page) $sel = "class='active' checked";
		$check = "<input $sel type='checkbox' name='page[]' value='$qr[id]' rel='ck'>";
		echo "<li value='$qr[id]' $sel>$pre&nbsp;&nbsp;|_ $check $qr[name]</li>"; 
		option_sub_menu($qr['id'],$sub+1,"&nbsp;".$pre."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",$page);	
	}			
}	