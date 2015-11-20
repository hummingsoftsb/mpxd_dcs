<?php
	$userkey='';
	$uservalue='';
	foreach ($users as $user):
	if($userkey=='')
	{
		$userkey= '"'.$user->user_id.'"';
		$uservalue= '"'.$user->user_full_name.'"';
	}
	else
	{
		$userkey .= ',"'.$user->user_id.'"';
		$uservalue .= ',"'.$user->user_full_name.'"';
	}
	endforeach;

	$labelnames='';
	foreach ($labels as $label): 
		$labelnames .= ','.$label->sec_label_desc;
	endforeach;
	$labelnames=substr($labelnames,1);
	$labelname=explode(",",$labelnames);

?>
<script>


var userskey=<?php echo '[' . $userkey . ']'; ?>;
var usersvalue=<?php echo '[' . $uservalue . ']'; ?>;
var journals = <?php echo json_encode($records); ?>;

var mode = 'add';

function openModalEdit(journal_no) {
	mode = "update";
	$('#MyModal2').modal('show');
	var j = $.grep(journals, function(n){return (n.journal_no == journal_no)})[0];
	
	$('#MyModal2 #projectname').val(j.project_no);
	$('#MyModal2 #journalname').val(j.journal_name);
	$('#MyModal2 #user').val(j.owner_user_id);
	$('#MyModal2 #validateuser1').val(j.validate_user_id);
	$('#MyModal2 #dataentryuser1').val(j.data_user_id);
	$('#MyModal2 #reminder_frequency').val(j.reminder_frequency);
	$('#MyModal2 #journal_no').val(j.journal_no);
}

$(document).on("click", ".modaldelete", function ()
{
    if(confirm("Do you want to delete?"))
    {
        var id = $(this).data("id");
        $.post( "<?php echo base_url(); ?><?php echo $cpagename; ?>/delete",{id:id}, function( data ) {
            location.reload();
        });
    }
});

function notify(text) {
	$('#notification').empty().append($('<div class="alert alert-danger alert-dismissible fade in" role="alert" style="text-align:left;"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button><strong id="notification_text">'+text+'</strong></div></div>')).show();
	$('#notification2').empty().append($('<div class="alert alert-danger alert-dismissible fade in" role="alert" style="text-align:left;"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button><strong id="notification_text">'+text+'</strong></div></div>')).show();
}
	$(document).ready(function()
	{
	
		$('#addbutton').on('click', function(){mode = 'add';$('#journal_no').val('');});
	
		$('#addrecord, #addrecord2').on('submit', function(e){
			var $t = $(this);
			var data = $t.serializeArray();
			var url = '<?php echo base_url(); ?><?php echo $cpagename; ?>/'+ ((mode == "add") ? "add":"update") +'/';
			console.log(url);
			$.post(url, data)
			.done(function(d){
				console.log(d);
				try {
					var log = $.parseJSON(d);
					console.log(log);
					if ((log.st == 0) && (log.msg != "")) {
						hideloader();
						notify(log.msg);
					} else if (log.st == 1) {
						$('#MyModal').modal('hide');
						hideloader();
						location.reload();
					}
				} catch(e) {
					hideloader();
					notify("Unknown error. Please contact system administrator")
				}
				
				hideloader(function(){});
			})
			
			e.preventDefault();
			//return false;
		});

		

		$("#recordselect").change(function()
		{
			var recordselect = $(this).val();
			$.post( "<?php echo base_url(); ?><?php echo $cpagename; ?>/selectrecord",{recordselect:recordselect}, function( data ) {
				location.href="<?php echo base_url(); ?><?php echo $cpagename; ?>/select";
			});
	    });

	    $("#recordsearch").click(function ()
	    {
			var search = $('#search').val();
			$.post( "<?php echo base_url(); ?><?php echo $cpagename; ?>/searchrecord",{search:search}, function( data ) {
				location.href="<?php echo base_url(); ?><?php echo $cpagename; ?>/search";
			});
	    });
	});

