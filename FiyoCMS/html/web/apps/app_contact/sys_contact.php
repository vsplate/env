<?php
/**
* @version		2.0
* @package		Fiyo CMS
* @copyright	Copyright (C) 2014 Fiyo CMS.
* @license		GNU/GPL, see license.txt
* @description	
**/

defined('_FINDEX_') or die('Access Denied');

loadLang(dirname(__FILE__));

function contactInfo($output) {
	$id = app_param('id');
	$output = oneQuery('contact','id',$id ,$output);
	return  $output;
}

function groupInfo($output) {	
	$id = app_param('id');
	$output = oneQuery('contact_group','group_id',$id ,$output);
	return  $output;
}

class Contact {
	var $sent = false;
	function item($id,$menuId) {
		$db = new FQuery();  
		$db->connect();
		$sql = $db->select(FDBPrefix.'contact','*','status = 1 AND id='.$id); 
		$qr	 = $sql[0]; 	
							
		if(empty($qr['id']))
			echo "<h3>Opps, Contact person is not found!";
		else {		
			//get group name
			$group = oneQuery('contact_group','group_id',$qr['group_id'],'group_name');	
			if(!empty($qr['email'])) $email = "<a href='mailto:$qr[email]' title=\"send mail to $qr[name]\">$qr[email]</a>";	
			if(!empty($qr['photo'])) $photo = "<img src='$qr[photo]' title=\"$qr[name]'s contact photo\" />";
			if(!empty($qr['tw'])) $tw = "<a href='http://twitter.com/$qr[tw]' title=\"follow $qr[name] on twitter\" target='_blank'><img src='".FUrl."apps/app_contact/theme/images/tw.png'></a>";	
			if(!empty($qr['fb'])) $fb = "<a href='http://facebook.com/$qr[fb]' title=\"find $qr[name] on facebook\" target='_blank'><img src='".FUrl."apps/app_contact/theme/images/fb.png'></a>";
			if(!empty($qr['fb'])) $fb = "<a href='http://facebook.com/$qr[fb]' title=\"find $qr[name] on facebook\" target='_blank'><img src='".FUrl."apps/app_contact/theme/images/fb.png'></a>";
			if(!empty($qr['web'])) $web = "<a href='http://$qr[web]' title=\"visit $qr[name]'s website\" target='_blank'><img src='".FUrl."apps/app_contact/theme/images/web.png'></a>";
			if(!empty($qr['ym'])) $ym = "<a href='ymsgr:sendIM?$qr[ym]' title=\"chat with $qr[name] via YahooMasangger\"><img src='".FUrl."apps/app_contact/theme/images/ym.png'></a>";
			$desc = str_replace("\n","<br>",$qr['description']);
			$this -> name		= $qr['name'];
			$this -> mail		= $qr['email'];
			$this -> group		= $group;
			$this -> gender		= $qr['gender'];
			$this -> address	= $qr['address'];
			$this -> city		= $qr['city'];
			$this -> state		= $qr['state'];
			$this -> country	= $qr['country'];
			$this -> zip		= $qr['zip'];
			$this -> about 		= $desc;
			$this -> phone		= $qr['phone'];
			$this -> fax		= $qr['fax'];
			$this -> email		= @$email;
			$this -> web		= @$web;
			$this -> ym			= @$ym;
			$this -> twitter	= @$tw;
			$this -> facebook	= @$fb;
			$this -> photo		= @$photo;
		}	
	}
	
	function send($name,$email,$post,$send,$to) {
		if(isset($send)) {
			if(empty($name) or empty($email) or empty($post)) 
				alert("error",contact_Error);
			else if(!preg_match("/^.+@.+\\..+$/",$email))
				alert("error",contact_Error2);
			else if($_POST['captcha'] == $_SESSION['captcha']) {
				// multiple recipients
				$site = siteConfig('site_name');
				$to = "$to";
				$subject = "Email via $site";
				$message = "$post<p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p><small>Sent by <b> $site</b></small></p>";		
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				$headers .= "To: <$to>\r\n";
				$headers .= "From: $name <$email>" . "\r\n";
				$mail = @mail($to,$subject,$message,$headers);	
				alert("info",contact_Info); 
				$this -> sent 	= true;
			}	
			else alert("error",contact_Error3);
		}	
	}

