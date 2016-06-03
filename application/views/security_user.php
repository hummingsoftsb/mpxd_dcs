<script>
	$(document).ready(function()
	{

        // DataTable
//        var table = $('#user_table').DataTable({
//        });

        var oTable = $('#user_table').dataTable({
            "order": [[ 0, "asc" ]],
            "columnDefs": [ {
                "targets"  : 'no-sort',
                "orderable": false
            }]
        });
        $('div.dataTables_filter input').attr('placeholder', 'Enter the text here');



        $("#modaladd").click(function ()
		{
			var empty="";
			$(".modal-body #name").val( empty );
			$(".modal-body #Email").val( empty );
			$(".modal-body #development").val( empty );
			$(".modal-body #username").val( empty );
			$(".modal-body #chkpass").prop('checked', false);
			$(".modal-body #chklock").prop('checked', false);
			$(".modal-body #lockcount").val( empty );
			$(".modal-body #errorname").html( empty );
			$(".modal-body #erroremail").html( empty );
			$(".modal-body #errorrole").html( empty );
			$(".modal-body #errordept").html( empty );
			$(".modal-body #erroruname").html( empty );
			$(".modal-body #errorpass").html( empty );
			$(".modal-body #errorlock").html( empty );
		});

		$('#addrecord').submit(function()
		{
			showloader();
			$.post($('#addrecord').attr('action'), $('#addrecord').serialize(), function( data )
			{
				if(data.st == 0)
				{
					hideloader();
		 			$('#errorname').html(data.msg);
		 			$('#erroremail').html(data.msg1);
					$('#errorrole').html(data.msg2);
		 			$('#errordept').html(data.msg3);
					$('#erroruname').html(data.msg4);
		 			$('#errorlock').html(data.msg5);
				}
				else if(data.st == 1)
				{
		  			location.href="<?php echo base_url(); ?><?php echo $cpagename; ?>";
				}

			}, 'json');
			return false;
   		});

   		$(document).on("click", ".modaledit", function ()
		{
			var userid = $(this).data('userid');
		    var name = $(this).data('userfullname');
			var email = $(this).data('email');
			var role = $(this).data('roleid');
			var dept = $(this).data('depart');
			var uname = $(this).data('username');
			var changepwd=$(this).data('changepwd');
			var lockcount = $(this).data('lockcount');
			var empty="";
			$(".modal-body #userid").val( userid );
			$(".modal-body #name1").val( name );
			$(".modal-body #Email1").val( email );
			$(".modal-body #role1").val( role );
			$(".modal-body #development1").val( dept );
			$(".modal-body #username1").val( uname );
			if(changepwd==1)
				$(".modal-body #chkpass1").prop('checked', true);
			else
				$(".modal-body #chkpass1").prop('checked', false);
			if(lockcount==0)
			{
				$(".modal-body #chklock1").prop('checked', false);
				$(".modal-body #lockcount1").val( empty );
			}
			else
			{
				$(".modal-body #chklock1").prop('checked', true);
				$(".modal-body #lockcount1").val( lockcount );
			}
			$(".modal-body #errorname1").html( empty );
			$(".modal-body #erroremail1").html( empty );
			$(".modal-body #errorrole1").html( empty );
			$(".modal-body #errordept1").html( empty );
			$(".modal-body #erroruname1").html( empty );
			$(".modal-body #errorpass1").html( empty );
			$(".modal-body #errorlock1").html( empty );
		});

		$('#updaterecord').submit(function()
		{
			$.post($('#updaterecord').attr('action'), $('#updaterecord').serialize(), function( data )
			{
				if(data.st == 0)
				{
					hideloader();
		 			$('#errorname1').html(data.msg);
		 			$('#erroremail1').html(data.msg1);
					$('#errorrole1').html(data.msg2);
		 			$('#errordept1').html(data.msg3);
					$('#erroruname1').html(data.msg4);
		 			$('#errorlock1').html(data.msg5);
				}
				else if(data.st == 1)
				{
		  			location.reload();
				}

			}, 'json');
			return false;
   		});

   		$(document).on("click", ".modalreset", function ()
		{
			var userid = $(this).data('userid');
			var empty="";
			$(".modal-body #resetid").val( userid );
			$(".modal-body #pass").val( empty );
			$(".modal-body #cpass").val( empty );
			$(".modal-body #errorpassword").html( empty );
			$(".modal-body #errorcpass").html( empty );

		});

		$('#resetrecord').submit(function()
		{
			$.post($('#resetrecord').attr('action'), $('#resetrecord').serialize(), function( data )
			{
				if(data.st == 0)
				{
					hideloader();
		 			$('#errorpassword').html(data.msg);
		 			$('#errorcpass').html(data.msg1);
				}
				else if(data.st == 1)
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
				var userid = $(this).data('userid');
				$.post( "<?php echo base_url(); ?><?php echo $cpagename; ?>/delete",{id:userid}, function( data ) {
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
			$.post( "<?php echo base_url(); ?><?php echo $cpagename; ?>/searchrecord",{search:search}, function( data ) {
				location.href="<?php echo base_url(); ?><?php echo $cpagename; ?>/search";
			});
			/*var search = $('#search').val();
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
			}*/
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
<div class="container" onload="hideloader();">
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
<!--		<label for="search" class="col-sm-1 control-label">Search</label>-->
    	<div class="col-sm-4">

		</div>
<!--		<input type="button" class="btn btn-primary btn-sm" id="recordsearch" name="recordsearch" value="Search" />-->
<!--		<a href="--><?php //echo base_url(); ?><!----><?php //echo $cpagename; ?><!--" class="btn btn-danger btn-sm">Clear</a>-->
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
						    	<div class="col-sm-5">
						    		<input type="text" class="form-control" id="name" name="name" placeholder="Muhammad Hilmi"  maxlength="80">
						        </div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-5">
						    		<label id="erroremail" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-3 control-label"><?php echo $labelname[1]; ?><red>*</red></label>
								<div class="col-sm-5">
									<input type="email" class="form-control" id="Email" name="Email" placeholder="Muhammadhilmi@mrt.com" maxlength="120">
								</div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-5">
						    		<label id="errorrole" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-3 control-label"><?php echo $labelname[2]; ?><red>*</red></label>
								<div class="col-sm-5">
									<select class="form-control" id="role" name="role">
										<?php
											foreach ($roles as $role):
										?>
												<option value="<?php echo $role->sec_role_id; ?>"><?php echo $role->sec_role_name; ?></option>
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
						    	<div class="col-sm-5">
						    		<label id="errordept" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-3 control-label"><?php echo $labelname[3]; ?><red>*</red></label>
								<div class="col-sm-5">
									<input type="text" class="form-control" id="development" name="development" placeholder="Strategic and Planning" maxlength="80">
								</div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-5">
						    		<label id="erroruname" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-3 control-label"><?php echo $labelname[4]; ?><red>*</red></label>
								<div class="col-sm-5">
									<input type="text" class="form-control" id="username" name="username" placeholder="hilmi"  maxlength="40">
								</div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-5">
						    		<label id="errorpass" class="text-danger"></label>
						        </div>
							</div>
						</div>
                        <?php
                        if($delperm==1 && $ldap!=1) // added by agaile on 03/06/2016 if the user is logged in via ldap disable the change pass on next login
                        {
                        ?>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-3 control-label"><?php echo $labelname[5]; ?></label>
								<div class="col-sm-5">
									<input type="checkbox" id="chkpass" name="chkpass" /> Next Login
								</div>
							</div>
						</div>
                        <?php
                        }
                        ?>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-6">
						    		<label id="errorlock" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-3 control-label"><?php echo $labelname[6]; ?></label>
								<div class="col-sm-5">
									<input type="checkbox" id="chklock" name="chklock" /> Lock account after maximum number of wrong attempts.
								</div>
								<div class="col-sm-2">
									<input type="text" class="form-control" id="lockcount" name="lockcount" placeholder="5"  maxlength="3">
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
						<input type="submit" class="btn btn-primary btn-sm" value="Add User" onclick="showloader();" />
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
						    	<div class="col-sm-5">
						    		<input type="hidden" class="form-control" id="userid" name="userid">
						    		<input type="name" class="form-control" id="name1" name="name1" placeholder="Muhammad Hilmi" maxlength="80">
						        </div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-5">
						    		<label id="erroremail1" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-3 control-label"><?php echo $labelname[1]; ?><red>*</red></label>
								<div class="col-sm-5">
									<input type="email" class="form-control" id="Email1" name="Email1" placeholder="Muhammadhilmi@mrt.com" maxlength="120">
								</div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-5">
						    		<label id="errorrole1" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-3 control-label"><?php echo $labelname[2]; ?><red>*</red></label>
								<div class="col-sm-5">
									<select class="form-control" id="role1" name="role1">
										<?php
											foreach ($roles as $role):
										?>
												<option value="<?php echo $role->sec_role_id; ?>"><?php echo $role->sec_role_name; ?></option>
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
						    	<div class="col-sm-5">
						    		<label id="errordept1" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-3 control-label"><?php echo $labelname[3]; ?><red>*</red></label>
								<div class="col-sm-5">
									<input type="role" class="form-control" id="development1" name="development1" placeholder="Strategic and Planning" maxlength="80">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-5">
						    		<label id="erroruname1" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-3 control-label"><?php echo $labelname[4]; ?><red>*</red></label>
								<div class="col-sm-5">
									<input type="text" class="form-control" id="username1" name="username1" placeholder="hilmi" maxlength="40">
								</div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-5">
						    		<label id="errorpass1" class="text-danger"></label>
						        </div>
							</div>
						</div>
                        <?php
                        if($delperm==1 && $ldap!=1) // added by agaile on 03/06/2016 if the user is logged in via ldap disable the change pass on next login
                        {
                        ?>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-3 control-label"><?php echo $labelname[5]; ?></label>
								<div class="col-sm-5">
									<input type="checkbox" id="chkpass1" name="chkpass1" />Next Login
								</div>
							</div>
						</div>
                        <?php
                        }
                        ?>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-6">
						    		<label id="errorlock1" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-3 control-label"><?php echo $labelname[6]; ?></label>
								<div class="col-sm-5">
									<input type="checkbox" id="chklock1" name="chklock1" />Lock account after maximum number of wrong attemps.
								</div>
								<div class="col-sm-2">
									<input type="text" class="form-control" id="lockcount1" name="lockcount1" placeholder="5"  maxlength="3">
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
						<input type="submit" class="btn btn-primary btn-sm" value="Save Changes" onclick="showloader();" />
					</div>
				</form>
			</div>
		</div>
	</div>
	<!--close pop-up-->
	<!-- pop-up -->
	<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog">
			<div class="modal-content">
				<form method=post id=resetrecord action="<?php echo base_url(); ?><?php echo $cpagename; ?>/reset/">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h4 class="modal-title" id="myModalLabel">Reset Password</h4>
					</div>
					<div class="modal-body">
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-5">
						    		<label id="errorpassword" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-4 control-label"><?php echo $labelname[7]; ?><red>*</red></label>
						    	<div class="col-sm-5">
						    		<input type="hidden" class="form-control" id="resetid" name="resetid" >
						    		<input type="password" class="form-control" id="pass" name="pass" maxlength="20">
						        </div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-5">
						    		<label id="errorcpass" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-4 control-label"><?php echo $labelname[8]; ?><red>*</red></label>
								<div class="col-sm-5">
									<input type="password" class="form-control" id="cpass" name="cpass" maxlength="20">
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
						<input type="submit" class="btn btn-primary btn-sm" value="Reset" onclick="showloader();" />
					</div>
				</form>
			</div>
		</div>
	</div>
	<!--close pop-up-->
	<!-- <div class="row text-center text-danger"><?php echo $message; ?> </div> -->
	<div class="row text-center <?php echo $message_type == 1? "text-success" : "text-danger"; ?>"><?php echo $message; ?></div>
    <div>&nbsp&nbsp&nbsp&nbsp&nbsp</div>
<!--    disable reset passs here : agaile-->
	<div class="row">
		<table class="table table-striped table-hover" id="user_table">
	        <thead>
    			<tr>
      				<th>No</th>
			        <th><?php echo $labelname[0]; ?></th>
			        <th><?php echo $labelname[2]; ?></th>
			        <th><?php echo $labelname[1]; ?></th>
			        <th><?php echo $labelname[4]; ?></th>
			        <th><?php echo $labelname[5]; ?></th>
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
				  			<td><?php echo $record->user_full_name; ?></td>
				  			<td><?php echo $record->sec_role_name; ?></td>
				  			<td><?php echo $record->email_id; ?></td>
				  			<td><?php echo $record->user_name; ?></td>
				  			<td>
				  				<?php
									if($editperm==1 && $ldap!=1) // added new condition $ldap ==1 by agaile on 03/06/2016 if the user is logged in via ldap disable the reset pass
									{
								?>
				  						<a href="#" data-toggle="modal" data-target="#myModal2" class="modalreset" data-userid="<?php echo $record->user_id; ?>" >Reset</a>
                                <?php
									}
                                else{
                                    echo 'Reset';
                                }
								?>
				  			</td>
		          			<td>
		          				<?php
									if($editperm==1 )
									{
								?>
										<a href="#" data-toggle="modal" data-target="#myModal1" class="modaledit" data-roleid="<?php echo $record->sec_role_id; ?>" data-userid="<?php echo $record->user_id; ?>" data-userfullname="<?php echo $record->user_full_name; ?>" data-email="<?php echo $record->email_id; ?>" data-depart="<?php echo $record->dept_name; ?>" data-username="<?php echo $record->user_name; ?>" data-changepwd="<?php echo $record->change_pwd_opt; ?>" data-lockcount="<?php echo $record->lock_by_pwd; ?>"  ><span class="glyphicon glyphicon-edit">&nbsp;</span></a>
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
									if($delperm==1 && $ldap!=1) // added new condition $ldap ==1 by agaile on 03/06/2016 if the user is logged in via ldap disable the delete user
									{
								?>
		          						<a href="#" data-toggle="modal" class="modaldelete" data-userid="<?php echo $record->user_id; ?>"><span class="glyphicon glyphicon-trash">&nbsp;</span></a>
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
						echo '<tr><td class="row text-center text-danger" colspan="8"> No Record Found</td></tr></tbody></table>';
					}
					else
					{
				?>

  			</tbody>
		</table>
	</div>
	<div class="row">
		<div class="col-md-12">
<!--			<div class="col-md-4">-->
<!--				<ul class="pagination">-->
<!--                	--><?php //echo $this->pagination->create_links(); ?>
<!--				</ul>-->
<!--			</div>-->
<!--			<div class="col-md-4 col-md-offset-1">-->
<!--				<div class="form-group">-->
<!--					<label for="search" class="col-sm-2 control-label" style="padding-top: 22px;">Show</label>-->
<!--					<div class="col-sm-3" style="padding-top: 14px;">-->
<!--						<select class="form-control" id="recordselect" name="recordselect">-->
<!--						</select>-->
<!--					</div>-->
<!--				</div>-->
<!--			</div>-->
<!--			--><?php
//				// Display the number of records in a page
//				$end=$mpage+$page-1;
//				if($totalrows<$end) $end=$totalrows;
//			?>
<!--			<div class="col-md-3" style="padding-top: 22px;"> Showing --><?php //echo $page; ?><!-- to --><?php //echo $end; ?><!-- of --><?php //echo $totalrows; ?><!-- rows  </div>-->
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