</script>
<div id="after_header">
<div class="container">
	<!-- INPUT HERE-->
	<div class="page-header">
		<h1 id="nav"><?php echo $labelobject; ?></h1>
	</div>
	<div class="row">
		<div class="col-md-4">
			<ul class="breadcrumb" style=" text-align: center; ">
				<li><a href="<?php echo base_url(); ?>home">Home</a></li>
                <li><?php echo $labelgroup; ?></li>
                <li class="active"><?php echo $labelobject; ?></li>
             </ul>
		</div>
	</div>
	<div class="row">
		<div class="form-group">
			<label for="search" class="col-sm-1 control-label">Search</label>
			<div class="col-sm-4">
				<input type="email" class="form-control" id="search" placeholder="Search" value="<?php echo $searchrecord; ?>">
			</div>
			<input type="button" class="btn btn-primary btn-sm" id="recordsearch" name="recordsearch" value="Search" />
			<a href="<?php echo base_url(); ?><?php echo $cpagename; ?>" class="btn btn-danger btn-sm">Clear</a>
			<button type="button" class="btn btn-success btn-sm pull-right" id="addbutton"  data-toggle="modal" data-target="#MyModal" <?php if($addperm==0) echo 'disabled="true"'; ?>>Add New</button>
		</div>
	</div>
	<!-- pop-up -->
	<div class="modal fade" id="MyModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-md">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
						<h4 class="modal-title" id="myModalLabel">Add <?php echo $labelobject; ?></h4>
					</div>
					<div class="modal-body">
						<form method=post id=addrecord>
							
							
							<input type="hidden" name="journal_no" id="journal_no" value=""/>
							<div class="row" style="margin-bottom:15px">
							<div class="form-group">
								<label for="select" class="col-lg-4 control-label"><?php echo $labelname[0]; ?> <red>*</red></label>
								<div class="col-lg-8">
									<select class="dropdown-toggle form-control" id="projectname" name="projectname">
										<?php
											foreach ($projects as $project):
										?>
												<option value="<?php echo $project->project_no; ?>"><?php echo $project->project_name; ?></option>
										<?php
											endforeach;
										?>
									</select>
								</div>
							</div>
							</div>

							<div class="row" style="margin-bottom:15px">
							<div class="form-group">
								<label for="select" class="col-lg-4 control-label"><?php echo $labelname[1]; ?> <red>*</red></label>
								<div class="col-lg-8">
									<input type="text" class="form-control" id="journalname" name="journalname" placeholder="" maxlength="120">
								</div>
							</div>
							</div>


							<div class="row" style="margin-bottom:15px">
							<div class="form-group">
								<label for="select" class="col-lg-4 control-label"><?php echo $labelname[3]; ?> <red>*</red></label>
								<div class="col-lg-8">
									<select class="dropdown-toggle form-control" id="user" name="user">
										<?php
											$session_data = $this->session->userdata('logged_in');
											$userid = $session_data['id'];
											foreach ($users as $user):
												if($user->user_id==$userid)
													{
										?>
														<option value="<?php echo $user->user_id; ?>" selected="selected"><?php echo $user->user_full_name; ?></option>
												<?php	
													}
													else
													{
												?>
														<option value="<?php echo $user->user_id; ?>"><?php echo $user->user_full_name; ?></option>
										<?php
													}
											endforeach;
										?>
									</select>
								</div>
							</div>
							</div>

							<div class="row" style="margin-bottom:15px">
							<div class="form-group">
        						<label for="select" class="col-lg-4 control-label"><?php echo $labelname[5]; ?> <red>*</red></label>
        						<div class="col-lg-8">
									<select class="dropdown-toggle form-control" id="validateuser1" name="validateuser1">
										<?php
											foreach ($users as $user):
										?>
												<option value="<?php echo $user->user_id; ?>"><?php echo $user->user_full_name; ?></option>
										<?php
											endforeach;
										?>
									</select>
								</div>
							</div>
							</div>
							
							
							<div class="row" style="margin-bottom:15px">
							<div class="form-group">
								<label for="select" class="col-lg-4 control-label"><?php echo $labelname[6]; ?> <red>*</red></label>
								<div class="col-lg-8">
									<select class="dropdown-toggle form-control" id="dataentryuser1" name="dataentryuser1">
										<?php
											foreach ($users as $user):
										?>
												<option value="<?php echo $user->user_id; ?>"><?php echo $user->user_full_name; ?></option>
										<?php
											endforeach;
										?>
									</select>
								</div>
							</div>
							</div>
							
							<div class="row" style="margin-bottom:15px">
							<div class="form-group">
								<label for="select" class="col-lg-4 control-label">Reminder Frequency</label>
								<div class="col-lg-8">
									<select class="dropdown-toggle form-control" id="reminder_frequency" name="reminder_frequency">
										<option value="">None</option>
										<option value="Weekly">Weekly</option>
										<option value="Monthly">Monthly</option>
									</select>
								</div>
							</div>
							</div>
							
							
						</div>
						
						<div class="modal-footer" style="text-align:center;border:0;">
						<div class="row">
							<div class="col-md-12"><div id="notification" style="display:none">
		
								
								</div>
								</div>
						</div>
							<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
							<input type="submit" class="btn btn-primary btn-sm" value="Add Journal" onclick="showloader();" />
						</div>
					</form>
				</div>
			
		</div>
	</div>
	<!--close-->
	<!-- pop-up -->
	<div class="modal fade" id="MyModal2" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-md">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
						<h4 class="modal-title" id="myModalLabel">Edit <?php echo $labelobject; ?></h4>
					</div>
					<div class="modal-body">
						<form method=post id=addrecord2>
							
							
							<input type="hidden" name="journal_no" id="journal_no" value=""/>
							<div class="row" style="margin-bottom:15px">
							<div class="form-group">
								<label for="select" class="col-lg-4 control-label"><?php echo $labelname[0]; ?> <red>*</red></label>
								<div class="col-lg-8">
									<select class="dropdown-toggle form-control" id="projectname" name="projectname">
										<?php
											foreach ($projects as $project):
										?>
												<option value="<?php echo $project->project_no; ?>"><?php echo $project->project_name; ?></option>
										<?php
											endforeach;
										?>
									</select>
								</div>
							</div>
							</div>

							<div class="row" style="margin-bottom:15px">
							<div class="form-group">
								<label for="select" class="col-lg-4 control-label"><?php echo $labelname[1]; ?> <red>*</red></label>
								<div class="col-lg-8">
									<input type="text" class="form-control" id="journalname" name="journalname" placeholder="" maxlength="120">
								</div>
							</div>
							</div>


							<div class="row" style="margin-bottom:15px">
							<div class="form-group">
								<label for="select" class="col-lg-4 control-label"><?php echo $labelname[3]; ?> <red>*</red></label>
								<div class="col-lg-8">
									<select class="dropdown-toggle form-control" id="user" name="user">
										<?php
											$session_data = $this->session->userdata('logged_in');
											$userid = $session_data['id'];
											foreach ($users as $user):
												if($user->user_id==$userid)
													{
										?>
														<option value="<?php echo $user->user_id; ?>" selected="selected"><?php echo $user->user_full_name; ?></option>
												<?php	
													}
													else
													{
												?>
														<option value="<?php echo $user->user_id; ?>"><?php echo $user->user_full_name; ?></option>
										<?php
													}
											endforeach;
										?>
									</select>
								</div>
							</div>
							</div>

							<div class="row" style="margin-bottom:15px">
							<div class="form-group">
        						<label for="select" class="col-lg-4 control-label"><?php echo $labelname[5]; ?> <red>*</red></label>
        						<div class="col-lg-8">
									<select class="dropdown-toggle form-control" id="validateuser1" name="validateuser1">
										<?php
											foreach ($users as $user):
										?>
												<option value="<?php echo $user->user_id; ?>"><?php echo $user->user_full_name; ?></option>
										<?php
											endforeach;
										?>
									</select>
								</div>
							</div>
							</div>
							
							
							<div class="row" style="margin-bottom:15px">
							<div class="form-group">
								<label for="select" class="col-lg-4 control-label"><?php echo $labelname[6]; ?> <red>*</red></label>
								<div class="col-lg-8">
									<select class="dropdown-toggle form-control" id="dataentryuser1" name="dataentryuser1">
										<?php
											foreach ($users as $user):
										?>
												<option value="<?php echo $user->user_id; ?>"><?php echo $user->user_full_name; ?></option>
										<?php
											endforeach;
										?>
									</select>
								</div>
							</div>
							</div>
							
							<div class="row" style="margin-bottom:15px">
							<div class="form-group">
								<label for="select" class="col-lg-4 control-label">Reminder Frequency</label>
								<div class="col-lg-8">
									<select class="dropdown-toggle form-control" id="reminder_frequency" name="reminder_frequency">
										<option value="">None</option>
										<option value="Weekly">Weekly</option>
										<option value="Monthly">Monthly</option>
									</select>
								</div>
							</div>
							</div>
							
							
						</div>
						
						<div class="modal-footer" style="text-align:center;border:0;">
						<div class="row">
							<div class="col-md-12"><div id="notification2" style="display:none">
		
								
								</div>
								</div>
						</div>
							<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
							<input type="submit" class="btn btn-primary btn-sm" value="Save Changes" onclick="showloader();" />
						</div>
					</form>
				</div>
			
		</div>
	</div>
	<!--close-->
	<!-- pop-up -->
	<!--close-->
	<!-- <div class="row text-center text-danger"><?php echo $message; ?> </div> -->
	<div class="row text-center <?php echo $message_type == 1? "text-success" : "text-danger"; ?>"><?php echo $message; ?></div>
	<div>
		<table class="table table-striped table-hover">
			<tr>
				<th>No</th>
				<th><?php echo $labelname[0]; ?></th>
				<th><?php echo $labelname[1]; ?></th>
				
				<th><?php echo $labelname[3]; ?></th>
				<th>Edit</th>
				<th>Design</th>
				<th>Delete</th>
			</tr>
			<?php
				$sno=$page;
				foreach ($records as $record):
					//$startdate=date("d-m-Y", strtotime($record->start_date));
					//$enddate=date("d-m-Y", strtotime($record->end_date));
					//$validatorvalues=$validatorvalue[$record->journal_no];
					//$dataentryvalues=$dataentryvalue[$record->journal_no];
					//$dataattbvalues=$dataattbvalue[$record->journal_no];

			?>
					<tr>
						<td><?php echo $sno; ?></td>
						<td><?php echo $record->project_name; ?></td>
						<td><?php echo $record->journal_name; ?></td>
						
						<td><?php echo $record->user_full_name; ?></td>
						<td><?php
								if($editperm==1)
								{

							?>
									
									
									<!-- added by ilyas -->
										<a href="javascript:openModalEdit(<?php echo $record->journal_no; ?>);"> <span class="glyphicon glyphicon-edit" style="">&nbsp;</span></a>
									<!-- end -->
							<?php
								}
								else
								{
									echo '<span class="glyphicon glyphicon-edit">&nbsp;</span>';
								}
							?></td>
						<td>
							<?php
								if($editperm==1)
								{

							?>
									
									
									<!-- added by ilyas -->
										<a href="<?php echo base_url(); ?>index.php/ilyasdesign?jid=<?php echo $record->journal_no; ?>"> <span class="glyphicon glyphicon-edit" style="color:red">&nbsp;</span></a>
									<!-- end -->
							<?php
								}
								else
								{
									echo '<span class="glyphicon glyphicon-edit">&nbsp;</span>';
								}
							?>
						</td>
						<td>
							<?php
								if($delperm==1)
								{
							?>
									<a href="#" data-toggle="modal" class="modaldelete" data-id="<?php echo $record->journal_no; ?>"><span style="color:#333;" class="glyphicon glyphicon-trash">&nbsp;</span></a>
							<?php
								}
								else
								{
									echo '<span class="glyphicon glyphicon-trash">&nbsp;</span>';
								}
							?>
						</td>
					</tr>
			<?php
				$sno=$sno+1;
				endforeach;
				if($totalrows==0)
				{
					echo '<tr><td class="row text-center text-danger" colspan="8"> No Record Found</td></tr></table>';
				}
				else
				{
			?>
		</table>
		<div class="row">
		<div class="col-md-12">
			<div class="col-md-4">
				<ul class="pagination">
                	<?php echo $this->pagination->create_links(); ?>
				</ul>
			</div>
			<div class="col-md-4 col-md-offset-1" >
				<div class="form-group">
					<label for="search" class="col-sm-2 control-label" style="padding-top: 15px; padding-bottom: 5px;">Show</label>
					<div class="col-sm-3" style="padding-top: 15px; padding-bottom: 5px;">
						<select class="form-control" id="recordselect" name="recordselect" onchange="this.form.submit()">
							<option <?php if($selectrecord=="10") echo "selected=selected"; ?>>10</option>
							<option <?php if($selectrecord=="20") echo "selected=selected"; ?>>20</option>
							<option <?php if($selectrecord=="40") echo "selected=selected"; ?>>40</option>
						</select>
					</div>
				</div>
			</div>
			<?php
				// Display the number of records in a page
				$end=$mpage+$page-1;
				if($totalrows<$end) $end=$totalrows;
			?>
			<div class="col-md-3" style="padding-top: 22px;"> Showing <?php echo $page; ?> to <?php echo $end; ?> of <?php echo $totalrows; ?> rows  </div>
		</div>
		<?php }?>
	</div>
</div>
<script type="text/javascript">
	function showloader() {
		$('#after_header').loader('show');
	}

	function hideloader() {
		setTimeout(function(){$('#after_header').loader('hide')},200);
	}
	$.post("<?php echo $this->config->base_url().'index.php/'.$cpagename; ?>/validate?jid=<?php echo $details->journal_no; ?>", data).always(function(data){
		console.log(data);
		hideloader();
		disallow();
		location.href='<?php echo $this->config->base_url() ?>/index.php/journalvalidationnonp';
		if (typeof callback == "function") callback();
	});
</script>
</div>