<?php
/**
* @name			Article System
* @version		2.0
* @package		Fiyo CMS
* @copyright	Copyright (C) 2014 Fiyo CMS.
* @license		GNU/GPL, see LICENSE.
**/

defined('_FINDEX_') or die('Access Denied');


function loadComment() {
	include_once ('apps/app_comment/index.php');
}

function articleInfo($output,$id = null) {
	if(empty($id)) $id = app_param('id');
	$output = oneQuery('article','id',$id ,$output);
	return  $output;
}

function articleParameter($value) {	
	$menu_id = Page_ID;
	$param	 = pageInfo($menu_id ,'parameter');
	$param	 = mod_param($value,$param);
	return 1;
}

function articleHits($vid) {
	$db = new FQuery();  
	$id = app_param('id');
	$db->update(FDBPrefix.'article',array('hits'=> '+hits' ),"id=$id");	
}

function categoryInfo($output, $id = null) {
	if(empty($id)) 
		if(app_param('view') == 'item')
			$id = articleInfo('category');
		else
			$id = app_param('id');		
	$output = oneQuery('article_category','id',$id ,$output);
	return  $output;
}

function categoryLink($value) {
	$link = make_permalink("?app=article&view=category&id=$value");
	return $link ;
}


function itemLink($value) {
	$link = make_permalink("?app=article&view=item&id=$value");
	return $link ;
}

function tagToLink($tags, $hits = null) {
	$db = new FQuery();  
	$tgs = explode(",",$tags);
	$tags = null;
	foreach($tgs as $tag) {			
		$ltag = str_replace(" ","-",$tag);	
		$ltag = "?app=article&tag=$ltag";	
		$ltag = make_permalink($ltag);
		$tags .= "<li><a href='$ltag' alt='See article for tag $tag'>$tag</a></li>";
		if($hits){
			if(!oneQuery('article_tags','name',"$tag"))
			$qr=$db->insert(FDBPrefix.'article_tags',array("","$tag","","1"));
			else
			$db->update(FDBPrefix.'article_tags',array('hits'=> '+hits'),"name='$tag'");			
		}		
	}
	return $tags;
}

function articleIntro($article) {
	$article = str_replace('"',"'",$article);
	$article = str_replace('&nbsp;'," ",$article);
	$limit = strpos("$article","<hr id=");	
	if(empty($limit)) 
	$limit = strpos("$article","<div style='page-break-after: always");	
	if(!empty($limit))
		return substr("$article",0,$limit);
	else 
		return substr("$article",0);
}

function articleImage($article) {
	$opentag = strpos($article,"<img");
	if($opentag) {
		$closetag = substr($article,$opentag);
		$closetag = strpos($closetag,">");
		$image = substr($article,$opentag,$opentag+$closetag);
		$a = strpos($image,'src="');
		
		if(empty($a)) 
			$a = strpos($image,"src='");
			
		$b = substr($image,$a+5);					
		$c = strpos($b,'"');
		if(empty($c))$c = strpos($b,"'");
		return  substr($image,$a+5,$c);					
	}	
	else return false;
}
	
function clearXMLString($xml)  {
	$xml = str_replace('&nbsp',' ',$xml);
	$xml = str_replace('&','&amp;',$xml);
	$xml = str_replace('"',"'",$xml);
	return $xml;
}

function dateRelative($h,$i,$s,$m,$d,$y) {
	$time = mktime($h,$i,$s,$m,$d,$y);
	$w = date("l",$time);
	$d2 = date("D",$time);
	$p = date("A",$time);
	$timer = abs($time - (time())); 

	if($timer < 60) {
		$timer = $timer . second_ago;
	}
	else if($timer < 3600) {
		$timer = ceil($timer/60) . " " .minutes_ago;
	}
	else if($timer < 86400) {	
		$timer = ceil($timer/3600) . " " . hours_ago;
	}
	else if($timer < 259500) {	
		$timer = ceil($timer/86400) . " " . days_ago;
	}		
	else {
		$timer = false;
	}	
	if($timer) {
		if(siteConfig('lang') == 'id') {		
			if($d2 == 'Sun') $w = 'Minggu';
			if($d2 == 'Mon') $w = 'Senin';
			if($d2 == 'Tue') $w = 'Selasa';
			if($d2 == 'Wed') $w = 'Rabu';
			if($d2 == 'Thu') $w = 'Kamis';
			if($d2 == 'Fri') $w = 'Jumat';
			if($d2 == 'Sat') $w = 'Sabtu';		
			$time = "$w, $d-$m-$y $h:$i $p";		
		}
		else
		$time = "$w, $y-$m-$d $h:$i $p";
		return "<span title='$time' style='cursor:help'>$timer</span>";
	}
	else return false;
}

