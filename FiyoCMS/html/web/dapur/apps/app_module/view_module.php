<?php
/**
* @version		2.0
* @package		Fiyo CMS
* @copyright	Copyright (C) 2014 Fiyo CMS.
* @license		GNU/GPL, see LICENSE.
**/

defined('_FINDEX_') or die('Access Denied');

?>	
<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
	$(".activator label").click(function(){ 
		var parent = $(this).parents('.switch');
		var id = $('.number',parent).val();	
		var value = $('.type',parent).val();
		if(value == 1) value = 0; else value = 1;
		$.ajax({
			url: "apps/app_module/controller/status.php",
			data: "stat="+value+"&id="+id,
			success: function(data){	
				if(value == 1) {
					$('.type',parent).val("1");
				}
				else 
					$('.type',parent).val("0");				
				notice(data);		
			}
		});
	});
	$(".home label").click(function(){ 
		var parent = $(this).parents('.switch');
		var id = $('.number',parent).attr('value');	
		var value = $('.type',parent).attr('value');
		if(value == 1) value = 0; else value = 1;
		$.ajax({
			url: "apps/app_module/controller/status.php",
			data: "name="+value+"&id="+id,
			success: function(data){
				if(value == 1)
					$('.type',parent).val("1");
				else 
					$('.type',parent).val("0");				
				notice(data);		
			}
		});
	});
	
	$(".cb-enable").click(function(){		
		var parent = $(this).parents('.switch');
		$('.cb-disable',parent).removeClass('selected');
		$(this).addClass('selected');
		$('.checkbox',parent).attr('checked', false);	
	});
	
	$(".cb-disable").click(function(){		
		var parent = $(this).parents('.switch');
		$('.cb-enable',parent).removeClass('selected');
		$(this).addClass('selected');
		$('.checkbox',parent).attr('checked', false);	
	});
	
	$("#form").submit(function(e){
		e.preventDefault();
		var ff = this;
		var checked = $('input[name="check[]"]:checked').length > 0;
		if(checked) {	
			$('#confirmDelete').modal('show');	
			$('#confirm').on('click', function(){
				ff.submit();
			});		
		} else {
			noticeabs("<?php echo alert('error',Please_Select_Delete); ?>");
			$('input[name="check[]"]').next().addClass('input-error');
			return false;
		}
	});		
	loadTable();
});
</script>
<form method="post" id="form">
	<div id="app_header">
		<div class="warp_app_header">		
			<div class="app_title"><?php echo Module_Manager; ?></div>
			<div class="app_link">			
				<a class="add btn btn-primary" href="?app=module&act=add" title="<?php echo Add_new_module; ?>"><i class="icon-plus"></i> <?php echo Add_new_module; ?></a>
				<button type="submit" class="delete btn btn-danger btn-sm btn-grad" title="<?php echo Delete; ?>" value="<?php echo Delete; ?>" name="delete"><i class="icon-trash"></i> &nbsp;<?php echo Delete; ?></button>
				<input type="hidden" value="true" name="delete_confirm"  style="display:none" />
				<?php printAlert(); ?>
			</div>
		</div>		 
	</div>
	<table class="data">
		<thead>
			<tr>								  
				<th style="width:2% !important;" class="no" colspan="0" id="ck">
				<input type="checkbox" id="checkall" target="check[]"></th>
				<th style="width:20% !important;" hidden-xs><?php echo Title; ?></th>
				<th style="width:13% !important;" class='no hidden-xs' align="center">Status</th>
				<th style="width:17% !important;"><?php echo Position; ?></th>
				<th style="width:18% !important;" class='hidden-xs'><?php echo Type; ?></th>
				<th style="width:10% !important; text-align:center;" class='hidden-xs'><?php echo Short; ?></th>
				<th style="width:15% !important; text-align:center;" class='hidden-xs'><?php echo Access_Level; ?></th>
				<th style="width:5% !important;" class='hidden-xs'>ID</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$db = new FQuery();  
			$sql = $db->select(FDBPrefix.'module','*','','`position` DESC, `short` ASC, `name` ASC');
			$no=1;
			foreach($sql as $row){	
				if($row['status']==1)
				{ $stat1 ="selected"; $stat2 =""; $enable = ' enable';}							
				else
				{ $stat2 ="selected";$stat1 =""; $enable = 'disable';}
				
				$status ="<span class='invisible'>$enable</span>
				<div class='switch s-icon activator'>
					<label class='cb-enable $stat1 tips' data-placement='right' title='".Disable."'><span>
					<i class='icon-remove-sign'></i></span></label>
					<label class='cb-disable $stat2 tips' data-placement='right' title='".Enable."'><span>
					<i class='icon-check-circle'></i></span></label>
					<input type='hidden' value='$row[id]' class='number invisible'>
					<input type='hidden' value='$row[status]'  class='type invisible'>
				</div>";	
				
				//switch status
				if($row['show_title']==1)
				{ $sname1 ="selected"; $sname2 =""; $stitle = ' show';}							
				else
				{ $sname2 ="selected"; $sname1 =""; $stitle ='hide';}
				$sname ="
				<div class='switch s-icon home'><span class='invisible'>$stitle</span>
					<label class='cb-enable $sname1 tips' data-placement='left' title='".Hidden_title."'><span>
					<i class='icon-font'></i>
					</span></label>
					<label class='cb-disable $sname2 tips' data-placement='left' title='".Visible_title."'><span>
					<i class='icon-font'></i></span></label>
					<input type='hidden' value='$row[id]'  class='number invisible'>
					<input type='hidden' value='$row[show_title]' class='type invisible'>
				</div>";			
				
				//module name
				$name = "<a href='?app=module&act=edit&id=$row[id]' class='tips' data-placement='right' title='".Edit."'>$row[name]</a>";
				
				//checkbox
				$check = "<input type='checkbox' name='check[]' value='$row[id]' rel='ck'>";						
				//creat user group values	
				$level = oneQuery('user_group','level',"'$row[level]'",'group_name');
				if(empty($level)) $level = _Public;
				
				echo "<tr><td align='center'>$check</td><td>$name</td><td class='hidden-xs'><div class='switch-group'>$sname$status</div></td><td class='hidden-xs'>$row[position]</td><td>$row[folder]</td><td align='center' class='hidden-xs'>$row[short]</td><td align='center' class='hidden-xs'>$level</td><td align='center' class='hidden-xs'>$row[id]</td></tr>";
				$no++;	
			}
			?>
        </tbody>			
	</table>
</form>

<script type="text/javascript">
$(document).ready(function() {	
	CKEDITOR.replace( 'editor', {
		toolbar : 'Null',
	});
});
</script>	
<div style="display: none;">
	<div id="editor" style="display: none;"></div>
</div>