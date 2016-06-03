<script>
	$(document).ready(function()
	{
		$("#modaladd").click(function ()
		{
			var empty="";
			$(".modal-body #name").val( empty );
			$(".modal-body #desc").val( empty );
			$(".modal-body #startdate").val( empty );
			$(".modal-body #enddate").val( empty );
			$(".modal-body #errorname").html( empty );
			$(".modal-body #errordesc").html( empty );
			$(".modal-body #erroruser").html( empty );
			$(".modal-body #errorstart").html( empty );
			$(".modal-body #errorend").html( empty );
		});

		$('#addrecord').submit(function()
		{
			$.post($('#addrecord').attr('action'), $('#addrecord').serialize(), function( data )
			{
				if(data.st == 0)
				{
					hideloader();
		 			$(".modal-body #errorname").html( data.msg );
					$(".modal-body #errordesc").html( data.msg1 );
					$(".modal-body #erroruser").html( data.msg2 );
					$(".modal-body #errorstart").html( data.msg3 );
					//$(".modal-body #errorend").html( data.msg4 );
				}
				if(data.st == 1)
				{
		  			location.href="<?php echo base_url(); ?><?php echo $cpagename; ?>";
				}

			}, 'json');
			return false;
   		});

   		$(document).on("click", ".modaledit", function ()
		{
			var editid = $(this).data('editid');
		    var name = $(this).data('projname');
			var desc = $(this).data('projdesc');
			var userid = $(this).data('userid');
			var start = $(this).data('start');
			var end = $(this).data('end');
			var empty="";
			$(".modal-body #editid").val( editid );
			$(".modal-body #name1").val( name );
			$(".modal-body #desc1").val( desc );
			$(".modal-body #user1").val( userid );
			$(".modal-body #startdate1").val( start );
			$(".modal-body #enddate1").val( end );
			$(".modal-body #errorname1").html( empty );
			$(".modal-body #errordesc1").html( empty );
			$(".modal-body #erroruser1").html( empty );
			$(".modal-body #errorstart1").html( empty );
			$(".modal-body #errorend1").html( empty );
		});

		$('#updaterecord').submit(function()
		{
			$.post($('#updaterecord').attr('action'), $('#updaterecord').serialize(), function( data )
			{
				if(data.st == 0)
				{
					hideloader();
		 			$(".modal-body #errorname1").html( data.msg );
					$(".modal-body #errordesc1").html( data.msg1 );
					$(".modal-body #erroruser1").html( data.msg2 );
					$(".modal-body #errorstart1").html( data.msg3 );
					$(".modal-body #errorend1").html( data.msg4 );
				}
				if(data.st == 1)
				{
		  			location.reload();
				}

			}, 'json');
			return false;
   		});

   		$(document).on("click", ".modaldelete", function ()
		{
			if(confirm("Do you want to delete?"))
			{
				var id = $(this).data('id');
				$.post( "<?php echo base_url(); ?><?php echo $cpagename; ?>/delete",{id:id}, function( data ) {
					location.reload();
				});
			}
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
			var patt = new RegExp(/^[A-Za-z0-9 _\-\(\)\.]+$/);
			if(patt.test(search) || search=='')
			{
				var search = $('#search').val();
				$.post( "<?php echo base_url(); ?><?php echo $cpagename; ?>/searchrecord",{search:search}, function( data ) {
					location.href="<?php echo base_url(); ?><?php echo $cpagename; ?>/search";
				});
			}
			else
			{
				alert('The Search field may only contain alpha-numeric characters, underscores, dashes and bracket.');
			}
	    });

		$( "#startdate" ).datepicker(
		{
			showOn: "button",
			buttonImage: "<?php echo base_url(); ?>img/calendar.gif",
			buttonImageOnly: true,
			buttonText: "Select date",
			dateFormat: "dd-mm-yy"

		});

		$( "#enddate" ).datepicker(
		{
			showOn: "button",
			buttonImage: "<?php echo base_url(); ?>/img/calendar.gif",
			buttonImageOnly: true,
			buttonText: "Select date",
			dateFormat: "dd-mm-yy"

		});

		$( "#startdate1" ).datepicker(
		{
			showOn: "button",
			buttonImage: "<?php echo base_url(); ?>/img/calendar.gif",
			buttonImageOnly: true,
			buttonText: "Select date",
			dateFormat: "dd-mm-yy"

		});

		$( "#enddate1" ).datepicker(
		{
			showOn: "button",
			buttonImage: "<?php echo base_url(); ?>/img/calendar.gif",
			buttonImageOnly: true,
			buttonText: "Select date",
			dateFormat: "dd-mm-yy"

		});

	});



