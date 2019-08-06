<?php
/**
* @version		2.0
* @package		Fiyo CMS
* @copyright	Copyright (C) 2014 Fiyo CMS.
* @license		GNU/GPL, see LICENSE.txt
**/

defined('_FINDEX_') or die('Access Denied');

$db = new FQuery();  
$db->connect();

if(isset($_SERVER['HTTPS'])) $http = "https"; else $http = "http";
if(isset($_GET['app'])) $link = $_GET['app']; else $link = 'dashboard';
?>
<!-- #menu -->
<ul id="menu" class="collapse top affix content">
<?php	
$sql = $db->select(FDBPrefix."menu","*","category='adminpanel' AND status=1  AND parent_id=0 ".Level_Access,"short ASC");
$no = 1;
$sum = count($sql);	

foreach($sql as $menu) {				
	$subtitle = $pid = "";			
	$app2 = str_replace("$http://".siteConfig('backend_folder')."/index.php","",$menu['link']);
	$app = $menu['sub_name'];
	$ac = false;
	if(isset($_REQUEST['app']))
	$pid = FQuery('menu',"link LIKE '%$_REQUEST[app]%' AND status=1 AND category ='adminpanel'","parent_id");
	if((isset($_REQUEST['app']) AND $_REQUEST['app'] == $app) OR ($menu['id'] == $pid)) { 
		$a ="panel active $app"; 
		$ac = true;
	}
	else if(!isset($_REQUEST['app']) AND $menu['link'] == 'index.php')
		$a ="panel active root $app"; 
	else
		$a ="panel";
		if(empty($menu['class'])) $menu['class'] = 'icon-asterisk';
		if ($menu['app'] =="sperator"){
			echo "<li class=\"$a\"><a data-parent=\"#menu\" data-toggle=\"collapse\" class=\"accordion-toggle collapsed\" data-target=\"#nav-$menu[id]\">
			<i class=\"$menu[class]\" style=\"$menu[style]\"></i> $menu[name]
			<span class=\"pull-right\">
			<i class=\"icon-angle-left\"></i>
			<i class=\"icon-angle-down\"></i>
			</span>
		</a>";
			subsmenu($menu['id'],$app);			
			/*if($app == 'apps') {
				$sql2=$db->select(FDBPrefix.'apps','*',"type = 1 or type = 2","name ASC");
				$sum=mysql_num_rows($sql2);
				echo "<ul class=\"sub-menu collapse\" id=\"nav-$menu[id]\">";
				foreach($row=mysql_fetch_array($sql2)) {				
					$fd=str_replace("app_","","$row[folder]");
					echo "<li><a class='link' href='?app=$fd'><i class='icon-list-alt'></i> $row[name]</a></li>";
				}
				echo '</ul>';
			}
			echo "</li>";*/
		}
		else if ($menu['app']=="link"){
			if($menu['link'] != 'index.php') $r = 'root'; else $r = '';
			echo "<li class=\"$a $r\"><a href=\"$menu[link]\"><i class=\"$menu[class]\" style=\" $menu[style]\"></i> $menu[name]$subtitle</a>";
			subsmenu($menu['id']);
			echo "</li>";
		}
		else { 
			if(empty($menu['link'])) $menu['link']="#";
			echo "<li class=\"$a\"><a href=\"$menu[link]\"><i class=\"$menu[class]\" style=\"$menu[style]\"></i> $menu[name]$subtitle</a>";
			subsmenu($menu['id']);
			echo "</li>";
		}
		//if($ac) echo "$menu[name]$menu[sub_name]";
	
}		
echo "</ul>";  

function subsmenu($parent_id,$sub = null){
	$db = new FQuery();  
	$db -> connect(); 
	if($sub == 'apps')$short = 'name ASC';  else  $short = 'short ASC';
	$level = Level_Access;
	$menus = $db->select(FDBPrefix."menu","*","parent_id=$parent_id AND status=1 $level","$short"); 
	$sum = count($menus);
	$no = 1;
	if($sum>0) {
		echo "<ul class=\"sub-menu collapse\" id='nav-$parent_id'>";		
		foreach($menus as $menu){	
			$link = @$menu['link'];			
			$link = @$menu['link'];			
			$subtitle 	= '';	
			$app = $menu['sub_name'];
			
			$a = "";
			$l = substr(getUrl(),strpos(getUrl(),"?app="));
			if($l == $link) {
				$a = " active"; }
			if(empty($menu['class'])) $menu['class'] = 'icon-double-angle-right';
			if ($menu['home']==0){
				if ($menu['app']=="sperator"){
					echo "<li class=\"$a\"><a href='#'><i class=\"$menu[class]\" style=\"$menu[style]\"></i> $menu[name]</a>";
					 subsmenu($menu['id']);
					echo "</li>";
				}
				else if ($menu['app']=="link"){
					echo "<li class=\"$a\"><a href=\"$link\"><i class=\"$menu[class]\" style=\"$menu[style]\"></i> $menu[name]</a>";
					 subsmenu($menu['id']);
					echo "</li>";
				}
				else { 
					if(empty($menu['link'])) $menu['link']="#";
					echo "<li class=\"$a\"><a href=\"$link\"><i class=\"$menu[class]\" style=\"$menu[style]\"></i> $menu[name]</a>";
					subsmenu($menu['id']);
					echo "</li>";
				}	
			}
			if($app = 'menu' AND $link == '?app=menu&view=add') {
				$level = Level_Access;
				$sql2=$db->select(FDBPrefix.'menu_category',"*","id > 0  $level"); 
				$no=1;
				foreach($sql2 as $menu) {
					$sump = FQuery("menu","category='$menu[category]' AND home=1");
					$summ = FQuery("menu","category='$menu[category]'");
					if($sump)
						$sump="<span class='label label-danger home-label'>home</span>";
					else 
						$sump="";
					echo "<li class='list-menu menu-$menu[category]'><a class='link' href='?app=menu&cat=$menu[category]'><i class='icon-list-alt'></i>$menu[title]<span class='label label-primary'>$summ</span>$sump</a></li>";
					$no++;
				}
			}
		}
		echo "</ul>";
	}
}	

