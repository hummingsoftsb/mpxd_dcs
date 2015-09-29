<script>
	$(document).ready(function()
	{
		$("#modaladd").click(function ()
		{
			var role = "";
			var roledesc = "";
			var error="";
			$(".modal-body #role").val( role );
			$(".modal-body #roledesc").val( roledesc );
			$('#erroraddrole').html(error);
			$('#erroraddroledesc').html(error);
		});

		$('#addrecord').submit(function()
		{
			$.post($('#addrecord').attr('action'), $('#addrecord').serialize(), function( data )
			{
				if(data.st == 0)
				{
					hideloader();
		 			$('#erroraddrole').html(data.msg);
		 			$('#erroraddroledesc').html(data.msg1);
				}
				if(data.st == 1)
				{
					hideloader();
					$('#MyModal').modal('hide');
					$('#MyModal2').modal('show');
					$('input:checkbox').removeAttr('checked');
					$(".modal-body #rolepermid").val( data.msg );
					$(".modal-body #roleupdate").val( 'Add' );

				}

			}, 'json');
			return false;
   		});

   		$(document).on("click", ".modaledit", function () {

			var roleid = $(this).data('roleid');
		    var role = $(this).data('role');
		    var roledesc = $(this).data('roledesc');
		    var error="";
			$(".modal-body #roleid").val( roleid );
		    $(".modal-body #role1").val( role );
		    $(".modal-body #roledesc1").val( roledesc );
		    $('#erroreditrole').html(error);
			$('#erroreditroledesc').html(error);
		});

		$('#updaterecord').submit(function()
		{
			$.post($('#updaterecord').attr('action'), $('#updaterecord').serialize(), function( data )
			{
				if(data.st == 0)
				{
					hideloader();
		 			$('#erroreditrole').html(data.msg);
		 			$('#erroreditroledesc').html(data.msg1);
				}
				if(data.st == 1)
				{
		  			location.reload();
				}

			}, 'json');
			return false;
   		});

		$(document).on("click", ".modalpermedit", function ()
		{
			var	roledat = $(this).data('roledata');
			var rolepermid = $(this).data('roleid');
			var roledat1 = roledat.split(',777,');
			$('input:checkbox').removeAttr('checked');
			for (var i = 0; i < roledat1.length; i++)
			{
				var roledat2 = roledat1[i].split(',');
				for (var k = 1; k < roledat2.length; k++)
				{
				    if(roledat2[k+1]==1)
			        {
			        	var rdata = roledat2[1]+'_'+k;
			        	$(".modal-body #"+rdata).prop('checked',true);
			        }
				}
        	}
        	$(".modal-body #rolepermid").val( rolepermid );
        	$(".modal-body #roleupdate").val( 'Update' );
		});

		$('#editpermission').submit(function()
		{
			$.post($('#editpermission').attr('action'), $('#editpermission').serialize(), function( data )
			{
				location.reload();

			}, 'json');
			return false;
   		});

		$('#checkallview').click(function(event)
		{
			//on click
		    if(this.checked)
		    {
		        // check select status
				$('.1').each(function()
				{ //loop through each checkbox
					this.checked = true;  //select all checkboxes with class "checkbox1"
		        });
			}
			else
			{
				$('.1').each(function()
				{ //loop through each checkbox
					this.checked = false; //deselect all checkboxes with class "checkbox1"
		        });
			}
    	});


		$('#checkalladd').click(function(event)
		{
			//on click
		    if(this.checked)
		    {
		        // check select status
				$('.2').each(function()
				{ //loop through each checkbox
					this.checked = true;  //select all checkboxes with class "checkbox1"
		        });
			}
			else
			{
				$('.2').each(function()
				{ //loop through each checkbox
					this.checked = false; //deselect all checkboxes with class "checkbox1"
		        });
			}
    	});

    	$('#checkalledit').click(function(event)
		{
			//on click
		    if(this.checked)
		    {
		        // check select status
				$('.3').each(function()
				{ //loop through each checkbox
					this.checked = true;  //select all checkboxes with class "checkbox1"
		        });
			}
			else
			{
				$('.3').each(function()
				{ //loop through each checkbox
					this.checked = false; //deselect all checkboxes with class "checkbox1"
		        });
			}
    	});

    	$('#checkalldelete').click(function(event)
		{
			//on click
		    if(this.checked)
		    {
		        // check select status
				$('.4').each(function()
				{ //loop through each checkbox
					this.checked = true;  //select all checkboxes with class "checkbox1"
		        });
			}
			else
			{
				$('.4').each(function()
				{ //loop through each checkbox
					this.checked = false; //deselect all checkboxes with class "checkbox1"
		        });
			}
    	});

    	$('#checkallexport').click(function(event)
		{
			//on click
		    if(this.checked)
		    {
		        // check select status
				$('.5').each(function()
				{ //loop through each checkbox
					this.checked = true;  //select all checkboxes with class "checkbox1"
		        });
			}
			else
			{
				$('.5').each(function()
				{ //loop through each checkbox
					this.checked = false; //deselect all checkboxes with class "checkbox1"
		        });
			}
    	});

    	$('#checkallprint').click(function(event)
		{
			//on click
		    if(this.checked)
		    {
		        // check select status
				$('.6').each(function()
				{ //loop through each checkbox
					this.checked = true;  //select all checkboxes with class "checkbox1"
		        });
			}
			else
			{
				$('.6').each(function()
				{ //loop through each checkbox
					this.checked = false; //deselect all checkboxes with class "checkbox1"
		        });
			}
    	});

    	$('#checkallemail').click(function(event)
		{
			//on click
		    if(this.checked)
		    {
		        // check select status
				$('.7').each(function()
				{ //loop through each checkbox
					this.checked = true;  //select all checkboxes with class "checkbox1"
		        });
			}
			else
			{
				$('.7').each(function()
				{ //loop through each checkbox
					this.checked = false; //deselect all checkboxes with class "checkbox1"
		        });
			}
    	});

   		$(document).on("click", ".modaldelete", function ()
		{
			if(confirm("Do you want to delete the role?"))
			{
				var roleid = $(this).data('roleid');
				$.post( "<?php echo base_url(); ?><?php echo $cpagename; ?>/delete",{id:roleid}, function( data ) {
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
		<button type="button" class="btn btn-success btn-sm pull-right modaladd" id="modaladd" name="modaladd" data-toggle="modal" data-target=".bs-example-modal-md1" <?php if($addperm==0) echo 'disabled="true"'; ?>>Add New</button>
	</div>
	<!-- -------------------------------------------- -->
	<!-- pop-up -->
	<div class="modal fade bs-example-modal-md1" id="MyModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-md">
    		<div class="modal-content">
    			<div class="modal-content">
    				<form method=post id=addrecord action="<?php echo base_url(); ?><?php echo $cpagename; ?>/add/">
      					<div class="modal-header">
	        				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
	        				<h4 class="modal-title" id="myModalLabel"><?php echo $labelobject; ?></h4>
	      				</div>
						<div class="modal-body">
							<div class="form-group">
								<label for="select" class="col-md-3 control-label"></label>
								<div class="col-md-8">
									<label id="erroraddrole" class="text-danger"></label>
								</div>
							</div>
							<br>
	      					<div class="form-group">
	        					<label for="select" class="col-lg-3 control-label"><?php echo $labelname[0]; ?> <red>*</red></label>
	        					<div class="col-lg-8">
									<input type="text" class="form-control" id="role" name="role" maxlength="40">
								</div>
							</div>
	     					<br>
	     					<div class="form-group">
								<label for="select" class="col-md-3 control-label"></label>
								<div class="col-md-8">
									<label id="erroraddroledesc" class="text-danger"></label>
								</div>
							</div>
							<br>
	      					<div class="form-group">
	        					<label for="select" class="col-lg-3 control-label"><?php echo $labelname[1]; ?> <red>*</red></label>
						        <div class="col-lg-8">
						        	<input type="text" class="form-control" id="roledesc" name="roledesc" maxlength="80">
						        </div>
	      					</div>
	      				</div>
	      				<div class="modal-footer">
							<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
							<input type="submit" class="btn btn-primary btn-sm" value="Permission" onclick="showloader();" />
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<!-- ----- -->
	<!-- pop-up -->
	<div class="modal fade bs-example-modal-md2" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-md">
    		<div class="modal-content">
    			<div class="modal-content">
    				<form method=post id=updaterecord action="<?php echo base_url(); ?><?php echo $cpagename; ?>/update/">
      					<div class="modal-header">
	        				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
	        				<h4 class="modal-title" id="myModalLabel">Edit <?php echo $labelobject; ?></h4>
	      				</div>
						<div class="modal-body">
							<div class="form-group">
								<label for="select" class="col-md-3 control-label"></label>
								<div class="col-md-8">
									<label id="erroreditrole" class="text-danger"></label>
								</div>
							</div>
							<br>
	      					<div class="form-group">
	        					<label for="select" class="col-lg-3 control-label"><?php echo $labelname[0]; ?> <red>*</red></label>
	        					<div class="col-lg-8">
									<input type="text" class="form-control" id="role1" name="role1" maxlength="40">
									<input type="hidden" class="form-control" id="roleid" name="roleid">
								</div>
							</div>
	     					<br>
	     					<div class="form-group">
								<label for="select" class="col-md-3 control-label"></label>
								<div class="col-md-8">
									<label id="erroreditroledesc" class="text-danger"></label>
								</div>
							</div>
							<br>
	      					<div class="form-group">
	        					<label for="select" class="col-lg-3 control-label"><?php echo $labelname[1]; ?> <red>*</red></label>
						        <div class="col-lg-8">
						        	<input type="text" class="form-control" id="roledesc1" name="roledesc1" maxlength="80">
						        </div>
	      					</div>
	      				</div>
	      				<div class="modal-footer">
							<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
							<input type="submit" class="btn btn-primary btn-sm" value="Save Changes" onclick="showloader();" />
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<!-- ----- -->
	<!-- pop-up -->
	<div class="modal fade bs-example-modal-md3" id="MyModal2" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-md">
    		<div class="modal-content">
    			<div class="modal-content">
      				<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        				<h4 class="modal-title" id="myModalLabel"><?php echo $labelname[2]; ?></h4>
      				</div>
      				<div class="modal-body" style="height:500px;overflow-y:scroll;">
      				<form method=post id="editpermission" action="<?php echo base_url(); ?><?php echo $cpagename; ?>/update_perm/">
      					<table class="table table-striped table-hover">
							<thead>
								<tr>
									<th><?php echo $labelname[3]; ?></th>
									<th>View</th>
									<th>Add</th>
									<th>Edit</th>
									<th>Delete</th>
									<th>Export</th>
									<th>Print</th>
									<th>Email</th>
								</tr>
								<tr>
									<th></th>
									<th><input type="checkbox" class="checkallview" id="checkallview">
										<input type="hidden" id="rolepermid" name="rolepermid" />
									<input type="hidden" class="form-control" id="roleupdate" name="roleupdate">
									</th>
									<th><input type="checkbox" class="checkalladd" id="checkalladd"></th>
									<th><input type="checkbox" class="checkalledit" id="checkalledit"></th>
									<th><input type="checkbox" class="checkalldelete" id="checkalldelete"></th>
									<th><input type="checkbox" class="checkallexport" id="checkallexport"></th>
									<th><input type="checkbox" class="checkallprint" id="checkallprint"></th>
									<th><input type="checkbox" class="checkallemail" id="checkallemail"></th>
								</tr>
        					</thead>
							<tbody>

							<?php
							$gname="";
							foreach ($permi as $perm):
							if($gname=="" || $gname!=$perm->sec_group_desc) {
							?>
								<tr>
									<th colspan="8" style="padding-top: 15px; padding-bottom: 5px;">

										<?php
										echo $perm->sec_group_desc;
										$gname=$perm->sec_group_desc;
										?>
									</th>
								</tr>
								<?php } ?>
								<tr>
									<td><?php echo $perm->sec_obj_desc; ?></td>
									<?php
									$objid=$perm->sec_obj_id;
									for ($i=1;$i<8;$i++) {
									$chkname=$objid."_".$i;
									echo "<td><input type=checkbox name=".$chkname." id=".$chkname." value=1 class=".$i."></td>";
									} ?>
								</tr>
								<?php

								endforeach;
								?>

							</tbody>
						</table>
						<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
						<input type="submit" class="btn btn-primary btn-sm" value="Save Changes" onclick="showloader();" />
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- ----- -->
	<!-- <div class="row text-center text-danger"><?php echo $message; ?> </div> -->
	<div class="row text-center <?php echo $message_type == 1? "text-success" : "text-danger"; ?>"><?php echo $message; ?></div>
	<div class="row">
		<table class="table table-striped table-hover">
        	<thead>
        		<tr>
					<th>No</th>
					<th><?php echo $labelname[0]; ?></th>
					<th><?php echo $labelname[1]; ?></th>
					<th><?php echo $labelname[2]; ?></th>
					<th>Edit</th>
					<th>Delete</th>
				</tr>
			</thead>
        	<tbody>
           		<?php
					$sno=$page;
					foreach ($records as $record):
				?>
						<tr>
				  			<td><?php echo $sno; ?></td>
				  			<td><?php echo $record->sec_role_name; ?></td>
				  			<td><?php echo $record->sec_role_desc; ?></td>
				  			<?php
				  			//split the array as comma seperated value
				  			$permdat=$rolepermi[$record->sec_role_id];
				  			?>
				  			<td>
				  				<?php
									if($editperm==1)
									{
								?>
										<a href="#" data-toggle="modal" data-target=".bs-example-modal-md3" class="modalpermedit" data-roleid="<?php echo $record->sec_role_id; ?>" data-roledata="<?php echo $permdat;  ?>"><span class="glyphicon glyphicon-file">&nbsp;</span></a>
								<?php
									}
									else
									{
										echo '<span class="glyphicon glyphicon-file">&nbsp;</span>';
									}
								?>
				  			</td>
          					<td>
          						<?php
									if($editperm==1)
									{
								?>
										<a href="#" data-toggle="modal" data-target=".bs-example-modal-md2" class="modaledit" data-roleid="<?php echo $record->sec_role_id; ?>" data-role="<?php echo $record->sec_role_name; ?>" data-roledesc="<?php echo $record->sec_role_desc; ?>" ><span class="glyphicon glyphicon-edit">&nbsp;</span></a>
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
										<a href="#" data-toggle="modal" class="modaldelete" data-roleid="<?php echo $record->sec_role_id; ?>"><span class="glyphicon glyphicon-trash">&nbsp;</span></a>
								<?php
									}
									else
									{
										echo '<span class="glyphicon glyphicon-edit">&nbsp;</span>';
									}
								?>
				  			</td>
						</tr>
				<?php
					$sno=$sno+1;
					endforeach;
					if($totalrows==0)
					{
						echo '<tr><td class="row text-center text-danger" colspan="6"> No Record Found</td></tr></tbody></table>';
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
			<div class="col-md-4 col-md-offset-1">
				<div class="form-group">
					<label for="search" class="col-sm-2 control-label" style="padding-top: 22px;">Show</label>
					<div class="col-sm-3" style="padding-top: 14px;">
						<select class="form-control" id="recordselect" name="recordselect">
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