	function category($id,$fp = null) {
		$db = new FQuery();  
		$db->connect(); 
		
		$show_panel	= menu_param('show_name');
		$read_more  = menu_param('read_more');
		$per_page	= menu_param('per_page');
		$this -> sname		= 1;
		$this -> sgroup		= menu_param('show_group');
		$this -> sgender	= menu_param('show_gender');
		$this -> saddress	= menu_param('show_address');
		$this -> semail		= menu_param('show_email');
		$this -> sjob		= menu_param('show_job');
		$this -> slinks		= menu_param('show_links');
		$this -> sphone		= menu_param('show_phone');
		$this -> sphoto		= $sphoto = menu_param('show_photo');

		
		$groupId = app_param('id');
		$whereCat = "AND group_id = $id";
		$sql = $db->select(FDBPrefix.'contact','*','status = 1 AND group_id='.$id); 
		if(!count($sql))
			echo "<h3>Opps, Contact group is empty!";
		else {		
			loadPaging();		
			$paging = new paging();
			$rowsPerPage = $per_page;
			$result = $paging->pagerQuery(FDBPrefix.'contact',"*","status=1 $whereCat",'id ASC',$rowsPerPage);
			
			$no=0;
			$sum= count($result);		
			foreach($result as $qr) {			
			$group = oneQuery('contact_group','group_id',$qr['group_id'],'group_name');						
			$vlink="?app=contact&view=person&id=$qr[id]";	
			$link = make_permalink($vlink,Page_ID);	
			$title = "<a href=\"$link\">$qr[name]</a>";
			if(empty($read_more)) $read_more="read more...";
			$readmore = "<a href=\"$link\"class='readmore'>$read_more</a>";		
			$comment = FQuery('comment',"link='$vlink'AND status=1");
			
			$name = "<a href='$link'>$qr[name]</a>";
			
			if($sphoto==1 AND !empty($qr['photo'])) $photo = "<img src=\"$qr[photo]\" width=\"150px\">";
			
			if(!empty($qr['email'])) $email = "<a href='mailto:$qr[email]' title=\"send mail to $qr[name]\">$qr[email]</a>"; else	$email="";
			if(!empty($qr['photo'])) $photo = "<img src='$qr[photo]' title=\"$qr[name]'s contact photo\" />";
			if(!empty($qr['tw'])) $tw = " <a href='http://twitter.com/$qr[tw]' title=\"follow $qr[name] on twitter\" target='_blank'><img src='".FUrl."apps/app_contact/theme/images/tw.png'></a>";	
			if(!empty($qr['fb'])) $fb = " <a href='http://facebook.com/$qr[fb]' title=\"find $qr[name] on facebook\" target='_blank'><img src='".FUrl."apps/app_contact/theme/images/fb.png'></a>";
			if(!empty($qr['web'])) $web = " <a href='http://$qr[web]' title=\"visit $qr[name]'s website\" target='_blank'><img src='".FUrl."apps/app_contact/theme/images/web.png'></a>";
			if(!empty($qr['ym'])) $ym = " <a href='ymsgr:sendIM?$qr[ym]' title=\"chat with $qr[name] via YahooMasangger\"><img src='".FUrl."apps/app_contact/theme/images/ym.png'></a>";
			if(isset($ym) or isset($fb) or isset($tw) or isset($web))
				$links = $ym.$fb.$tw.$web;
			else  
				$links='';				
			
			if(!empty($qr['country']))
				$address = $qr['city'].", ".$qr['country'];
			else if(!empty($qr['city']))
				$address = $qr['country'];
			else $address = $qr['city'];
			
			$this -> perrows 		= $sum;
			$this -> name[$no]		= $name;
			$this -> photo[$no]		= $photo;
			$this -> group[$no]		= $group;
			$this -> gender[$no]	= $qr['gender'];
			$this -> address[$no]	= $address;
			$this -> email[$no]		= @$qr['email'];
			$this -> job[$no]		= $qr['job'];
			$this -> links[$no]		= $links;
			$this -> phone[$no]		= $qr['phone'];
			$this -> fax[$no]		= $qr['fax'];
			$this -> per_page		= $per_page;
			$ym=$fb=$tw=$web=null;//reset $link variable;	
				if(defined('SEF_URL')) {		
					$link = link_paging('?');	
				}
				else {		
					$link="?app=contact&view=group&id=$groupId";	
					$link = make_permalink($link,Page_ID);
					$link = $link."&";		
				}
				$no++;
			}
			
			
			$c= $db->select(FDBPrefix.'contact','*',"status=1 $whereCat");
			$jml= count($c);
			if($jml>$rowsPerPage) 					
				$pagelink = $paging->createPaging($link);
			else
				$pagelink = null;
			
			$this -> pagelink		= $pagelink;	
			
		}	
		
	}
}

/****************************************/
/*			   SEF Contact				*/
/****************************************/
$view  = app_param('view');
$id  = app_param('id');
if(SEF_URL){
	if($view == 'person') {
		$item = oneQuery('contact','id',$id,'name');
		$vcat = oneQuery('contact','id',$id,'group_id');
		$ncat = oneQuery('contact_group','group_id',$vcat,'group_name');		
		$page = oneQuery('menu','link',"'?app=contact&view=person&id=$id'",'id');
		if(!$page) {
			$page = oneQuery('permalink','link',"'?app=contact&view=person&id=$id'",'pid');
		}	
		if(!$page) {
			$page = oneQuery('permalink','link',"'?app=contact&view=group&id=$vcat'",'pid');
		}	
		add_permalink($item,"contact/".$ncat,$page);		
	}
	else if($view == 'group') {
		$ncat = oneQuery('contact_group','group_id',$id,'group_name');
		add_permalink("contact/".$ncat);
	}
	else if(app_param() == 'contact' AND empty($id) AND empty($view)) {
		add_permalink("contact");
	}
}


/****************************************/
/*			 Contact Title				*/
/****************************************/
if(!checkHomePage())
if ($view=="person") 
	define('PageTitle', contactInfo('name'));
else if($view=="group")
	define('PageTitle', groupInfo('group_name').' Contacts');
else
	define('PageTitle','Contact');