if(isset($_GET['view']))
  $name = $link.'-'.$_GET['view'];
else if(isset($_GET['act']))
  $name = $link.'-'.$_GET['act'];
else if(isset($_GET['type']))
  $name = $link.'-'.$_GET['type'];
else $name = $link;

if(isset($_SESSION['PLATFORM'])) $f = FAdmin; else $f = '';
?>

<!-- /#menu -->
<script language="javascript" type="text/javascript">
function loadUrl(url) {						
	$("#loadingbar").remove();
	var dataurl = $(url).attr('href');
	var n = dataurl.search('#');
	if(n == 0 || n === '') {
		return false;	
	} else {	
		$("body").append("<div id='loadingbar'></div>").find("#loadingbar").animate({width:'90%'},3000);
	}
	if(dataurl == 'index.php') url = '?theme=blank';
	else if(dataurl) url = dataurl+'&theme=blank';
	if(dataurl) {
					var gurl = url;
		var w = $("#loadingbar");
	  	$.ajax({
			url: '<?php echo $f;?>'+url,
			type: 'POST',
			data: "blank=true",
			timeout:10000, 
			error:function(data){ 
				$("#mods").modal("show");
				w.stop();
				w.animate({width:'101%'},100).fadeOut('fast');
			},
			success: function(data){	
				$('.alert').remove();
				w.stop();
				window.history.pushState(dataurl, "Fiyo CMS", dataurl);
				$('.mCSB_container').css('top',0);
				
				if(data == 'Redirecting...' || data == 'Access Denied!')
					window.location.replace(location.href);
				else {
					$("#mainApps").html(data);
					w.animate({width:'101%'},100).fadeOut('fast');
					setTimeout(function() {
					  w.remove();
					}, 1100);
					var z = $(".load-time").val();
					$("#load-time").html(z);	
					$.ajax({
						url: "<?php echo FAdminPath; ?>/module/breadcrumb.php",
						data: gurl+"&access",
						success: function(data){							
							$(".crumb").html(data);
						}
					});
					$('.scrolling').removeClass('hide-sidebar');
					noticeabs();
					loader();
					loadSpinner();
					loadChoosen();
					loadScrollbar();				
					$('#content a[href]').on('click', function(e){
						if (!$(this).attr('target') ){
							if ($(this).attr('href')!== window.location.hash){
								e.preventDefault();	
								loadUrl(this);
							}
						}
					});
				}
			}
		});
	}
}

window.onpopstate = function(e){
    if(e.state){
		var url = e.state+'&theme=blank';
		var w = $("#loadingbar");
	  	$.ajax({
			url: url,
			success: function(data){			
				$('.alert').remove();
				$("#mainApps").html(data);
				var z = $(".load-time").val();
				$("#load-time").html(z);
				w.animate({width:'101%'},100).delay(60).fadeOut('fast');
				setTimeout(function() {
				  w.remove();
				}, 1100);
				noticeabs();
				loader();
				loadSpinner();
				loadChoosen();
				loadScrollbar()
				$('#content a[href]').on('click', function(e){				
					if (!$(this).attr('target') ){
						if ($(this).attr('href')!== window.location.hash){
							e.preventDefault();	
							loadUrl(this);
						}
					}
				});
			}
		});
    }
};

$(function() {
	$('#content a[href], #left #menu a[href], .nav a[href]').click(function(e) {			
	   if (!$(this).attr('target')){
		   if ($(this).attr('href')!==window.location.hash){
			e.preventDefault();	
			loadUrl(this);
		   }
	   }
	});	
	$('#left #menu a[href]').click(function(e) {
		$('#left #menu').find('li').removeClass('active');
		$(this).parents('li').addClass('active');
	});
	$('#menu li.active a').removeClass('collapsed');
	$('#menu li.active ul').addClass('in');
	$('#menu li.active li.<?php echo $name; ?>').addClass('active');
});
</script>