class Article {	
	function item($id) {
		if(articleInfo('id',$id)) {		
			$db = new FQuery();  
			$sql = $db->select(FDBPrefix."article","*,
			DATE_FORMAT(date,'%W, %d %M %Y %H:%i') as time,
			DATE_FORMAT(date,'%Y-%m-%d %H:%i:%s') as timer,
			DATE_FORMAT(date,'%d %M %Y') as date,
			DATE_FORMAT(date,'%W') as D,
			DATE_FORMAT(date,'%d') as f,
			DATE_FORMAT(date,'%b') as b,
			DATE_FORMAT(date,'%a') as a,
			DATE_FORMAT(date,'%D') as d,
			DATE_FORMAT(date,'%m') as n,
			DATE_FORMAT(date,'%M') as m,
			DATE_FORMAT(date,'%y') as y,
			DATE_FORMAT(date,'%Y') as Y,
			DATE_FORMAT(date,'%h') as h,
			DATE_FORMAT(date,'%H') as H,
			DATE_FORMAT(date,'%p') as p,
			DATE_FORMAT(date,'%i') as i,
			DATE_FORMAT(date,'%s') as s","id=$id AND status=1 LIMIT 1");
			$row = $sql[0];	
			if($row) {		
				$category 	= categoryInfo('name',$row['category']);
				$catLevel 	= categoryInfo('level',$row['category']);
				$catLink	= categoryLink($row['category']);
				if(!empty($row['author_id'])) {
					$author		= userInfo('name',$row['author_id']);
					if(empty($author)) $author = "Administrator";		
					$autMail	= userInfo('email',$row['author_id']);	
					$autBio		= userInfo('about',$row['author_id']);	
				}
				else {		
					$author		= 'Administrator';				
					$autMail	= "-";	
					$autBio		= "Bio";	
				}	
					
							
				if($autBio === true) $autBio = "Sorry, no description about me.";
				$autBio	 	= str_replace("\n","<br>",$autBio);	
				
				
				if(!empty($row['author'])) $author = $row['author'];
					
				articleHits($row['hits']);			
				$tag 		= mod_param('tags',$row['parameter']);
				$sdate 		= mod_param('show_date',$row['parameter']);
				$shits 		= mod_param('show_hits',$row['parameter']);
				$srate 		= mod_param('show_rate',$row['parameter']);
				$tpanel		= mod_param('panel_top',$row['parameter']);
				$bpanel		= mod_param('panel_bottom',$row['parameter']);
				$stag		= mod_param('show_tags',$row['parameter']);
				$voter 		= mod_param('rate_counter',$row['parameter']);
				$rate 		= mod_param('rate_value',$row['parameter']);
				$stitle 	= mod_param('show_title',$row['parameter']);
				$sauthor 	= mod_param('show_author',$row['parameter']);
				$comment	= mod_param('show_comment',$row['parameter']);
				$scategory 	= mod_param('show_category',$row['parameter']);
				$catLinks	= categoryLink($row['category']);	
				$catHref	= "<a href='$catLinks'>$category</a>";
				
				$fpanel = "*" . menu_param('panel_format',Page_ID);
				$panel = str_replace('%rel',"",$fpanel);
				if(empty($panel) or !strpos($panel,'%')) {
					$a = "<b>%A</b>  &#183;";
					if(!$sauthor) $a = '';
					if(siteConfig('lang') == 'id')
					$date = "%f %m %Y &#183;";
					else
					$date = "%m, %f %Y &#183;";
					if(!$sdate) $date = '';
					if(siteConfig('lang') == 'id')
					$panel = "$a $date %c";
					else
					$panel = "$date $a %c";
					
				}
				$panel = str_replace('%A',"$author",$panel);
				
				if($scategory) 
					$panel = str_replace('%c',"$catHref",$panel);
				else
					$panel = str_replace('&#183; %c','',$panel);
				if(!$sdate AND !$scategory) {
					$panel = str_replace('&#183;','',$panel);
					$panel = str_replace('%c','',$panel);
				}
					
				$panel = str_replace('%h',$row['hits'],$panel);
				
				$timeRel = dateRelative($row['H'],$row['i'],$row['s'],$row['n'],$row['f'],$row['Y']);
				if($timeRel AND strpos($fpanel,'%rel')) {
					$panel = str_replace(', ',"",$panel);
					$panel = str_replace('%d',"",$panel);
					$panel = str_replace('%b',"",$panel);
					$panel = str_replace('%f',"$timeRel",$panel);
					$panel = str_replace('%m',"",$panel);
					$panel = str_replace('%n',"",$panel);
					$panel = str_replace('%y',"",$panel);
					$panel = str_replace('%Y',"",$panel);
					$panel = str_replace('%H',"",$panel);
					$panel = str_replace('%h',"",$panel);
					$panel = str_replace('%i',"",$panel);
					$panel = str_replace('%s',"",$panel);
					$panel = str_replace('%p',"",$panel);
					if(strlen($panel) < 3 )
					$panel   =  $timeRel;
				}
				else {
					if(siteConfig('lang') == 'id')
						$panel = str_replace('%f',$row['f'],$panel);
					else
						$panel = str_replace('%f',$row['d'],$panel);
				
					$panel = str_replace("%rel",$panel,$panel);
					$panel = str_replace('%d',$row['d'],$panel);
					$panel = str_replace('%D',$row['D'],$panel);
					$panel = str_replace('%b',$row['b'],$panel);
					$panel = str_replace('%a',$row['a'],$panel);
					$panel = str_replace('%m',$row['m'],$panel);
					$panel = str_replace('%n',$row['n'],$panel);
					$panel = str_replace('%y',$row['y'],$panel);
					$panel = str_replace('%Y',$row['Y'],$panel);
					$panel = str_replace('%H',$row['H'],$panel);
					$panel = str_replace('%h',$row['h'],$panel);
					$panel = str_replace('%i',$row['i'],$panel);
					$panel = str_replace('%s',$row['s'],$panel);
					$panel = str_replace('%p',$row['p'],$panel);
				}
				$panel = str_replace('*',"",$panel);
				$panel = str_replace('*',"",$panel);
								
				/* voter */
				if(!is_numeric($voter) or !is_numeric($rate)) $voter = 0;
				$rate = (@round($rate / $voter,1)) * 20; 
				/* tags */
				$tags = null;
				if(!empty($row['tags'])) {
					$tags = tagToLink($row['tags'], true);		
				}
				
				$article = $row['article'];
				if(checkLocalhost()) {
					$article = str_replace(FLocal."media/","media/",$article);
					$article = str_replace("/media/",FUrl."media/",$article);			
				}
				
				/* perijinan akses artikel */				
				if(USER_LEVEL > $catLevel AND USER_LEVEL > $row['level']) {
					echo Article_cant_access;
					}
				else {
					$this -> article	= $article;
					$this -> category	= $category;
					$this -> catlink	= $catLink;
					$this -> author		= $author;
					$this -> autmail	= $autMail;
					$this -> autbio		= $autBio;
					$this -> title		= $row['title'];
					$this -> day 		= $row['f'];
					$this -> month 		= $row['m'];
					$this -> year 		= $row['y'];
					$this -> hits 		= digit($row['hits']);	
					$this -> comment	= $comment;	
					$this -> panel		= $panel;	
					$this -> tags		= $tags ;	
					$this -> stag		= $stag ;	
					$this -> sdate		= $sdate;
					$this -> sauthor	= $sauthor;	
					$this -> stitle		= $stitle;	
					$this -> tpanel		= $tpanel;	
					$this -> bpanel		= $bpanel;	
					$this -> scategory	= $scategory;	
					$this -> shits		= $shits;	
					$this -> srate		= $srate;	
					$this -> rate		= $rate;	
					$this -> voter		= $voter;	
				}		
			}
		}
	}

	function category($type, $id = null,$format = null) {
		$link = null;
		/* Set global parameter */
		$show_panel	= menu_param('show_panel',Page_ID);
		$show_rss	= menu_param('show_rss',Page_ID);
		$read_more  = menu_param('read_more',Page_ID);
		$per_page	= menu_param('per_page',Page_ID);
		$intro		= menu_param('intro',Page_ID);
		
		if(empty($intro)) $intro = $per_page;
		
		/* Set Access_Level */
		$accessLevel = Level_Access;
		
		if($type == 'archives')  {	
			$where = "status=1";
		} 
		else if($type == 'category')  {
			$catName = categoryInfo('name',$id);
			$catDesc = categoryInfo('description',$id);
			$catLink  = categoryLink($id);	
			$where = "status=1 AND category = $id";
		}
		else if($type == 'featured') {
			$where = "status=1 AND featured = 1";
		
		}
		else if($type == 'tag') {
			if(empty($per_page))
				$per_page = 10;
			$tag = app_param('tag');
			$tag = str_replace("-"," ",$tag);
			$where = "status=1 AND tags LIKE '%".$tag."%'";
			
			$db = new FQuery();
			if(!oneQuery('article_tags','name',"$tag"))
			$qr=$db->insert(FDBPrefix.'article_tags',array("","$tag","","1"));
			else
			$db->update(FDBPrefix.'article_tags',array('hits'=> '+hits'),"name='$tag'");
		} 
		if(_FEED_ == 'rss') {
			$per_page = 20;
			$pages = url_param('page');
			if($pages != null) {
				$link = str_replace("?page=$pages","",getUrl());
				redirect("$link?feed=rss");					
			}
		}		
			
		loadPaging();		
		$paging = new Paging();
		
		$result = $paging->pagerQuery(FDBPrefix.'article',"*,
		DATE_FORMAT(date,'%d %M %Y') as date,
		DATE_FORMAT(date,'%Y-%m-%d %H:%i:%s') as order_date,
		DATE_FORMAT(date,'%a, %m %d %Y %H:%i:%s') as time,
		DATE_FORMAT(date,'%d') as f,
		DATE_FORMAT(date,'%D') as d,
		DATE_FORMAT(date,'%b') as b,
		DATE_FORMAT(date,'%a') as a,
		DATE_FORMAT(date,'%W') as D,
		DATE_FORMAT(date,'%m') as n,
		DATE_FORMAT(date,'%M') as m,
		DATE_FORMAT(date,'%y') as y,
		DATE_FORMAT(date,'%Y') as Y,
		DATE_FORMAT(date,'%h') as h,
		DATE_FORMAT(date,'%H') as H,
		DATE_FORMAT(date,'%p') as p,
		DATE_FORMAT(date,'%i') as i,
		DATE_FORMAT(date,'%s') as s","$where $accessLevel",'order_date DESC',$per_page);
		
		$no = 0;
		$perrows =  count($result);		
		foreach($result as $row) {
		
			/* Category Details */		
			$catLinks	= categoryLink($row['category']);					
			$category	= categoryInfo('name',$row['category']);
			$catHref	= "<a href='$catLinks'>$category</a>";
			
			/* Author */			
			if(empty($row['author'])) {
				if(!empty($row['author_id']))
				$author = userInfo('name',$row['author_id']);
				if(empty($author))
				$author = "Administrator";
			}
			else  {
				$author = $row['author'];
			}
			
			/* Article Links */
			$link	= "?app=article&amp;view=item&amp;id=$row[id]";	
			$vlink  = str_replace("&amp;","&",$link);
			$vlink  = make_permalink($vlink);
				
			/* Article Title */				
			$title 	= "<a href='$vlink'>$row[title]</a>";
			
			$link  	= $vlink;
				
			/* Article Tags */
			$tags 	= tagToLink($row['tags']);
				
			/* Article Content */
			$article = $row['article'];	
			
			if(checkLocalhost()) {
				$article = str_replace(FLocal."media/","media/",$article);
				$article = str_replace("/media/",FUrl."media/",$article);		
			}	
			
			$comment = null;
			/* Article Comments */
			
	
			$comm = FQuery('comment',"link='$link'AND status=1");
			if(FQuery('apps',"folder='app_comment'")) { 
				$comment =  "<a class='send-comment' href='$link#comment'>";
				if($comm > 1) $comment .= "<span>$comm</span> ".Comments;
				if($comm ==1) $comment .= "<span>$comm</span> ".Comment; 
				if($comm < 1) $comment .= Send_Comment;
				$comment .= "</a>";
			}
			$scomment	= mod_param('show_comment',articleInfo('parameter',$row['id']));
			if(!$scomment) $comment = '';
			
			/* Read More */
			if(empty($read_more)) $read_more= Readmore;
			$readmore = "<a href='$link' class='readmore'>$read_more</a> $comment";	
			
			/* Intro limit (read more) */			
			$content = $article;	
			
			/* Blog Style */
			if($format == 'blog' or $type == 'tag' or $format == 'list' or $format == 'grid') {
				$image	= articleImage($content);	
				$image	= str_replace("/media","/media/.thumbs",$image);
				$imgH  = menu_param('imgH',Page_ID);
				$imgW  = menu_param('imgW',Page_ID);	
				
				$this -> image[$no]		= $image;				
				$this -> imgH			= $imgH;				
				$this -> imgW			= $imgW;	
				$content = preg_replace("/<img[^>]+\>/i", "", $content); 
			}
			
			$content = articleIntro($content);	
					
				$panel	= menu_param('panel_format',Page_ID);
				$fpanel = "#" . menu_param('panel_format',Page_ID);
				$dpanel = str_replace('%rel',"",$fpanel);
				$ctname = strtolower($category);
				if(empty($panel) or !strpos($dpanel,'%')) {
					if($format == 'grid')
					$panel = "<span class='author-link'><b>%A</b>,</span> <date><span>%f</span> <span>%m</span> <span>%Y</span>  &#183;</date> <span class='category-link category-$ctname'>%c</span>";
					else if(siteConfig('lang') == 'id')
					$panel = "<span class='author-link'><b>%A</b> &#183;</span> <date><span>%f</span> <span>%m</span> <span>%Y</span></date> &#183; <span class='category-link category-$ctname'>%c</span>";
					else
					$panel = "<date>%m, %f %Y &#183;</date> <span class='author-link'><b>%A</b> &#183;</span> <span class='category-link category-$ctname'>%c</span>";					
				}
				$panel = str_replace('%A',$author,$panel);
				
				$panel = str_replace('%c',"$catHref",$panel);
					
				$panel = str_replace('%h',$row['hits'],$panel);				
				
				$timeRel = dateRelative($row['H'],$row['i'],$row['s'],$row['n'],$row['f'],$row['Y']);

				if($timeRel AND strpos($fpanel,'%rel')) {
					$panel = str_replace(', ',"",$panel);
					$panel = str_replace('%d',"",$panel);
					$panel = str_replace('%f',"$timeRel",$panel);
					$panel = str_replace('%m',"",$panel);
					$panel = str_replace('%n',"",$panel);
					$panel = str_replace('%y',"",$panel);
					$panel = str_replace('%Y',"",$panel);
					$panel = str_replace('%H',"",$panel);
					$panel = str_replace('%h',"",$panel);
					$panel = str_replace('%i',"",$panel);
					$panel = str_replace('%s',"",$panel);
					$panel = str_replace('%p',"",$panel);
					if(strlen($panel) < 3 )
					$panel   =  $timeRel;
				}
				else {
					if(siteConfig('lang') == 'id')
						$panel = str_replace('%f',$row['f'],$panel);
					else
						$panel = str_replace('%f',$row['d'],$panel);
						
					$panel = str_replace("%rel",$panel,$panel);
					$panel = str_replace('%d',$row['d'],$panel);
					$panel = str_replace('%a',$row['a'],$panel);
					$panel = str_replace('%b',$row['b'],$panel);
					$panel = str_replace('%m',$row['m'],$panel);
					$panel = str_replace('%n',$row['n'],$panel);
					$panel = str_replace('%y',$row['y'],$panel);
					$panel = str_replace('%Y',$row['Y'],$panel);
					$panel = str_replace('%H',$row['H'],$panel);
					$panel = str_replace('%h',$row['h'],$panel);
					$panel = str_replace('%i',$row['i'],$panel);
					$panel = str_replace('%s',$row['s'],$panel);
					$panel = str_replace('%p',$row['p'],$panel);
				}
			$panel = str_replace('*',"",$panel);	
			
			if($format == 'grid');			
			/* RSS Feed */
			$this -> perrows 		= $perrows;
			$this -> intro	 		= $intro;
			$this -> show_rss		= $show_rss;
			$this -> show_panel		= $show_panel;
			$this -> panel[$no]		= $panel;
			$this -> category[$no]	= $category;
			$this -> catlink[$no]	= $catLinks;
			$this -> readmore[$no]	= $readmore;
			$this -> comment[$no]	= $comment;
			$this -> author[$no]	= $author;
			$this -> title[$no] 	= $title;
			$this -> link[$no] 		= $link;
			$this -> tags[$no] 		= $tags;
			$this -> ftime[$no]		= $row['time'];
			$this -> hits[$no]		= $row['hits'];
			$this -> desc[$no]		= clearXMLString("$content");
			$this -> ftitle[$no] 	= clearXMLString($row['title']);
			$this -> content[$no] 	= $content;	
			
			if(defined('SEF_URL')) {		
				$link = link_paging('?');
				if (strpos(getUrl(),'&') > 0)  {			
					$link = link_paging('&');
				}				
			}
			else if(checkhomepage())  {
				$link = "?";
			}
			else if(!url_param('id'))  {			
				$tag  = app_param('tag');
				$link = "?app=article&tag=$tag";	
				$link = make_permalink($link,Page_ID);
				$link = $link."&amp;";
			}
			else {		
				$link="?app=article&view=category&id=$categoryId";	
				$link = make_permalink($link,Page_ID);
				$link = $link."&amp;";		
			}				
			$no++;
		}
		
		// pageLink	
		$this -> pglink	 = $paging->createPaging($link);
		
		// rssLink
		if($type == 'tag')		{	
			$tag = str_replace(" ","-",$tag);	
			$rssLink = "?app=article&tag=$tag&feed=rss";	
		}
		else if($type == 'category')	{
			$rssLink = "?app=article&view=category&id=$id&feed=rss";	
		}
		else {
			$rssLink = "?app=article&view=archives&feed=rss";	
		}
		
		if(_FEED_ == 'rss') {
			$rssLink = make_permalink($rssLink);
			$this -> rssTitle = @clearXMLString(SiteTitle);			
			$categoryLink = @clearXMLString($rssLink);
			$categoryLink = str_replace(".xml","",$categoryLink);
			$this -> rssLink  	= $categoryLink;
			$this -> rssDesc  	= @$categoryDesc;	
		}
		else {
			
			$this -> rssLink  	= make_permalink($rssLink);
		}
	}	
}


/****************************************/
/*			   SEF Article				*/
/****************************************/
$view = app_param('view');
$id = app_param('id');

if($id > 0) {
	$a = FQuery("article_category","id=$id",'',1); 
	if(!$a)
	$a = FQuery("article","id=$id",'',1); 
}
else if ((app_param('tag') != null)) {
	$a = app_param('tag');
}
else{
	$a = app_param('view');
}


if($a){ 
	if(SEF_URL AND !checkHomePage()){
		if($view == 'item') {
			$icat = articleInfo('category');
			$ncat = categoryInfo('name');
			$page = menuInfo('id',"?app=article&view=category&id=$icat");
			$lcat = "$ncat";
			
			$i = 1;
			while(empty($page) AND !check_permalink('link',getLink()) AND $i < 10 AND !empty($lcat) AND $icat != 0) {	
				$icat = categoryInfo('parent_id',$icat);
				$ncat = categoryInfo('name',$icat);
				$page = menuInfo('id',"?app=article&view=category&id=$icat");
				if($icat == 0) break;
				$lcat = "$ncat/$lcat";
				$i++;
			}
			$lcat = strtolower($lcat);
			$item = articleInfo('title');
			add_permalink($item,$lcat,$page);
		}
		else if($view == 'category' or $view == 'catlist') {	
			$icat = categoryInfo('id');
			$ncat = categoryInfo('name');
			$page = menuInfo('id',"?app=article&view=category&id=$icat");
			$lcat = "$ncat";
			
			$i = 1;
			while(empty($page) AND !check_permalink('link',getLink()) AND $i < 10 AND $icat != 0) {		
				$icat = categoryInfo('parent_id',$icat);
				$ncat = categoryInfo('name',$icat);
				$page = menuInfo('id',"?app=article&view=category&id=$icat");
				if($icat == 0) break;
				$lcat = "$ncat/$lcat";
				$i++;
			}
			$lcat = strtolower($lcat);
			$item = articleInfo('title');
			if(_FEED_ == 'rss')
				add_permalink("$lcat","","","rss");
			else
				add_permalink($lcat,'',$page);
		}
		else if($view == "archives") {
			if(_FEED_ == 'rss')
				add_permalink("archives","","","rss");
			else
				add_permalink("archives");	
		}	
		else if($view == "featured") {
			if(_FEED_ == 'rss')
				add_permalink("featured","","","rss");
			else
				add_permalink("featured");	
		}			
		else if (app_param('tag') != null) {	
			$tag = app_param('tag');
			if(_FEED_ == 'rss')
				add_permalink("tag/$tag","","","rss");		
			else add_permalink("tag/$tag");
		}	
	}
}

/****************************************/
/*			 Article Title				*/
/****************************************/
if($id > 0) {
	$follow = true;
	$a = FQuery("article_category","id=$id",'',1); 
	if(!$a) {
		$a = FQuery("article","id=$id",'',1); 
		if(!$a) {
			$follow = false;
			define('MetaRobots',"noindex");
		}
	}
	else {
		if(app_param('view')=='featured')
			$a = 1;
		else 
			$follow = false;
	}
}

if($a){
	if(!checkHomePage()) {	
		if ($view=="item") {
			define('PageTitle', articleInfo('title'));			
			$desc = articleInfo('description');
			if(!empty($desc)) 	
				define('MetaDesc', articleInfo('description'));
			else
				define('MetaDesc', generateDesc(articleInfo('article')));
			
			$keys = articleInfo('keyword');		
			if(!empty($keys)) 	
				define('MetaKeys', articleInfo('keyword'));
			else
				define('MetaKeys', generateKeywords(articleInfo('article')));
			if(!$follow)
				$follow = 'noindex';
			else if(siteConfig('follow_link'))
				$follow = 'index, follow';
			else
				$follow = 'index, nofollow';
			
			define('MetaRobots',"$follow");
			
			$author = articleInfo('author');
			if(empty($author)) 
				$author = oneQuery('user','id',articleInfo('author_id'),'name');
			if(empty($author)) 
				$author = oneQuery('user','id',articleInfo('editor'),'name');
			if(!empty($author))
				define('MetaAuthor',$author);
			
			
		}
		else if($view=="category" or $view=="catlist") {
			if(pageInfo(Page_ID,'name'))
				define('PageTitle', pageInfo(Page_ID,'name'));
			else
				define('PageTitle', categoryInfo('name'));
			$desc = categoryInfo('description');
			if(!empty($desc )) 
				define('MetaDesc', $desc);
			else
				
			$keys = categoryInfo('keywords');
			if(!empty($keys)) 
				define('MetaKeys', $keys );
			
			
			$cat = app_param('id');
			$rowy = oneQuery("menu","link","'?app=article&view=category&id=$cat'");
			if(!$rowy)
				$rowy = oneQuery("menu","link","'?app=article&view=catlist&id=$cat'");
			if($rowy) {
				if(siteConfig('follow_link'))
					$follow = 'index, follow';
				else
					$follow = 'index, nofollow';
			}
			else
				$follow = 'noindex';
			define('MetaRobots',"$follow");
			
		}		
		else if($view=='archives')
			define('PageTitle', "Archives");
		else if($view=='featured')
			define('PageTitle', "Featured");
		else if (app_param('tag') != null)		{	
			define('PageTitle', $p = str_replace("-"," ",ucfirst(app_param('tag'))));
			define('Apps_Title', $p);
			
			}
	}
}
