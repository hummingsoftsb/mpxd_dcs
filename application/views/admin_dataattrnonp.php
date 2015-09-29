<script>
	$(document).ready(function()
	{
		$("#modaladd").click(function ()
		{
			var empty="";
			$(".modal-body #label").val( empty );
			$(".modal-body #inputtype").val( '1' );
			$(".modal-body #errorlabel").html( empty );
			$(".modal-body #errorinputtype").html( empty );
		});

		$('#addrecord').submit(function()
		{
			$.post($('#addrecord').attr('action'), $('#addrecord').serialize(), function( data )
			{
				if(data.st == 0)
				{
		 			$('#errorlabel').html(data.msg);
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
			var labelname = $(this).data('label');
		    var inputtype = $(this).data('attbtype');
			var datasetid = $(this).data('attbid');
			
			var empty="";
			$(".modal-body #label1").val( labelname );
			$(".modal-body #inputtype1").val( inputtype );
			$(".modal-body #datasetid").val(datasetid);
			$(".modal-body #errorlabel1").html( empty );
			$(".modal-body #errorinputtype1").html( empty );

		});

		
		$('#updaterecord').submit(function()
		{
			$.post($('#updaterecord').attr('action'), $('#updaterecord').serialize(), function( data )
			{
				if(data.st == 0)
				{
		 			$('#errorlabel1').html(data.msg);
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
				var id = $(this).data('attbid');
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
<div class="container">
	<!-- INPUT HERE-->
	<div class="page-header">
		<h1 id="nav"><?php echo $labelobject; ?></h1>
	</div>
	<div class="row">
		<div class="col-md-4">
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
						    	<div class="col-sm-6">
						    		<label id="errorlabel" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-4 control-label"><?php echo $labelname[0]; ?><red>*</red></label>
						    	<div class="col-sm-6">
						    		<input type="text" class="form-control" id="label" name="label" placeholder="Enter the Label name" maxlength="60">
						        </div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-6">
						    		<label id="errorinputtype" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-4 control-label"><?php echo $labelname[1]; ?><red>*</red></label>
								<div class="col-sm-6">
									<select class="form-control" id="inputtype" name="inputtype">
                        				<?php
											foreach ($inputtypes as $inputtype):
										?>
												<option value="<?php echo $inputtype->data_attb_type_id; ?>"><?php echo $inputtype->data_attb_type_desc; ?></option>
										<?php
											endforeach;
										?>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
						<input type="submit" class="btn btn-primary btn-sm" value="Add New" />
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
						    	<div class="col-sm-6">
						    		<label id="errorlabel1" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-4 control-label"><?php echo $labelname[0]; ?><red>*</red></label>
						    	<div class="col-sm-6">
						    		<input type="hidden" class="form-control" id="datasetid" name="datasetid">
						    		<input type="text" class="form-control" id="label1" name="label1" placeholder="Enter the Span Value" maxlength="60">
						        </div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-6">
						    		<label id="errorinputtype1" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-4 control-label"><?php echo $labelname[1]; ?><red>*</red></label>
								<div class="col-sm-6">
									<select class="form-control" id="inputtype1" name="inputtype1">
                        				<?php
											foreach ($inputtypes as $inputtype):
										?>
												<option value="<?php echo $inputtype->data_attb_type_id; ?>"><?php echo $inputtype->data_attb_type_desc; ?></option>
										<?php
											endforeach;
										?>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
						<input type="submit" class="btn btn-primary btn-sm" value="Save Changes" />
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
				  	<th><?php echo $labelname[1]; ?></th>
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
				  			<td><?php echo html_escape($record->data_attb_label); ?></td>
				  			<td><?php echo html_escape($record->data_attb_type_desc); ?></td>
	      					<td>
								<?php
									if($editperm==1)
									{
								?>
										<a href="#" data-toggle="modal" data-target="#myModal1" class="modaledit" data-attbid="<?php echo html_escape($record->data_attb_id); ?>" data-label="<?php echo html_escape($record->data_attb_label); ?>" data-attbtype="<?php echo html_escape($record->data_attb_type_id	); ?>" ><span class="glyphicon glyphicon-edit">&nbsp;</span></a>
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
										<a href="#" data-toggle="modal" class="modaldelete" data-attbid="<?php echo html_escape($record->data_attb_id); ?>"><span class="glyphicon glyphicon-trash">&nbsp;</span></a>
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
			<div class="col-md-4">
				<ul class="pagination">
                	<?php echo $this->pagination->create_links(); ?>
				</ul>
			</div>
			<div class="col-md-4 col-md-offset-1" >
				<div class="form-group">
					<label for="search" class="col-sm-2 control-label" style="padding-top: 15px; padding-bottom: 5px;">Show</label>
					<div class="col-sm-3" style="padding-top: 15px; padding-bottom: 5px;">
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
</div>\