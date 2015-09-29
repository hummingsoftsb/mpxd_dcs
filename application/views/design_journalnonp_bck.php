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
	$(document).ready(function()
	{
		var validatorcount=2;
		var dataentrycount=2;
		var validatorcount1=2;
		var dataentrycount1=2;
		$("#modaladd").click(function ()
		{
			var empty="";
			$(".modal-body #errorprojname").html( empty );
			$(".modal-body #errorjournalname").html( empty );
			$(".modal-body #erroruser").html( empty );
			/*$(".modal-body #errorfrequency").html( empty );
			$(".modal-body #errorstart").html( empty );
			$(".modal-body #errorend").html( empty );*/
			$(".modal-body #errordataattb").html( empty );
			$(".modal-body #errorjournalproperty").html( empty );
			$('.modal-body input:checkbox').removeAttr('checked');
			$('.modal-body input:text').val(empty);
			$("#validator").find("tr:gt(2)").remove();
			$("#dataentry").find("tr:gt(2)").remove();
			validatorcount=2;
			dataentrycount=2;
		});

		$('#addrecord').submit(function()
		{
			$.post($('#addrecord').attr('action'), $('#addrecord').serialize(), function( data )
			{
				if(data.st == 0)
				{
		 			$(".modal-body #errorprojname").html( data.msg );
					$(".modal-body #errorjournalname").html( data.msg1 );
					$(".modal-body #erroruser").html( data.msg2 );
					/*$(".modal-body #errorfrequency").html( data.msg3 );
					$(".modal-body #errorstart").html( data.msg4 );
					$(".modal-body #errorend").html( data.msg5 );*/
					$(".modal-body #errordataattb").html( data.msg6 );
					$(".modal-body #errordata").html( data.msg7 );
					/*if(data.msg8!='')
						$(".modal-body #errorend").html( data.msg8 );*/
					$(".modal-body #errorjournalproperty").html( data.msg9);
						
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

			var empty="";

			$(".modal-body #errorprojname1").html( empty );
			$(".modal-body #errorjournalname1").html( empty );
			$(".modal-body #erroruser1").html( empty );
			/*$(".modal-body #errorfrequency1").html( empty );
			$(".modal-body #errorstart1").html( empty );
			$(".modal-body #errorend1").html( empty );*/
			$(".modal-body #errordataattb1").html( empty );
			$(".modal-body #errorjournalproperty1").html( empty );
			$('.modal-body input:checkbox').removeAttr('checked');
			$('.modal-body input:text').val(empty);
			$('.modal-body input:checkbox').removeAttr('checked');
			$('.modal-body input:text').val(empty);
			$("#validator1").find("tr:gt(1)").remove();
			$("#dataentry1").find("tr:gt(1)").remove();
			$("#validatorid1").val(empty);
			$("#dataentryid1").val(empty);
			validatorcount1=1;
			dataentrycount1=1;
			var editid = $(this).data('editid');
			var projno = $(this).data('projno');
			var journalname = $(this).data('journalname');
			var journalproperty = $(this).data('journalproperty');
			var user = $(this).data('user');
			/*var startdate = $(this).data('startdate');
			var enddate = $(this).data('enddate');*/
			var frequency = $(this).data('frequency');
			var validatorval=$(this).data('validatorvalue');
			var dataentryval=$(this).data('dataentryvalue');
			var dataattbval=$(this).data('dataattbvalue');

			$('.modal-body #editjournalno').val(editid);
			$('.modal-body #projectname1').val(projno);
			$('.modal-body #journalname1').val(journalname);
			$('.modal-body #journalproperty1').val(journalproperty);
			$('.modal-body #user1').val(user);
			/*$('.modal-body #startdate1').val(startdate);
			$('.modal-body #enddate1').val(enddate);
			$('.modal-body #frequency1').val(frequency);*/

			var validatorval1 = validatorval.split(',777,');
			var dataentryval1 = dataentryval.split(',777,');
			var dataattbval1 = dataattbval.split(',777,');

			var userskey=<?php echo '[' . $userkey . ']'; ?>;
			var usersvalue=<?php echo '[' . $uservalue . ']'; ?>;


			for (var j = 0; j < validatorval1.length; j++)
			{
				if(validatorval1[j]!="")
				{
					var validatorval2 = validatorval1[j].split(',');

					var validatorid=$("#validatorid1").val();

					var content="<tr><td>";
					content += '<select class="dropdown-toggle" id="1validateuser'+validatorcount1+'" name="1validateuser'+validatorcount1+'">';

					for(i=0;i<userskey.length;i++)
					{
						if(userskey[i]==validatorval2[0])
						{
							content +='<option value="'+userskey[i]+'" selected="selected">'+usersvalue[i]+'</option>';
						}
						else
						{
							content +='<option value="'+userskey[i]+'">'+usersvalue[i]+'</option>';
						}
					}
					content +='</select></td><td>';
					content +='<select class="dropdown-toggle" id="1level'+validatorcount1+'" name="1level'+validatorcount1+'">';
					if(validatorval2[1]==1)
					{
						content +='<option value="1"><?php echo $labelname[13]; ?> 1</option>';
					}
					else if(validatorval2[1]==2)
					{
						content +='<option value="2"><?php echo $labelname[13]; ?> 2</option>';
					}
					else
					{
						content +='<option value="3"><?php echo $labelname[13]; ?> 3</option>';
					}
					content +='</select></td>';
					if(validatorval2[1]==1)
					{
						content +='<td><span class="glyphicon glyphicon-trash">&nbsp;</span></td></tr>';
					}
					else
					{
						content +='<td> <a href="javascript:void(0)" class="removeValidator1" data-id="'+validatorcount1+'"><span class="glyphicon glyphicon-trash">&nbsp;</span></a></td></tr>';
					}
					$("#validator1").append(content);
					if(validatorid=="")
						$("#validatorid1").val( validatorcount1 );
					else
						$("#validatorid1").val( validatorid+','+validatorcount1 );
					validatorcount1++;

					var index=userskey.indexOf(validatorval2[0]);
					userskey.splice(index,1);
					usersvalue.splice(index,1);
				}
        	}

			var userskey=<?php echo '[' . $userkey . ']'; ?>;
			var usersvalue=<?php echo '[' . $uservalue . ']'; ?>;


			for (var j = 0; j < dataentryval1.length; j++)
			{
				if(dataentryval1[j]!="")
				{
					var dataentryval2 = dataentryval1[j].split(',');

					var dataentryid=$("#dataentryid1").val();

					var content="<tr><td>";
					content += '<select class="dropdown-toggle" id="1dataentryuser'+dataentrycount1+'" name="1dataentryuser'+dataentrycount1+'">';

					for(i=0;i<userskey.length;i++)
					{
						if(userskey[i]==dataentryval2[0])
						{
							content +='<option value="'+userskey[i]+'" selected="selected">'+usersvalue[i]+'</option>';
						}
						else
						{
							content +='<option value="'+userskey[i]+'">'+usersvalue[i]+'</option>';
						}
					}
					content +='</select></td><td>';
					content +='<input type ="radio" id="dataentryowner1" name="dataentryowner1" value="'+dataentrycount1+'" ';
					if(dataentryval2[1]==1)
						content +=' checked="true" ';
					content +='></td>';
					if(dataentryval2[1]==1)
						content +='<td><span class="glyphicon glyphicon-trash">&nbsp;</span></td></tr>';
					else
						content +='<td> <a href="javascript:void(0)" class="removeDataEntry1" data-id="'+dataentrycount1+'"><span class="glyphicon glyphicon-trash">&nbsp;</span></a></td></tr>';
					$("#dataentry1").append(content);
					if(dataentryid=="")
						$("#dataentryid1").val( dataentrycount1 );
					else
						$("#dataentryid1").val( dataentryid+','+dataentrycount1 );
					dataentrycount1++;

					var index=userskey.indexOf(dataentryval2[0]);
					userskey.splice(index,1);
					usersvalue.splice(index,1);
				}
        	}

			var dataattbcount=$("#dataattbcount").val();
			for (var j = 0; j < dataattbval1.length; j++)
			{
				if(dataattbval1[j]!="")
				{
					var dataattbval2 = dataattbval1[j].split(',');
					var ordercount=$('#1ordercount').val();
					for(var i=1;i<=dataattbcount;i++)
					{
						if($("#1dataattbid"+i).val()==dataattbval2[0])
						{
							$("#1dataattb"+i).prop('checked', true);
							$("#1order"+i).val(parseInt(dataattbval2[1]));
							if(ordercount<$('#1order'+i).val())
							{
								ordercount=$('#1order'+i).val();
							}
							ordercount++;
						}
					}
					$('#1ordercount').val(ordercount);
				}
			}

		});

		$('#updaterecord').submit(function()
		{
			$.post($('#updaterecord').attr('action'), $('#updaterecord').serialize(), function( data )
			{
				if(data.st == 0)
				{
		 			$(".modal-body #errorprojname1").html( data.msg );
					$(".modal-body #errorjournalname1").html( data.msg1 );
					$(".modal-body #erroruser1").html( data.msg2 );
					//$(".modal-body #errorfrequency1").html( data.msg3 );
					$(".modal-body #errordataattb1").html( data.msg4 );
					$(".modal-body #errordata1").html( data.msg5 );
					$(".modal-body #errorjournalproperty1").html( data.msg6 );
					
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
			$.post( "<?php echo base_url(); ?><?php echo $cpagename; ?>/searchrecord",{search:search}, function( data ) {
				location.href="<?php echo base_url(); ?><?php echo $cpagename; ?>/search";
			});
	    });

		/*$( "#startdate" ).datepicker(
		{
			showOn: "button",
			buttonImage: "/img/calendar.gif",
			buttonImageOnly: true,
			buttonText: "Select date",
			dateFormat: "dd-mm-yy"

		});

		$( "#enddate" ).datepicker(
		{
			showOn: "button",
			buttonImage: "/img/calendar.gif",
			buttonImageOnly: true,
			buttonText: "Select date",
			dateFormat: "dd-mm-yy"

		});*/

		$(".addValidator").click(function()
		{
			var validatorid=$("#validatorid").val();
			var userskey=<?php echo '[' . $userkey . ']'; ?>;
			var usersvalue=<?php echo '[' . $uservalue . ']'; ?>;
			var count=validatorid.split(',');
			for(i=0;i<count.length;i++)
			{
				var index=userskey.indexOf($("#validateuser"+count[i]).val());
				userskey.splice(index,1);
				usersvalue.splice(index,1);
			}
			if(count.length<3)
			{
				var content="<tr><td>";
				content += '<select class="dropdown-toggle" id="validateuser'+validatorcount+'" name="validateuser'+validatorcount+'">';

				for(i=0;i<userskey.length;i++)
				{

					content +='<option value="'+userskey[i]+'">'+usersvalue[i]+'</option>';
				}
				content +='</select></td><td>';
				content +='<select class="dropdown-toggle" id="level'+validatorcount+'" name="level'+validatorcount+'">';
				if(validatorid==1 || $("#level"+count[1]).val()==3)
				{
					content +='<option value="2"><?php echo $labelname[13]; ?> 2</option>';
				}
				else
				{
					content +='<option value="3"><?php echo $labelname[13]; ?> 3</option>';
				}
				content +='</select></td>';
				content +='<td> <a href="javascript:void(0)" class="removeValidator" data-id="'+validatorcount+'"><span class="glyphicon glyphicon-trash">&nbsp;</span></a></td></tr>';
				$("#validator").append(content);
				$("#validatorid").val( validatorid+','+validatorcount );
				validatorcount++;
			}
		});

		$("#validator").on('click','.removeValidator',function()
		{
			var id=$(this).data('id');
			var validatorid=$("#validatorid").val();
			validatorid=validatorid.replace(","+id,"");
			$("#validatorid").val(validatorid);
			$(this).parent().parent().remove();

		});

		$(".addDataEntry").click(function()
		{
			var dataentryid=$("#dataentryid").val();
			var userskey=<?php echo '[' . $userkey . ']'; ?>;
			var usersvalue=<?php echo '[' . $uservalue . ']'; ?>;

			var count=dataentryid.split(',');
			for(i=0;i<count.length;i++)
			{
				var index=userskey.indexOf($("#dataentryuser"+count[i]).val());
				userskey.splice(index,1);
				usersvalue.splice(index,1);
			}
			if(userskey.length!=0)
			{
				var content="<tr><td>";
				content += '<select class="dropdown-toggle" id="dataentryuser'+dataentrycount+'" name="dataentryuser'+dataentrycount+'">';

				for(i=0;i<userskey.length;i++)
				{
					content +='<option value="'+userskey[i]+'">'+usersvalue[i]+'</option>';
				}
				content +='</select></td><td>';
				content +='<input type ="radio" id="dataentryowner" name="dataentryowner" value="'+dataentrycount+'"></td>';
				content +='<td> <a href="javascript:void(0)" class="removeDataEntry" data-id="'+dataentrycount+'"><span class="glyphicon glyphicon-trash">&nbsp;</span></a></td></tr>';
				$("#dataentry").append(content);
				$("#dataentryid").val( dataentryid+','+dataentrycount );
				dataentrycount++;
			}

		});

		$("#dataentry").on('click','.removeDataEntry',function()
		{
			var id=$(this).data('id');
			var dataentryid=$("#dataentryid").val();
			dataentryid=dataentryid.replace(","+id,"");
			$("#dataentryid").val(dataentryid);
			$(this).parent().parent().remove();
		});

		$(".addValidator1").click(function()
		{
			var validatorid=$("#validatorid1").val();
			var userskey=<?php echo '[' . $userkey . ']'; ?>;
			var usersvalue=<?php echo '[' . $uservalue . ']'; ?>;
			var count=validatorid.split(',');
			for(i=0;i<count.length;i++)
			{
				var index=userskey.indexOf($("#1validateuser"+count[i]).val());
				userskey.splice(index,1);
				usersvalue.splice(index,1);
			}
			if(count.length<3)
			{
				var content="<tr><td>";
				content += '<select class="dropdown-toggle" id="1validateuser'+validatorcount1+'" name="1validateuser'+validatorcount1+'">';

				for(i=0;i<userskey.length;i++)
				{
					content +='<option value="'+userskey[i]+'">'+usersvalue[i]+'</option>';
				}
				content +='</select></td><td>';
				content +='<select class="dropdown-toggle" id="1level'+validatorcount1+'" name="1level'+validatorcount1+'">';
				if(validatorid==1 || $("#1level"+count[1]).val()==3)
				{
					content +='<option value="2"><?php echo $labelname[13]; ?> 2</option>';
				}
				else
				{
					content +='<option value="3"><?php echo $labelname[13]; ?> 3</option>';
				}
				content +='</select></td>';
				content +='<td> <a href="javascript:void(0)" class="removeValidator1" data-id="'+validatorcount1+'"><span class="glyphicon glyphicon-trash">&nbsp;</span></a></td></tr>';
				$("#validator1").append(content);
				$("#validatorid1").val( validatorid+','+validatorcount1 );
				validatorcount1++;
			}
		});

		$("#validator1").on('click','.removeValidator1',function()
		{
			var id=$(this).data('id');
			var validatorid=$("#validatorid1").val();
			validatorid=validatorid.replace(","+id,"");
			$("#validatorid1").val(validatorid);
			$(this).parent().parent().remove();

		});

		$(".addDataEntry1").click(function()
		{
			var dataentryid=$("#dataentryid1").val();
			var userskey=<?php echo '[' . $userkey . ']'; ?>;
			var usersvalue=<?php echo '[' . $uservalue . ']'; ?>;

			var count=dataentryid.split(',');
			for(i=0;i<count.length;i++)
			{
				var index=userskey.indexOf($("#1dataentryuser"+count[i]).val());
				userskey.splice(index,1);
				usersvalue.splice(index,1);
			}
			if(userskey.length!=0)
			{
				var content="<tr><td>";
				content += '<select class="dropdown-toggle" id="1dataentryuser'+dataentrycount1+'" name="1dataentryuser'+dataentrycount1+'">';

				for(i=0;i<userskey.length;i++)
				{
					content +='<option value="'+userskey[i]+'">'+usersvalue[i]+'</option>';
				}
				content +='</select></td><td>';
				content +='<input type ="radio" id="dataentryowner1" name="dataentryowner1" value="'+dataentrycount1+'"></td>';
				content +='<td> <a href="javascript:void(0)" class="removeDataEntry1" data-id="'+dataentrycount1+'"><span class="glyphicon glyphicon-trash">&nbsp;</span></a></td></tr>';
				$("#dataentry1").append(content);
				$("#dataentryid1").val( dataentryid+','+dataentrycount1 );
				dataentrycount1++;
			}
		});

		$("#dataentry1").on('click','.removeDataEntry1',function()
		{
			var id=$(this).data('id');
			var dataentryid=$("#dataentryid1").val();
			dataentryid=dataentryid.replace(","+id,"");
			$("#dataentryid1").val(dataentryid);
			$(this).parent().parent().remove();
		});
		$("#MyModal input:checkbox").change(function()
		{
			var id=$(this).attr('id');
			id=id.substring(8);
			var ordercount=$("#ordercount").val();
			if($(this).prop('checked')==true)
			{
				$('#order'+id).val(ordercount);
				ordercount++;
			}
			else
			{
				$('#order'+id).val('');
			}
			$("#ordercount").val(ordercount);
		});
		$("#MyModal1 input:checkbox").change(function()
		{
			var id=$(this).attr('id');
			id=id.substring(9);
			var ordercount=$("#1ordercount").val();
			if($(this).prop('checked')==true)
			{
				$('#1order'+id).val(ordercount);
				ordercount++;
			}
			else
			{
				$('#1order'+id).val('');
			}
			$("#1ordercount").val(ordercount);
		});
	});

</script>
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
			<button type="button" class="btn btn-success btn-sm pull-right" id="modaladd"  data-toggle="modal" data-target="#MyModal" <?php if($addperm==0) echo 'disabled="true"'; ?>>Add New</button>
		</div>
	</div>
	<!-- pop-up -->
	<div class="modal fade" id="MyModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
						<h4 class="modal-title" id="myModalLabel"><?php echo $labelobject; ?></h4>
					</div>
					<div class="modal-body">
						<form method=post id=addrecord action="<?php echo base_url(); ?><?php echo $cpagename; ?>/add/">
							<div class="form-group">
								<label for="select" class="col-lg-2 control-label"></label>
								<div class="col-lg-10">
									<label id="errorprojname" class="text-danger"></label>
								</div>
							</div>

							<div class="form-group">
								<label for="select" class="col-lg-2 control-label"><?php echo $labelname[0]; ?> <red>*</red></label>
								<div class="col-lg-10">
									<select class="dropdown-toggle" id="projectname" name="projectname">
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

							<div class="form-group">
								<label for="select" class="col-lg-2 control-label"></label>
								<div class="col-lg-10">
									<label id="errorjournalname" class="text-danger"></label>
								</div>
							</div>

							<div class="form-group">
								<label for="select" class="col-lg-2 control-label"><?php echo $labelname[1]; ?> <red>*</red></label>
								<div class="col-lg-10">
									<input type="text" class="form-control" id="journalname" name="journalname" placeholder="LR Beam Structure" maxlength="120">
								</div>
							</div>

							<div class="form-group">
								<label for="select" class="col-lg-2 control-label"></label>
								<div class="col-lg-10">
									<label id="errorjournalproperty" class="text-danger"></label>
								</div>
							</div>

							<div class="form-group">
								<label for="select" class="col-lg-2 control-label"><?php echo $labelname[2]; ?></label>
								<div class="col-lg-10">
									<input type="text" class="form-control" id="journalproperty" name="journalproperty" maxlength="120">
								</div>
							</div>

							<div class="form-group">
								<label for="select" class="col-lg-2 control-label"></label>
								<div class="col-lg-10">
									<label id="erroruser" class="text-danger"></label>
								</div>
							</div>

							<div class="form-group">
								<label for="select" class="col-lg-2 control-label"><?php echo $labelname[3]; ?> <red>*</red></label>
								<div class="col-lg-10">
									<select class="dropdown-toggle" id="user" name="user">
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

							<!--<div class="form-group">
								<label for="select" class="col-lg-2 control-label"></label>
								<div class="col-lg-10">
									<label id="errorfrequency" class="text-danger"></label>
								</div>
							</div>

							<div class="form-group">
								<label for="select" class="col-lg-2 control-label"><?php echo $labelname[4]; ?> <red>*</red></label>
								<div class="col-lg-10">
									<select class="dropdown-toggle" id="frequency" name="frequency">
										<?php
											foreach ($frequencys as $frequency):
										?>
												<option value="<?php echo $frequency->frequency_no; ?>"><?php echo $frequency->frequency_name; ?></option>
										<?php
											endforeach;
										?>
									</select>
								</div>
							</div>

							<div class="form-group">
								<label for="select" class="col-lg-2 control-label"></label>
								<div class="col-lg-10">
									<label id="errorstart" class="text-danger"></label>
								</div>
							</div>

							<div class="form-group">
								<label for="select" class="col-lg-2 control-label">Start Date <red>*</red></label>
								<div class="col-lg-10">
									<input class="input-small" type="text" id="startdate" name="startdate" placeholder="12/06/2015">
								</div>
							</div>

							<div class="form-group">
								<label for="select" class="col-lg-2 control-label"></label>
								<div class="col-lg-10">
									<label id="errorend" class="text-danger"></label>
								</div>
							</div>

							<div class="form-group">
								<label for="select" class="col-lg-2 control-label">End Date <red>*</red></label>
								<div class="col-lg-10">
									<input class="input-small" type="text" id="enddate" name="enddate" placeholder="23/09/2015">
								</div>
							</div>-->
							<div class="form-group">
        						<label for="select" class="col-lg-2 control-label"><?php echo $labelname[5]; ?></label>
        						<div class="col-lg-10">
									<div class="table">
										<table class="table table-striped table-hover" id="validator">
											<tr>
												<td colspan="2" align="center"><input type="hidden" name="validatorid" id="validatorid" value="1" /></td>
												<td align="right"><a href="javascript:void(0)" class="addValidator" >Add</a></td>
											</tr>
          									<tr>
												<th><?php echo $labelname[12]; ?></th>
												<th><?php echo $labelname[13]; ?></th>
												<th>Delete</th>
											</tr>
											<tr>
												<td>
													<select class="dropdown-toggle" id="validateuser1" name="validateuser1">
														<?php
															foreach ($users as $user):
														?>
																<option value="<?php echo $user->user_id; ?>"><?php echo $user->user_full_name; ?></option>
														<?php
															endforeach;
														?>
													</select>
												</td>
												<td>
													<select class="dropdown-toggle" id="level1" name="level1">
														<option value="1"><?php echo $labelname[13]; ?> 1</option>
													</select>
												</td>
												<td><span class="glyphicon glyphicon-trash">&nbsp;</span></td>
											</tr>
										</table>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label for="select" class="col-lg-2 control-label"><?php echo $labelname[6]; ?> <red>*</red></label>
								<div class="col-lg-10">
									<div class="table">
										<div class="row text-center text-danger" id="errordata"> </div>
										<table class="table table-striped table-hover" id="dataentry">
											<tr>
												<td colspan="2" align="center"><input type="hidden" name="dataentryid" id="dataentryid" value="1"/></td>
												<td align="right"><a href="javascript:void(0)" class="addDataEntry" >Add</a></td>
											</tr>
											<tr>
												<th><?php echo $labelname[12]; ?></th>
												<th><?php echo $labelname[14]; ?></th>
												<th>Delete</th>
											</tr>
											<tr>
												<td>
													<select class="dropdown-toggle" id="dataentryuser1" name="dataentryuser1">
														<?php
															foreach ($users as $user):
														?>
																<option value="<?php echo $user->user_id; ?>"><?php echo $user->user_full_name; ?></option>
														<?php
															endforeach;
														?>
													</select>
												</td>
												<td><input type ="radio" id="dataentryowner" name="dataentryowner" checked="true" value="1"></td>
												<td><span class="glyphicon glyphicon-trash">&nbsp;</span></td>
											</tr>
										</table>
										<fieldset>
											<legend><?php echo $labelname[7]; ?></legend>
											<div class="table">
												<table class="table table-striped table-hover" id="dataentry">
													<tr>
														<td colspan="8" align="center"><label id="errordataattb" class="text-danger"></label></td>
													</tr>
													<tr>
														<th><?php echo $labelname[8]; ?></th>
														<th><?php echo $labelname[9]; ?></th>
														<th><?php echo $labelname[10]; ?>e</th>
														<th><?php echo $labelname[11]; ?><input type="hidden" name="ordercount" id="ordercount" value="1" /></th>
													</tr>
													<?php
														$dataattbcount=1;
														foreach ($dataattbs as $dataattb):
														$id=$dataattb->data_attb_id;
													?>
															<tr>
																<td><input type="hidden" name="dataattbid<?php echo $dataattbcount; ?>" id="dataattbid<?php echo $dataattbcount; ?>" value="<?php echo $dataattb->data_attb_id; ?>"/>
																<input type="checkbox" id="dataattb<?php echo $dataattbcount; ?>" name="dataattb<?php echo $dataattbcount; ?>" /></td>
																<td><?php echo $dataattb->data_attb_label; ?></td>
																<td><?php echo $dataattb->data_attb_type_desc; ?></td>
																<td>
																	<input id="order<?php echo $dataattbcount; ?>" type="text" maxlength="3" name="order<?php echo $dataattbcount; ?>" style="width:40px">
																</td>
															</tr>
													<?php
														$dataattbcount++;
														endforeach;
													?>
													<input type="hidden" name="dataattbcount" id="dataattbcount" value="<?php echo $dataattbcount-1; ?>" />
												</table>
											</div>
										</fieldset>
									</div>
								</div>
							</div>
						</div>
						<div class="modal-footer" style="text-align:center;border:0;">
							<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
							<input type="submit" class="btn btn-primary btn-sm" value="Add Journal" />
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<!--close-->
	<!-- pop-up -->
	<div class="modal fade" id="MyModal1" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
						<h4 class="modal-title" id="myModalLabel">Edit <?php echo $labelobject; ?></h4>
					</div>
					<div class="modal-body">
						<form method=post id=updaterecord action="<?php echo base_url(); ?><?php echo $cpagename; ?>/update/">
							<div class="form-group">
								<label for="select" class="col-lg-2 control-label"></label>
								<div class="col-lg-10">
									<label id="errorprojname1" class="text-danger"></label>
									<input type="hidden" name="editjournalno" id="editjournalno"  />
								</div>
							</div>

							<div class="form-group">
								<label for="select" class="col-lg-2 control-label"><?php echo $labelname[0]; ?><red>*</red></label>
								<div class="col-lg-10">
									<select class="dropdown-toggle" id="projectname1" name="projectname1">
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

							<div class="form-group">
								<label for="select" class="col-lg-2 control-label"></label>
								<div class="col-lg-10">
									<label id="errorjournalname1" class="text-danger"></label>
								</div>
							</div>

							<div class="form-group">
								<label for="select" class="col-lg-2 control-label"><?php echo $labelname[1]; ?> <red>*</red></label>
								<div class="col-lg-10">
									<input type="text" class="form-control" id="journalname1" name="journalname1" placeholder="LR Beam Structure" maxlength="120">
								</div>
							</div>

							<div class="form-group">
								<label for="select" class="col-lg-2 control-label"></label>
								<div class="col-lg-10">
									<label id="errorjournalproperty1" class="text-danger"></label>
								</div>
							</div>

							<div class="form-group">
								<label for="select" class="col-lg-2 control-label"><?php echo $labelname[2]; ?></label>
								<div class="col-lg-10">
									<input type="text" class="form-control" id="journalproperty1" name="journalproperty1" maxlength="120">
								</div>
							</div>

							<div class="form-group">
								<label for="select" class="col-lg-2 control-label"></label>
								<div class="col-lg-10">
									<label id="erroruser1" class="text-danger"></label>
								</div>
							</div>

							<div class="form-group">
								<label for="select" class="col-lg-2 control-label"><?php echo $labelname[3]; ?> <red>*</red></label>
								<div class="col-lg-10">
									<select class="dropdown-toggle" id="user1" name="user1">
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

							<!--<div class="form-group">
								<label for="select" class="col-lg-2 control-label"></label>
								<div class="col-lg-10">
									<label id="errorfrequency1" class="text-danger"></label>
								</div>
							</div>

							<div class="form-group">
								<label for="select" class="col-lg-2 control-label"><?php echo $labelname[4]; ?> <red>*</red></label>
								<div class="col-lg-10">
									<select class="dropdown-toggle" id="frequency1" name="frequency1">
										<?php
											foreach ($frequencys as $frequency):
										?>
												<option value="<?php echo $frequency->frequency_no; ?>"><?php echo $frequency->frequency_name; ?></option>
										<?php
											endforeach;
										?>
									</select>
								</div>
							</div>

							<div class="form-group">
								<label for="select" class="col-lg-2 control-label"></label>
								<div class="col-lg-10">
									<label id="errorstart1" class="text-danger"></label>
								</div>
							</div>

							<div class="form-group">
								<label for="select" class="col-lg-2 control-label">Start Date <red>*</red></label>
								<div class="col-lg-10">
									<input class="input-small" type="text" id="startdate1" name="startdate1" disabled="true">
								</div>
							</div>

							<div class="form-group">
								<label for="select" class="col-lg-2 control-label"></label>
								<div class="col-lg-10">
									<label id="errorend1" class="text-danger"></label>
								</div>
							</div>

							<div class="form-group">
								<label for="select" class="col-lg-2 control-label">End Date <red>*</red></label>
								<div class="col-lg-10">
									<input class="input-small" type="text" id="enddate1" name="enddate1" disabled="true">
								</div>
							</div>-->
							<div class="form-group">
        						<label for="select" class="col-lg-2 control-label"><?php echo $labelname[5]; ?></label>
        						<div class="col-lg-10">
									<div class="table">
										<table class="table table-striped table-hover" id="validator1">
											<tr>
												<td colspan="2" align="center"><input type="hidden" name="validatorid1" id="validatorid1" value="1" /></td>
												<td align="right"><a href="javascript:void(0)" class="addValidator1" >Add</a></td>
											</tr>
          									<tr>
												<th><?php echo $labelname[12]; ?></th>
												<th><?php echo $labelname[13]; ?></th>
												<th>Delete</th>
											</tr>
										</table>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label for="select" class="col-lg-2 control-label"><?php echo $labelname[6]; ?> <red>*</red></label>
								<div class="col-lg-10">
									<div class="table">
									<div class="row text-center text-danger" id="errordata1"> </div>
										<table class="table table-striped table-hover" id="dataentry1">
											<tr>
												<td colspan="2" align="center"><input type="hidden" name="dataentryid1" id="dataentryid1" value="1"/></td>
												<td align="right"><a href="javascript:void(0)" class="addDataEntry1" >Add</a></td>
											</tr>
											<tr>
												<th><?php echo $labelname[12]; ?></th>
												<th><?php echo $labelname[14]; ?></th>
												<th>Delete</th>
											</tr>
										</table>
										<fieldset>
											<legend><?php echo $labelname[7]; ?></legend>
											<div class="table">
												<table class="table table-striped table-hover">
													<tr>
														<td colspan="8" align="center"><label id="errordataattb1" class="text-danger"></label></td>
													</tr>
													<tr>
														<th><?php echo $labelname[8]; ?></th>
														<th><?php echo $labelname[9]; ?></th>
														<th><?php echo $labelname[10]; ?></th>
														<th><?php echo $labelname[11]; ?><input type="hidden" name="1ordercount" id="1ordercount" value="1" /></th>
													</tr>
													<?php
														$dataattbcount=1;
														foreach ($dataattbs as $dataattb):
														$id=$dataattb->data_attb_id;
													?>
															<tr>
																<td><input type="hidden" name="1dataattbid<?php echo $dataattbcount; ?>" id="1dataattbid<?php echo $dataattbcount; ?>" value="<?php echo $dataattb->data_attb_id; ?>"/><input type="checkbox" id="1dataattb<?php echo $dataattbcount; ?>" name="1dataattb<?php echo $dataattbcount; ?>" /></td>
																<td><?php echo $dataattb->data_attb_label; ?></td>
																<td><?php echo $dataattb->data_attb_type_desc; ?></td>
																<td>
																	<input id="1order<?php echo $dataattbcount; ?>" type="text" maxlength="3" name="1order<?php echo $dataattbcount; ?>" style="width:40px">
																</td>
															</tr>
													<?php
														$dataattbcount++;
														endforeach;
													?>
													<input type="hidden" name="dataattbcount1" id="dataattbcount1" value="<?php echo $dataattbcount-1; ?>" />
												</table>
											</div>
										</fieldset>
									</div>
								</div>
							</div>
						</div>
						<div class="modal-footer" style="text-align:center;border:0;">
							<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
							<input type="submit" class="btn btn-primary btn-sm" value="Save Changes" />
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<!--close-->
	<div class="row text-center text-danger"><?php echo $message; ?> </div>
	<div = "table">
		<table class="table table-striped table-hover">
			<tr>
				<th>No</th>
				<th><?php echo $labelname[0]; ?></th>
				<th><?php echo $labelname[1]; ?></th>
				<th><?php echo $labelname[2]; ?></th>
				<th><?php echo $labelname[3]; ?></th>
				<!--<th>Start Date</th>
				<th>End Date</th>-->
				<th>Edit</th>
				<th>Delete</th>
			</tr>
			<?php
				$sno=$page;
				foreach ($records as $record):
					$startdate=date("d-m-Y", strtotime($record->start_date));
					$enddate=date("d-m-Y", strtotime($record->end_date));
					$validatorvalues=$validatorvalue[$record->journal_no];
					$dataentryvalues=$dataentryvalue[$record->journal_no];
					$dataattbvalues=$dataattbvalue[$record->journal_no];

			?>
					<tr>
						<td><?php echo $sno; ?></td>
						<td><?php echo $record->project_name; ?></td>
						<td><?php echo $record->journal_name; ?></td>
						<td><?php echo $record->journal_property; ?></td>
						<td><?php echo $record->user_full_name; ?></td>
						<td>
							<?php
								if($editperm==1)
								{

							?>
									<a href="#" data-toggle="modal" data-target="#MyModal1" class="modaledit" data-editid="<?php echo $record->journal_no; ?>" data-projno="<?php echo $record->project_no; ?>" data-journalname="<?php echo $record->journal_name; ?>" data-user="<?php echo $record->user_id; ?>" data-startdate="<?php echo $record->start_date; ?>" data-enddate="<?php echo $record->end_date; ?>" data-frequency="<?php echo $record->frequency_no; ?>"
									data-validatorvalue="<?php echo $validatorvalues; ?>" data-dataentryvalue="<?php echo $dataentryvalues; ?>" data-dataattbvalue="<?php echo $dataattbvalues; ?>" data-journalproperty="<?php echo $record->journal_property; ?>"><span class="glyphicon glyphicon-edit">&nbsp;</span></a>
									
									<!-- added by ilyas -->
										<a href="/index.php/ilyasdesign?jid=<?php echo $record->journal_no; ?>"> <span class="glyphicon glyphicon-edit" style="color:red">&nbsp;</span></a>
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
									<a href="#" data-toggle="modal" class="modaldelete" data-id="<?php echo $record->journal_no; ?>"><span class="glyphicon glyphicon-trash">&nbsp;</span></a>
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