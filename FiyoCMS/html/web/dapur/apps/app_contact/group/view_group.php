<?php
/**
* @version		2.0
* @package		Fiyo CMS
* @copyright	Copyright (C) 2014 Fiyo CMS.
* @license		GNU/GPL, see license.txt
* @description	
**/

defined('_FINDEX_') or die('Access Denied');
printAlert();
?>	
<script type="text/javascript" charset="utf-8">
$(document).ready(function() {

		$("#form").submit(function(e){
			e.preventDefault();
			var ff = this;
			var checked = $('input[name="check_group[]"]:checked').length > 0;
			if(checked) {	
				$('#confirmDelete').modal('show');	
				$('#confirm').on('click', function(){
					ff.submit();
				});		
			} else {
				noticeabs("<?php echo alert('error',Please_Select_Menu); ?>");
				$('input[name="check_group[]"]').next().addClass('input-error');
				return false;
			}
		});	
		
		loadTable();
		$('#checkall').click(function () {
			$(this).parents('form:eq(0)').find(':checkbox').attr('checked', this.checked);
		});
});
</script>
<form method="post" id="form">
	<div id="app_header">
		<div class="warp_app_header">				
			<div class="app_title"><?php echo Contact_Manager; ?></div>
			<div class="app_link">			
				<a class="btn add btn-primary" href="?app=contact&view=group&act=add" title="<?php echo Add_new_group; ?>"><i class="icon-plus"></i> <?php echo New_Group; ?></a> 
				<button type="submit" class="delete btn btn-danger" title="<?php echo Delete; ?>" value="<?php echo Delete; ?>" name="delete_group"><i class="icon-trash"></i> &nbsp;<?php echo Delete; ?></button>
				<input type="hidden" value="true" name="delete_confirm"  style="display:none" />
		  </div> 	
		</div>
	</div>
	<table class="data">
		<thead>
			<tr>								  
				<th width="3%" class="no" colspan="0" id="ck">  
					<input type="checkbox" id="checkall"></th>				
				<th style="width:20% !important;">Group Name</th>
				<th style="width:60% !important;"><?php echo Description; ?></th>
				<th style="width:15% !important; text-align: center;" class=''>Contact</th>
				<th style="width:2% !important;">ID</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$db = new FQuery();  
			$sql = $db->select(FDBPrefix.'contact_group'); 
			$no = 1; 
			$tcc = FDBPrefix."contact";
			foreach($sql as $row){
				$count = "SELECT COUNT(*) FROM  `$tcc` WHERE group_id='$row[group_id]'"; 
				$count = $db->db->query( $count)->fetchColumn();
				
				$checkbox ="<input type='checkbox' name='check_group[]' value='$row[group_id]'>";	
				$name ="<a class='tips' title='".Edit."' href='?app=contact&view=group&act=edit&id=$row[group_id]'>$row[group_name]</a>";
				echo "<tr>";
				echo "<td align='center'>$checkbox</td><td>$name</td><td>$row[group_desc]</td><td align='center'>$count</td><td align='center'>$row[group_id]</td>";
				echo "</tr>";
				$no++;	
			}
			?>
        </tbody>			
	</table>
</form>


<div class="app_link tabs" style="text-align: center;width: 90%;">	
	<a class="btn apps " href="?app=contact" title="<?php echo Manage_Apps; ?>"><i class="icon-user"></i> Personal</a>		
	<a class="btn module active" href="?app=contact&view=group" title="<?php echo Manage_Modules; ?>"><i class="icon-group"></i> Group</a>	
</div>