</script>
<?php
	$labelnames='';
	foreach ($labels as $label): 
		$labelnames .= ','.$label->sec_label_desc;
	endforeach;
	$labelnames=substr($labelnames,1);
	$labelname=explode(",",$labelnames);
?>
<div id="after_header">
<div class="container">
	<!-- INPUT HERE-->
	<div class="page-header">
		<h1 id="nav"><?php echo $labelobject; ?></h1>
	</div>
	<div class="row">
		<div class="col-md-3">
			<ul class="breadcrumb">
				<li><a href="<?php echo base_url(); ?>home">Home</a></li>
                <li><?php echo $labelgroup; ?></li>
                <li class="active"><?php echo $labelobject; ?></li>
			</ul>
		</div>
	</div>
	<!--SEARCH-->
	<div class="form-group">
		<label for="search" class="col-sm-1 control-label">Search</label>
		<div class="col-sm-4">
			<input type="text" class="form-control" id="search" name="search" value="<?php echo $searchrecord; ?>" placeholder="Enter the text here">
		</div>
		<input type="button" class="btn btn-primary btn-sm" id="recordsearch" name="recordsearch" value="Search" />
		<a href="<?php echo base_url(); ?><?php echo $cpagename; ?>" class="btn btn-danger btn-sm">Clear</a>
		<button type="button" class="btn btn-success btn-sm pull-right" id="modaladd" name="modaladd" data-toggle="modal" data-target="#myModal" <?php if($addperm==0) echo 'disabled="true"'; ?>>Add New</button>
	</div>
	<!-- -------------------------------------------- -->
	<!-- pop-up -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog">
			<div class="modal-content">
				<form method=post id=addrecord action="<?php echo base_url(); ?><?php echo $cpagename; ?>/add/">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h4 class="modal-title" id="myModalLabel">Add New <?php echo $labelobject; ?></h4>
					</div>
					<div class="modal-body">
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-5">
						    		<label id="errorname" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"><?php echo $labelname[0]; ?><red>*</red></label>
						    	<div class="col-sm-8">
						    		<input type="text" class="form-control" id="name" name="name" placeholder="Sungai Buloh KTM Station" maxlength="120">
						        </div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-5">
						    		<label id="errordesc" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-3 control-label"><?php echo $labelname[1]; ?><red>*</red></label>
								<div class="col-sm-8">
									<input type="text" class="form-control" id="desc" name="desc" placeholder="Sungai Buloh KTM Station" maxlength="250">
								</div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-5">
						    		<label id="erroruser" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-3 control-label"><?php echo $labelname[2]; ?><red>*</red></label>
								<div class="col-sm-8">
									<select class="form-control" id="user" name="user">
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
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-8">
						    		<label id="errorstart" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-3 control-label"><?php echo $labelname[3]; ?><red>*</red></label>
								<div class="col-sm-8">
          							<input class="input-small" type="text" id="startdate" name="startdate" placeholder="12/06/2015">
								</div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-5">
						    		<label id="errorend" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
<!--								<label for="search" class="col-sm-3 control-label">--><?php //echo $labelname[4]; ?><!--<red>*</red></label>-->
								<label for="search" class="col-sm-3 control-label"><?php echo $labelname[4]; ?></label>
								<div class="col-sm-8">
					           		<input class="input-small" type="text" id="enddate" name="enddate" placeholder="23/09/2015">
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
						<input type="submit" class="btn btn-primary btn-sm" value="Add Project" onclick="showloader(); console.log('showloader add');" />
					</div>
				</form>
			</div>
		</div>
	</div>
	<!--close pop-up-->
	<!-- pop-up -->
	<div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog">
			<div class="modal-content">
				<form method=post id=updaterecord action="<?php echo base_url(); ?><?php echo $cpagename; ?>/update/">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h4 class="modal-title" id="myModalLabel">Edit <?php echo $labelobject; ?></h4>
					</div>
					<div class="modal-body">
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-5">
						    		<label id="errorname1" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"><?php echo $labelname[0]; ?><red>*</red></label>
						    	<div class="col-sm-8">
						    		<input type="hidden" class="form-control" id="editid" name="editid">
						    		<input type="text" class="form-control" id="name1" name="name1" placeholder="Sungai Buloh KTM Station" maxlength="120">
						        </div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-5">
						    		<label id="errordesc1" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-3 control-label"><?php echo $labelname[1]; ?><red>*</red></label>
								<div class="col-sm-8">
									<input type="text" class="form-control" id="desc1" name="desc1" placeholder="Sungai Buloh KTM Station" maxlength="250">
								</div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-5">
						    		<label id="erroruser1" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-3 control-label"><?php echo $labelname[2]; ?><red>*</red></label>
								<div class="col-sm-8">
									<select class="form-control" id="user1" name="user1">
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
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-8">
						    		<label id="errorstart1" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-3 control-label"><?php echo $labelname[3]; ?><red>*</red></label>
								<div class="col-sm-8">
          							<input class="input-small" type="text" id="startdate1" name="startdate1" >
								</div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-5">
						    		<label id="errorend1" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-3 control-label"><?php echo $labelname[4]; ?><red>*</red></label>
								<div class="col-sm-8">
					           		<input class="input-small" type="text" id="enddate1" name="enddate1">
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
						<input type="submit" class="btn btn-primary btn-sm" value="Save Changes" onclick="showloader(); console.log('showloader edit');"/>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!--close pop-up-->
	<!-- <div class="row text-center text-danger"><?php echo $message; ?> </div> -->
	<div class="row text-center <?php echo $message_type == 1? "text-success" : "text-danger"; ?>"><?php echo $message; ?></div>
	<div class="row">
		<table class="table table-striped table-hover">
	        <thead>
    			<tr>
      				<th>No</th>
					<th><?php echo $labelname[0]; ?></th>
					<th><?php echo $labelname[2]; ?></th>
					<th><?php echo $labelname[3]; ?></th>
					<th><?php echo $labelname[4]; ?></th>
					<th>Edit</th>
					<th>Delete</th>
    			</tr>
    		</thead>
    		<tbody>
    			<?php
					$sno=$page;
					foreach ($records as $record):
					$startdate=date("d-m-Y", strtotime($record->start_date));
					$enddate=date("d-m-Y", strtotime($record->end_date));
				?>
						<tr>
				  			<td><?php echo $sno; ?></td>
				  			<td><?php echo $record->project_name; ?></td>
				  			<td><?php echo $record->user_full_name; ?></td>
				  			<td><?php echo $startdate;  ?></td>
				  			<td><?php echo $enddate; ?></td>
		          			<td>
		          				<?php
									if($editperm==1)
									{

								?>
										<a href="#" data-toggle="modal" data-target="#myModal1" class="modaledit" data-editid="<?php echo $record->project_no; ?>" data-userid="<?php echo $record->user_id; ?>" data-projname="<?php echo $record->project_name; ?>" data-projdesc="<?php echo $record->project_definition; ?>" data-start="<?php echo $startdate; ?>" data-end="<?php echo $enddate; ?>" ><span class="glyphicon glyphicon-edit">&nbsp;</span></a>
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
		          						<a href="#" data-toggle="modal" class="modaldelete" data-id="<?php echo $record->project_no; ?>"><span class="glyphicon glyphicon-trash">&nbsp;</span></a>
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
						echo '<tr><td class="row text-center text-danger" colspan="7"> No Record Found</td></tr></tbody></table>';
					}
					else
					{
				?>
  			</tbody>
		</table>
	</div>
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